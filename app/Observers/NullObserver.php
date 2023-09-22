<?php

namespace App\Observers;

use App\Services\PageConversionService;
use App\Services\SiteInfoService;
use DOMDocument;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlObservers\CrawlObserver;

class NullObserver extends CrawlObserver
{
    protected SiteInfoService $info;
    protected PageConversionService $pageService;

    public function __construct()
    {
    }

    /**
     * Called when the crawler will crawl the url.
     *
     */
    public function willCrawl(UriInterface $url): void
    {
    }

    /**
     * Called when the crawler has crawled the given url successfully.
     *
     */
    public function crawled(
        UriInterface      $url,
        ResponseInterface $response,
        ?UriInterface     $foundOnUrl = null
    ): void
    {
        echo 'Reading: ' . $url . PHP_EOL;

    }

    /**
     * Called when the crawler had a problem crawling the given url.
     *
     */
    public function crawlFailed(
        UriInterface     $url,
        RequestException $requestException,
        ?UriInterface    $foundOnUrl = null
    ): void
    {
        //echo 'crawlFailed: ' . $url . PHP_EOL;
    }

    /**
     * Called when the crawl has ended.
     */
    public function finishedCrawling(): void
    {
    }

}
