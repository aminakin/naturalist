<?php

use Bitrix\Main\Application;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class object_uhotels extends CModule
{
    public function __construct()
    {
        $arModuleVersion = [];
        include __DIR__ . '/version.php';

        if (is_array($arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'] ?? null;
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'] ?? null;
        }

        $this->MODULE_ID = 'object.uhotels';
        $this->MODULE_NAME = Loc::getMessage('OBJECT_UHOTELS_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('OBJECT_UHOTELS_MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = Loc::getMessage('OBJECT_UHOTELS_MODULE_PARTNER_NAME');
        $this->PARTNER_URI = '#';
    }

    public function doInstall()
    {
        if (!CheckVersion(ModuleManager::getVersion('main'), '14.00.00')) {
            global $APPLICATION;
            $APPLICATION->ThrowException(
                Loc::getMessage('OBJECT_UHOTELS_INSTALL_ERROR_VERSION')
            );
            return false;
        }

        $this->InstallFiles();
        ModuleManager::registerModule($this->MODULE_ID);
        return true;
    }

    public function doUninstall()
    {
        $this->UnInstallFiles();
        ModuleManager::unRegisterModule($this->MODULE_ID);
        return true;
    }

    public function InstallFiles()
    {
        $sourcePath = $_SERVER['DOCUMENT_ROOT'] . '/local/modules/' . $this->MODULE_ID . '/install/admin/object_uhotels.php';
        $targetPath = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/object_uhotels.php';

        CopyDirFiles($sourcePath, $targetPath, true, true);
        return true;
    }

    public function UnInstallFiles()
    {
        $filePath = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/object_uhotels.php';
        DeleteDirFilesEx($filePath);
        return true;
    }
}