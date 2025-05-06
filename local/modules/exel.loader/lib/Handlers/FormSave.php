<?php

namespace Exel\Loader\Handlers;

use Bitrix\Main\Application;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\SystemException;
use Exel\Loader\DocumentParser\DocumentParser;
use Exel\Loader\HLLoader\Users\UsersLoader;
use Exel\Loader\HLLoader\Yandexreview\YandexReviewLoader;

class FormSave
{

    /**
     * @var \Bitrix\Main\Application The Bitrix application instance.
     */
    public $app;

    /**
     * @var \Bitrix\Main\Context The Bitrix context instance.
     */
    public $context;

    /**
     * @var \Bitrix\Main\HttpRequest The Bitrix request instance.
     */
    public $request;

    /**
     * @var string - ответ для POST запроса
     */
    public $responce = '';

    public function __construct()
    {
        $this->app = Application::getInstance();
        $this->context = $this->app->getContext();
        $this->request = $this->context->getRequest();


        if ($this->request->isPost() && check_bitrix_sessid()) {
            $this->handlePostRequest();
        }
    }

    /**
     * Handles the post request.
     */
    private function handlePostRequest()
    {
        if (!empty($this->request->getPost('save'))) {
            if (!empty($this->request->getFile('yandexreview')) && $this->request->getFile('yandexreview')['tmp_name'] != NULL) {
                $this->handleSaveYandexRewiewLoader($this->request->getFile('yandexreview'));
            }
            if (!empty($this->request->getFile('userloader')) && $this->request->getFile('userloader')['tmp_name'] != NULL) {
                $this->handleSaveUsersLoader($this->request->getFile('userloader'));
            }
        }
    }

    /**
     * Сохранить документ яндекс отзыввов
     * @param $file
     */
    private function handleSaveYandexRewiewLoader($file)
    {
        $parsedFile = DocumentParser::parseDocument($file);

        $this->responce = YandexReviewLoader::loadData($parsedFile);
    }

    /**
     * Сохранить пользователея лидов
     * @param $file
     */
    private function handleSaveUsersLoader($file)
    {
        $parsedFile = DocumentParser::parseDocument($file);

        $this->responce = UsersLoader::loadData($parsedFile);
    }


}
