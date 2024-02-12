<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use Bitrix\Main\Loader;
use Bitrix\Iblock;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Context;
use Bitrix\Main\Type\Date;
use Naturalist\Certificates\CatalogHelper;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\Delivery\Services;

Loc::loadMessages(__FILE__);

class CertBuy extends \CBitrixComponent 
{
    private $certificates;

    public function onPrepareComponentParams($arParams)
	{        
        return $arParams;
    }
    
    protected function prepareResultArray()
	{
        Loader::includeModule("sale");

        $this->certificates = new CatalogHelper();

        $this->arResult = [
            'CERTIFICATES' => $this->getCertificates(),
            'VARIANT' => $this->certificates->hlVariantsValues,
            'VARIANT_EL' => $this->certificates->hlElVariantsValues,
            'POCKET' => $this->certificates->hlPocketsValues,
            'PAY_SYSTEMS' => PaySystem\Manager::getList([
                'filter'  => [
                    'ACTIVE' => 'Y',
                    '!ACTION_FILE' => 'inner',
                    'CODE' => 'CERT',
                ]
            ])->fetchAll(),
            'DELIVERIES' => Services\Table::getList([
                'filter' => [
                    'ACTIVE'=>'Y',
                    'PARENT_ID' => CERT_DELIVERY_PARENT_ID,
                ]
            ])->fetchAll(),
        ];
    }

    public function executeComponent()
	{
		global $APPLICATION;		
        $this->prepareResultArray();        
        $this->handleRequest();
		$this->includeComponentTemplate();
	}

    /**
     * Возвращает список сертификатов из каталога
     *
     * @return array
     * 
     */
    private function getCertificates() : array
    {        
        return $this->certificates->getProducts();
    }

    /**
     * Обрабатывает входящий POST запрос     
     */
    protected function handleRequest() 
    {
        $request = Context::getCurrent()->getRequest();
        $postList = $request->getPostList()->toArray();
        xprint($postList);        
    }
}