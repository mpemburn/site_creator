<?php

namespace App\Console\Commands;

use App\Services\SiteInfoService;
use App\Services\WebCrawlerService;
use Illuminate\Console\Command;

class CrawlCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'web:crawl {--url=} {--home=} {--site=} {--db=} {--prefix=} {--theme=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl a web page';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $info = new SiteInfoService();
        $info->baseUrl = $this->option('url');
        $info->homePage = $this->option('home');
        $info->dbName = $this->option('db');
        $info->prefix = $this->option('prefix') ?: 'wp_';
        $info->siteName = $this->option('site');
        $info->theme = $this->option('theme') ?: 'wp_';

        (new WebCrawlerService($info))->fetchContent();
    }
}
