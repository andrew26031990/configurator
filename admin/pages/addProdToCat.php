<h1 class="h3 mb-2 text-gray-800">Добавление товара в категории</h1>
<div class="form-group">
    <label for="levels2">Выберите сборку</label>
    <select class="form-control" name="levels2" id="sborka2">
        <option value="0">Выберите сборку</option>
        <?php $node = getNodes($mysqli, 1);
        foreach ($node as $item){
            ?>
            <option value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?></option>
        <?php } ?>
    </select>
</div>
<div class="form-group">
    <label for="levels2">Выберите тип товара</label>
    <select class="form-control" name="levels2" id="tip_tovara2">
        <option value="0">Выберите тип товара</option>
    </select>
</div>
<div class="form-group">
    <label for="levels2">Выберите товар</label>
    <input class="form-control" type="text" id="prod_id_input" list="temp_list">
    <datalist id="temp_list">
        <?php $products = getAllProducts($mysqli);
        foreach ($products as $item){
            ?>
            <option value="<?php echo $item['name']; ?>" id="<?php echo $item['id']; ?>" />
        <?php } ?>
    </datalist>
    <!--<select class="form-control" name="levels2" id="tovar2">
                <option value="0">Выберите товар</option>
                <?php /*$products = getAllProducts($mysqli);
                       foreach ($products as $item){
                */?>
                <option value="<?php /*echo $item['id']; */?>"><?php /*echo $item['name']; */?></option>
                <?php /*} */?>
            </select>-->
</div>
<button type="button" class="btn btn-success" id="tip_tovara2_tovar2">Добавить связь</button>