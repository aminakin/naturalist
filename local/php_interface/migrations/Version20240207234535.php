<?php

namespace Sprint\Migration;


class Version20240207234535 extends Version
{
    protected $description = "105397 | Backend. Функционал создания и хранения сертификатов в базе | HL блок сертификатов";

    protected $moduleVersion = "4.4.1";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
    $hlblockId = $helper->Hlblock()->saveHlblock(array (
  'NAME' => 'Certificates',
  'TABLE_NAME' => 'certificates',
  'LANG' => 
  array (
    'ru' => 
    array (
      'NAME' => 'Сертификаты',
    ),
    'en' => 
    array (
      'NAME' => 'Certificates',
    ),
  ),
));
        $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_CODE',
  'USER_TYPE_ID' => 'string',
  'XML_ID' => '',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
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
    'en' => 'Код сертификата',
    'ru' => 'Код сертификата',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => 'Код сертификата',
    'ru' => 'Код сертификата',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => 'Код сертификата',
    'ru' => 'Код сертификата',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => 'Код сертификата',
    'ru' => 'Код сертификата',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => 'Код сертификата',
    'ru' => 'Код сертификата',
  ),
));
            $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_DATE_CREATE',
  'USER_TYPE_ID' => 'date',
  'XML_ID' => '',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'DEFAULT_VALUE' => 
    array (
      'TYPE' => 'NONE',
      'VALUE' => '',
    ),
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => 'Дата создания',
    'ru' => 'Дата создания',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => 'Дата создания',
    'ru' => 'Дата создания',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => 'Дата создания',
    'ru' => 'Дата создания',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => 'Дата создания',
    'ru' => 'Дата создания',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => 'Дата создания',
    'ru' => 'Дата создания',
  ),
));
            $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_DATE_UNTIL',
  'USER_TYPE_ID' => 'date',
  'XML_ID' => '',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'DEFAULT_VALUE' => 
    array (
      'TYPE' => 'NONE',
      'VALUE' => '',
    ),
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => 'Годен до',
    'ru' => 'Годен до',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => 'Годен до',
    'ru' => 'Годен до',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => 'Годен до',
    'ru' => 'Годен до',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => 'Годен до',
    'ru' => 'Годен до',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => 'Годен до',
    'ru' => 'Годен до',
  ),
));
            $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_COST',
  'USER_TYPE_ID' => 'string',
  'XML_ID' => '',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
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
    'en' => 'Номинал',
    'ru' => 'Номинал',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => 'Номинал',
    'ru' => 'Номинал',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => 'Номинал',
    'ru' => 'Номинал',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => 'Номинал',
    'ru' => 'Номинал',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => 'Номинал',
    'ru' => 'Номинал',
  ),
));
            $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_DATE_ACTIVATE',
  'USER_TYPE_ID' => 'date',
  'XML_ID' => '',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'DEFAULT_VALUE' => 
    array (
      'TYPE' => 'NONE',
      'VALUE' => '',
    ),
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => 'Дата активации',
    'ru' => 'Дата активации',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => 'Дата активации',
    'ru' => 'Дата активации',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => 'Дата активации',
    'ru' => 'Дата активации',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => 'Дата активации',
    'ru' => 'Дата активации',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => 'Дата активации',
    'ru' => 'Дата активации',
  ),
));
            $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_USER_ID',
  'USER_TYPE_ID' => 'integer',
  'XML_ID' => '',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'SIZE' => 20,
    'MIN_VALUE' => 0,
    'MAX_VALUE' => 0,
    'DEFAULT_VALUE' => NULL,
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => 'ID получателя',
    'ru' => 'ID получателя',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => 'ID получателя',
    'ru' => 'ID получателя',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => 'ID получателя',
    'ru' => 'ID получателя',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => 'ID получателя',
    'ru' => 'ID получателя',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => 'ID получателя',
    'ru' => 'ID получателя',
  ),
));
            $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_IS_ACTIVE',
  'USER_TYPE_ID' => 'boolean',
  'XML_ID' => '',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'DEFAULT_VALUE' => 0,
    'DISPLAY' => 'CHECKBOX',
    'LABEL' => 
    array (
      0 => '',
      1 => '',
    ),
    'LABEL_CHECKBOX' => '',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => 'Активирован?',
    'ru' => 'Активирован?',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => 'Активирован?',
    'ru' => 'Активирован?',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => 'Активирован?',
    'ru' => 'Активирован?',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => 'Активирован?',
    'ru' => 'Активирован?',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => 'Активирован?',
    'ru' => 'Активирован?',
  ),
));
        }

    public function down()
    {
        //your code ...
    }
}
