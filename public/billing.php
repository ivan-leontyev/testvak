<?php
require __DIR__ . '/../lib/bootstrap.php';

$periods = db()->query("SELECT DISTINCT period FROM invoices ORDER BY period DESC")->fetchAll(PDO::FETCH_COLUMN);
$period = isset($_GET['period']) ? $_GET['period'] : '';
if (!in_array($period, $periods) && $periods) {
    $period = $periods[0];
}

page_header('Счета за месяц');
?>
<h1>Счета за месяц</h1>

<form class="filter actions" id="billing-period">
    Месяц:
    <select name="period">
        <?php foreach ($periods as $p): ?>
            <option value="<?= h($p) ?>"<?= $p == $period ? ' selected' : '' ?>><?= h($p) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="button" id="billing-show">Показать</button>
    <button type="button" id="billing-apply">Применить</button>
</form>

<div id="billing-msg" class="msg" style="display:none"></div>

<table class="grid" id="billing-preview">
    <thead>
    <tr>
        <th>Клиент</th>
        <th>Счёт</th>
        <th class="r">Было, ₽</th>
        <th class="r">Стало, ₽</th>
        <th class="r">Изменение, ₽</th>
    </tr>
    </thead>
    <tbody>
    <tr><td colspan="5" class="empty">Выберите месяц и нажмите «Показать»</td></tr>
    </tbody>
</table>

<script src="js/billing_preview.js"></script>
<script src="js/billing_apply.js"></script>
<?php page_footer();
