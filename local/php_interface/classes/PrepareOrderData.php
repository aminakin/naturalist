<?

namespace Naturalist;

use Bitrix\Main\Loader;

/**
 * Сбор данных по заказу для PDF
 */

class PrepareOrderData {
    public $arOrder = [];
    
    public function __construct() {
        Loader::includeModule("sale");
    }  
}