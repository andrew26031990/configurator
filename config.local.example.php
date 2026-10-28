<?php
/**
 * Шаблон локального конфига. Скопировать в config.local.php и заполнить.
 * config.local.php НЕ коммитится (см. .gitignore) и закрыт от веба (см. .htaccess).
 */

return [
    'db' => [
        'host'    => 'localhost',
        'user'    => 'CHANGE_ME',
        'pass'    => 'CHANGE_ME',
        'name'    => 'configurator',
        // Оставлено 'utf8' (utf8mb3), как в текущей базе. Менять вместе с колонками.
        'charset' => 'utf8',
    ],

    'telegram' => [
        'token'   => 'CHANGE_ME',
        'chat_id' => 'CHANGE_ME',
    ],

    // Письмо с заявкой. Дублирует уведомление в Telegram, поэтому при
    // enabled = false заявки продолжают приходить в бота.
    'mail' => [
        'enabled'    => false,
        'host'       => 'smtp.beget.com',
        'port'       => 465,
        'encryption' => 'ssl',      // 'ssl' (465), 'tls' (587) или '' без шифрования
        'username'   => 'CHANGE_ME',
        'password'   => 'CHANGE_ME',
        'from'       => 'CHANGE_ME',        // пусто — берётся username
        'from_name'  => 'Конфигуратор pcmarket.uz',
        'to'         => 'CHANGE_ME',        // куда падают заявки менеджеру
        'subject'    => 'Конфигурация',
        'debug'      => false,      // подробный лог SMTP в error_log
    ],

    'recaptcha' => [
        // false — форма входа показывается без капчи, проверка не делается.
        // Защитой от перебора в этом режиме остаётся только ограничение
        // попыток по IP (см. login_* в security.php), поэтому выключать
        // стоит временно.
        'enabled' => true,
        'secret'  => 'CHANGE_ME',
        'sitekey' => 'CHANGE_ME',
    ],

    // Ограничение попыток входа. Действует всегда, но особенно важно,
    // когда recaptcha.enabled = false.
    'login' => [
        'max_attempts'  => 5,   // неудачных попыток с одного IP
        'block_seconds' => 900, // на сколько блокировать после исчерпания
    ],

    // Показывать PHP-ошибки в браузере. На проде всегда false.
    'debug' => false,
];


/**
 * ЛОКАЛЬНЫЙ конфиг для разработки (MariaDB на 3307, тестовые заглушки).
 * НЕ для прода. Боевой конфиг: C:\Users\Andrew\tools\config.local.prod.php.bak
 * Файл в .gitignore, на сервер не уезжает.
 */

//return [
//    'db' => [
//        'host' => 'localhost',
//        'user' => 'wp_user',
//        'pass' => 'uK2wi9eiEeghili9',
//        'name' => 'configurator',
//        'charset' => 'utf8',
//    ],
//
//    'telegram' => [
//        'token' => '1675277208:AAHjq6IDErlrINsBh0VvHS_o6qCI974ytFg',
//        'photo_token' => '6232662696:AAFz93P-ZB6d1C1XxmvAwd8Am2XISR4eQfo',
//        'chat_id' => '-1001385649462',
//    ],
//
//    'mail' => [
//        'enabled' => true,
//        'host' => 'smtp.beget.com',
//        'port' => 465,
//        'encryption' => 'ssl',
//        'username' => 'sale@pcmarket.uz',
//        'password' => '7UR%QHGy',
//        'from' => 'sale@pcmarket.uz',
//        'from_name' => 'Конфигуратор pcmarket.uz',
//        'to' => 'sale@pcmarket.uz',
//        'subject' => 'Конфигурация',
//        'debug' => false,
//    ],
//
//    'recaptcha' => [
//        // Временно выключена. Вернуть true, когда будут актуальные ключи.
//        'enabled' => true,
//        'secret' => '6LfLXM0UAAAAAIoB2wLKujeWKwT_2dW-GVPJHqfe',
//        'sitekey' => '6LfLXM0UAAAAAG34WmIzuvGAHeAgLz33y24i-6bq',
//    ],
//
//    // Пока капча выключена — это единственная защита формы входа.
//    'login' => [
//        'max_attempts' => 5,
//        'block_seconds' => 900,
//    ],
//
//    'debug' => false,
//];


