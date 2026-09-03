<?php
/**
 * Страница "Готовые сборки" (index.php?cat=kompyutery).
 *
 * Подключается из index.php, соединение с базой берётся оттуда же —
 * раньше здесь было второе подключение с логином и паролем в коде.
 */

require_once __DIR__ . '/../functions.php';

$pageNo   = get_int('page', 1);
$pageNo   = ($pageNo === null || $pageNo < 1) ? 1 : $pageNo;
$sizePage = 8;

$totalRows  = db_count($mysqli, 'SELECT COUNT(*) FROM `sborki`');
$totalPages = (int) ceil($totalRows / $sizePage);

if ($totalPages > 0 && $pageNo > $totalPages) {
    $pageNo = $totalPages;
}

$offset = ($pageNo - 1) * $sizePage;

// LIMIT тоже параметризован: раньше сюда попадал $_GET['page'] как есть.
$sborki = db_rows(
    $mysqli,
    'SELECT id, title, link, image, description, price FROM `sborki` ORDER BY price LIMIT ?, ?',
    array($offset, $sizePage)
);

$currentCat = isset($_GET['cat']) ? (string) $_GET['cat'] : '';
?>

<div class="container">
    <div class="row products">
        <!--- СБОРКА -->
        <?php foreach ($sborki as $row) { ?>
            <div class="product">

                <div class="product__inner">
                    <a href="#" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">

                            <span class="wrapped">
                                <img width="400" height="400"
                                     src="configurator/images/sborki/<?= e($row['image']); ?>" alt=""
                                     loading="lazy">
                            </span>
                        <h2 class="woocommerce-loop-product__title"><?= e_html($row['title']); ?></h2>
                        <span class="price"><del aria-hidden="true">Цена:
                                    <span class="woocommerce-Price-amount amount"><bdi><?= e(number_format((float) $row['price'], 0, '', ' ')); ?>
                                            <span class="woocommerce-Price-currencySymbol">сум</span>
                                        </bdi>

                                    </span>
                                </del>
                            </span>
                    </a>

                    <a class="description" href="<?= e_url($row['link']); ?>" target="_blank" rel="noopener">
                            <span class="product-image">
                                <img width="400" height="400"
                                     src="configurator/images/sborki/<?= e($row['image']); ?>" alt=""> </span>

                        <h5><?= e_html($row['description']); ?>
                        </h5>
                        <a href="<?= e_url($row['link']); ?>" class="button" target="_blank" rel="noopener"
                           style="z-index: 1;">Открыть сборку</a>
                    </a>
                </div>

            </div>
            <!---- -->
        <?php } ?>

    </div>
    <ul class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
            <?php if ($i !== $pageNo) { ?>
                <li class="active">
                    <a href="index.php?cat=<?= e(rawurlencode($currentCat)); ?>&amp;page=<?= $i; ?>"><?= $i; ?></a>
                </li>
            <?php } else { ?>
                <li class="disabled">
                    <a href="#" style="pointer-events: none;opacity: 0.5;"><?= $i; ?></a>
                </li>
            <?php } ?>
        <?php } ?>
    </ul>
</div>
