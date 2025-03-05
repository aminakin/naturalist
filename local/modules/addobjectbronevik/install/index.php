<?
class addobjectbronevik extends CModule
{
    var $MODULE_ID = "addobjectbronevik";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    function __construct()
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
        $this->MODULE_NAME = "addobjectbronevik – модуль";
        $this->MODULE_DESCRIPTION = "После установки вы сможете пользоваться модулем";
    }
    function InstallFiles()
    {
//        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/add_object_bronevik/install/components",
//            $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
        CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/local/modules/addobjectbronevik/install/cron/addObjectBronevikParserCron.php',
            $_SERVER["DOCUMENT_ROOT"].'/local/cron/addObjectBronevikParserCron.php');
        CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/local/modules/addobjectbronevik/install/admin/add_object_bronevik_rows_list.php',
            $_SERVER["DOCUMENT_ROOT"].'/bitrix/admin/add_object_bronevik_rows_list.php');

        return true;
    }
    function UnInstallFiles()
    {
        DeleteDirFilesEx($_SERVER["DOCUMENT_ROOT"].'/local/cron/addObjectBronevikParserCron.php');
        DeleteDirFilesEx($_SERVER["DOCUMENT_ROOT"].'/bitrix/admin/add_object_bronevik_rows_list.php');

        return true;
    }
    function DoInstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->InstallFiles();
        $this->InstallDB();
        RegisterModule("addobjectbronevik");
//        $APPLICATION->IncludeAdminFile("Установка модуля add_object_bronevik", $DOCUMENT_ROOT."/local/modules/add_object_bronevik/install/step.php");
    }
    function DoUninstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->UnInstallFiles();
        $this->UnInstallDB();
        UnRegisterModule("addobjectbronevik");
//        $APPLICATION->IncludeAdminFile("Деинсталляция модуля add_object_bronevik", $DOCUMENT_ROOT."/local/modules/add_object_bronevik/install/unstep.php");
    }

    function InstallDB()
    {
        global $DB;
        $this->errors = false;

        if (!$DB->TableExists('b_bronevik_advance_hotels'))
        {
            $this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/local/modules/addobjectbronevik/install/db//install.sql');
        }

        CAgent::AddAgent('Local\AddObjectBronevik\Agents\AddObjectBronevikParserAgent::parser()', 'addobjectbronevik', 'N', 43200, '', '');

        return true;
    }

    function UnInstallDB($arParams = Array())
    {
        global $DB;
        $this->errors = false;

        $this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . "/local/modules/addobjectbronevik/install/db/uninstall.sql");

        CAgent::RemoveAgent('addobjectbronevik');
        \COption::RemoveOption('addobjectbronevik');
    }
}
?>