<?
foreach ($arResult as $key => $value) {
    ${$key} = $value;
}
?>
<div class="crumbs">
    <ul class="list crumbs__list">
        <li class="list__item"><a class="list__link" href="/">Главная</a></li>
        <li class="list__item"><a class="list__link" href="/catalog/">Каталог</a></li>
        <li class="list__item"><a class="list__link"><?= htmlspecialcharsBack($arSection["NAME"]) ?></a></li>
    </ul>
</div>