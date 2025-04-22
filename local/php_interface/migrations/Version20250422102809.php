<?php

namespace Sprint\Migration;

use Bitrix\Main\Loader;
use Bitrix\Sale\PaySystem\Manager;
use Bitrix\Sale\Services\PaySystem\Restrictions\Manager as RestrictionsManager;
use Bitrix\Main\Localization\Loc;

class Version20250422102809 extends Version
{
    protected $author = "admin";
    protected $description = "121705 | Списание сертификатов для розыгрышей | Создание платежек для админов";
    protected $moduleVersion = "4.18.0";

    private $paymentSystemsData = [
        [
            'NAME' => 'Списание',
            'CODE' => 'write_off',
            'DESCRIPTION' => 'Списание средств (не попадает в кассу)',
            'FUNCTIONALITY' => 'paid', // Оплачено
            'ACTIVE' => 'N', // Неактивно
        ],
        [
            'NAME' => 'Аладдин',
            'CODE' => 'aladdin',
            'DESCRIPTION' => 'Оплата через Аладдин (не попадает в кассу)',
            'FUNCTIONALITY' => 'paid', // Оплачено
            'ACTIVE' => 'N', // Неактивно
        ],
        [
            'NAME' => 'Яндекс',
            'CODE' => 'yandex',
            'DESCRIPTION' => 'Оплата через Яндекс (не попадает в кассу)',
            'FUNCTIONALITY' => 'paid', // Оплачено
            'ACTIVE' => 'N', // Неактивно
        ],
        [
            'NAME' => 'FlowWow',
            'CODE' => 'flowwow',
            'DESCRIPTION' => 'Оплата через FlowWow (не попадает в кассу)',
            'FUNCTIONALITY' => 'paid', // Оплачено
            'ACTIVE' => 'N', // Неактивно
        ],
        [
            'NAME' => 'Гифтери',
            'CODE' => 'giftery',
            'DESCRIPTION' => 'Оплата через Гифтери (ожидается оплата)',
            'FUNCTIONALITY' => 'awaiting_payment', // Принят, ожидается оплата
            'ACTIVE' => 'N', // Неактивно
        ],
        [
            'NAME' => 'Дигифт',
            'CODE' => 'digift',
            'DESCRIPTION' => 'Оплата через Дигифт (ожидается оплата)',
            'FUNCTIONALITY' => 'awaiting_payment', // Принят, ожидается оплата
            'ACTIVE' => 'N', // Неактивно
        ],
    ];

    public function __construct()
    {
        Loader::includeModule('sale');
    }

    public function up()
    {
        foreach ($this->paymentSystemsData as $data) {
            $this->createPaymentSystem($data);
        }

        return true;
    }

    public function down()
    {
        foreach ($this->paymentSystemsData as $data) {
            $paymentSystemId = $this->findPaymentSystemIdByCode($data['CODE']);

            if ($paymentSystemId) {
                $result = Manager::delete($paymentSystemId);

                if ($result->isSuccess()) {
                    echo "Платежная система '{$data['NAME']}' успешно удалена.\n";
                } else {
                    echo "Ошибка при удалении платежной системы '{$data['NAME']}': " . implode(', ', $result->getErrorMessages()) . "\n";
                }
            } else {
                echo "Платежная система '{$data['NAME']}' не найдена для удаления.\n";
            }
        }

        return true;
    }

    protected function createPaymentSystem($data)
    {
        // Проверяем, существует ли уже платежная система с таким названием
        $existingPaymentSystem = Manager::getList([
            'filter' => ['NAME' => $data['NAME']],
        ])->fetch();

        if ($existingPaymentSystem) {
            echo "Платежная система '{$data['NAME']}' уже существует.\n";
            return;
        }

        // Определяем обработчик в зависимости от функционала
        if ($data['FUNCTIONALITY'] === 'paid') {
            $actionFile = 'cash'; // Оплачено
        } elseif ($data['FUNCTIONALITY'] === 'awaiting_payment') {
            $actionFile = 'bill'; // Принят, ожидается оплата
        } else {
            echo "Неизвестный функционал для платежной системы '{$data['NAME']}'\n";
            return;
        }

        // Генерация XML_ID
        $xmlId = Manager::generateXmlId();

        // Добавляем новую платежную систему
        $paymentSystemFields = [
            'NAME' => $data['NAME'], // Название платежной системы
            'CODE' => $data['CODE'], // Уникальный код
            'PSA_NAME' => $data['NAME'], // Название обработчика
            'DESCRIPTION' => $data['DESCRIPTION'], // Описание
            'SORT' => 100, // Сортировка
            'ACTIVE' => $data['ACTIVE'], // Активность
            'ACTION_FILE' => $actionFile, // Файл обработчика
            'AUTO_CHANGE_1C' => 'N', // Интеграция с 1С
            'XML_ID' => $xmlId, // Генерация уникального XML_ID
            'ENTITY_REGISTRY_TYPE' => \Bitrix\Sale\Registry::REGISTRY_TYPE_ORDER, // Тип реестра (заказы)
        ];

        $result = Manager::add($paymentSystemFields);

        if ($result->isSuccess()) {
            $id = $result->getId();
            echo "Платежная система '{$data['NAME']}' успешно создана с ID: {$id}.\n";

            // Логирование события
            AddEventToStatFile('sale', 'addPaysystem', $id, $data['ACTION_FILE']);

            // Обновление параметров платежной системы
            $fields = [
                'PARAMS' => serialize(['BX_PAY_SYSTEM_ID' => $id]),
                'PAY_SYSTEM_ID' => $id,
            ];

            $updateResult = Manager::update($id, $fields);
            if (!$updateResult->isSuccess()) {
                echo "Ошибка при обновлении параметров платежной системы '{$data['NAME']}': " . implode(', ', $updateResult->getErrorMessages()) . "\n";
            }

            // Настройка ограничений по умолчанию
            $service = Manager::getObjectById($id);
            $applyRestrictionsResult = RestrictionsManager::setupDefaultRestrictions($service);
            if (!$applyRestrictionsResult->isSuccess()) {
                echo "Ошибка при настройке ограничений для платежной системы '{$data['NAME']}': " . implode(', ', $applyRestrictionsResult->getErrorMessages()) . "\n";
            }
        } else {
            echo "Ошибка при создании платежной системы '{$data['NAME']}': " . implode(', ', $result->getErrorMessages()) . "\n";
        }
    }

    protected function findPaymentSystemIdByCode($code)
    {
        $paymentSystem = Manager::getList([
            'filter' => ['CODE' => $code],
        ])->fetch();

        return $paymentSystem ? $paymentSystem['ID'] : null;
    }
}