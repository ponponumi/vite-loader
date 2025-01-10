<?php

require __DIR__ . "/../vendor/autoload.php";

// 間違ったパス
$manifest = \Ponponumi\ViteLoader\Manifest::load(__DIR__ . "/build/vite/manifest.json");
var_dump($manifest);

// 正しいパス
$manifest = \Ponponumi\ViteLoader\Manifest::load(__DIR__ . "/build/.vite/manifest.json");
var_dump($manifest);
