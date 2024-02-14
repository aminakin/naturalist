<?php

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$arComponentParameters = [
	"PARAMETERS" => [		
		"VARIANT_COST" => [
            "NAME" => GetMessage("VARIANT_COST"),
            "PARENT" => "BASE",
            "TYPE" => "STRING",
            "DEFAULT" => 300
        ],
        "POCKET_COST" => [
            "NAME" => GetMessage("POCKET_COST"),
            "PARENT" => "BASE",
            "TYPE" => "STRING",
            "DEFAULT" => 690
        ],
        "MIN_COST" => [
            "NAME" => GetMessage("MIN_COST"),
            "PARENT" => "BASE",
            "TYPE" => "STRING",
            "DEFAULT" => 5000
        ],
		"CACHE_TIME"  =>  [
            "DEFAULT" => 36000000
        ],
	],
];