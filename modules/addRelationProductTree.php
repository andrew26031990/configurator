<?php
require_once __DIR__ . '/../functions.php';
require_admin();

$treeId = post_int('tree_id');
$prodId = post_int('prod_id');

if ($treeId === null || $treeId <= 0 || $prodId === null || $prodId <= 0) {
    http_response_code(400);
    exit('Не выбран товар или категория');
}

try {
    db_exec(
        $mysqli,
        'INSERT INTO `tree_prod` (`tree_id`, `prod_id`) VALUES (?, ?)',
        array($treeId, $prodId)
    );
    echo 'Связь успешно добавлена';
} catch (mysqli_sql_exception $e) {
    if ((int) $e->getCode() === 1062) {
        echo 'Такая связь уже существует';
    } else {
        error_log('addRelationProductTree: ' . $e->getMessage());
        http_response_code(500);
        echo 'Ошибка при добавлении связи';
    }
}
