<?php
/**
 * Автозаполнение сокета и типа памяти по названиям товаров (плата / процессор / ОЗУ).
 *
 *   php bin/compat_autofill.php          — отчёт, ничего не пишет
 *   php bin/compat_autofill.php --apply  — заполнить пустые поля
 *
 * То же самое — кнопка «Заполнить из названий» в админке «Совместимость».
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../functions.php';

$apply  = in_array('--apply', $argv, true);
$result = compat_autofill($mysqli, $apply);

echo ($apply ? 'Заполнено' : 'Будет заполнено') . ': ' . count($result['filled']) . "\n";
foreach ($result['filled'] as $f) {
    $parts = array();
    foreach ($f['set'] as $k => $v) {
        $parts[] = "{$k}={$v}";
    }
    echo "  [{$f['role']}] {$f['name']} (id {$f['id']}): " . implode(', ', $parts) . "\n";
}

echo "\nНе распознано (заполнить вручную в админке «Совместимость»): " . count($result['unresolved']) . "\n";
foreach ($result['unresolved'] as $u) {
    echo "  [{$u['role']}] {$u['name']} (id {$u['id']}) — нет: {$u['missing']}; {$u['blocks']}\n";
}

if (!$apply) {
    echo "\nЭто был пробный прогон. Для записи: php bin/compat_autofill.php --apply\n";
}
