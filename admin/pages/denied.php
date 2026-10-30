<?php
// Страница «Доступ запрещён». Рендерится внутри обычного layout админки,
// когда у пользователя нет права на запрошенную страницу (admin.php, код 403).
?>
<div class="text-center py-5">
    <i class="fas fa-exclamation-triangle text-danger" style="font-size:72px;"></i>
    <h2 class="mt-3 text-danger font-weight-bold">Доступ запрещён</h2>
    <p class="text-muted">
        У вас недостаточно прав для просмотра этой страницы.<br>
        Если считаете, что это ошибка — обратитесь к администратору.
    </p>
    <a href="/admin" class="btn btn-primary mt-2">
        <i class="fas fa-arrow-left mr-1"></i> Вернуться на дашборд
    </a>
</div>
