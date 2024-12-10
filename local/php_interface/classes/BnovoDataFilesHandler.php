<?php

namespace Naturalist;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Web\Json;
use Naturalist\Rest;
use Bitrix\Main\Loader;

Loader::includeModule('highloadblock');

/**
 * Класс для работы с файлами цен и размещений от Бново
 */
class BnovoDataFilesHandler
{
    private $arFiles = [];
    private $entity;
    private $rest;

    public function __construct()
    {
        $this->entity = HighloadBlockTable::compileEntity(BNOVO_FILES_HL_ENTITY)->getDataClass();
        $this->rest = new Rest();
    }

    /**
     * Обрабатывает файл из таблицы
     *
     * @return void
     */
    public function handleFile(): void
    {
        var_dump($GLOBALS['BNOVO_FILES_WORKING']);

        if ($GLOBALS['BNOVO_FILES_WORKING']) {
            echo 'Процесс занят';
            return;
        }

        $GLOBALS['BNOVO_FILES_WORKING'] = true;
        $this->getLastFiles();

        if (empty($this->arFiles)) {
            $GLOBALS['BNOVO_FILES_WORKING'] = false;
            return;
        }

        foreach ($this->arFiles as $file) {
            $data = Json::decode(file_get_contents($file['UF_FILE_NAME']));

            $result = $this->rest->updatePrices($data, $file['UF_FILE_NAME']);

            if ($result['code'] == 200) {
                $this->entity::update($file['ID'], ['UF_LOADED' => 1]);
            }
        }

        $GLOBALS['BNOVO_FILES_WORKING'] = false;

        echo "Агент отработал \r\n";
    }

    /**
     * Возвращает содержимое последнего необработанного файла
     *
     * @return void
     */
    private function getLastFiles(): void
    {
        $info = $this->entity::query()
            ->addSelect('*')
            ->where('UF_LOADED', 0)
            ->setLimit(3)
            ?->fetchAll();

        if (empty($info)) {
            return;
        }

        $this->arFiles = $info;
    }
}
