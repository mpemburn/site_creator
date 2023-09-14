<?php

namespace App\Helpers;
class SiteInfo
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

    public string $baseUrl;
    public string $homePage;
    public string $siteName;
    public string $dbName;
    public string $blogId;
    public string $uploadPath;
    public string $domain;
    public string $title;
    public string $pageName;

    public function setPageName(string $pageUrl): void
    {
        $pathParts = pathinfo($pageUrl);

        $this->pageName = $pathParts['filename'];
    }
}
