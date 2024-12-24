<?php

namespace Ponponumi\ViteLoader;

class Manifest
{
    public static function load(string $path): array
    {
        // manifest.jsonを読み込む
        if (file_exists($path)) {
            $json = file_get_contents($path);
            $data = json_decode($json, true);

            if($data !== NULL){
                return $data;
            }
        }

        return [];
    }
}
