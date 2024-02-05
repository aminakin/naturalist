<?
namespace Naturalist;

use Bitrix\Main\Application;
use Bitrix\Main\Grid\Declension;
use Bitrix\Main\Type\Date;

use Naturalist\PdfGenerator;
use Naturalist\Orders;

/**
 * Создание PDF для заказа
 */

class CreateOrderPdf {
    private $orderData = [];
    private $pdfManager;
    
    public function __construct() {
        $documentRoot = Application::getDocumentRoot();
        require $documentRoot . '/local/php_interface/lib/dompdf/autoload.inc.php';
        $this->pdfManager = new PdfGenerator();
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
     * Возвращает интервал дат отдыха в формате шаблона
     *
     * @return void
     * 
     */
    private function getDates() {        
        $dateFrom = new Date($this->orderData['PROPS']['DATE_FROM']);
        $dateTo = new Date($this->orderData['PROPS']['DATE_TO']);
    
        if (FormatDate("F", MakeTimeStamp($dateFrom)) == FormatDate("F", MakeTimeStamp($dateTo))) {
            $this->orderData['INTERVAL'] = FormatDate("d", MakeTimeStamp($dateFrom)).'-'.FormatDate("d F", MakeTimeStamp($dateTo));
        } else {
            $this->orderData['INTERVAL'] = FormatDate("d F", MakeTimeStamp($dateFrom)).'-'.FormatDate("d F", MakeTimeStamp($dateTo));
        }
        
        $countNights = new Declension('ночь', 'ночи', 'ночей');
        $this->orderData['INTERVAL'] .= ', ' . $this->orderData['ITEMS'][0]['ITEM_BAKET_PROPS']['DAYS_COUNT']['VALUE'] . ' ' . $countNights->get(intval($this->orderData['ITEMS'][0]['ITEM_BAKET_PROPS']['DAYS_COUNT']['VALUE']));
    }

    /**
     * Возвращает изображение номера
     *
     * @return void
     * 
     */
    private function getImage() {
        if (isset($this->orderData['ITEMS'][0]['ITEM']['PROPERTIES']['PHOTO_ARRAY']['~VALUE']['TEXT'])) {        
            $imgTemp = str_replace(['[', ']'], '', $this->orderData['ITEMS'][0]['ITEM']['PROPERTIES']['PHOTO_ARRAY']['~VALUE']['TEXT']);
            $imgTemp = explode(',', $imgTemp)[0];
            $this->orderData['IMAGE_URL'] = json_decode($imgTemp)->url;
        } else if (is_array($this->orderData['ITEMS'][0]['ITEM']['PROPERTIES']['PHOTOS']['VALUE'][0]) && count($this->orderData['ITEMS'][0]['ITEM']['PROPERTIES']['PHOTOS']['VALUE'][0])) {        
            $this->orderData['IMAGE'] = \CFile::getPath($this->orderData['ITEMS'][0]['ITEM']['PROPERTIES']['PHOTOS']['VALUE'][0]);
        } else {
            $this->orderData['IMAGE'] = $this->orderData['ITEMS'][0]['ITEM_BAKET_PROPS']['PHOTO']['VALUE'];
        }
    }

    /**
     * Возвращает список гостей
     *
     * @return [type]
     * 
     */
    private function getGuests() {
        $countAdults = new Declension('взрослый', 'взрозлых', 'взрозлых');
        $this->orderData['PEOPLE'] = $this->orderData['ITEMS'][0]['ITEM_BAKET_PROPS']['GUESTS_COUNT']['VALUE'] . ' ' . $countAdults->get(intval($this->orderData['ITEMS'][0]['ITEM_BAKET_PROPS']['GUESTS_COUNT']['VALUE']));
    
        if (isset($this->orderData['ITEMS'][0]['ITEM_BAKET_PROPS']['CHILDREN']) && $this->orderData['ITEMS'][0]['ITEM_BAKET_PROPS']['CHILDREN']['VALUE']) {
            $countChildren = new Declension('ребёнок', 'детей', 'детей');
            $children = count(explode(',', $this->orderData['ITEMS'][0]['ITEM_BAKET_PROPS']['CHILDREN']['VALUE']));
            $this->orderData['PEOPLE'] .= ', ' . $children . ' ' . $countChildren->get(intval($children));
        }
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
             '/ajax/pdf/inc/pdfHtml.php', 
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
            '/local/templates/main/assets/css/pdf.css',        
        ]);
        
        $this->getOrderData($orderId);
        $this->getDates();
        $this->getImage();        
        $this->getGuests();
        $this->createHtml();
    
        $this->pdfManager->quickRender($this->createHtml());
    
        $processSaveFile = $this->pdfManager->saveFilePdf(
            '/upload/vaucers'
        );
    
        if ($processSaveFile) {
    
            return json_encode([
                "LINK" => HTTP_HOST.$this->pdfManager->getPathFilePdf(),
                "SHORT" => $this->pdfManager->getPathFilePdf()
            ]);
    
            // $dir = new IO\Directory(
            //     implode('', [
            //         $documentRoot , 
            //         PDF_DEFAULT_PATH , 
            //         'calculator'
            //         ]
            //     )
            // );
    
            // if (!$dir->isExists()) {
            //     $dir->create();
            // }
    
            // $arFiles = $dir->getChildren();
    
            // if (!empty($arFiles)) {
    
            //     $currentDateEntity = new DateTime();
            //     $currentDate = $currentDateEntity->format("d-m-Y H:i");
    
            //     foreach ($arFiles as $arFile) {
    
            //         $pathFile = $arFile->getPath();
            //         $fileEntity = new IO\File($pathFile);
    
            //         $createdAtFile = DateTime::createFromTimestamp(
            //             $fileEntity->getCreationTime()
            //         )->add('30 minutes')->format("d-m-Y H:i");
    
            //         if ($currentDate > $createdAtFile) {
            //             $fileEntity->deleteFile($pathFile);
            //         }
    
            //     }
            // }
             
        } else {
            return json_encode([
                "ERROR" => "Что-то пошло не так. Пожалуйста, попробуйте позже"
            ]);
        }
    }
}