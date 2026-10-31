<div class="modal fade" id="blockBulkDeleteModal" tabindex="-1" role="dialog" aria-labelledby="blockBulkDeleteModalTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="blockBulkDeleteModalTitle">Удаление товаров</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="blockBulkDeleteMode" value="selected">

                <p>Вы уверены, что хотите удалить <strong id="blockBulkDeleteCount">0</strong> элементов?
                    Это действие нельзя отменить.</p>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="1" id="blockBulkUnlinkShared">
                    <label class="form-check-label" for="blockBulkUnlinkShared">
                        Товары, которые есть и в других блоках, только убрать из этого блока
                    </label>
                    <small class="form-text text-muted">
                        Без галочки такие товары не удаляются — в отчёте будет указано, где они используются.
                    </small>
                </div>

                <div id="blockBulkDeleteAllConfirm" style="display:none;">
                    <label for="blockBulkDeletePhrase">Будут удалены <b>все</b> товары блока. Для подтверждения введите: <b>удалить все</b></label>
                    <input type="text" class="form-control" id="blockBulkDeletePhrase" autocomplete="off">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger" id="blockBulkDeleteConfirm">Удалить</button>
            </div>
        </div>
    </div>
</div>
