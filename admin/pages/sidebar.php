<?php $view = isset($view) ? (string) $view : (isset($_GET['view']) ? (string) $_GET['view'] : 'dashboard'); ?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <!-- Brand -->
    <a href="/" class="brand-link text-center">
        <span class="brand-text font-weight-light"><b>PC</b>MARKET</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <?php if (user_can('dashboard.view')) { ?>
                <li class="nav-item">
                    <a href="/admin" class="nav-link <?php echo $view === 'dashboard' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <?php } ?>

                <?php
                $componentViews = array('products', 'categories', 'no_category', 'constructors', 'add_constructor');
                $canProducts = user_can('products.view');
                $canSborki   = user_can('sborki.view');
                if ($canProducts || $canSborki) {
                ?>
                <li class="nav-item <?php echo in_array($view, $componentViews, true) ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link <?php echo in_array($view, $componentViews, true) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-microchip"></i>
                        <p>Товары и сборки <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <?php if ($canProducts) { ?>
                        <li class="nav-item">
                            <a href="/admin/products" class="nav-link <?php echo in_array($view, array('products', 'categories'), true) ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i><p>Товары</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/admin/no_category" class="nav-link <?php echo $view === 'no_category' ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i><p>Товары без категории</p>
                            </a>
                        </li>
                        <?php } ?>
                        <?php if ($canSborki) { ?>
                        <li class="nav-item">
                            <a href="/admin/constructors" class="nav-link <?php echo in_array($view, array('constructors', 'add_constructor'), true) ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i><p>Все сборки</p>
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                </li>
                <?php } ?>

                <?php if (user_can('filters.view')) { ?>
                <li class="nav-item">
                    <a href="/admin/filters" class="nav-link <?php echo $view === 'filters' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-filter"></i>
                        <p>Фильтры</p>
                    </a>
                </li>
                <?php } ?>

                <?php if (user_can('users.manage')) { ?>
                <li class="nav-item <?php echo $view === 'logins' ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link <?php echo $view === 'logins' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Пользователи <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/admin/logins" class="nav-link <?php echo $view === 'logins' ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i><p>Учетные записи</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php } ?>

            </ul>
        </nav>
    </div>
    <!-- /.sidebar -->
</aside>
