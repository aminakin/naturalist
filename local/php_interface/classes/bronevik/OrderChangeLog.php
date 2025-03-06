<?php

namespace Naturalist\bronevik;

use Bitrix\Highloadblock\HighloadBlockTable;
use \Bitrix\Main\Application;

class OrderChangeLog
{
    public function store(int $orderId, string $dateTime, array $data)
    {
        $entity = HighloadBlockTable::compileEntity(BRONEVIK_ORDER_CHANGE_LOG_HL_ENTITY);
        $entityDataClass = $entity->getDataClass();
        $scheme = Application::getInstance()->getContext()->getRequest()->isHttps() ? 'https' : 'http';
        $domain = Application::getInstance()->getContext()->getServer()->getHttpHost();

        $data = [
            'UF_ORDER_LINK' => $scheme.'://'.$domain.'/bitrix/admin/sale_order_view.php?filter=Y&lang=ru&ID='.$orderId,
            'UF_ORDER_ID' => $orderId,
            'UF_CHANGE_DATE' => date('d.m.Y H:i:s', strtotime($dateTime)),
            'UF_CHANGE_LOG' => implode("\n", $data),
        ];

        $entityDataClass::add($data);
    }
}