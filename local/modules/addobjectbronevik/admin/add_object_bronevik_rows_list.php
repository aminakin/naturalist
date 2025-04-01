<?php
use Bitrix\Main\Entity\Query;
use Bitrix\Main\Entity\ExpressionField;
use Bitrix\Highloadblock as HL;

// admin initialization
define("ADMIN_MODULE_NAME", "addobjectbronevik");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

global $APPLICATION, $USER, $USER_FIELD_MANAGER;

IncludeModuleLangFile(__FILE__);

if (!CModule::IncludeModule(ADMIN_MODULE_NAME))
{
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

// get entity settings
$APPLICATION->SetTitle('Броневик. Отели для добавления.');

$entity = \Local\AddObjectBronevik\Orm\AddObjectBronevikTable::getEntity();

/** @var HL\DataManager $entity_data_class */
$entity_data_class = $entity->getDataClass();
$full_entity_table_name = \Local\AddObjectBronevik\Orm\AddObjectBronevikTable::getTableName();
$entity_table_name = str_replace('b_', '', $full_entity_table_name);

$sTableID = 'tbl_'.$entity_table_name;
$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

$arHeaders = array(
    [
        'id' => 'ID',
        'content' => 'ID',
        'sort' => 'ID',
        'default' => true,
    ],
    [
        'id' => 'NAME',
        'content' => 'NAME',
        'title' => 'NAME',
        'sort' => 'NAME',
        'default' => true,
    ],
    [
        'id' => 'CODE',
        'content' => 'CODE',
        'title' => 'CODE',
        'sort' => 'CODE',
    ],
    [
        'id' => 'TYPE',
        'content' => 'TYPE' ,
        'title' => 'TYPE',
        'sort' => 'TYPE',
    ],
    [
        'id' => 'COUNTRY',
        'content' => 'COUNTRY',
        'title' => 'COUNTRY',
        'sort' => 'COUNTRY',
    ],
    [
        'id' => 'CITY',
        'content' => 'CITY',
        'title' => 'CITY',
        'sort' => 'CITY',
    ],
    [
        'id' => 'ADDRESS',
        'content' => 'ADDRESS',
        'title' => 'ADDRESS',
        'sort' => 'ADDRESS',
    ],
    [
        'id' => 'ZIP',
        'content' => 'ZIP',
        'title' => 'ZIP',
        'sort' => 'ZIP',
    ],
    [
        'id' => 'LAST_MODIFIED',
        'content' => 'LAST_MODIFIED',
        'title' => 'LAST_MODIFIED',
        'sort' => 'LAST_MODIFIED',
    ],
);

// show all columns by default
foreach ($arHeaders as &$arHeader)
{
    $arHeader['default'] = true;
}
unset($arHeader);

$lAdmin->AddHeaders($arHeaders);

if (!in_array($by, $lAdmin->GetVisibleHeaderColumns(), true))
{
    $by = 'ID';
}

// add filter
$filter = null;

$filterFields = array('find_id', 'find_name', 'find_code', 'find_type');
$filterValues = array();
$filterTitles = array('ID', 'NAME', 'CODE', 'TYPE');

$filter = $lAdmin->InitFilter($filterFields);

if (!empty($find_id))
{
    $filterValues['ID'] = $find_id;
}
if (!empty($find_name))
{
    $filterValues['NAME'] = '%' . $find_name . '%';
}
if (!empty($find_code))
{
    $filterValues['CODE'] = $find_code;
}
if (!empty($find_type))
{
    $filterValues['TYPE'] = $find_type;
}

$filter = new CAdminFilter(
    $sTableID."_filter_id",
    $filterTitles
);


// group actions

$arr = array();
$arr['load'] = [
        'action' => 'addHotel()',
        'type' => 'button',
        'name' => 'Загрузить',
        'value' => 'load',
    ];
$lAdmin->AddGroupActionTable($arr);

// select data
/** @var string $order */
$order = mb_strtoupper($order);

$usePageNavigation = true;
if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'excel')
{
    $usePageNavigation = false;
}
else
{
    $navyParams = CDBResult::GetNavParams(CAdminResult::GetNavSize(
        $sTableID,
        array('nPageSize' => 20, 'sNavID' => $APPLICATION->GetCurPage())
    ));
    if ($navyParams['SHOW_ALL'])
    {
        $usePageNavigation = false;
    }
    else
    {
        $navyParams['PAGEN'] = (int)$navyParams['PAGEN'];
        $navyParams['SIZEN'] = (int)$navyParams['SIZEN'];
    }
}
$selectFields = $lAdmin->GetVisibleHeaderColumns();
if (!in_array('ID', $selectFields))
    $selectFields[] = 'ID';
