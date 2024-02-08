<?php

namespace Naturalist\Certificates;

use Bitrix\Main\Loader;
use DateTime;
use Naturalist\HighLoadBlockHelper;

/**
 * Активация подарочного сертификата.
 */

class Activate
{
    private $hlEntity;
    private $certificate;
    private $userId;
    private $objDateTime;

    public function __construct()
    {        
        Loader::includeModule("sale");
        $this->hlEntity = new HighLoadBlockHelper('Certificates');
        $this->objDateTime = new DateTime();
    }

    /**
     * Активирует сертификат 
     *
     * @param string $certCode
     * @param int $userId
     * 
     * @return array
     * 
     */
    public function resolveCode(int $userId, string $certCode) : array {
        $result = [
            'ERROR_MESSAGE' => '',
            'SUCCESS_MESSAGE' => '',
            'STATUS' => 'ERROR'
        ];

        $this->userId = $userId;
        $this->getCert($certCode);
        if ($checkResult = $this->checkCert()) {
            $result['ERROR_MESSAGE'] = $checkResult;
            return $result;
        }

        if (!$this->addAmount()) {
            $result['ERROR_MESSAGE'] = 'Что-то пошло не так. Пожалуйста, обратитесь в поддержку';
            return $result;
        }

        if ($this->activateCert()) {
            $result['SUCCESS_MESSAGE'] = 'Сертификат успешно активирован';
            $result['STATUS'] = 'SUCCESS';
        }

        return $result;
    }

    /**
     * Зачисляет сумму на счёт пользователя          
     * 
     * @return int|false
     * 
     */
    private function addAmount() : ?int
    {
        return \CSaleUserAccount::UpdateAccount($this->userId, +intval($this->certificate['UF_COST']), 'RUB', 'Начисление по сертификату ID:' . $this->certificate['ID']);
    }

    /**
     * Возвращает данные по сертификату
     *
     * @param string $certCode
     * 
     * @return void
     * 
     */
    private function getCert(string $certCode) : void
    {        
        $this->hlEntity->prepareParamsQuery(['*'], [], ['UF_CODE' => strtolower($certCode)]);
        $this->certificate = $this->hlEntity->getData();
    }

    /**
     * Проверяет сертификат на валидность     
     * 
     * @return string
     * 
     */
    private function checkCert() : ?string
    {        
        if ($this->certificate == false) {
            return 'Сертификат не найден';
        }

        if ($this->certificate['UF_IS_ACTIVE'] == 1) {
            return 'Сертификат с таким кодом уже активирован';
        }

        if ($this->objDateTime->format("d.m.Y") > $this->certificate['UF_DATE_UNTIL']->format("d.m.Y")) {
            return 'Истёк срок действия сертификата';
        }

        return false;
    }

    /**
     * Обновляет данные по сетрификату
     *
     * @return void
     * 
     */
    private function activateCert() : bool
    {
        $arFields = [
            'UF_USER_ID' => $this->userId,
            'UF_IS_ACTIVE' => 1,
            'UF_DATE_ACTIVATE' => $this->objDateTime->format("d.m.Y"),
        ];

        if($this->hlEntity->update($this->certificate['ID'], $arFields)) {
            return true;
        }

        return false;
    }
}