<?php

namespace Naturalist\Certificates;

use DateTime;
use DateInterval;
use Naturalist\HighLoadBlockHelper;

/**
 * Создание подарочного сертификата.
 */

class Create
{    
    private $hlEntity;    

    public function __construct()
    {        
        $this->hlEntity = new HighLoadBlockHelper('Certificates');        
    }

    /**
     * Генерирует код сертификата.
     *
     * @return string
     * 
     */
    private function generateCode() : string
    {
        return bin2hex(random_bytes(2)) . '-' . bin2hex(random_bytes(2)) . '-' . bin2hex(random_bytes(2));
    }
   
    /**
     * Добавляет новый сертификат.
     *
     * @param int $nominal
     * 
     * @return bool
     * 
     */
    public function add(int $nominal) : bool
    {
        $objDateTime = new DateTime();

        $arFields = [
            'UF_CODE' => $this->generateCode(),
            'UF_DATE_CREATE' => $objDateTime->format("d.m.Y"),
            'UF_DATE_UNTIL' => $objDateTime->add(new DateInterval('P1Y'))->format("d.m.Y"),
            'UF_COST' => $nominal
        ];

        if($this->hlEntity->add($arFields)) {
            return true;
        }

        return false;
    }
}
