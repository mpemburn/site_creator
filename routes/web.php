<?php

use App\Models\Post;
use App\Services\DatabaseService;
use App\Services\HeaderService;
use App\Services\SiteInfoService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
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

    // Get the current highest blog ID from the destination
    $options = DB::select('SELECT * FROM wp_options');
    collect($options)->each(function ($option) {
        if ($option->option_name === 'admin_email') {
            $name = Str::camel($option->option_name);
            !d($name, $option);
        }
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
