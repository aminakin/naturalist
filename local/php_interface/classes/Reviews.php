<?

namespace Naturalist;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;
use CModule;
use CIBlockElement;
use CUser;
use CFile;
use NaturalistCatalog;

Loader::IncludeModule("iblock");

defined("B_PROLOG_INCLUDED") && B_PROLOG_INCLUDED === true || die();

/**
 * @global CMain $APPLICATION
 * @global CUser $USER
 */
class Reviews
{
    private $photosDir = 'users/reviews';

    /* Получение списка отзывов */
    public function getList($arSort = false, $arFilter = false, $arSelect = false, $iblockId = REVIEWS_IBLOCK_ID)
    {
        if (!$arSort) {
            $arSort = array("SORT" => "ASC");
        }

        if (!$arFilter) {
            $arFilter = array("IBLOCK_ID" => $iblockId, "ACTIVE" => "Y");
        } else {
            $arFilter = array_merge(array("IBLOCK_ID" => $iblockId, "ACTIVE" => "Y"), $arFilter);
        }

        if (!$arSelect) {
            $arSelect = array("IBLOCK_ID", "ID", "NAME", "DATE_CREATE", "DETAIL_TEXT");
        }

        $rsItems = CIBlockElement::GetList(
            $arSort,
            $arFilter,
            false,
            false,
            $arSelect
        );

        $arReviews = array();
        while ($obItem = $rsItems->GetNextElement()) {
            $arReview = $obItem->GetFields();
            $arReview['PROPERTIES'] = $obItem->GetProperties();

            foreach ($arReview["PROPERTIES"]["PHOTOS"]["VALUE"] as $photoId) {
                $arReview["PICTURES"][] = CFile::GetFileArray($photoId);
            }

            $arReviews[] = $arReview;
        }

        return $arReviews;
    }

    /* Получение списка отзывов по Id кемпинга */
    public function getListByCampingId($campingId)
    {
        $arReviews = $this->getList(array("ID" => "DESC"), array("PROPERTY_CAMPING_ID" => intval($campingId)));
        return $arReviews;
    }

    /* Получение списка отзывов по Id пользователя */
    public function getListByUserId($userId)
    {
        $arReviews = $this->getList(array("ID" => "DESC"), array("PROPERTY_USER_ID" => $userId));
        return $arReviews;
    }

    /* Получение рейтинга по Id кемпинга */
    public static function getCampingRating($arCampingIDs)
    {
        $rsItems = CIBlockElement::GetList(
            false,
            array(
                "IBLOCK_ID" => REVIEWS_IBLOCK_ID,
                "ACTIVE" => "Y",
                "PROPERTY_CAMPING_ID" => (array)$arCampingIDs
            ),
            false,
            false,
            array(
                "ID",
                "PROPERTY_RATING",
                "PROPERTY_CAMPING_ID",
                "PROPERTY_CRITERION_1",
                "PROPERTY_CRITERION_2",
                "PROPERTY_CRITERION_3",
                "PROPERTY_CRITERION_4",
                "PROPERTY_CRITERION_5",
                "PROPERTY_CRITERION_6",
                "PROPERTY_CRITERION_7",
                "PROPERTY_CRITERION_8"
            )
        );

        $arRatings = array();
        while ($arReview = $rsItems->Fetch()) {
            $arRatings[$arReview["PROPERTY_CAMPING_ID_VALUE"]]["RATING"][] = $arReview["PROPERTY_RATING_VALUE"];
            $arRatings[$arReview["PROPERTY_CAMPING_ID_VALUE"]]["REVIEW"][] = $arReview;
        }


        $arRatingsAvg = array();
        foreach ($arRatings as $campingId => $arRatingItem) {
            $countRating = 0;
            $arAvgCriterias = [];
            foreach ($arRatingItem["RATING"] as $value) {
                if ($value > 0) {
                    $countRating += 1;
                }
            }

            if ($countRating != 0) {
                $arRatingsAvg[$campingId]["avg"] = (count($arRatingItem["RATING"]) > 0) ? round(array_sum($arRatingItem["RATING"]) / $countRating, 1) : 0;
                $arRatingsAvg[$campingId]["count"] = count($arRatingItem["RATING"]);

                foreach ($arRatingItem["REVIEW"] as $review) {
                    for($i = 1; $i <= 8; $i++) {
                        if($review["PROPERTY_CRITERION_".$i."_VALUE"] > 0){
                            $arAvgCriterias[$i][0]['value'] += $review["PROPERTY_CRITERION_".$i."_VALUE"];
                            $arAvgCriterias[$i][0]['count'] += 1;
                        }
                    }
                }
                // Средние значения
                for($i = 1; $i <= 8; $i++) {
                    if(!empty($arAvgCriterias[$i][0]['count'])){
                        $arAvgCriterias[$i][0] = number_format(round($arAvgCriterias[$i][0]['value']/$arAvgCriterias[$i][0]['count'], 1), 1, '.', '');
                        $arAvgCriterias[$i][1] = round($arAvgCriterias[$i][0] * 100 / 5);
                    }
                }

                $arRatingsAvg[$campingId]["criterials"] = $arAvgCriterias;
            }


        }

        return $arRatingsAvg;
    }

