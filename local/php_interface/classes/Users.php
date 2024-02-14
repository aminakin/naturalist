<?

namespace Naturalist;

use Bitrix\Main\Data\Cache;
use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Loader,
    Bitrix\Main\Mail\Event;
use CUser;
use CFile;
use CSaleBasket;
use CSaleUser;
use Naturalist\Baskets;

defined("B_PROLOG_INCLUDED") && B_PROLOG_INCLUDED === true || die();

/**
 * @global CMain $APPLICATION
 * @global CUser $USER
 */
class Users
{
    private static $smsApiLogin = 'nikolay@naturalist.travel';
    private static $smsApiKey = 'XK9h1ItIdgaRgJu33qJWrvZHkiUE';

    private static $weatherApiURL = 'https://api.weatherapi.com/v1';
    private static $weatherApiKey = '0e2555562589491182d70650222010';

    private static $favouriteCookieName = 'USER_FAVOURITES';

    private $avatarsDir = 'users/photos';

    public function avatarGen() {
        return CFile::MakeFileArray(SITE_TEMPLATE_PATH."/img/av-".rand(1, 5).".png");
    }

    public static function getInnerScore()
    {
        Loader::includeModule('sale');

        if (!\Bitrix\Main\Engine\CurrentUser::get()->getId()) {
            return 0;
        }

        return \CSaleUserAccount::GetByUserID(
            \Bitrix\Main\Engine\CurrentUser::get()->getId(), 'RUB'
        )['CURRENT_BUDGET'];
    }

    // Получения кода для авторизации
    public function authGetCode($params)
    {
        $user = new CUser();

        $type = $params['type'];
        $login = $params['login'];
        $email = $params['email'];
        $name = $params['name'];
        $last_name = $params['last_name'];

        if ($type == 'phone') {
            $filter = array("PERSONAL_PHONE" => $login);
        } elseif ($type == 'email') {
            $filter = array("EMAIL" => $login);
        }
        $arUser = CUser::GetList(($by = "ID"), ($order = "ASC"), $filter)->Fetch();

        $code = $this->generateCode(4);

        if (!$arUser) {
            $arFields = array(
                "ACTIVE" => "N",
                "LOGIN" => $login,
                "EMAIL" => ($type == 'email') ? $login : $email,
                "PERSONAL_PHONE" => ($type == 'phone') ? $login : "",
                "PASSWORD" => $code.$code,
                "CONFIRM_PASSWORD" => $code.$code,
                "NAME" => $name,
                "LAST_NAME" => $last_name,
                "UF_AUTH_CODE" => $code,
                "UF_AUTH_TYPE" => $type,
                "UF_SUBSCRIBE_EMAIL_1" => 1
            );
            if (!empty($photo)) {
                $arFields["PERSONAL_PHOTO"] = CFile::MakeFileArray($photo);
            } else {
                $arFields["PERSONAL_PHOTO"] = $this->avatarGen();
            }

            $userId = $user->Add($arFields);

        } else {
            $userId = $arUser["ID"];
            $updRes = $user->Update($arUser["ID"], array(
                "UF_AUTH_CODE" => $code,
            ));
        }

        if (!$params['fromOrder']) {
            if ($userId > 0) {
                // if ($type == 'phone') {
                //     $res = $this->sendCodeBySMS($login, $code);
                // } elseif ($type == 'email') {
                //     $res = $this->sendCodeByEmail($login, $code, $userId, $arUser);
                // }
    
                $res = true;
    
                if ($res) {
                    return json_encode([
                        "MESSAGE" => "Код успешно отправлен."
                    ]);
    
                } else {
                    return json_encode([
                        "ERROR" => "Ошибка при отправке кода."
                    ]);
                }
    
            } else {
                return json_encode([
                    "ERROR" => $user->LAST_ERROR
                ]);
            }
        } else {
            return $userId;
        }
    }

