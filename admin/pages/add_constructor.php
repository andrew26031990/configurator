<h1 class="h3 mb-2 text-gray-800">Добавление сборки</h1>

<iframe id="confSborka" src="https://configurator.pcmarket.uz" style="width: 100%;height: 1000px"></iframe>

<div class="card-body">
    <form id="addSborka">
        <div class="form-group">
            <label for="name">Название сборки</label>
            <input type="text" class="form-control" name="name" required>
        </div>
        <div class="form-group">
            <label for="price">Ссылка на сборку</label>
            <input type="text" class="form-control" name="link" required>
        </div>
        <div class="form-group">
            <label for="picture">Картинка сборки</label>
            <input type="file" class="form-control-file" accept="image/*" name="image" required>
        </div>
        <div class="form-group">
            <label for="picture">Цена сборки</label>
            <input type="number" class="form-control" name="price" required>
        </div>
        <div class="form-group">
            <label for="picture">Описание сборки</label>

                <textarea type="text" id="editor" class="form-control" name="description" required>

                </textarea>

        </div>
        <input type="hidden" name="category" value=""/>
        <button class="btn btn-primary submitBtn">Сохранить</button>
    </form>
</div>

<script type="text/javascript">
    $('input[name="link"]').on("paste", function () {
        setTimeout(function () {
            var link = $('input[name="link"]').val();
            var price = getQueryParam(link, 'p');
            var category = getQueryParam(link, 'cat');
            if (price != null) {
                $('input[name="price"]').val(price.replaceAll('.', ''));
                $('input[name="category"]').val(category);
            } else {
                alert('В данной ссылке цена не указана');
            }
        }, 100);
    });

    $('input[name="link"]').on("input", function () {
        setTimeout(function () {
            var link = $('input[name="link"]').val();
            var price = getQueryParam(link, 'p');
            var category = getQueryParam(link, 'cat');
            if (price != null) {
                $('input[name="price"]').val(price.replaceAll('.', ''));
                $('input[name="category"]').val(category);
            } else {
                alert('В данной ссылке цена не указана');
            }
        }, 100);
    });

    function getQueryParam(url, key) {
        var queryStartPos = url.indexOf('?');
        if (queryStartPos === -1) {
            return;
        }
        var params = url.substring(queryStartPos + 1).split('&');
        for (var i = 0; i < params.length; i++) {
            var pairs = params[i].split('=');
            if (decodeURIComponent(pairs.shift()) == key) {
                return decodeURIComponent(pairs.join('='));
            }
        }
    }
</script>
<script>
    CKEDITOR.replace( 'editor' );
</script>
