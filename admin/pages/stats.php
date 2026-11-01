<?php
// Статистика посещаемости и воронка заказа. Гейт stats.view — в admin.php.

$isDate = function ($v) { return is_string($v) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) && strtotime($v) !== false; };

$configurators = db_rows($mysqli, "SELECT name, link FROM tree WHERE parent_id = 1 AND link <> '' ORDER BY sort, id");
$catNames      = array('' => 'Главная');
foreach ($configurators as $c) {
    $catNames[$c['link']] = $c['name'];
}

$filter = array(
    'from'   => isset($_GET['from']) && $isDate($_GET['from']) ? $_GET['from'] : date('Y-m-d', strtotime('-29 days')),
    'to'     => isset($_GET['to']) && $isDate($_GET['to']) ? $_GET['to'] : date('Y-m-d'),
    'cat'    => isset($_GET['cat']) && isset($catNames[$_GET['cat']]) && $_GET['cat'] !== '' ? (string) $_GET['cat'] : '',
    'device' => isset($_GET['device']) && in_array($_GET['device'], array('mobile', 'desktop'), true) ? $_GET['device'] : '',
);
if ($filter['from'] > $filter['to']) {
    list($filter['from'], $filter['to']) = array($filter['to'], $filter['from']);
}
// Не больше года на графике по дням.
if (strtotime($filter['to']) - strtotime($filter['from']) > 366 * 86400) {
    $filter['from'] = date('Y-m-d', strtotime('-366 days', strtotime($filter['to'])));
}

$totals   = getAnalyticsTotals($mysqli, $filter);
$daily    = getAnalyticsDaily($mysqli, $filter);
$monthly  = getAnalyticsMonthly($mysqli, $filter);
$funnelRs = getAnalyticsFunnel($mysqli, $filter);
$funnel   = $funnelRs['funnel'];
$abandon  = array_slice($funnelRs['abandon'], 0, 10, true);
$errors   = getAnalyticsBreakdown($mysqli, $filter, 'order_error', 'detail');
$sources  = getAnalyticsBreakdown($mysqli, $filter, 'page_view', 'referrer', 'entry');
$devices  = getAnalyticsBreakdown($mysqli, $filter, 'page_view', 'device');
$byCat    = getAnalyticsBreakdown($mysqli, $filter, 'page_view', 'cat');
$shares   = getAnalyticsBreakdown($mysqli, $filter, 'share_link', 'device');
$pdfs     = getAnalyticsBreakdown($mysqli, $filter, 'pdf_download', 'device');
$blocks   = getBlockNamesByTranslit($mysqli);

// С фильтром по конфигуратору шаг «Зашли на сайт» совпадает со вторым — не показываем.
if ($filter['cat'] !== '') {
    $funnel = array_values(array_filter($funnel, function ($s) { return $s['step'] > 1; }));
}

$errorLabels = array(
    'client_fields'     => 'Не заполнены поля (проверка в браузере)',
    'client_components' => 'Мало комплектующих для заказа (в браузере)',
    'name'              => 'Некорректное имя',
    'phone'             => 'Некорректный телефон',
    'email'             => 'Некорректный e-mail',
    'link'              => 'Не прочиталась ссылка на сборку',
    'items'             => 'В сборке нет товаров',
    'rate'              => 'Лимит заявок с одного IP',
    'expired'           => 'Страница открыта слишком давно',
    'token'             => 'Устаревшая страница (без метки формы)',
);
$deviceLabels = array('mobile' => 'Телефон / планшет', 'desktop' => 'Компьютер');
$sourceLabel  = function ($k) { return $k === '' ? 'Прямой заход / закладка' : $k; };

$conv = $totals['sessions'] > 0 ? round($totals['orders'] * 100 / $totals['sessions'], 2) : 0;

/**
 * Таблица «название — сессии — доля» для правой колонки.
 *
 * @param array<string,int> $rows
 */
