<?php
$arUrlRewrite=array (
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
