<?

namespace Naturalist;

use \Bitrix\Main,
    \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\UserField;

class CatalogCustomProp
{

    /**
     * Метод возвращает массив описания собственного типа свойств
     * @return array
     */
    public function GetUserTypeDescription()
    {
        return array(
            "USER_TYPE_ID" => 'catalogcustomprop', //Уникальный идентификатор типа свойств
            "CLASS_NAME" => __CLASS__,
            "DESCRIPTION" => 'Привязка к свойству каталога',
            "BASE_TYPE" => 'enumeration',
        );
    }

    /**
     * Получаем список значений
     * @param $arUserField
     * @return array|bool|\CDBResult
     */
    public function GetList($arUserField)
    {
        return CIBlockProperty::GetList(
			['NAME' => 'ASC'],
			['IBLOCK_ID' => $arUserField["SETTINGS"]["IBLOCK_ID"], 'ACTIVE' => 'Y']
		);
	
    }    
}