    // Авторизация по коду
    public function login($params)
    {
        $type = $params['type'];
        $page = $params['page'];
        $code = $params['code'];
        $login = $params['login'];
        $auth_from_order = $params['auth_from_order'];

        if ($type == 'phone') {
            $filter = array("PERSONAL_PHONE" => $login);
        } elseif ($type == 'email') {
            $filter = array("EMAIL" => $login);
        }
        if ($filter) {
            $arUser = CUser::GetList(($by = "sort"), ($order = "desc"), $filter,
                array("SELECT" => array("UF_*")))->Fetch();
        }

        if ($arUser) {
            if ($arUser["UF_AUTH_CODE"] === $code) {
                $userId = $arUser["ID"];
                self::auth($userId, $arUser);

                return json_encode([
                    "MESSAGE" => "Вы успешно вошли",
                    "NO_RELOAD" => $auth_from_order,
                    "USER_ID" => $userId,
                    "REDIRECT_URL" => ($page == "/order/") ? "/order/" : "/personal/"
                ]);

            } else {
                return json_encode([
                    "ERROR" => "Неверный код"
                ]);
            }

        } else {
            return json_encode([
                "ERROR" => "Такой пользователь не найден."
            ]);
        }
    }

    // Авторизация через соцсети
    public function loginBySocnets($params)
    {
        $authType = $params['type'];
        $page = $params['page'];
        $login = $params['login'];
        $name = $params['name'] ?? '';
        $lastname = $params['lastname'] ?? '';
        $photo = $params['photo'] ?? '';

        $arUser = CUser::GetByLogin($login)->Fetch();

        if ($arUser) {
            $userId = $arUser["ID"];
        } else {
            $user = new CUser();

            $code = $this->generateCode(4);
            $arFields = array(
                "ACTIVE" => "Y",
                "LOGIN" => $login,
                "NAME" => $name,
                "LAST_NAME" => $lastname,
                "PASSWORD" => $code.$code,
                "CONFIRM_PASSWORD" => $code.$code,
                "UF_AUTH_TYPE" => $authType,
                "UF_SUBSCRIBE_EMAIL_1" => 1,
            );
            if (!empty($photo)) {
                $arFields["PERSONAL_PHOTO"] = CFile::MakeFileArray($photo);
            } else {
                $arFields["PERSONAL_PHOTO"] = $this->avatarGen();
            }

            $userId = $user->Add($arFields);
        }

        if (intval($userId) > 0) {
            self::auth($userId);

            return json_encode([
                "MESSAGE" => "Вы успешно вошли",
                "USER_ID" => $userId,
                "REDIRECT_URL" => ($page == "/order/") ? "/order/" : "/personal/"
            ]);

        } else {
            return json_encode([
                "ERROR" => "Ошибка при входе через соцсеть."
            ]);
        }
    }

    // Процесс авторизации
    private static function auth($userId, $arUser = false)
    {
        global $USER;
        Loader::includeModule("sale");

        // Получение FUSER_ID неавторизованного пользователя
        if (!CSaleUser::getFUserCode()) {
            $fUserId = $_SESSION["SALE_USER_ID"];
        }

        // Активируем пользователя, если неактивен
        if (!empty($arUser) && $arUser["ACTIVE"] == "N") {
            $user = new CUser();
            $user->Update($userId, array(
                "ACTIVE" => "Y",
            ));
        }

        //Удаление старой корзины
        $fUserIdOld = CSaleUser::GetList(array("USER_ID" => (int)$userId))["ID"];

        if (!empty($fUserIdOld)) {
            CSaleBasket::DeleteAll($fUserIdOld);
        }

        // Авторизовываем пользователя
        $USER->Authorize($userId);

        // Сохранение корзины неавторизованного пользователя
        if (!empty($fUserId) && !empty($fUserIdOld)) {

            //CSaleBasket::DeleteAll($fUserIdBasket);


            $newFUserId = CSaleUser::getFUserCode();
            $res = CSaleBasket::TransferBasket($fUserId, $newFUserId);

            //CSaleUser::Update($newFUserId);
        }

        // Сохранение избранного неавторизованного пользователя
        self::saveFavourites($userId);
        // Очистка кеша
        self::cleanCache($userId);
    }

