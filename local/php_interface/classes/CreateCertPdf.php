<?
namespace Naturalist;

use Bitrix\Main\Application;
use Naturalist\HighLoadBlockHelper;
use Naturalist\PdfGenerator;
use Naturalist\Orders;

/**
 * Создание PDF для сертификата
 */

class CreateCertPdf {
    private $orderData = [];
    private $pdfManager;
    private $hlEntity;
    
    public function __construct() {
        $documentRoot = Application::getDocumentRoot();
        require $documentRoot . '/local/php_interface/lib/dompdf/autoload.inc.php';
        $this->pdfManager = new PdfGenerator();
        $this->hlEntity = new HighLoadBlockHelper('Certificates');
    }

    /**
     * Возвращает массив данных заказа
     *
     * @param int $orderId
     * 
     * @return array
     * 
     */
    private function getOrderData($orderId) {
        $order = new Orders;
        return $this->orderData = $order->get($orderId);
    }

    /**
     * Находит сертификат и получает его свойства
     *
     * @return void
     * 
     */
    private function getCert() {        
        $this->hlEntity->prepareParamsQuery(['*'], [], ['UF_ORDER_ID' => $this->orderData['ID']]);
        $this->orderData['CERT'] = $this->hlEntity->getData();
    }    

    /**
     * Возвращает html разметку для Pdf
     *
     * @return string
     * 
     */
    private function createHtml() {    
         ob_start();

         global $APPLICATION;
     
         $APPLICATION->IncludeFile(
             '/ajax/pdf/inc/certPdfHtml.php', 
             [
                 'arResult' => $this->orderData,            
             ], 
             [
                 'SHOW_BORDER' => false
             ]
         ); 
     
         $htmlContentBody = ob_get_clean();     
         $html = implode('', [
             $this->pdfManager->getDefaultHeader(),
             $htmlContentBody,
             $this->pdfManager->getDefaultFooter(),
         ]);
         
         return $html;
    }

    /**
     * Возвращает ссылку на файл
     *
     * @param mixed $orderId
     * 
     * @return json
     * 
     */
    public function getPdfLink($orderId) {    
        $this->pdfManager->setPdfStyles([
            '/local/templates/main/assets/css/certpdf.css',        
        ]);        
        
        $this->getOrderData($orderId);
        $this->getCert();        
        $this->createHtml();
    
        $this->pdfManager->quickRender($this->createHtml());
    
        $processSaveFile = $this->pdfManager->saveFilePdf(
            '/upload/certs', 'Сертификат_'.$orderId
        );
    
        if ($processSaveFile) {
    
            return json_encode([
                "LINK" => HTTP_HOST.$this->pdfManager->getPathFilePdf(),
                "SHORT" => $this->pdfManager->getPathFilePdf()
            ]);            
             
        } else {
            return json_encode([
                "ERROR" => "Что-то пошло не так. Пожалуйста, попробуйте позже"
            ]);
        }
    }
}