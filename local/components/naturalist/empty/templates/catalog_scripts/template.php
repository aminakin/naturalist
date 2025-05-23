<?php
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

foreach($arResult as $key => $value) {
    ${$key} = $value;
}

?>
<script>
    window.mapOptions = {
        center: [55.757207, 37.623915],
        zoom: 7,
        maxZoom: 20,
        imageAssetsPath: '<?= SITE_TEMPLATE_PATH ?>/',
    }
    window.mapItems = [
        <? foreach ($arSections as $arSection) : ?>
            <? if (isset($arSection["COORDS"]) && !empty($arSection["COORDS"])) : ?> {
                id: '<?=$arSection["ID"]?>',
                title: '<?=str_replace("'", "", $arSection["NAME"])  ?>',
                gallery: [
                    <?
                    $prewievPicture = '';
                    foreach ($arSection["PICTURES"] as $arPhoto) :
                    $prewievPicture = !empty($prewievPicture) ? $prewievPicture : $arPhoto["src"];
                    ?>
                    '<?= $arPhoto["src"] ?>',
                    <? endforeach; ?>
                ],
                preview: '<?= $prewievPicture ?>',
                marker: '<?= $arSection["UF_ADDRESS"] ?>',
                score: '<?= $arReviewsAvg[$arSection["ID"]]["avg"] ?? 0  ?>',
                scoreData: [
                    {
                        label: "Удобство расположения",
                        value: <?= $arReviewsAvg[$arSection["ID"]]["criterials"][1][0] ?? '0.0'?>
                    },
                    {
                        label: "Питание",
                        value: <?= $arReviewsAvg[$arSection["ID"]]["criterials"][2][0] ?? '0.0'?>
                    },
                    {
                        label: "Уют",
                        value: <?= $arReviewsAvg[$arSection["ID"]]["criterials"][3][0] ?? '0.0'?>
                    },
                    {
                        label: "Сервис",
                        value: <?= $arReviewsAvg[$arSection["ID"]]["criterials"][4][0] ?? '0.0'?>
                    },
                    {
                        label: "Чистота",
                        value: <?= $arReviewsAvg[$arSection["ID"]]["criterials"][5][0] ?? '0.0'?>
                    },
                    {
                        label: "Эстетика окружения",
                        value: <?= $arReviewsAvg[$arSection["ID"]]["criterials"][6][0] ?? '0.0'?>
                    },
                    {
                        label: "Разнообразие досуга",
                        value: <?= $arReviewsAvg[$arSection["ID"]]["criterials"][7][0] ?? '0.0'?>
                    },
                    {
                        label: "Соотношение цена/качество",
                        value: <?= $arReviewsAvg[$arSection["ID"]]["criterials"][8][0] ?? '0.0'?>
                    }
                ],
                price: '<?= number_format((float)$arSection["PRICE"], 0, '.', ' ') ?> ₽',
                tag: <?if ($arSection["IS_DISCOUNT"] == 'Y') : ?>'<?= $arSection["UF_SALE_LABEL"] != '' ? $arSection["UF_SALE_LABEL"] : Loc::GetMessage('CATALOG_DISCOUNT') ?>'<? elseif (!empty($arSection["UF_ACTION"])) : ?>'<?= $arSection["UF_ACTION"] ?>'<? else : ?>false<? endif; ?>,
                favorite: <? if ($arFavourites && in_array($arSection["ID"], $arFavourites)) : ?>true<? else : ?>false<? endif; ?>,
                mapHref: '<?= $arSection["URL"] ?>',
                href: '<?= $arSection["URL"] ?>',
                coords: [<?= $arSection["COORDS"][0] ?>, <?= $arSection["COORDS"][1] ?>]
            },
            <? endif; ?>
        <? endforeach; ?>
    ]

    <?if($arParams["map"]):?>
    $(function() {
        window.map.handleShowHide(true);
    });
    <?endif;?>
</script>