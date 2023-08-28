<?
foreach ($arResult as $key => $value) {
    ${$key} = $value;
}
?>
<div class="about" data-services-parent>
    <? if (!empty($arSection["DESCRIPTION"]) || $arSection["UF_FEATURES"]): ?>
        <div class="about__text about__text_hidden">
            <div class="about__text-content">
                <div class="h3">Описание</div>
                <div class="about__text-hide" data-text-show>
                    <div><?= htmlspecialcharsBack($arSection["~DESCRIPTION"]) ?></div>
                </div>
            </div>

            <div class="about__text-show">
                <a href="#" data-text-show-control data-text-show-more="Подробнее" data-text-show-hide="Скрыть"></a>
            </div>

            <? if ($arSection["UF_FEATURES"]): ?>
                <ul class="list list_icons">
                    <?
                    //$arSlicedFeatures = array_slice($arSection["UF_FEATURES"], 0, 6);
                    ?>
                    <? foreach ($arSection["UF_FEATURES"] as $key => $featureId): ?>
                        <?
                        $arIcon = CFile::GetFileArray($arHLFeatures[$featureId]["UF_ICON"]);
                        ?>
                        <li class="list__item" <?= $key > 5 ? 'data-services-hide' : ''; ?>>
                            <img src="<?= $arIcon["SRC"] ?>" alt="<?= $arHLFeatures[$featureId]["UF_NAME"] ?>">
                            <span><?= $arHLFeatures[$featureId]["UF_NAME"] ?></span>
                        </li>
                    <? endforeach; ?>
                </ul>
            <? endif; ?>
        </div>
    <? endif; ?>

    <? if ($coords): ?>
        <div class="about__map">
            <div class="about__map-heading">
                <div class="h3">Расположение</div>
                <a href="https://yandex.ru/maps/?mode=routes&rtext=~<?= $coords ?>">Проложить маршрут</a>
            </div>

            <div class="about__map-map">
                <div id="map-preview"></div>
                <a class="about__map-modal" href="#modal-map" data-modal></a>
            </div>
        </div>
    <? endif; ?>

    <? if ($arServices): ?>
        <div class="about__services">
            <div class="about__services-list" data-services-hide>
                <? foreach ($arServices as $arServiceGroup): ?>
                    <div class="about__services-item">
                        <div class="h6"><?= $arServiceGroup["NAME"] ?></div>
                        <ul class="list">
                            <? foreach ($arServiceGroup["ITEMS"] as $arServiceItem): ?>
                                <li class="list__item"><?= $arServiceItem["NAME"] ?></li>
                            <? endforeach; ?>
                        </ul>
                    </div>
                <? endforeach; ?>
            </div>

            <div class="about__services-show">
                <a href="#" data-services-control data-services-control-more="Все услуги" data-services-control-hide="Скрыть услуги"></a>
            </div>
        </div>
    <? endif; ?>
</div>