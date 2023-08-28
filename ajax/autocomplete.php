<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main;
use Bitrix\Main\Application;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;


CModule::IncludeModule('iblock');

if (!Main\Loader::includeModule('highloadblock')) {
    throw new Main\LoaderException(Loc::getMessage('IBLOCK_MODULE_NOT_INSTALLED'));
}

$request = Application::getInstance()->getContext()->getRequest();


/**
 * Sorting item flats
 */
if ($request->get('text') != null) {
    $text = urldecode($request->get('text'));
}

if ($text) {
    function array_unique_key($array, $key)
    {
        $tmp = $key_array = array();
        $i = 0;

        foreach ($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $tmp[$i] = $val;
            }
            $i++;
        }
        return $tmp;
    }

    $arFilterArea = [
        "IBLOCK_ID" => CATALOG_IBLOCK_ID,
        "ACTIVE" => "Y"
    ];

    $arFilterStreet = [
        "IBLOCK_ID" => CATALOG_IBLOCK_ID,
        "ACTIVE" => "Y"
    ];

    $arFilterObject = [
        "IBLOCK_ID" => CATALOG_IBLOCK_ID,
        "ACTIVE" => "Y"
    ];

    if ($text != null) {
        $reqText = (is_array($text)) ? $text : explode(',', $text);
        if (count($reqText) > 0) {
            $arFilterArea["%UF_REGION_NAME"] = $reqText;
            $arFilterStreet["%UF_ADDRESS"] = $reqText;
            $arFilterObject["%NAME"] = $reqText;
        }
    }

    $arAreas = [
        'type' => 'Регион',
        'id' => 'area',
        'list' => [],
    ];

    $arStreets = [
        'type' => 'Объекты размещения',
        'id' => 'id',
        'list' => [],
    ];

    /*$arObjects = [
        'type' => 'Объект',
        'id' => 'object',
        'list' => [],
    ];*/

    $resAreas = CIBlockSection::GetList(
        array(),
        $arFilterArea,
        false,
        array(
            "ID",
            "IBLOCK_ID",
            "CODE",
            "NAME",
            "PICTURE",
            "UF_*"
        ),
        false
    );

    while ($arArea = $resAreas->GetNext()) {
        $area = $arArea['UF_REGION_NAME'];
        if (in_array($area, $arAreas['list'])) {
            continue;
        }
        $arAreas['list'][] = [
            'id' => $area,
            'title' => $area
        ];
    }
    $arAreas['list'] = array_unique($arAreas['list']);

    $resStreets = CIBlockSection::GetList(
        false,
        $arFilterStreet,
        false,
        array(
            "ID",
            "IBLOCK_ID",
            "CODE",
            "NAME",
            "PICTURE",
            "UF_*"
        ),
        false
    );

    while ($arStreet = $resStreets->GetNext()) {
        /*$street = $arStreet['NAME']." ".$arStreet['UF_ADDRESS'];
        $streetText = "<strong>".$arStreet['NAME']."</strong><br>".$arStreet['UF_ADDRESS'];
        if (in_array($streetText, $arStreets['list'])) {
            continue;
        }*/
        $arStreets['list'][] = [
            'id' => $arStreet['ID'],
            'title' => $arStreet['NAME'],
            'footnote' => $arStreet['UF_ADDRESS']
        ];
    }

    //находим объекты

    $resObjects = CIBlockSection::GetList(
        false,
        $arFilterObject,
        false,
        array(
            "ID",
            "IBLOCK_ID",
            "CODE",
            "NAME",
            "PICTURE",
            "UF_*"
        ),
        false
    );

    while ($arObject = $resObjects->GetNext()) {
        /*$street = $arObject['NAME']." ".$arObject['UF_ADDRESS'];
        $streetText = "<strong>".$arObject['NAME']."</strong><br>".$arObject['UF_ADDRESS'];
        if (in_array($streetText, $arStreets['list'])) {
            continue;
        }*/
        $arStreets['list'][] = [
            'id' => $arObject['ID'],
            'title' => $arObject['NAME'],
            'footnote' => $arObject['UF_ADDRESS']
        ];
    }

    $arStreets['list'] = array_unique_key($arStreets['list'], 'title');

    if ($arAreas['list']) {
        $arReturn[] = $arAreas;
    }
    if ($arStreets['list']) {
        $arReturn[] = $arStreets;
    }
    /*if ($arObjects['list']) {
        $arReturn[] = $arObjects;
    }*/

    ?>
    <?
    if(isset($arReturn) && !empty($arReturn)){
        echo $encode = json_encode($arReturn, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }else {
        echo $encode = json_encode(["messageType" => "error", "messageText" => "Объекты или регион не найдены"], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
} else {
    echo "Повторите запрос";
}
?>