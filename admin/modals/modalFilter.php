<div class="modal fade" id="exampleModalLabelFilter" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelFilter" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Добавление фильтра</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addFilter">
                    <div class="form-group">
                        <label for="name">Название фильтра</label>
                        <input type="text" class="form-control" name="filter" placeholder="Введите название фильтра">
                    </div>
                    <div class="form-group">
                        <label for="addFilter_trees">Сборки / типы товара (куда входит фильтр)</label>
                        <select class="form-control" name="filter_tree[]" id="addFilter_trees" multiple size="10">
                            <?php $addFilterGroups = getProductTypesGrouped($mysqli);
                            foreach ($addFilterGroups as $g){ ?>
                                <optgroup label="<?php echo htmlspecialchars($g['build']); ?>">
                                    <?php foreach ($g['types'] as $t){ ?>
                                        <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['name']); ?></option>
                                    <?php } ?>
                                </optgroup>
                            <?php } ?>
                        </select>
                        <small class="form-text text-muted">Удерживайте Ctrl (⌘ на Mac), чтобы выбрать несколько. Можно оставить пустым.</small>
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