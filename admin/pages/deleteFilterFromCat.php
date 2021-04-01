<h1 class="h3 mb-2 text-gray-800">Удаление Фильтра из категории</h1>
<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="tree_table" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th>Сборка</th>
                    <th>Переферийное устройство</th>
                    <th>Имя товара</th>
                    <th>Удалить</th>
                </tr>
                </thead>
                <?php $fT = getTreeFilterRelations($mysqli); foreach ($fT as $item){ ?>
                    <tr id="<?php echo $item['id']; ?>">
                        <td>
                            <?php echo $item['sborka']; ?>
                        </td>
                        <td>
                            <?=$item['name']; ?>
                        </td>
                        <td>
                            <?=$item['f_name']; ?>
                        </td>
                        <td>
                            <button dataId="<?php echo $item['id']; ?>" type="button" class="btn btn-danger delete_relation_filter_tree">Delete</button>
                        </td>
                    </tr>
                <?php } ?>
                <tfoot>
                <tr>
                    <th>Сборка</th>
                    <th>Переферийное устройство</th>
                    <th>Имя товара</th>
                    <th>Удалить</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>