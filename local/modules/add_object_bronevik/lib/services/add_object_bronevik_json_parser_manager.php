<?php

namespace Local\AddObjectBronevik\Lib;

use Bitrix\Main\Application;
use Local\AddObjectBronevik\Orm\AddObjectBronevikTable;
use Local\AddObjectBronevik\Parser\IAddObjectBronevikJsonLinePosition;
use Local\AddObjectBronevik\Parser\IAddObjectBronevikParser;
use Local\AddObjectBronevik\Repository\AddObjectBronevikLoadFileRepository;
use Local\AddObjectBronevik\Repository\IAddObjectBronevikRepository;

class AddObjectBronevikJsonParserManager
{
    private IAddObjectBronevikRepository $writerRepository;

    private IAddObjectBronevikParser $parser;

    private string $path;

    private AddObjectBronevikLoadFileRepository $fileRepository;

    public function __construct() {
        $this->fileRepository = new AddObjectBronevikLoadFileRepository();
    }
    public function setWriter(IAddObjectBronevikRepository $repository)
    {
        $this->writerRepository = $repository;
    }

    public function setParser(IAddObjectBronevikParser $parser)
    {
        $this->parser = $parser;
    }

    public function setFilePath(string $filePath)
    {
        $this->path = $filePath;
    }

    public function parse()
    {
        if ($this->setFilePathInParser()) {
            $needLoad = \COption::GetOptionString('add_object_bronevik', 'needLoad', true);

            if ($needLoad) {
                $this->setStartLine();

                while ($data = $this->parser->getNextElement()) {
                    $this->writerRepository->upsert($data);
                    \COption::SetOptionString('add_object_bronevik', 'parserLastRow', $data->line);
                }
                if ($data === false) {
                    \COption::SetOptionString('add_object_bronevik', 'needLoad', false);
                    \COption::SetOptionString('add_object_bronevik', 'endLoad', (new \Bitrix\Main\Type\DateTime()));
                    $this->deleteNotUpdated();
                }
            }
        }
    }

    private function deleteNotUpdated()
    {
        $dateStart = \Bitrix\Main\Type\DateTime::createFromText(\COption::GetOptionString('add_object_bronevik', 'startLoad'));

        $result = AddObjectBronevikTable::getList(
            [
                'filter' => [
                    '<LAST_MODIFIED' =>  $dateStart,
                ],
            ],
        );

        while ($row = $result->fetch()) {
            AddObjectBronevikTable::delete($row['ID']);
        }

    }

    private function setStartLine(): void
    {
        /** @var IAddObjectBronevikJsonLinePosition $this ->parser */
        if ($this->parser instanceof IAddObjectBronevikJsonLinePosition) {
            $this->parser->setStartLine(\COption::GetOptionString('add_object_bronevik', 'parserLastRow', 1));
        }
    }

    private function setFilePathInParser(): bool
    {
        $this->downloadFile();

        $this->path = \COption::GetOptionString('add_object_bronevik', 'loadJsonPath');

        $this->parser->setFilePath($this->path);

        return true;
    }

    private function downloadFile(): void
    {
        $jsonPath = \COption::GetOptionString('add_object_bronevik', 'loadJsonPath');
        if (empty($this->path) || ! file_exists($jsonPath)) {
            $remoteLastModifier = $this->fileRepository->getHeaderLastModifier();
            $dbLastModifier = \COption::GetOptionString('add_object_bronevik', 'lastModifier');

            if ($remoteLastModifier != $dbLastModifier  || ! file_exists($jsonPath)) {
                $fileName = $this->fileRepository->getFileName();
                $newPath = Application::getDocumentRoot() . '/upload/' . $fileName;
                $this->fileRepository->downloadFile($newPath);
                \COption::SetOptionString('add_object_bronevik', 'lastModifier', $remoteLastModifier);
                \COption::SetOptionString('add_object_bronevik', 'loadJsonPath', $newPath);
                if (file_exists($jsonPath) && $jsonPath !== $newPath) {
                    unlink($jsonPath);
                }

                $this->clearStepVariables();
            }
        }
    }

    private function clearStepVariables(): void
    {
        \COption::RemoveOption('add_object_bronevik', 'parserLastRow');
        \COption::SetOptionString('add_object_bronevik', 'needLoad', true);
        \COption::SetOptionString('add_object_bronevik', 'startLoad', (new \Bitrix\Main\Type\DateTime()));
    }
}