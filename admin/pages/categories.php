<style>
    /* Дерево не должно вылезать за границы карточки — скроллим внутри */
    #tree { overflow: auto; max-height: 78vh; }
    #tree .jstree-anchor { white-space: nowrap; }
    /* Панель товаров "едет" вместе со скроллом */
    .products-sticky { position: -webkit-sticky; position: sticky; top: 1rem; }
</style>

<div class="row">

    <!-- Дерево категорий (25%) -->
    <div class="col-lg-3">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Категории</h6>
                <small class="text-muted">Двойной клик по типу товара — настройки</small>
            </div>
            <div class="card-body">
                <div id="tree"></div>
            </div>
        </div>
    </div>

    <!-- Список товаров выбранной ноды (75%) -->
    <div class="col-lg-9 align-self-start products-sticky">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Товары выбранной категории</h6>
                <div class="d-flex align-items-center ml-auto">
                    <span class="mr-3">Найдено: <span class="badge badge-primary" id="node_prod_count">0</span></span>
                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#exampleModal">
                        <i class="fas fa-plus mr-1"></i> Добавить товар
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="node_products_table" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>Название товара</th>
                            <th>Цена</th>
                            <th>Фильтр</th>
                            <th>Картинка</th>
                            <th>Изменить</th>
                            <th>Удалить</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
