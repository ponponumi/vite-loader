<?php

namespace Ponponumi\ViteLoader;

class LinkCreate
{
    public static function css(string $url): string
    {
        // CSSのリンクを作る
        return '<link rel="stylesheet" href="' . $url . '">';
    }

    public static function js(string $url, $moduleMode = false): string
    {
        // JSのリンクを作る
        $module = "";

        if ($moduleMode) {
            $module = 'type="module" ';
        }

        return '<script ' . $module . 'src="' . $url . '"></script>';
    }

    public static function module(string $url): string
    {
        // JSのモジュールのリンクを作る
        return self::js($url, true);
    }

    public static function urlLastSlashAdd(string $url): string
    {
        // URLの最後にスラッシュを追加する
        if ($url !== "") {
            // パスの文字があれば
            if (substr($url, -1) !== "/") {
                // 最後の文字がスラッシュでなければ
                $url .= "/";
            }
        }

        return $url;
    }

    public static function htmlCreate(string $url, $moduleMode=false, string $getType=""): string
    {

        $html = "";
        $type = TypeGet::path($url);

        switch ($type) {
            case "style":
                if ($getType !== "script") {
                    $html = self::css($url);
                }
                break;
            case "script":
                if ($getType !== "style") {
                    if ($moduleMode) {
                        $html = self::module($url);
                    } else {
                        $html = self::js($url);
                    }
                }
                break;
        }

        return $html;
    }
}
