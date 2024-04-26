<?
// подключим все необходимые файлы:
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php"); // первый общий пролог
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/add_object_bnovo/include.php"); // инициализация модуля
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/add_object_bnovo/prolog.php"); // пролог модуля

use Naturalist\Bnovo;

// подключим языковой файл
IncludeModuleLangFile(__FILE__);

// сформируем список закладок
$aTabs = [
    [
        "DIV" => "edit1",
        "TAB" => GetMessage("MODULE_TAB"),
        "ICON" => "main_user_edit",
        "TITLE" => GetMessage("MODULE_TITLE")
    ]
];

$tabControl = new CAdminTabControl("tabControl", $aTabs);

$ID = intval($ID);      // идентификатор редактируемой записи
$message = null;        // сообщение об ошибке
$bVarsFromForm = false; // флаг "Данные получены с формы", обозначающий, что выводимые данные получены с формы, а не из БД.

// ******************************************************************** //
//                ОБРАБОТКА ИЗМЕНЕНИЙ ФОРМЫ                             //
// ******************************************************************** //

if ($REQUEST_METHOD == "POST" && check_bitrix_sessid() && !empty($_REQUEST["object_id"])) {

    $bnovo = new Bnovo();
    $object = $bnovo->updatePublicObject($_REQUEST["object_id"], $_REQUEST["only_rooms"], $_REQUEST["only_tariffs"]);

	if (!empty($object["MESSAGE"]["ERRORS"])) {
        foreach ($object["MESSAGE"]["ERRORS"] as $error) {
            $arMsg[] = ["id" => "NULL", "text" => $error];
        }

        $e = new CAdminException($arMsg);
        $GLOBALS["APPLICATION"]->ThrowException($e);

        $message = new CAdminMessage($object["MESSAGE"]["ERRORS"], $e);
    }
}

// ******************************************************************** //
//                ВЫБОРКА И ПОДГОТОВКА ДАННЫХ ФОРМЫ                     //
// ******************************************************************** //
?>

<?
// ******************************************************************** //
//                ВЫВОД ФОРМЫ                                           //
// ******************************************************************** //
$APPLICATION->SetTitle(GetMessage("MODULE_TITLE"));
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>

<?
// если есть сообщения об ошибках или об успешном сохранении - выведем их.

if (!$message && !empty($object["MESSAGE"]["SUCCESS"])) {
    $str = $object["MESSAGE"]["SUCCESS"];

    $message = new CAdminMessage(
        [
            "MESSAGE" => $str,
            "TYPE" => "OK"
        ]
    );

    echo $message->Show();
} elseif ($message) {
    echo $message->Show();
}

?>

<?
// далее выводим собственно форму
?>
<form method="POST" Action="<? echo $APPLICATION->GetCurPage() ?>" ENCTYPE="multipart/form-data" name="post_form">
    <? // проверка идентификатора сессии ?>
    <? echo bitrix_sessid_post(); ?>
    <?
    // отобразим заголовки закладок
    $tabControl->Begin();
    ?>
    <?
    $tabControl->BeginNextTab();
    ?>
    <tr>
        <td class="field-data">
            <input type="text" name="object_id" id="object_id" value="<?= $_REQUEST["object_id"] ?>" style="width:40%"/>
        </td>
    </tr>    
    <tr>
        <td style="padding: 10px 0 0 0;"><?=GetMessage("MODULE_INSERT_ID")?></td>
    </tr>
    <tr>
        <td style="padding: 10px 0 0 0;">
            <label>
                <input type="checkbox" name="only_rooms" id="only_rooms" value="Y">
                <span><?=GetMessage("MODULE_ONLY_ROOMS")?></span>
            </label>
        </td>
    </tr>    
    <tr>
        <td style="padding: 10px 0 0 0;">
            <label>
                <input type="checkbox" name="only_tariffs" id="only_tariffs" value="Y">
                <span><?=GetMessage("MODULE_ONLY_TARIFFS")?></span>
            </label>
        </td>
    </tr>    
    <?
    // завершение формы - вывод кнопок сохранения изменений
    $tabControl->Buttons();
    ?>
    <? echo bitrix_sessid_post(); ?>
    <input type="hidden" name="lang" value="<?= LANG ?>">
    <input type="submit" value="<?= GetMessage("MODULE_IMPORT_BTN") ?> &gt;&gt;" name="doImport">
    <?
    // завершаем интерфейс закладки
    $tabControl->End();
    ?>

    <?
    // дополнительное уведомление об ошибках - вывод иконки около поля, в котором возникла ошибка
    $tabControl->ShowWarnings("post_form", $message);
    ?>

    <?
    if ($arResult["LOG"]):
        echo BeginNote(); ?>
        <? echo implode('<br />', $arResult["LOG"]) ?>
        <? echo EndNote(); ?>
    <? endif ?>
</form>

<?
// завершение страницы
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>
