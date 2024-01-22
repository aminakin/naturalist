<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Naturalist\Orders;

use Bitrix\Main\Application;
use Bitrix\Main\IO;
use Naturalist\PdfGenerator;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Grid\Declension;

global $APPLICATION;

$documentRoot = Application::getDocumentRoot();

require $documentRoot . '/local/php_interface/lib/dompdf/autoload.inc.php';

//$request = Application::getInstance()->getContext()->getRequest();

//if ($request->isPost()) {

    //$arPostValues = $request->getPostList()->getValues();

    // if (!check_bitrix_sessid()) {
    //     die('Access denied');
    // }

    $pdfManager = new PdfGenerator();

    $pdfManager->setPdfStyles([
        '/local/templates/main/assets/css/pdf.css',        
    ]);

    $currentUser = CurrentUser::get();
    $order = new Orders;
    $orderData = $order->get(695);

    // Получаем интервал дат отдыха в формате шаблона
    $dateFrom = new \Bitrix\Main\Type\Date($orderData['PROPS']['DATE_FROM']);
    $dateTo = new \Bitrix\Main\Type\Date($orderData['PROPS']['DATE_TO']);

    if (FormatDate("F", MakeTimeStamp($dateFrom)) == FormatDate("F", MakeTimeStamp($dateTo))) {
        $orderData['INTERVAL'] = FormatDate("d", MakeTimeStamp($dateFrom)).'-'.FormatDate("d F", MakeTimeStamp($dateTo));
    } else {
        $orderData['INTERVAL'] = FormatDate("d F", MakeTimeStamp($dateFrom)).'-'.FormatDate("d F", MakeTimeStamp($dateTo));
    }
    
    $countNights = new Declension('ночь', 'ночи', 'ночей');

    $orderData['INTERVAL'] .= ', ' . $orderData['ITEMS'][0]['ITEM_BAKET_PROPS']['DAYS_COUNT']['VALUE'] . ' ' . $countNights->get(intval($orderData['ITEMS'][0]['ITEM_BAKET_PROPS']['DAYS_COUNT']['VALUE']));

    // Получаем изображение номера
    if (isset($orderData['ITEMS'][0]['ITEM']['PROPERTIES']['PHOTO_ARRAY']['~VALUE']['TEXT'])) {        
        $imgTemp = str_replace(['[', ']'], '', $orderData['ITEMS'][0]['ITEM']['PROPERTIES']['PHOTO_ARRAY']['~VALUE']['TEXT']);
        $imgTemp = explode(',', $imgTemp)[0];
        $orderData['IMAGE_URL'] = json_decode($imgTemp)->url;
    } else {        
        $orderData['IMAGE'] = CFile::getPath($orderData['ITEMS'][0]['ITEM']['PROPERTIES']['PHOTOS']['VALUE'][0]);
    }

    /**
     * HTML CONTENT START
     */

    ob_start();

    $APPLICATION->IncludeFile(
        '/ajax/pdf/inc/pdfHtml.php', 
        [
            'arResult' => $orderData,            
        ], 
        [
            'SHOW_BORDER' => false
        ]
    ); 

    $htmlContentBody = ob_get_clean();

    /**
     * HTML CONTENT END
    */

    $html = implode('', [
        $pdfManager->getDefaultHeader(),
        $htmlContentBody,
        $pdfManager->getDefaultFooter(),
    ]);

    $pdfManager->quickRender($html);

    $processSaveFile = $pdfManager->saveFilePdf(
        '/upload'
    );

    // if ($processSaveFile) {

    //     echo $pdfManager->getPathFilePdf();

    //     $dir = new IO\Directory(
    //         implode('', [
    //             $documentRoot , 
    //             PDF_DEFAULT_PATH , 
    //             'calculator'
    //             ]
    //         )
    //     );

    //     if (!$dir->isExists()) {
    //         $dir->create();
    //     }

    //     $arFiles = $dir->getChildren();

    //     if (!empty($arFiles)) {

    //         $currentDateEntity = new DateTime();
    //         $currentDate = $currentDateEntity->format("d-m-Y H:i");

    //         foreach ($arFiles as $arFile) {

    //             $pathFile = $arFile->getPath();
    //             $fileEntity = new IO\File($pathFile);

    //             $createdAtFile = DateTime::createFromTimestamp(
    //                 $fileEntity->getCreationTime()
    //             )->add('30 minutes')->format("d-m-Y H:i");

    //             if ($currentDate > $createdAtFile) {
    //                 $fileEntity->deleteFile($pathFile);
    //             }

    //         }
    //     }
         
    // }

//}