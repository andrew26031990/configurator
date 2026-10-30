<?php
// Страница управления пользователями (RBAC). Гейт users.manage стоит в admin.php.
$users = getAllUsersWithRoles($mysqli);
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0 text-gray-800">Пользователи</h1>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#userModal" id="addUserBtn">
        <i class="fas fa-user-plus mr-1"></i> Добавить пользователя
    </button>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="users" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th>Логин</th>
                    <th>Роли</th>
                    <th>Статус</th>
                    <th>Последний вход</th>
                    <th style="width:140px;">Действия</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $item) { ?>
                    <tr>
                        <td><?= e($item['username']); ?></td>
                        <td><?= $item['roles'] !== '' ? e($item['roles']) : '<span class="text-muted">—</span>'; ?></td>
                        <td>
                            <?php if ((int) $item['is_active'] === 1) { ?>
                                <span class="badge badge-success">Активен</span>
                            <?php } else { ?>
                                <span class="badge badge-secondary">Отключён</span>
                            <?php } ?>
                        </td>
                        <td><?= e($item['last_login']); ?></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-info edit_user" data-id="<?= (int) $item['id']; ?>">
                                Изменить
                            </button>
                            <button type="button" class="btn btn-sm btn-danger delete_user"
                                    data-id="<?= (int) $item['id']; ?>" data-username="<?= e($item['username']); ?>">
                                Удалить
                            </button>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
