<?php

namespace Ponponumi\ViteLoader;

class Manifest
{
    /**
     * manifest.jsonを読み込みます。
     *
     * @param string $path ここには、manifest.jsonのパスを渡して下さい。
     * @return array manifest.jsonがあればそのデータ、なければ空の配列を返します。
     */
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

    /**
     * ソースのパスからビルド後のデータを取得する
     *
     * @param string $sourcePath ここには、ソースファイルのパスを渡して下さい。
     * @param array $manifestData ここには、manifest.jsonの中身を渡して下さい。
     * @param boolean $errorMode ファイルが見つからない場合、エラーを発生させるにはtrueを渡して下さい。
     * @return array
     */
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

    /**
     * ソースのパスからビルド後のファイルパスを取得する
     *
     * @param string $sourcePath ここには、ソースファイルのパスを渡して下さい。
     * @param array $manifestData ここには、manifest.jsonの中身を渡して下さい。
     * @param boolean $errorMode ファイルが見つからない場合、エラーを発生させるにはtrueを渡して下さい。
     * @return array
     */
    public static function pathGet(string $sourcePath, array $manifestData, $errorMode=false): string
    {
        $result = "";
        $data = self::dataGet($sourcePath, $manifestData, $errorMode);

        if($data !== []){
            $result = $data["file"];
        }

        return $result;
    }

    /**
     * ソースのパスからビルド後のURLを取得します。
     *
     * @param string $sourcePath ここには、ソースファイルのパスを渡して下さい。
     * @param array $manifestData ここには、manifest.jsonの中身を渡して下さい。
     * @param string $buildPath ここには、ビルド済みファイルが保存されたパスを渡して下さい。
     * @param mixed $devMode ここには、開発モードを有効にするかを渡して下さい。
     * @param string $devServerHostWeb ここには、開発サーバーのホストを渡して下さい。
     * @param mixed $devServerAccessStatus ここには、開発サーバーのステータスを渡して下さい。
     * @param boolean $errorMode ファイルが見つからない場合、エラーを発生させるにはtrueを渡して下さい。
     * @return string
     */
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
            $path = LinkCreate::urlLastSlashAdd($devServerHostWeb) . $sourcePath;
        }else{
            // デバッグモードでなければ
            $data = self::pathGet($sourcePath, $manifestData, $errorMode);

            if($data !== ""){
                // データがあれば
                $path = LinkCreate::urlLastSlashAdd($buildPath) . $data;
            }
        }

        return $path;
    }
}