$getListParams = array(
    'select' => $selectFields,
    'filter' => $filterValues,
    'order' => array($by => $order)
);
unset($filterValues, $selectFields);
if ($usePageNavigation)
{
    $getListParams['limit'] = $navyParams['SIZEN'];
    $getListParams['offset'] = $navyParams['SIZEN']*($navyParams['PAGEN']-1);
}

if ($usePageNavigation)
{
    $countQuery = new Query($entity_data_class::getEntity());
    $countQuery->addSelect(new ExpressionField('CNT', 'COUNT(1)'));
    $countQuery->setFilter($getListParams['filter']);
    $totalCount = $countQuery->setLimit(null)->setOffset(null)->exec()->fetch();
    unset($countQuery);
    $totalCount = (int)$totalCount['CNT'];
    if ($totalCount > 0)
    {
        $totalPages = ceil($totalCount/$navyParams['SIZEN']);
        if ($navyParams['PAGEN'] > $totalPages)
            $navyParams['PAGEN'] = $totalPages;
        $getListParams['limit'] = $navyParams['SIZEN'];
        $getListParams['offset'] = $navyParams['SIZEN']*($navyParams['PAGEN']-1);
    }
    else
    {
        $navyParams['PAGEN'] = 1;
        $getListParams['limit'] = $navyParams['SIZEN'];
        $getListParams['offset'] = 0;
    }
}
$rsData = new CAdminResult($entity_data_class::getList($getListParams), $sTableID);
if ($usePageNavigation)
{
    $rsData->NavStart($getListParams['limit'], $navyParams['SHOW_ALL'], $navyParams['PAGEN']);
    $rsData->NavRecordCount = $totalCount;
    $rsData->NavPageCount = $totalPages;
    $rsData->NavPageNomer = $navyParams['PAGEN'];
}
else
{
    $rsData->NavStart();
}

$typeList = [
        'hotel' => 'Отель',
        'hostel' => 'Хостел',
        'city-serviced-apartments' => 'Апартаменты в разных районах города',
        'mini-hotel' => 'Мини-отель',
        'health-resort' => 'Санаторий',
        'turbaza' => 'Турбаза',
        'pension' => 'Пансионат',
        'living-quarters' => 'Жилые помещения',
        'furnished-rooms' => 'Меблированные комнаты',
        'aparthotel' => 'Апарт-отель',
        'motel' => 'Мотель',
        'camping' => 'Кемпинг',
        'glamping' => 'Глэмпинг',
];
// build list
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("PAGES")));
while($arRes = $rsData->NavNext(true, "f_"))
{
    $arRes['TYPE'] = $typeList[$arRes['TYPE']];
    $row = $lAdmin->AddRow($f_ID, $arRes);
//    $row->AddViewField('ID', '<a href="' . 'highloadblock_row_edit.php?ENTITY_ID='.$hlblock['ID'].'&ID='.$f_ID.'&lang='.LANGUAGE_ID . '">'.$f_ID.'</a>');

//    $USER_FIELD_MANAGER->AddUserFields('HLBLOCK_'.$hlblock['ID'], $arRes, $row);

//    $arActions = array();
//
//    $arActions[] = array(
//        'ICON' => 'edit',
//        'TEXT' => GetMessage($canEdit ? 'MAIN_ADMIN_MENU_EDIT' : 'MAIN_ADMIN_MENU_VIEW'),
//        'ACTION' => $lAdmin->ActionRedirect('highloadblock_row_edit.php?ENTITY_ID='.$hlblock['ID'].'&ID='.$f_ID.'&lang='.LANGUAGE_ID),
//        'DEFAULT' => true
//    );
//    if ($canEdit)
//    {
//        $arActions[] = array(
//            'ICON' => 'copy',
//            'TEXT' => GetMessage('MAIN_ADMIN_MENU_COPY'),
//            'ACTION' => $lAdmin->ActionRedirect('highloadblock_row_edit.php?ENTITY_ID='.$hlblock['ID'].'&ID='.$f_ID.'&lang='.LANGUAGE_ID.'&action=copy')
//        );
//    }
//    if ($canDelete)
//    {
//        $arActions[] = array(
//            'ICON'=>'delete',
//            'TEXT' => GetMessage('MAIN_ADMIN_MENU_DELETE'),
//            'ACTION' => 'if(confirm(\''.GetMessageJS('HLBLOCK_ADMIN_DELETE_ROW_CONFIRM').'\')) '.
//                $lAdmin->ActionRedirect('highloadblock_row_edit.php?action=delete&ENTITY_ID='.$hlblock['ID'].'&ID='.$f_ID.'&lang='.LANGUAGE_ID.'&'.bitrix_sessid_get())
//        );
//    }
//
//    $row->AddActions($arActions);
}

