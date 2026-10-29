<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0 text-gray-800">Сборки</h1>
    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addSborkaModal">
        <i class="fas fa-plus mr-1"></i> Добавить сборку
    </button>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        Список сборок
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="tree_table" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th>id</th>
                    <th>Название сборки</th>
                    <th>Ссылка на сборку</th>
                    <th>Цена сборки</th>
                    <th>Описание</th>
                    <th>Картинка</th>
                    <th>Изменить</th>
                    <th>Удалить</th>
                </tr>
                </thead>
                <?php $sborki = getAllConstructors($mysqli);foreach ($sborki as $item){ ?>
                    <tr>
                        <td>
                            <?php echo e($item['id']); ?>
                        </td>
                        <td>
                            <?= e_html($item['title']); ?>
                        </td>
                        <td>
                            <a href="<?= e_url($item['link']); ?>" target="_blank" rel="noopener">Ссылка</a>
                        </td>
                        <td>
                            <?= e($item['price']); ?>
                        </td>
                        <td>
                            <?= e_html($item['description']); ?>
                        </td>
                        <td>
                            <img src="/configurator/images/sborki/<?= e($item['image']); ?>" style="height: 85px;" />
                        </td>
                        <td>
                            <button dataId="<?php echo e($item['id']); ?>" type="button" class="btn btn-info edit_sborka" data-toggle="modal" data-target="#exampleModalEditSborka">Изменить</button>
                        </td>
                        <td>
                            <button dataId="<?php echo e($item['id']); ?>:<?php echo e($item['image']); ?>" type="button" class="btn btn-danger delete_sborka">Удалить</button>
                        </td>
                    </tr>
                <?php } ?>
                <tfoot>
                <tr>
                    <th>id</th>
                    <th>Название сборки</th>
                    <th>Ссылка на сборку</th>
                    <th>Цена сборки</th>
                    <th>Описание</th>
                    <th>Картинка</th>
                    <th>Изменить</th>
                    <th>Удалить</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
