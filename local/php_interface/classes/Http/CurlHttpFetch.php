<?php
namespace Natural\Http;

use Naturalist\Http\HttpFetchInterface;
class CurlHttpFetch implements HttpFetchInterface {
    public function post(string $url, array $data): string
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception('CURL Error: ' . curl_error($ch));
        }

        curl_close($ch);

        return $response;
    }
}