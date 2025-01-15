<?php

namespace Ponponumi\ViteLoader;

class TypeGet
{
    /**
     * 拡張子からタイプを取得します。
     *
     * @param string $extension ここには、拡張子を渡して下さい。
     * @return string JS系であれば「script」、CSS系であれば「style」、それ以外は空の文字列を返します。
     */
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

    public static function path(string|null $path): string
    {
        // パスからファイルのタイプを取得する
        $result = "";

        if ($path !== null) {
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            $result = self::extension($extension);
        }

        return $result;
    }
}
