<?php

namespace Sprint\Migration;

use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable;
use CFile;

class Version20240514123440 extends Version
{
    protected $description = "123440 | Backend. Таблица для хранения обратной стороны сертификатов";
    protected $moduleVersion = "4.4.1";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();

        if (!Loader::includeModule('highloadblock')) {
            throw new \Exception('Не удалось подключить модуль highloadblock');
        }

        $recordId = 3;
        $filePath = $_SERVER['DOCUMENT_ROOT'] . '/upload/certs/dobro.03.jpg';

        if (file_exists($filePath)) {
            $fileArray = CFile::MakeFileArray($filePath);
            if ($fileArray) {
                $fileId = CFile::SaveFile($fileArray, 'uf');

                if ($fileId) {
                    $hlblockId = 21;
                    $entity = HighloadBlockTable::getById($hlblockId)->fetch();

                    if ($entity) {
                        $entityClass = HighloadBlockTable::compileEntity($entity)->getDataClass();
                        $record = $entityClass::getById($recordId)->fetch();

                        if (!$record) {
                            throw new \Exception("Запись с ID $recordId не найдена в highload-блоке.");
                        }

                        $data = [
                            'UF_FILE' => $fileId,
                            'UF_IMG_TO_CERT' => $fileId
                        ];

                        $result = $entityClass::update($recordId, $data);
                        if ($result->isSuccess()) {
                            $this->out('Запись обновлена успешно.');
                            $updatedRecord = $entityClass::getById($recordId)->fetch();
                            $fileData = CFile::GetFileArray($updatedRecord['UF_FILE']);
                        } else {
                            throw new \Exception('Ошибка при обновлении записи: ' . implode(', ', $result->getErrorMessages()));
                        }
                    } else {
                        throw new \Exception('Highload block не найден.');
                    }
                } else {
                    throw new \Exception('Ошибка загрузки файла.');
                }
            } else {
                throw new \Exception('Ошибка при создании массива файла.');
            }
        } else {
            throw new \Exception('Файл не найден.');
        }

        return true;
    }


public function down()
    {
        // ваш код для отката миграции
    }
}
