<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
global $USER;

if (CModule::IncludeModule('subscribe') && !empty($_POST['sf_EMAIL'])) {

	$email = $_POST['sf_EMAIL'];

	$subscribeFields = array(
		"USER_ID" => ($USER->IsAuthorized()? $USER->GetID():false),
		"FORMAT" => "html",
		"EMAIL" => $email,
		"ACTIVE" => "Y",
		"CONFIRMED" => "Y",
		"SEND_CONFIRM" => "N",
		"RUB_ID" => array(1)
	);

	$subscr = new CSubscription;
	$ID = $subscr->Add($subscribeFields);

	if($ID > 0) {
		CSubscription::Authorize($ID);
    echo 'success';
	} else {
    echo 'fail';
  }
} else {
  echo 'nothing';
}

?>