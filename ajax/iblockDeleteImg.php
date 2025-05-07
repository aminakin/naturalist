<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

function generatePhotoUrl(array $imgIds): array {
    $imgUrls = [];

    foreach ($imgIds as $imgId) {
        $file = CFile::GetFileArray($imgId);
        if ($file) {
            $src = $file["SRC"];
            $srcParts = explode('/', $src);
            unset($srcParts[6], $srcParts[5]);

            $imgUrls[] = ['src' => implode('/', $srcParts), 'imgID' => $imgId, "full_src" => $file['SRC']];
        }
    }
    return $imgUrls;
}

if ($_POST['action'] === 'deleteFile' && check_bitrix_sessid()) {
    $fileSrc = $_POST['fileSrc'];
    $iblockId = (int)$_POST['sectionID'];
    $sectionID = (int)$_POST['iblockId'];
    $relativePath = explode('/', str_replace([$_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'], '/resize_cache'], '', $fileSrc));
    unset($relativePath[5], $relativePath[6]);
    $relativePath = implode('/', $relativePath);

    CModule::IncludeModule("iblock");

    $res = CIBlockSection::GetList(
        [],
        ["IBLOCK_ID" => $iblockId, "ID" => $sectionID],
        false,
        ["UF_PHOTOS"]
    );
    if ($section = $res->GetNext()) {
        $propertyValue = $section;
        $arPhotos = generatePhotoUrl($propertyValue['UF_PHOTOS']);

        $photoIdToRemove = null;
        $externalId = null;
        $fullSrc = null;

        foreach ($arPhotos as $item) {
            if ($item['src'] === $relativePath) {
                $photoIdToRemove = (int)$item['imgID'];
                $fullSrc = $_SERVER['DOCUMENT_ROOT'].$item['full_src'];
                break;
            }
        }

        if ($photoIdToRemove !== null) {
            if (($key = array_search($photoIdToRemove, $propertyValue['UF_PHOTOS'])) !== false) {
                unset($propertyValue['UF_PHOTOS'][$key]);
            }

            $sectionUpdate = new CIBlockSection();
            if ($sectionUpdate->Update($sectionID, ["UF_PHOTOS" => array_values($propertyValue['UF_PHOTOS'])])) {
                    if (unlink($fullSrc)) {
                        echo json_encode(["status" => 'success']);
                    } else {
                        echo json_encode(["status" => false, "error" => "Не удалось удалить файл с сервера."]);
                    }
            } else {
                echo json_encode(["status" => false, "error" => "Не удалось обновить секцию."]);
            }
        } else {
            echo json_encode(["status" => false, "error" => "Фото не найдено."]);
        }
    } else {
        echo json_encode(["status" => false, "error" => "Секция не найдена."]);
    }
}