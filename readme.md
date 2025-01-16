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
