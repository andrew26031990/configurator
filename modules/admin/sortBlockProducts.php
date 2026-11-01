<?php
/**
 * Порядок товаров внутри блока.
 *
 * POST tree_id + одно из:
 *   items — JSON-массив пар [{"item_id": 12, "sort_order": 1}, ...]
 *           (все товары блока или только часть — остальные сохраняют номер);
 *   reset — сброс к сортировке по умолчанию: price | name | id.
 *
 * Номера валидируются (целые > 0), дубликаты/дыры допустимы — после
 * пересчёта блок нумеруется подряд 1..N. Ответ — актуальный список товаров.
 */

require_once __DIR__ . '/../../functions.php';
require_admin();
require_capability('products.edit');

$treeId = post_int('tree_id');
$reset  = post_str('reset', 10);

if ($treeId === null || $treeId <= 0 || !($block = getBlock($mysqli, $treeId))) {
    http_response_code(400);
    json_out(array('success' => false, 'error' => 'Блок не найден'));
    return;
}

if ($reset !== '') {
    if (!in_array($reset, array('price', 'name', 'id'), true)) {
        http_response_code(400);
        json_out(array('success' => false, 'error' => 'Неизвестный вариант сброса'));
        return;
    }
    $requested = null;
} else {
    $pairs = isset($_POST['items']) && is_string($_POST['items']) ? json_decode($_POST['items'], true) : null;

    list($requested, $errors) = block_sort_parse_pairs($pairs);

    if ($errors) {
        http_response_code(400);
        json_out(array('success' => false, 'error' => implode("\n", $errors)));
        return;
    }
}

try {
    $mysqli->begin_transaction();

    // FOR UPDATE: два одновременных сохранения одного блока выстраиваются
    // в очередь, а не перемешивают номера друг друга.
    db_rows($mysqli, 'SELECT prod_id FROM tree_prod WHERE tree_id = ? FOR UPDATE', array($treeId));

    if ($requested === null) {
        $ordered = getBlockProductIds($mysqli, $treeId, $reset);
    } else {
        $ordered = block_sort_resolve(getBlockProductIds($mysqli, $treeId), $requested);
    }

    saveBlockOrder($mysqli, $treeId, $ordered);
    $mysqli->commit();
} catch (Throwable $e) {
    $mysqli->rollback();
    error_log('sortBlockProducts: ' . $e->getMessage());
    http_response_code(500);
    json_out(array('success' => false, 'error' => 'Не удалось сохранить порядок'));
    return;
}

$resetLabels = array('price' => 'по цене', 'name' => 'по названию', 'id' => 'по ID');
audit_log(
    $mysqli,
    'block.sort',
    $reset === ''
        ? "Изменён порядок товаров в блоке «{$block['name']}»"
        : "Порядок товаров в блоке «{$block['name']}» сброшен {$resetLabels[$reset]}",
    array('ids' => $ordered, 'tree_id' => $treeId, 'details' => array('block' => $block['name'], 'reset' => $reset === '' ? null : $reset))
);

$products = getBlockProducts($mysqli, $treeId);
json_out(array('success' => true, 'count' => count($products), 'products' => $products));
