<?php

/** @var $user \model\User */
/** @var $dataProvider \model\News[] */
/** @var $model \model\News */
?>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <meta name="robots" content="noindex, nofollow">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= 'News' ?></title>
        <link href="/assets/css.css" rel="stylesheet">
    </head>
    <body>
        <div class="container">
<?php
foreach ($dataProvider as $news) {
    echo viewPhpFile(getViewPath() . 'news.php', [
        'news' => $news,
    ]);
}
?>
        </div>
    </body>
</html>
