<?php

namespace App\Services;

use App\Helpers\SiteInfo;
use App\Models\Post;
use App\Sql\PostCreate;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class PageConversionService
{
    protected SiteInfo $info;
    protected ?string $header = null;

    public function __construct(SiteInfo $info)
    {
        $this->info = $info;

        $this->info->uploadPath = $info->blogId . '/' . Carbon::now()->format('Y/m/');
    }

    public function processPage(string $content)
    {
        $rawContent = $content;
        // Get rid of header and footer
        $content = $this->extractBody($content);
        if (! $this->header) {
            // TODO: Figure out where to put the header.
            $this->header = $this->buildHeader($content, $rawContent);
        }

        if (stripos($content, '<img') !== false) {
            if (preg_match_all('/(<img)(.*)(>)/', $content, $images)) {
                $content = $this->processImages(current($images), $content);
            }
        }

        $this->saveAsPost($content);
    }

    protected function extractBody($content): string
    {
        $body = '';
        $isBody = false;
        collect(explode("\n", $content))
            ->each(function ($line) use (&$body, &$isBody) {
                if (stripos($line, '<title') !== false) {
                    $this->info->title = $this->extractTitle($line);
                }
                if (! $isBody) {
                    $isBody = stripos($line, '<body') !== false;
                    return;
                }
                if (stripos($line, '</body') !== false) {
                    $isBody = false;
                    return;
                }
                $body .= $line . "\n";
            });

        return $body;
    }

    protected function extractTitle(string $titleLine): string
    {
        $title = preg_replace('/<[^>]*>/', '', $titleLine);

        return $title ?: $this->info->pageName;
    }

    protected function buildHeader(string $content, string $rawContent): string
    {
        return str_replace($content, '', $rawContent);
    }

    protected function processImages(array $images, string $content): string
    {
        collect($images)->each(function ($image) use (&$content) {
            preg_match('/(src=")([^">]+)(")/', $image, $matches);
            $oldPath = $matches[2] ?: '';
            if (!str_starts_with($oldPath, 'http')) {
                $newPath = $this->saveImage($oldPath);
                if ($newPath) {
                    $content = str_replace($oldPath, $newPath, $content);
                }
            }
        });

        return $content;
    }

    protected function saveImage($imgSrc): ?string
    {
        if (! UrlService::testUrl($this->info->baseUrl . $imgSrc)) {
            return null;
        }
        $binary = file_get_contents($this->info->baseUrl . $imgSrc);
        Storage::put($this->info->uploadPath . $imgSrc, $binary);

        return 'https://' . $this->info->domain . '/wp-content/uploads/sites/' . $this->info->uploadPath . $imgSrc;
    }

    protected function saveAsPost(string $content): void
    {
        DatabaseService::setDb('multisite');
        $postTable = 'wp_' . $this->info->blogId . '_posts';
        if (! Schema::hasTable($postTable)) {
            PostCreate::make($postTable);
        }

        $last = DB::select("SELECT MAX(ID) AS max FROM {$postTable}");
        $lastId = ((int) current($last)->max) + 1;

        $post = new Post();
        $post->setTable($postTable);
        $post->create([
            'post_content' => trim($content),
            'post_title' => trim($this->info->title),
            'post_excerpt' => '',
            'post_type' => 'page',
            'to_ping' => '',
            'pinged' => '',
            'post_content_filtered' => '',
            'guid' => 'https://' . $this->info->domain . '/' . $this->info->siteName . '/?p=' . $lastId
        ]);
    }
}
