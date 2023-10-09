<?php

namespace Sprint\Migration;


class Version20231004144241 extends Version
{
    protected $description = "101021 | Добавить миграцию для установки цен / рубрик | создание рубрики для рассылки";

    protected $moduleVersion = "4.4.1";

    public function up()
    {
        \Bitrix\Main\Loader::includeModule('subscribe');

        $rubric = new \CRubric;
        $arFields = Array(
            "ACTIVE" => "Y",
            "NAME" => "Новости",            
            "LID" => "s1",            
        );
        $ID = $rubric->Add($arFields);
        if($ID == false)
            echo $rubric->LAST_ERROR;
    }

    public function down()
    {
        //your code ...
    }
}
