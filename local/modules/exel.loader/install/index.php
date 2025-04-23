<?php

use Bitrix\Main\Application;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class exel_loader extends CModule
{

    public function __construct()
    {
        $arModuleVersion = [];

        include __DIR__ . '/version.php';

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_ID = 'exel.loader';
        $this->MODULE_NAME = Loc::getMessage('EXEL_LOADER_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('EXEL_LOADER_MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = Loc::getMessage('EXEL_LOADER_MODULE_PARTNER_NAME');
        $this->PARTNER_URI = '#';
    }

    public function doInstall()
    {
        if (CheckVersion(ModuleManager::getVersion('main'), '20.00.00')) {
            ModuleManager::registerModule($this->MODULE_ID);
        } else {
            $APPLICATION->ThrowException(
                Loc::getMessage('EXEL_LOADER_INSTALL_ERROR_VERSION')
            );
        }

    }


    public function doUninstall()
    {
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}
