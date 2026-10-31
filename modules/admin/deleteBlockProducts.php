<?php
/**
 * Массовое удаление товаров блока.
 *
 * POST:
 *   tree_id       — блок (тип-нода level 3);
 *   mode          — selected | all;
 *   item_ids      — JSON-массив id (для mode=selected);
 *   confirm       — для mode=all фраза «удалить все»;
 *   unlink_shared — 1: товары, которые лежат и в других блоках, убрать только
 *                   из этого блока. 0 (по умолчанию): такие товары не трогаем
 *                   и возвращаем в skipped с указанием, где они используются.
 *
 * Всё в одной транзакции: либо применилось целиком, либо ничего.
 * Ответ: deleted / unlinked — id, skipped — [{id, name, reason}], products —
 * актуальный список блока.
 */

require_once __DIR__ . '/../../functions.php';
require_admin();
require_capability('products.delete');

$uploadDir = APP_ROOT . '/configurator/images/products';

$treeId       = post_int('tree_id');
$mode         = post_str('mode', 10);
$unlinkShared = post_int('unlink_shared', 0) === 1;

if ($treeId === null || $treeId <= 0 || !($block = getBlock($mysqli, $treeId))) {
    http_response_code(400);
    json_out(array('success' => false, 'error' => 'Блок не найден'));
    return;
}

if ($mode === 'all') {
    if (mb_strtolower(post_str('confirm', 50), 'UTF-8') !== 'удалить все') {
        http_response_code(400);
        json_out(array('success' => false, 'error' => 'Подтверждение не совпадает. Удаление отменено.'));
        return;
    }
    $requested = null;
} elseif ($mode === 'selected') {
    $raw       = isset($_POST['item_ids']) && is_string($_POST['item_ids']) ? json_decode($_POST['item_ids'], true) : null;
    $requested = array();
    foreach (is_array($raw) ? $raw : array() as $v) {
        $id = filter_var($v, FILTER_VALIDATE_INT);
        if ($id !== false && $id > 0) {
            $requested[$id] = $id;
        }
    }
    if (!$requested) {
        http_response_code(400);
        json_out(array('success' => false, 'error' => 'Не выбрано ни одного товара'));
        return;
    }
} else {
    http_response_code(400);
    json_out(array('success' => false, 'error' => 'Неизвестный режим удаления'));
    return;
}

/**
 * "?, ?, ?" под IN (...).
 */
function in_placeholders(array $ids)
{
    return implode(', ', array_fill(0, count($ids), '?'));
}

$deleted  = array();
$unlinked = array();
$skipped  = array();
$images   = array();

try {
    $mysqli->begin_transaction();

    $blockIds = array();
    foreach (db_rows($mysqli, 'SELECT prod_id FROM tree_prod WHERE tree_id = ? FOR UPDATE', array($treeId)) as $r) {
        $blockIds[(int) $r['prod_id']] = true;
    }

    $targets = array();
    if ($requested === null) {
        $targets = array_keys($blockIds);
    } else {
        foreach ($requested as $id) {
            if (isset($blockIds[$id])) {
                $targets[] = $id;
            } else {
                $skipped[] = array('id' => $id, 'name' => '', 'reason' => 'Товара нет в этом блоке');
            }
        }
    }

    foreach (array_chunk($targets, 500) as $chunk) {
        $in = in_placeholders($chunk);

        $products = db_rows_by_id($mysqli, "SELECT id, name, image FROM products WHERE id IN ($in)", $chunk);

        // Где ещё лежат товары: «Сборка → Блок».
        $usedIn = array();
        foreach (db_rows(
            $mysqli,
            "SELECT tp.prod_id,
                    CONCAT(COALESCE((SELECT b.name FROM tree b WHERE b.link = t.group_id LIMIT 1), '?'), ' → ', t.name) AS place
               FROM tree_prod tp
               JOIN tree t ON t.id = tp.tree_id
              WHERE tp.tree_id <> ? AND tp.prod_id IN ($in)",
            array_merge(array($treeId), $chunk)
        ) as $r) {
            $usedIn[(int) $r['prod_id']][] = $r['place'];
        }

        $toDelete = array();
        $toUnlink = array();

        foreach ($chunk as $id) {
            if (!isset($usedIn[$id])) {
                $toDelete[] = $id;
                continue;
            }
            if ($unlinkShared) {
                $toUnlink[] = $id;
                continue;
            }
            $skipped[] = array(
                'id'     => $id,
                'name'   => isset($products[$id]) ? $products[$id]['name'] : '',
                'reason' => 'Используется в других блоках: ' . implode(', ', $usedIn[$id]),
            );
        }

        if ($toUnlink) {
            db_exec(
                $mysqli,
                'DELETE FROM tree_prod WHERE tree_id = ? AND prod_id IN (' . in_placeholders($toUnlink) . ')',
                array_merge(array($treeId), $toUnlink)
            );
            $unlinked = array_merge($unlinked, $toUnlink);
        }

        if ($toDelete) {
            $in = in_placeholders($toDelete);
            db_exec($mysqli, "DELETE FROM tree_prod WHERE prod_id IN ($in)", $toDelete);
            db_exec($mysqli, "DELETE FROM products  WHERE id IN ($in)", $toDelete);

            foreach ($toDelete as $id) {
                if (isset($products[$id]) && $products[$id]['image'] !== '') {
                    $images[] = $products[$id]['image'];
                }
            }
            $deleted = array_merge($deleted, $toDelete);
        }
    }

    // Убираем дыры в нумерации оставшихся товаров блока.
    if ($deleted || $unlinked) {
        saveBlockOrder($mysqli, $treeId, getBlockProductIds($mysqli, $treeId));
    }

    $mysqli->commit();
} catch (Throwable $e) {
    $mysqli->rollback();
    error_log('deleteBlockProducts: ' . $e->getMessage());
    http_response_code(500);
    json_out(array('success' => false, 'error' => 'Ошибка при удалении. Ни один товар не удалён.'));
    return;
}

// Файлы — только после успешного коммита: при откате картинки должны остаться.
foreach ($images as $image) {
    delete_upload($uploadDir, $image);
}

audit_log(
    $mysqli,
    'block.bulk_delete',
    "Блок «{$block['name']}»: удалено " . count($deleted) . ', убрано из блока ' . count($unlinked)
        . ', не удалено ' . count($skipped) . ($mode === 'all' ? ' (удаление всех)' : ''),
    array(
        'ids'     => array_merge($deleted, $unlinked),
        'tree_id' => $treeId,
        'details' => array(
            'block'    => $block['name'],
            'mode'     => $mode,
            'deleted'  => $deleted,
            'unlinked' => $unlinked,
            'skipped'  => $skipped,
        ),
    )
);

$products = getBlockProducts($mysqli, $treeId);

json_out(array(
    'success'  => true,
    'deleted'  => $deleted,
    'unlinked' => $unlinked,
    'skipped'  => $skipped,
    'count'    => count($products),
    'products' => $products,
));
