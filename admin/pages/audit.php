<?php
// Журнал действий пользователей админки. Гейт audit.view стоит в admin.php.

$perPage = 50;
$actions = audit_actions();

$filter = array(
    'user_id'   => get_int('user_id', 0),
    'action'    => isset($_GET['action']) && isset($actions[$_GET['action']]) ? (string) $_GET['action'] : '',
    'date_from' => isset($_GET['date_from']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['date_from']) ? $_GET['date_from'] : '',
    'date_to'   => isset($_GET['date_to']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['date_to']) ? $_GET['date_to'] : '',
    'q'         => isset($_GET['q']) && is_scalar($_GET['q']) ? trim(mb_substr((string) $_GET['q'], 0, 100, 'UTF-8')) : '',
);

$page   = max(1, (int) get_int('p', 1));
$result = getAuditLog($mysqli, $filter, $perPage, ($page - 1) * $perPage);
$pages  = max(1, (int) ceil($result['total'] / $perPage));
$users  = getAuditUsers($mysqli);

/**
 * Ссылка на страницу журнала с текущими фильтрами.
 */
function audit_page_url(array $filter, $page)
{
    $params = array_filter($filter, function ($v) { return $v !== '' && $v !== 0 && $v !== null; });
    $params['p'] = $page;
    return '/admin/audit?' . http_build_query($params);
}

// Человеческие названия полей в блоке «было → стало».
$fieldLabels = array(
    'name' => 'Название', 'f_name' => 'Название', 'title' => 'Название', 'username' => 'Логин',
    'description' => 'Описание', 'price' => 'Цена', 'f_id' => 'Фильтр (id)', 'tree_ids' => 'Категории (id)',
    'image' => 'Картинка', 'link' => 'Ссылка', 'category' => 'Категория', 'is_active' => 'Активен',
    'roles' => 'Роли', 'password' => 'Пароль', 'uncheck_btn' => 'Кнопка «Убрать»',
    'onLoadcountable' => 'Автодобавление в сборку', 'compat_role' => 'Роль совместимости',
    'socket' => 'Сокет', 'mem_type' => 'Тип памяти',
);

/**
 * Значение из details для показа: массивы — JSON, null — прочерк.
 */
