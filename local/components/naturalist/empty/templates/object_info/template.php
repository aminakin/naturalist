<?
foreach($arResult as $key => $value) {
    ${$key} = $value;
}
?>
<div class="info" data-info-dropdown="data-info-dropdown">
    <ul class="list">
        <li class="list__item list__item_large"><img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/main/info/1.svg" alt="N<?= $arMeteo['coords']['lon'] ?>°, E<?= $arMeteo['coords']['lat'] ?>°"><span>N<?= $arMeteo['coords']['lon'] ?>°, E<?= $arMeteo['coords']['lat'] ?>°</span></li>
        <li class="list__item"><img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/main/info/2.svg" alt="<?= $arMeteo['sunrise_time'] ?>"><span><?= $arMeteo['sunrise_time'] ?></span></li>
        <li class="list__item"><img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/main/info/3.svg" alt="<?= $arMeteo['sunset_time'] ?>"><span><?= $arMeteo['sunset_time'] ?></span></li>
        <li class="list__item"><img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/main/info/4.svg" alt="<?= $arMeteo['humidity'] ?>%"><span><?= $arMeteo['humidity'] ?>%</span></li>
        <li class="list__item"><img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/main/info/5.svg" alt="<?= $arMeteo['temp'] ?>"><span><?= $arMeteo['temp'] ?></span></li>
    </ul>
</div>