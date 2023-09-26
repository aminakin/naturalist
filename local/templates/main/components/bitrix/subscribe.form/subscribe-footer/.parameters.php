<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
	die();
}

$arTemplateParameters = array(
	'FORM_TITLE' => array(
		'PARENT' => 'ADDITIONAL_SETTINGS',
		'NAME' => GetMessage('FORM_TITLE'),
		'TYPE' => 'STRING',		
		'DEFAULT' => '',		
	),	
    'FORM_SUBTITLE' => array(
		'PARENT' => 'ADDITIONAL_SETTINGS',
		'NAME' => GetMessage('FORM_SUBTITLE'),
		'TYPE' => 'STRING',		
		'DEFAULT' => '',		
	),   
    'FORM_POLITICS_LINK' => array(
		'PARENT' => 'ADDITIONAL_SETTINGS',
		'NAME' => GetMessage('FORM_POLITICS_LINK'),
		'TYPE' => 'STRING',		
		'DEFAULT' => '',		
	),
);
?>