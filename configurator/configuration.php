
<header class="header header--mobile mobile-only">
    <div class="owl-carousel carousel--first mobile-only">
        <?php
        $types = getRootNodesFirst($mysqli, $cat);
        $i = 0;
        foreach ($types as $item) {
            ?>
            <div class="slide" data-items="<?php echo $i; ?>">
                <a href="javascript:void(0);"><?php $rootNodeFirst = $item['id'];
                    echo $item['name']; ?></a>
            </div>
            <?php $i++;
        } ?>
    </div>
    <!--- / Карусель -->


    <!--- Карусель -->
    <div class="owl-carousel carousel--second mobile-only">
        <?php
        $types = getRootNodesFirst($mysqli, $cat);
        foreach ($types as $item) {
            ?>
            <div class="slide">
                <div class="owl-carousel carousel carousel--inner">
                    <?php
                    $rootNodeFirst = $item['id'];
                    $components = getRootNodesSecond($mysqli, $rootNodeFirst);
                    foreach ($components as $item) {
                        ?>
                        <div class="slide">
                            <a href="#<?php echo $item['translit']; ?>">
                                <?php /*echo $item['image']; */
                                ?>
                                <?php echo $item['name']; ?>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
</header>

<div class="container mar-t-30">
    <div class="config-block">


        <!-- Левый сайдбар-->
        <aside data-offset="100" class="">
            <h2 class="no-mobile">Меню</h2>
            <div class="aside-inner">

                <ul class="accordion">
                    <?php
                    $types = getRootNodesFirst($mysqli, $cat);
                    foreach ($types as $item) {
                        ?>
                        <li>
                            <a class="toggle" href="javascript:void(0);">
                                <img src="configurator/images/<?php echo $item['image']; ?>" />
                                <?php $rootNodeFirst = $item['id'];
                                echo $item['name']; ?>
                            </a>
                            <ul class="inner menuElems">
                                <?php
                                $components = getRootNodesSecond($mysqli, $rootNodeFirst);
                                foreach ($components as $item) {
                                    ?>
                                    <li>
                                        <a href="#"><?php echo $item['name']; ?></a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </aside>
        <!-- / Левый сайдбар-->

        <div class="center-block">
            <?php
            $components = getAllChildNodes($mysqli, $cat);
            foreach ($components as $item) {
                $translit = $item['translit'];
                $onLoadcountable = $item['onLoadcountable'];
                $uncheck_btn = $item['uncheck_btn'];
                ?>
                <div class="scrollbar-inner">
                    <table class="product-table <?php if ($onLoadcountable == 1) { ?>countable<?php } ?>"
                           id="<?php echo $item['translit']; ?>">
                        <tbody>
                        <tr>
                            <td class="left-td">
                                <h2><?php $id = $item['id'];
                                    echo $item['name']; ?></h2>
                                <div class="left-td-inner">
                                    <!-- Табы - кнопки -->
                                    <div class="inner scroll-pane">
                                        <ul class="list filter">
                                            <li data-filter=".all">
                                                <a href="javascript:void(0);" data-type="<?php echo $translit; ?>" filter-id="0000"
                                                   data-id="<?php echo $id; ?>">Все</a>
                                            </li>

                                            <?php
                                            $filters = getFiltersByTreeId($mysqli, $id);
                                            foreach ($filters as $item) {
                                                ?>
                                                <li data-filter=".asus">
                                                    <a href="javascript:void(0);" data-id="<?php echo $id; ?>"
                                                       data-type="<?php echo $translit; ?>"
                                                       filter-id="<?php echo $item['id'] ?>"><?php echo $item['f_name']; ?></a>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                    <!-- / Табы -кнопки-->
                                </div>
                                <div class="inner_2 scroll-pane">
                                    <!-- Табы - контент -->
                                    <ul class="main-list">
                                        <?php
                                        $prod = getAllProductsByTreeId($mysqli, $id);
                                        foreach ($prod as $item) {
                                            ?>
                                            <li class="all asus">
                                                <div class="product-block">
                                                    <input type="radio" data="<?= $item['image']; ?>"
                                                           id="<?= $item['id']; ?>" name="<?= $translit; ?>"
                                                           value="<?php echo $item['price']; ?>"/>
                                                    <label for="<?= $item['id']; ?>"
                                                           data-name="<?php echo $item['name']; ?>">
                                                        <span></span>
                                                        <select title="Выберите количество" class="select quantity"
                                                                method="post" name="<?= $translit; ?>" <?php if ($translit !== 'monitor' && $translit !== 'operativnaya_pamyatj' && $translit !== 'ozu' && $translit !== 'jestkie_diski_HDD' && $translit !== 'jestkiy_disk') { ?> disabled <?php } ?>>
                                                            <option value="1" selected>1</option>
                                                            <option value="2">2</option>
                                                            <option value="3">3</option>
                                                            <option value="4">4</option>
                                                            <option value="5">5</option>
                                                            <option value="6">6</option>
                                                        </select>

                                                        <?php echo $item['name']; ?>
                                                       <a class="info" href="<?php echo $item['description']; ?>" style="z-index: 10;width: 20px;margin-left:5px;" target="_BLANK"><img src="configurator/images/info.svg" /></a>
                                                    </label>
                                                </div>
                                                <div class="price-block">
                                                    <?php echo $item['price']; ?>
                                                    <!--UZS&nbsp;--><?php //echo money_format('%i ', $item['price']);
                                                    ?>
                                                </div>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                    <!-- / Табы - контент -->
                                </div>
                            </td>
                            <td class="right-td <?= $translit; ?>">
                                <?php if ($uncheck_btn == 1) { ?>
                                    <a href="#" class="button float-right refresh">Удалить элемент</a>
                                <?php } ?>
                                <div class="clearfix"></div>
                                <div class="img-holder">
                                    <img src="" alt="" class="zoom" data-magnify-src="configurator/images/products/<?= $item['image']; ?>"/>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </div>


        <aside class="configuration no-mobile fixed" data-offset="100">
            <h2>Ваша сборка</h2>
            <div class="configuration-inner">
                <div class="configuration-inner-inner">
                    <?php $title = getTitle($mysqli, $cat);
                    foreach ($title as $item) { ?>
                        <h3><?php echo $item['name'];
                            $link = $item['name']; ?></h3>
                    <?php } ?>
                    <div class="config-img-outer">
                        <img src="configurator/images/config.png" alt="" class="config-img"/>
                    </div>

                    <div class="result-holder">
                        <ul class="result">

                        </ul>
                        <a href="#modal" class="popup" style="width: 100%;color: red;margin-left: 64px;">Полная спецификация</a>
                    </div>
                    <div class="total-result">
                        <p>Общая сумма:</p>

                        <div class="total-title">
                            0 сум
                        </div>
                    </div>

                    <div class="buttons-holder">
                        <a href="#modal_2" class="button popup" style="width: 100%">
                            Купить
                        </a>
                    </div>
                    <div class="buttons-holder">
                        <a href="javascript:void(0);" style="width: 100%;color: black" class="button button--violet-light" onclick="generateConfigToLink()">
                            Скопировать ссылку на конфигурацию
                        </a>
                    </div>
                    
                    </div>

                </div>
            </div>
        </aside>
    </div>
</div>

<!-- Нижний блок -->
<div class="bottom-section mobile-only">
    <div class="bottom-section__left">
        <p></p>
    </div>

    <div class="bottom-section__right">
        <a href="#modal" class="button popup">Купить</a>

        <a href="#" class="bottom-section__button">
            <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" data-svg="more">
                <circle cx="3" cy="10" r="2"></circle>
                <circle cx="10" cy="10" r="2"></circle>
                <circle cx="17" cy="10" r="2"></circle>
            </svg>
            Больше</a>
    </div>

    <div class="bottom-section__inner">
        <ul>
            <li>
                <a href="#modal" class="popup">
                    <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" data-svg="cog">
                        <circle fill="none" cx="9.997" cy="10" r="3.31"></circle>
                        <path fill="none"
                              d="M18.488,12.285 L16.205,16.237 C15.322,15.496 14.185,15.281 13.303,15.791 C12.428,16.289 12.047,17.373 12.246,18.5 L7.735,18.5 C7.938,17.374 7.553,16.299 6.684,15.791 C5.801,15.27 4.655,15.492 3.773,16.237 L1.5,12.285 C2.573,11.871 3.317,10.999 3.317,9.991 C3.305,8.98 2.573,8.121 1.5,7.716 L3.765,3.784 C4.645,4.516 5.794,4.738 6.687,4.232 C7.555,3.722 7.939,2.637 7.735,1.5 L12.263,1.5 C12.072,2.637 12.441,3.71 13.314,4.22 C14.206,4.73 15.343,4.516 16.225,3.794 L18.487,7.714 C17.404,8.117 16.661,8.988 16.67,10.009 C16.672,11.018 17.415,11.88 18.488,12.285 L18.488,12.285 Z">
                        </path>
                    </svg>
                    Конфигурация</a>
            </li>
            <li>
                <a href="#" class="androidShareBtn">
                    <svg width="20" height="20" viewBox="0 0 28 26" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd"><path d="M16.272 5.451c-.176-.45-.272-.939-.272-1.451 0-2.208 1.792-4 4-4s4 1.792 4 4-1.792 4-4 4c-1.339 0-2.525-.659-3.251-1.67l-7.131 3.751c.246.591.382 1.239.382 1.919 0 .681-.136 1.33-.384 1.922l7.131 3.751c.726-1.013 1.913-1.673 3.253-1.673 2.208 0 4 1.792 4 4s-1.792 4-4 4-4-1.792-4-4c0-.51.096-.999.27-1.447l-7.129-3.751c-.9 1.326-2.419 2.198-4.141 2.198-2.76 0-5-2.24-5-5s2.24-5 5-5c1.723 0 3.243.873 4.143 2.201l7.129-3.75zm3.728 11.549c1.656 0 3 1.344 3 3s-1.344 3-3 3-3-1.344-3-3 1.344-3 3-3zm-15-9c2.208 0 4 1.792 4 4s-1.792 4-4 4-4-1.792-4-4 1.792-4 4-4zm15-7c1.656 0 3 1.344 3 3s-1.344 3-3 3-3-1.344-3-3 1.344-3 3-3z"/></svg>
                    Поделиться сборкой</a>
            </li>
            <!--<li>
                <a href="javascript:void(0);" class="helpBtnClick">
                    <svg width="20" height="20" viewBox="0 0 28 26" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd"><path d="M16.272 5.451c-.176-.45-.272-.939-.272-1.451 0-2.208 1.792-4 4-4s4 1.792 4 4-1.792 4-4 4c-1.339 0-2.525-.659-3.251-1.67l-7.131 3.751c.246.591.382 1.239.382 1.919 0 .681-.136 1.33-.384 1.922l7.131 3.751c.726-1.013 1.913-1.673 3.253-1.673 2.208 0 4 1.792 4 4s-1.792 4-4 4-4-1.792-4-4c0-.51.096-.999.27-1.447l-7.129-3.751c-.9 1.326-2.419 2.198-4.141 2.198-2.76 0-5-2.24-5-5s2.24-5 5-5c1.723 0 3.243.873 4.143 2.201l7.129-3.75zm3.728 11.549c1.656 0 3 1.344 3 3s-1.344 3-3 3-3-1.344-3-3 1.344-3 3-3zm-15-9c2.208 0 4 1.792 4 4s-1.792 4-4 4-4-1.792-4-4 1.792-4 4-4zm15-7c1.656 0 3 1.344 3 3s-1.344 3-3 3-3-1.344-3-3 1.344-3 3-3z"/></svg>
                    Служба поддержки</a>
            </li>-->
        </ul>
    </div>
</div>


<!-- Модальное окно-->
<div class="modal mfp-hide" id="modal">
    <div class="configuration" data-offset="100">
        <h2>Ваша сборка</h2>
        <div class="configuration-inner">
            <div class="configuration-inner-inner">
                <h3>Конфигурация</h3>

                <div class="config-img-outer">
                    <img src="configurator/images/config.png" alt="" class="config-img"/>
                </div>

                <div class="result-holder">
                    <ul class="result">

                    </ul>
                </div>
                <div class="total-result">
                    <p>Общая сумма:</p>

                    <div class="total-title">

                    </div>
                </div>

                <div class="buttons-holder">
                    <a href="#modal_2" class="button popup" style="width: 100%">
                        Купить
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Модальное окно 2-->
<div class="modal mfp-hide" id="modal_2">
    <form class="configuration">
        <h2>Купить</h2>
        <div class="configuration-inner">
            <div class="input-holder">
                <input type="text" id="name" placeholder="Имя" required>
            </div>

            <div class="input-holder">
                <input type="text" id="phone" placeholder="Телефон" required>
            </div>

            <div class="input-holder">
                <input type="text" id="email" placeholder="E-mail" required>
            </div>
            <input type="hidden" id="sborka" value="<?php echo $link; ?>">
            <div class="input-holder">
                <button type="submit" class="button send_config">Отправить</button>
            </div>
        </div>
    </form>
</div>