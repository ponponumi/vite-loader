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
     * @param string $sourcePath ここには、ソースのパスを渡して下さい。
     * @return array
     */
    public function buildDataGet(string $sourcePath): array
    {
        // ソースのパスからビルド後のデータを取得する
        // なければ空の配列を返す
        return Manifest::dataGet($sourcePath, $this->manifestData, $this->errorMode);
    }

    /**
     * ソースのパスからビルド後のパスを取得します。ファイルが見つからない場合、errorModeが有効ならエラーが発生し、無効なら空の文字列を返します。
     *
     * @param string $sourcePath ここには、ソースのパスを渡して下さい。
     * @return string
     */
    public function buildPathGet(string $sourcePath): string
    {
        // ソースのパスからビルド後のパスを取得する
        // なければ空の文字を返す
        return Manifest::pathGet($sourcePath, $this->manifestData, $this->errorMode);
    }

    /**
     * ソースのパスからビルド後のWebのパスを取得します。ファイルが見つからない場合、errorModeが有効ならエラーが発生し、無効なら空の文字列を返します。
     *
     * @param string $sourcePath ここには、ソースのパスを渡して下さい。
     * @return string
     */
    public function buildWebPathGet(string $sourcePath): string
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

    /**
     * モジュールスクリプトモードを有効にするか設定します。
     *
     * @param mixed $set ここには、新たな値を設定してください。
     * @return void
     */
    public function moduleModeSet($set = true): void
    {
        $this->moduleMode = $set;
    }

    /**
     * CSSのHTMLのリンクを作ります。
     *
     * @param string $url ここには、CSSのURLを渡して下さい。
     * @return string
     */
    public function cssLinkCreate(string $url): string
    {
        // CSSのリンクを作る
        return LinkCreate::css($url);
    }

    /**
     * JSのHTMLのリンクを作ります。
     *
     * @param string $url ここには、JSのURLを渡して下さい。
     * @param mixed $moduleMode モジュールモードを有効にする場合は、trueを渡して下さい。
     * @return string
     */
    public function jsLinkCreate(string $url, $moduleMode = false): string
    {
        return LinkCreate::js($url, $moduleMode);
    }


    /**
     * JSモジュールのHTMLのリンクを作ります。
     *
     * @param string $url ここには、JSのURLを渡して下さい。
     * @return string
     */
    public function moduleLinkCreate(string $url): string
    {
        return LinkCreate::module($url);
    }

    /**
     * HTMLを取得します。
     *
     * @param string $sourcePath ここには、ソースのパスを渡して下さい。
     * @param string $getType ここには、どのタイプを取得するかを渡して下さい。
     * @return string
     */
    public function htmlGet(string $sourcePath, string $getType = ""): string
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

    /**
     * HTMLを出力します。
     *
     * @param string $sourcePath ここには、ソースのパスを渡して下さい。
     * @param string $getType ここには、どのタイプを取得するかを渡して下さい。
     * @return void
     */
    public function html(string $sourcePath, string $getType = ""): void
    {
        // HTMLに出力する
        echo $this->htmlGet($sourcePath, $getType);
    }

    /**
     * ソースのパスリストから、HTMLを返します。
     *
     * @param array $sourcePathList ここには、ソースのパスリストを渡して下さい。
     * @param string $getType ここには、どのタイプを取得するかを渡して下さい。
     * @return string
     */
    public function htmlListGet(array $sourcePathList, string $getType = ""): string
    {
        // ソースのパスリストからHTMLを返す
        $html = "";

        foreach ($sourcePathList as $sourcePath) {
            $html .= $this->htmlGet($sourcePath, $getType);
        }

        return $html;
    }

    /**
     * ソースのパスリストから、HTMLを出力します。
     *
     * @param array $sourcePathList ここには、ソースのパスリストを渡して下さい。
     * @param string $getType ここには、どのタイプを取得するかを渡して下さい。
     * @return void
     */
    public function htmlList(array $sourcePathList, string $getType = ""): void
    {
        // ソースのパスリストからHTMLを出力する
        echo $this->htmlListGet($sourcePathList, $getType);
    }

    /**
     * 開発サーバーにアクセスします。
     *
     * @return bool
     */
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

    /**
     * 開発サーバーの設定をします。
     *
     * @param bool $devMode デバッグモードを有効にするか渡して下さい。
     * @param string $devHost 開発サーバーのホストを渡して下さい。
     * @param string $devHostWeb 開発サーバーのWeb上のホストを渡して下さい。省略した場合は、$devHostの値を使います。
     * @return void
     */
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

    /**
     * タイプとパスに分けて取得するためのベースの配列を作ります。
     *
     * @param string|null $path ここには、パスを渡して下さい。
     * @param string $type ここには、種類を渡して下さい。
     * @return array
     */
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

    /**
     * 拡張子からファイルのタイプを取得します。
     *
     * @param string $extension ここには、拡張子を渡して下さい。
     * @return string
     */
    public function typeGetExtension(string $extension): string
    {
        // 拡張子からファイルのタイプを取得する
        return TypeGet::extension($extension);
    }

    /**
     * パスから、ファイルのタイプを取得します。
     *
     * @param string|null $path ここには、パスを渡して下さい。
     * @return string
     */
    public function typeGetPath(string|null $path): string
    {
        // パスからファイルのタイプを取得する
        return TypeGet::path($path);
    }

    /**
     * タイプとWebのパスを取得します。
     *
     * @param string $path ここには、ソースのパスを渡して下さい。
     * @return array
     */
    public function typeWebPathGet(string $path): array
    {
        // タイプとWebのパスを取得する
        $webPath = $this->buildWebPathGet($path);
        $type = $this->typeGetPath($webPath);

        return $this->typeGetArrayCreate($webPath, $type);
    }

    /**
     * Viteのhmrのパスを取得します。
     *
     * @param mixed $delete ここには、取得後に削除するかを渡して下さい。
     * @return string
     */
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

    /**
     * Viteのhmrのパスと、タイプを取得します。
     * @return array
     */
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

    /**
     * Viteのhmrを読み込むHTMLを取得します。
     *
     * @param mixed $delete 削除するかどうかを指定してください。
     * @return string
     */
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

    /**
     * タイプ別に分け、Webのパスをリストで取得します。
     *
     * @param array $pathList ここには、ソースのパスの一覧を渡して下さい。
     * @return array
     */
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

    /**
     * タイプとWebのパスをリストで取得します。
     *
     * @param array $pathList ここには、ソースのパスのリストを渡して下さい。
     * @return array
     */
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

    /**
     * Webのパスから、ソースのパスを取得します。
     *
     * @param string|null $webPath ここには、Webのパスを渡して下さい。
     * @return string
     */
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
