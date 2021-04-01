<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Добавление товара</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
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
                            <label for="filter">Выберите фильтр</label>
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        <button type="submit" class="btn btn-primary submitBtn">Сохранить</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>