function stats_breakdown_table(array $rows, $labelFn, $empty = 'Нет данных за период')
{
    if (!$rows) {
        echo '<p class="text-muted mb-0">' . e($empty) . '</p>';
        return;
    }
    $total = array_sum($rows);
    echo '<table class="table table-sm mb-0"><tbody>';
    foreach ($rows as $k => $n) {
        $pct = $total > 0 ? round($n * 100 / $total) : 0;
        echo '<tr><td>' . e($labelFn($k)) . '</td>'
            . '<td class="text-right text-nowrap" style="width:90px;"><b>' . (int) $n . '</b></td>'
            . '<td style="width:35%;"><div class="progress" style="height:6px;margin-top:8px;"><div class="progress-bar" style="width:' . $pct . '%"></div></div></td></tr>';
    }
    echo '</tbody></table>';
}
?>
<style>
    .stat-tile .value { font-size: 1.8rem; font-weight: 700; line-height: 1.1; }
    .stat-tile .label { color: #6c757d; font-size: .85rem; }
    .funnel-row { display: flex; align-items: center; margin-bottom: .35rem; }
    .funnel-label { width: 260px; flex-shrink: 0; padding-right: 12px; }
    .funnel-label small { display: block; color: #6c757d; line-height: 1.2; }
    .funnel-bar-wrap { flex: 1; background: #f1f3f5; border-radius: 3px; height: 34px; position: relative; }
    .funnel-bar { background: #4e73df; height: 100%; border-radius: 3px; min-width: 2px; }
    .funnel-bar-text { position: absolute; left: 10px; top: 7px; font-weight: 600; color: #1a1a1a; }
    .funnel-drop { width: 190px; flex-shrink: 0; padding-left: 12px; font-size: .85rem; }
    .funnel-drop .lost { color: #e74a3b; }
    @media (max-width: 768px) {
        .funnel-row { flex-wrap: wrap; }
        .funnel-label, .funnel-drop { width: 100%; padding: 0; }
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0 text-gray-800">Статистика</h1>
    <span class="text-muted small">Учитываются посетители с включённым JavaScript; роботы и администраторы не считаются</span>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form method="get" action="/admin/stats" class="form-row align-items-end">
            <div class="col-md-2 mb-2">
                <label class="small mb-1" for="st_from">С</label>
                <input type="date" name="from" id="st_from" class="form-control form-control-sm" value="<?php echo e($filter['from']); ?>">
            </div>
            <div class="col-md-2 mb-2">
                <label class="small mb-1" for="st_to">По</label>
                <input type="date" name="to" id="st_to" class="form-control form-control-sm" value="<?php echo e($filter['to']); ?>">
            </div>
            <div class="col-md-3 mb-2">
                <label class="small mb-1" for="st_cat">Конфигуратор</label>
                <select name="cat" id="st_cat" class="custom-select custom-select-sm">
                    <option value="">Все</option>
                    <?php foreach ($configurators as $c) { ?>
                        <option value="<?php echo e($c['link']); ?>" <?php echo $filter['cat'] === $c['link'] ? 'selected' : ''; ?>><?php echo e($c['name']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <label class="small mb-1" for="st_dev">Устройство</label>
                <select name="device" id="st_dev" class="custom-select custom-select-sm">
                    <option value="">Все</option>
                    <?php foreach ($deviceLabels as $k => $l) { ?>
                        <option value="<?php echo e($k); ?>" <?php echo $filter['device'] === $k ? 'selected' : ''; ?>><?php echo e($l); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <button type="submit" class="btn btn-sm btn-primary">Показать</button>
                <a class="btn btn-sm btn-outline-secondary" href="/admin/stats?from=<?php echo date('Y-m-01'); ?>&to=<?php echo date('Y-m-d'); ?>">Этот месяц</a>
                <a class="btn btn-sm btn-outline-secondary" href="/admin/stats?from=<?php echo date('Y-m-d', strtotime('-6 days')); ?>&to=<?php echo date('Y-m-d'); ?>">7 дней</a>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <?php
    $tiles = array(
        array('Посетители', $totals['visitors'], 'уникальные браузеры'),
        array('Новые', $totals['new_visitors'], 'впервые на сайте'),
        array('Сессии', $totals['sessions'], 'визиты (30 мин без действий — новый)'),
        array('Просмотры', $totals['page_views'], 'страниц'),
        array('Заявки', $totals['orders'], 'принято сервером'),
        array('Конверсия', $conv . '%', 'сессий → заявка'),
    );
    foreach ($tiles as $t) { ?>
        <div class="col-6 col-md-4 col-xl-2 mb-4">
            <div class="card shadow h-100 stat-tile"><div class="card-body py-3">
                <div class="label"><?php echo e($t[0]); ?></div>
                <div class="value"><?php echo e((string) $t[1]); ?></div>
                <div class="label small"><?php echo e($t[2]); ?></div>
            </div></div>
        </div>
    <?php } ?>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">По дням</h6></div>
    <div class="card-body"><div style="position:relative;height:280px;"><canvas id="statsDaily"></canvas></div></div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Воронка заказа</h6>
        <small class="text-muted">Сколько сессий дошли до каждого шага и сколько отвалилось на переходе к нему</small>
    </div>
    <div class="card-body">
        <?php $top = $funnel ? max(1, $funnel[0]['sessions']) : 1;
        foreach ($funnel as $i => $s) {
            $w = round($s['sessions'] * 100 / $top, 1); ?>
            <div class="funnel-row">
                <div class="funnel-label"><b><?php echo e($s['label']); ?></b><?php if ($s['hint'] !== '') { ?><small><?php echo e($s['hint']); ?></small><?php } ?></div>
                <div class="funnel-bar-wrap">
                    <div class="funnel-bar" style="width:<?php echo $w; ?>%;"></div>
                    <span class="funnel-bar-text"><?php echo (int) $s['sessions']; ?> · <?php echo e((string) $s['from_first']); ?>%</span>
                </div>
                <div class="funnel-drop">
                    <?php if ($i > 0) { ?>
                        <?php echo e((string) $s['from_prev']); ?>% от предыдущего<br>
                        <span class="lost">−<?php echo (int) $s['lost']; ?> сессий</span>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
        <?php if (!$totals['sessions']) { ?><p class="text-muted mb-0">За период событий нет.</p><?php } ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Где бросают сборку</h6>
                <small class="text-muted">Последний блок, в котором выбирали товар, у тех, кто начал собирать, но не открыл форму заказа</small>
            </div>
            <div class="card-body">
                <?php stats_breakdown_table($abandon, function ($k) use ($blocks) { return isset($blocks[$k]) ? $blocks[$k] : $k; }); ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Ошибки формы заказа</h6>
                <small class="text-muted">Сессии, в которых покупатель увидел ошибку при отправке</small>
            </div>
            <div class="card-body">
                <?php stats_breakdown_table($errors, function ($k) use ($errorLabels) { return isset($errorLabels[$k]) ? $errorLabels[$k] : $k; }, 'Ошибок не было'); ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Откуда приходят</h6></div>
            <div class="card-body"><?php stats_breakdown_table($sources, $sourceLabel); ?></div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Страницы</h6></div>
            <div class="card-body"><?php stats_breakdown_table($byCat, function ($k) use ($catNames) { return isset($catNames[$k]) ? $catNames[$k] : $k; }); ?></div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Устройства и действия</h6></div>
            <div class="card-body">
                <?php stats_breakdown_table($devices, function ($k) use ($deviceLabels) { return isset($deviceLabels[$k]) ? $deviceLabels[$k] : $k; }); ?>
                <hr>
                <div class="d-flex justify-content-between"><span>Скопировали ссылку на сборку</span><b><?php echo (int) array_sum($shares); ?></b></div>
                <div class="d-flex justify-content-between"><span>Скачали PDF</span><b><?php echo (int) array_sum($pdfs); ?></b></div>
                <small class="text-muted">число сессий</small>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">По месяцам</h6><small class="text-muted">последние 12 месяцев, с учётом фильтров конфигуратора и устройства</small></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead><tr><th>Месяц</th><th>Посетители</th><th>Новые</th><th>Сессии</th><th>Заявки</th><th>Конверсия</th></tr></thead>
                <tbody>
                <?php if (!$monthly) { ?><tr><td colspan="6" class="text-center text-muted">Данных пока нет</td></tr><?php } ?>
                <?php
                $months = array('01' => 'Январь', '02' => 'Февраль', '03' => 'Март', '04' => 'Апрель', '05' => 'Май', '06' => 'Июнь',
                    '07' => 'Июль', '08' => 'Август', '09' => 'Сентябрь', '10' => 'Октябрь', '11' => 'Ноябрь', '12' => 'Декабрь');
                foreach ($monthly as $m) {
                    list($y, $mm) = explode('-', $m['month']);
                    $c = (int) $m['sessions'] > 0 ? round((int) $m['orders'] * 100 / (int) $m['sessions'], 2) : 0; ?>
                    <tr>
                        <td><a href="/admin/stats?from=<?php echo e($m['month']); ?>-01&to=<?php echo e(date('Y-m-t', strtotime($m['month'] . '-01'))); ?><?php echo $filter['cat'] !== '' ? '&cat=' . e(rawurlencode($filter['cat'])) : ''; ?>"><?php echo e($months[$mm] . ' ' . $y); ?></a></td>
                        <td><?php echo (int) $m['visitors']; ?></td>
                        <td><?php echo (int) $m['new_visitors']; ?></td>
                        <td><?php echo (int) $m['sessions']; ?></td>
                        <td><?php echo (int) $m['orders']; ?></td>
                        <td><?php echo e((string) $c); ?>%</td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(function () {
        var el = document.getElementById('statsDaily');
        if (!el || typeof Chart === 'undefined') { return; }
        var days = <?php echo json_encode(array_keys($daily)); ?>;
        var d = <?php echo json_encode(array_values($daily)); ?>;
        new Chart(el, {
            type: 'line',
            data: {
                labels: days.map(function (s) { var p = s.split('-'); return p[2] + '.' + p[1]; }),
                datasets: [
                    { label: 'Посетители', data: d.map(function (r) { return r.visitors; }), borderColor: '#4e73df', backgroundColor: 'rgba(78,115,223,.08)', lineTension: .25, yAxisID: 'y' },
                    { label: 'Сессии', data: d.map(function (r) { return r.sessions; }), borderColor: '#36b9cc', backgroundColor: 'transparent', lineTension: .25, yAxisID: 'y' },
                    { label: 'Заявки', data: d.map(function (r) { return r.orders; }), borderColor: '#1cc88a', backgroundColor: 'rgba(28,200,138,.25)', type: 'bar', yAxisID: 'o' }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                tooltips: { mode: 'index', intersect: false },
                scales: {
                    yAxes: [
                        { id: 'y', position: 'left', ticks: { beginAtZero: true, precision: 0 } },
                        { id: 'o', position: 'right', ticks: { beginAtZero: true, precision: 0 }, gridLines: { display: false }, scaleLabel: { display: true, labelString: 'Заявки' } }
                    ]
                }
            }
        });
    });
</script>
