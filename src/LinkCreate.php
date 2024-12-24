<?php

namespace Ponponumi\ViteLoader;

class LinkCreate
{
    public static function cssLinkCreate(string $url): string
    {
        // CSSのリンクを作る
        return '<link rel="stylesheet" href="' . $url . '">';
    }
}
