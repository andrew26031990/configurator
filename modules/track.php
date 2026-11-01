<?php
/**
 * Приём событий статистики из configurator/js/analytics.js (navigator.sendBeacon).
 * Публичный эндпоинт. Ответ всегда 204 — браузеру нечего с ним делать.
 *
 * POST: e — событие (analytics_client_events), cat — конфигуратор,
 *       d — подробность (блок, причина ошибки), new — первый визит посетителя,
 *       entry — первая страница сессии, ref — document.referrer (только для entry).
 */

require_once __DIR__ . '/../functions.php';

if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$event = post_str('e', 32);

if (in_array($event, analytics_client_events(), true)) {
    $detail = post_str('d', 100);
    $extra  = array('is_new' => post_int('new', 0) === 1);

    if ($event === 'page_view' && post_int('entry', 0) === 1) {
        // Первая страница сессии: запоминаем, откуда пришли.
        $detail            = 'entry';
        $extra['referrer'] = analytics_referrer(post_str('ref', 500), isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
    }

    analytics_record($mysqli, $event, post_str('cat', 64), $detail, $extra);
}

http_response_code(204);
