<?php 

namespace Naturalist;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;
use \Dompdf\Options;
use \Dompdf\Dompdf;

require_once Application::getDocumentRoot() . '/local/php_interface/lib/dompdf/autoload.inc.php';

/**
 * Класс для более облегченной работы с DomPdf
 */
class PdfGenerator 
{
    /**
     * @var Dompdf $pdfEntity Экземпляр объекта dompdf.
     */
    private $pdfEntity = null;

    /**
     * @var Options $pdfOptions Экземпляр объекта настроек для dompodf.
     */
    private $pdfOptions = null;

    /**
     * @var string|null $pathFile Путь до сгенерированного файла PDF.
     */
    private $pathFile = null;

    /**
     * @var array $pdfStyles Массив ссылок стилей, которые подключаются в файле PDF.
     */
    private $pdfStyles = [];

    function setPdfStyles(array $arStyles) : void 
    {
        $this->pdfStyles = $arStyles;
    }

    /**
     * Конструктор класса
     * 
     * @return void
     */
    function __construct($size = [0,0,440,700])
    {
        $this->pdfOptions = new Options();
        $this->prepareDefaultParams($size);

        $this->pdfEntity = new Dompdf(
            $this->pdfOptions
        );

    }

    /**
     * Установка и обработка дефолтных значений
     * для генерации PDF.
     * 
     * @return void
     */
    function prepareDefaultParams($size) : void
    {
        $this->pdfOptions->setIsRemoteEnabled(true);
        $this->pdfOptions->setDefaultPaperSize('portrait');        
        $this->pdfOptions->setDefaultPaperSize($size);
    }

    /**
     * Загрузка HTML из текста
     * 
     * @param string $html HTML текст.
     * 
     * @return void
     */
    function loadHtml(string $html) : void
    {
        $this->pdfEntity->loadHtml($html, 'UTF-8');
    }

    /**
     * Рендер PDF
     * 
     * @return mixed Результат рендера.
     */
    function render() : mixed
    {
        return $this->pdfEntity->render();
    }

    /**
     * Получение PDF на выходе
     * 
     * @return mixed PDF в виде байтовой строки.
     */
    function getOutputPdf() : mixed
    {
        return $this->pdfEntity->output();
    }

    /**
     * Сохранение файла PDF
     * 
     * @param string $path Путь сдля сохранения.
     * @param string $fileNamePrefix Имя файла до расширения.
     * 
     * @return mixed Кол-во записанных байт в случае успешной записи, иначе false.
     */
    function saveFilePdf(string $path = PDF_DEFAULT_PATH, string $fileNamePrefix) : mixed
    {

        $filename = $fileNamePrefix . '.pdf';

        $pathSave = implode('/', [
            Application::getDocumentRoot()  . $path,
            $filename,
        ]);

        $this->pathFile = implode('/', [
            $path,
            $filename,
        ]); 

        return file_put_contents(
            $pathSave,
            $this->getOutputPdf()
        );

    }

    /**
     * Получение пути до файла
     * 
     * @return string|null Путь до файла.
     */
    function getPathFilePdf() : ?string
    {
        return $this->pathFile;
    }

    /**
     * Получение отформатированных ссылок на стили
     * в виде HTML строки
     * 
     * @return string Ссылки в виде HTML.
     */
    function getDefaultStyles() : string
    {

        $htmlStyles = [];

        foreach ($this->pdfStyles as $styleLink) {
            array_push($htmlStyles,                 
                '<link rel="stylesheet" href="' . HTTP_HOST . $styleLink . '">'
            );
        }

        return implode('', $htmlStyles);
    }

    /**
     * Шаблон начала PDF
     * 
     * @return string
     */
    function getDefaultHeader() : string
    {
        return '<!DOCTYPE html>
            <html lang="en">
            <head>'
                .$this->getDefaultStyles().
                '<meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
            </head>
            <body>';
    }

    /**
     * Шаблон конца PDF
     * 
     * @return string
     */
    function getDefaultFooter() : string
    {
        return '</body>
            </html>';
    }

    /**
     * Быстрый рендер
     * 
     * @param string $html HTML для рендера.
     * 
     * @return mixed Результат рендера.
     */
    function quickRender($html) : mixed
    {
        $this->loadHtml($html);
        
        return $this->render();
    }

}