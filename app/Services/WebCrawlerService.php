<?php
namespace App\Services;

use App\Models\BlogList;
use App\Observers\WebObserver;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Crawler\Crawler;

class WebCrawlerService
{
    protected string $baseUrl;
    protected string $homePage;
    protected string $dbName;
    protected string $siteName;
    protected string $maxBlogId;
    protected string $domain;

    public function __construct()
    {
    }

    public function setBaseUrl(string $baseUrl): self
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }
    public function setHomePage(string $homePage): self
    {
        $this->homePage = $homePage;

        return $this;
    }
    public function setSiteName(string $siteName): self
    {
        $this->siteName = $siteName;

        return $this;
    }
    public function setDatabase(string $dbName): self
    {
        $this->dbName = $dbName;

        return $this;
    }

    public function fetchContent() {
        $options = [RequestOptions::ALLOW_REDIRECTS => true, RequestOptions::TIMEOUT => 30];
        $this->getDatabaseBlogInfo($this->dbName);

        //# initiate crawler
        Crawler::create($options)
            ->acceptNofollowLinks()
            ->ignoreRobots()
            ->setCrawlObserver(new WebObserver($this->baseUrl, $this->siteName, $this->maxBlogId, $this->domain))
            ->setMaximumResponseSize(1024 * 1024 * 2) // 2 MB maximum
            ->setDelayBetweenRequests(500)
            ->startCrawling($this->baseUrl . $this->homePage);

        return true;
    }

    protected function getDatabaseBlogInfo(string $dbName): void
    {
        DatabaseService::setDb($dbName);

        // Get the current highest blog ID from the destination
        $blogs = DB::select('SELECT domain, MAX(blog_id) AS max FROM wp_blogs GROUP BY domain');

        $this->domain = current($blogs)->domain;
        $this->maxBlogId = (int) current($blogs)->max + 1;
    }

}
