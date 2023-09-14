<?php

use App\Models\Post;
use App\Services\DatabaseService;
use App\Services\HeaderService;
use App\Services\SiteInfoService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dev', function () {
    $header = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?xml version="1.0" encoding="UTF-8"?><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="Generator" content="iWeb 3.0.4">
    <meta name="iWeb-Build" content="local-build-20201219">
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7">
    <meta name="viewport" content="width=600">
    <title>Home</title>
    <link rel="stylesheet" type="text/css" media="screen,print" href="Home_files/Home.css">
    <!--[if lt IE 8]><link rel=\'stylesheet\' type=\'text/css\' media=\'screen,print\' href=\'Home_files/HomeIE.css\'/><![endif]-->
    <!--[if gte IE 8]><link rel=\'stylesheet\' type=\'text/css\' media=\'screen,print\' href=\'Media/IE8.css\'/><![endif]-->
    <script type="text/javascript" src="Scripts/iWebSite.js"></script>
    <script type="text/javascript" src="Scripts/iWebImage.js"></script>
    <script type="text/javascript" src="Home_files/Home.js"></script>
  </head>
  <body style="background: rgb(255, 255, 255); margin: 0pt; " onload="onPageLoad();">
  </body>
</html>
';
    $info = new SiteInfoService();
    $info->baseUrl = 'https://www2.clarku.edu/faculty/sgranados/';
    $info->dbName = 'multisite';
    $info->blogId = 4;
    $info->createSubsiteTables();
//    $info->homePage = $this->option('home');
//    $info->dbName = $this->option('db');
//    $info->prefix = $this->option('prefix') ?: 'wp_';
//    $info->siteName = $this->option('site');
//    $baseUrl = 'https://www2.clarku.edu/faculty/sgranados/';
    (new HeaderService($info))->parse($header);
//    $service = new SiteInfoService();
//    $service->dbName = 'multisite';
//    $service->prefix = 'wp_';
//    $service->blogId = 4;
//    $service->createSubsiteTables();
});
