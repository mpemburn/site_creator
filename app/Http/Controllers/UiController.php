<?php

namespace App\Http\Controllers;

use App\Helpers\CurlHelper;
use App\Helpers\FileHelper;
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

        return response()->json(['success' => $success, 'message' => 'Database does not exist.']);
    }
    public function pathExists(Request $request): JsonResponse
    {
        $path = request('value');
        $success = file_exists($path);

        $themes = [];
        if ($success) {
            $themePath = $path . '/wp-content/themes';
            $themes = WordPressHelper::getThemesArray($themePath);
        }

        return response()->json([
            'success' => $success,
            'themes' => $themes,
            'message' => 'Path does not exist.'
        ]);
    }
    public function urlExists(Request $request): JsonResponse
    {
        $url = request('value');
        $success = CurlHelper::testUrl($url);

        return response()->json(['success' => $success, 'message' => 'URL does not exist.']);
    }
}
