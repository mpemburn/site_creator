<?php

use App\Services\DatabaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

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
    $path = '/Users/MPemburn/Sandbox/wpsandbox';
    $does = file_exists($path);

    if ($does) {
        $themesPath = $path . '/wp-content/themes';
        $prefix = \App\Helpers\WordPressHelper::getPrefix($path);
        $themes = \App\Helpers\WordPressHelper::getThemesArray($themesPath);

        !d($prefix);
        !d($themes);
    }
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/db_test', [App\Http\Controllers\UiController::class, 'dbExists'])->name('db_test');
Route::get('/path_test', [App\Http\Controllers\UiController::class, 'pathExists'])->name('path_test');
Route::get('/url_test', [App\Http\Controllers\UiController::class, 'urlExists'])->name('url_test');
