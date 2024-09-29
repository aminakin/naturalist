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
    private $rowId;
    private $filePath;
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
        $GLOBALS['BNOVO_FILES_WORKING'] = true;
        $this->getLastFile();

        $data = Json::decode(file_get_contents($this->filePath));

        if (empty($data)) {
            $GLOBALS['BNOVO_FILES_WORKING'] = false;
            return;
        }

        $result = $this->rest->updatePrices($data, $this->filePath, true);

        if ($result['code'] == 200) {
            $this->entity::update($this->rowId, ['UF_LOADED' => 1]);
        }

        $GLOBALS['BNOVO_FILES_WORKING'] = false;
    }

    /**
     * Возвращает содержимое последнего необработанного файла
     *
     * @return void
     */
    private function getLastFile(): void
    {
        $info = $this->entity::query()
            ->addSelect('*')
            ->where('UF_LOADED', 0)
            ?->fetch();

        if (empty($info)) {
            return;
        }

        $this->rowId = $info['ID'];
        $this->filePath = $info['UF_FILE_NAME'];
    }
}
