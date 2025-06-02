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

<div class="order-pdf__wrap">
    <!-- Фоновое изображение сертификата -->
    <div class="cert__image">
        <img width="587" height="830" src="<?= $certImage ?>" alt="Certificate Background">
    </div>

    <!-- Основной контент -->
    <table class="pdf-container">

        <!-- 1. Верхняя область: Логотип и подзаголовок -->
        <tr>
            <td class="pdf-top-height">
                <div style="height: 186px; width: 100%; position: relative;">
                    <div style="position: absolute; bottom: 20px; width: 100%; text-align: center;">

                        <!-- Логотип -->
                        <div class="order-pdf__logo" style="margin-bottom: 15px;">
                            <img width="350" src="<?= HTTP_HOST ?>/ajax/pdf/inc/img/logo.png" alt="Company Logo">
                        </div>

                        <!-- Подзаголовок -->
                        <div class="pdf-font-14 pdf-text-center">
                            <?= Loc::getMessage('PDF_SUB_TITLE'); ?>
                        </div>

                    </div>
                </div>
            </td>
        </tr>

        <!-- 2. Центральная область: Основной текст -->
        <tr>
            <td class="pdf-middle-height" style="height: 415px;position: relative;">

                <div style="position: absolute; top: 40%; left: 50%; transform: translate(-50%, -50%); width: 80%; text-align: center;">
                    <div class="pdf-font-24" style="line-height: 1.2;">
                        <?php if ($hasCustomCongrats): ?>
                            <?= $arResult['PROPS']['PROP_CONGRATS'] ?>
                        <?php else: ?>
                            <?= Loc::getMessage('PDF_ROOT_TITLE'); ?>
                        <?php endif; ?>
                    </div>
                </div>

            </td>
        </tr>

        <!-- 3. Нижняя область: QR-код и контакты -->
        <tr>
            <td class="pdf-bottom-height">
                <div style="height: 229px; width: 100%; position: relative;">
                    <div style="position: absolute; bottom: 20px; width: 100%; text-align: center;">

                        <!-- QR код и информация о сертификате -->
                        <table class="pdf-qr-section" style="margin-bottom: 15px;">
                            <tr>
                                <td style="padding-right: 25px; ">
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
                        <hr style="border: 0; border-top: 1px solid black; width: 80%; margin: 10px auto;">

                        <!-- Контактная информация -->
                        <table class="pdf-contact-table" style="margin-bottom: 5px;">
                            <tr>
                                <td class="pdf-font-14" style="text-align: left; width: 50%; vertical-align: middle;">
                                    <img width="14" height="14" src="<?= HTTP_HOST ?>/ajax/pdf/inc/img/phone.png"
                                         style="margin-top: 8px; margin-right: 5px; vertical-align: middle;" alt="">
                                    <?= Loc::getMessage('PDF_PHONE'); ?>
                                </td>
                                <td class="pdf-font-14" style="text-align: right; width: 50%; vertical-align: middle;">
                                    <img width="14" height="14" src="<?= HTTP_HOST ?>/ajax/pdf/inc/img/mail.png"
                                         style="margin-top: 8px ;margin-right: 5px; vertical-align: middle;" alt="">
                                    <?= Loc::getMessage('PDF_EMAIL'); ?>
                                </td>
                            </tr>
                        </table>

                        <!-- Дополнительная информация для добро-сертификата -->
                        <?php if ($isDobroCert): ?>
                            <div class="pdf-font-14" style="width: 80%; margin: 0 auto; text-align: justify; word-spacing: 0.2em;">
                                <?= Loc::getMessage('PDF_GIFT'); ?>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </td>
        </tr>

    </table>
</div>
