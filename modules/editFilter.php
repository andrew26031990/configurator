<?php
require_once __DIR__ . '/../functions.php';
require_admin();
require_capability('filters.manage');

$id   = post_int('filter_id');
$name = post_str('f_name', 200);

// Мультивыбор сборок/типов: filter_tree[] (может быть пустым — тогда связи снимаются)
$treesRaw = isset($_POST['filter_tree']) && is_array($_POST['filter_tree']) ? $_POST['filter_tree'] : array();
$trees = array();
foreach ($treesRaw as $t) {
    $tid = filter_var($t, FILTER_VALIDATE_INT);
    if ($tid !== false && $tid > 0) {
        $trees[$tid] = $tid;
    }
}
$trees = array_values($trees);

if ($id === null || $id <= 0 || $name === '') {
    http_response_code(400);
    exit('Не все поля заполнены');
}

try {
    db_exec($mysqli, 'UPDATE filters SET f_name = ? WHERE id = ?', array($name, $id));

    // Пересинхронизация связей фильтр↔категория
    db_exec($mysqli, 'DELETE FROM tree_filter WHERE filter_id = ?', array($id));
    foreach ($trees as $tid) {
        db_exec($mysqli, 'INSERT INTO tree_filter (tree_id, filter_id) VALUES (?, ?)', array($tid, $id));
    }

    echo 'Название фильтра обновлено';
} catch (Throwable $e) {
    error_log('editFilter: ' . $e->getMessage());
    http_response_code(500);
    echo 'Ошибка при обновлении фильтра';
}
