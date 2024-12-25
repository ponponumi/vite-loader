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

    public static function dataGet(string $sourcePath, array $manifestData, $errorMode=false): array
    {
        // ソースのパスからビルド後のデータを取得する
        // なければ空の配列を返す
        if (array_key_exists($sourcePath, $manifestData)) {
            // ある場合
            return $manifestData[$sourcePath];
        } else {
            // ない場合
            if ($errorMode) {
                // エラーモードが有効であれば
                throw new \Exception("'$sourcePath' は見つかりません");
            }

            return [];
        }
    }

    public static function pathGet(string $sourcePath, array $manifestData, $errorMode=false): string
    {
        $result = "";
        $data = self::dataGet($sourcePath, $manifestData, $errorMode);

        if($data !== []){
            $result = $data["file"];
        }

        return $result;
    }

    public static function webPathGet(
        string $sourcePath,
        array $manifestData,
        string $buildPath="",
        $devMode=false,
        string $devServerHostWeb="",
        $devServerAccessStatus=false,
        $errorMode=false
    ): string
    {
        // Webのパスを取得
        $path = "";

        if($devMode && $devServerHostWeb !== "" && $devServerAccessStatus){
            // デバッグモードなら
            $path = $devServerHostWeb . $sourcePath;
        }else{
            // デバッグモードでなければ
            $data = self::pathGet($sourcePath, $manifestData, $errorMode);

            if($data !== ""){
                // データがあれば
                $path = $buildPath . $data;
            }
        }

        return $path;
    }
}
