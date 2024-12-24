<?php

namespace Sprint\Migration;


class Version20241224140212 extends Version
{
    protected $description = "118334 | Промо-страница-сертификаты | Настройка формы элемента ИБ промо сертификатов";

    protected $moduleVersion = "4.4.1";

    public function up()
    {
        $helper = $this->getHelperManager();
        $helper->UserOptions()->saveUserForm(array (
  'Пользователь|edit1' => 
  array (
    'DATE_REGISTER' => 'Дата регистрации',
    'LAST_UPDATE' => 'Дата обновления',
    'LAST_LOGIN' => 'Последняя авторизация',
    'BLOCKED' => 'Заблокирован',
    'TITLE' => 'Обращение',
    'NAME' => 'Имя',
    'LAST_NAME' => 'Фамилия',
    'SECOND_NAME' => 'Отчество',
    'EMAIL' => 'E-Mail',
    'LOGIN' => 'Логин (мин. 3 символа)',
    'PHONE_NUMBER' => 'Номер телефона для регистрации',
    'PASSWORD' => 'Новый пароль',
    'PASSWORD_EXPIRED' => 'Требуется сменить пароль при следующем входе',
    'XML_ID' => 'Внешний код',
    'LID' => 'Сайт по умолчанию для уведомлений',
    'LANGUAGE_ID' => 'Язык для уведомлений',
    'user_info_event' => 'Оповестить пользователя',
  ),
  'Группы|edit2' => 
  array (
    'GROUP_ID' => 'Группы пользователя',
  ),
  'Безопасность|edit_policy' => 
  array (
    'GROUP_POLICY' => 'Политики безопасности',
  ),
  'Личные данные|edit3' => 
  array (
    'PERSONAL_PROFESSION' => 'Профессия',
    'PERSONAL_WWW' => 'WWW-страница',
    'PERSONAL_ICQ' => 'ICQ',
    'PERSONAL_GENDER' => 'Пол',
    'PERSONAL_BIRTHDAY' => 'Дата рождения',
    'PERSONAL_PHOTO' => 'Фотография',
    'USER_PHONES' => 'Телефоны',
    'PERSONAL_PHONE' => 'Телефон',
    'PERSONAL_FAX' => 'Факс',
    'PERSONAL_MOBILE' => 'Мобильный',
    'PERSONAL_PAGER' => 'Пейджер',
    'USER_POST_ADDRESS' => 'Почтовый адрес',
    'PERSONAL_COUNTRY' => 'Страна',
    'PERSONAL_STATE' => 'Область / край',
    'PERSONAL_CITY' => 'Город',
    'PERSONAL_ZIP' => 'Почтовый индекс',
    'PERSONAL_STREET' => 'Улица',
    'PERSONAL_MAILBOX' => 'Почтовый ящик',
    'PERSONAL_NOTES' => 'Дополнительные заметки',
  ),
  'Работа|edit4' => 
  array (
    'WORK_COMPANY' => 'Наименование компании',
    'WORK_WWW' => 'WWW-страница',
    'WORK_DEPARTMENT' => 'Департамент / Отдел',
    'WORK_POSITION' => 'Должность',
    'WORK_PROFILE' => 'Направления деятельности',
    'WORK_LOGO' => 'Логотип компании',
    'USER_WORK_PHONES' => 'Телефоны',
    'WORK_PHONE' => 'Телефон',
    'WORK_FAX' => 'Факс',
    'WORK_PAGER' => 'Пейджер',
    'USER_WORK_POST_ADDRESS' => 'Почтовый адрес',
    'WORK_COUNTRY' => 'Страна',
    'WORK_STATE' => 'Область / край',
    'WORK_CITY' => 'Город',
    'WORK_ZIP' => 'Почтовый индекс',
    'WORK_STREET' => 'Улица',
    'WORK_MAILBOX' => 'Почтовый ящик',
    'WORK_NOTES' => 'Дополнительные заметки',
  ),
  'Рейтинг|edit_rating' => 
  array (
    'RATING_BOX' => 'Рейтинг',
  ),
  'Блог|edit_blog' => 
  array (
    'MODULE_TAB_blog' => 'Блог',
  ),
  'Форум|edit_forum' => 
  array (
    'MODULE_TAB_forum' => 'Форум',
  ),
  'Двухэтапная авторизация|edit_security' => 
  array (
    'MODULE_TAB_security' => 'Двухэтапная авторизация',
  ),
  'Битрикс24|edit_socialservices' => 
  array (
    'MODULE_TAB_socialservices' => 'Битрикс24',
  ),
  'Заметки|edit10' => 
  array (
    'ADMIN_NOTES' => 'Заметки администратора',
  ),
  'Доп. поля|user_fields_tab' => 
  array (
    'USER_FIELDS_ADD' => 'Добавить пользовательское поле',
    'UF_GUESTS_DATA' => 'UF_GUESTS_DATA',
    'UF_FAVOURITES' => 'UF_FAVOURITES',
    'UF_EMAIL_CHANGE' => 'UF_EMAIL_CHANGE',
    'UF_EMAIL_CODE' => 'UF_EMAIL_CODE',
    'UF_PHONE_CHANGE' => 'UF_PHONE_CHANGE',
    'UF_PHONE_CODE' => 'UF_PHONE_CODE',
    'UF_SUBSCRIBE_EMAIL_1' => 'UF_SUBSCRIBE_EMAIL_1',
    'UF_AUTH_CODE' => 'UF_AUTH_CODE',
    'UF_AUTH_TYPE' => 'UF_AUTH_TYPE',
  ),
));

    }

    public function down()
    {
        //your code ...
    }
}
