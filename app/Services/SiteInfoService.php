<?php

namespace App\Services;
use App\Factories\PopulateTableFactory;
use App\Sql\OptionsCreate;
use App\Sql\PostCreate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SiteInfoService
{
    public const SERVER_PAGE_EXTENSIONS = [
        'aspx',
        'asp',
        'cfm',
        'html',
        'htm',
        'jspx',
        'jsp',
        'php',
    ];

    protected const DEFAULT_SUBSITE_TABLES = [
        'blogmeta' => null,
        'blogs' => null,
        'commentmeta' => null,
        'comments' => null,
        'links' => null,
        'options' => OptionsCreate::class,
        'postmeta' => null,
        'posts' => PostCreate::class,
        'registration_log' => null,
        'signups' => null,
        'site' => null,
        'sitemeta' => null,
        'term_relationships' => null,
        'term_taxonomy' => null,
        'termmeta' => null,
        'terms' => null,
        'usermeta' => null,
        'users' => null,
    ];

    public string $url;
    public string $destUrl;
    public ?string $home = null;
    public string $site;
    public string $db;
    public string $prefix = 'wp_';
    public string $theme = 'twentytwentythree';
    public string $blogId;
    public string $uploadPath;
    public string $domain;
    public string $pageTitle;
    public string $pageName;
    public string $currentTable;
    public array $postTables = [];
    public bool $hasHeader = false;

    public function setOptions(array $options): self
    {
        collect($options)->each(function ($option, $key) {
            if ($option) {
                $this->$key = $option;
            }
        });

        return $this;
    }

    public function createSubsiteTables(): self
    {
        DatabaseService::setDb($this->db);

        collect(self::DEFAULT_SUBSITE_TABLES)->each(function ($class, $key) {
            if (! $class) {
                return;
            }

            $this->postTables[$key] = $this->prefix . $this->blogId . '_' . $key;
            if (! Schema::hasTable($this->postTables[$key])) {
                $class::make($this->postTables[$key]);
                $this->populateSubsiteTable($key, $this->postTables[$key]);
            }
        });


        return $this;
    }

    protected function populateSubsiteTable(string $key, $currentTable): void
    {
        $this->currentTable = $currentTable;

        PopulateTableFactory::build($key, $this);
    }

    public function setDatabaseBlogInfo(): self
    {
        DatabaseService::setDb($this->db);

        // Get the current highest blog ID from the destination
        $blogs = DB::select('SELECT domain, MAX(blog_id) AS max FROM wp_blogs GROUP BY domain');

        $this->domain = current($blogs)->domain;
        $this->blogId = (int) current($blogs)->max + 1;
        $this->destUrl = 'https://' . $this->domain . '/' . $this->site . '/';

        return $this;
    }

    public function insertBlogRecord(): self
    {
        $now = Carbon::now()->toDateTimeString();
        $tableName = $this->prefix . 'blogs';
        $data = [
            'blog_id' => $this->blogId,
            'site_id' => 1,
            'domain' => $this->domain,
            'path' => '/' . $this->site . '/',
            'registered' => $now,
            'last_updated' => $now,
            'public' => 1,
            'archived' => 0,
            'mature' => 0,
            'spam' => 0,
            'deleted' => 0,
            'lang_id' => 0,
        ];

        SqlService::insert($data, $tableName);

        return $this;
    }

    // Extract the page name in case there is no <title> available
    public function setPageName(string $pageUrl): void
    {
        $pathParts = pathinfo($pageUrl);

        $this->pageName = $pathParts['filename'];
    }

}
