<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 30/04/18
 * Time: 15:30
 */


$clients = range(0, 10);
$destinations = range(0, 10);
$referenceDate = "2017-10-10";


foreach (range(0, 1000 - 1) as $index) {
    $index = rand(0, 3);

    $set = [
        'client' => "'A" . rand(0, 10) . "'",
        'carrier' => "'B" . rand(0, 10) . "'",
        'destination' => "'C" . rand(0, 10) . "'",
        "int0" => (int) ($index == 0),
        "int1" => (int) ($index == 1),
        "int2" => (int) ($index == 2),
        "int3" => (int) ($index == 3),
    ];

    array_unshift($set, "'" . md5($set['client'] . "_" . $set['carrier'] . "_" . $set['destination']) . "'");

    $values[] = "(" . implode(', ', $set) . ")";
}

$values = implode(', ' . PHP_EOL, $values);

$query = "INSERT INTO Daily_2018_04_30_Agg_interval_cost 
            (hash_id, client, carrier, destination, interval_0, interval_1, interval_2, interval_3) VALUES $values
            ON DUPLICATE KEY UPDATE interval_0 = interval_0 + IFNULL(VALUES(interval_0), 0)
            , interval_1 = interval_1 + IFNULL(VALUES(interval_1), 0)
            , interval_2 = interval_2 + IFNULL(VALUES(interval_2), 0)
            , interval_3 = interval_3 + IFNULL(VALUES(interval_3), 0)
";

echo $query . PHP_EOL;