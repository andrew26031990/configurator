<?php
include '../functions.php';

$filter_id = (int)$_POST['filter_id'];
$f_name    = mysqli_real_escape_string($mysqli, isset($_POST['f_name']) ? $_POST['f_name'] : '');

// Мультивыбор сборок/типов: filter_tree[] (может быть пустым — тогда связи снимаются)
$trees = isset($_POST['filter_tree']) ? (array)$_POST['filter_tree'] : array();
$trees = array_values(array_unique(array_filter(array_map('intval', $trees), function($v){ return $v > 0; })));

if ($filter_id <= 0 || $f_name === '') {
    echo 'Не все поля заполнены';
    return;
}

$mysqli->query("UPDATE filters SET f_name = '$f_name' WHERE id = $filter_id");

// Пересинхронизация связей фильтр↔категория
$mysqli->query("DELETE FROM tree_filter WHERE filter_id = $filter_id");
foreach ($trees as $tid) {
    $mysqli->query("INSERT INTO `tree_filter` (`tree_id`, `filter_id`) VALUES ($tid, $filter_id)");
}

echo 'Название фильтра обновлено';
