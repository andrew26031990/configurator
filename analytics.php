<?php
/**
 * Статистика посещаемости и воронка заказа: чистые функции (без базы).
 * Проверяются tests/analytics_test.php. Запросы к базе — в functions.php.
 */

/**
 * События, которые принимает modules/track.php (из браузера).
 * order_success и order_error пишет сам modules/mail.php.
 *
 * @return array<string,string> код => подпись
 */
function analytics_events()
{
    return array(
        'page_view'        => 'Просмотр страницы',
        'component_select' => 'Выбор комплектующего',
        'build_ready'      => 'Собрана основа (плата, процессор, ОЗУ)',
        'order_open'       => 'Открыта форма заказа',
        'order_submit'     => 'Нажата «Отправить»',
        'order_error'      => 'Ошибка формы заказа',
        'order_success'    => 'Заявка принята',
        'share_link'       => 'Скопирована ссылка на сборку',
        'pdf_download'     => 'Скачан PDF',
    );
}

/**
 * События, которые браузер может прислать сам. order_success — только сервер,
 * иначе воронку можно было бы «накрутить» запросами.
 *
 * @return array<int,string>
 */
function analytics_client_events()
{
    return array('page_view', 'component_select', 'build_ready', 'order_open', 'order_submit', 'order_error', 'share_link', 'pdf_download');
}

/**
 * Шаги воронки: номер => [подпись, пояснение].
 *
 * @return array<int,array{0:string,1:string}>
 */
function analytics_funnel_steps()
{
    return array(
        1 => array('Зашли на сайт', 'любая страница конфигуратора'),
        2 => array('Открыли конфигуратор', 'страница сборки (Intel, AMD …), не главная и не «Готовые сборки»'),
        3 => array('Выбрали комплектующее', 'хотя бы один клик по товару'),
        4 => array('Собрали основу', 'выбраны материнская плата, процессор и ОЗУ'),
        5 => array('Открыли форму «Купить»', ''),
        6 => array('Нажали «Отправить»', 'прошли проверку полей в браузере'),
        7 => array('Заявка принята', 'сервер принял и отправил менеджеру'),
    );
}

/**
 * Номер шага воронки для события.
 *
 * @return int 0 — событие не шаг воронки
 */
function analytics_event_step($event, $cat)
{
    switch ($event) {
        case 'page_view':
            return ($cat !== '' && $cat !== 'kompyutery') ? 2 : 1;
        case 'component_select':
            return 3;
        case 'build_ready':
            return 4;
        case 'order_open':
            return 5;
        case 'order_submit':
            // Отправляется браузером только после проверки полей в браузере.
            return 6;
        case 'order_success':
            return 7;
    }
    return 0;
}

/**
 * Воронка по «максимальному шагу» каждой сессии.
 *
 * Сессия считается дошедшей до шага k, если её максимальный шаг ≥ k: заявка по
 * ссылке «Поделиться» без кликов по товарам всё равно проходит все шаги.
 *
 * @param  array<int,int> $sessionsByMaxStep шаг => число сессий с таким максимальным шагом
 * @return array<int,array{step:int,label:string,hint:string,sessions:int,from_prev:float|null,from_first:float|null,lost:int}>
 */
function analytics_funnel(array $sessionsByMaxStep)
{
    $steps = analytics_funnel_steps();
    $out   = array();
    $first = null;
    $prev  = null;

    foreach ($steps as $k => $s) {
        $reached = 0;
        foreach ($sessionsByMaxStep as $max => $n) {
            if ((int) $max >= $k) {
                $reached += (int) $n;
            }
        }

        if ($first === null) {
            $first = $reached;
        }

        $out[] = array(
            'step'       => $k,
            'label'      => $s[0],
            'hint'       => $s[1],
            'sessions'   => $reached,
            'from_prev'  => $prev === null ? null : ($prev > 0 ? round($reached * 100 / $prev, 1) : 0.0),
            'from_first' => $first > 0 ? round($reached * 100 / $first, 1) : 0.0,
            'lost'       => $prev === null ? 0 : max(0, $prev - $reached),
        );
        $prev = $reached;
    }

    return $out;
}

/**
 * Идентификатор посетителя/сессии: 16 символов [a-z0-9].
 *
 * @return bool
 */
function analytics_id_ok($id)
{
    return is_string($id) && preg_match('/^[a-z0-9]{16}$/', $id) === 1;
}

/**
 * Значение cat/detail: латиница, цифры, _ . -
 *
 * @return string '' если не подходит
 */
function analytics_clean_token($value, $max)
{
    $value = (string) $value;
    return preg_match('/^[A-Za-z0-9_.\-]{0,' . (int) $max . '}$/', $value) ? $value : '';
}

/**
 * mobile | desktop по User-Agent.
 *
 * @return string
 */
function analytics_device($ua)
{
    return preg_match('/Mobi|Android|iPhone|iPad|iPod|Opera Mini|IEMobile/i', (string) $ua) ? 'mobile' : 'desktop';
}

/**
 * Робот/сканер по User-Agent. Учёт работает через JS, так что большинство
 * ботов и так не доходит, но безголовые браузеры и краулеры с JS отсекаем.
 *
 * @return bool
 */
function analytics_is_bot($ua)
{
    $ua = (string) $ua;
    if ($ua === '') {
        return true;
    }
    return preg_match('/bot|crawl|spider|slurp|headless|phantom|puppeteer|playwright|selenium|lighthouse|pagespeed|preview|curl|wget|python|httpclient|java\//i', $ua) === 1;
}

/**
 * Источник перехода: хост реферера, сгруппированный для отчёта.
 *
 * @return string '' — прямой заход или переход внутри сайта
 */
function analytics_referrer($referrer, $ownHost)
{
    $host = strtolower((string) parse_url((string) $referrer, PHP_URL_HOST));
    $own  = strtolower(preg_replace('/:\d+$/', '', (string) $ownHost));

    if ($host === '' || $host === $own) {
        return '';
    }
    $host = preg_replace('/^(www\.|m\.|l\.|lm\.)/', '', $host);

    $groups = array(
        'google'    => '/(^|\.)google\./',
        'yandex'    => '/(^|\.)(yandex|ya)\./',
        'instagram' => '/(^|\.)instagram\.com$/',
        'facebook'  => '/(^|\.)facebook\.com$/',
        'telegram'  => '/(^|\.)(t\.me|telegram\.org|web\.telegram\.org)$/',
        'youtube'   => '/(^|\.)(youtube\.com|youtu\.be)$/',
        'pcmarket.uz' => '/(^|\.)pcmarket\.uz$/',
    );
    foreach ($groups as $name => $re) {
        if (preg_match($re, $host)) {
            return $name;
        }
    }

    return substr($host, 0, 64);
}
