<?php

/** @var $user \model\User */
/** @var $model \model\News */
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
        <h2><?= $model->getAttribute('title') ?></h2>
        <span>[<?= \model\News::getCategoryLabels()[$model->getAttribute('category')] ?? 'N/a' ?>]</span>
    </div>
    <div class="row text-center">
        <img class="text-center" src="<?= getImageLink($model->getAttribute('image')) ?>">
    </div>
    <div class="row text-justify">
        <div class="news-message"><?= $model->getAttribute('message') ?></div>
    </div>
    <div class="row">
        <a href="<?= TEST_BASE_URL ?>">&larr; Back</a>
    </div>
</div>
<link href="<?= TEST_BASE_URL ?>assets/css.css" rel="stylesheet">
</body>
</html>

