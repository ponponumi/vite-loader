<?php

require __DIR__ . "/../vendor/autoload.php";

// 正しいパス
$manifest = \Ponponumi\ViteLoader\Manifest::load(__DIR__ . "/build/.vite/manifest.json");
var_dump($manifest);

// 間違ったパス
$manifest = \Ponponumi\ViteLoader\Manifest::load(__DIR__ . "/build/vite/manifest.json");
var_dump($manifest);
