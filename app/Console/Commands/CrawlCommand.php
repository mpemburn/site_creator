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
        $info = (new SiteInfoService())
            ->setOptions($this->options())
            ->setDatabaseBlogInfo()
            ->createSubsiteTables()
            ->insertBlogRecord();

        (new WebCrawlerService($info))->fetchContent();
    }
}
