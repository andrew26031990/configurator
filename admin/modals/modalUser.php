<?php
// Модалка создания/редактирования пользователя. Подключается в modals.php.
// Список ролей тянем из БД — чекбоксы.
if (!function_exists('getAllRoles')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/functions.php';
}
$allRoles = getAllRoles($mysqli);
?>
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="userForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Пользователь</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="userId" value="0">

                    <div class="form-group">
                        <label for="userUsername">Логин</label>
                        <input type="text" class="form-control" name="username" id="userUsername" autocomplete="off" required>
                    </div>

                    <div class="form-group">
                        <label for="userPassword">Пароль</label>
                        <input type="password" class="form-control" name="password" id="userPassword" autocomplete="new-password">
                        <small class="form-text text-muted" id="userPasswordHint">При редактировании оставьте пустым, чтобы не менять.</small>
                    </div>

                    <div class="form-group">
                        <label>Роли</label>
                        <?php foreach ($allRoles as $role) { ?>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input user-role-cb" name="roles[]"
                                       id="role_<?= (int) $role['id']; ?>" value="<?= (int) $role['id']; ?>">
                                <label class="custom-control-label" for="role_<?= (int) $role['id']; ?>">
                                    <?= e($role['label']); ?>
                                    <small class="text-muted">(<?= e($role['name']); ?>)</small>
                                </label>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="userIsActive" name="is_active" value="1" checked>
                            <label class="custom-control-label" for="userIsActive">Активен</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>
