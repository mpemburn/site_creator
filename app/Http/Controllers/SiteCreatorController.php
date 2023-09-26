<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteCreatorController extends Controller
{
    public function createSite(Request $request): JsonResponse
    {
        $url = request('url');
        $home = request('home');
        $db = request('db');
        $path = request('path');
        $theme = request('theme');

        return response()->json([
            'url' => $url,
            'home' => $home,
            'db' => $db,
            'path' => $path
        ]);


    }
}
