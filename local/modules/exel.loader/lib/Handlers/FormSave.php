<?php

namespace Calculator\Kploader\Handlers;

use Bitrix\Main\Application;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\SystemException;
use Calculator\Kploader\DocumentParser\DocumentParser;
use Calculator\Kploader\HLLoader\airFraht\AirFrahtLoader;
use Calculator\Kploader\HLLoader\autoRoutes\AutoRoutesLoader;
use Calculator\Kploader\HLLoader\autoSvh\AutoSvhLoader;
use Calculator\Kploader\HLLoader\customsDuty\CustomsDutyLoader;
use Calculator\Kploader\HLLoader\localDeliverySbor\LocalDeliverySborLoader;
use Calculator\Kploader\HLLoader\rzd40\RZD40Loader;
use Calculator\Kploader\HLLoader\rzdConstrukt\RZDConstruktLoader;
use Calculator\Kploader\HLLoader\seaContainer\SeaContainerLoader;
use Calculator\Kploader\HLLoader\transportationRfSbor\TransportationRfSborLoader;

class FormSave
{

    /**
     * @var \Bitrix\Main\Application The Bitrix application instance.
     */
    public $app;

    /**
     * @var \Bitrix\Main\Context The Bitrix context instance.
     */
    public $context;

    /**
     * @var \Bitrix\Main\HttpRequest The Bitrix request instance.
     */
    public $request;

    /**
     * @var string - ответ для POST запроса
     */
    public $responce = '';

    public function __construct()
    {
        $this->app = Application::getInstance();
        $this->context = $this->app->getContext();
        $this->request = $this->context->getRequest();


        if ($this->request->isPost() && check_bitrix_sessid()) {
            $this->handlePostRequest();
        }
    }
    /**
     * Handles the post request.
     */
    private function handlePostRequest()
    {
        if (!empty($this->request->getPost('save'))) {

            if (!empty($this->request->getFile('rzd40_file')) && $this->request->getFile('rzd40_file')['tmp_name'] != NULL){
                $this->handleSaveRZD40($this->request->getFile('rzd40_file'));
            }

            if (!empty($this->request->getFile('rzdConstrukt')) && $this->request->getFile('rzdConstrukt')['tmp_name'] != NULL){
                $this->handleSaveRZDConstrukt($this->request->getFile('rzdConstrukt'));
            }

            if (!empty($this->request->getFile('customsDuty')) && $this->request->getFile('customsDuty')['tmp_name'] != NULL){
                $this->handleSaveCustomsDuty($this->request->getFile('customsDuty'));
            }

            if (!empty($this->request->getFile('localDeliverySbor')) && $this->request->getFile('localDeliverySbor')['tmp_name'] != NULL){
                $this->handleSaveLocalDeliverySbor($this->request->getFile('localDeliverySbor'));
            }

            if (!empty($this->request->getFile('seaContainer')) && $this->request->getFile('seaContainer')['tmp_name'] != NULL){
                $this->handleSaveSeaContainer($this->request->getFile('seaContainer'));
            }

            if (!empty($this->request->getFile('airFraht')) && $this->request->getFile('airFraht')['tmp_name'] != NULL){
                $this->handleSaveAirFraht($this->request->getFile('airFraht'));
            }

            if (!empty($this->request->getFile('autoSvh')) && $this->request->getFile('autoSvh')['tmp_name'] != NULL){
                $this->handleSaveAutoSvh($this->request->getFile('autoSvh'));
            }

            if (!empty($this->request->getFile('transportationRfSbor')) && $this->request->getFile('transportationRfSbor')['tmp_name'] != NULL){
                $this->handleSaveTransportationRfSbor($this->request->getFile('transportationRfSbor'));
            }

            if (!empty($this->request->getFile('autoRoutes')) && $this->request->getFile('autoRoutes')['tmp_name'] != NULL){
                $this->handleSaveAutoRoutes($this->request->getFile('autoRoutes'));
            }
        }
    }

    /**
     * Сохранить HL ЖД контейнер 40 фут
     * @param $file
     */
    private function handleSaveRZD40($file)
    {
        $parsedFile = DocumentParser::parseDocument($file);

        $this->responce = RZD40Loader::loadData($parsedFile);
    }

    /**
     * Сохранить HL ЖД Контейнер Сборка
     * @param $file
     */
    private function handleSaveRZDConstrukt($file)
    {
        $parsedFile = DocumentParser::parseDocument($file);

        $this->responce = RZDConstruktLoader::loadData($parsedFile);
    }

    /**
     * Сохранить HL Таможенный сбор
     * @param $file
     */
    private function handleSaveCustomsDuty($file)
    {
        $parsedFile = DocumentParser::parseDocument($file);

        $this->responce = CustomsDutyLoader::loadData($parsedFile);
    }

    /**
     * Сохранить HL Локальная доставка Сборка
     * @param $file
     */
    private function handleSaveLocalDeliverySbor($file)
    {
        $parsedFile = DocumentParser::parseDocument($file);

        $this->responce = LocalDeliverySborLoader::loadData($parsedFile);
    }

    /**
     * Сохранить HL Контейнер целый море - 20, 20 утяж, 40
     * @param $file
     */
    private function handleSaveSeaContainer($file)
    {
        $parsedFile = DocumentParser::parseDocument($file);

        $this->responce = SeaContainerLoader::loadData($parsedFile);
    }

    /**
     * Сохранить HL Контейнер целый море - 20, 20 утяж, 40
     * @param $file
     */
    private function handleSaveAirFraht($file)
    {
        $parsedFile = DocumentParser::parseDocument($file);

        $this->responce = AirFrahtLoader::loadData($parsedFile);
    }

    /**
     * Сохранить HL Ставки СВХ Авто
     * @param $file
     */
    private function handleSaveAutoSvh($file)
    {
        $parsedFile = DocumentParser::parseDocument($file);

        $this->responce = AutoSvhLoader::loadData($parsedFile);
    }

    /**
     * Сохранить HL Транспортировка по РФ (Сборный)
     * @param $file
     */
    private function handleSaveTransportationRfSbor($file)
    {
        $parsedFile = DocumentParser::parseDocument($file);

        $this->responce = TransportationRfSborLoader::loadData($parsedFile);
    }

    /**
     * Сохранить HL Маршруты Авто
     * @param $file
     */
    private function handleSaveAutoRoutes($file)
    {
        $parsedFile = DocumentParser::parseDocument($file);

        $this->responce = AutoRoutesLoader::loadData($parsedFile);
    }
}
