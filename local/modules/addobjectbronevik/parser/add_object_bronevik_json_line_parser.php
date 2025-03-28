<?php

namespace Local\AddObjectBronevik\Parser;

use Local\AddObjectBronevik\Data\AdvanceHotelDTO;

class AddObjectBronevikJsonLineParser implements IAddObjectBronevikParser, IAddObjectBronevikJsonLinePosition
{
    private string $filePath;

    private mixed $file;

    private int $startLine = 1;

    private int $currentLine = 1;

    public function setStartLine(int $startLine): void
    {
        $this->startLine = $startLine;
    }

    public function setFilePath(string $filePath)
    {
        if (file_exists($filePath)) {
            $this->filePath = $filePath;
        } else {
            throw new \Exception('file ' . $filePath . ' not found');
        }
    }

    public function getNextElement(): false|AdvanceHotelDTO
    {
        $this->openFile();

        $this->moveToStartLine();

        if (($line = fgets($this->file)) !== false) {
            $line = $this->jsonStringTrim($line);
            $data = json_decode($line, true);
            $this->currentLine++;

            return $this->dataToDto($data, $this->currentLine);
        }

        return false;
    }

    private function moveToStartLine()
    {
        while($this->currentLine < $this->startLine) {
            fgets($this->file);
            $this->currentLine++;
        }
    }

    private function jsonStringTrim(string $jsonString): string
    {
        return rtrim(rtrim(ltrim(trim($jsonString), '['), ','), ']');
    }

    private function openFile()
    {
        if (!isset($this->file)) {
            $this->file = fopen($this->filePath, 'r');
        }
    }

    private function dataToDto(array $data, int $line): AdvanceHotelDTO
    {
        return new AdvanceHotelDTO(
            name: $data['name'],
            code: $data['id'],
            type: $data['type'],
            address: $data['address'],
            city: $data['cityName'],
            country: $data['descriptionDetails']['countryName'],
            zip: $data['descriptionDetails']['zipCode'],
            lat: $data['descriptionDetails']['latitude'],
            lon: $data['descriptionDetails']['longitude'],
            description: $data['descriptionDetails']['description'],
            photos: json_encode($data['descriptionDetails']['photos']),
            line: $line,
        );
    }
}