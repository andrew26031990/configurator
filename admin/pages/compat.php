<?php
// Совместимость: сокет и тип памяти у плат, процессоров и ОЗУ. Гейт products.view — в admin.php.

$roles    = compat_roles();
$role     = isset($_GET['role']) && isset($roles[$_GET['role']]) ? (string) $_GET['role'] : '';
$products = getCompatProducts($mysqli, $role);
$canEdit  = user_can('products.edit');

$missing = 0;
foreach ($products as $p) {
    if (($p['role'] !== 'ram' && $p['socket'] === '') || $p['mem_type'] === '') {
        $missing++;
    }
}
?>
<style>
    #compat_table td { vertical-align: middle; }
    #compat_table tr.compat-missing { background: #fff8e1; }
    .compat-socket { width: 130px; }
    .compat-saved { color: #28a745; display: none; }
</style>

<div class="d-flex justify-content-between align-items-center mb-2">
    <h1 class="h3 mb-0 text-gray-800">Совместимость</h1>
    <?php if ($canEdit) { ?>
    <button type="button" class="btn btn-primary" id="compatAutofill">
        <i class="fas fa-magic mr-1"></i> Заполнить пустые из названий
    </button>
    <?php } ?>
</div>
<p class="text-muted">
    Покупатель на витрине видит несовместимые товары тусклыми: процессор должен совпадать с платой по сокету,
    ОЗУ — подходить плате и процессору по типу памяти. Пустое поле — «неизвестно», такой товар не ограничивается.
    Роль блока (плата / процессор / ОЗУ) задаётся в настройках категории (двойной клик в дереве).
</p>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-wrap align-items-center">
        <div class="btn-group btn-group-sm mr-3" role="group">
            <a href="/admin/compat" class="btn btn-<?php echo $role === '' ? 'primary' : 'outline-primary'; ?>">Все</a>
            <?php foreach ($roles as $code => $label) { ?>
                <a href="/admin/compat?role=<?php echo e($code); ?>" class="btn btn-<?php echo $role === $code ? 'primary' : 'outline-primary'; ?>"><?php echo e($label); ?></a>
            <?php } ?>
        </div>
        <div class="custom-control custom-switch mr-3">
            <input type="checkbox" class="custom-control-input" id="compatOnlyMissing">
            <label class="custom-control-label" for="compatOnlyMissing">Только незаполненные</label>
        </div>
        <span class="ml-auto">
            Товаров: <span class="badge badge-primary"><?php echo count($products); ?></span>
            Незаполнено: <span class="badge badge-<?php echo $missing ? 'warning' : 'success'; ?>" id="compatMissingCount"><?php echo $missing; ?></span>
        </span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="compat_table" width="100%">
                <thead>
                <tr>
                    <th style="width:60px;">id</th>
                    <th>Товар</th>
                    <th style="width:130px;">Роль</th>
                    <th style="width:150px;">Сокет</th>
                    <th style="width:190px;">Тип памяти</th>
                    <th>Блоки</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($products as $p) {
                    $mem       = $p['mem_type'] === '' ? array() : explode(',', $p['mem_type']);
                    $isMissing = ($p['role'] !== 'ram' && $p['socket'] === '') || $p['mem_type'] === '';
                    ?>
                    <tr class="<?php echo $isMissing ? 'compat-missing' : ''; ?>" data-id="<?php echo (int) $p['id']; ?>" data-role="<?php echo e($p['role']); ?>">
                        <td><?php echo (int) $p['id']; ?></td>
                        <td><?php echo e($p['name']); ?></td>
                        <td><?php echo e(isset($roles[$p['role']]) ? $roles[$p['role']] : $p['role']); ?></td>
                        <td data-order="<?php echo e($p['socket']); ?>">
                            <?php if ($p['role'] === 'ram') { ?>
                                <span class="text-muted">—</span>
                            <?php } elseif ($canEdit) { ?>
                                <select class="custom-select custom-select-sm compat-socket">
                                    <option value="">— не указан —</option>
                                    <?php
                                    $sockets = compat_sockets();
                                    if ($p['socket'] !== '' && !in_array($p['socket'], $sockets, true)) {
                                        $sockets[] = $p['socket'];
                                    }
                                    foreach ($sockets as $s) { ?>
                                        <option value="<?php echo e($s); ?>" <?php echo $s === $p['socket'] ? 'selected' : ''; ?>><?php echo e($s); ?></option>
                                    <?php } ?>
                                </select>
                            <?php } else {
                                echo $p['socket'] !== '' ? e($p['socket']) : '<span class="text-muted">—</span>';
                            } ?>
                        </td>
                        <td data-order="<?php echo e($p['mem_type']); ?>">
                            <?php if ($canEdit) {
                                foreach (compat_mem_types() as $t) { ?>
                                    <div class="custom-control custom-checkbox custom-control-inline mr-2">
                                        <input type="checkbox" class="custom-control-input compat-mem" value="<?php echo e($t); ?>"
                                               id="mem_<?php echo (int) $p['id'] . '_' . e($t); ?>" <?php echo in_array($t, $mem, true) ? 'checked' : ''; ?>>
                                        <label class="custom-control-label" for="mem_<?php echo (int) $p['id'] . '_' . e($t); ?>"><?php echo e($t); ?></label>
                                    </div>
                                <?php } ?>
                                <i class="fas fa-check compat-saved" title="Сохранено"></i>
                            <?php } else {
                                echo $p['mem_type'] !== '' ? e(str_replace(',', ', ', $p['mem_type'])) : '<span class="text-muted">—</span>';
                            } ?>
                        </td>
                        <td class="small text-muted"><?php echo e($p['blocks']); ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
