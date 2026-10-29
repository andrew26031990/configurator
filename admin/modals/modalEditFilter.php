<div class="modal fade" id="editFilterModal" tabindex="-1" role="dialog" aria-labelledby="editFilterModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Редактирование фильтра</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editFilter">
                    <input type="hidden" name="filter_id" id="editFilter_id" value="">
                    <div class="form-group">
                        <label for="editFilter_name">Название фильтра</label>
                        <input type="text" class="form-control" name="f_name" id="editFilter_name" placeholder="Введите название фильтра">
                    </div>
                    <div class="form-group">
                        <label for="editFilter_trees">Сборки / типы товара (куда входит фильтр)</label>
                        <select class="form-control" name="filter_tree[]" id="editFilter_trees" multiple size="10">
                            <?php $editFilterGroups = getProductTypesGrouped($mysqli);
                            foreach ($editFilterGroups as $g){ ?>
                                <optgroup label="<?php echo htmlspecialchars($g['build']); ?>">
                                    <?php foreach ($g['types'] as $t){ ?>
                                        <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['name']); ?></option>
                                    <?php } ?>
                                </optgroup>
                            <?php } ?>
                        </select>
                        <small class="form-text text-muted">Снимите галку, чтобы убрать фильтр из категории.</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
