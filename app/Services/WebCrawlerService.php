<?php
namespace App\Services;

use App\Models\BlogList;
use App\Observers\WebObserver;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Collection;
use Spatie\Crawler\Crawler;

class WebCrawlerService
{
    protected Collection $processes;
    protected ?int $resumeAt = null;

    public function __construct()
    {
        $this->processes = collect();
    }

    public function fetchContent(string $url, string $find, bool $echo = false) {
        $options = [RequestOptions::ALLOW_REDIRECTS => true, RequestOptions::TIMEOUT => 30];

        //# initiate crawler
        Crawler::create($options)
            ->acceptNofollowLinks()
            ->ignoreRobots()
            ->setCrawlObserver(new WebObserver($find, $echo))
            ->setMaximumResponseSize(1024 * 1024 * 2) // 2 MB maximum
            ->setDelayBetweenRequests(500)
            ->startCrawling($url);

        return true;
    }
}
