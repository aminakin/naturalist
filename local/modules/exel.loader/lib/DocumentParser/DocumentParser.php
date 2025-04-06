<?php

namespace Exel\Loader\DocumentParser;

use Bitrix\Bizproc\Service\Debug;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


/**
 * Парсит из CSV в ARRAY
 */
class DocumentParser
{
    /* upload file */
    protected static array $file;

    /**
     * @param $file
     * @return array
     */
    public static function parseDocument($file)
    {
        self::$file = $file;

        if (self::$file['type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
            $data = self::readFileXLSX();
        }
        if ( self::$file['type'] == 'application/vnd.ms-excel') {
            $data = self::readFileXLS();
        }
//        $data = self::parseCSV();

        return $data ?? [];
    }

    /**
     * Форматирование SCV в array
     * @return array
     */
    private static function parseCSV()
    {
        $csvFile = file(self::$file['tmp_name']);
        $data = [];

        $lineCount = 1;
        foreach ($csvFile as $line) {
            $row = str_getcsv($line);

            $isOldFomat = explode(';', $row[0]);
            if ($isOldFomat && is_array($isOldFomat) && count($isOldFomat) > 1) {
                $rowDataToString = implode('', $row);

                $data[] = explode(';', $rowDataToString);
            } else {
                //не записываем первую - это названия столбцов
                if ($lineCount > 1) {
                    $data[] = $row;
                }

                $lineCount++;
            }
        }

        return $data;
    }

    /**
     * Получает массив данных из файлы.
     *
     * @return array
     */
    private static function readFileXLSX() : array
    {
        $reader = IOFactory::createReader("Xlsx");
        $spreadsheet = $reader->load(self::$file['tmp_name']);
        return $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
    }

    /**
     * Получает массив данных из файлы.
     *
     * @return array
     */
    private static function readFileXLS() : array
    {
        $reader = IOFactory::createReader("Xls");
        $spreadsheet = $reader->load(self::$file['tmp_name']);
        return $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
    }

}
