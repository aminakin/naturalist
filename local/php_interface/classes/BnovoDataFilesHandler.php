<?php

namespace Naturalist;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Web\Json;
use Naturalist\Rest;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

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
        // 5 мин
        $timeout = 300;
        $isBusy = Option::get("main", "bnovo_files_working");
        $lockTime = Option::get("main", "bnovo_files_lock_time");

        // Если флаг установлен и прошло больше таймаута — сбросьте его
        if ($isBusy == 'Y' && time() - (int)$lockTime > $timeout) {
            Option::set("main", "bnovo_files_working", 'N');
            echo "Флаг принудительно сброшен из-за таймаута\r\n";
        }


        if ($isBusy == 'Y') {
            echo "Процесс занят \r\n";
            return;
        }

        Option::set("main", "bnovo_files_working", 'Y');
        Option::set("main", "bnovo_files_lock_time", (string)time());

        try {
            $this->getLastFiles();

            if (empty($this->arFiles)) {
                Option::set("main", "bnovo_files_working", 'N');
                return;
            }


            foreach ($this->arFiles as $file) {
                $data = Json::decode(file_get_contents($file['UF_FILE_NAME']));

                $result = $this->rest->updatePrices($data, $file['UF_FILE_NAME']);

                if ($result['code'] == 200) {
                    $this->entity::update($file['ID'], ['UF_LOADED' => 1]);
                }
                var_export($result);
            }

            Option::set("main", "bnovo_files_working", 'N');

            echo "Агент отработал \r\n";
        } catch (\Exception $e) {
            error_log("Ошибка в обработке файлов: " . $e->getMessage());
            Option::set("main", "bnovo_files_working", 'N');
            echo "Ошибка: " . $e->getMessage() . "\r\n";
        }

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
            ->setLimit(10)
            ?->fetchAll();

        if (empty($info)) {
            return;
        }

        $this->arFiles = $info;
    }
}
