<?php

require __DIR__ . "/../vendor/autoload.php";

$projectRootPath = __DIR__;
$projectRootUrl = (!empty($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace($_SERVER['DOCUMENT_ROOT'], '', $projectRootPath);

$vite = new \Ponponumi\ViteLoader\ViteLoader(__DIR__ . "/build/.vite/manifest.json",$projectRootUrl . "/build",false,[
  "devMode" => true,
  "devHost" => $_ENV["VITE_HOST"] . ":" . $_ENV["VITE_PORT"],
  "devHostWeb" => $_ENV["VITE_HOST_WEB"] . ":" . $_ENV["VITE_PORT"],
]);
// $vite = new \Ponponumi\ViteLoader\ViteLoader(__DIR__ . "/build/.vite/manifest.json",$projectRootUrl . "/build");

// $vite->devServerSetting(false);

// $reloadHTML = $vite->viteReloadHtmlGet();

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

  <ul>
    <li>テキスト</li>
    <li>テキスト</li>
    <li class="test">テキスト</li>
  </ul>

  <pre>
    <?php var_dump($vite->manifestDataGet()); ?>
  </pre>
  <?php $vite->htmlList([
    "asset/js/script.js",
    "asset/ts/script.ts",
    "asset/ts/test.ts",
    "asset/scss/style.scss",
  ]); ?>
  <p>ホスト: <?= htmlspecialchars($_ENV["VITE_HOST"] . ":" . $_ENV["VITE_PORT"]) ?></p>

  <pre>
    <?php var_dump($vite->separateByTypeWebPathListGet([
      "asset/js/script.js",
      "asset/ts/script.ts",
      "asset/ts/test.ts",
      "asset/scss/style.scss",
    ])); ?>
  </pre>

  <p><?= $vite->typeGetExtension("TS") ?></p>
  <p><?= $vite->typeGetExtension("js") ?></p>
  <p><?= $vite->typeGetExtension("css") ?></p>
  <p><?= $vite->typeGetExtension("scss") ?></p>

  <?php

  $webPath = $vite->buildWebPathGet("asset/scss/style.scss");
  $sourcePath = $vite->sourcePathGet($webPath);

  ?>

  <p>Webのパス<?= $webPath ?></p>
  <p>ソースのパス<?= $sourcePath ?></p>
</body>
</html>
