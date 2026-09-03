<?php
require_once __DIR__ . '/../functions.php';
require_admin();

json_out(db_rows(
    $mysqli,
    'SELECT COUNT(id) AS quantity, date FROM `orders` GROUP BY date ORDER BY date'
));
