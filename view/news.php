<?php

/** @var $news array */
?>

<div class="flexbox">
    <div class="box image">
        <img src="<?= getImageLink($news['image']) ?>" /><br/>
        <?= $news['created_at'] ?>
    </div>
    <div class="box text">
        <?= $news['title'] ?><br/>
        <?= $news['message'] ?>
    </div>
</div>
