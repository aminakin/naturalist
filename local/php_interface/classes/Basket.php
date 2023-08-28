<?

namespace Naturalist;

use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Loader,
    Bitrix\Main\Mail\Event;

use CSaleBasket;
use CSaleUser;

defined("B_PROLOG_INCLUDED") && B_PROLOG_INCLUDED === true || die();

/**
 * @global CMain $APPLICATION
 * @global CUser $USER
 */
class Baskets
{
    public function __construct()
    {
        Loader::includeModule("sale");
    }

    public function get($userIdF = false)
    {
        global $arUser, $userId;
        $products = new Products();

        if(!empty($userIdF)) {
            $fUserId = CSaleUser::GetList(array("USER_ID" => (int)$userIdF))["ID"];
        } else {
            $fUserId = (intval($userId) > 0) ? CSaleUser::getFUserCode() : CSaleBasket::GetBasketUserID();
        }
        //xprint($fUserId);
        //$fUserId = $userIdF;
        $arBasketItems = array();
        $rsBasketItems = CSaleBasket::GetList(
            array(
                "NAME" => "ASC",
                "ID" => "ASC"
            ),
            array(
                "FUSER_ID" => $fUserId,
                "LID" => SITE_ID,
                "ORDER_ID" => "NULL"
            ),
            false,
            false,
            array(
                "ID",
                "PRODUCT_ID",
                "NAME",
                "QUANTITY",
                "PRICE"
            )
        );

        $totalCount = 0;
        $totalPrice = 0;
        while ($arBasketItem = $rsBasketItems->Fetch()) {
            $arProduct = $products->get($arBasketItem["PRODUCT_ID"]);
            $arBasketItem["ITEM"] = $arProduct;

            $rsProps = CSaleBasket::GetPropsList(
                array("SORT" => "ASC"),
                array("BASKET_ID" => $arBasketItem["ID"])
            );
            $arBasketProps = array();
            while ($arProp = $rsProps->Fetch()) {
                $arBasketProps[$arProp["CODE"]] = $arProp["VALUE"];
            }
            $arBasketItem["PROPS"] = $arBasketProps;

            $arBasketItems[] = $arBasketItem;

            $totalCount += $arBasketItem["QUANTITY"];
            $totalPrice += $arBasketItem["PRICE"] * $arBasketItem["QUANTITY"];
        }

        $arData = array(
            "TOTAL_COUNT" => $totalCount,
            "TOTAL_PRICE" => $totalPrice,
        );

        return array(
            "ITEMS" => $arBasketItems,
            "DATA" => $arData
        );
    }

    public function add($productId, $count, $price, $arProps)
    {
        global $arUser, $userId;

        //$fUserId = (intval($userId) > 0) ? CSaleUser::getFUserCode() : CSaleBasket::GetBasketUserID();
        $fUserId = (intval($userId) > 0) ? CSaleUser::getFUserCode() : false;
        $products = new Products();
        $arProduct = $products->get($productId);
        self::deleteAll();

        $arFields = array(
            'PRODUCT_ID' => $productId,
            'PRODUCT_PRICE_ID' => 1,
            'PRICE' => $price,
            'CUSTOM_PRICE' => 'Y',
            'CURRENCY' => 'RUB',
            'QUANTITY' => $count,
            'LID' => SITE_ID,
            'MODULE' => 'sale',
            'NAME' => $arProduct['NAME'],
            //'FUSER_ID' => $fUserId,
            'PROPS' => $arProps
        );
        if(!empty($fUserId)){
            $arFields["FUSER_ID"] = $fUserId;
        }

        $basketProductId = CSaleBasket::Add($arFields);

        if ($basketProductId) {
            return json_encode([
                "ID" => $basketProductId,
                "MESSAGE" => "Товар успешно добавлен в корзину.",
                "RELOAD" => true
            ]);

        } else {
            return json_encode([
                "ERROR" => "Произошла ошибка при добавлении товара в корзину."
            ]);
        }
    }

    public function update($productId, $count)
    {
        $res = CSaleBasket::Update($productId, [
            "QUANTITY" => intval($count),
        ]);

        if ($res) {
            return json_encode([
                "MESSAGE" => "Товар успешно обновлён.",
                "RELOAD" => true
            ]);

        } else {
            return json_encode([
                "ERROR" => "Произошла ошибка при обновлении товара."
            ]);
        }
    }

    public function delete($productId)
    {
        $res = CSaleBasket::Delete($productId);

        if ($res) {
            return json_encode([
                "MESSAGE" => "Товар успешно удалён.",
                "RELOAD" => true
            ]);

        } else {
            return json_encode([
                "ERROR" => "Произошла ошибка при удалении товара."
            ]);
        }
    }

    public static function deleteAll()
    {
        global $arUser, $userId;
        $fUserId = (intval($userId) > 0) ? CSaleUser::getFUserCode() : CSaleBasket::GetBasketUserID();

        $res = \CSaleBasket::DeleteAll($fUserId);

        if ($res) {
            return json_encode([
                "MESSAGE" => "Корзина успешно очищена.",
                "RELOAD" => true
            ]);

        } else {
            return json_encode([
                "ERROR" => "Произошла ошибка при очищении корзины."
            ]);
        }
    }
}
