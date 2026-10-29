$(document).ready(function() {
    showGraph();
    $('body .preloader2').fadeOut('slow', function () {
            });
    /*setTimeout(function(){
            $('body .preloader2').fadeOut('slow', function () {
            });
        },10000);  */

    $('#tree_table_filter').css('float', 'right');

    $(".delete_relation_filter_tree").on('click',(function(e) {
        e.preventDefault();
        var r = confirm("Вы действительно хотите удалить эту связь?");
          if (r === true) {
            var relation_id = $(this).attr("dataId");
            var a = $(this).closest('tr').attr('id', relation_id);
            $.ajax({
            url: "../modules/deleteRelationTreeFilter.php",
             type: "POST",
             data:  {relation_id: relation_id},
             beforeSend : function()
             {
              //alert('beforesend')
             },
             success: function(data)
                {
                    alert(data);
                    a.css('display', 'none');
                    window.location.reload();

                },
               error: function(e)
                {
                alert(e.toString());
                   }
                 });

          }

    }));

    $(".delete_relation_prod_tree").on('click',(function(e) {
        e.preventDefault();
        var r = confirm("Вы действительно хотите удалить эту связь?");
          if (r === true) {
            var relation_id = $(this).attr("dataId");
            var a = $(this).closest('tr').attr('id', relation_id);
            $.ajax({
            url: "../modules/deleteRelationTreeProduct.php",
             type: "POST",
             data:  {relation_id: relation_id},
             beforeSend : function()
             {
              //alert('beforesend')
             },
             success: function(data)
                {
                    alert(data);
                    a.css('display', 'none');
                    window.location.reload();

                },
               error: function(e)
                {
                alert(e.toString());
                   }
                 });

          }

    }));

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
                alert(e.toString());
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
                    alert(e.toString());
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
                alert(e.toString());
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
                alert(e.toString());
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
                alert(e.toString());
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
            error: function (err) { alert(err.toString()); }
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
            error: function (err) { alert(err.toString()); }
        });
    });

    $('select').on('change',(function(e) {
        var type = $(this).attr('id');
        if(type === "sborka2"){
            //alert($("#sborka2").val());
            var filter_id = $("#sborka2").val();
            if(filter_id === '0'){
                $('#tip_tovara2').find('option').remove();
                $('#tip_tovara2').append('<option>Выберите тип товара</option>');
            }else{
                $.ajax({
               url: "../modules/selectProductType.php",
                type: "POST",
                data:  {filter: filter_id},
                beforeSend : function()
                {
                 //alert('beforesend')
                },
                success: function(data)
                   {
                       var myObj = $.parseJSON(data);
                       $('#tip_tovara2').find('option').remove();
                       $('#tip_tovara2').append('<option value="0">Выберите тип товара</option>');
                       for (var i=0; i<myObj.length; i++) {
                            var tr_str = `<option value='` + myObj[i]['id'] + `'>` + myObj[i]['name']  + `</option>`;
                            $('#tip_tovara2').append(tr_str);
                        }
                   },
                  error: function(e)
                   {
                       alert(e.toString());
                   }
                 });
            }

        }else if(type === "sborka1"){
            //alert($("#sborka1").val());
            var filter_id = $("#sborka1").val();
            if(filter_id === '0'){
                $('#tip_tovara1').find('option').remove();
                $('#tip_tovara1').append('<option value="0">Выберите тип товара</option>');
            }else{
                $.ajax({
               url: "../modules/selectProductType.php",
                type: "POST",
                data:  {filter: filter_id},
                beforeSend : function()
                {
                 //alert('beforesend')
                },
                success: function(data)
                   {
                       var myObj = $.parseJSON(data);
                       $('#tip_tovara1').find('option').remove();
                       $('#tip_tovara1').append('<option>Выберите тип товара</option>');
                       for (var i=0; i<myObj.length; i++) {
                            var tr_str = `<option value='` + myObj[i]['id'] + `'>` + myObj[i]['name']  + `</option>`;
                            $('#tip_tovara1').append(tr_str);
                        }
                   },
                  error: function(e)
                   {
                       alert(e.toString());
                   }
                 });
            }

        }
    }));


    $('#enableDisableFileInput').change(function() {
        if(this.checked) {
            $(this).closest("#editProduct").find("input[type=file]").removeAttr('disabled');
        }else{
            $(this).closest("#editProduct").find("input[type=file]").attr('disabled', 'disabled');
        }
    });

    $("#tip_tovara2_tovar2").on('click',(function(e) {
        e.preventDefault();
        var tree_id = $('#tip_tovara2').val();
        //var prod_id = $('#tovar2').val();
        var prod_id_input = $('#prod_id_input').val();
        //alert(prod_id_input + tree_id);
        var prod_id = $('option[value="'+prod_id_input+'"]').attr('id');
        //alert(prod_id);
        $.ajax({
        url: "../modules/addRelationProductTree.php",
         type: "POST",
         data:  {tree_id: tree_id, prod_id: prod_id},
         beforeSend : function()
         {
          //alert(tree_id + " " + prod_id)
         },
         success: function(data)
            {
                alert(data);
            },
           error: function(e)
            {
                alert(e.toString());
                   }
                 });
    }));

    $("#saveCategorySettings").on('click',(function(e) {
        var treeId = $('#settings_tree_id').val();
        var uncheck = $('#uncheck_btn').is(':checked') ? 1 : 0;
        var onload = $('#onLoadcountable').is(':checked') ? 1 : 0;
        $.ajax({
            url: "/modules/admin/saveNodeSettings.php",
            type: "POST",
            data: { tree_id: treeId, uncheck_btn: uncheck, onLoadcountable: onload },
            success: function(resp) {
                var obj = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                if (obj.success) {
                    $('#settingsCategoryModal').modal('hide');
                } else {
                    alert('Ошибка сохранения: ' + (obj.error || ''));
                }
            },
            error: function(err) { alert(err.toString()); }
        });
    }));

    $("#tip_tovara1_filter1").on('click',(function(e) {
        e.preventDefault();
        var tree_id = $('#tip_tovara1').val();
        var filter_id = $('#filter1').val();
        //alert(tree_id + filter_id);
        $.ajax({
        url: "../modules/addRelationFilterTree.php",
         type: "POST",
         data:  {tree_id: tree_id, filter_id: filter_id},
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
                alert(e.toString());
                   }
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
                        alert(e.toString());
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
                    window.location = '/admin.php?view=constructors';
                }else{
                    alert(data);
                }
            },
            error: function(e)
            {
                alert(e.toString());
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

            // Проставляем фильтр — только в селекте фильтра
            var optionsList = $('#editProduct select[name=filter] option');
            for(var i=0;i<optionsList.length;i++){
                $(optionsList[i]).prop('selected', optionsList[i].text == filter);
            }

            // Подгружаем текущие сборки/типы товара в мультиселект
            $('#editProduct_trees option').prop('selected', false);
            $.ajax({
                url: "../modules/getProductTrees.php",
                type: "POST",
                data: { prod_id: id },
                success: function(data){
                    var ids = (typeof data === 'string') ? JSON.parse(data) : data;
                    $('#editProduct_trees option').prop('selected', false);
                    for(var k=0;k<ids.length;k++){
                        $('#editProduct_trees option[value="'+ids[k]+'"]').prop('selected', true);
                    }
                },
                error: function(e){ alert(e.toString()); }
            });
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
                alert(e.toString());
            }
          });
       }));

    $("#editSborka").on('submit',(function(e) {
        e.preventDefault();
        alert('befin');
        console.log(new FormData(this));
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
                alert(e.toString());
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
                alert(e.toString());
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

    $('#no_cat_table').DataTable({
        "lengthChange": true,
        "columnDefs": [{ "orderable": false, "targets": [0, 6, 7] }]
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

    $( "#leftPanel" ).click(function() {
        if($('nav.col-md-2').hasClass('remove')){
                       $('nav.col-md-2').removeClass('remove');
               }else{
                       $('nav.col-md-2').addClass('remove');
               }

       });

       function loadNodeProducts(treeId) {
           $.ajax({
               url: "/modules/admin/getProductsByNode.php",
               type: "POST",
               data: { tree_id: treeId },
               success: function (resp) {
                   var obj = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                   $('#node_prod_count').text(obj.count);
                   $('#node_products_table').DataTable({
                       destroy: true,
                       paging: true,
                       searching: true,
                       data: obj.products,
                       columns: [
                           { data: 'id' },
                           { data: 'name' },
                           { data: 'price' },
                           { data: 'f_name', render: function (d) { return d ? d : ''; } },
                           { data: null, orderable: false, searchable: false, render: function (row) {
                               return row.image
                                   ? '<img src="/configurator/images/products/' + row.image + '" class="zoomable-img" style="height:50px;cursor:pointer;" />'
                                   : '';
                           }},
                           { data: null, orderable: false, searchable: false, render: function (row) {
                               return '<button type="button" class="btn btn-sm btn-info edit_prod_cat" data-id="' + row.id + '">Изменить</button>';
                           }},
                           { data: null, orderable: false, searchable: false, render: function (row) {
                               return '<button type="button" class="btn btn-sm btn-danger delete_prod_cat" data-id="' + row.id + '" data-img="' + (row.image || '') + '">Удалить</button>';
                           }}
                       ]
                   });
               },
               error: function (err) { alert(err.toString()); }
           });
       }

       $('#tree').on("select_node.jstree", function (e, data) {
           loadNodeProducts(data.node.id);
       });

       // Редактирование товара из списка категорий / отчёта — та же модалка, что и на странице товаров
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
               error: function (err) { alert(err.toString()); }
           });
       });

       // Просмотр картинки товара в увеличенном виде
       $(document).on('click', '.zoomable-img', function () {
           var src = $(this).attr('src');
           if (!src) { return; }
           $('#imagePreviewImg').attr('src', src);
           $('#imagePreviewModal').modal('show');
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
                   if (sel && sel.length) {
                       loadNodeProducts(sel[0]);
                   } else {
                       window.location.reload();
                   }
               },
               error: function (err) { alert(err.toString()); }
           });
       });

       // ===== Массовые операции на странице "Товары без категории" =====
       // Выбор сохраняется между страницами DataTables (ключ — id товара)
       var noCatSelected = {}; // id -> image

       function updateNoCatToolbar() {
           var count = Object.keys(noCatSelected).length;
           $('#noCatSelCount').text(count);
           if (count > 0) {
               $('#deleteSelectedNoCat, #deleteAllNoCat').show();
           } else {
               $('#deleteSelectedNoCat, #deleteAllNoCat').hide();
           }
       }

       function updateNoCatSelectAllState() {
           var $boxes = $('#no_cat_table tbody .no-cat-check');
           var allChecked = $boxes.length > 0 && $boxes.filter(':checked').length === $boxes.length;
           $('#no_cat_select_all').prop('checked', allChecked);
       }

       // При перерисовке таблицы (смена страницы/поиск) восстанавливаем галочки из сохранённого выбора
       function restoreNoCatChecks() {
           $('#no_cat_table tbody .no-cat-check').each(function () {
               this.checked = noCatSelected.hasOwnProperty($(this).attr('data-id'));
           });
           updateNoCatSelectAllState();
           updateNoCatToolbar();
       }
       $('#no_cat_table').on('draw.dt', restoreNoCatChecks);

       // Отметка/снятие одного товара
       $(document).on('change', '.no-cat-check', function () {
           var id = $(this).attr('data-id');
           var img = $(this).attr('data-img') || '';
           if (this.checked) { noCatSelected[id] = img; } else { delete noCatSelected[id]; }
           updateNoCatSelectAllState();
           updateNoCatToolbar();
       });

       // Выбрать все на ТЕКУЩЕЙ странице (добавляется к ранее выбранным на других страницах)
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

       // Удалить выбранные (со всех страниц)
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
               error: function (err) { alert(err.toString()); }
           });
       });

       // Удалить ВСЕ без категории — с защитой: нужно ввести "удалить все"
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
               error: function (err) { alert(err.toString()); }
           });
       });


       function customMenu(node) {
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
                                        alert(e.toString());
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
                                        alert(e.toString());
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
                                        alert(e.toString());
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
                    "plugins": [ "contextmenu", "unique", "dnd"],
                    "contextmenu": {items: customMenu}
                }).on("select_node.jstree", function (e, data) {

                });

                // Все ноды раскрыты по умолчанию
                $('#tree').on('ready.jstree refresh.jstree', function () {
                    $('#tree').jstree('open_all');
                });

                // Двойной клик по тип-ноде (level=3) -> модалка настроек с актуальными значениями
                $('#tree').on('dblclick.jstree', '.jstree-anchor', function () {
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
                            error: function (err) { alert(err.toString()); }
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




                $("#addRootNode").on('click',(function(e) {
                    e.preventDefault();
                    var name = $('#level1').val();
                    var parent_id, level, sort = 0;
                    var enabled = 1;
                    if(name === ''){
                        alert('Поле не может быть пустым');
                    }else{
                        $.ajax({
                           url: "../modules/addTreeNode.php",
                            type: "POST",
                            data:  {name: name, parent_id: parent_id, level: level, sort: sort, enabled: enabled},
                            beforeSend : function()
                            {
                             //alert('beforesend')
                            },
                            success: function(data)
                               {
                                   console.log(data);
                                 alert(data);
                                $("#level1").val();
                                $('#tree').jstree(true).refresh();
                               },
                              error: function(e)
                               {
                                   alert(e.toString());
                               }
                             });
                    }
                    //alert('befin');

                   }));


});

