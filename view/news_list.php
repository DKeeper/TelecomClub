<?php

/** @var $user \model\User */
/** @var $dataProvider array */
/** @var $postData array */
/** @var $filterValues array */
?>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <meta name="robots" content="noindex, nofollow">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= 'News' ?></title>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <?= viewPhpFile(getViewPath() . 'news_filter.php', [
                    'filterValues' => $filterValues,
                    'postData' => $postData,
                ]) ?>
            </div>
<?php
foreach ($dataProvider as $news) {
    echo viewPhpFile(getViewPath() . 'news.php', [
        'news' => $news,
    ]);
}
?>
        </div>
        <link href="<?= TEST_BASE_URL ?>assets/f-css.css" rel="stylesheet">
    </body>
</html>
