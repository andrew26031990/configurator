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
