<?php

/** @var $user \model\User */
/** @var $dataProvider \model\News[] */
/** @var $postData array */
/** @var $filterValues array */
/**
 * @param $value
 * @param $selected
 * @param $text
 */
function printOption($value, $selected, $text)
{
    $template = '<option value="%s">%s</option>';

    if ($value === $selected) {
        $template = str_replace('value', 'selected value', $template);
    }

    echo sprintf($template, $value, $text);
}
?>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <meta name="robots" content="noindex, nofollow">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= 'News' ?></title>
        <link href="<?= TEST_BASE_URL ?>assets/css.css" rel="stylesheet">
    </head>
    <body>
        <div class="container">
            <div class="row">
                <form id="filter-form" method="post" autocomplete="off">
                    <div class="form-group filter-sort required">
                        <label for="sort">Тип сортировки</label>
                        <select id="sort" class="form-control" name="filters[sort]">
                            <?php
                            foreach ($filterValues['sort'] as $value => $text) {
                                printOption($value, $postData['sort'], $text);
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group filter-sort required">
                        <label for="limit">Количество записей</label>
                        <select id="limit" class="form-control" name="filters[limit]">
                            <?php
                            foreach ($filterValues['limit'] as $value => $text) {
                                printOption($value, $postData['limit'], $text);
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group filter-sort required">
                        <label for="page">Страница выборки</label>
                        <select id="page" class="form-control" name="filters[page]">
                            <?php
                            foreach ($filterValues['page'] as $value => $text) {
                                printOption($value, $postData['page'], $text);
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block"><?= 'Filter' ?></button>
                </form>
            </div>
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
