<?php
require __DIR__ . '/../../lib/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(array('error' => 'Метод не поддерживается'), 405);
}

$period = isset($_GET['period']) ? $_GET['period'] : (isset($_POST['period']) ? $_POST['period'] : '');
if (!preg_match('/^\d{4}-\d{2}$/', $period)) {
    json_out(array('error' => 'Неверный месяц'), 400);
}

json_out(billing_apply_period($period));
