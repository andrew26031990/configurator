<div class="modal fade" id="settingsCategoryModal" tabindex="-1" role="dialog" aria-labelledby="settingsCategoryModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h1 class="h3 mb-2 text-gray-800">Добавление товара в категории</h1>
                <div class="form-group">
                    <label for="levels2">Выберите сборку</label>
                    <select class="form-control" name="levels2" id="sborka2">
                        <option value="0">Выберите сборку</option>
                        <?php $node = getNodes($mysqli, 1);
                        foreach ($node as $item){
                            ?>
                            <option value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="levels2">Выберите тип товара</label>
                    <select class="form-control" name="levels2" id="tip_tovara2">
                        <option value="0">Выберите тип товара</option>
                    </select>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="uncheck_btn">
                    <label class="form-check-label" for="uncheck_btn">
                        Управление кнопкой "Убрать" (поставьте галочку если хотите показывать эту кнопку)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="onLoadcountable">
                    <label class="form-check-label" for="onLoadcountable">
                        Автодобавление в конфигурацию товара из данного типа товара
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="defaultCheck2" disabled>
                    <label class="form-check-label" for="defaultCheck2">
                        Disabled checkbox
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveCategorySettings">Save changes</button>
            </div>
        </div>
    </div>
</div>