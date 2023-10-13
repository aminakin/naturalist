<?php

namespace Sprint\Migration;


use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;

class Version20231013091622 extends Version
{
    protected $description   = "101310 | Баги | Отмеченные регионы избранным ";
    protected $moduleVersion = "4.4.1";

    /**
     * @throws Exceptions\MigrationException
     * @throws Exceptions\RestartException
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        Loader::includeModule('highloadblock');

        $regionEntity = HighloadBlockTable::compileEntity('Regions')->getDataClass();

        $regionsToUpd = $regionEntity::query()
            ->addSelect('ID')
            ->whereIn('ID', [1,2,3,4,5])
            ->fetchAll();

        foreach ($regionsToUpd as $region) {
            $regionEntity::update($region['ID'], [
                'UF_FAVORITE' => true
            ]);
        }

    }

    /**
     * @throws Exceptions\MigrationException
     * @throws Exceptions\RestartException
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function down()
    {
    }


}
