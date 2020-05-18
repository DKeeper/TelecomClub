<?php
/** @var $postData array */
/** @var $filterValues array */
/**
 * @param string $value
 * @param string $selected
 * @param string $text
 */
function printOption(string $value, string $selected, string $text)
{
    $template = '<option value="%s">%s</option>';

    if ($value === $selected) {
        $template = str_replace('value', 'selected value', $template);
    }

    echo sprintf($template, $value, $text);
}
?>
<form id="filter-form" method="post" autocomplete="off">
    <div class="form-group filter-sort required">
        <label for="sort">Тип сортировки</label>
        <select id="sort" class="form-control" name="filters[sort]">
            <?php
            foreach ($filterValues['sort'] as $value => $text) {
                printOption((string) $value, (string) $postData['sort'], (string) $text);
            }
            ?>
        </select>
    </div>
    <div class="form-group filter-sort required">
        <label for="limit">Количество записей</label>
        <select id="limit" class="form-control" name="filters[limit]">
            <?php
            foreach ($filterValues['limit'] as $value => $text) {
                printOption((string) $value, (string) $postData['limit'], (string) $text);
            }
            ?>
        </select>
    </div>
    <div class="form-group filter-sort required">
        <label for="page">Страница выборки</label>
        <input type="number" id="page" name="filters[page]" min="1" max="100" value="<?= $postData['page'] ?? 1 ?>">
    </div>
    <button type="submit" class="btn btn-primary btn-block"><?= 'Filter' ?></button>
</form>
