<?php

namespace Ponponumi\ViteLoader;

class ViteLoader{
  public $manifestPath;
  public $buildPath;
  public $manifestData;
  public $errorMode;

  public function __construct($manifestPath,$buildPath="",$errorMode=false,array $viteDevServer=[]){
    if($buildPath !== ""){
      // ビルドパスの文字があれば
      if(substr($buildPath,-1) !== "/"){
        // 最後の文字がスラッシュでなければ
        $buildPath .= "/";
      }
    }

    $this->manifestPath = $manifestPath;
    $this->buildPath = $buildPath;

    $this->errorMode = $errorMode;

    $json = file_get_contents($manifestPath);
    $data = json_decode($json,true);
    $this->manifestData = $data;
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
      return $this->buildPath . $data;
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
          $html = '<link rel="stylesheet" href="' . $url . '">';
          break;

        case "js":
          $html = '<script src="' . $url . '"></script>';
          break;
      }
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
}
