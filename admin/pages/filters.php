<h1 class="h3 mb-2 text-gray-800">Фильтры</h1>
<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#exampleModalLabelFilter">Добавить фильтр</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="tree_table" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th>id</th>
                    <th>Название фильтра</th>
                    <th>удалить</th>
                    <th>редактировать</th>
                </tr>
                </thead>
                <?php $filters = getAllFilters($mysqli); foreach ($filters as $item){ ?>
                    <tr>
                        <td>
                            <?php echo $item['id']; ?>
                        </td>
                        <td>
                            <?=$item['f_name']; ?>
                        </td>
                        <td>
                            <button dataId="<?php echo $item['id']; ?>" type="button" class="btn btn-danger delete_filter">Delete</button>
                        </td>
                        <td>
                            <button dataId="<?php echo $item['id'].':'.$item['f_name']; ?>" type="button" class="btn btn-info edit_filter">Edit</button>
                        </td>
                    </tr>
                <?php } ?>
                <tfoot>
                <tr>
                    <th>id</th>
                    <th>Название фильтра</th>
                    <th>удалить</th>
                    <th>редактировать</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>