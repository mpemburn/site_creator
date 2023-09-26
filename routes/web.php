<?php

use App\Events\OrderStatusUpdated;
use App\Helpers\CurlHelper;
use App\Helpers\FileHelper;
use App\Helpers\SourceHelper;
use App\Services\DatabaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Process;
use Illuminate\Process\Pool;

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
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/db_test', [App\Http\Controllers\UiController::class, 'dbExists'])->name('db_test');
Route::get('/path_test', [App\Http\Controllers\UiController::class, 'pathExists'])->name('path_test');
Route::get('/url_test', [App\Http\Controllers\UiController::class, 'urlExists'])->name('url_test');
Route::get('/find_home', [App\Http\Controllers\UiController::class, 'findHome'])->name('find_home');
Route::get('/home_test', [App\Http\Controllers\UiController::class, 'homeExists'])->name('home_test');
Route::post('/create_site', [App\Http\Controllers\SiteCreatorController::class, 'createSite'])->name('create_site');