    // Очистка кеша
    private static function cleanCache($userId)
    {
        $cache = Cache::createInstance();
        $cacheId = 'user_' . $userId;
        $cache->clean($cacheId, 'users');
    }

    // Получение текущего пользователя
    public static function getUser()
    {
        global $USER;
        $userId = $USER->GetID();

        if ($userId > 0) {
            $cache = Cache::createInstance();
            $cachePath = 'users';
            $cacheTtl = 3600;
            $cacheId = 'user_' . $userId;

            if ($cache->initCache($cacheTtl, $cacheId, $cachePath)) {
                $vars = $cache->getVars();
                $arUser = $vars[$cacheId];

            } elseif ($cache->startDataCache()) {
                $arUser = CUser::GetByID($userId)->GetNext();

                if ($arUser) {
                    if ($arUser["PERSONAL_PHOTO"]) {
                        $arUser["PERSONAL_PHOTO"] = CFile::GetFileArray($arUser["PERSONAL_PHOTO"]);
                    }

                    if ($arUser["UF_GUESTS_DATA"]) {
                        $arUser["UF_GUESTS_DATA"] = json_decode($arUser["~UF_GUESTS_DATA"], true);
                    }
                    if ($arUser["UF_FAVOURITES"]) {
                        $arUser["UF_FAVOURITES"] = explode(",", $arUser["UF_FAVOURITES"]);
                    }

                    $cache->endDataCache(array(
                        $cacheId => $arUser
                    ));
                }
            }
        }

        return $arUser ?? [];
    }

    // Выход из профиля
    public static function logout()
    {
        global $USER;
        global $userId;

        self::cleanCache($userId);

        $USER->Logout();
        LocalRedirect('/');
    }

