<?php

require __DIR__ . "/../vendor/autoload.php";

// 間違ったパス
$manifest = \Ponponumi\ViteLoader\Manifest::load(__DIR__ . "/build/vite/manifest.json");
var_dump($manifest);

// 正しいパス
$manifest = \Ponponumi\ViteLoader\Manifest::load(__DIR__ . "/build/.vite/manifest.json");
var_dump($manifest);

$path = \Ponponumi\ViteLoader\Manifest::dataGet("asset/js/script.js", $manifest);
var_dump($path);

$webPath = \Ponponumi\ViteLoader\Manifest::webPathGet(
    "asset/js/script.js",
    $manifest,
    "http://localhost"
);
var_dump($webPath);

$webPath = \Ponponumi\ViteLoader\Manifest::webPathGet(
    "asset/js/script.js",
    $manifest,
    "http://localhost",
    true,
    "http://localhost:5173",
    true
);
var_dump($webPath);

$webPath = \Ponponumi\ViteLoader\Manifest::webPathGet(
    "asset/js/jsfile.js",
    $manifest,
    "http://localhost",
    true,
    "http://localhost:5173",
    true,
    true
);
var_dump($webPath);
