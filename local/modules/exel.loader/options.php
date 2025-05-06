<?php

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Exel\Loader\Handlers\FormSave;

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

defined('ADMIN_MODULE_NAME') or define('ADMIN_MODULE_NAME', 'exel.loader');

/** @var $APPLICATION */
/** @var $USER */
/** @var $mid */


if (!$USER->isAdmin()) {
    $APPLICATION->authForm('Nope');
}

$moduleId = 'exel.loader';
Loader::includeModule($moduleId);
Loader::includeModule('highloadblock');

$FormSave = new FormSave();

Loc::loadMessages(__FILE__);

$tabControl = new CAdminTabControl('tabControl', [
    [
        'DIV' => 'edit1',
        'TAB' => Loc::getMessage('EXEL_LOADER_YANDEX_REVIEW'),
        'TITLE' => Loc::getMessage('EXEL_LOADER_YANDEX_REVIEW'),
    ],
    [
        'DIV' => 'edit2',
        'TAB' => Loc::getMessage('EXEL_LOADER_USERS'),
        'TITLE' => Loc::getMessage('EXEL_LOADER_USERS'),
    ],
]);

?>
<form method="post"
      action="<?php echo $APPLICATION->GetCurPage() ?>?mid=<?=htmlspecialchars($mid)?>&amp;lang=<?php echo LANG; ?>"
      enctype="multipart/form-data">
<?php
$tabControl->Begin();
$tabControl->BeginNextTab();
include($_SERVER['DOCUMENT_ROOT'] . '/local/modules/' . $moduleId . '/optionsInclude/yandexreview.php');
$tabControl->BeginNextTab();
include($_SERVER['DOCUMENT_ROOT'] . '/local/modules/' . $moduleId . '/optionsInclude/userloader.php');


$tabControl->Buttons();?>

    <div>
        <?php if ($FormSave->responce != '') {
            echo $FormSave->responce;
        } ?>
    </div>
    <div align="left">
        <input type="hidden" name="save" value="Y">
        <input type="submit" <?php if(!$USER->IsAdmin()) echo " disabled "; ?> name="save" value="<?php echo GetMessage('MAIN_SAVE'); ?>">
    </div>
    <?php $tabControl->End(); ?>
    <?= bitrix_sessid_post(); ?>
</form>
