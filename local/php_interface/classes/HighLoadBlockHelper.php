<?php

namespace Naturalist;

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;

/**
 * Облегчает работу с highload блоками
 */
class HighLoadBlockHelper
{
    /**
     * @var string|null
     */
    private $codeHl;

    /**
     * @var HL\Entity|null
     */
    private $entityHl;

    /**
     * @var HL\DataManager|null
     */
    private $entityDataClass;

    /**
     * @var array
     */
    private $paramsQuery = [];

    /**
     * HihgLoadBlockHelper constructor.
     *
     * @param string|null $codeHl Код highload блока
     *
     * @throws Exception
     */
    public function __construct(?string $codeHl = null)
    {
        if (!Loader::includeModule('highloadblock')) {
            throw new \Bitrix\Main\SystemException('Module highloadblock is not initialized');
        }

        if ($codeHl === null) {
            throw new \Bitrix\Main\SystemException('Empty code highloadblock');
        }

        $this->codeHl = $codeHl;
        $this->compilationHighloadblock();
    }

    private function compilationHighloadblock(): void
    {
        $this->compileEntityHl();
        $this->getCompileDataClassHl();
    }

    /**
     * Получает сущность highload блока.
     *
     * @return HL\Entity|null
     */
    private function compileEntityHl()
    {
        return $this->entityHl = HL\HighloadBlockTable::compileEntity($this->codeHl);
    }

    /**
     * Получает класс данных highload блока.
     *
     * @return HL\DataManager|null
     */
    private function getCompileDataClassHl()
    {
        return $this->entityDataClass = $this->entityHl->getDataClass();
    }

    /**
     * Подготавливает параметры для запроса.
     *
     * @param array $select Выбираемы поля
     * @param array $order  Сортировка
     * @param array $filter Фильтр
     *
     * @return array
     */
    public function prepareParamsQuery(array $select = ['*'], array $order = [], array $filter = []): array
    {
        $this->paramsQuery['select'] = $select;
        $this->paramsQuery['order'] = $order;
        $this->paramsQuery['filter'] = $filter;
        return $this->paramsQuery;
    }

    /**
     * Получает параметры для запроса.
     *
     * @return array
     */
    public function getParamsQuery(): array
    {
        return $this->paramsQuery;
    }

    /**
     * Получает одну запись из highload блока.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->entityDataClass::getList($this->paramsQuery)->fetch();
    }

    /**
     * Получает все записи их highload блока.
     *
     * @return mixed
     */
    public function getDataAll()
    {
        return $this->entityDataClass::getList($this->paramsQuery)->fetchAll();
    }

    /**
     * Удаляет запись из highload блока.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function delete(int $id)
    {
        return $this->entityDataClass::delete($id);
    }

    /**
     * Добавляет запись в highload блок.
     *
     * @param array $fields
     *
     * @return mixed
     */
    public function add(array $fields)
    {
        return $this->entityDataClass::add($fields);
    }

    /**
     * Обновляет запись в highload блоке.
     *
     * @param int $id
     * @param array $fields
     *      
     * @return mixed
     */
    public function update(int $id, array $fields)
    {
        return $this->entityDataClass::update($id, $fields);
    }

    /**
     * Копирует запись в highload блоке.
     *
     * @param int $id
     * @param array $fields
     *      
     * @return mixed
     */
    public function copy(int $id)
    {
        $this->prepareParamsQuery(
            ['*'],
            ["ID" => "ASC"],
            ['ID' => $id]
        );
        $fields = $this->getData();
        unset($fields['ID']);

        return $this->entityDataClass::add($fields);
    }
}
