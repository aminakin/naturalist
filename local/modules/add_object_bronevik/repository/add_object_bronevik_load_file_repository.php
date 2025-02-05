<?php

namespace Local\AddObjectBronevik\Repository;

class AddObjectBronevikLoadFileRepository
{
    private string $url = 'https://hotels-api.bronevik.com/dumps/download/?lang=ru';

    private string $login;

    private string $password;

    public function __construct()
    {
        $this->login = \COption::GetOptionString('add_object_bronevik', 'dump_login', 'naturalist_api_dump');
        $this->password = \COption::GetOptionString('add_object_bronevik', 'dump_password', 'ywvUt3s');
    }

    public function getFileName()
    {
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->login . ":" . $this->password);

        $headers = curl_exec($ch);
        $http_response = curl_getinfo($ch);
        $headers = substr($headers, 0, $http_response['header_size']);
        curl_close($ch);

        $headers = $this->headerParser($headers);

        if ($http_response['http_code'] == 200) {
            $matches = null;
            preg_match('/^.*filename=\\"(?P<name>\\S+)\\"$/', $headers['Content-Disposition'], $matches);
            return $matches['name'] ?? time().'.json';
        }

        return false;
    }

    public function downloadFile(string $path): bool
    {
        $fp = fopen($path, "w+");
        if ($fp === false) {
            throw new \Exception('Error create file ' . $path);
        }
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->login . ":" . $this->password);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        if (extension_loaded('zlib')) {
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        }
        curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        fclose($fp);
        curl_close($ch);
        if ($http_status == 200) {
            return true;
        }

        return false;
    }
    public function getHeaderLastModifier()
    {
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->login . ":" . $this->password);

        $headers = curl_exec($ch);
        $http_response = curl_getinfo($ch);
        $headers = substr($headers, 0, $http_response['header_size']);
        curl_close($ch);

        $headers = $this->headerParser($headers);

        if ($http_response['http_code'] == 200) {
            return $headers['Last-Modified'];
        }

        return false;
    }

    private function headerParser($headersText): array
    {
        $headers = explode("\r\n", $headersText);
        $headerPairs = [];
        foreach ($headers as $headerLine) {
            if (strpos($headerLine, ':') !== false) {
                list($key, $value) = explode(':', $headerLine, 2);
                $headerPairs[$key] = trim($value);
            }
        }

        return $headerPairs;
    }
}