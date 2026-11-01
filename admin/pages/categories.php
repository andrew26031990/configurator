<style>
    /* Дерево не должно вылезать за границы карточки — скроллим внутри */
    #tree { overflow: auto; max-height: 78vh; }
    #tree .jstree-anchor { white-space: nowrap; }
    /* Панель товаров "едет" вместе со скроллом */
    .products-sticky { position: -webkit-sticky; position: sticky; top: 1rem; }
    /* Сортировка товаров блока */
    .block-drag { cursor: grab; color: #6c757d; }
    .block-drag.disabled { cursor: not-allowed; opacity: .3; }
    .block-sort-input { width: 80px; }
    .block-sort-input.is-invalid { border-color: #dc3545; }
    #node_products_table tr.sortable-ghost { opacity: .4; background: #e9f2ff; }
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
                    <?php if (user_can('products.edit')) { ?>
                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#exampleModal">
                        <i class="fas fa-plus mr-1"></i> Добавить товар
                    </button>
                    <?php } ?>
                </div>
            </div>
            <div class="card-body">
                <!-- Панель блока: видна, только когда выбран тип-нода (блок) -->
                <div id="blockToolbar" class="mb-3" style="display:none;">
                    <div class="d-flex flex-wrap align-items-center">
                        <strong class="mr-3">Блок: <span id="blockName"></span></strong>

                        <?php if (user_can('products.edit')) { ?>
                        <button type="button" id="applyBlockSort" class="btn btn-sm btn-primary mr-2 mb-1" disabled>
                            <i class="fas fa-save mr-1"></i> Применить сортировку
                        </button>
                        <div class="input-group input-group-sm mr-2 mb-1" style="width:auto;">
                            <select id="blockSortReset" class="custom-select custom-select-sm">
                                <option value="price">По цене</option>
                                <option value="name">По названию</option>
                                <option value="id">По ID (дате добавления)</option>
                            </select>
                            <div class="input-group-append">
                                <button type="button" id="blockSortResetBtn" class="btn btn-outline-secondary">Сбросить порядок</button>
                            </div>
                        </div>
                        <span id="blockSortStatus" class="small text-muted mr-3 mb-1"></span>
                        <?php } ?>

                        <?php if (user_can('products.delete')) { ?>
                        <div class="ml-auto mb-1">
                            <button type="button" id="blockDeleteSelected" class="btn btn-sm btn-danger mr-2" disabled>
                                Удалить выбранные (<span id="blockSelCount">0</span>)
                            </button>
                            <button type="button" id="blockDeleteAll" class="btn btn-sm btn-outline-danger">
                                Удалить все в блоке
                            </button>
                        </div>
                        <?php } ?>
                    </div>
                    <?php if (user_can('products.edit')) { ?>
                    <small class="text-muted d-block">
                        Перетаскивайте строки за <i class="fas fa-grip-vertical"></i> или задайте номер в колонке «Порядок»,
                        затем нажмите «Применить сортировку». Перетаскивание доступно, когда таблица отсортирована
                        по «Порядку» и поиск пуст.
                    </small>
                    <?php } ?>
                    <div id="blockResult" class="mt-2"></div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="node_products_table" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th style="width:30px;"><input type="checkbox" id="block_select_all" title="Выбрать все" /></th>
                            <th style="width:30px;"></th>
                            <th>id</th>
                            <th>Название товара</th>
                            <th>Порядок</th>
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
