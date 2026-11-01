<?php
/**
 * Порядок товаров внутри блока: разбор входных данных и пересчёт номеров.
 *
 * Чистые функции, базы не трогают — проверяются tests/block_sort_test.php.
 */

/**
 * Разбирает массив пар {item_id, sort_order} из запроса.
 *
 * Возвращает array($map, $errors): $map — item_id => sort_order,
 * $errors — список текстов ошибок (пустой, если всё корректно).
 *
 * @param  mixed $pairs
 * @return array{0: array<int,int>, 1: array<int,string>}
 */
function block_sort_parse_pairs($pairs)
{
    $map    = array();
    $errors = array();

    if (!is_array($pairs)) {
        return array($map, array('Ожидался массив пар {item_id, sort_order}'));
    }

    foreach ($pairs as $i => $pair) {
        $n = (int) $i + 1;

        if (!is_array($pair) || !isset($pair['item_id'], $pair['sort_order'])) {
            $errors[] = "Пара №{$n}: нужны поля item_id и sort_order";
            continue;
        }

        $id    = filter_var($pair['item_id'], FILTER_VALIDATE_INT);
        $order = filter_var($pair['sort_order'], FILTER_VALIDATE_INT);

        if ($id === false || $id <= 0) {
            $errors[] = "Пара №{$n}: некорректный item_id";
            continue;
        }
        if ($order === false || $order <= 0) {
            $errors[] = "Товар {$id}: порядок должен быть целым положительным числом";
            continue;
        }
        if (isset($map[$id])) {
            $errors[] = "Товар {$id} указан дважды";
            continue;
        }

        $map[$id] = $order;
    }

    return array($map, $errors);
}

/**
 * Новый порядок товаров блока.
 *
 * $currentIds — id товаров блока в текущем порядке (позиция i ↔ номер i+1).
 * $requested  — item_id => желаемый номер; товары, которых нет в $requested,
 *               сохраняют свой текущий номер.
 *
 * Дубликаты и дыры допустимы: список сортируется по желаемому номеру, при
 * равенстве вперёд идёт товар, который двигали (ему явно задали другой номер),
 * затем — прежний порядок. Результат потом нумеруется подряд 1..N.
 *
 * @param  array<int,int> $currentIds
 * @param  array<int,int> $requested
 * @return array<int,int> id товаров в новом порядке
 */
function block_sort_resolve(array $currentIds, array $requested)
{
    $rows = array();

    foreach (array_values($currentIds) as $pos => $id) {
        $id    = (int) $id;
        $was   = $pos + 1;
        $want  = isset($requested[$id]) ? (int) $requested[$id] : $was;
        $rows[] = array($want, $want !== $was ? 0 : 1, $pos, $id);
    }

    usort($rows, function ($a, $b) {
        for ($k = 0; $k < 3; $k++) {
            if ($a[$k] !== $b[$k]) {
                return $a[$k] < $b[$k] ? -1 : 1;
            }
        }
        return 0;
    });

    $out = array();
    foreach ($rows as $r) {
        $out[] = $r[3];
    }

    return $out;
}
