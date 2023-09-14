<?php

namespace App\Observers;

use App\Helpers\SiteInfo;
use App\Services\PageConversionService;
use DOMDocument;
use Spatie\Crawler\CrawlObservers\CrawlObserver;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

class WebObserver extends CrawlObserver
{
    protected SiteInfo $info;
    protected PageConversionService $pageService;

    public function __construct(SiteInfo $info)
    {
        $this->info = $info;
        $this->pageService = new PageConversionService($info);
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
        if (stripos($url, $this->info->baseUrl) === false) {
            return;
        }
        $pathParts = pathinfo($url);
        if (! in_array($pathParts['extension'], SiteInfo::SERVER_PAGE_EXTENSIONS)) {
            return;
        }

        echo 'Reading: ' . $url . PHP_EOL;
        $this->info->setPageName($url);

        $doc = new DOMDocument();
        $body = $response->getBody();

        if (strlen($body) < 1) {
            return;
        }

        @$doc->loadHTML($body);
        //# save HTML
        $content = $doc->saveHTML();

        $this->pageService->processPage($content);

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
