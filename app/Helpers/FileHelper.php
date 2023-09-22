<?php

namespace App\Helpers;
use RecursiveDirectoryIterator;

class FileHelper
{
    public static function getDirectories(string $path): array
    {
        $subdirs = [];

        $iterator = new RecursiveDirectoryIterator($path);
        collect($iterator)->each(function ($file) use (&$subdirs) {
            if ($file->isDir()) {
                $name = $file->getFilename();
                if ($name !== '.' && $name !== '..') {
                    $subdirs[] = $name;
                }
            }
        });

        return $subdirs;
    }

    public static function getContentsArray(string $filepath): array
    {
        if (file_exists($filepath)) {
            $contents = file_get_contents($filepath);
            return explode("\n", $contents);
        }

        return [];
    }
}
