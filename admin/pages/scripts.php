<!-- Bootstrap 4 Bundle (с Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE 3 -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>

<!-- CKEditor -->
<script src="https://cdn.ckeditor.com/ckeditor5/33.0.0/classic/ckeditor.js"></script>

<!-- Page level plugins -->
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="/admin/vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.8/jstree.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>

<!-- Права текущего пользователя для UI. Это ТОЛЬКО для скрытия кнопок —
     реальный рубеж безопасности остаётся на сервере (require_capability). -->
<script>
    window.CAN = {
        'products.edit':     <?php echo user_can('products.edit') ? 'true' : 'false'; ?>,
        'products.delete':   <?php echo user_can('products.delete') ? 'true' : 'false'; ?>,
        'categories.manage': <?php echo user_can('categories.manage') ? 'true' : 'false'; ?>,
        'filters.manage':    <?php echo user_can('filters.manage') ? 'true' : 'false'; ?>,
        'sborki.manage':     <?php echo user_can('sborki.manage') ? 'true' : 'false'; ?>
    };
</script>

<!-- Custom admin scripts -->
<script src="/admin/js/admin.js?v=<?php echo @filemtime(__DIR__ . '/../js/admin.js'); ?>"></script>
