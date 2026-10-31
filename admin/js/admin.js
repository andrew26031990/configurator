$(document).ready(function() {
    $('body .preloader2').fadeOut('slow', function () {
            });
    /*setTimeout(function(){
            $('body .preloader2').fadeOut('slow', function () {
            });
        },10000);  */

    $('#tree_table_filter').css('float', 'right');

    $(".delete_prod").on('click',(function(e) {
        e.preventDefault();
        var r = confirm("Вы действительно хотите удалить товар?");
          if (r === true) {
            var prod_id = $(this).attr("dataId");
            $.ajax({
            url: "../modules/deleteProduct.php",
             type: "POST",
             data:  {prod_id: prod_id},
             beforeSend : function()
             {
              //alert('beforesend')
             },
             success: function(data)
                {
                    alert(data);
                    window.location.reload();
                },
               error: function(e)
                {
                alert(e.responseText || ('Ошибка ' + e.status));
                   }
                 });

          }

    }));

    $(".delete_sborka").on('click',(function(e) {
        e.preventDefault();
        var r = confirm("Вы действительно хотите удалить сборку?");
        if (r === true) {
            var prod_id = $(this).attr("dataId");
            $.ajax({
                url: "../modules/deleteSborka.php",
                type: "POST",
                data:  {prod_id: prod_id},
                beforeSend : function()
                {
                    //alert('beforesend')
                },
                success: function(data)
                {
                    alert(data);
                    window.location.reload();
                },
                error: function(e)
                {
                    alert(e.responseText || ('Ошибка ' + e.status));
                }
            });

        }

    }));

    $("#me").on('click', '.delete_prod',(function(e) {
        e.preventDefault();
        var r = confirm("Вы действительно хотите удалить товар?");
          if (r === true) {
            var prod_id = $(this).attr("dataId");
            $.ajax({
            url: "../modules/deleteProduct.php",
             type: "POST",
             data:  {prod_id: prod_id},
             beforeSend : function()
             {
              //alert('beforesend')
             },
             success: function(data)
                {
                    alert(data);
                },
               error: function(e)
                {
                alert(e.responseText || ('Ошибка ' + e.status));
                   }
                 });

          }
        $(this).DataTable().reload();
    }));

    $(".delete_filter").on('click',(function(e) {
        e.preventDefault();
        var r = confirm("Вы действительно хотите удалить фильтр?");
        if (r === true) {
        var filter_id = $(this).attr("dataId");
        $.ajax({
        url: "../modules/deleteFilter.php",
         type: "POST",
         data:  {filter_id: filter_id},
         beforeSend : function()
         {
          //alert('beforesend')
         },
         success: function(data)
            {
                alert(data);
                window.location.reload();
            },
           error: function(e)
            {
                alert(e.responseText || ('Ошибка ' + e.status));
                   }
                 });
        }
    }));

    /*$(".edit_prod").on('click',(function(e) {
        e.preventDefault();
        var r = confirm("Вы действительно хотите удалить товар?");
          if (r === true) {
            var prod_id = $(this).attr("dataId");
            $.ajax({
            url: "../modules/deleteProduct.php",
             type: "POST",
             data:  {prod_id: prod_id},
             beforeSend : function()
             {
              //alert('beforesend')
             },
             success: function(data)
                {
                    alert(data);
                    window.location.reload();
                },
               error: function(e)
                {
                alert(e.responseText || ('Ошибка ' + e.status));
                   }
                 });

          }

    }));*/

    // Редактирование фильтра — модалка #editFilterModal с мультиселектом категорий
    $(document).on('click', '.edit_filter', function () {
        var id = $(this).attr('data-id') || ($(this).attr('dataId') || '').split(':')[0];
        $.ajax({
            url: "/modules/admin/getFilter.php",
            type: "POST",
            data: { filter_id: id },
            success: function (resp) {
                var f = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                if (!f) { return; }
                $('#editFilter_id').val(f.id);
                $('#editFilter input[name=f_name]').val(f.f_name);
                $('#editFilter_trees option').prop('selected', false);
                (f.trees || []).forEach(function (t) {
                    $('#editFilter_trees option[value="' + t + '"]').prop('selected', true);
                });
                $('#editFilterModal').modal('show');
            },
            error: function (err) { alert(err.responseText || ('Ошибка ' + err.status)); }
        });
    });

    $(document).on('submit', '#editFilter', function (e) {
        e.preventDefault();
        $.ajax({
            url: "/modules/editFilter.php",
            type: "POST",
            data: $(this).serialize(),
            success: function (data) {
                alert(data);
                window.location.reload();
            },
            error: function (err) { alert(err.responseText || ('Ошибка ' + err.status)); }
        });
    });

    $('#enableDisableFileInput').change(function() {
        if(this.checked) {
            $(this).closest("#editProduct").find("input[type=file]").removeAttr('disabled');
        }else{
            $(this).closest("#editProduct").find("input[type=file]").attr('disabled', 'disabled');
        }
    });

    $("#saveCategorySettings").on('click',(function(e) {
        var treeId = $('#settings_tree_id').val();
        var uncheck = $('#uncheck_btn').is(':checked') ? 1 : 0;
        var onload = $('#onLoadcountable').is(':checked') ? 1 : 0;
        $.ajax({
            url: "/modules/admin/saveNodeSettings.php",
            type: "POST",
            data: { tree_id: treeId, uncheck_btn: uncheck, onLoadcountable: onload },
            success: function (resp) {
                var obj = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                if (obj.success) { $('#settingsCategoryModal').modal('hide'); }
                else { alert('Ошибка сохранения: ' + (obj.error || '')); }
            },
            error: function (err) { alert(err.responseText || ('Ошибка ' + err.status)); }
        });
    }));

    $("#addProduct").on('submit',(function(e) {
        e.preventDefault();
        //alert('befin');
        $.ajax({
               url: "../modules/addProduct.php",
                 type: "POST",
                 data:  new FormData(this),
                 contentType: false,
                       cache: false,
                 processData:false,
                 beforeSend : function()
                 {
                  //alert('beforesend')
                 },
                 success: function(data)
                    {
                  if(data=='Не все поля заполнены')
                  {
                   // invalid file format.
                   alert(data);
                  }
                  else if(data == 'Неверное расширение картинки')
                  {
                    alert(data);
                  }else if(data == 'Товар успешно добавлен в базу, связь добавлена'){
                      alert(data);
                     $("#addProduct")[0].reset();
                     window.location.reload();
                  }else{
                      alert(data);
                  }
                    },
                   error: function(e)
                    {
                        alert(e.responseText || ('Ошибка ' + e.status));
                    }
                  });
       }));

    $("#addSborka").on('submit',(function(e) {
        e.preventDefault();
        $.ajax({
            url: "../modules/addSborka.php",
            type: "POST",
            data:  new FormData(this),
            contentType: false,
            cache: false,
            processData:false,
            beforeSend : function()
            {
                //alert('beforesend')
            },
            success: function(data)
            {
                if(data=='Не все поля заполнены')
                {
                    // invalid file format.
                    alert(data);
                }
                else if(data == 'Неверное расширение картинки')
                {
                    alert(data);
                }else if(data == 'Сборка успешно добавлена в базу'){
                    alert(data);
                    window.location = '/admin/constructors';
                }else{
                    alert(data);
                }
            },
            error: function(e)
            {
                alert(e.responseText || ('Ошибка ' + e.status));
            }
        });
    }));

       $(".edit_prod").on('click',(function(e) {
           var id = $(this).closest('tr').find("td:eq(0)").text().trim();
           var oldImg = $(this).closest('tr').find("td:eq(4)").find("img").attr('src').trim();
           var name = $(this).closest('tr').find("td:eq(1)").text().trim();
           var description = $(this).closest('tr').find("td:eq(2)").text().trim();
           var price = $(this).closest('tr').find("td:eq(3)").text().trim();
           var filter = $(this).closest('tr').find("td:eq(5)").text().trim();
            $('#editProduct').find('input[name=name]').val(name);
            $('#editProduct').find('textarea[name=description]').val(description);
            $('#editProduct').find('input[name=price]').val(price);
            $('#editProduct').find('input[name=idTovara]').val(id);
            $('#editProduct').find('input[name=oldImg]').val(oldImg);
            var optionsList = $('#editProduct select option');
            for(var i=0;i<optionsList.length;i++){
                if(optionsList[i].text == filter){
                    $(optionsList[i]).attr('selected', 'selected');
                    return;
                }
            }
            //$('#editProduct').find('select').find('option:contains('+filter+')').attr('selected','selected');
            //$('#editProduct select').find('option').text('nvidia').attr('selected','selected');
       }));

    $(".edit_sborka").on('click',(function(e) {
        var id = $(this).closest('tr').find("td:eq(0)").text().trim();
        var title = $(this).closest('tr').find("td:eq(1)").text().trim();
        var link = $(this).closest('tr').find("td:eq(2)").find('a').attr('href');
        var price = $(this).closest('tr').find("td:eq(3)").text().trim();
        var description = $(this).closest('tr').find("td:eq(4)").text().trim();
        var oldImg = $(this).closest('tr').find("td:eq(5)").find("img").attr('src').trim();


        $('#editSborka').find('input[name=edit_title]').val(title);
        $('#editSborka').find('iframe[name=edit_frame]').attr('src', link);
        $('#editSborka').find('textarea[name=edit_description]').val(description);
        $('#editSborka').find('input[name=edit_price]').val(price);
        $('#editSborka').find('input[name=edit_link]').val(link);
        $('#editSborka').find('input[name=idSborki]').val(id);
        $('#editSborka').find('input[name=oldImg]').val(oldImg);
    }));

       $("#editProduct").on('submit',(function(e) {
        e.preventDefault();
        //alert('befin');
        $.ajax({
               url: "../modules/editProduct.php",
         type: "POST",
         data:  new FormData(this),
         contentType: false,
               cache: false,
         processData:false,
         beforeSend : function()
         {
          //alert('beforesend')
         },
         success: function(data)
            {
                alert(data);
                $("#editProduct")[0].reset();
                window.location.reload();
            },
           error: function(e)
            {
                alert(e.responseText || ('Ошибка ' + e.status));
            }
          });
       }));

    $("#editSborka").on('submit',(function(e) {
        e.preventDefault();


        $.ajax({
            url: "../modules/editSborka.php",
            type: "POST",
            data:  new FormData(this),
            contentType: false,
            cache: false,
            processData:false,
            beforeSend : function()
            {
                //alert('beforesend')
            },
            success: function(data)
            {
                alert(data);
                $("#editSborka")[0].reset();
                window.location.reload();
            },
            error: function(e)
            {
                alert(e.responseText || ('Ошибка ' + e.status));
            }
        });
    }));

       $("#addFilter").on('submit',(function(e) {
        e.preventDefault();
        //alert('befin');
        $.ajax({
               url: "../modules/addFilter.php",
         type: "POST",
         data:  new FormData(this),
         contentType: false,
               cache: false,
         processData:false,
         beforeSend : function()
         {
          //alert('beforesend')
         },
         success: function(data)
            {
                if(data == 'Фильтр успешно добавлен в базу'){
                    alert(data);
                    $("#addFilter")[0].reset();
                    window.location.reload();
                }else{
                   alert(data);
                }

            },
           error: function(e)
            {
                alert(e.responseText || ('Ошибка ' + e.status));
            }
          });
       }));




    $('#tree_table').DataTable({
        "lengthChange": true,
    });

    $('#filter_table').DataTable({
        "lengthChange": false,
    });

    $('#filter_tree_relation').DataTable({
        "lengthChange": false,
    });

    $('#prod_tree_relation').DataTable({
        "lengthChange": false,
    });

    $('#cat_prod_table').DataTable({
        "lengthChange": true,
    });

    $('#users').DataTable({
        "lengthChange": true,
    });

    $('#cat_filter_table').DataTable({
        "lengthChange": true,
    });

    $('#table_id').DataTable({
        'processing': true,
        'serverSide':true,
        'ajax':{
            url:'../modules/products/getProdDatatable.php',
            type:'post'
        }
    });

       /* ----- Товары выбранной ноды; для блока — сортировка и массовое удаление ----- */

       // Колонки #node_products_table
       var COL_CHECK = 0, COL_DRAG = 1, COL_ID = 2, COL_NAME = 3, COL_ORDER = 4;

       var block = {
           treeId: null,     // выбранная нода
           isBlock: false,   // нода — блок (тип товара, level 3)
           name: '',
           rows: [],         // товары в текущем (возможно, несохранённом) порядке
           selected: {},     // id -> true, переживает пагинацию
           dirty: false,     // есть несохранённый порядок
           busy: false       // идёт запрос — повторные клики игнорируем
       };
       var blockSortable = null;

       function escHtml(s) {
           return String(s === null || s === undefined ? '' : s)
               .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
               .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
       }

       function canEdit()   { return !!(window.CAN && window.CAN['products.edit']); }
       function canDelete() { return !!(window.CAN && window.CAN['products.delete']); }

       function setSortStatus(text, cls) {
           $('#blockSortStatus').attr('class', 'small mr-3 mb-1 ' + (cls || 'text-muted')).html(text);
       }

       function setDirty(dirty) {
           block.dirty = dirty;
           $('#applyBlockSort').prop('disabled', !dirty || block.busy);
           if (dirty) { setSortStatus('<i class="fas fa-circle text-warning"></i> Есть несохранённые изменения порядка', 'text-warning'); }
       }

       function updateBlockSelection() {
           var count = Object.keys(block.selected).length;
           $('#blockSelCount').text(count);
           $('#blockDeleteSelected').prop('disabled', count === 0 || block.busy);
       }

       function nodeTable() { return $('#node_products_table').DataTable(); }

       // Перетаскивать можно только в «естественном» виде: сортировка по «Порядку» ↑ и пустой поиск,
       // иначе порядок строк на экране не совпадает с порядком в блоке.
       function isNaturalOrder() {
           var t = nodeTable(), o = t.order();
           return o.length === 1 && o[0][0] === COL_ORDER && o[0][1] === 'asc' && t.search() === '';
       }

       function refreshDragState() {
           if (!blockSortable) { return; }
           var on = block.isBlock && canEdit() && isNaturalOrder() && !block.busy;
           blockSortable.option('disabled', !on);
           $('#node_products_table .block-drag').toggleClass('disabled', !on);
       }

       function restoreBlockChecks() {
           $('#node_products_table tbody .block-check').each(function () {
               this.checked = block.selected.hasOwnProperty($(this).attr('data-id'));
           });
           var $boxes = $('#node_products_table tbody .block-check');
           $('#block_select_all').prop('checked', $boxes.length > 0 && $boxes.filter(':checked').length === $boxes.length);
           updateBlockSelection();
           refreshDragState();
       }

       function renderNodeTable() {
           var isBlock = block.isBlock;
           $('#node_products_table').DataTable({
               destroy: true, paging: true, searching: true,
               data: block.rows,
               pageLength: isBlock ? 50 : 10,
               lengthMenu: [[10, 20, 50, 100, -1], [10, 20, 50, 100, 'Все']],
               order: [[isBlock ? COL_ORDER : COL_ID, 'asc']],
               columns: [
                   { data: null, orderable: false, searchable: false, visible: isBlock && canDelete(), render: function (row) {
                       return '<input type="checkbox" class="block-check" data-id="' + row.id + '" />';
                   }},
                   { data: null, orderable: false, searchable: false, visible: isBlock && canEdit(), render: function () {
                       return '<i class="fas fa-grip-vertical block-drag" title="Перетащить"></i>';
                   }},
                   { data: 'id' },
                   { data: 'name', render: function (d, type) { return type === 'display' ? escHtml(d) : d; } },
                   { data: 'sort_order', visible: isBlock, searchable: false, type: 'num', render: function (d, type, row) {
                       if (type !== 'display') { return parseInt(d, 10) || 0; }
                       if (!canEdit()) { return escHtml(d); }
                       return '<input type="number" min="1" step="1" class="form-control form-control-sm block-sort-input" data-id="' + row.id + '" value="' + escHtml(d) + '" />';
                   }},
                   { data: 'price' },
                   { data: 'f_name', render: function (d) { return d ? escHtml(d) : ''; } },
                   { data: null, orderable: false, searchable: false, render: function (row) {
                       return row.image
                           ? '<img src="/configurator/images/products/' + escHtml(row.image) + '" class="zoomable-img" style="height:50px;cursor:pointer;" />'
                           : '';
                   }},
                   { data: null, orderable: false, searchable: false, visible: canEdit(), render: function (row) {
                       return '<button type="button" class="btn btn-sm btn-info edit_prod_cat" data-id="' + row.id + '">Изменить</button>';
                   }},
                   { data: null, orderable: false, searchable: false, visible: canDelete(), render: function (row) {
                       return '<button type="button" class="btn btn-sm btn-danger delete_prod_cat" data-id="' + row.id + '" data-img="' + escHtml(row.image || '') + '">Удалить</button>';
                   }}
               ],
               rowCallback: function (tr, row) { $(tr).attr('data-id', row.id); }
           });

           if (!blockSortable && window.Sortable) {
               blockSortable = Sortable.create($('#node_products_table tbody')[0], {
                   handle: '.block-drag',
                   animation: 150,
                   onEnd: onBlockDragEnd
               });
           }
           restoreBlockChecks();
       }

       // Применяем ответ сервера со списком товаров
       function applyNodeData(obj) {
           block.rows = obj.products || [];
           block.isBlock = !!obj.block;
           $('#node_prod_count').text(obj.count);
           $('#blockToolbar').toggle(block.isBlock);
           // Выбор держим только по товарам, которые ещё есть в списке
           var ids = {};
           block.rows.forEach(function (r) { ids[r.id] = true; });
           Object.keys(block.selected).forEach(function (id) { if (!ids[id]) { delete block.selected[id]; } });
           setDirty(false);
           renderNodeTable();
       }

       function loadNodeProducts(treeId) {
           $.ajax({
               url: "/modules/admin/getProductsByNode.php",
               type: "POST",
               data: { tree_id: treeId },
               success: function (resp) {
                   var obj = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                   applyNodeData(obj);
               },
               error: function (err) { alert(err.responseText || ('Ошибка ' + err.status)); }
           });
       }

       $('#tree').on("select_node.jstree", function (e, data) {
           if (String(data.node.id) === String(block.treeId)) { return; }
           if (block.dirty && !confirm('Порядок в блоке «' + block.name + '» не сохранён. Перейти без сохранения?')) {
               data.instance.deselect_node(data.node, true);
               data.instance.select_node(block.treeId, true);
               return;
           }
           block.treeId = data.node.id;
           block.name = data.node.text;
           block.selected = {};
           $('#blockName').text(block.name);
           $('#blockResult').empty();
           setSortStatus('');
           loadNodeProducts(data.node.id);
       });

       // Перетаскивание: переставили строки на текущей странице — переносим это
       // в общий список блока и перенумеровываем 1..N.
       function onBlockDragEnd(evt) {
           if (evt.oldIndex === evt.newIndex) { return; }
           var t = nodeTable(), start = t.page.info().start;
           var pageIds = $('#node_products_table tbody tr').map(function () { return $(this).attr('data-id'); }).get();

           var ordered = block.rows.slice().sort(function (a, b) { return a.sort_order - b.sort_order; });
           var byId = {};
           ordered.forEach(function (r) { byId[r.id] = r; });
           var pageRows = pageIds.map(function (id) { return byId[id]; });
           Array.prototype.splice.apply(ordered, [start, pageRows.length].concat(pageRows));
           ordered.forEach(function (r, i) { r.sort_order = i + 1; });

           block.rows = ordered;
           t.clear().rows.add(block.rows).draw(false);
           setDirty(true);
       }

       $(document).on('change', '.block-sort-input', function () {
           var v = $(this).val().trim(), id = $(this).attr('data-id');
           if (!/^[1-9]\d*$/.test(v)) {
               $(this).addClass('is-invalid');
               setSortStatus('Порядок — целое положительное число', 'text-danger');
               return;
           }
           $(this).removeClass('is-invalid');
           block.rows.forEach(function (r) { if (String(r.id) === id) { r.sort_order = parseInt(v, 10); } });
           setDirty(true);
       });

       $('#node_products_table').on('draw.dt', restoreBlockChecks);

       function sendBlockSort(data) {
           if (block.busy) { return; }
           block.busy = true;
           $('#applyBlockSort, #blockSortResetBtn').prop('disabled', true);
           refreshDragState();
           setSortStatus('<i class="fas fa-spinner fa-spin"></i> Сохранение…');
           data.tree_id = block.treeId;
           $.ajax({
               url: "/modules/admin/sortBlockProducts.php",
               type: "POST",
               data: data,
               success: function (resp) {
                   var r = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                   block.busy = false;
                   applyNodeData({ block: true, count: r.count, products: r.products });
                   setSortStatus('<i class="fas fa-check text-success"></i> Сортировка сохранена', 'text-success');
               },
               error: function (err) {
                   block.busy = false;
                   var r = err.responseJSON || {};
                   setDirty(block.dirty);
                   setSortStatus('<i class="fas fa-exclamation-triangle"></i> ' + escHtml(r.error || ('Ошибка ' + err.status)), 'text-danger');
               },
               complete: function () {
                   $('#blockSortResetBtn').prop('disabled', false);
                   refreshDragState();
               }
           });
       }

       $(document).on('click', '#applyBlockSort', function () {
           if ($('#node_products_table .block-sort-input.is-invalid').length) {
               setSortStatus('Исправьте некорректные номера порядка', 'text-danger');
               return;
           }
           var items = block.rows.map(function (r) { return { item_id: r.id, sort_order: parseInt(r.sort_order, 10) }; });
           sendBlockSort({ items: JSON.stringify(items) });
       });

       $(document).on('click', '#blockSortResetBtn', function () {
           var $opt = $('#blockSortReset option:selected');
           if (!confirm('Сбросить порядок блока «' + block.name + '» (' + $opt.text().toLowerCase() + ')? Ручная сортировка будет потеряна.')) { return; }
           sendBlockSort({ reset: $opt.val() });
       });

       /* Выбор товаров блока */
       $(document).on('change', '.block-check', function () {
           var id = $(this).attr('data-id');
           if (this.checked) { block.selected[id] = true; } else { delete block.selected[id]; }
           restoreBlockChecks();
       });

       // «Выбрать все» — все строки, подходящие под текущий поиск (на всех страницах)
       $(document).on('change', '#block_select_all', function () {
           var checked = this.checked;
           nodeTable().rows({ search: 'applied' }).data().each(function (row) {
               if (checked) { block.selected[row.id] = true; } else { delete block.selected[row.id]; }
           });
           restoreBlockChecks();
       });

       function openBlockDeleteModal(mode) {
           var count = mode === 'all' ? block.rows.length : Object.keys(block.selected).length;
           if (count === 0) { return; }
           $('#blockBulkDeleteMode').val(mode);
           $('#blockBulkDeleteCount').text(count);
           $('#blockBulkDeleteModalTitle').text(mode === 'all' ? 'Удалить все товары блока «' + block.name + '»' : 'Удалить выбранные товары');
           $('#blockBulkDeletePhrase').val('');
           $('#blockBulkDeleteAllConfirm').toggle(mode === 'all');
           $('#blockBulkUnlinkShared').prop('checked', false);
           $('#blockBulkDeleteModal').modal('show');
       }

       $(document).on('click', '#blockDeleteSelected', function () { openBlockDeleteModal('selected'); });
       $(document).on('click', '#blockDeleteAll', function () { openBlockDeleteModal('all'); });

       function renderBlockDeleteResult(r) {
           var deleted = (r.deleted || []).length, unlinked = (r.unlinked || []).length, skipped = r.skipped || [];
           var html = 'Удалено ' + deleted + ' элементов.';
           if (unlinked) { html += ' Убрано из блока (остались в других блоках): ' + unlinked + '.'; }
           if (skipped.length) {
               html += ' ' + skipped.length + ' элементов не удалены:<ul class="mb-0 mt-1">';
               skipped.forEach(function (s) {
                   html += '<li>' + escHtml(s.name ? s.name + ' (id ' + s.id + ')' : 'id ' + s.id) + ' — ' + escHtml(s.reason) + '</li>';
               });
               html += '</ul>';
           }
           $('#blockResult').html(
               '<div class="alert alert-' + (skipped.length ? 'warning' : 'success') + ' alert-dismissible mb-0">' + html +
               '<button type="button" class="close" data-dismiss="alert">&times;</button></div>'
           );
       }

       $(document).on('click', '#blockBulkDeleteConfirm', function () {
           if (block.busy) { return; }
           var mode = $('#blockBulkDeleteMode').val();
           var data = {
               tree_id: block.treeId,
               mode: mode,
               unlink_shared: $('#blockBulkUnlinkShared').is(':checked') ? 1 : 0
           };
           if (mode === 'all') {
               data.confirm = $('#blockBulkDeletePhrase').val().trim();
               if (data.confirm.toLowerCase() !== 'удалить все') {
                   alert('Подтверждение не совпадает. Удаление отменено.');
                   return;
               }
           } else {
               data.item_ids = JSON.stringify(Object.keys(block.selected).map(Number));
           }
           if (block.dirty && !confirm('Несохранённый порядок будет потерян. Продолжить удаление?')) { return; }

           var $btn = $(this);
           block.busy = true;
           $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Удаление…');
           $.ajax({
               url: "/modules/admin/deleteBlockProducts.php",
               type: "POST",
               data: data,
               success: function (resp) {
                   var r = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                   $('#blockBulkDeleteModal').modal('hide');
                   block.selected = {};
                   block.busy = false;
                   applyNodeData({ block: true, count: r.count, products: r.products });
                   renderBlockDeleteResult(r);
               },
               error: function (err) {
                   var r = err.responseJSON || {};
                   alert(r.error || err.responseText || ('Ошибка ' + err.status));
               },
               complete: function () {
                   block.busy = false;
                   $btn.prop('disabled', false).text('Удалить');
                   updateBlockSelection();
               }
           });
       });

       // Редактирование товара из списка категорий / отчёта — модалка #exampleModalEditProd
       $(document).on('click', '.edit_prod_cat', function () {
           var id = $(this).attr('data-id');
           $.ajax({
               url: "/modules/admin/getProduct.php",
               type: "POST",
               data: { prod_id: id },
               success: function (resp) {
                   var p = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                   if (!p) { return; }
                   $('#editProduct').find('input[name=name]').val(p.name);
                   $('#editProduct').find('textarea[name=description]').val(p.description);
                   $('#editProduct').find('input[name=price]').val(p.price);
                   $('#editProduct').find('input[name=idTovara]').val(p.id);
                   $('#editProduct').find('input[name=oldImg]').val(p.image ? 'configurator/images/products/' + p.image : '');
                   $('#editProduct select[name=filter]').val(p.f_id);
                   $('#editProduct_trees option').prop('selected', false);
                   (p.trees || []).forEach(function (t) {
                       $('#editProduct_trees option[value="' + t + '"]').prop('selected', true);
                   });
                   $('#exampleModalEditProd').modal('show');
               },
               error: function (err) { alert(err.responseText || ('Ошибка ' + err.status)); }
           });
       });

       // Удаление товара из списка категорий / отчёта
       $(document).on('click', '.delete_prod_cat', function () {
           if (!confirm('Вы действительно хотите удалить товар?')) { return; }
           var id = $(this).attr('data-id');
           var img = $(this).attr('data-img') || '';
           $.ajax({
               url: "/modules/deleteProduct.php",
               type: "POST",
               data: { prod_id: id + ':' + img },
               success: function () {
                   var sel = $('#tree').length ? $('#tree').jstree(true).get_selected() : [];
                   if (sel && sel.length) { loadNodeProducts(sel[0]); }
                   else { window.location.reload(); }
               },
               error: function (err) { alert(err.responseText || ('Ошибка ' + err.status)); }
           });
       });

       // Просмотр картинки товара в увеличенном виде
       $(document).on('click', '.zoomable-img', function () {
           var src = $(this).attr('src');
           if (!src) { return; }
           $('#imagePreviewImg').attr('src', src);
           $('#imagePreviewModal').modal('show');
       });


       function customMenu(node) {
            // Нет права управлять деревом категорий — контекстного меню нет.
            if (!(window.CAN && window.CAN['categories.manage'])) {
                return {};
            }
            // The default set of all items
            var items = {
                createItem: { // The "create" menu item
                    label: "Создать",
                    icon: "../images/add-icon.png",
                    action: function (data) {
                        if ( node.parents.length < 4 ) {
                            //alert(node.parents.length);
                            var name = prompt("Введите название узла: ");
                            if(!!name){
                                $.ajax({
                                url: "../modules/insertNode.php",
                                 type: "POST",
                                 data:  {node_id: node.id, upcomingNodeLevel: node.parents.length, nodeName: name},
                                 beforeSend : function()
                                 {
                                  //alert('beforesend')
                                 },
                                 success: function(data)
                                    {
                                        //alert(data);
                                        $('#tree').jstree(true).refresh();
                                        //notifSet ();
                                    },
                                   error: function(e)
                                    {
                                        alert(e.responseText || ('Ошибка ' + e.status));
                                       }
                                    });
                            }
                        }else{
                            alert('В этот узел нельзя ничего добавлять');
                        }
                    }
                },
                renameItem: {
                    label: "Переименовать",
                    icon: "../images/textfield-rename-icon.png",
                    action: function (data) {
                        if ( node.parents.length < 5 && node.parents.length > 1 ) {
                            var name = prompt("Переименовать на: ", node.text);
                            if(!!name){
                                $.ajax({
                                url: "../modules/renameNode.php",
                                 type: "POST",
                                 data:  {node_id: node.id, nodeName: name},
                                 beforeSend : function()
                                 {
                                  //alert('beforesend')
                                 },
                                 success: function(data)
                                    {
                                        //alert(data);
                                        $('#tree').jstree(true).refresh();
                                    },
                                   error: function(e)
                                    {
                                        alert(e.responseText || ('Ошибка ' + e.status));
                                       }
                                    });
                            }
                        }else{
                            alert('Этот объект запрещен для редактирования');
                        }

                    }
                },
                deleteItem: { // The "delete" menu item
                    label: "Удалить",
                    icon: "../images/Actions-edit-delete-icon.png",
                    action: function (data) {
                        var inst = $.jstree.reference(data.reference), obj = inst.get_node(data.reference);
                        if ( node.parents.length < 5 && node.parents.length > 1 ) {
                            var isAdmin = confirm("Вы действительно хотите удалить этот узел? Внимание! Узлы ниже стоящего будут также удалены");
                            if(isAdmin){
                                $.ajax({
                                url: "../modules/deleteNodes.php",
                                 type: "POST",
                                 data:  {node_id: node.id},
                                 beforeSend : function()
                                 {
                                  //alert('beforesend')
                                 },
                                 success: function(data)
                                    {
                                        //alert('Удаление прошло успешно');
                                        $('#tree').jstree(true).refresh();

                                    },
                                   error: function(e)
                                    {
                                        alert(e.responseText || ('Ошибка ' + e.status));
                                       }
                                    });
                            }
                        }else{
                            alert('Этот объект запрещен для удаления');
                        }
                        //console.log(inst);
                        //console.log(obj);

                    }
                }
            };

            /*var inst = $.jstree.reference(data.reference),
                            obj = inst.get_node(data.reference);
                        if(inst.is_selected(obj)) {
                            var isAdmin = confirm("Вы - администратор?");
                            if(isAdmin){
                                alert(node.id);
                            }

                            //inst.delete_node(inst.get_selected());
                        }
                        else {
                            console.log(obj.id);
                            //inst.delete_node(obj);
                        }*/

            return items;
        }

                $('#tree').jstree({
                    'core' : {
                        'check_callback': true,
                        "themes" : { "stripes" : true },
                        'data' : {
                            'url' : '../modules/tree.php',
                            'dataType' : 'json' // needed only if you do not supply JSON headers
                        }
                    },
                    "plugins": (window.CAN && window.CAN['categories.manage'])
                        ? [ "contextmenu", "unique", "dnd" ]
                        : [ "unique" ],
                    "contextmenu": {items: customMenu}
                }).on("select_node.jstree", function (e, data) {

                });

                // Все ноды раскрыты по умолчанию
                $('#tree').on('ready.jstree refresh.jstree', function () {
                    $('#tree').jstree('open_all');
                });

                // Без права управления деревом плагина contextmenu нет, поэтому
                // правый клик показал бы нативное меню браузера — подавляем его.
                // (У менеджера нативное меню перехватывает сам jstree.)
                $('#tree').on('contextmenu', function (e) {
                    if (!(window.CAN && window.CAN['categories.manage'])) {
                        e.preventDefault();
                    }
                });

                // Двойной клик по тип-ноде (level=3) -> модалка настроек с актуальными значениями
                $('#tree').on('dblclick.jstree', '.jstree-anchor', function () {
                    // Настройки ноды — тоже управление деревом.
                    if (!(window.CAN && window.CAN['categories.manage'])) { return; }
                    var node = $('#tree').jstree(true).get_node(this);
                    // level-3 нода: предки [тип-родитель, группа, сборка, '#'] => length === 4
                    if (node && node.parents && node.parents.length === 4) {
                        $.ajax({
                            url: "/modules/admin/getNodeSettings.php",
                            type: "POST",
                            data: { tree_id: node.id },
                            success: function (resp) {
                                var s = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                                if (!s) { return; }
                                $('#settings_tree_id').val(s.id);
                                $('#settingsCategoryModalTitle').text('Настройки: ' + s.name);
                                $('#uncheck_btn').prop('checked', parseInt(s.uncheck_btn) === 1);
                                $('#onLoadcountable').prop('checked', parseInt(s.onLoadcountable) === 1);
                                $('#settingsCategoryModal').modal('show');
                            },
                            error: function (err) { alert(err.responseText || ('Ошибка ' + err.status)); }
                        });
                    }
                });
                    /*.bind("move_node.jstree", function(e, data) {
                        alert(data.node.id);
                        alert(data.parent);
                        alert(data.old_parent);
                        alert(data.position);
                    })*/

                /*.bind("dblclick.jstree", function(e) {

                    var data= $('#tree').jstree().get_selected(true);
                    alert(data[0].id + data.node.parents.length);
                });*/




    // ===== Страница "Товары без категории": DataTable + массовое удаление =====
    $('#no_cat_table').DataTable({
        "lengthChange": true,
        "columnDefs": [
            { "orderable": false, "targets": [0, 5, 6, 7] },
            { "visible": !!(window.CAN && window.CAN['products.edit']),   "targets": 6 },
            { "visible": !!(window.CAN && window.CAN['products.delete']), "targets": 7 }
        ]
    });

    // Выбор сохраняется между страницами DataTables (ключ — id товара)
    var noCatSelected = {}; // id -> image

    function updateNoCatToolbar() {
        var count = Object.keys(noCatSelected).length;
        $('#noCatSelCount').text(count);
        if (count > 0) { $('#deleteSelectedNoCat, #deleteAllNoCat').show(); }
        else { $('#deleteSelectedNoCat, #deleteAllNoCat').hide(); }
    }

    function updateNoCatSelectAllState() {
        var $boxes = $('#no_cat_table tbody .no-cat-check');
        var allChecked = $boxes.length > 0 && $boxes.filter(':checked').length === $boxes.length;
        $('#no_cat_select_all').prop('checked', allChecked);
    }

    function restoreNoCatChecks() {
        $('#no_cat_table tbody .no-cat-check').each(function () {
            this.checked = noCatSelected.hasOwnProperty($(this).attr('data-id'));
        });
        updateNoCatSelectAllState();
        updateNoCatToolbar();
    }
    $('#no_cat_table').on('draw.dt', restoreNoCatChecks);

    $(document).on('change', '.no-cat-check', function () {
        var id = $(this).attr('data-id');
        var img = $(this).attr('data-img') || '';
        if (this.checked) { noCatSelected[id] = img; } else { delete noCatSelected[id]; }
        updateNoCatSelectAllState();
        updateNoCatToolbar();
    });

    $(document).on('change', '#no_cat_select_all', function () {
        var checked = this.checked;
        $('#no_cat_table tbody .no-cat-check').each(function () {
            this.checked = checked;
            var id = $(this).attr('data-id');
            var img = $(this).attr('data-img') || '';
            if (checked) { noCatSelected[id] = img; } else { delete noCatSelected[id]; }
        });
        updateNoCatToolbar();
    });

    $(document).on('click', '#deleteSelectedNoCat', function () {
        var items = [];
        for (var id in noCatSelected) {
            if (noCatSelected.hasOwnProperty(id)) {
                items.push(id + ':' + (noCatSelected[id] || ''));
            }
        }
        if (items.length === 0) { return; }
        if (!confirm('Удалить выбранные товары (' + items.length + ' шт.)? Действие необратимо.')) { return; }
        $.ajax({
            url: "/modules/deleteProductsBulk.php",
            type: "POST",
            data: { items: items },
            success: function (resp) {
                var r = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                alert('Удалено товаров: ' + (r.deleted || 0));
                window.location.reload();
            },
            error: function (err) { alert(err.responseText || ('Ошибка ' + err.status)); }
        });
    });

    $(document).on('click', '#deleteAllNoCat', function () {
        var phrase = prompt('Это удалит ВСЕ товары без категории безвозвратно.\nЧтобы подтвердить, введите: удалить все');
        if (phrase === null) { return; }
        if (phrase.trim().toLowerCase() !== 'удалить все') {
            alert('Подтверждение не совпадает. Удаление отменено.');
            return;
        }
        $.ajax({
            url: "/modules/deleteAllNoCategory.php",
            type: "POST",
            data: { confirm: phrase },
            success: function (resp) {
                var r = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                if (r.success) {
                    alert('Удалено товаров без категории: ' + (r.deleted || 0));
                    window.location.reload();
                } else {
                    alert('Ошибка: ' + (r.error || 'не удалось удалить'));
                }
            },
            error: function (err) { alert(err.responseText || ('Ошибка ' + err.status)); }
        });
    });

    /* ----- Управление пользователями (RBAC) ----------------------- */

    // Открытие модалки на создание — сброс формы.
    $(document).on('click', '#addUserBtn', function () {
        var $f = $('#userForm');
        $f[0].reset();
        $('#userId').val(0);
        $('#userModalLabel').text('Новый пользователь');
        $('#userPasswordHint').text('Обязательно для нового пользователя.');
        $('.user-role-cb').prop('checked', false);
        $('#userIsActive').prop('checked', true);
    });

    // Редактирование — подтягиваем данные и открываем модалку.
    $(document).on('click', '.edit_user', function () {
        var id = $(this).attr('data-id');
        $.ajax({
            url: "/modules/admin/getUser.php",
            type: "POST",
            dataType: "json",
            data: { id: id },
            success: function (r) {
                if (!r || !r.success) {
                    alert(r && r.error ? r.error : 'Не удалось загрузить пользователя');
                    return;
                }
                $('#userForm')[0].reset();
                $('#userId').val(r.id);
                $('#userUsername').val(r.username);
                $('#userPassword').val('');
                $('#userModalLabel').text('Редактирование пользователя');
                $('#userPasswordHint').text('Оставьте пустым, чтобы не менять пароль.');
                $('.user-role-cb').prop('checked', false);
                $.each(r.roles || [], function (i, roleId) {
                    $('#role_' + roleId).prop('checked', true);
                });
                $('#userIsActive').prop('checked', parseInt(r.is_active, 10) === 1);
                $('#userModal').modal('show');
            },
            error: function (err) { alert(err.responseText || ('Ошибка ' + err.status)); }
        });
    });

    // Сохранение (создание/обновление).
    $(document).on('submit', '#userForm', function (e) {
        e.preventDefault();
        $.ajax({
            url: "/modules/admin/saveUser.php",
            type: "POST",
            dataType: "json",
            data: $(this).serialize(),
            success: function (r) {
                if (r && r.success) {
                    window.location.reload();
                } else {
                    alert(r && r.error ? r.error : 'Не удалось сохранить пользователя');
                }
            },
            error: function (err) { alert(err.responseText || ('Ошибка ' + err.status)); }
        });
    });

    // Удаление.
    $(document).on('click', '.delete_user', function () {
        var id = $(this).attr('data-id');
        var username = $(this).attr('data-username') || '';
        if (!confirm('Удалить пользователя «' + username + '»? Действие необратимо.')) { return; }
        $.ajax({
            url: "/modules/admin/deleteUser.php",
            type: "POST",
            dataType: "json",
            data: { id: id },
            success: function (r) {
                if (r && r.success) {
                    window.location.reload();
                } else {
                    alert(r && r.error ? r.error : 'Не удалось удалить пользователя');
                }
            },
            error: function (err) { alert(err.responseText || ('Ошибка ' + err.status)); }
        });
    });

});
