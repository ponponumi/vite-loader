<?php

namespace Ponponumi\ViteLoader;

class ViteLoader{
  public $manifestPath;
  public $buildPath;
  public $manifestData;
  public $errorMode;
  public bool $devMode = false;
  public string $devServerHost = "";
  public string $devServerHostWeb = "";
  public bool $devServerAccessStatus;
  public string $viteReloadPath = "";

  public function __construct($manifestPath,$buildPath="",$errorMode=false,array $viteDevServer=[]){
    $this->manifestPath = $manifestPath;
    $this->buildPath = $this->lastSlashAdd($buildPath);

    $this->errorMode = $errorMode;

    $json = file_get_contents($manifestPath);
    $data = json_decode($json,true);
    $this->manifestData = $data;

    $this->devServerSetting(
      array_key_exists('devMode', $viteDevServer) ? boolval($viteDevServer['devMode']) : false,
      array_key_exists('devHost', $viteDevServer) ? strval($viteDevServer['devHost']) : "",
      array_key_exists('devHostWeb', $viteDevServer) ? strval($viteDevServer['devHostWeb']) : ""
    );
  }

  public function lastSlashAdd($path){
    // 最後にスラッシュを追加する
    if($path !== ""){
      // パスの文字があれば
      if(substr($path,-1) !== "/"){
        // 最後の文字がスラッシュでなければ
        $path .= "/";
      }
    }

    return $path;
  }

  public function manifestDataGet(){
    // マニフェストデータを取得する
    return $this->manifestData;
  }

  public function buildDataGet($sourcePath){
    // ソースのパスからビルド後のデータを取得する
    // なければnullを返す
    if(array_key_exists($sourcePath,$this->manifestData)){
      // ある場合
      return $this->manifestData[$sourcePath];
    }else{
      // ない場合
      if($this->errorMode){
        // エラーモードが有効であれば
        throw new \Exception("'$sourcePath' は見つかりません");
      }

      return null;
    }
  }

  public function buildPathGet($sourcePath){
    // ソースのパスからビルド後のパスを取得する
    // なければnullを返す
    $data = $this->buildDataGet($sourcePath);

    if($data){
      // ある場合
      return $data["file"];
    }else{
      // ない場合
      return null;
    }
  }

  public function buildWebPathGet($sourcePath){
    // ソースのパスからビルド後のWeb用パスを取得する
    // なければnullを返す
    $data = $this->buildPathGet($sourcePath);

    if($data){
      // ある場合
      if($this->devMode && $this->devServerHost !== "" && $this->devServerAccessStatus){
        // 開発サーバーからのURLを取得するなら
        return $this->devServerHostWeb . $sourcePath;
      }else{
        // ビルドファイルのURLを取得するなら
        return $this->buildPath . $data;
      }
    }else{
      // ない場合
      return null;
    }
  }

  public function htmlGet($sourcePath){
    // HTMLを取得する
    // なければ空文字を返す
    $html = "";
    $url = $this->buildWebPathGet($sourcePath);

    if($url){
      $extension = pathinfo($url, PATHINFO_EXTENSION);

      switch ($extension) {
        case "css":
        case "scss":
        case "sass":
        case "less":
        case "stylus":
        case "styl":
          $html = '<link rel="stylesheet" href="' . $url . '">';
          break;

        case "js":
        case "ts":
        case "jsx":
        case "tsx":
        case "coffee":
          $html = '<script src="' . $url . '"></script>';
          break;
      }
    }

    if($this->viteReloadPath !== ""){
      $html = '<script type="module" src="' . $this->viteReloadPath . '"></script>' . $html;
      $this->viteReloadPath = "";
    }

    return $html;
  }

  public function html($sourcePath){
    // HTMLに出力する
    echo $this->htmlGet($sourcePath);
  }

  public function htmlListGet(array $sourcePathList){
    // ソースのパスリストからHTMLを返す
    $html = "";

    foreach ($sourcePathList as $sourcePath) {
      $html .= $this->htmlGet($sourcePath);
    }

    return $html;
  }

  public function htmlList(array $sourcePathList){
    // ソースのパスリストからHTMLを出力する
    echo $this->htmlListGet($sourcePathList);
  }

  private function devServerAccess(){
    // 開発サーバーにアクセスする
    // 開発サーバーが動いていればtrue、動いていなければfalseを返す
    $check = false;

    if($this->devServerHost !== ""){
      try{
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $this->devServerHost, [
          // 'timeout' => 5,
          'http_errors' => false,
        ]);

        $check = true;

        $this->viteReloadPath = $this->devServerHostWeb . '@vite/client';
      }catch(\Exception $e){
        // echo $e->getMessage();
      }
    }

    return $check;
  }

  public function devServerSetting(bool $devMode,string $devHost="",string $devHostWeb=""){
    $this->devMode = $devMode;

    if($devMode){
      $this->devServerHost = $this->lastSlashAdd($devHost);
      $this->devServerHostWeb = $devHostWeb !== "" ? $this->lastSlashAdd($devHostWeb) : $this->devServerHost;

      $this->devServerAccessStatus = $this->devServerAccess();
    }
  }
}
