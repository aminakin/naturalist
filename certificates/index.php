<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Сертификат");?>

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
    </div>
</section>



<section class="certificates__section">
    <?php
        $APPLICATION->includeComponent(
            'naturalist:certificates.index',
            '',
            []
        );
    ?>
</section>


<?php 

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
