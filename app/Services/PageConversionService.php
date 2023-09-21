<?php

namespace App\Services;

use App\Helpers\ImageHelper;
use App\Helpers\RegexHelper;
use App\Models\Post;
use App\Models\Postmeta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PageConversionService
{
    protected SiteInfoService $info;
    protected ?string $header = null;

    public function __construct(SiteInfoService $info)
    {
        $this->info = $info;
    }

    public function processPage(string $content)
    {
        $rawContent = $content;
        // Get rid of header and footer
        $content = $this->extractBody($content);
        if (! $this->info->hasHeader) {
            // Extract any local CSS files from header
            (new HeaderService($this->info))->parse($this->getHeader($content, $rawContent));
            $this->info->hasHeader = true;
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
                    $this->info->pageTitle = $this->extractTitle($line);
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

    protected function getHeader(string $content, string $rawContent): string
    {
        return str_replace($content, '', $rawContent);
    }

    protected function processImages(array $images, string $content): string
    {
        // Find and replace image paths on page
        collect($images)->each(function ($image) use (&$content) {
            $originalPath = RegexHelper::extractAttribute('src', $image);
            if (! str_starts_with($originalPath, 'http')) {
                $newPath = $this->saveImage($originalPath);
                if ($newPath) {
                    // Create a post record for the image
                    $postId = $this->createAttachment($newPath);
                    $this->createAttachmentMeta($originalPath, $postId);
                    $content = str_replace($originalPath, $newPath, $content);
                }
            }
        });

        return $content;
    }

    protected function saveImage($imgSrc): ?string
    {
        if (! CurlService::testUrl($this->info->url . $imgSrc)) {
            return null;
        }
        $binary = file_get_contents($this->info->url . $imgSrc);
        Storage::put($this->info->uploadPath . $imgSrc, $binary);

        return 'https://' . $this->info->domain . '/wp-content/uploads/sites/' . $this->info->uploadPath . $imgSrc;
    }

    protected function saveAsPost(string $content): void
    {
        $postTable = $this->info->postTables['posts'];

        $last = DB::select("SELECT MAX(ID) AS max FROM {$postTable}");
        $lastId = ((int) current($last)->max) + 1;

        $post = new Post();
        $post->setTable($postTable);
        $post->create([
            'post_content' => trim($content),
            'post_author' => 1,
            'post_title' => trim($this->info->pageTitle),
            'post_excerpt' => '',
            'post_type' => 'page',
            'to_ping' => '',
            'pinged' => '',
            'post_content_filtered' => '',
            'guid' => 'https://' . $this->info->domain . '/' . $this->info->site . '/?p=' . $lastId
        ]);
    }

    protected function createAttachment(string $attachmentPath): ?string
    {
        $pathParts = pathinfo($attachmentPath);

        $post = new Post();
        $post->setTable($this->info->postTables['posts']);
        $result = $post->create([
            'post_content' => '',
            'post_author' => 1,
            'post_title' => $pathParts['filename'],
            'post_excerpt' => '',
            'post_type' => 'attachment',
            'to_ping' => '',
            'pinged' => '',
            'post_content_filtered' => '',
            'guid' => $attachmentPath
        ]);

        return$result->ID;
    }

    protected function createAttachmentMeta(string $path, string $postId)
    {
        collect([
            '_wp_attached_file' => $this->info->yearMonth . '/' . $path,
            '_wp_attachment_metadata' => serialize(ImageHelper::getImageMeta($this->info->url . $path))
        ])->each(function ($value, $key) use ($postId) {
            $postmeta = new Postmeta();
            $postmeta->setTable($this->info->postTables['postmeta']);
            $postmeta->create([
                'post_id' => $postId,
                'meta_key' => $key,
                'meta_value' => $value,
            ]);
        });
    }
}
