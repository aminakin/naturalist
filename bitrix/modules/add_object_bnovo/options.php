<?

$sModuleId  = 'dd_blank_module';

CModule::IncludeModule( $sModuleId );

global $MESS;
IncludeModuleLangFile( __FILE__ );
 
if( $REQUEST_METHOD == 'POST' && $_POST['Update'] == 'Y' ) {
    COption::SetOptionString( $sModuleId, 'option', $_POST['option'] == 'Y' ? 'Y' : 'N' );
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
        <td colspan="2"><?=GetMessage( 'DD_BM_GROUP_TITLE' )?></td>
    </tr>
    <tr>
        <td width="50%" valign="top"><label for="option"><?echo GetMessage( 'DD_BM_LABEL' );?>:</td>
        <td  valign="top">
            <input type="checkbox" name="option" id="option"<? if ( COption::GetOptionString( $sModuleId, 'option', 'Y' ) == 'Y'):?> checked="checked"<? endif; ?> value="Y" />
        </td>
    </tr>
    <?$oTabControl->Buttons();?>
    <input type="submit" name="Update" value="<?=GetMessage( 'DD_BM_BUTTON_SAVE' )?>" />
    <input type="reset" name="reset" value="<?= GetMessage( 'DD_BM_BUTTON_RESET' )?>" />
    <input type="hidden" name="Update" value="Y" />
    <?$oTabControl->End();?>
</form>