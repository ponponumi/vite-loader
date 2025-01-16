# vite-loader

このパッケージは、Viteでビルドした、CSSとJSをHTMLで読み込ませるためのパッケージです。

## Composerでのインストールについて

次のコマンドを実行する事で、インストール可能です。

```bash
composer require ponponumi/vite-loader
```

## パッケージの読み込みについて

次のように入力してください。(autoload.phpへのパスは、必要に応じて修正してください)

```php
require_once __DIR__ . "/vendor/autoload.php";

use Ponponumi\ViteLoader\ViteLoader;
```

## 使い方

### インスタンスの作成

例えば、このような構成だったとします。

* Webサーバー: `http://localhost`
* 開発サーバー: Dockerのviteというコンテナで、ポート5173で動作

この場合、次のように渡してください。

```php
require_once __DIR__ . "/vendor/autoload.php";

use Ponponumi\ViteLoader\ViteLoader;

$viteLoader = new ViteLoader(__DIR__ . "/build/.vite/manifest.json","http://localhost/build");
$viteLoader->devServerSetting(true,"http://vite:5173","http://localhost:5173");
```

## ライセンスについて

このパッケージは、MITライセンスとして作成されています。
