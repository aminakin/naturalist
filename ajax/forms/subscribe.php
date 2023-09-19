<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
global $USER;

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

if (\Bitrix\Main\Loader::includeModule('subscribe') && !empty($request->getPost('sf_EMAIL'))) {

	$email = $request->getPost('sf_EMAIL');

	$subscribeFields = [
		"USER_ID" => ($USER->IsAuthorized()? $USER->GetID():false),
		"FORMAT" => "html",
		"EMAIL" => $email,
		"ACTIVE" => "Y",
		"CONFIRMED" => "Y",
		"SEND_CONFIRM" => "N",
		"RUB_ID" => [1]
	];

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