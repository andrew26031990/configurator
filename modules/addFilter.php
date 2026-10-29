<?php

include_once '../functions.php';

if ($_POST['filter'] != '') {
    $name = mysqli_real_escape_string($mysqli, $_POST['filter']);

    // Мультивыбор сборок/типов: filter_tree[] -> связи tree_filter
    $trees = isset($_POST['filter_tree']) ? (array)$_POST['filter_tree'] : array();
    $trees = array_values(array_unique(array_filter(array_map('intval', $trees), function($v){ return $v > 0; })));

    try {
        $insert = $mysqli->query("INSERT INTO filters (f_name) VALUES ('".$name."')");
        if (!$insert) {
            throw new Exception('Ошибка добавления фильтра: '.$mysqli->error);
        }
        $filter_id = $mysqli->insert_id;

        foreach ($trees as $tid) {
            $mysqli->query("INSERT INTO `tree_filter` (`tree_id`, `filter_id`) VALUES ($tid, $filter_id)");
        }

        echo 'Фильтр успешно добавлен в базу';
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }
} else {
    echo 'Не все поля заполнены';
}
