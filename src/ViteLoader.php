<?php

namespace Ponponumi\ViteLoader;

class ViteLoader {
    public $manifestPath;
    public $buildPath;
    public $manifestData = [];
    public $errorMode;
    public bool $devMode = false;
    public string $devServerHost = "";
    public string $devServerHostWeb = "";
    public bool $devServerAccessStatus;
    public string $viteReloadPath = "";
    public $moduleMode = false;

    public function __construct(string $manifestPath, string $buildPath = "", $errorMode = false, array $viteDevServer = [], $moduleMode = false) {
        $this->manifestPath = $manifestPath;
        $this->buildPath = $this->lastSlashAdd($buildPath);

        $this->errorMode = $errorMode;
        $this->manifestData = Manifest::load($manifestPath);

        $this->devServerSetting(
            array_key_exists('devMode', $viteDevServer) ? boolval($viteDevServer['devMode']) : false,
            array_key_exists('devHost', $viteDevServer) ? strval($viteDevServer['devHost']) : "",
            array_key_exists('devHostWeb', $viteDevServer) ? strval($viteDevServer['devHostWeb']) : ""
        );

        $this->moduleModeSet($moduleMode);
    }

    public function lastSlashAdd(string $path) {
        // 最後にスラッシュを追加する
        return LinkCreate::urlLastSlashAdd($path);
    }

    public function manifestDataGet() {
        // マニフェストデータを取得する
        return $this->manifestData;
    }

    public function buildDataGet($sourcePath): array
    {
        // ソースのパスからビルド後のデータを取得する
        // なければ空の配列を返す
        return Manifest::dataGet($sourcePath, $this->manifestData, $this->errorMode);
    }

    public function buildPathGet($sourcePath): string
    {
        // ソースのパスからビルド後のパスを取得する
        // なければ空の文字を返す
        return Manifest::pathGet($sourcePath, $this->manifestData, $this->errorMode);
    }

    public function buildWebPathGet($sourcePath): string
    {
        // ソースのパスからビルド後のWeb用パスを取得する
        // なければ空の文字を返す
        return Manifest::webPathGet(
            $sourcePath,
            $this->manifestData,
            $this->buildPath,
            $this->devMode,
            $this->devServerHostWeb,
            $this->devServerAccessStatus,
            $this->errorMode
        );
    }

    public function moduleModeSet($set = true) {
        $this->moduleMode = $set;
    }

    public function cssLinkCreate(string $url) {
        // CSSのリンクを作る
        return LinkCreate::css($url);
    }

    public function jsLinkCreate(string $url, $moduleMode = false) {
        return LinkCreate::js($url, $moduleMode);
    }

    public function moduleLinkCreate(string $url) {
        return LinkCreate::module($url);
    }

    public function htmlGet($sourcePath, string $getType = "") {
        // HTMLを取得する
        // なければ空文字を返す
        $url = $this->buildWebPathGet($sourcePath);
        $html = LinkCreate::htmlCreate($url, $this->moduleMode, $getType);

        $viteReloadHtml = $this->viteReloadHtmlGet();

        if ($viteReloadHtml !== "") {
            $html = $viteReloadHtml . $html;
        }

        return $html;
    }

    public function html($sourcePath, string $getType = "") {
        // HTMLに出力する
        echo $this->htmlGet($sourcePath, $getType);
    }

    public function htmlListGet(array $sourcePathList, string $getType = "") {
        // ソースのパスリストからHTMLを返す
        $html = "";

        foreach ($sourcePathList as $sourcePath) {
            $html .= $this->htmlGet($sourcePath, $getType);
        }

        return $html;
    }

    public function htmlList(array $sourcePathList, string $getType = "") {
        // ソースのパスリストからHTMLを出力する
        echo $this->htmlListGet($sourcePathList, $getType);
    }

    private function devServerAccess() {
        // 開発サーバーにアクセスする
        // 開発サーバーが動いていればtrue、動いていなければfalseを返す
        $check = false;

        if ($this->devServerHost !== "") {
            try {
                $client = new \GuzzleHttp\Client();
                $response = $client->request('GET', $this->devServerHost, [
                    // 'timeout' => 5,
                    'http_errors' => false,
                ]);

                $check = true;

                $this->viteReloadPath = $this->devServerHostWeb . '@vite/client';
            } catch (\Exception $e) {
                // echo $e->getMessage();
            }
        }

        return $check;
    }

