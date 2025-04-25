<?php

namespace Exel\Loader\HLLoader\Users;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;
use CUser;
use Exel\Loader\HLLoader\HLLoaderAbstract;

class UsersLoader extends HLLoaderAbstract
{
    protected static string $hlCodeName = '';
    protected static mixed $hlEntity;


    /**
     * example
     * тут поля городов
     *
     *      [0] => ID
     *      [1] => Name
     *      [2] => Email
     *      [3] => Reg Date
     *      [4] => Reg Date Time
     *      [5] => email_list_ids
     *      [6] => Region
     *
     * соотношение столбцов CSV и имен в БД
     * uf_field =>  номер столбца
     **/
    protected static $fields = [
        'NAME' => 1,
        'EMAIL' => 2,
        'DATE_CREATE' => 3,
    ];


    /**
     * @throws SystemException
     */
    public static function loadData($data)
    {
        $rowCount = $resultCount = 0;
        foreach ($data as $row) {
            if ((empty($row[0]) && $row[0] == NULL) || $row['0'] == 'ID') {
                continue;
            }

            $rowCount++;
            $result = false;

            $result = self::findRow($row);

            if ($result) {
                $resultCount++;
                $messageRow[] = Loc::getMessage('EXEL_LOADER_SUCCESS_MESSAGE') . ' -> ' . implode(' | ', $row);
            } else {
                $messageRow[] = Loc::getMessage('EXEL_LOADER_ERROR_MESSAGE') . ' -> ' . implode(' | ', $row);
            }
        }

        return self::buildResponce($rowCount, $resultCount, $messageRow);
    }

    /**
     * Поиск и загрзука данных
     * @param $cityDepatureId
     * @param $cityArrivalId
     * @param $data
     * @return mixed
     */
    protected static function findRow($data): mixed
    {
        $issetRow = UserTable::query()
            ->addSelect('ID')
            ->where('EMAIL', '=', $data[self::$fields['EMAIL']])
            ->fetch();

        if (!$issetRow) {

            $user = new CUser();

            $password = self::generateRandomPassword(12); // Длина пароля — 12 символов

            $userId = $user->Add([
                'NAME' => $data[self::$fields['NAME']],
                'LOGIN' => $data[self::$fields['EMAIL']],
                'EMAIL' => $data[self::$fields['EMAIL']],
                'PASSWORD' => $password,
                'CONFIRM_PASSWORD' => $password,
            ]);

            if ($userId > 0) {
                $customDateCreate = new DateTime($data[self::$fields['DATE_CREATE']], 'Y-m-d H:i:s');
                $updateResult = $user->Update($userId, [
                    'TIMESTAMP_X' => $customDateCreate,
                    'DATE_REGISTER' => $customDateCreate,
                ]);

                return $updateResult;
            }

            return $user->LAST_ERROR;
        }
    }

    private static function generateRandomPassword($length = 12): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
        $password = '';
        $hasDigit = false;

        // Генерируем пароль, пока он не будет содержать хотя бы одну цифру
        while (!$hasDigit) {
            $password = '';
            for ($i = 0; $i < $length; $i++) {
                $password .= $chars[random_int(0, strlen($chars) - 1)];
            }

            // Проверяем, содержит ли пароль хотя бы одну цифру
            if (preg_match('/[0-9]/', $password)) {
                $hasDigit = true;
            }
        }

        return $password;
    }

}
