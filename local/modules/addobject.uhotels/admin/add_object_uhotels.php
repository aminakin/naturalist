<?php


use Addobject\Uhotels\Import\ImportData;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

IncludeModuleLangFile(__FILE__);

$test = \Bitrix\Main\Loader::includeModule("addobject.uhotels");
$test = \Bitrix\Main\Loader::includeModule("iblock");


// Список закладок
$aTabs = [
    [
        "DIV" => "edit1",
        "TAB" => GetMessage("MODULE_TAB"),
        "ICON" => "main_user_edit",
        "TITLE" => GetMessage("MODULE_TITLE")
    ]
];

$tabControl = new CAdminTabControl("tabControl", $aTabs);

$message = null; // Сообщение об ошибке
$bVarsFromForm = false; // Флаг "Данные получены с формы"

// ******************************************************************** //
//                ОБРАБОТКА ИЗМЕНЕНИЙ ФОРМЫ                             //
// ******************************************************************** //

if ($_SERVER["REQUEST_METHOD"] === "POST" && check_bitrix_sessid() && !empty($_REQUEST["object_id"])) {

    //тут код загрузки
    $ImportData = new ImportData();
    $object = $ImportData->import($_REQUEST["object_id"], (bool)$_REQUEST["only_rooms"], (bool)$_REQUEST["only_tariffs"]);

    if (!empty($object["MESSAGE"]["ERRORS"])) {
        $arMsg = array_map(fn($error) => ["id" => "NULL", "text" => $error], $object["MESSAGE"]["ERRORS"]);
        $e = new CAdminException($arMsg);
        $GLOBALS["APPLICATION"]->ThrowException($e);
        $message = new CAdminMessage($object["MESSAGE"]["ERRORS"], $e);
    }
}

// ******************************************************************** //
//                ВЫБОРКА И ПОДГОТОВКА ДАННЫХ ФОРМЫ                     //
// ******************************************************************** //

$APPLICATION->SetTitle(GetMessage("MODULE_TITLE"));
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

// ******************************************************************** //
//                ВЫВОД СООБЩЕНИЙ                                      //
// ******************************************************************** //

if (!$message && !empty($object["MESSAGE"]["SUCCESS"])) {
    $message = new CAdminMessage([
        "MESSAGE" => $object["MESSAGE"]["SUCCESS"],
        "TYPE" => "OK"
    ]);
    echo $message->Show();
} elseif ($message) {
    echo $message->Show();
}

// ******************************************************************** //
//                ВЫВОД ФОРМЫ                                           //
// ******************************************************************** //

?>
    <form method="POST" action="<?= $APPLICATION->GetCurPage() ?>" enctype="multipart/form-data" name="post_form">
        <?php echo bitrix_sessid_post(); ?>

        <?php $tabControl->Begin(); ?>
        <?php $tabControl->BeginNextTab(); ?>

        <tr>
            <td class="field-data">
                <input type="text" name="object_id" id="object_id"
                       value="<?= htmlspecialcharsbx($_REQUEST["object_id"]) ?>" style="width:40%"/>
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 0 0 0;"><?= GetMessage("MODULE_INSERT_ID") ?></td>
        </tr>
        <tr>
            <td style="padding: 10px 0 0 0;">
                <label>
                    <input type="checkbox" name="only_rooms" id="only_rooms" value="Y">
                    <span><?= GetMessage("MODULE_ONLY_ROOMS") ?></span>
                </label>
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 0 0 0;">
                <label>
                    <input type="checkbox" name="only_tariffs" id="only_tariffs" value="Y">
                    <span><?= GetMessage("MODULE_ONLY_TARIFFS") ?></span>
                </label>
            </td>
        </tr>

        <?php $tabControl->Buttons(); ?>
        <input type="hidden" name="lang" value="<?= LANG ?>">
        <input type="submit" value="<?= GetMessage("MODULE_IMPORT_BTN") ?> >>" name="doImport">

        <?php $tabControl->End(); ?>

        <?php $tabControl->ShowWarnings("post_form", $message); ?>

        <?php if (!empty($arResult["LOG"])): ?>
            <?= BeginNote() ?>
            <?= implode('<br />', $arResult["LOG"]) ?>
            <?= EndNote() ?>
        <?php endif; ?>
    </form>

<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");