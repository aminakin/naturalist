<?php

namespace Sprint\Migration;


class order_bronevik_notification20250306143806 extends Version
{
    protected $author = "admin";

    protected $description = "";

    protected $moduleVersion = "4.12.6";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $helper->Event()->saveEventType('BRONEVIK_ORDER_CHANGE', array (
  'LID' => 'ru',
  'EVENT_TYPE' => 'email',
  'NAME' => 'Изменился заказ по API',
  'DESCRIPTION' => '',
  'SORT' => '150',
));
            $helper->Event()->saveEventType('BRONEVIK_ORDER_CHANGE', array (
  'LID' => 'en',
  'EVENT_TYPE' => 'email',
  'NAME' => '',
  'DESCRIPTION' => '',
  'SORT' => '150',
));
            $helper->Event()->saveEventMessage('BRONEVIK_ORDER_CHANGE', array (
  'LID' => 
  array (
    0 => 's1',
  ),
  'ACTIVE' => 'Y',
  'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
  'EMAIL_TO' => '#DEFAULT_EMAIL_FROM#',
  'SUBJECT' => 'Изменение заказа',
  'MESSAGE' => 'Заказ #ORDER_ID#
Изменения
#CONTENT#',
  'BODY_TYPE' => 'text',
  'BCC' => '',
  'REPLY_TO' => '',
  'CC' => '',
  'IN_REPLY_TO' => '',
  'PRIORITY' => '',
  'FIELD1_NAME' => '',
  'FIELD1_VALUE' => '',
  'FIELD2_NAME' => '',
  'FIELD2_VALUE' => '',
  'SITE_TEMPLATE_ID' => '',
  'ADDITIONAL_FIELD' => 
  array (
  ),
  'LANGUAGE_ID' => '',
  'EVENT_TYPE' => '[ BRONEVIK_ORDER_CHANGE ] Изменился заказ по API',
));
        }
}
