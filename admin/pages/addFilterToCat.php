<h1 class="h3 mb-2 text-gray-800">Добавление товара в категории</h1>
<div class="form-group">
    <label for="sborka1">Выберите сборку</label>
    <select class="form-control" name="levels2" id="sborka1">
        <option value="0">Выберите сборку</option>
        <?php $node = getNodes($mysqli, 1);
        foreach ($node as $item){
            ?>
            <option value="<?php echo e($item['id']); ?>"><?php echo e($item['name']); ?></option>
        <?php } ?>
    </select>
</div>
<div class="form-group">
    <label for="tip_tovara1">Выберите тип товара</label>
    <select class="form-control" name="levels2" id="tip_tovara1">
        <option value="0">Выберите тип товара</option>
    </select>
</div>
<div class="form-group">
    <label for="filter1">Выберите фильтр</label>
    <select class="form-control" name="levels2" id="filter1">
        <option value="0">Выберите фильтр</option>
        <?php $filters = getAllFilters($mysqli);
        foreach ($filters as $item){
            ?>
            <option value="<?php echo e($item['id']); ?>"><?php echo e($item['f_name']); ?></option>
        <?php } ?>
    </select>
</div>
<button type="button" class="btn btn-success" id="tip_tovara1_filter1">Добавить связь</button>