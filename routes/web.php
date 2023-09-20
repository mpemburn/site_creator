<?php

use App\Models\Post;
use App\Services\DatabaseService;
use App\Services\HeaderService;
use App\Services\SiteInfoService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
    DatabaseService::setDb('wpsandbox');

    collect([
        'https://s28151.pcdn.co/wp-content/uploads/2021/10/2021-Family-Visit-Campus-Clark-University-1340x550.jpg',
        "https://www.clarku.edu/wp-content/themes/clarku/assets/img/main-logo.svg",
        'https://www2.clarku.edu/faculty/mwiser/learning%20and%20instruction.gif',
        Storage::path('Sample BMP Image File Download.bmp'),
    ])->each(function ($image) {
        $metaData = \App\Services\ImageService::getMimeType($image);
        !d($metaData);
    });
//    \App\Factories\PopulateTableFactory::build('options', 'wp_2_options');
    //    $info->homePage = $this->option('home');
//    $info->dbName = $this->option('db');
//    $info->prefix = $this->option('prefix') ?: 'wp_';
//    $info->siteName = $this->option('site');
//    $baseUrl = 'https://www2.clarku.edu/faculty/sgranados/';
//    (new HeaderService($info))->parse($header);
//    $service = new SiteInfoService();
//    $service->dbName = 'multisite';
//    $service->prefix = 'wp_';
//    $service->blogId = sgranados;
//    $service->createSubsiteTables();
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
