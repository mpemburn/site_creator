<?php

namespace App\Console\Commands;

use App\Helpers\SiteInfo;
use App\Services\WebCrawlerService;
use Illuminate\Console\Command;

class CrawlCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'web:crawl {--url=} {--home=} {--site=} {--db=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl a web page';

    protected int $maxBlogId;
    protected string $domain;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $info = new SiteInfo();
        $info->baseUrl = $this->option('url');
        $info->homePage = $this->option('home');
        $info->dbName = $this->option('db');
        $info->siteName = $this->option('site');


        (new WebCrawlerService($info))->fetchContent();
    }
}
