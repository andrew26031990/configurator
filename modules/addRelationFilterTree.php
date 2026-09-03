<?php
require_once __DIR__ . '/../functions.php';
require_admin();

$treeId   = post_int('tree_id');
$filterId = post_int('filter_id');

if ($treeId === null || $treeId <= 0 || $filterId === null || $filterId <= 0) {
    http_response_code(400);
    exit('Не выбран фильтр или категория');
}

try {
    db_exec(
        $mysqli,
        'INSERT INTO `tree_filter` (`tree_id`, `filter_id`) VALUES (?, ?)',
        array($treeId, $filterId)
    );
    echo 'Связь успешно добавлена';
} catch (mysqli_sql_exception $e) {
    // 1062 — дубликат по первичному ключу (tree_id, filter_id).
    if ((int) $e->getCode() === 1062) {
        echo 'Такая связь уже существует';
    } else {
        error_log('addRelationFilterTree: ' . $e->getMessage());
        http_response_code(500);
        echo 'Ошибка при добавлении связи';
    }
}
