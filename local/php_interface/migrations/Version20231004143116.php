<?php

namespace Sprint\Migration;


use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Loader;

class Version20231004143116 extends Version
{
    protected $description = "101021 | Добавить миграцию для установки цен / рубрик | превращает инфоблок номеров в торговый каталог";

    protected $moduleVersion = "4.4.1";

    public function up()
    {
        Loader::includeModule('catalog');
        $iblockId = IblockTable::getList(['filter'=>['CODE'=>'catalog']])->Fetch()["ID"];
        if (!\CCatalog::GetByID($iblockId))
        {
            $arFields = array(
                'IBLOCK_ID' => $iblockId,
            );
            $boolResult = \CCatalog::Add($arFields);
            if ($boolResult == false)
            {
                if ($ex = $APPLICATION->GetException())
                {
                    $strError = $ex->GetString();
                    ShowError($strError);
                }
            }
        }
    }

    public function down()
    {
        //your code ...
    }
}
