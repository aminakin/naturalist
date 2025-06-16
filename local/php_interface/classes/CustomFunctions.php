<?php

namespace Naturalist;

use \Bitrix\Iblock\Model\Section;
use \Bitrix\Main\Loader;
use \Bitrix\Sale\Order;
use \DateTime;

Loader::includeModule("iblock");
Loader::IncludeModule('sale');

/**
 * Товары каталога.
 */

class CustomFunctions
{
    private static $ufFolder = '/upload/uf/';
    private static $amenitiesHlCode = 'CampingFeatures';
    private static $certvarHlCode = 'Certvar';
    private static $certpocketHlCode = 'Certpocket';
    private static $certelvarHlCode = 'Certelvar';
    private static $regionsHlCode = 'Regions';
    private static $waterHlCode = 'Water';
    private static $suitHlCode = 'SuitTypes';

    /**
     * Массив
     */
    private const SEO_REFERERS = [
        'yandex.ru',
        'ya.ru',
        'google.com',
        'bing.com',
        'duckduckgo.com',
        'yahoo.com',
        'mail.ru',
        'rambler.ru'
    ];

    /**
     * Удаляет неиспользуемые файлы из папки /upload/uf/
     *     
     */
    public static function deleteOldUfFiles()
    {
        // Получаем все фото из доп. полей разделов каталога
        $entity = Section::compileEntityByIblock(CATALOG_IBLOCK_ID);
        $rsSectionObjects = $entity::getList(
            [
                'filter' => ['IBLOCK_ID' => CATALOG_IBLOCK_ID],
                'select' => ['NAME', 'UF_PHOTOS'],
            ]
        );

        while ($arSectionItem = $rsSectionObjects->Fetch()) {
            if (is_array($arSectionItem['UF_PHOTOS']) && count($arSectionItem['UF_PHOTOS'])) {
                foreach ($arSectionItem['UF_PHOTOS'] as $photo) {
                    $filePath = $_SERVER['DOCUMENT_ROOT'] . \CFile::getPath($photo);
                    if (str_contains($filePath, '/uf')) {
                        $photoPaths[] = $filePath;
                    }
                }
            }
        }

        // Получаем все фото из HL блоков особенностей объектов и сертификатов
        $photoPaths = array_merge($photoPaths, self::addPhotosToArray(self::$amenitiesHlCode, 'UF_ICON'));
        $photoPaths = array_merge($photoPaths, self::addPhotosToArray(self::$certvarHlCode, 'UF_FILE'));
        $photoPaths = array_merge($photoPaths, self::addPhotosToArray(self::$certvarHlCode, 'UF_IMG_TO_CERT'));
        $photoPaths = array_merge($photoPaths, self::addPhotosToArray(self::$certpocketHlCode, 'UF_FILE'));
        $photoPaths = array_merge($photoPaths, self::addPhotosToArray(self::$certelvarHlCode, 'UF_FILE'));
        $photoPaths = array_merge($photoPaths, self::addPhotosToArray(self::$regionsHlCode, 'UF_ICON'));
        $photoPaths = array_merge($photoPaths, self::addPhotosToArray(self::$waterHlCode, 'UF_IMG'));
        $photoPaths = array_merge($photoPaths, self::addPhotosToArray(self::$suitHlCode, 'UF_IMG'));

        $photoPaths = array_unique($photoPaths);
        $uf = scandir($_SERVER['DOCUMENT_ROOT'] . self::$ufFolder);

        foreach ($uf as $ufFolder) {
            if ($ufFolder != '.' && $ufFolder != '..') {
                $ufInnerFolder = scandir($_SERVER['DOCUMENT_ROOT'] . self::$ufFolder . $ufFolder);

                foreach ($ufInnerFolder as $innerFolder) {
                    if ($innerFolder != '.' && $innerFolder != '..') {
                        $ufInnerFiles = scandir($_SERVER['DOCUMENT_ROOT'] . self::$ufFolder . $ufFolder . '/' . $innerFolder);

                        foreach ($ufInnerFiles as $lastFile) {
                            if (is_file($_SERVER['DOCUMENT_ROOT'] . self::$ufFolder . $ufFolder . '/' . $innerFolder . '/' . $lastFile)) {
                                $ufAllFiles[] = $_SERVER['DOCUMENT_ROOT'] . self::$ufFolder . $ufFolder . '/' . $innerFolder . '/' . $lastFile;
                            }
                        }
                        unset($lastFile);
                    }
                    if (is_file($_SERVER['DOCUMENT_ROOT'] . self::$ufFolder . $ufFolder . '/' . $innerFolder)) {
                        $ufAllFiles[] = $_SERVER['DOCUMENT_ROOT'] . self::$ufFolder . $ufFolder . '/' . $innerFolder;
                    }
                }
                unset($innerFolder);
            }
        }

        foreach ($ufAllFiles as $photo) {
            if (array_search($photo, $photoPaths) === false) {
                unlink($photo);
            }
        }
    }

