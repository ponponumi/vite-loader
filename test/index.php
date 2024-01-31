<?php

require __DIR__ . "/../vendor/autoload.php";

$projectRootPath = __DIR__;
$projectRootUrl = (!empty($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace($_SERVER['DOCUMENT_ROOT'], '', $projectRootPath);

$vite = new \Ponponumi\ViteLoader\ViteLoader(__DIR__ . "/build/.vite/manifest.json",$projectRootUrl . "/build");

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>テスト</title>
  <?php $vite->html("asset/scss/style.scss"); ?>
</head>
<body>
  <h1>テスト</h1>
  <p>テキストテキスト</p>
  <pre>
    <?php var_dump($vite->manifestDataGet()); ?>
  </pre>
  <?php $vite->htmlList(["asset/js/script.js","asset/ts/script.ts"]); ?>
</body>
</html>
