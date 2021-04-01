<h1 class="h3 mb-2 text-gray-800">Товары</h1>
<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        Добавить новый товар
    </div>
    <div class="card-body">
        <form id="addProduct">
            <div class="form-group">
                <label for="name">Название товара</label>
                <input type="text" class="form-control" name="name" placeholder="Введите название товара">
            </div>
            <div class="form-group">
                <label for="price">Цена товара</label>
                <input type="number" class="form-control" name="price" placeholder="Цена товара">
            </div>
            <div class="form-group">
                <label for="picture">Картинка товара</label>
                <input type="file" class="form-control-file" accept="image/*" name="picture">
            </div>
            <div class="form-group">
                <div class="row-fluid">
                    <label for="filter">Добавить фильтр</label>
                    <select class="form-control" name="filter">
                        <?php $filters = getAllFilters($mysqli);
                        foreach ($filters as $item){
                            ?>
                            <option value="<?php echo $item['id']; ?>"><?php echo $item['f_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="description">Описание товара</label>
                <textarea class="form-control" name="description" rows="5" placeholder="Описание товара"></textarea>
            </div>
            <!--<h1 class="h3 mb-2 text-gray-800">Добавление товара в категорию</h1>
            <div class="form-group">
                <label for="levels2">Выберите сборку</label>
                <select class="form-control" name="levels2" id="sborka2">
                    <option value="0">Выберите сборку</option>
                    <?php /*$node = getNodes($mysqli, 1);
                    foreach ($node as $item){
                        */?>
                        <option value="<?php /*echo $item['id']; */?>"><?php /*echo $item['name']; */?></option>
                    <?php /*} */?>
                </select>
            </div>
            <div class="form-group">
                <label for="levels2">Выберите тип товара</label>
                <select class="form-control" name="levels2" id="tip_tovara2">
                    <option value="0">Выберите тип товара</option>
                </select>
            </div>-->
            <button class="btn btn-secondary">Редактировать</button>
            <button class="btn btn-primary submitBtn">Сохранить</button>
        </form>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <!--<button type="button" class="btn btn-success" data-toggle="modal" data-target="#exampleModal">Добавить товар</button>-->
        Список товаров
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="tree_table" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th>id</th>
                    <th>Название товара</th>
                    <th>Описание</th>
                    <th>Цена</th>
                    <th>Картинка</th>
                    <th>Фильтр</th>
                    <th>удалить</th>
                    <th>редактировать</th>
                </tr>
                </thead>
                <?php $products = getAllProducts($mysqli);foreach ($products as $item){ ?>
                    <tr>
                        <td>
                            <?php echo $item['id']; ?>
                        </td>
                        <td>
                            <?=$item['name']; ?>
                        </td>
                        <td>
                            <?=$item['description']; ?>
                        </td>
                        <td>
                            <?=$item['price']; ?>
                        </td>
                        <td><img src="/configurator/images/products/<?=$item['image']; ?>" style="height: 85px;" /></td>
                        <td>
                            <?=$item['f_name']; ?>
                        </td>
                        <td>
                            <button dataId="<?php echo $item['id']; ?>:<?php echo $item['image']; ?>" type="button" class="btn btn-danger delete_prod">Delete</button>
                        </td>
                        <td>
                            <button dataId="<?php echo $item['id']; ?>" type="button" class="btn btn-info edit_prod" data-toggle="modal" data-target="#exampleModalEditProd">Edit</button>
                        </td>
                    </tr>
                <?php } ?>
                <tfoot>
                <tr>
                    <th>id</th>
                    <th>Название товара</th>
                    <th>Описание</th>
                    <th>Цена</th>
                    <th>Картинка</th>
                    <th>Фильтр</th>
                    <th>удалить</th>
                    <th>редактировать</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>