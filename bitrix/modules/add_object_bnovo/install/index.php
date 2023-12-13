<?

Class add_object_bnovo extends CModule
{
    var $MODULE_ID = "add_object_bnovo";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;

    function add_object_bnovo()
    {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->MODULE_NAME = "Добавление объекта Bnovo";
        $this->MODULE_DESCRIPTION = "Добавление объектов по внешнему ID";
    }

    function InstallFiles()
    {
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/ssd_search_keys/install/components",
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
        return true;
    }

    function UnInstallFiles()
    {
        DeleteDirFilesEx("/bitrix/components/add_object_bnovo");
        return true;
    }

    function DoInstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->InstallFiles();
        RegisterModule("ssd_search_keys");
        $APPLICATION->IncludeAdminFile("Установка модуля add_object_bnovo", $DOCUMENT_ROOT."/bitrix/modules/ssd_search_keys/install/step.php");
    }

    function DoUninstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->UnInstallFiles();
        UnRegisterModule("ssd_search_keys");
        $APPLICATION->IncludeAdminFile("Деинсталляция модуля add_object_bnovo", $DOCUMENT_ROOT."/bitrix/modules/ssd_search_keys/install/unstep.php");
    }
}
?>
