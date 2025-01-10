<?php

require __DIR__ . "/../vendor/autoload.php";

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LinkCreateのテスト</title>
</head>

<body>
    <h1>LinkCreateのテスト</h1>

    <table>
        <thead>
            <tr>
                <th>想定される結果</th>
                <th>実行結果</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= htmlspecialchars('<link rel="stylesheet" href="/assets/style.css">') ?></td>
                <td><?= htmlspecialchars(\Ponponumi\ViteLoader\LinkCreate::css("/assets/style.css")) ?></td>
            </tr>
            <tr>
                <td><?= htmlspecialchars('<script src="/assets/script.js"></script>') ?></td>
                <td><?= htmlspecialchars(\Ponponumi\ViteLoader\LinkCreate::js("/assets/script.js")) ?></td>
            </tr>
        </tbody>
    </table>
</body>

</html>
