<?php

/** @var $news array */
?>

<div class="flexbox">
    <div class="box image">
        <img src="<?= getImageLink($news['image']) ?>" /><br/>
        <?= $news['created_at'] ?>
    </div>
    <div class="box text">
        <?= $news['title'] ?> [<?= \model\News::getCategoryLabels()[$news['category']] ?? 'N/a' ?>]<br/>
        <?= $news['message'] ?> ... <br/>
        <a href="<?= TEST_BASE_URL ?>?q=news&id=<?= $news['id']?>">Read more</a>
    </div>
</div>
