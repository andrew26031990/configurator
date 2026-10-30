<?php
require_once __DIR__ . '/../functions.php';
require_admin();
require_capability('filters.manage');

$name = post_str('filter', 200);

// Мультивыбор сборок/типов: filter_tree[] -> связи tree_filter
$treesRaw = isset($_POST['filter_tree']) && is_array($_POST['filter_tree']) ? $_POST['filter_tree'] : array();
$trees = array();
foreach ($treesRaw as $t) {
    $tid = filter_var($t, FILTER_VALIDATE_INT);
    if ($tid !== false && $tid > 0) {
        $trees[$tid] = $tid;
    }
}
$trees = array_values($trees);

if ($name === '') {
    http_response_code(400);
    exit('Не все поля заполнены');
}

try {
    $mysqli->begin_transaction();

    db_exec($mysqli, 'INSERT INTO filters (f_name) VALUES (?)', array($name));
    $filterId = (int) $mysqli->insert_id;

    foreach ($trees as $tid) {
        db_exec($mysqli, 'INSERT INTO tree_filter (tree_id, filter_id) VALUES (?, ?)', array($tid, $filterId));
    }

    $mysqli->commit();
    echo 'Фильтр успешно добавлен в базу';
} catch (Throwable $e) {
    $mysqli->rollback();
    error_log('addFilter: ' . $e->getMessage());
    http_response_code(500);
    echo 'Ошибка при добавлении фильтра';
}