// force disable edit
if (!$canEdit)
{
    $eventManager = \Bitrix\Main\EventManager::getInstance();
    $eventManager->addEventHandler('main', 'OnAdminListDisplay',
        function(&$list)
        {
            $list->bCanBeEdited = false;
        }
    );
}

// view

$lAdmin->CheckListMode();

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

\Bitrix\Main\UI\Extension::load("ui.stepprocessing");
?>
    <form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>">
        <?
        $filter->Begin();
        ?>
        <tr>
            <td>ID</td>
            <td><input type="text" name="find_id" size="47" value="<?echo htmlspecialcharsbx($find_id)?>"></td>
        </tr>
        <tr>
            <td><font class="tableheadtext"><b>NAME:</b></td>
            <td><input type="text" name="find_name" value="<?echo ($find_name <> '') ? htmlspecialcharsbx($find_name) : ""?>" size="38"></td>
        </tr>
        <tr>
            <td><font class="tableheadtext"><b>CODE:</b></td>
            <td><input type="text" name="find_code" value="<?echo ($find_code <> '') ? htmlspecialcharsbx($find_code) : ""?>" size="38"></td>
        </tr>
        <tr>
            <td><font class="tableheadtext"><b>TYPE:</b></td>
            <td>
                <select name="find_type">
                    <option value="">...</option>
                    <?php
                    foreach ($typeList as $key => $value) { ?>
                        <option value="<?= $key ?>"><?= $value ?></option>
                    <?php } ?>
                </select>
