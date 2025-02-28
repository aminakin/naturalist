<?php

namespace Naturalist;

use Bitrix\Main\Diag\Debug;
use Petrovich;


class Utils
{
    const EARTH_RADIUS = 6372795;

    /**
     * $latitudeFirstPoint, $longitudeFirstPoint - широта, долгота 1-й точки,
     * $latitudeSecondPoint, $longitudeSecondPoint - широта, долгота 2-й точки
     *
     * Возвращает км
     */
    public static function calculateTheDistance(
        float $latitudeFirstPoint,
        float $longitudeFirstPoint,
        float $latitudeSecondPoint,
        float $longitudeSecondPoint
    ):float {
        // преобразование параметров в float
        $latitudeFirstPoint = $latitudeFirstPoint;
        $longitudeFirstPoint = $longitudeFirstPoint;
        $latitudeSecondPoint = $latitudeSecondPoint;
        $longitudeSecondPoint = $longitudeSecondPoint;

        // перевести координаты в радианы
        $latitudeFirstPointToRad = $latitudeFirstPoint * M_PI / 180;
        $latitudeSecondPointToRad = $latitudeSecondPoint * M_PI / 180;

        $longitudeFirstPointToRad = $longitudeFirstPoint * M_PI / 180;
        $longitudeSecondPointToRad = $longitudeSecondPoint * M_PI / 180;


        // косинусы и синусы широт и разницы долгот
        $cosLatitudeFirstPoint = cos($latitudeFirstPointToRad);
        $cosLatitudeSecondPoint = cos($latitudeSecondPointToRad);
        $sinLatitudeFirstPoint = sin($latitudeFirstPointToRad);
        $sinLatitudeSecondPoint = sin($latitudeSecondPointToRad);
        $delta = $longitudeSecondPointToRad - $longitudeFirstPointToRad;
        $cosDelta = cos($delta);
        $sinDelta = sin($delta);


        // вычисления длины большого круга
        $x = $sinLatitudeFirstPoint * $sinLatitudeSecondPoint + $cosLatitudeFirstPoint * $cosLatitudeSecondPoint * $cosDelta;

        $y = sqrt(
            pow($cosLatitudeSecondPoint * $sinDelta, 2) +
            pow($cosLatitudeFirstPoint * $sinLatitudeSecondPoint - $sinLatitudeFirstPoint * $cosLatitudeSecondPoint * $cosDelta, 2)
        );

        return round(((atan2($y, $x) * self::EARTH_RADIUS) / 1000));
    }

    /**
     * Перевод слова в падежи
     * @param $word
     */
    public static function morpher($word, $case = Morpher::CASE_NOMENATIVE)
    {
        $Morpher = new Morpher();
        return $Morpher->title($word, $case);
    }

    /**
     * Функция добавления svg файла в html
     *
     * @param string $base Папка с фалом относительно корня сайта.
     * @return string Добавление содержимого файла.
     */
    public static function buildSVG(string $base = 'image')
    {
        return file_get_contents($_SERVER['DOCUMENT_ROOT'] . $base);
    }

    /**
     * Преобразование многомерного массива в строку
     *
     * @param string $separator
     * @param array $array
     * @return string
     */
    public static function recursiveImplode(array $array, string $separator = ',', bool $includeKey = true): string
    {
        $string = '';
        foreach ($array as $key => $arrayItem) {
            if (is_array($arrayItem)) {
                $string .= ($includeKey) ? $key . $separator . self::recursiveImplode($arrayItem, $separator) : self::recursiveImplode($arrayItem, $separator);
            } else {
                $string .= ($includeKey) ? $key . $separator . $arrayItem : $arrayItem;
                if ($key < count($array) - 1) {
                    $string .= $separator;
                }
            }
        }

        return $string;
    }
}
