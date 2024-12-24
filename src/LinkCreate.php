<?php

namespace Ponponumi\ViteLoader;

class LinkCreate
{
    public static function cssLinkCreate(string $url): string
    {
        // CSSのリンクを作る
        return '<link rel="stylesheet" href="' . $url . '">';
    }

    public static function jsLinkCreate(string $url, $moduleMode = false): string
    {
        // JSのリンクを作る
        $module = "";

        if ($moduleMode) {
            $module = 'type="module" ';
        }

        return '<script ' . $module . 'src="' . $url . '"></script>';
    }

    public static function moduleLinkCreate(string $url): string
    {
        // JSのモジュールのリンクを作る
        return self::jsLinkCreate($url, true);
    }
}
