<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$metaTags = getMetaTags();
$currentURLDir = $APPLICATION->GetCurDir();

	$APPLICATION->SetTitle("Натуралист");
    // $APPLICATION->AddHeadString('<meta name="description" content="Главная | Naturalist.travel" />');
?>
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js""alert('Похоже, у вас включен блокировщик рекламы. Отключите его, чтобы увидеть лучшие предложения!')"></script>
<div class="wrapper-f">
<?
$APPLICATION->IncludeComponent(
    "naturalist:empty", 
    "flights", 
    array()
);
?>
</div>
<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>