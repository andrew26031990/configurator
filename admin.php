<?php
require_once __DIR__ . '/functions.php';

deny_framing();

if (!is_admin()) {
    header('Location: /login');
    exit();
}

if (isset($_GET['view']) && $_GET['view'] === 'exit_cab') {
    exit_cab();
    header('Location: /login');
    exit();
}

/*
 * Имя подключаемой страницы приходит из ?view=. Раньше оно попадало
 * в include как есть, то есть позволяло подключить любой .php на диске.
 */
$view = isset($_GET['view']) ? (string) $_GET['view'] : 'dashboard';

// Явный белый список страниц админки — подключить произвольный .php через ?view нельзя.
$allowedViews = array(
    'dashboard', 'products', 'categories', 'no_category',
    'filters', 'constructors', 'add_constructor', 'logins',
);

if (!in_array($view, $allowedViews, true)) {
    $view = 'dashboard';
}

// Сессия протухла (старая, без user_id) или аккаунт деактивирован — на вход.
if (!current_user()) {
    header('Location: /login');
    exit();
}

// RBAC: какое право нужно для страницы. Нет права — на дашборд
// (а если и дашборд недоступен — 403, чтобы не было петли редиректов).
$viewCapabilities = array(
    'dashboard'       => 'dashboard.view',
    'products'        => 'products.view',
    'categories'      => 'categories.manage',
    'no_category'     => 'products.view',
    'filters'         => 'filters.manage',
    'constructors'    => 'sborki.manage',
    'add_constructor' => 'sborki.manage',
    'logins'          => 'users.manage',
);

$needCap = isset($viewCapabilities[$view]) ? $viewCapabilities[$view] : null;

if ($needCap !== null && !user_can($needCap)) {
    if ($view === 'dashboard') {
        http_response_code(403);
        exit('Недостаточно прав');
    }
    header('Location: /admin');
    exit();
}

$viewFile = __DIR__ . '/admin/pages/' . $view . '.php';

if (!is_file($viewFile)) {
    $viewFile = __DIR__ . '/admin/pages/dashboard.php';
}
?>
<!DOCTYPE html>
<html lang="ru">

<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>PCMARKET - Dashboard</title>

    <!-- jQuery (нужен плагинам ниже) -->
    <script src="https://code.jquery.com/jquery-latest.min.js"></script>

    <!-- Font Awesome -->
    <link href="/admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <!-- Google Font: Source Sans Pro (AdminLTE) -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- AdminLTE 3 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    <!-- jsTree -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
    <!-- DataTables (Bootstrap 4) -->
    <link href="/admin/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar / Topbar -->
    <?php include(__DIR__ . '/admin/pages/topbar.php'); ?>

    <!-- Sidebar -->
    <?php include(__DIR__ . '/admin/pages/sidebar.php'); ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <section class="content pt-3">
            <div class="container-fluid">
                <?php include $viewFile; ?>
            </div>
        </section>
    </div>
    <!-- /.content-wrapper -->

    <!-- Footer -->
    <?php include(__DIR__ . '/admin/pages/footer.php'); ?>

</div>
<!-- ./wrapper -->

<!-- Modals-->
<?php include(__DIR__ . '/admin/modals/modals.php'); ?>
<!-- End Modals-->
<?php include(__DIR__ . '/admin/pages/scripts.php'); ?>


</body>

</html>
