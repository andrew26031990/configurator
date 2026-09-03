<?php
/**
 * Подгрузка списка комплектующих при клике по фильтру в конфигураторе.
 * Публичный эндпоинт.
 */

require_once __DIR__ . '/../functions.php';

$treeId   = post_int('dataId');
$filterId = post_str('filterId', 10);

if ($treeId === null || $treeId <= 0) {
    json_out(array());
    return;
}

$sql = 'SELECT prod.id, prod.name, prod.price, prod.image, prod.description
          FROM `products` AS prod
          JOIN `tree_prod` AS tp ON tp.prod_id = prod.id
         WHERE tp.tree_id = ?';

$params = array($treeId);

// '0000' — псевдофильтр «Все».
if ($filterId !== '' && $filterId !== '0000') {
    $sql .= ' AND prod.f_id = ?';
    $params[] = (int) $filterId;
}

$sql .= ' ORDER BY prod.price';

json_out(db_rows($mysqli, $sql, $params));
