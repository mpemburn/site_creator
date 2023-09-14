<?php
namespace App\Services;

use App\Models\BlogList;
use App\Observers\WebObserver;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\DB;
use Spatie\Crawler\Crawler;

class WebCrawlerService
{
    protected SiteInfoService $info;

    public function __construct(SiteInfoService $info)
    {
        $this->info = $info;
    }

    public function fetchContent() {
        $options = [RequestOptions::ALLOW_REDIRECTS => true, RequestOptions::TIMEOUT => 30];
        $this->setDatabaseBlogInfo();

        //# initiate crawler
        Crawler::create($options)
            ->acceptNofollowLinks()
            ->ignoreRobots()
            ->setCrawlObserver(new WebObserver($this->info))
            ->setMaximumResponseSize(1024 * 1024 * 2) // 2 MB maximum
            ->setDelayBetweenRequests(500)
            ->startCrawling($this->info->baseUrl . $this->info->homePage);

        return true;
    }

    protected function setDatabaseBlogInfo(): void
    {
        DatabaseService::setDb($this->info->dbName);

        // Get the current highest blog ID from the destination
        $blogs = DB::select('SELECT domain, MAX(blog_id) AS max FROM wp_blogs GROUP BY domain');

        $this->info->domain = current($blogs)->domain;
        $this->info->blogId = (int) current($blogs)->max + 1;
        $this->info->createSubsiteTables();

    }

}
