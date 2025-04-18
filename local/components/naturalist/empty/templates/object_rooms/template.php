<?php

use Naturalist\Users;

global $isAuthorized, $isMobile;

foreach ($arParams['VARS'] as $key => $value) {
    ${$key} = $value;
}?>

<div class="rooms" data-object-container>
    <? if (empty($arExternalInfo)) { ?>
        <? foreach ($arElements as $arElement):
           require 'empty_rooms.php';
        endforeach; ?>
    <? } else { ?>
        <div class="rooms__heading">Выберите размещение <span><?= $daysRange ?>, <?= $guests ?> <?= $guestsDeclension->get($guests) ?><? if ($children > 0): ?>, <?= $children ?> <?= $childrenDeclension->get($children) ?><? endif; ?></span></div>

        <div class="rooms__list">
            <? if ($arSection["UF_EXTERNAL_SERVICE"] == "bnovo") {
                require 'bnovo_rooms.php';
            } elseif ($arSection["UF_EXTERNAL_SERVICE"] == "bronevik") {
                require 'bronevik_rooms.php';
            } elseif ($arSection["UF_EXTERNAL_SERVICE"] == "uhotels") {
                require 'uhotels_rooms.php';
            } else {
                require 'traveline_rooms.php';
            } ?>
        </div>

        <? if ($page < $pageCount): ?>
            <div class="rooms__more">
                <a href="#" data-object-showmore data-page="<?= $page + 1 ?>">Показать ещё</a>
            </div>
        <? endif; ?>
    <? } ?>
</div>