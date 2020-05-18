<?php

/** @var $news array */
?>

<div class="row single-post">
    <div class="user-block col-400px">
        <img src="<?= getImageLink($news['image']) ?>" /><br/>
        <?= $news['created_at'] ?>
    </div>
    <div class="message-block margin-left-400px">
        <?= $news['title'] ?>
        <?= $news['message'] ?>
    </div>
</div>