<!--                <input type="text" name="find_type" value="--><?//echo ($find_type <> '') ? htmlspecialcharsbx($find_type) : ""?><!--" size="38"></td>-->
        </tr>
        <?
        $filter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage(), "form"=>"find_form"));
        $filter->End();
        ?>
    </form>
    <script type="text/javascript">
        // import { Process, ProcessManager } from 'ui.stepprocessing';
        BX.UI.StepProcessing.ProcessManager.create(<?= \Bitrix\Main\Web\Json::encode([
            'id' => 'import', // Уникальный идентификатор процесса в контексте страницы
            'controller' => 'bitrix:addobjectbronevik.api.hotel', // дефолтый контроллер процесса
            'messages' => [
                // Для всех сообщений уже имеются фразы по-умолчанию.
                // Переопределение фразы необходимо для кастомизации под конкретную задачу.
                'DialogTitle' => 'Импорт', // Заголовок диалога
                'DialogSummary' => 'Загрузить выбранные отели в каталог', // Аннотация на стартовом диалоге
                'DialogStartButton' => 'Старт', // Кнопка старт
                'DialogStopButton' => 'Стоп', // Кнопка стоп
                'DialogCloseButton' => 'Закрыть', // Кнопка закрыть
                'RequestCanceling' => 'Прерываю...', // Аннотация, выводимая при прерывании процесса. По-умолчанию: 'Прерываю...'"
                'RequestCanceled' => 'Прервано', // Аннотация, выводимая если процесс прерван
                'RequestCompleted' => 'Успешно', // Текст на финальном диалоге успешного завершения
//                'DialogExportDownloadButton' => 'Скачать', // Кнопка для скачивания файла
//                'DialogExportClearButton' => 'Удалить', // Кнопка удаления файла
            ],

            // Очередь заданий
            'queue' => [
                [
                    'controller' => 'bitrix:addobjectbronevik.api.hotel', // отдельный контроллер на шаге /bitrix/services/main/ajax.php?action=add_object_bronevik.api.hotel.store
                    'params' => ['params' => ['item' => 55, 'items' => 555]], // дополнительные параметры, добавляемые в запрос
                    'action' => 'init',
                    'title' => \Bitrix\Main\Localization\Loc::getMessage('INDEX', ['#NUM#' => 3, '#LEN#' => 3]),
                    'progressBarTitle' => \Bitrix\Main\Localization\Loc::getMessage('INDEX_PROGRESS'),
                ],
                [
                    'controller' => 'bitrix:addobjectbronevik.api.hotel', // отдельный контроллер на шаге /bitrix/services/main/ajax.php?action=add_object_bronevik.api.hotel.store
                    'params' => ['params' => ['item' => 55, 'items' => 555]], // дополнительные параметры, добавляемые в запрос
                    'action' => 'store',
                    'title' => \Bitrix\Main\Localization\Loc::getMessage('INDEX', ['#NUM#' => 3, '#LEN#' => 3]),
                    'progressBarTitle' => \Bitrix\Main\Localization\Loc::getMessage('INDEX_PROGRESS'),
                ],
                [
                    'action' => 'finalize',
                    'finalize' => true, // финальный шаг не отображается пользователю
                ],
            ],

            // параметры, добавляемые в запрос на всех хитах к контроллеру
            'params' => [
//                'path' => "/bitrix/modules", // список {name: value}
//                ...
            ],
            'optionsFields' => [
                    'loadHotels' => [
                        'name' => 'loadHotels',
                        'type' => 'checkbox',
                        'title' => 'Загружать/обновлять отели',
                        'value' => 1,
                    ],
                    'loadHotelRooms' => [
                        'name' => 'loadHotelRooms',
                        'type' => 'checkbox',
                        'title' => 'Загружать/обновлять компанты в отелях',
                        'value' => 1,
                    ],
                    'loadHotelsMinimalPrice' => [
                        'name' => 'loadHotelsMinimalPrice',
                        'type' => 'checkbox',
                        'title' => 'Загружать/обновлять минимальную цену отелей',
                        'value' => 1,
                    ],
            ],
            'eventHandlers' => [
                'StateChanged' => '', // js-function обработчик на события StateChanged
                'RequestStart' => '', // js-function обработчик на события RequestStart
                'RequestStop' => '', // js-function обработчик на события RequestStop
                'RequestFinalize' => '', // js-function обработчик на события RequestFinalize
            ],
            'setHandlers' => [
                    'RequestStart' => 'function (actionData) {          
                      debugger;
                        console.log(actionData)
                    }'
            ],
            'showButtons' => [
                'start' => true, // вывести кнопку Старт. По-умолчанию: true
                'stop' => true, // вывести кнопку Стоп. По-умолчанию: true
                'close' => true, // вывести кнопку Закрыть. По-умолчанию: true
            ],
        ])?>);



        function addHotel()
        {
            var oForm = document.form_<?= $sTableID ?>;
            var expType = oForm.action_target.checked;

            var hotelIds = [];
            if(!expType)
            {
                var num = oForm.elements.length;
                for (var i = 0; i < num; i++)
                {
                    if(oForm.elements[i].tagName.toUpperCase() === "INPUT"
                        && oForm.elements[i].type.toUpperCase() === "CHECKBOX"
                        && oForm.elements[i].name.toUpperCase() === "ID[]"
                        && oForm.elements[i].checked === true)
                    {
                        hotelIds.push(oForm.elements[i].value);
                    }
                }
            }

            var process = BX.UI.StepProcessing.ProcessManager.get('import')
                process.setHandler('RequestStart', function(FormData ){
                    let initialOptions = this.getDialog().getOptionFieldValues()
                    if (initialOptions.loadHotels === undefined) {
                        FormData.delete('loadHotels')
                    }
                    if (initialOptions.loadHotelRooms === undefined) {
                        FormData.delete('loadHotelRooms')
                    }
                    if (initialOptions.loadHotelsMinimalPrice === undefined) {
                        FormData.delete('loadHotelsMinimalPrice')
                    }
                        console.log(this.getDialog().getOptionFieldValues())
                },)
                process.setParams({
                    'expType': expType,
                    'hotelIds': hotelIds
                })
                process.showDialog()
        }
    </script>
<?

$lAdmin->DisplayList();

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");