
<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

function generatePhotoUrl(array $imgIds):array{
    $imgUrls = [];

    foreach ($imgIds as $imgId){
        $file = CFile::GetFileArray($imgId);
        if($file){
            $file = explode('/', $file['SRC']);
            unset($file[0]);

            $imgUrls[] = ['src' => $file['SRC'], 'imgID' => $imgId, 'operationId' => 'sex'];
        }

    }

    return $imgUrls;
}

if ($_POST['action'] === 'deleteFile' && check_bitrix_sessid()) {

    $fileSrc = $_POST['fileSrc']; // URL файла, который нужно удалить

    // Преобразуем абсолютный путь в относительный
    $relativePath = explode('/', str_replace([$_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'], '/resize_cache'], '', $fileSrc));

    unset($relativePath[5]);
    unset($relativePath[6]);

    $relativePath = implode('/', $relativePath);



    $fileId = $res['ID'];

    CModule::IncludeModule("iblock");

    $iblockID = 1; // ID инфоблока
    $sectionID = 1766; // ID раздела

    $res = CIBlockSection::GetList(
        array(),
        array(
            "IBLOCK_ID" => $iblockID,
            "ID" => $sectionID
        ),
        false,
        array(
            "UF_PHOTOS" // символьный код свойства
        )
    );

    if ($section = $res->GetNext()) {
        $propertyValue = $section;
        $arPhotos = generatePhotoUrl($propertyValue['UF_PHOTOS']);
        var_dump($relativePath);
        if(!empty($arPhotos)){
            foreach ($arPhotos as $item){
                if(is_null($item[$relativePath])) continue;
                var_dump($item[$relativePath]);
            }
        }
        // использование значения свойства
    }

//    if ($res && isset($res['ID'])) {
//        $fileId = $res['ID']; // ID файла, который нужно удалить
//
//        // Получаем все элементы инфоблока, у которых используется данный файл в поле UF_PHOTOS
//        $elRes = CIBlockElement::GetList(
//            [],
//            ['IBLOCK_ID' => 1], // укажите свой IBLOCK_ID
//            false,
//            false,
//            ['ID', 'IBLOCK_ID', 'UF_PHOTOS'] // берем ID элемента и значение поля UF_PHOTOS
//        );
//
//        while ($el = $elRes->Fetch()) {
//            // Получаем текущее значение пользовательского поля (UF_PHOTOS)
//            $photos = $el['UF_PHOTOS'];
//
//            // Если в поле UF_PHOTOS есть файл с таким ID
//            if (in_array($fileId, (array)$photos)) {
//                // Удаляем файл из массива
//                $newPhotos = array_diff((array)$photos, [$fileId]);
//
//                // Обновляем значение поля UF_PHOTOS
//                CIBlockElement::SetPropertyValuesEx($el['ID'], $el['IBLOCK_ID'], [
//                    'UF_PHOTOS' => $newPhotos
//                ]);
//            }
//        }
//
//        // Теперь удаляем сам файл
//        if (CFile::Delete($fileId)) {
//            echo json_encode(['status' => 'success']);
//        } else {
//            echo json_encode(['status' => 'error', 'message' => 'Не удалось удалить файл']);
//        }
//    } else {
//        echo json_encode(['status' => 'error', 'message' => 'Файл не найден']);
//    }
//
//    die();
}