    private static function addPhotosToArray($entity, $field)
    {
        $result = [];
        $hlEntity = new HighLoadBlockHelper($entity);

        $hlEntity->prepareParamsQuery(
            ["ID", $field],
            ["ID" => "ASC"],
            [],
        );

        $rows = $hlEntity->getDataAll();

        if (is_array($rows) && count($rows)) {
            foreach ($rows as $HlPhoto) {
                if ($HlPhoto[$field]) {
                    $filePath = $_SERVER['DOCUMENT_ROOT'] . \CFile::getPath($HlPhoto[$field]);
                    $result[] = $filePath;
                }
            }
        }

        return $result;
    }

    // Парсит строку сесии в массив
    public static function unserialize_php($session_data)
    {
        $return_data = array();
        $offset = 0;
        while ($offset < strlen($session_data)) {
            if (!strstr(substr($session_data, $offset), "|")) {
                throw new Exception("invalid data, remaining: " . substr($session_data, $offset));
            }
            $pos = strpos($session_data, "|", $offset);
            $num = $pos - $offset;
            $varname = substr($session_data, $offset, $num);
            $offset += $num + 1;
            $data = unserialize(substr($session_data, $offset));
            $return_data[$varname] = $data;
            $offset += strlen(serialize($data));
        }
        return $return_data;
    }

    /**
     * Отправка просьбы оставить отзыв на следующий день после выезда
     */
    public static function sendReviewInvite()
    {
        $today = new DateTime();
        $yesterday = new DateTime(date('d.m.Y', strtotime($today->format("d.m.Y") . '-1 day')));

        $dbRes = Order::getList([
            'select' => [
                "ID",
                "CHECKOUT_DATE.VALUE",
            ],
            'filter' => [
                '=STATUS_ID' => 'F',
                '=CHECKOUT_DATE.CODE' => 'DATE_TO',
                '=CHECKOUT_DATE.VALUE' => $yesterday->format("d.m.Y"),
            ],
            'runtime' => [
                new \Bitrix\Main\Entity\ReferenceField(
                    'CHECKOUT_DATE',
                    '\Bitrix\sale\Internals\OrderPropsValueTable',
                    ["=this.ID" => "ref.ORDER_ID"],
                    ["join_type" => "left"]
                ),
            ]
        ]);
        while ($order = $dbRes->fetch()) {
            $objectType = '';

            $orderClass = new Orders;
            $orderData = $orderClass->get($order['ID']);

            $hlEntity = new HighLoadBlockHelper('CampingTypes');
            $hlEntity->prepareParamsQuery(['*'], [], ['ID' => $orderData['ITEMS'][0]['ITEM']['SECTION']['UF_TYPE']]);

            if ($hlEntity->getData()) {
                $objectType = $hlEntity->getData()['UF_NAME'];
            }

            $objectPhoto = \CFile::getPath($orderData['ITEMS'][0]['ITEM']['PROPERTIES']['PHOTOS']['VALUE'][0]);

            $objectName = $orderData['PROPS']['OBJECT'];

            $email = $orderData['PROPS']['EMAIL'];

            $fields = [
                "OBJECT_TYPE" => $objectType,
                "OBJECT_NAME" => $orderData['PROPS']['OBJECT'],
                "ROOM_PHOTO" => $objectPhoto ? $objectPhoto : $orderData['PROPS']['ROOM_PHOTO'],
                "EMAIL" => $orderData['PROPS']['EMAIL'],
            ];
            Users::sendEmail("REVIEW_INVITE", "69", $fields);
        }
    }

    /**
     * Записывает referer из заголовка в куки
     *
     * @return void
     */
    public static function setSeoReferer(): void
    {
        $httpReferer = $_SERVER['HTTP_REFERER'];

        foreach (self::SEO_REFERERS as $referer) {
            if (str_contains($httpReferer, $referer)) {
                setcookie('utm_referer', $httpReferer, time()+360000);
            }
        }
    }
}
