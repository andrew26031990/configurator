<div class="main__caption">
    <div class="container">
        <h2>КОНФИГУРАТОР</h2>
        <p>Выберите нужный вам тип конфигуратора</p>
        <ul>
            <?php
            // Категория 'sborki' ещё в разработке и показывается только
            // с офисных IP.
            $previewIps = array('81.95.232.52', '62.209.150.52', '185.43.87.195');
            $clientIp   = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

            $category = getRootNodesZero($mysqli);

            if (count($category) > 0) {
                foreach ($category as $value) {
                    $isPreview = ($value['link'] === 'sborki');

                    if ($isPreview && !in_array($clientIp, $previewIps, true)) {
                        continue;
                    }

                    $label = $value['name'] . ($isPreview ? ' (в процессе разработки)' : '');
                    ?>
                    <li>
                        <a href="/index.php?cat=<?= e(rawurlencode($value['link'])); ?>">
                            <img src="configurator/images/<?= e($value['image']); ?>" alt="">
                            <span><?= e($label); ?></span>
                        </a>
                    </li>
                    <?php
                }
            } else {
                ?>
                <li><span>нет данных</span></li>
            <?php } ?>
        </ul>
    </div>
</div>
