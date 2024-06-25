<?php

namespace Naturalist;
use \Bitrix\Iblock\Model\Section;
use \Bitrix\Main\Loader;

Loader::includeModule("iblock");

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

    /**
     * Удаляет неиспользуемые файлы из папки /upload/uf/
     *     
     */
    public static function deleteOldUfFiles() {
        // Получаем все фото из доп. полей разделов каталога
        $entity = Section::compileEntityByIblock(CATALOG_IBLOCK_ID);
        $rsSectionObjects = $entity::getList(
            [
                'filter' => ['IBLOCK_ID' => CATALOG_IBLOCK_ID],
                'select' => ['NAME', 'UF_PHOTOS'],        
            ]
        );

        while ($arSectionItem = $rsSectionObjects->Fetch()){
            if (is_array($arSectionItem['UF_PHOTOS']) && count($arSectionItem['UF_PHOTOS'])) {
                foreach ($arSectionItem['UF_PHOTOS'] as $photo) {
                    $filePath = $_SERVER['DOCUMENT_ROOT'].\CFile::getPath($photo);
                    if (str_contains($filePath, '/uf')) {
                        $photoPaths[] = $filePath;
                    }            
                }
            }    
        }

        // Получаем все фото из HL блоков особенностей объектов и сертификатов
        array_merge($photoPaths, self::addPhotosToArray(self::$amenitiesHlCode, 'UF_ICON'));
        array_merge($photoPaths, self::addPhotosToArray(self::$certvarHlCode, 'UF_FILE'));
        array_merge($photoPaths, self::addPhotosToArray(self::$certvarHlCode, 'UF_IMG_TO_CERT'));
        array_merge($photoPaths, self::addPhotosToArray(self::$certpocketHlCode, 'UF_FILE'));
        array_merge($photoPaths, self::addPhotosToArray(self::$certelvarHlCode, 'UF_FILE'));

        $photoPaths = array_unique($photoPaths);
        $uf = scandir($_SERVER['DOCUMENT_ROOT'].self::$ufFolder);

        foreach ($uf as $ufFolder) {
            if ($ufFolder != '.' && $ufFolder != '..') {        
                $ufInnerFolder = scandir($_SERVER['DOCUMENT_ROOT'].self::$ufFolder.$ufFolder);

                foreach ($ufInnerFolder as $innerFolder) {
                    if ($innerFolder != '.' && $innerFolder != '..') {
                        $ufInnerFiles = scandir($_SERVER['DOCUMENT_ROOT'].self::$ufFolder.$ufFolder.'/'.$innerFolder);

                        foreach ($ufInnerFiles as $lastFile) {
                            if (is_file($_SERVER['DOCUMENT_ROOT'].self::$ufFolder.$ufFolder.'/'.$innerFolder.'/'.$lastFile)) {
                                $ufAllFiles[] = $_SERVER['DOCUMENT_ROOT'].self::$ufFolder.$ufFolder.'/'.$innerFolder.'/'.$lastFile;
                            }    
                        }
                        unset($lastFile);
                    }
                    if (is_file($_SERVER['DOCUMENT_ROOT'].self::$ufFolder.$ufFolder.'/'.$innerFolder)) {
                        $ufAllFiles[] = $_SERVER['DOCUMENT_ROOT'].self::$ufFolder.$ufFolder.'/'.$innerFolder;
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
                    $filePath = $_SERVER['DOCUMENT_ROOT'].\CFile::getPath($HlPhoto[$field]);                    
                    $result[] = $filePath;                                
                }                
            }
        }

        return $result;
    }

    // Парсит строку сесии в массив
    public static function unserialize_php($session_data) {
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
}