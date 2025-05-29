<?
$aMenuLinks = Array(
	Array(
		"Главная", 
		SITE_DIR."/", 
		Array(), 
		Array("IS_MOBILE"=>"Y"), 
		"" 
	),
	Array(
		"Каталог", 
		SITE_DIR."/catalog/", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Карта", 
		SITE_DIR."/map/", 
		Array(), 
		Array(), 
		"" 
	),
	
	Array(
		"Подборки", 
		SITE_DIR."/impressions/", 
		Array(), 
		Array(),
		"" 
	),
	Array(
		"Локации", 
		SITE_DIR."/regions/", 
		Array(), 
		Array(), 
		"" 
	),
    Array(
        "Авиабилеты",
        SITE_DIR."/flights/",
        Array(),
        Array(),
        ""
    ),
	Array(
		"Сертификат", 
		SITE_DIR."/certificates/", 
		Array(), 
		Array("ALWAYS_ORANGE"=>"Y"), 
		"" 
	),

	Array(
		"О проекте", 
		SITE_DIR."/about/", 
		Array(), 
		Array(), 
		"" 
	),


);

if(CSite::InDir('/catalog') || CSite::InDir('/map'))
{
    $aMenuLinks[] = Array(
        "Контакты",
        SITE_DIR."/contacts/",
        Array(),
        Array(),
        ""
    );
}
?>