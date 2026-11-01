<?php
/**
 * Совместимость материнской платы, процессора и ОЗУ.
 *
 * У товара два поля: products.socket (LGA1700, AM5 …) и products.mem_type
 * (DDR4 / DDR5, для процессора и платы — список через запятую). Роль блока —
 * tree.compat_role: motherboard | cpu | ram.
 *
 * Здесь чистые функции: распознавание полей по названию товара (автозаполнение).
 * Базы не трогают — проверяются tests/compat_test.php.
 */

/**
 * Роли блоков для совместимости: код => подпись.
 *
 * @return array<string,string>
 */
function compat_roles()
{
    return array(
        'motherboard' => 'Материнская плата',
        'cpu'         => 'Процессор',
        'ram'         => 'ОЗУ',
    );
}

/**
 * Известные сокеты (порядок — для выпадающих списков).
 *
 * @return array<int,string>
 */
function compat_sockets()
{
    return array('LGA1851', 'LGA1700', 'LGA1200', 'LGA1151', 'AM5', 'AM4');
}

/**
 * @return array<int,string>
 */
function compat_mem_types()
{
    return array('DDR5', 'DDR4', 'DDR3');
}

/**
 * Сокет по чипсету материнской платы, когда сокета нет в названии
 * («ASUS PRIME B760M-K DDR5» → LGA1700).
 *
 * @return array<string,string>
 */
function compat_chipset_sockets()
{
    $map = array();
    $groups = array(
        'LGA1851' => array('H810', 'B860', 'Z890'),
        'LGA1700' => array('H610', 'B660', 'H670', 'Z690', 'B760', 'H770', 'Z790'),
        'LGA1200' => array('H410', 'B460', 'H470', 'Z490', 'H510', 'B560', 'H570', 'Z590'),
        'LGA1151' => array('H310', 'B360', 'B365', 'H370', 'Z370', 'Z390'),
        'AM5'     => array('A620', 'B650', 'X670', 'B840', 'B850', 'X870'),
        'AM4'     => array('A320', 'B350', 'X370', 'B450', 'X470', 'A520', 'B550', 'X570'),
    );
    foreach ($groups as $socket => $chipsets) {
        foreach ($chipsets as $c) {
            $map[$c] = $socket;
        }
    }
    return $map;
}

/**
 * Какую память поддерживает процессор на данном сокете.
 *
 * @return array<int,string>
 */
function compat_socket_memory($socket)
{
    $map = array(
        'LGA1851' => array('DDR5'),
        'LGA1700' => array('DDR4', 'DDR5'),
        'LGA1200' => array('DDR4'),
        'LGA1151' => array('DDR4'),
        'AM5'     => array('DDR5'),
        'AM4'     => array('DDR4'),
    );
    return isset($map[$socket]) ? $map[$socket] : array();
}

/**
 * Сокет из названия: «LGA1700», «LGA 1851», «LGA-1700», «AM5».
 *
 * @return string '' если не нашли
 */
function compat_parse_socket($name)
{
    if (preg_match('/LGA\s*-?\s*(\d{4})/i', $name, $m)) {
        return 'LGA' . $m[1];
    }
    if (preg_match('/(?<![A-Z0-9])AM([45])(?![0-9])/i', $name, $m)) {
        return 'AM' . $m[1];
    }
    return '';
}

/**
 * Тип памяти из названия: «DDR5», «DDR 4», «D4» (ASUS … D4).
 *
 * @return string '' если не нашли
 */
function compat_parse_memory($name)
{
    if (preg_match('/DDR\s*-?\s*([345])(?![0-9])/i', $name, $m)) {
        return 'DDR' . $m[1];
    }
    if (preg_match('/(?<![A-Z0-9])D([45])(?![A-Z0-9])/i', $name, $m)) {
        return 'DDR' . $m[1];
    }
    return '';
}

/**
 * Сокет процессора по модели, когда его нет в названии.
 *   Intel Core i3/i5/i7/i9 10–11xxx → LGA1200, 12–14xxx → LGA1700, Core Ultra 2xx → LGA1851;
 *   Ryzen 1000–5000 → AM4, 7000–9000 → AM5.
 *
 * @return string
 */
function compat_cpu_socket_by_model($name)
{
    if (preg_match('/Ultra\s*[3579]\s*-?\s*2\d\d/i', $name)) {
        return 'LGA1851';
    }
    if (preg_match('/\bi[3579]\s*-?\s*(1[0-4])\d{3}/i', $name, $m)) {
        return (int) $m[1] >= 12 ? 'LGA1700' : 'LGA1200';
    }
    if (preg_match('/Ryzen.*?\b([1-9])\d{3}/iu', $name, $m)) {
        return (int) $m[1] >= 7 ? 'AM5' : 'AM4';
    }
    return '';
}

/**
 * Поля совместимости, которые удалось распознать по названию товара.
 *
 * @param  string $role motherboard | cpu | ram
 * @return array{socket: string, mem_type: string}
 */
function compat_guess($name, $role)
{
    $name   = (string) $name;
    $socket = '';
    $mem    = '';

    if ($role === 'motherboard' || $role === 'cpu') {
        $socket = compat_parse_socket($name);
    }

    if ($role === 'motherboard') {
        if ($socket === '' && preg_match_all('/(?<![A-Z0-9])([ABHXZ]\d{3})/i', $name, $mm)) {
            $chipsets = compat_chipset_sockets();
            foreach ($mm[1] as $c) {
                $c = strtoupper($c);
                if (isset($chipsets[$c])) {
                    $socket = $chipsets[$c];
                    break;
                }
            }
        }
        $mem = compat_parse_memory($name);
        // На AM4/AM5 и LGA1851 тип памяти однозначен; на LGA1700 бывают обе — без названия не угадать.
        if ($mem === '' && in_array($socket, array('AM4', 'AM5', 'LGA1851'), true)) {
            $mem = implode(',', compat_socket_memory($socket));
        }
    } elseif ($role === 'cpu') {
        if ($socket === '') {
            $socket = compat_cpu_socket_by_model($name);
        }
        $mem = implode(',', compat_socket_memory($socket));
    } elseif ($role === 'ram') {
        $mem = compat_parse_memory($name);
    }

    return array('socket' => $socket, 'mem_type' => $mem);
}

/**
 * Нормализует тип памяти из формы: «ddr4, DDR5» → «DDR4,DDR5». Неизвестное отбрасывается.
 *
 * @param  string|array<int,string> $value
 * @return string
 */
function compat_normalize_mem($value)
{
    $parts = is_array($value) ? $value : explode(',', (string) $value);
    $out   = array();
    foreach ($parts as $p) {
        $p = strtoupper(trim((string) $p));
        if (in_array($p, compat_mem_types(), true)) {
            $out[$p] = $p;
        }
    }
    // Порядок по возрастанию — чтобы «DDR5,DDR4» и «DDR4,DDR5» не считались разными.
    $sorted = array();
    foreach (array('DDR3', 'DDR4', 'DDR5') as $t) {
        if (isset($out[$t])) {
            $sorted[] = $t;
        }
    }
    return implode(',', $sorted);
}
