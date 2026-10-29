<h1 class="h3 mb-2 text-gray-800">Товары без категории</h1>
<p class="text-muted">Товары, не привязанные ни к одной существующей ноде дерева. Нажмите «Изменить», чтобы привязать их к сборкам/типам.</p>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <span>Список товаров без категории</span>
        <div>
            <button type="button" id="deleteSelectedNoCat" class="btn btn-sm btn-danger mr-2" style="display:none;">
                Удалить выбранные (<span id="noCatSelCount">0</span>)
            </button>
            <button type="button" id="deleteAllNoCat" class="btn btn-sm btn-outline-danger" style="display:none;">
                Удалить все
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="no_cat_table" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th style="width:30px;"><input type="checkbox" id="no_cat_select_all" /></th>
                    <th>id</th>
                    <th>Название товара</th>
                    <th>Цена</th>
                    <th>Фильтр</th>
                    <th>Картинка</th>
                    <th>Изменить</th>
                    <th>Удалить</th>
                </tr>
                </thead>
                <tbody>
                <?php $products = getProductsWithoutCategory($mysqli); foreach ($products as $item){ ?>
                    <tr>
                        <td><input type="checkbox" class="no-cat-check" data-id="<?php echo $item['id']; ?>" data-img="<?php echo $item['image']; ?>" /></td>
                        <td><?php echo $item['id']; ?></td>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo $item['price']; ?></td>
                        <td><?php echo htmlspecialchars($item['f_name']); ?></td>
                        <td><img src="/configurator/images/products/<?php echo $item['image']; ?>" class="zoomable-img" style="height: 60px;cursor:pointer;" /></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-info edit_prod_cat" data-id="<?php echo $item['id']; ?>">Изменить</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger delete_prod_cat" data-id="<?php echo $item['id']; ?>" data-img="<?php echo $item['image']; ?>">Удалить</button>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
