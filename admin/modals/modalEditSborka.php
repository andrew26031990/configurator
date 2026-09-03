<style>
    .full_modal-dialog {
        width: 98% !important;
        height: auto !important;
        min-width: 98% !important;
        min-height: 92% !important;
        max-width: 98% !important;
        max-height: 92% !important;
        padding: 0 !important;
    }

    .full_modal-content {
        height: 99% !important;
        min-height: 99% !important;
        max-height: 99% !important;
    }
</style>
<div class="modal fade" id="exampleModalEditSborka" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog full_modal-dialog" role="document">
        <div class="modal-content full_modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Редактирование сборки</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editSborka">
                    <iframe id="confSborkaEdit" src="https://configurator.pcmarket.uz" name="edit_frame" style="width: 100%;height: 1000px"></iframe>
                    <div class="form-group">
                        <label for="name">Название сборки</label>
                        <input type="text" class="form-control" name="edit_title" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Ссылка на сборку</label>
                        <input type="text" class="form-control" name="edit_link" required>
                    </div>
                    <div class="form-group">
                        <label for="picture">Картинка сборки</label>
                        <input type="file" class="form-control-file" accept="image/*" name="edit_image">
                    </div>
                    <div class="form-group">
                        <label for="picture">Цена сборки</label>
                        <input type="number" class="form-control" name="edit_price" required>
                    </div>
                    <div class="form-group">
                        <label for="picture">Описание сборки</label>
                        <div id="editor">
                            <textarea type="text" class="form-control" name="edit_description" required></textarea>
                        </div>

                    </div>
                    <input type="hidden" name="idSborki" value=""/>
                    <input type="hidden" name="edit_category" value=""/>
                    <input type="hidden" name="oldImg" value=""/>
                    <button class="btn btn-primary submitBtn">Изменить</button>
                </form>
            </div>

        </div>
    </div>
</div>
<script type="text/javascript">
    $('input[name="edit_link"]').on("paste", function () {
        setTimeout(function() {
            var link = $('input[name="edit_link"]').val();
            var price = getQueryParam(link, 'p');
            var category = getQueryParam(link, 'cat');
            if(price != null){
                $('input[name="edit_price"]').val(price.replaceAll('.',''));
                $('input[name="edit_category"]').val(category);
            }else{
                alert('В данной ссылке цена не указана');
            }
        }, 100);
    });

    $('input[name="edit_link"]').on("input", function () {
        setTimeout(function() {
            var link = $('input[name="edit_link"]').val();
            var price = getQueryParam(link, 'p');
            var category = getQueryParam(link, 'cat');
            if(price != null){
                $('input[name="edit_price"]').val(price.replaceAll('.',''));
                $('input[name="edit_category"]').val(category);
            }else{
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

    function myFunction(){
        alert();
    }
</script>
<script>
    CKEDITOR.replace( 'editor' );
</script>