    public function devServerSetting(bool $devMode, string $devHost = "", string $devHostWeb = "") {
        $this->devMode = $devMode;

        if ($devMode) {
            $this->devServerHost = $this->lastSlashAdd($devHost);
            $this->devServerHostWeb = $devHostWeb !== "" ? $this->lastSlashAdd($devHostWeb) : $this->devServerHost;

            $this->devServerAccessStatus = $this->devServerAccess();
        } else {
            $this->viteReloadPath = "";
        }
    }

    private function typeGetArrayCreate(string|null $path, string $type){
        if($path === null){
            $path = "";
        }

        return  [
            "type" => $type,
            "path" => $path,
        ];
    }

    public function typeGetExtension(string $extension) {
        // 拡張子からファイルのタイプを取得する
        return TypeGet::extension($extension);
    }

    public function typeGetPath(string|null $path) {
        // パスからファイルのタイプを取得する
        return TypeGet::path($path);
    }

    public function typeWebPathGet(string $path) {
        // タイプとWebのパスを取得する
        $webPath = $this->buildWebPathGet($path);
        $type = $this->typeGetPath($webPath);

        return $this->typeGetArrayCreate($webPath, $type);
    }

    public function viteReloadPathGet($delete = true) {
        // Viteのリロードスクリプトパスを取得する
        $result = "";

        if ($this->viteReloadPath !== "") {
            $result = $this->viteReloadPath;

            if ($delete) {
                $this->viteReloadPath = "";
            }
        }

        return $result;
    }

    public function typeViteReloadPathGet() {
        // タイプとViteのリロードスクリプトパスを取得する
        // なければ空の配列を返す
        $result = [];
        $reloadPath = $this->viteReloadPathGet();

        if ($reloadPath !== "") {
            $result = $this->typeGetArrayCreate($reloadPath, "script");
        }

        return $result;
    }

    public function viteReloadHtmlGet($delete = true) {
        // Viteのリロードスクリプト用HTMLを取得する
        $html = "";
        $reloadPath = $this->viteReloadPathGet($delete);

        if ($reloadPath !== "") {
            $html = $this->moduleLinkCreate($reloadPath);
        }

        return $html;
    }

    public function separateByTypeWebPathListGet(array $pathList) {
        // タイプ別に分け、Webのパスをリストで取得する
        $result = [
            "style" => [],
            "script" => [],
        ];

        $dataList = $this->typeWebPathListGet($pathList);

        if ($dataList !== []) {
            foreach ($dataList as $data) {
                if ($data["type"] === "style") {
                    $result["style"][] = $data["path"];
                } elseif ($data["type"] === "script") {
                    $result["script"][] = $data["path"];
                }
            }
        }

        return $result;
    }

    public function typeWebPathListGet(array $pathList) {
        // タイプとWebのパスをリストで取得する
        $result = [];
        $viteReload = $this->typeViteReloadPathGet();

        if ($viteReload !== []) {
            $result[] = $viteReload;
        }

        foreach ($pathList as $path) {
            $result[] = $this->typeWebPathGet($path);
        }

        return $result;
    }

    public function sourcePathGet(string|null $webPath): string {
        // Webのパスからソースのパスを取得する
        if($webPath === null){
            return "";
        }

        if ($this->devMode && $this->devServerHost !== "" && $this->devServerAccessStatus && str_contains($webPath, $this->devServerHostWeb)) {
            // 開発サーバーが動いていればそのまま返す
            return str_replace($this->devServerHostWeb, "", $webPath);
        }

        $webPath = str_replace($this->buildPath, "", $webPath);

        $sourceData = array_filter($this->manifestData, function ($manifest) use ($webPath) {
            return $manifest["file"] === $webPath;
        });

        $source = $sourceData ? array_key_first($sourceData) : "";

        return $source;
    }
}
