<div class="main__caption">
    <div class="container">
        <h2>КОНФИГУРАТОР</h2>
        <p>Выберите нужный вам тип конфигуратора</p>
        <ul>
            <?php $category = getRootNodesZero($mysqli);
                if(count($category) > 0){
                    foreach ($category as $value) {
                        echo '<li><a href="/index.php?cat='.$value['link'].'"><img src="configurator/images/'.$value['image'].'" alt=""><span>'.$value['name'].'</span></a></li>';
                    }
                }else{
                    echo '<li><span>нет данных</span></li>';
                }
            ?>
        </ul>
    </div>
</div>