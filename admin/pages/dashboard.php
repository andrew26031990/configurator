<?php
$totalProducts = CountProducts($mysqli);
$inCat         = CountProductsInCategories($mysqli);
$noCat         = CountProductsWithoutCategory($mysqli);
$filtersCount  = CountFilters($mysqli);
$sborkiCount   = CountSborka($mysqli);

$perBuild    = getProductsPerBuild($mysqli);
$buildLabels = array();
$buildData   = array();
foreach ($perBuild as $b) {
    $buildLabels[] = $b['name'];
    $buildData[]   = $b['count'];
}
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
</div>

<!-- Карточки статистики -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner"><h3><?php echo (int) $totalProducts; ?></h3><p>Всего товаров</p></div>
            <div class="icon"><i class="fas fa-box"></i></div>
            <a href="admin.php?view=products" class="small-box-footer">К товарам <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner"><h3><?php echo (int) $inCat; ?></h3><p>Разложено по категориям</p></div>
            <div class="icon"><i class="fas fa-sitemap"></i></div>
            <a href="admin.php?view=products" class="small-box-footer">К товарам <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner"><h3><?php echo (int) $noCat; ?></h3><p>Без категории</p></div>
            <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
            <a href="admin.php?view=no_category" class="small-box-footer">Показать <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner"><h3><?php echo (int) $sborkiCount; ?></h3><p>Сборок</p></div>
            <div class="icon"><i class="fas fa-desktop"></i></div>
            <a href="admin.php?view=constructors" class="small-box-footer">К сборкам <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner"><h3><?php echo (int) $filtersCount; ?></h3><p>Фильтров</p></div>
            <div class="icon"><i class="fas fa-filter"></i></div>
            <a href="admin.php?view=filters" class="small-box-footer">К фильтрам <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<!-- Графики -->
<div class="row">
    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Товары: в категориях vs без категории</h6>
            </div>
            <div class="card-body">
                <div style="position:relative;height:260px;"><canvas id="catPie"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Товаров по сборкам</h6>
            </div>
            <div class="card-body">
                <div style="position:relative;height:260px;"><canvas id="buildBar"></canvas></div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        if (typeof Chart === 'undefined') { return; }

        var pieEl = document.getElementById('catPie');
        if (pieEl) {
            new Chart(pieEl, {
                type: 'doughnut',
                data: {
                    labels: ['В категориях', 'Без категории'],
                    datasets: [{ data: [<?php echo (int) $inCat; ?>, <?php echo (int) $noCat; ?>], backgroundColor: ['#1cc88a', '#f6c23e'] }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        var barEl = document.getElementById('buildBar');
        if (barEl) {
            new Chart(barEl, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($buildLabels, JSON_UNESCAPED_UNICODE); ?>,
                    datasets: [{ label: 'Товаров', data: <?php echo json_encode($buildData); ?>, backgroundColor: '#4e73df' }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: { display: false },
                    scales: { yAxes: [{ ticks: { beginAtZero: true } }] }
                }
            });
        }
    });
</script>
