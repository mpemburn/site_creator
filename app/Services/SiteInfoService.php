<?php

namespace App\Services;
use App\Sql\OptionsCreate;
use App\Sql\PostCreate;
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

    public string $baseUrl;
    public ?string $homePage;
    public string $siteName;
    public string $dbName;
    public string $prefix = 'wp_';
    public string $theme = '';
    public string $blogId;
    public string $uploadPath;
    public string $domain;
    public string $title;
    public string $pageName;
    public array $postTables = [];
    public bool $hasHeader = false;

    public function createSubsiteTables()
    {
        DatabaseService::setDb($this->dbName);

        collect(self::DEFAULT_SUBSITE_TABLES)->each(function ($class, $name) {
            if (! $class) {
                return;
            }

            $this->postTables[$name] = $this->prefix . $this->blogId . '_' . $name;
            if (! Schema::hasTable($this->postTables[$name])) {
                $class::make($this->postTables[$name]);
            }

        });
    }

    public function setPageName(string $pageUrl): void
    {
        $pathParts = pathinfo($pageUrl);

        $this->pageName = $pathParts['filename'];
    }

}
