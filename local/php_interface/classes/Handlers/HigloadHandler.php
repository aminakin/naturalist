<?php

namespace Naturalist\Handlers;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Entity\EventResult;
use Bitrix\Main\Event;
use CUtil;

class HigloadHandler
{
    /**
     * Добавление обязательного кода для связки с инфоблоками
     *
     * @param Event $event
     * @return EventResult
     */
    public static function handle(Event $event): EventResult
    {
        $arFields = $event->getParameter("fields");

        self::setXmlID($arFields);

        $event->setParameter("fields", $arFields);

        $result = new EventResult();

        $result->modifyFields($arFields);

        return $result;
    }

    private static function setXmlID(&$arFields)
    {
        if (isset($arFields["UF_XML_ID"]) && is_string($arFields["UF_NAME"])) {
            $arFields["UF_XML_ID"] = CUtil::translit($arFields["UF_NAME"], 'ru', ["replace_space" => "-", "replace_other" => "-"]);
        }
    }
}