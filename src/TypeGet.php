<?php

namespace Ponponumi\ViteLoader;

class TypeGet
{
    public static function extension(string $extension): string
    {
        $type = "";

        switch (mb_strtolower($extension)) {
            case "css":
            case "scss":
            case "sass":
            case "less":
            case "stylus":
            case "styl":
                $type = "style";
                break;

            case "js":
            case "ts":
            case "jsx":
            case "tsx":
            case "coffee":
                $type = "script";
                break;
        }

        return $type;
    }
}
