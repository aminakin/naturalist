<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Подтверждение заказа");?>
<main class="main">
    <section class="section section_crumbs">
        <div class="container">
            <div class="crumbs">
                <ul class="list crumbs__list">
                    <?
                    $APPLICATION->IncludeComponent(
                        "bitrix:breadcrumb",
                        "main",
                        array(
                            "PATH" => "",
                            "SITE_ID" => "s1",
                            "START_FROM" => "0",
                            "COMPONENT_TEMPLATE" => "main"
                        ),
                        false
                    );
                    ?>
                </ul>
            </div>
            <div class="cert__status-wrap">
                <div class="cert__back">
                    <picture>
                        <source media="(min-width: 1440px)" srcset="/local/components/addamant/certificates.buy/templates/.default/img/back.svg" />
                        <source media="(min-width: 769px)" srcset="/local/components/addamant/certificates.buy/templates/.default/img/back-laptop.svg" />
                        <source media="(min-width: 501px)" srcset="/local/components/addamant/certificates.buy/templates/.default/img/back-tablet.svg" />
                        <img src="/local/components/addamant/certificates.buy/templates/.default/img/back-mob.svg"/>
                    </picture>
                    <div class="cert__message">
                        <p class="cert__message-title">Ваш заказ успешно <br>оформлен!</p>
                        <p class="cert__message-text">Если у вас возникли вопросы - обратитесь в <a href="/contacts/">службу заботы</a></p>
                    </div>
                </div>
                <picture class="cert__geo">                    
                    <source media="(min-width: 1440px)" srcset="/local/components/addamant/certificates.buy/templates/.default/img/geo.svg" />
                    <source media="(min-width: 769px)" srcset="/local/components/addamant/certificates.buy/templates/.default/img/geo-laptop.svg" />
                    <source media="(min-width: 501px)" srcset="/local/components/addamant/certificates.buy/templates/.default/img/geo-tablet.svg" />
                    <img src="/local/components/addamant/certificates.buy/templates/.default/img/geo-mob.svg"/>
                </picture>
            </div>
        </div>        
    </section>    
</main>

<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");?>