<h1 class="h3 mb-2 text-gray-800">Пользователи</h1>
<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="users" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th>Имя пользователя</th>
                    <th>Дата последнего логирования</th>
                </tr>
                </thead>
                <?php $users = getAllUsers($mysqli); foreach ($users as $item) { ?>
                    <tr>
                        <td>
                            <?= e($item['username']); ?>
                        </td>
                        <td>
                            <?= e($item['last_login']); ?>
                        </td>
                    </tr>
                <?php } ?>
                <tfoot>
                <tr>
                    <th>Имя пользователя</th>
                    <th>Дата последнего логирования</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
