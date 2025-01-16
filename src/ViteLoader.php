<?php

namespace Ponponumi\ViteLoader;

class ViteLoader
{
    public $manifestPath;
    public $buildPath;
    public $manifestData = [];
    public $errorMode;
    public bool $devMode = false;
    public string $devServerHost = "";
    public string $devServerHostWeb = "";
    public bool $devServerAccessStatus = false;
    public string $viteReloadPath = "";
    public $moduleMode = true;

    /**
     * コンストラクタです
     *
     * @param string $manifestPath ここには、manifest.jsonのパスを渡して下さい。
     * @param string $buildPath ここには、ビルドフォルダのパスを渡して下さい。
     * @param mixed $errorMode エラーモードを有効にする場合は、trueを渡して下さい。
     * @param array $viteDevServer 開発サーバーを有効にする場合は、trueを渡して下さい。
     * @param mixed $moduleMode モジュールスクリプトモードを有効にする場合は、trueを渡して下さい。
     */
    public function __construct(
        string $manifestPath,
        string $buildPath = "",
        $errorMode = false,
        array $viteDevServer = [],
        $moduleMode = true
    )
    {
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

    /**
     * URLの最後にスラッシュを渡します。
     *
     * @param string $path ここには、URLを渡して下さい。
     * @return string
     */
    public function lastSlashAdd(string $path): string
    {
        // 最後にスラッシュを追加する
        return LinkCreate::urlLastSlashAdd($path);
    }

    /**
     * マニフェストのデータを取得します
     * @return array
     */
    public function manifestDataGet(): array
    {
        // マニフェストデータを取得する
        return $this->manifestData;
    }

    /**
     * ソースのパスからビルド後のデータを取得します。ファイルが見つからない場合、errorModeが有効ならエラーが発生し、無効なら空の配列を返します。
     *
     * @param mixed $sourcePath ここには、ソースのパスを渡して下さい。
     * @return array
     */
    public function buildDataGet($sourcePath): array
    {
        // ソースのパスからビルド後のデータを取得する
        // なければ空の配列を返す
        return Manifest::dataGet($sourcePath, $this->manifestData, $this->errorMode);
    }

    /**
     * ソースのパスからビルド後のパスを取得します。ファイルが見つからない場合、errorModeが有効ならエラーが発生し、無効なら空の文字列を返します。
     * 
     * @param mixed $sourcePath ここには、ソースのパスを渡して下さい。
     * @return string
     */
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

    public function moduleModeSet($set = true): void
    {
        $this->moduleMode = $set;
    }

    public function cssLinkCreate(string $url): string
    {
        // CSSのリンクを作る
        return LinkCreate::css($url);
    }

    public function jsLinkCreate(string $url, $moduleMode = false): string
    {
        return LinkCreate::js($url, $moduleMode);
    }

    public function moduleLinkCreate(string $url): string
    {
        return LinkCreate::module($url);
    }

    public function htmlGet($sourcePath, string $getType = ""): string
    {
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

    public function html($sourcePath, string $getType = ""): void
    {
        // HTMLに出力する
        echo $this->htmlGet($sourcePath, $getType);
    }

    public function htmlListGet(array $sourcePathList, string $getType = ""): string
    {
        // ソースのパスリストからHTMLを返す
        $html = "";

        foreach ($sourcePathList as $sourcePath) {
            $html .= $this->htmlGet($sourcePath, $getType);
        }

        return $html;
    }

    public function htmlList(array $sourcePathList, string $getType = ""): void
    {
        // ソースのパスリストからHTMLを出力する
        echo $this->htmlListGet($sourcePathList, $getType);
    }

    private function devServerAccess(): bool
    {
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

    public function devServerSetting(bool $devMode, string $devHost = "", string $devHostWeb = ""): void
    {
        $this->devMode = $devMode;

        if ($devMode) {
            $this->devServerHost = $this->lastSlashAdd($devHost);
            $this->devServerHostWeb = $devHostWeb !== "" ? $this->lastSlashAdd($devHostWeb) : $this->devServerHost;

            $this->devServerAccessStatus = $this->devServerAccess();
        } else {
            $this->viteReloadPath = "";
        }
    }

    private function typeGetArrayCreate(string|null $path, string $type): array
    {
        if($path === null){
            $path = "";
        }

        return  [
            "type" => $type,
            "path" => $path,
        ];
    }

    public function typeGetExtension(string $extension): string
    {
        // 拡張子からファイルのタイプを取得する
        return TypeGet::extension($extension);
    }

    public function typeGetPath(string|null $path): string
    {
        // パスからファイルのタイプを取得する
        return TypeGet::path($path);
    }

    public function typeWebPathGet(string $path): array
    {
        // タイプとWebのパスを取得する
        $webPath = $this->buildWebPathGet($path);
        $type = $this->typeGetPath($webPath);

        return $this->typeGetArrayCreate($webPath, $type);
    }

    public function viteReloadPathGet($delete = true): string
    {
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

    public function typeViteReloadPathGet(): array
    {
        // タイプとViteのリロードスクリプトパスを取得する
        // なければ空の配列を返す
        $result = [];
        $reloadPath = $this->viteReloadPathGet();

        if ($reloadPath !== "") {
            $result = $this->typeGetArrayCreate($reloadPath, "script");
        }

        return $result;
    }

    public function viteReloadHtmlGet($delete = true): string
    {
        // Viteのリロードスクリプト用HTMLを取得する
        $html = "";
        $reloadPath = $this->viteReloadPathGet($delete);

        if ($reloadPath !== "") {
            $html = $this->moduleLinkCreate($reloadPath);
        }

        return $html;
    }

    public function separateByTypeWebPathListGet(array $pathList): array
    {
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

    public function typeWebPathListGet(array $pathList): array
    {
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

    public function sourcePathGet(string|null $webPath): string
    {
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
