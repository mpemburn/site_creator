<?php

namespace App\Http\Controllers;

use App\Helpers\CurlHelper;
use App\Helpers\SourceHelper;
use App\Helpers\WordPressHelper;
use App\Services\DatabaseService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class UiController extends Controller
{
    public function dbExists(Request $request): JsonResponse
    {
        $success = false;
        $database = request('value');

        try {
            DatabaseService::setDb($database);
            Schema::hasTable(null);

            $success = true;
        } catch (Exception $e) {
            //
        }

        return response()->json(['success' => $success, 'message' => __('ui.missing.db')]);
    }
    public function pathExists(Request $request): JsonResponse
    {
        $path = request('value');
        $success = file_exists($path . '/wp-config.php');

        $themes = [];
        if ($success) {
            $themePath = $path . '/wp-content/themes';
            $themes = WordPressHelper::getThemesArray($themePath);
        }

        return response()->json([
            'success' => $success,
            'themes' => $themes,
            'message' => __('ui.missing.path')
        ]);
    }
    public function urlExists(Request $request): JsonResponse
    {
        $url = request('value');
        $success = CurlHelper::testUrl($url);

        return response()->json([
            'success' => $success,
            'message' => __('ui.missing.url')]
        );
    }

    public function findHome(Request $request): JsonResponse
    {
        $url = request('value');
        $success = CurlHelper::testUrl($url);

        $home = SourceHelper::getIndexFile($url);
        if ($home) {
            $home = SourceHelper::getForwardPage($url, $home);
        }

        return response()->json([
            'success' => $success,
            'home' => $home,
            'message' => __('ui.missing.home')
        ]);
    }
    public function homeExists(Request $request): JsonResponse
    {
        $url = request('value');
        $success = CurlHelper::testUrl($url);

        $info = pathinfo($url);

        return response()->json([
            'success' => $success,
            'home' => $info['basename'],
            'message' => __('ui.missing.home')
        ]);
    }
}
