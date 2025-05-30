<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
?>
<div class="order-pdf__wrap">
    <div class="cert__image">
        <img width="587" height="830" src="<?= $arResult['PROPS']['CERT_FORMAT'] == 'electro' ? $arResult['PROPS']['ELECTRO_VARIANT'] : $arResult['PROPS']['FIZ_VARIANT'] ?>" alt="">
    </div>
    <table style="width: 100%; border-collapse: collapse; page-break-inside: avoid;">
        <tr>
            <td style="color:black;text-align: center; vertical-align: top; ">
                <div class="order-pdf__logo" style="margin-bottom: 10px; margin-top: 50px;">
                    <img width="350" src="<?= HTTP_HOST ?>/ajax/pdf/inc/img/logo.png" alt="">
                </div>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="color:black;text-align: center; font-size: 14px;"><?= Loc::getMessage('PDF_SUB_TITLE'); ?></td>
                    </tr>
                    <tr>
                        <td style="color:black; text-align: center; padding-top: 30px; font-size: 24px;"><?= Loc::getMessage('PDF_ROOT_TITLE'); ?></td>
                    </tr>
                    <tr>
                        <td style="padding-top: 70px;">
                            <?php if (!strlen($arResult['PROPS']['PROP_CONGRATS'])) { ?>

                                <div class="order-pdf__congrats-title" style="font-size: 18px"><?= Loc::getMessage('PDF_CONGRATS_TITLE'); ?></div>
                                <div class="order-pdf__congrats" style="font-size: 14px; line-height: 1.3;">
                                    <?= Loc::getMessage('PDF_CONGRATS'); ?>
                                </div>
                            <?php } else { ?>
                                <div class="" style=" color:black;font-size: 14px;">
                                    <div style="text-align: center; font-size: 22px; color:black;">
                                        <?= $arResult['PROPS']['PROP_CONGRATS'] ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr><td style="height: 140px;"></td></tr>
                    <tr>
                        <td style="text-align: center; vertical-align: middle;">
                            <table style="width: auto; margin: 0 auto;">
                                <tr>

                                        <img width="180" src="<?= HTTP_HOST ?>/ajax/pdf/inc/img/qr.png" alt="">

                                    <td style="padding-left: 5px; vertical-align: top;">
                                        <div style="font-size: 26px; line-height: 1;"><?= Loc::getMessage('PDF_INSTRUCTION'); ?></div>
                                        <div style="font-size: 26px; line-height: 1;"><?= Loc::getMessage('PDF_ACTIVATE'); ?></div>
                                        <div style="font-size: 26px; line-height: 1;"><?= Loc::getMessage('PDF_CERT'); ?></div>
                                    </br>
                                        <div style="font-size: 14px; line-height: 0.9;"><?= Loc::getMessage('PDF_NOM_CERT'); ?><?=$arResult['PROPS']['PROP_CERT_PRICE']?><?= Loc::getMessage('PDF_NOM_R'); ?></div>
                                        <div style="font-size: 14px; line-height:  0.9;"><?= Loc::getMessage('PDF_NUMBER'); ?><?=strtoupper(str_replace('-', '', $arResult['CERT']['UF_CODE']))?></div>
                                        <div style="font-size: 14px; line-height:  0.9;"><?= Loc::getMessage('PDF_TO'); ?><?=$arResult['CERT']['UF_DATE_UNTIL']->format("d.m.Y")?></div>
                                    </td>

                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
                <hr style="border-top: 1px solid black; margin: 20px 10%; width: 80%;" />
                <table style="width: 100%; page-break-inside: avoid;">
                    <tr>
                        <td style="text-align: center; font-size: 14px;">
                            <div style="display: inline-block; margin-right: 50px;"><?= Loc::getMessage('PDF_PHONE'); ?></div>
                            <div style="display: inline-block;margin-left: 50px;"><?= Loc::getMessage('PDF_EMAIL'); ?></div>
                        </td>
                    </tr>
                    <?php if ($arResult['PROPS']['FIZ_VARIANT_DOBRO_CERT'] == 'Y') { ?>
                        <div style="text-align: center; margin-left: 13px; margin-right: 13px; font-size: 14px; color:black; page-break-inside: avoid;">
                            <?= Loc::getMessage('PDF_GIFT'); ?>
                        </div>
                    <?php } ?>
                </table>




            </td>
        </tr>
    </table>

</div>