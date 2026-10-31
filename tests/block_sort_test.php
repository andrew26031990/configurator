<?php
/**
 * Проверка пересчёта порядка товаров в блоке. Базы не требует.
 * Запуск: php tests/block_sort_test.php
 */

require_once __DIR__ . '/../block_sort.php';

ini_set('display_errors', '1');
error_reporting(E_ALL);

$pass = 0;
$fail = 0;

function check($label, $actual, $expected)
{
    global $pass, $fail;

    if ($actual === $expected) {
        $pass++;
        return;
    }

    $fail++;
    echo "FAIL: $label\n";
    echo "      ожидалось: " . var_export($expected, true) . "\n";
    echo "      получено:  " . var_export($actual, true) . "\n";
}

/* --- block_sort_parse_pairs ------------------------------------------ */

list($map, $errors) = block_sort_parse_pairs(array(
    array('item_id' => '10', 'sort_order' => '2'),
    array('item_id' => 11, 'sort_order' => 1),
));
check('корректные пары', $map, array(10 => 2, 11 => 1));
check('корректные пары: без ошибок', $errors, array());

list($map, $errors) = block_sort_parse_pairs('не массив');
check('не массив: ошибка', count($errors), 1);

list($map, $errors) = block_sort_parse_pairs(array(
    array('item_id' => 1, 'sort_order' => 0),
    array('item_id' => 2, 'sort_order' => -3),
    array('item_id' => 3, 'sort_order' => '1.5'),
    array('item_id' => 4, 'sort_order' => 'abc'),
    array('item_id' => 'x', 'sort_order' => 1),
    array('sort_order' => 1),
    array('item_id' => 5, 'sort_order' => 1),
    array('item_id' => 5, 'sort_order' => 2),
));
check('мусор отбрасывается', $map, array(5 => 1));
check('мусор: по ошибке на каждую плохую пару', count($errors), 7);

/* --- block_sort_resolve ---------------------------------------------- */

$cur = array(10, 20, 30, 40, 50);

check('без изменений', block_sort_resolve($cur, array()), $cur);

check(
    'полный новый порядок (drag-and-drop)',
    block_sort_resolve($cur, array(10 => 5, 20 => 4, 30 => 3, 40 => 2, 50 => 1)),
    array(50, 40, 30, 20, 10)
);

check(
    'одному товару задали номер 1 — он встаёт первым, остальные сдвигаются',
    block_sort_resolve($cur, array(40 => 1)),
    array(40, 10, 20, 30, 50)
);

check(
    'перенос вниз на занятый номер — подвинутый товар встаёт на этот номер',
    block_sort_resolve($cur, array(10 => 3)),
    array(20, 10, 30, 40, 50)
);

check(
    'номер больше длины списка — в конец',
    block_sort_resolve($cur, array(20 => 999)),
    array(10, 30, 40, 50, 20)
);

check(
    'дубликаты среди «двинутых» — сохраняют прежний взаимный порядок',
    block_sort_resolve($cur, array(50 => 1, 40 => 1)),
    array(40, 50, 10, 20, 30)
);

check(
    'чужие id в запросе игнорируются',
    block_sort_resolve($cur, array(999 => 1)),
    $cur
);

check('пустой блок', block_sort_resolve(array(), array(1 => 1)), array());

echo "\nПройдено: $pass, провалено: $fail\n";
exit($fail > 0 ? 1 : 0);
