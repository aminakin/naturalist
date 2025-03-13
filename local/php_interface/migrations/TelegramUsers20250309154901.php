<?php

namespace Sprint\Migration;


class TelegramUsers20250309154901 extends Version
{
    protected $author = "admin";

    protected $description = "120927 | таблица хранит записи chat_id пользователей telegram бота";

    protected $moduleVersion = "4.18.0";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
    $hlblockId = $helper->Hlblock()->saveHlblock(array (
  'NAME' => 'TelegramUsers',
  'TABLE_NAME' => 'telegram_users',
  'LANG' => 
  array (
    'ru' => 
    array (
      'NAME' => 'пользователи в telegram',
    ),
    'en' => 
    array (
      'NAME' => ' telegram users',
    ),
  ),
));
        $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_CHAT_ID',
  'USER_TYPE_ID' => 'string',
  'XML_ID' => '',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'Y',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'SIZE' => 20,
    'ROWS' => 1,
    'REGEXP' => '',
    'MIN_LENGTH' => 0,
    'MAX_LENGTH' => 0,
    'DEFAULT_VALUE' => '',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => 'user id',
    'ru' => 'идентификатор пользователя',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => 'user id',
    'ru' => 'идентификатор пользователя',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
));
        }
}
