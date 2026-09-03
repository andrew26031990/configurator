<h1 class="h3 mb-2 text-gray-800">Удаление товара из категории</h1>
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
                <?php $fT = getTreeProdRelations($mysqli); foreach ($fT as $item){ ?>
                    <tr id="<?php echo e($item['id']); ?>">
                        <td>
                            <?php echo e($item['sborka']); ?>
                        </td>
                        <td>
                            <?= e($item['name']); ?>
                        </td>
                        <td>
                            <?= e($item['product']); ?>
                        </td>
                        <td>
                            <button dataId="<?php echo e($item['id']); ?>" type="button" class="btn btn-danger delete_relation_prod_tree">Delete</button>
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