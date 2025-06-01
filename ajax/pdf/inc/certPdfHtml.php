<?php
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

// Получение данных
$certImage = $arResult['PROPS']['CERT_FORMAT'] == 'electro'
    ? $arResult['PROPS']['ELECTRO_VARIANT']
    : $arResult['PROPS']['FIZ_VARIANT'];
$certCode = strtoupper(str_replace('-', '', $arResult['CERT']['UF_CODE']));
$certDate = $arResult['CERT']['UF_DATE_UNTIL']->format("d.m.Y");
$isDobroCert = $arResult['PROPS']['FIZ_VARIANT_DOBRO_CERT'] == 'Y';
$hasCustomCongrats = strlen($arResult['PROPS']['PROP_CONGRATS']) > 0;
?>

<style>

    .pdf-container {
        width: 100%;
        color: black;
        border-collapse: collapse;
        page-break-inside: avoid;
    }
    .pdf-text-center { text-align: center; }
    .pdf-font-14 { font-size: 14px; }
    .pdf-font-18 { font-size: 18px; }
    .pdf-font-22 { font-size: 22px; }
    .pdf-font-24 { font-size: 24px; }
    .pdf-font-26 { font-size: 26px; }
    .pdf-line-height-09 { line-height: 0.9; }
    .pdf-line-height-1 { line-height: 1; }
    .pdf-line-height-13 { line-height: 1.3; }
    .pdf-spacer-50 { padding-top: 50px; }
    .pdf-spacer-110 { padding-top: 110px; }
    .pdf-spacer-20 { margin: 20px 10%; }
    .pdf-qr-section { width: auto; margin: 0 auto; }
    .pdf-qr-text { padding-left: 5px; vertical-align: top; }
    .pdf-contact-table {
        width: 80%;
        margin: 0 auto;
        border-collapse: collapse;
    }
</style>

<div class="order-pdf__wrap">
    <!-- Фоновое изображение сертификата -->
    <div class="cert__image">
        <img width="587" height="830" src="<?= $certImage ?>" alt="Certificate Background">
    </div>

    <!-- Основной контент -->
    <table class="pdf-container">
        <tr>
            <td class="pdf-text-center" style="vertical-align: top;">

                <!-- Логотип -->
                <div class="order-pdf__logo" style="margin: 50px 0 10px;">
                    <img width="350" src="<?= HTTP_HOST ?>/ajax/pdf/inc/img/logo.png" alt="Company Logo">
                </div>

                <!-- Заголовки -->
                <div class="pdf-font-14 pdf-text-center">
                    <?= Loc::getMessage('PDF_SUB_TITLE'); ?>
                </div>

                <div class="pdf-font-24 pdf-text-center pdf-spacer-110" style="width: 80%; margin: 0 auto;">
                    <?php if ($hasCustomCongrats): ?>
                        <?= $arResult['PROPS']['PROP_CONGRATS'] ?>
                    <?php else: ?>
                        <?= Loc::getMessage('PDF_ROOT_TITLE'); ?>
                    <?php endif; ?>
                </div>

                <!-- Поздравления -->
                <?/*<div class="pdf-spacer-50">
                    <?php if (!$isDobroCert): ?>
                        <div class="pdf-font-18">
                            <?= Loc::getMessage('PDF_CONGRATS_TITLE'); ?>
                        </div>

                        <div class="pdf-font-14 pdf-line-height-13">
                            <?= Loc::getMessage('PDF_CONGRATS'); ?>
                        </div>
                    <?php endif; ?>
                </div> */?>



                <!-- Спейсер -->
                <div style="height: 120px;"></div>

                <!-- QR код и информация о сертификате -->
                <table class="pdf-qr-section">
                    <tr>
                        <td>
                            <img width="180" src="<?= HTTP_HOST ?>/ajax/pdf/inc/img/qr.png" alt="QR Code">
                        </td>
                        <td class="pdf-qr-text">
                            <div class="pdf-font-26 pdf-line-height-1">
                                <?= Loc::getMessage('PDF_INSTRUCTION'); ?>
                            </div>
                            <div class="pdf-font-26 pdf-line-height-1">
                                <?= Loc::getMessage('PDF_ACTIVATE'); ?>
                            </div>
                            <div class="pdf-font-26 pdf-line-height-1">
                                <?= Loc::getMessage('PDF_CERT'); ?>
                            </div>
                            <br>
                            <div class="pdf-font-14 pdf-line-height-09">
                                <?= Loc::getMessage('PDF_NOM_CERT'); ?><?= $arResult['PROPS']['PROP_CERT_PRICE'] ?><?= Loc::getMessage('PDF_NOM_R'); ?>
                            </div>
                            <div class="pdf-font-14 pdf-line-height-09">
                                <?= Loc::getMessage('PDF_NUMBER'); ?><?= $certCode ?>
                            </div>
                            <div class="pdf-font-14 pdf-line-height-09">
                                <?= Loc::getMessage('PDF_TO'); ?><?= $certDate ?>
                            </div>
                        </td>
                    </tr>
                </table>

                <!-- Разделитель -->
                <hr style="border-top: 1px solid black; width: 80%;" class="pdf-spacer-20">

                <!-- Контактная информация -->
                <table class="pdf-contact-table">
                    <tr>
                        <td class="pdf-font-14" style="text-align: left; width: 50%; vertical-align: middle;">
                            <img width="14" height="14" src="<?= HTTP_HOST ?>/ajax/pdf/inc/img/mail.png"
                                 style="margin-right: 5px; margin-top: 8px; vertical-align: middle;" alt="">
                                <?= Loc::getMessage('PDF_PHONE'); ?>
                        </td>
                        <td class="pdf-font-14" style="text-align: right; width: 50%; vertical-align: middle;">
                            <img width="14" height="14" src="<?= HTTP_HOST ?>/ajax/pdf/inc/img/phone.png"
                                 style="margin-right: 5px; margin-top: 12px; vertical-align: middle;" alt="">
                            <?= Loc::getMessage('PDF_EMAIL'); ?>
                        </td>
                    </tr>
                </table>



                <!-- Дополнительная информация для добро-сертификата -->
                <?php if ($isDobroCert): ?>
                    <div class="pdf-font-14" style="width: 80%; margin: 20px auto 0; text-align: justify; word-spacing: 0.2em;">
                        <?= Loc::getMessage('PDF_GIFT'); ?>
                    </div>
                <?php endif; ?>

            </td>
        </tr>
    </table>
</div>
