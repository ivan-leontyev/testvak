<?php
require __DIR__ . '/../../lib/bootstrap.php';

$trip_id = isset($_GET['trip_id']) ? (int)$_GET['trip_id'] : 0;
$trip = db()->query("SELECT id, number, cost FROM trips WHERE id = $trip_id")->fetch();
if (!$trip) {
    json_out(array('error' => 'Рейс не найден'), 404);
}

$num = function ($value) {
    $value = (float)$value;
    return abs($value - round($value)) < 1e-9 ? (int)round($value) : $value;
};

$stats = array();
$rows = db()->query("SELECT client_id, COUNT(*) AS cargo_count, SUM(weight_kg) AS weight_kg
    FROM cargo
    WHERE trip_id = $trip_id
    GROUP BY client_id")->fetchAll();
foreach ($rows as $row) {
    $stats[$row['client_id']] = $row;
}

$clients = array();
foreach (calc_share_m3($trip_id) as $client_id => $share) {
    $stat = isset($stats[$client_id]) ? $stats[$client_id] : array('cargo_count' => 0, 'weight_kg' => 0);
    $clients[] = array(
        'client_id' => (int)$client_id,
        'name' => $share['name'],
        'cargo_count' => (int)$stat['cargo_count'],
        'weight_kg' => $num($stat['weight_kg']),
        'volume_m3' => $num($share['volume_m3']),
        'share' => $num($share['share']),
        'amount' => number_format($share['amount'], 2, '.', ''),
    );
}

json_out(array(
    'trip_id' => (int)$trip['id'],
    'number' => $trip['number'],
    'cost' => number_format($trip['cost'], 2, '.', ''),
    'clients' => $clients,
));
