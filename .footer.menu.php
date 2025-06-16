<?
$aMenuLinks = array(
	array(
		"Каталог",
		SITE_DIR . "/catalog/",
		array(),
		array(),
		""
	),
	array(
		"Карта",
		SITE_DIR . "/map/",
		array(),
		array(),
		""
	),

	array(
		"Подборки",
		SITE_DIR . "/impressions/",
		array(),
		array(),
		""
	),
	array(
		"О проекте",
		SITE_DIR . "/about/",
		array(),
		array(),
		""
	),
//	array(
//		"Контакты",
//		SITE_DIR . "/contacts/",
//		array(),
//		array(),
//		""
//	),
//    array(
//        "FAQ",
//        SITE_DIR . "/certificates/",
//        array(),
//        array("ALWAYS_ORANGE" => "Y"),
//        ""
//    ),
	array(
		"Сертификат",
		SITE_DIR . "/certificates/",
		array(),
		array("ALWAYS_ORANGE" => "Y"),
		""
	),
	array(
		"Карта сайта",
		SITE_DIR . "/sitemap/",
		array(),
		array(),
		""
	),
	array(
		"Объектам размещения",
		SITE_DIR . "/objects/",
		array(),
		array(),
		""
	),

);
if(CSite::InDir('/catalog') || CSite::InDir('/map'))
{
    $oldElements = [array(
        "Оплата",
        SITE_DIR . "/payment/",
        array(),
        array(),
        ""
    ),
        array(
            "Реквизиты",
            SITE_DIR . "/details/",
            array(),
            array(),
            ""
        ),
//        array(
//            "Локации",
//            SITE_DIR . "/regions/",
//            array(),
//            array(),
//            ""
//        ),
    ];

    foreach ($oldElements as $oldElement){
        $aMenuLinks[] = $oldElement;
    }
}