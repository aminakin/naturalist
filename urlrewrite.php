<?php
$arUrlRewrite=array (
  3 => 
  array (
    'CONDITION' => '#^/bitrix/services/yandexpay.pay/trading/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/bitrix/services/yandexpay.pay/trading/index.php',
    'SORT' => 1,
  ),
  5 => 
  array (
    'CONDITION' => '#^/bitrix/services/yandex.market/trading/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/bitrix/services/yandex.market/trading/index.php',
    'SORT' => 100,
  ),
  4 => 
  array (
    'CONDITION' => '#^/impressions/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/impressions/index.php',
    'SORT' => 100,
  ),
  2 => 
  array (
    'CONDITION' => '#^/api/([a-z0-9]+)/?#',
    'RULE' => 'resource=$1&',
    'ID' => '',
    'PATH' => '/api/index.php',
    'SORT' => 200,
  ),
  0 => 
  array (
    'CONDITION' => '#^/catalog/vpechatleniya/([a-zA-Z0-9\\-_]+)/(\\?(.*))?#',
    'RULE' => 'impressions=$1',
    'ID' => 'naturalist:catalog',
    'PATH' => '/catalog/index.php',
    'SORT' => 250,
  ),
  1 => 
  array (
    'CONDITION' => '#^/catalog/#',
    'RULE' => '',
    'ID' => 'naturalist:catalog',
    'PATH' => '/catalog/index.php',
    'SORT' => 300,
  ),
);
