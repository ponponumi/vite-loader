<?php

require __DIR__ . "/../vendor/autoload.php";

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TypeGetのテスト</title>
</head>

<body>
    <h1>TypeGetのテスト</h1>

    <table>
        <thead>
            <tr>
                <th>想定される結果</th>
                <th>実行結果</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>style</td>
                <td><?= \Ponponumi\ViteLoader\TypeGet::extension("css") ?></td>
            </tr>
            <tr>
                <td>style</td>
                <td><?= \Ponponumi\ViteLoader\TypeGet::extension("sass") ?></td>
            </tr>
            <tr>
                <td>style</td>
                <td><?= \Ponponumi\ViteLoader\TypeGet::extension("scss") ?></td>
            </tr>
            <tr>
                <td>style</td>
                <td><?= \Ponponumi\ViteLoader\TypeGet::extension("less") ?></td>
            </tr>
            <tr>
                <td>style</td>
                <td><?= \Ponponumi\ViteLoader\TypeGet::extension("stylus") ?></td>
            </tr>
            <tr>
                <td>style</td>
                <td><?= \Ponponumi\ViteLoader\TypeGet::extension("styl") ?></td>
            </tr>
            <tr>
                <td>script</td>
                <td><?= \Ponponumi\ViteLoader\TypeGet::extension("js") ?></td>
            </tr>
            <tr>
                <td>script</td>
                <td><?= \Ponponumi\ViteLoader\TypeGet::extension("jsx") ?></td>
            </tr>
            <tr>
                <td>script</td>
                <td><?= \Ponponumi\ViteLoader\TypeGet::extension("ts") ?></td>
            </tr>
            <tr>
                <td>script</td>
                <td><?= \Ponponumi\ViteLoader\TypeGet::extension("tsx") ?></td>
            </tr>
            <tr>
                <td>script</td>
                <td><?= \Ponponumi\ViteLoader\TypeGet::extension("coffee") ?></td>
            </tr>
            <tr>
                <td>(空の文字列)</td>
                <td><?= \Ponponumi\ViteLoader\TypeGet::extension("jpg") ?></td>
            </tr>
            <tr>
                <td>(空の文字列)</td>
                <td><?= \Ponponumi\ViteLoader\TypeGet::extension("png") ?></td>
            </tr>
        </tbody>
    </table>
</body>

</html>