function audit_value($v)
{
    if ($v === null || $v === '') {
        return '—';
    }
    if (is_array($v)) {
        return json_encode($v, JSON_UNESCAPED_UNICODE);
    }
    return (string) $v;
}
?>
<style>
    .audit-table td { vertical-align: top; }
    .audit-details summary { cursor: pointer; color: #007bff; }
    .audit-details pre { white-space: pre-wrap; word-break: break-all; font-size: 12px; max-height: 300px; overflow: auto; }
    .audit-changes td { font-size: 12px; padding: .2rem .4rem; }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0 text-gray-800">Журнал действий</h1>
    <span class="text-muted">Записей: <b><?php echo (int) $result['total']; ?></b></span>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form method="get" action="/admin/audit" class="form-row align-items-end">
            <div class="col-md-2 mb-2">
                <label class="small mb-1" for="audit_user">Пользователь</label>
                <select name="user_id" id="audit_user" class="custom-select custom-select-sm">
                    <option value="">Все</option>
                    <?php foreach ($users as $u) { ?>
                        <option value="<?php echo (int) $u['user_id']; ?>" <?php echo (int) $u['user_id'] === $filter['user_id'] ? 'selected' : ''; ?>>
                            <?php echo e($u['username']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <label class="small mb-1" for="audit_action">Действие</label>
                <select name="action" id="audit_action" class="custom-select custom-select-sm">
                    <option value="">Все</option>
                    <?php foreach ($actions as $code => $a) { ?>
                        <option value="<?php echo e($code); ?>" <?php echo $code === $filter['action'] ? 'selected' : ''; ?>><?php echo e($a[0]); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <label class="small mb-1" for="audit_from">С</label>
                <input type="date" name="date_from" id="audit_from" class="form-control form-control-sm" value="<?php echo e($filter['date_from']); ?>">
            </div>
            <div class="col-md-2 mb-2">
                <label class="small mb-1" for="audit_to">По</label>
                <input type="date" name="date_to" id="audit_to" class="form-control form-control-sm" value="<?php echo e($filter['date_to']); ?>">
            </div>
            <div class="col-md-2 mb-2">
                <label class="small mb-1" for="audit_q">Поиск</label>
                <input type="text" name="q" id="audit_q" class="form-control form-control-sm" placeholder="название, логин, id" value="<?php echo e($filter['q']); ?>">
            </div>
            <div class="col-md-1 mb-2 d-flex">
                <button type="submit" class="btn btn-sm btn-primary mr-1" title="Применить"><i class="fas fa-search"></i></button>
                <a href="/admin/audit" class="btn btn-sm btn-outline-secondary" title="Сбросить фильтры"><i class="fas fa-times"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm audit-table" width="100%">
                <thead>
                <tr>
                    <th style="width:150px;">Дата и время</th>
                    <th style="width:130px;">Пользователь</th>
                    <th style="width:200px;">Действие</th>
                    <th>Описание</th>
                    <th style="width:120px;">IP</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!$result['rows']) { ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Записей нет</td></tr>
                <?php } ?>
                <?php foreach ($result['rows'] as $row) {
                    $a       = isset($actions[$row['action']]) ? $actions[$row['action']] : array($row['action'], 'light');
                    $details = $row['details'] !== null ? json_decode($row['details'], true) : null;
                    $ids     = json_decode($row['item_ids'], true);
                    $changes = is_array($details) && isset($details['changes']) && is_array($details['changes']) ? $details['changes'] : null;
                    ?>
                    <tr>
                        <td class="text-nowrap"><?php echo e(date('d.m.Y H:i:s', strtotime($row['created_at']))); ?></td>
                        <td>
                            <?php echo $row['username'] !== '' ? e($row['username']) : '<span class="text-muted">—</span>'; ?>
                            <?php if ($row['user_id'] === null && $row['action'] !== 'auth.login_failed') { ?>
                                <span class="badge badge-light">система</span>
                            <?php } ?>
                        </td>
                        <td><span class="badge badge-<?php echo e($a[1]); ?>"><?php echo e($a[0]); ?></span></td>
                        <td>
                            <?php echo e($row['summary']); ?>

                            <?php if ($changes) { ?>
                                <table class="table table-borderless audit-changes mb-0 mt-1">
                                    <?php foreach ($changes as $field => $ch) { ?>
                                        <tr>
                                            <td class="text-muted" style="width:170px;"><?php echo e(isset($fieldLabels[$field]) ? $fieldLabels[$field] : $field); ?></td>
                                            <td><del class="text-danger"><?php echo e(audit_value(isset($ch['было']) ? $ch['было'] : null)); ?></del>
                                                → <span class="text-success"><?php echo e(audit_value(isset($ch['стало']) ? $ch['стало'] : null)); ?></span></td>
                                        </tr>
                                    <?php } ?>
                                </table>
                            <?php } elseif (is_array($details) && $details) { ?>
                                <details class="audit-details mt-1">
                                    <summary class="small">Подробности</summary>
                                    <pre class="mb-0"><?php echo e(json_encode($details, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)); ?></pre>
                                </details>
                            <?php } ?>

                            <?php if (is_array($ids) && count($ids) > 1) { ?>
                                <details class="audit-details mt-1">
                                    <summary class="small">ID объектов (<?php echo count($ids); ?>)</summary>
                                    <pre class="mb-0"><?php echo e(implode(', ', $ids)); ?></pre>
                                </details>
                            <?php } ?>
                        </td>
                        <td class="text-nowrap small"><?php echo e($row['ip']); ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

        <?php if ($pages > 1) { ?>
            <nav>
                <ul class="pagination pagination-sm justify-content-end mb-0">
                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo e(audit_page_url($filter, $page - 1)); ?>">«</a>
                    </li>
                    <?php
                    $from = max(1, $page - 3);
                    $to   = min($pages, $page + 3);
                    for ($i = $from; $i <= $to; $i++) { ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="<?php echo e(audit_page_url($filter, $i)); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php } ?>
                    <li class="page-item <?php echo $page >= $pages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo e(audit_page_url($filter, $page + 1)); ?>">»</a>
                    </li>
                </ul>
            </nav>
        <?php } ?>
    </div>
</div>
