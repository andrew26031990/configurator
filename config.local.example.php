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

    'recaptcha' => [
        'secret'  => 'CHANGE_ME',
        'sitekey' => 'CHANGE_ME',
    ],

    // Показывать PHP-ошибки в браузере. На проде всегда false.
    'debug' => false,
];
