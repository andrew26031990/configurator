<?php
/**
 * Распознавание сокета и типа памяти по названию товара. Базы не требует.
 * Запуск: php tests/compat_test.php
 */

require_once __DIR__ . '/../compat.php';

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

function g($name, $role)
{
    $r = compat_guess($name, $role);
    return $r['socket'] . '|' . $r['mem_type'];
}

/* --- Материнские платы (реальные названия из каталога) --------------- */

check('MSI H610 DDR4 LGA1700', g('MSI PRO H610M-S DDR4 LGA1700', 'motherboard'), 'LGA1700|DDR4');
check('Gigabyte DDR5 LGA1700', g('Gigabyte H610M K V2 DDR5 LGA1700', 'motherboard'), 'LGA1700|DDR5');
check('ASUS D4 без сокета — по чипсету', g('ASUS PRIME H610M-F D4 DDR4', 'motherboard'), 'LGA1700|DDR4');
check('ASUS B760M-K DDR5', g('ASUS PRIME B760M-K DDR5', 'motherboard'), 'LGA1700|DDR5');
check('ASUS Z790-P DDR5', g('ASUS PRIME Z790-P DDR5', 'motherboard'), 'LGA1700|DDR5');
check('MSI B860 LGA1851 без DDR — DDR5 по сокету', g('MSI PRO B860M-A WIFI LGA1851', 'motherboard'), 'LGA1851|DDR5');
check('AMD AM5 B650', g(' Gigabyte AMD AM5 B650M GAMING PLUS WF DDR5 ', 'motherboard'), 'AM5|DDR5');
check('AM5 без DDR — DDR5 по сокету', g('ASUS TUF GAMING B650-PLUS WIFI', 'motherboard'), 'AM5|DDR5');
check('AM4 по чипсету B550', g('MSI MAG B550 TOMAHAWK', 'motherboard'), 'AM4|DDR4');
check('LGA1700 без DDR — тип памяти не угадываем', g('ASUS PRIME B760M-A WIFI', 'motherboard'), 'LGA1700|');
check('ничего не распознано', g('Какая-то плата', 'motherboard'), '|');
check('«LGA-1700» через дефис', g('Board LGA-1700 DDR4', 'motherboard'), 'LGA1700|DDR4');

/* --- Процессоры ------------------------------------------------------- */

check('i3-12100F LGA1700', g('Intel-Core i3 - 12100F, 3.3 GHz, 12MB, LGA1700', 'cpu'), 'LGA1700|DDR4,DDR5');
check('Ultra 5-225F LGA 1851', g('Intel-Core Ultra 5-225F, 2.7 GHz, 24MB, LGA 1851', 'cpu'), 'LGA1851|DDR5');
check('Ryzen 5600X AM4', g('AMD Ryzen™ 5 Vermeer 5600X - 3.7 GHz, 6 cores/12 threads, No GPU, AM4', 'cpu'), 'AM4|DDR4');
check('Ryzen 7500F AM5', g('AMD Ryzen™ 5 Raphael 7500F - 3.7 GHz, 6 cores/12 threads, No GPU, AM5', 'cpu'), 'AM5|DDR5');
check('Intel без сокета — по модели', g('Intel Core i5-14400F BOX', 'cpu'), 'LGA1700|DDR4,DDR5');
check('Intel 10-го поколения — LGA1200', g('Intel Core i5-10400F', 'cpu'), 'LGA1200|DDR4');
check('Ultra без сокета — по модели', g('Intel Core Ultra 7 265K', 'cpu'), 'LGA1851|DDR5');
check('Ryzen без сокета — по серии', g('AMD Ryzen 7 9700X', 'cpu'), 'AM5|DDR5');
check('мусор', g('TEST2', 'cpu'), '|');

/* --- ОЗУ -------------------------------------------------------------- */

check('Hiksemi DDR4', g('Hiksema DDR4 8GB 3200Mhz', 'ram'), '|DDR4');
check('DDR5', g('Kingston Fury Beast DDR5 32GB 6000MHz', 'ram'), '|DDR5');
check('SSD в блоке ОЗУ', g('SSD M2 HIKSEMI 512GB  NVMe', 'ram'), '|');
check('сокет у ОЗУ не ищем', g('DDR4 AM4 edition', 'ram'), '|DDR4');

/* --- Нормализация ----------------------------------------------------- */

check('нормализация', compat_normalize_mem(' ddr5, DDR4 ,xx'), 'DDR4,DDR5');
check('нормализация массива', compat_normalize_mem(array('DDR4', 'DDR4')), 'DDR4');
check('пусто', compat_normalize_mem(''), '');

echo "\nПройдено: $pass, провалено: $fail\n";
exit($fail > 0 ? 1 : 0);
