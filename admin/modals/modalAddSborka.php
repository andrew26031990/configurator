<div class="modal fade" id="addSborkaModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Добавление сборки</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted">
                    Соберите конфигурацию ниже, скопируйте ссылку и вставьте её в поле «Ссылка на сборку» —
                    цена и категория подставятся автоматически.
                </p>
                <iframe id="confSborka" src="https://configurator.pcmarket.uz"
                        allow="clipboard-write; clipboard-read"
                        style="width:100%;height:600px;border:1px solid #ddd;border-radius:4px;"></iframe>

                <form id="addSborka" class="mt-3">
                    <div class="form-group">
                        <label>Название сборки</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Ссылка на сборку</label>
                        <input type="text" class="form-control" name="link" required>
                    </div>
                    <div class="form-group">
                        <label>Картинка сборки</label>
                        <input type="file" class="form-control-file" accept="image/*" name="image" required>
                    </div>
                    <div class="form-group">
                        <label>Цена сборки</label>
                        <input type="number" class="form-control" name="price" required>
                    </div>
                    <div class="form-group">
                        <label>Описание сборки</label>
                        <textarea class="form-control" name="description" rows="4" required></textarea>
                    </div>
                    <input type="hidden" name="category" value=""/>
                    <div class="modal-footer px-0">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        <button type="submit" class="btn btn-primary submitBtn">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    (function () {
        function sborkaGetQueryParam(url, key) {
            var queryStartPos = url.indexOf('?');
            if (queryStartPos === -1) { return; }
            var params = url.substring(queryStartPos + 1).split('&');
            for (var i = 0; i < params.length; i++) {
                var pairs = params[i].split('=');
                if (decodeURIComponent(pairs.shift()) == key) {
                    return decodeURIComponent(pairs.join('='));
                }
            }
        }
        function fillFromLink() {
            var link = $('#addSborka input[name="link"]').val();
            var price = sborkaGetQueryParam(link, 'p');
            var category = sborkaGetQueryParam(link, 'cat');
            if (price != null) {
                $('#addSborka input[name="price"]').val(String(price).split('.').join(''));
                $('#addSborka input[name="category"]').val(category || '');
            }
        }
        $(document).on('paste input', '#addSborka input[name="link"]', function () {
            setTimeout(fillFromLink, 100);
        });
    })();
</script>