    // Обновление профиля
    public function update($params)
    {
        global $userId, $arUser;

        if ($arUser) {
            $user = new CUser();

            $arFields = array();
            if (!empty($params["surname"])) {
                $arFields["LAST_NAME"] = $params["surname"];
            }
            if (!empty($params["name"])) {
                $arFields["NAME"] = $params["name"];
            }
            if (!empty($params["lastname"])) {
                $arFields["SECOND_NAME"] = $params["lastname"];
            }


            if (!empty($params["email"]) && !empty($params["code"])) {
                if (empty($arUser["UF_EMAIL_CODE"]) || empty($arUser["UF_EMAIL_CHANGE"]) || $params["code"] != $arUser["UF_EMAIL_CODE"] || $params["email"] != $arUser["UF_EMAIL_CHANGE"]) {
                    return json_encode([
                        "ERROR" => "Ошибка при смене почты. Передан неверный код"
                    ]);
                }

                $arFields["EMAIL"] = $params["email"];
                $arFields["UF_EMAIL_CODE"] = "";
                $arFields["UF_EMAIL_CHANGE"] = "";
            }

            if (!empty($params["phone"]) && !empty($params["code"])) {
                if (empty($arUser["UF_PHONE_CODE"]) || empty($arUser["UF_PHONE_CHANGE"]) || $params["code"] != $arUser["UF_PHONE_CODE"] || $params["phone"] != $arUser["UF_PHONE_CHANGE"]) {
                    return json_encode([
                        "ERROR" => "Ошибка при смене телефона. Передан неверный код"
                    ]);
                }

                $arFields["PERSONAL_PHONE"] = $params["phone"];
                $arFields["UF_PHONE_CODE"] = "";
                $arFields["UF_PHONE_CHANGE"] = "";
            }

            if ($params['avatar']) {

                $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $params['avatar']));
                if (!$_SERVER['DOCUMENT_ROOT']) {
                    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . "/../../../");
                }
                file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/upload/tmp/avatar-' . $userId . '.png', $data);
                $arFile = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"] . '/upload/tmp/avatar-' . $userId . '.png');
                $arFields["PERSONAL_PHOTO"] = $arFile;
            }
            if (!empty($params["deleteAvatar"])) {
                $arFields["PERSONAL_PHOTO"] = array('del' => 'Y');
            }

            if (isset($params["subscribe-email-1"])) {
                $arFields["UF_SUBSCRIBE_EMAIL_1"] = $params["subscribe-email-1"];
            }
            if (isset($params["subscribe-sms-1"])) {
                $arFields["UF_SUBSCRIBE_SMS_1"] = $params["subscribe-sms-1"];
            }

            $res = $user->Update($userId, $arFields);

            if ($res) {
                self::cleanCache($userId);

                return json_encode([
                    "MESSAGE" => "Профиль изменён",
                    "RELOAD" => true
                ]);

            } else {
                return json_encode([
                    "ERROR" => "Произошла ошибка при изменении профиля"
                ]);
            }

        } else {
            return json_encode([
                "ERROR" => "Необходима авторизация"
            ]);
        }
    }

    // Получение кода для смены полей
    public function updateGetCode($type, $value)
    {
        global $userId, $arUser;

        if ($arUser) {
            $user = new CUser();
            $arFields = array();

            if ($type == "email") {
                $arExistUser = CUser::GetList(($by = "ID"), ($order = "ASC"), array("EMAIL" => $value))->Fetch();
                if ($arExistUser) {
                    return json_encode([
                        "ERROR" => "Данная почта уже существует в системе."
                    ]);
                }

                $code = $this->generateCode(4);
                $arFields["UF_EMAIL_CHANGE"] = $value;
                $arFields["UF_EMAIL_CODE"] = $code;

                $resSend = $this->sendCodeByEmail($value, $code, $userId, $arUser);
            }
            if ($type == "phone") {
                $arExistUser = CUser::GetList(($by = "ID"), ($order = "ASC"),
                    array("PERSONAL_PHONE" => $value))->Fetch();
                if ($arExistUser) {
                    return json_encode([
                        "ERROR" => "Данный телефон уже существует в системе."
                    ]);
                }

                $code = $this->generateCode(4);
                $arFields["UF_PHONE_CHANGE"] = $value;
                $arFields["UF_PHONE_CODE"] = $code;

                $resSend = $this->sendCodeBySMS($value, $code);
            }

            $resUpd = $user->Update($userId, $arFields);
            if ($resSend && $resUpd) {
                self::cleanCache($userId);

                return json_encode([
                    "MESSAGE" => "Код успешно отправлен"
                ]);

            } else {
                return json_encode([
                    "ERROR" => "Ошибка при отправке кода."
                ]);
            }

        } else {
            return json_encode([
                "ERROR" => "Необходима авторизация."
            ]);
        }
    }

    // Получение списка избранного
    public static function getFavourites()
    {
        global $isAuthorized, $arUser;

        if ($isAuthorized) {
            $arFavouritesIDs = $arUser["UF_FAVOURITES"];
        } else {
            $arFavouritesIDs = (isset($_COOKIE[self::$favouriteCookieName]) && !empty($_COOKIE[self::$favouriteCookieName])) ? explode(',',
                $_COOKIE[self::$favouriteCookieName]) : [];
        }

        return $arFavouritesIDs;
    }

    // Добавление элемента в избранное
    public static function addFavourites($elementId)
    {
        global $isAuthorized, $userId, $arUser;

        $arFavouritesIDs = self::getFavourites();

        if ($isAuthorized) {
            if (!$arFavouritesIDs || !in_array($elementId, $arFavouritesIDs)) {
                $arFavouritesIDs[] = $elementId;
            }

            $user = new CUser();
            $res = $user->Update($userId, array(
                "UF_FAVOURITES" => implode(',', $arFavouritesIDs)
            ));

            if ($res) {
                self::cleanCache($userId);

                return json_encode([
                    "MESSAGE" => "Элемент успешно добавлен в избранное.",
                    "RELOAD" => true
                ]);

            } else {
                return json_encode([
                    "ERROR" => "Произошла ошибка при добавлении в избранное."
                ]);
            }

        } else {
            if (!$arFavouritesIDs || !in_array($elementId, $arFavouritesIDs)) {
                $arFavouritesIDs[] = $elementId;
            }

            setcookie(self::$favouriteCookieName, implode(',', $arFavouritesIDs), time() + 86400 * 30, "/");

            return json_encode([
                "MESSAGE" => "Элемент успешно добавлен в избранное.",
                "RELOAD" => true
            ]);
        }
    }

    // Удаление элемента из избранного
    public static function removeFavourites($elementId)
    {
        global $isAuthorized, $userId, $arUser;

        $arFavouritesIDs = self::getFavourites();
        if ($isAuthorized) {
            $index = array_search($elementId, $arFavouritesIDs);
            unset($arFavouritesIDs[$index]);

            $user = new CUser();
            $res = $user->Update($userId, array(
                "UF_FAVOURITES" => implode(',', $arFavouritesIDs)
            ));

            if ($res) {
                self::cleanCache($userId);

                return json_encode([
                    "MESSAGE" => "Элемент успешно удалён из избранного.",
                    "RELOAD" => true
                ]);

            } else {
                return json_encode([
                    "ERROR" => "Произошла ошибка при удалении из избранного."
                ]);
            }

        } else {
            $index = array_search($elementId, $arFavouritesIDs);
            unset($arFavouritesIDs[$index]);

            setcookie(self::$favouriteCookieName, implode(',', $arFavouritesIDs), time() + 86400 * 30, "/");

            return json_encode([
                "MESSAGE" => "Элемент успешно удалён из избранного.",
                "RELOAD" => true
            ]);
        }
    }

    // Сохранение избранного из куки в пользовательское свойство UF_FAVOURITES
    private static function saveFavourites($userId)
    {
        $arUser = CUser::GetByID($userId)->GetNext();
        if ($arUser["UF_FAVOURITES"]) {
            $arUser["UF_FAVOURITES"] = explode(",", $arUser["UF_FAVOURITES"]);
        }

        $arCookieFavouritesIDs = (isset($_COOKIE[self::$favouriteCookieName]) && !empty($_COOKIE[self::$favouriteCookieName])) ? explode(',',
            $_COOKIE[self::$favouriteCookieName]) : [];
        $arUserFavouritesIDs = $arUser["UF_FAVOURITES"] ?? [];
        $arFavouritesIDs = array_unique($arCookieFavouritesIDs + $arUserFavouritesIDs);

        $user = new CUser();
        $user->Update($userId, array(
            "UF_FAVOURITES" => implode(',', $arFavouritesIDs)
        ));
    }

    // Получение IP-адреса
    public static function getIP()
    {
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }

        return $ip;
    }

    // Получение города
    public static function getLocation()
    {
        $url = self::$weatherApiURL . '/current.json';
        $data = array(
            "key" => self::$weatherApiKey,
            "q" => self::getIP(),
            "lang" => "ru"
        );

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POSTFIELDS => $data
        ));
        $response = curl_exec($ch);
        $arResponse = json_decode($response, true);
        curl_close($ch);

        return [
            'coords' => [
                'lat' => $arResponse['location']['lat'],
                'lon' => $arResponse['location']['lon'],
            ],
            'name' => $arResponse['location']['name'],
        ];
    }

    // Получение метеоинформации
    public static function getMeteo($q = null)
    {
        if (!$q) {
            $q = self::getIP();
        }

        $cache = Cache::createInstance();
        $cachePath = 'meteo';
        $cacheTtl = 43200;
        $cacheId = 'meteo_' . md5($q);

        if ($cache->initCache($cacheTtl, $cacheId, $cachePath)) {
            $vars = $cache->getVars();
            $arMeteo = $vars['meteo'];

        } elseif ($cache->startDataCache()) {
            $url = self::$weatherApiURL . '/current.json';
            $data = array(
                "key" => self::$weatherApiKey,
                "q" => $q,
                "lang" => "ru"
            );
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_POSTFIELDS => $data
            ));
            $response = curl_exec($ch);
            $arWeatherObject = json_decode($response, true);
            curl_close($ch);

            $url = self::$weatherApiURL . '/astronomy.json';
            $data = array(
                "key" => self::$weatherApiKey,
                "q" => $q,
                "lang" => "ru"
            );
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_POSTFIELDS => $data
            ));
            $response = curl_exec($ch);
            $arAstroObject = json_decode($response, true);
            curl_close($ch);

            $arMeteo = ($arWeatherObject['location'] && $arWeatherObject['current'] && $arAstroObject['astronomy']) ? [
                'name' => $arWeatherObject['location']['name'],
                'coords' => [
                    'lat' => $arWeatherObject['location']['lat'],
                    'lon' => $arWeatherObject['location']['lon'],
                ],
                'sunrise_time' => date("H:i", strtotime($arAstroObject['astronomy']['astro']['sunrise'])),
                'sunset_time' => date("H:i", strtotime($arAstroObject['astronomy']['astro']['sunset'])),
                'humidity' => $arWeatherObject['current']['humidity'],
                'temp' => ($arWeatherObject['current']['temp_c'] > 0) ? "+" . $arWeatherObject['current']['temp_c'] : $arWeatherObject['current']['temp_c'],
            ] : false;

            $cache->endDataCache(array(
                'meteo' => $arMeteo
            ));
        }

        return $arMeteo;
    }

    // Генерация кода
    private function generateCode($digits = 4)
    {
        $i = 0;
        $pin = '';
        while ($i < $digits) {
            $pin .= mt_rand(0, 9);
            $i++;
        }

        return $pin;
    }

    // Отправка кода в СМС (обертка)
    private function sendCodeBySMS($phone, $code)
    {
        $text = "Ваш код для регистрации на сайте naturalist.travel: " . $code;
        $sign = "Naturalist";

        $res = self::sendSMS($phone, $text, $sign);
        return $res;
    }

    // Отправка кода на почту (обертка)
    private function sendCodeByEmail($email, $code, $userId, $arUser)
    {
        $res = self::sendEmail("USER_CODE_REQUEST", "10", array(
            "EMAIL" => $email,
            "CODE" => $code,
            "ID" => $userId,
            "LOGIN" => $arUser["LOGIN"] ?? $email,
        ));

        return $res;
    }

    // Отправка СМС
    public static function sendSMS($phone, $text, $sign)
    {
        $url = 'https://gate.smsaero.ru/v2/sms/send';
        $data = array(
            "number" => $phone,
            "text" => $text,
            "sign" => $sign,
        );

        $defaults = array(
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => $url,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_USERPWD => self::$smsApiLogin . ":" . self::$smsApiKey,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        );

        $ch = curl_init();
        curl_setopt_array($ch, $defaults);
        $result = curl_exec($ch);
        $arResponse = json_decode($result, true);
        curl_close($ch);

        //return $arResponse['success'] ? 1 : 0;
        return 1;
    }

    // Отправка почты
    public static function sendEmail($eventName, $templateId, $data, $files = [])
    {
        $res = Event::send(array(
            "EVENT_NAME" => $eventName,
            "MESSAGE_ID" => $templateId,
            "LID" => "s1",
            "C_FIELDS" => $data,
            "FILE" => $files,
        ));

        return $res;
    }
}