function number_format(number, decimals, dec_point, thousands_sep) {
  // *     example: number_format(1234.56, 2, ',', ' ');
  // *     return: '1 234,56'
  number = (number + '').replace(',', '').replace(' ', '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function(n, prec) {
      var k = Math.pow(10, prec);
      return '' + Math.round(n * k) / k;
    };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1).join('0');
  }
  return s.join(dec);
}

function showGraph()
{
    {
        $.post("../modules/ordersGraph.php",
        function (data)
        {
            var a = JSON.parse(data);
            console.log(a);
             var date = []; //mark
            var quantity = [];

            for (var i in a) {
                quantity.push(a[i].quantity);
                date.push(a[i].date);
            }

            var ctx = document.getElementById("myAreaChart");
            var myLineChart = new Chart(ctx, {
              type: 'line',
              data: {
                labels: date,
                datasets: [{
                  label: "Количество заказов за день",
                  lineTension: 0.3,
                  backgroundColor: "rgba(78, 115, 223, 0.05)",
                  borderColor: "rgba(78, 115, 223, 1)",
                  pointRadius: 3,
                  pointBackgroundColor: "rgba(78, 115, 223, 1)",
                  pointBorderColor: "rgba(78, 115, 223, 1)",
                  pointHoverRadius: 3,
                  pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                  pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                  pointHitRadius: 10,
                  pointBorderWidth: 2,
                  data: quantity,
                }],
              },options: {
                maintainAspectRatio: false,
                layout: {
                  padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                  }
                }
              }
            });
        });
    }
}

function notifSet () {
    alert();
		if (!("Notification" in window))
			alert ("Ваш браузер не поддерживает уведомления.");
		else if (Notification.permission === "granted")
			setTimeout(notifyMe, 2000);
		else if (Notification.permission !== "denied") {
			Notification.requestPermission (function (permission) {
				if (!('permission' in Notification))
					Notification.permission = permission;
				if (permission === "granted")
					setTimeout(notifyMe, 2000);
			});
		}
            }

            function notifyMe () {
		var notification = new Notification ("Все еще работаешь?", {
			tag : "ache-mail",
			body : "Пора сделать паузу и отдохнуть",
			icon : "https://itproger.com/img/notify.png"
		});
            }
