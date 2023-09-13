<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class PageConversionService
{
    protected string $baseUrl;
    protected string $blogId;
    protected string $uploadPath;
    protected string $domain;
    protected ?string $header = null;

    public function __construct(string $baseUrl, string $siteName, string $blogId, string $domain)
    {
        $this->baseUrl = $baseUrl;
        $this->blogId = $blogId;
        $this->domain = $domain;

        $this->uploadPath = $blogId . '/' . Carbon::now()->format('Y/m/');
    }

    public function processPage(string $content)
    {
        $rawContent = $content;
        // Get rid of header and footer
        $content = $this->extractBody($content);
        if (! $this->header) {
            $this->header = $this->buildHeader($content, $rawContent);
            Storage::put('header.html', $this->header);
        }

        if (stripos($content, '<img') !== false) {
            if (preg_match_all('/(<img)(.*)(>)/', $content, $images)) {
                $content = $this->processImages(current($images), $content);
            }
        }

        Storage::put('page' . Carbon::now()->format('-m-s-') . '.html', $content);
    }

    protected function extractBody($content): string
    {
        $body = '';
        $isBody = false;
        collect(explode("\n", $content))
            ->each(function ($line) use (&$body, &$isBody) {
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
                $content = str_replace($oldPath, $newPath, $content);
            }
        });

        return $content;
    }

    protected function saveImage($imgSrc): string
    {
        $binary = file_get_contents($this->baseUrl . $imgSrc);
        Storage::put($this->uploadPath . $imgSrc, $binary);

        return 'https://' . $this->domain . '/wp-content/uploads/sites/' . $this->uploadPath . $imgSrc;
    }

}
