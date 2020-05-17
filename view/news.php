<?php

/** @var $news \model\News */
?>

<div class="row single-post">
    <div class="user-block col-400px">
        <img src="<?= getImageLink($news->getAttribute('image')) ?>" /><br/>
        <?= $news->getAttribute('created_at') ?>
    </div>
    <div class="message-block margin-left-400px">
        <?= $news->getAttribute('title') ?>
        <?= $news->getAttribute('message') ?>
    </div>
</div>
