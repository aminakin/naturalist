<?php

$aMenuLinks = Array(
	Array(
		"Главная", 
		"/", 
		Array(), 
		Array(
		    "IS_MOBILE" => "Y",
        ),
		"" 
	),
	Array(
		"Карта", 
		"/map/", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Каталог", 
		"/catalog/", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Впечатления", 
		"/impressions/", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"О проекте", 
		"/about/", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Сертификат", 
		"/certificates/", 
		Array(), 
		Array(
			"HIGHLIGHT_BROWN" => "Y",
		), 
		"\$GLOBALS['USER']->IsAuthorized()"
	),
);