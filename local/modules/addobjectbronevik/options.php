<?

use Bronevik\HotelsConnector\Enum\Endpoints;

$sModuleId  = 'addobjectbronevik';

CModule::IncludeModule( $sModuleId );

global $MESS;
IncludeModuleLangFile( __FILE__ );
 
if( $REQUEST_METHOD == 'POST' && $_POST['Update'] == 'Y' ) {
    COption::SetOptionString( $sModuleId, 'dump_login', $_POST['dump_login'] );
    COption::SetOptionString( $sModuleId, 'dump_password', $_POST['dump_password'] );
    COption::SetOptionString( $sModuleId, 'login', $_POST['login'] );
    COption::SetOptionString( $sModuleId, 'password', $_POST['password'] );
    COption::SetOptionString( $sModuleId, 'key', $_POST['key'] );
    COption::SetOptionString( $sModuleId, 'stand', $_POST['stand'] );
}

$aTabs = array(
    array(
        'DIV'   => 'edit1',
        'TAB'   => GetMessage('MAIN_TAB_SET'),
        'ICON'  => 'fileman_settings',
        'TITLE' => GetMessage('MAIN_TAB_TITLE_SET')
    ),
);

$oTabControl = new CAdmintabControl('tabControl', $aTabs);
$oTabControl->Begin();

?>
<form method="POST" enctype="multipart/form-data" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars( $sModuleId )?>&lang=<?echo LANG?>">
    <?=bitrix_sessid_post()?>
    <?$oTabControl->BeginNextTab();?>
    <tr class="heading">
        <td colspan="2">Дамп</td>
    </tr>
    <tr>
        <td width="50%" valign="top"><label for="option">Логин для скачивания дампа:</td>
        <td  valign="top">
            <input type="text" name="dump_login" id="dump_login" value="<?= COption::GetOptionString( $sModuleId, 'dump_login', '' ) ?>" />
        </td>
    </tr>
    <tr>
        <td width="50%" valign="top"><label for="option">Пароль для скачивания дампа:</td>
        <td  valign="top">
            <input type="text" name="dump_password" id="dump_password" value="<?= COption::GetOptionString( $sModuleId, 'dump_password', '' ) ?>" />
        </td>
    </tr>
    <tr class="heading">
        <td colspan="2">API броневик</td>
    </tr>
    <tr>
        <td width="50%" valign="top"><label for="option">Логин:</td>
        <td  valign="top">
            <input type="text" name="login" id="login" value="<?= COption::GetOptionString( $sModuleId, 'login', '' ) ?>" />
        </td>
    </tr>
    <tr>
        <td width="50%" valign="top"><label for="option">Пароль:</td>
        <td  valign="top">
            <input type="text" name="password" id="password" value="<?= COption::GetOptionString( $sModuleId, 'password', '' ) ?>" />
        </td>
    </tr>
    <tr>
        <td width="50%" valign="top"><label for="option">Ключ:</td>
        <td  valign="top">
            <input type="text" name="key" id="key" value="<?= COption::GetOptionString( $sModuleId, 'key', '' ) ?>" />
        </td>
    </tr>
    <tr>
        <td width="50%" valign="top"><label for="option">Стенд:</td>
        <td  valign="top">
            <select name="stand" id="stand">
                <option value="<?= Endpoints::DEVELOPMENT ?>" <?= COption::GetOptionString( $sModuleId, 'stand', '' ) == Endpoints::DEVELOPMENT ? 'selected' : '' ?>>dev</option>
                <option value="<?= Endpoints::PRODUCTION ?>" <?= COption::GetOptionString( $sModuleId, 'stand', '' ) == Endpoints::PRODUCTION ? 'selected' : '' ?>>prod</option>
            </select>
        </td>
    </tr>
    <?$oTabControl->Buttons();?>
    <input type="submit" name="Update" value="Сохранить" />
    <input type="reset" name="reset" value="Сброс" />
    <input type="hidden" name="Update" value="Y" />
    <?$oTabControl->End();?>
</form>