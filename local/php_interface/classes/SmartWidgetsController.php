<?php

namespace Naturalist;

use Exception;

class SmartWidgetsController
{
    // Константы для настройки запроса
    const API_URL = "https://api.smartwidgets.ru/client/";
    const HTTP_METHOD = "POST";
    const CONTENT_TYPE_HEADER = "Content-Type: application/json";
    const SUCCESS_HTTP_CODE = 200;
    const CLIENT_KEY = 'b96dafa6f1217c0f867b42e0ad4d02cc';


    /**
     * Отправляет POST-запрос для получения данных виджетов.
     *
     * @param array $widgetIds Массив идентификаторов виджетов.
     * @return mixed Данные, полученные от API (JSON декодируется в массив).
     * @throws Exception В случае ошибки запроса.
     */
    public function getWidgetData($widgetIds)
    {
        $payload = [
            'key' => self::CLIENT_KEY,
            'widgets' => $widgetIds
        ];

        // Инициализация cURL
        $ch = curl_init(self::API_URL);

        // Настройка параметров cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, self::HTTP_METHOD);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [self::CONTENT_TYPE_HEADER]);

        // Выполнение запроса
        $response = curl_exec($ch);

        // Проверка на ошибки cURL
        if (curl_errno($ch)) {
            throw new Exception('Ошибка cURL: ' . curl_error($ch));
        }

        // Получение HTTP-кода ответа
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Закрытие соединения
        curl_close($ch);

        // Проверка статуса ответа
        if ($httpCode !== self::SUCCESS_HTTP_CODE) {
            throw new Exception("Ошибка HTTP: {$httpCode}. Ответ сервера: {$response}");
        }

        // Декодирование JSON-ответа
        $data = json_decode($response, true);

        // Проверка на успешное декодирование JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Ошибка при декодировании JSON: ' . json_last_error_msg());
        }

        return $data;
    }

    /**
     * Получает общее количество отзывов и средний рейтинг для всех источников.
     *
     * @param array $responseData Данные, полученные от API.
     * @return array Ассоциативный массив с total_count и average_rating.
     */
    public function calculateReviewsSummary($responseData)
    {
        $totalCount = 0;
        $totalRating = 0;
        $sourceCount = 0;

        // Проходим по всем виджетам
        foreach ($responseData as $widgetId => $widgetData) {
            if (isset($widgetData['item']['review_yandex_map'])) {
                // Проходим по каждому источнику отзывов
                foreach ($widgetData['item']['review_yandex_map'] as $source) {
                    if (isset($source['count'], $source['rating'])) {
                        $totalCount += $source['count'];
                        $totalRating += $source['rating'];
                        $sourceCount++;
                    }
                }
            }
        }

        // Вычисляем средний рейтинг
        $averageRating = $sourceCount > 0 ? ($totalRating / $sourceCount) : 0;

        return [
            'total_count' => $totalCount,
            'average_rating' => round($averageRating, 2)
        ];
    }
}