<?php 

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @global CUser $USER
 * @global CMain $APPLICATION
 */
    
use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Bitrix\Iblock\Elements\ElementCertificatesStepsTable;
use Bitrix\Iblock\Elements\ElementCertificatesQuestionsTable;

Loc::loadMessages(__FILE__);

if (!Loader::includeModule('iblock')) {
    throw new SystemException(
        Loc::getMessage('IBLOCK_MODULE_NOT_INSTALLED')
    );
}

/**
 * Class CertificatesIndex
 */
class CertificatesIndex extends CBitrixComponent
{
    
    /**
     * CertificatesIndex constructor.
     *
     * @param mixed|null $component
     */
    public function __construct($component = null)
    {
        parent::__construct($component);
    }
    
    /**
     * Prepare component parameters.
     *
     * @param array $params The component parameters.
     *
     * @return array The modified component parameters.
     */
    public function onPrepareComponentParams($params): array
    {
        $params = parent::onPrepareComponentParams($params);
        
        return $params;
    }
    
    /**
     * Make output result.
     */
    public function makeOutputResult(): void
    {
        $this->arResult['STEPS'] = $this->getCertificatesSteps();
        $this->arResult['QUESTIONS'] = $this->getCertificatesQuestions();
    }

    public function getCertificatesQuestions()
    {
        return ElementCertificatesQuestionsTable::getList([
            'select' => [
                'ID',
                'NAME',
                'SORT',
                'PREVIEW_TEXT'
            ],
            'order' => [
                'SORT' => 'ASC'
            ],
            'filter' => [
                '=ACTIVE' => 'Y'
            ]
        ])->fetchAll();
    }

    public function getCertificatesSteps()
    {
        return ElementCertificatesStepsTable::getList([
            'select' => [
                'ID',
                'NAME',
                'SORT',
                'PREVIEW_TEXT'
            ],
            'order' => [
                'SORT' => 'ASC'
            ],
            'filter' => [
                '=ACTIVE' => 'Y'
            ]
        ])->fetchAll();
    }

    public function executeComponent()
    {

        $this->makeOutputResult();
        
        $this->includeComponentTemplate();
    }

}