    /* Получение отзыва по Id */
    public function get($reviewId, $arSelect = false)
    {
        if (!$arSelect) {
            $arSelect = array("IBLOCK_ID", "ID", "NAME", "DATE_CREATE", "DETAIL_TEXT");
        }

        $obItem = CIBlockElement::GetList(
            false,
            array(
                //"IBLOCK_ID" => REVIEWS_IBLOCK_ID,
                "ID" => intval($reviewId)
            ),
            false,
            false,
            $arSelect
        )->GetNextElement();

        if ($obItem) {
            $arReview = $obItem->GetFields();
            $arReview['PROPERTIES'] = $obItem->GetProperties();

            foreach ($arReview["PROPERTIES"]["PHOTOS"]["VALUE"] as $photoId) {
                $arReview["PICTURES"][$photoId] = CFile::GetFileArray($photoId);
            }
        }

        return $arReview ?? array();
    }

    /* Добавление отзыва */
    public function add($params)
    {
        global $userId;
        $el = new CIBlockElement();

        $arPhotos = [];
        if ($_FILES['files']) {
            $arPhotos = $this->makeFilesArray($_FILES['files']);            
        }

        if (isset($params["criterias"]) && !empty($params["criterias"])) {
            $countRating = 0;
            foreach ($params["criterias"] as $value) {
                if ($value > 0) {
                    $countRating += 1;
                }
            }
            if ($countRating != 0) {
                $rating = round(array_sum($params["criterias"]) / $countRating, 1);
            }
        }

        $res = CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => REVIEWS_IBLOCK_ID,
                'NAME' => $params['name'],
                'DETAIL_TEXT' => $params['text'],
                'PROPERTY_CAMPING_ID_VALUE' => $params['campingId'],
                'PROPERTY_USER_ID_VALUE' => $userId,
                'PROPERTY_PHOTOS_VALUE' => $arPhotos,
                'PROPERTY_CRITERION_1_VALUE' => $params['criterias'][1],
                'PROPERTY_CRITERION_2_VALUE' => $params['criterias'][2],
                'PROPERTY_CRITERION_3_VALUE' => $params['criterias'][3],
                'PROPERTY_CRITERION_4_VALUE' => $params['criterias'][4],
                'PROPERTY_CRITERION_5_VALUE' => $params['criterias'][5],
                'PROPERTY_CRITERION_6_VALUE' => $params['criterias'][6],
                'PROPERTY_CRITERION_7_VALUE' => $params['criterias'][7],
                'PROPERTY_CRITERION_8_VALUE' => $params['criterias'][8],
                'PROPERTY_RATING_VALUE' => $rating
            ],
            false,
            [
                'nPageSize' => 1
            ],
            [
                'ID'
            ]
        );

        $existReview = false;

        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();

            if ($arFields && $arFields['ID']) {
                $existReview = true;
            }
        }

        if ($existReview) {
            return json_encode([
                'ERROR' => 'Вы отправили слишком много запросов.'
            ]);
        }

        $arFields = array(
            "IBLOCK_ID" => REVIEWS_IBLOCK_ID,
            "ACTIVE" => "N",
            "NAME" => $params["name"],
            "DETAIL_TEXT" => $params["text"],
            "DETAIL_TEXT_TYPE" => "text",
            "PROPERTY_VALUES" => array(
                "CAMPING_ID" => $params["campingId"],
                "USER_ID" => $userId,
                "ORDER_ID" => $params["orderId"],
                "PHOTOS" => $arPhotos,
                "CRITERION_1" => $params["criterias"][1],
                "CRITERION_2" => $params["criterias"][2],
                "CRITERION_3" => $params["criterias"][3],
                "CRITERION_4" => $params["criterias"][4],
                "CRITERION_5" => $params["criterias"][5],
                "CRITERION_6" => $params["criterias"][6],
                "CRITERION_7" => $params["criterias"][7],
                "CRITERION_8" => $params["criterias"][8],
                "RATING" => $rating
            )
        );

        $elementId = $el->Add($arFields);
        if ($elementId) {
            return json_encode([
                "ID" => $elementId,
                "MESSAGE" => "Отзыв успешно добавлен.",
                "RELOAD" => true
            ]);

        } else {
            return json_encode([
                "ERROR" => "Произошла ошибка при добавлении отзыва."
            ]);
        }
    }
    
    /**
     * Добавляет отзыв по сертификатам
     *
     * @param array $params
     * 
     * @return [type]
     * 
     */
    public function addCertReview($params)   {
        global $userId;
        $el = new CIBlockElement();

        $arPhotos = [];
        if ($_FILES['files']) {
            $arPhotos = $this->makeFilesArray($_FILES['files']);            
        }

        $arFields = array(
            "IBLOCK_ID" => CERT_REVIEWS_IBLOCK_ID,
            "ACTIVE" => "N",
            "NAME" => $params["name"],
            "DETAIL_TEXT" => $params["text"],
            "DETAIL_TEXT_TYPE" => "text",
            "PROPERTY_VALUES" => array(            
                "CRITERION_1" => $params["rating"],
                "RATING" => $params["rating"],
                "USER_ID" => $userId,
                "ORDER_ID" => $params["orderId"],
                "PHOTOS" => $arPhotos,
            )
        );

        $elementId = $el->Add($arFields);
        if ($elementId) {
            return json_encode([
                "ID" => $elementId,
                "MESSAGE" => "Отзыв успешно добавлен.",
                "RELOAD" => true
            ]);

        } else {
            return json_encode([
                "ERROR" => "Произошла ошибка при добавлении отзыва."
            ]);
        }
    }

    /**
     * [Description for makeFilesArray]
     *
     * @param array $files
     * 
     * @return array
     * 
     */
    private function makeFilesArray($files) : array 
    {
        $result = [];
        foreach ($files['name'] as $key => $name) {
            $arInputFile = array(
                'name' => $files['name'][$key],
                'type' => $files['type'][$key],
                'tmp_name' => $files['tmp_name'][$key],
                'size' => $files['size'][$key],
            );

            $arFile = array_merge($arInputFile, array("del" => "Y", "MODULE_ID" => "iblock"));
            $fileId = CFile::SaveFile($arFile, $this->photosDir);
            if ($fileId) {
                $result[] = $fileId;
            }
        }

        return $result;
    }

    /* Обновление отзыва */
    public function update($reviewId, $params)
    {
        global $userId;
        $el = new CIBlockElement();

        if (intval($reviewId) == 0) {
            return json_encode([
                "ERROR" => "Ошибка доступа."
            ]);
        }
        $arReview = $this->get($reviewId);
        if ($arReview["PROPERTIES"]["USER_ID"]["VALUE"] != $userId) {
            return json_encode([
                "ERROR" => "Ошибка доступа."
            ]);
        }

        $arFields = array(
            "NAME" => $params["name"],
            "DETAIL_TEXT" => $params["text"],
        );
        $res = $el->Update($reviewId, $arFields);

        $arPhotos = array();
        if ($_FILES['files']) {
            foreach ($_FILES['files']['name'] as $key => $name) {
                $arInputFile = array(
                    'name' => $_FILES['files']['name'][$key],
                    'type' => $_FILES['files']['type'][$key],
                    'tmp_name' => $_FILES['files']['tmp_name'][$key],
                    'size' => $_FILES['files']['size'][$key],
                );

                $arFile = array_merge($arInputFile, array("del" => "Y", "MODULE_ID" => "iblock"));
                $fileId = CFile::SaveFile($arFile, $this->photosDir);
                if ($fileId) {
                    $arPhotos[] = CFile::MakeFileArray($fileId);
                }
            }
        }

        if (isset($params["criterias"]) && !empty($params["criterias"])) {
            $countRating = 0;
            foreach ($params["criterias"] as $value) {
                if ($value > 0) {
                    $countRating += 1;
                }
            }
            if ($countRating != 0) {
                $rating = round(array_sum($params["criterias"]) / $countRating, 1);
            }
        }

        $arProps = array(
            "PHOTOS" => $arPhotos,
            "CRITERION_1" => $params["criterias"][1],
            "CRITERION_2" => $params["criterias"][2],
            "CRITERION_3" => $params["criterias"][3],
            "CRITERION_4" => $params["criterias"][4],
            "CRITERION_5" => $params["criterias"][5],
            "CRITERION_6" => $params["criterias"][6],
            "CRITERION_7" => $params["criterias"][7],
            "CRITERION_8" => $params["criterias"][8],
            "RATING" => $rating
        );

        CIBlockElement::SetPropertyValuesEx($reviewId, $params['iblockId'], $arProps);

        if ($res) {
            return json_encode([
                "MESSAGE" => "Отзыв успешно обновлен.",
                "RELOAD" => true
            ]);

        } else {
            return json_encode([
                "ERROR" => "Произошла ошибка при изменении отзыва."
            ]);
        }
    }

    /* Удаление отзыва */
    public function delete($reviewId)
    {
        global $userId;

        if (intval($reviewId) == 0) {
            return json_encode([
                "ERROR" => "Ошибка доступа."
            ]);
        }
        $arReview = $this->get($reviewId);
        if ($arReview["PROPERTIES"]["USER_ID"]["VALUE"] != $userId) {
            return json_encode([
                "ERROR" => "Ошибка доступа."
            ]);
        }

        $res = CIBlockElement::Delete($reviewId);
        if ($res) {
            return json_encode([
                "MESSAGE" => "Отзыв успешно удален.",
                "RELOAD" => true
            ]);

        } else {
            return json_encode([
                "ERROR" => "Произошла ошибка при удалении отзыва."
            ]);
        }
    }

    /* Удаление фотографий отзыва */
    public function deletePhoto($reviewId, $photoId)
    {
        global $userId;

        if (intval($reviewId) == 0 || intval($photoId) == 0) {
            return json_encode([
                "ERROR" => "Ошибка доступа."
            ]);
        }
        $arReview = $this->get($reviewId);
        if ($arReview["PROPERTIES"]["USER_ID"]["VALUE"] != $userId) {
            return json_encode([
                "ERROR" => "Ошибка доступа."
            ]);
        } else {
            CFile::Delete($photoId);

            $arPhotos = $arReview["PICTURES"];
            unset($arPhotos[$photoId]);
            $arPhotoIDs = array_keys($arPhotos);

            if(isset($arPhotoIDs) && !empty($arPhotoIDs)){
                $arNewPhotos = array();
                foreach ($arPhotoIDs as $photoId) {
                    $arNewPhotos[] = CFile::MakeFileArray($photoId);
                }
                CIBlockElement::SetPropertyValuesEx($reviewId, REVIEWS_IBLOCK_ID, array(
                    "PHOTOS" => $arNewPhotos
                ));
            } else {
                CIBlockElement::SetPropertyValuesEx($reviewId, REVIEWS_IBLOCK_ID, array(
                    "PHOTOS" => array('VALUE' => array())
                ));
            }

            return json_encode([
                "MESSAGE" => "Фотография отзыва успешно удалена.",
                "RELOAD" => true
            ]);
        }
    }

    /* Получить лайки отзывов в виде списка сущностей */
    public static function getLikes($arReviewsIDs)
    {
        $entityClass = self::getEntityClass();

        $rsData = $entityClass::getList([
            "select" => ["*"],
            "filter" => [
                "UF_REVIEW_ID" => (array)$arReviewsIDs
            ],
            "order" => ["ID" => "ASC"],
        ]);

        $arItems = array();
        $arUsers = array();
        $arStats = array();
        while ($arEntity = $rsData->Fetch()) {
            $arItems[$arEntity["UF_REVIEW_ID"]][$arEntity["UF_USER_ID"]] = $arEntity["UF_VALUE"];
            $arUsers[$arEntity["UF_REVIEW_ID"]] = $arEntity["UF_USER_ID"];
            $arStats[$arEntity["UF_REVIEW_ID"]][$arEntity["UF_VALUE"]]++;
        }

        return [
            "ITEMS" => $arItems,
            "USERS" => $arUsers,
            "STATS" => $arStats
        ];
    }

    /* Добавить лайк в отзыв */
    public function addLike($reviewId, $value)
    {
        global $userId;
        $entityClass = $this->getEntityClass();

        $rsData = $entityClass::getList([
            "select" => ["*"],
            "filter" => [
                "UF_REVIEW_ID" => $reviewId,
                "UF_USER_ID" => $userId,
            ],
            "order" => ["ID" => "ASC"],
        ]);
        $arData = $rsData->Fetch();

        if (!$arData) {
            $arValues = array(
                'UF_REVIEW_ID' => $reviewId,
                'UF_USER_ID' => $userId,
                'UF_ADD_DATE' => time(),
                'UF_VALUE' => $value
            );
            $entityId = $entityClass::add($arValues);

            if ($entityId > 0) {
                return json_encode([
                    "MESSAGE" => "Оценка успешно добавлена.",
                    "RELOAD" => true
                ]);

            } else {
                return json_encode([
                    "ERROR" => "Произошла ошибка при добавлении оценки на отзыв."
                ]);
            }

        } else {
            return json_encode([
                "ERROR" => "Невозможно добавить оценку на отзыв. Оценка уже была добавлена."
            ]);
        }
    }

    /* Удалить лайк с отзыва */
    public function deleteLike($reviewId)
    {
        global $userId;
        $entityClass = $this->getEntityClass();

        $rsData = $entityClass::getList([
            "select" => ["*"],
            "filter" => [
                "UF_REVIEW_ID" => $reviewId,
                "UF_USER_ID" => $userId,
            ],
            "order" => ["ID" => "ASC"],
        ]);
        $arData = $rsData->Fetch();

        if ($arData) {
            $entityId = $arData['ID'];
            $result = $entityClass::Delete($entityId);

            if ($result) {
                return json_encode([
                    "MESSAGE" => "Оценка на отзыв успешно удалена.",
                    "RELOAD" => true
                ]);

            } else {
                return json_encode([
                    "ERROR" => "Произошла ошибка при удалении оценки на отзыв."
                ]);
            }

        } else {
            return json_encode([
                "ERROR" => "Оценки на отзыв не существует."
            ]);
        }
    }

    private static function getEntityClass($hlId = 6)
    {
        CModule::IncludeModule('highloadblock');
        $hlblock = HighloadBlockTable::getById($hlId)->fetch();
        $entity = HighloadBlockTable::compileEntity($hlblock);
        return $entity->getDataClass();
    }
}
