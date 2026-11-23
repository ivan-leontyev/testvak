<?php
require __DIR__ . '/../../lib/bootstrap.php';

$period = isset($_GET['period']) ? $_GET['period'] : '';
if (!preg_match('/^\d{4}-\d{2}$/', $period)) {
    json_out(array('error' => 'Неверный месяц'), 400);
}

json_out(billing_preview_period($period));
