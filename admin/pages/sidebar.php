<?php $view = isset($_GET['view']) ? $_GET['view'] : ''; ?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <!-- Brand -->
    <a href="/" class="brand-link text-center">
        <span class="brand-text font-weight-light"><b>PC</b>MARKET</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-item">
                    <a href="/admin.php" class="nav-link <?php echo $view === '' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <?php $componentViews = ['products','categories','no_category','constructors','add_constructor']; ?>
                <li class="nav-item <?php echo in_array($view, $componentViews) ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link <?php echo in_array($view, $componentViews) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-microchip"></i>
                        <p>Товары и сборки <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="admin.php?view=products" class="nav-link <?php echo in_array($view, ['products','categories']) ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i><p>Товары</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin.php?view=no_category" class="nav-link <?php echo $view === 'no_category' ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i><p>Товары без категории</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin.php?view=constructors" class="nav-link <?php echo in_array($view, ['constructors','add_constructor']) ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i><p>Все сборки</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="admin.php?view=filters" class="nav-link <?php echo $view === 'filters' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-filter"></i>
                        <p>Фильтры</p>
                    </a>
                </li>

                <li class="nav-item <?php echo $view === 'logins' ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link <?php echo $view === 'logins' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Пользователи <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="admin.php?view=logins" class="nav-link <?php echo $view === 'logins' ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i><p>Учетные записи</p>
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
        </nav>
    </div>
    <!-- /.sidebar -->
</aside>
