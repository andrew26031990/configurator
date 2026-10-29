<div class="modal fade" id="settingsCategoryModal" tabindex="-1" role="dialog" aria-labelledby="settingsCategoryModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="settingsCategoryModalTitle">Настройки типа товара</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="settings_tree_id" value="">

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="1" id="uncheck_btn">
                    <label class="form-check-label" for="uncheck_btn">
                        Показывать кнопку «Убрать» (управление кнопкой "Убрать")
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="onLoadcountable">
                    <label class="form-check-label" for="onLoadcountable">
                        Автодобавление в конфигурацию товара из данного типа товара
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary" id="saveCategorySettings">Сохранить</button>
            </div>
        </div>
    </div>
</div>
