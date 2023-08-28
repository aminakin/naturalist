<?
@define("ERROR_404", "Y");
include_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/urlrewrite.php');
CHTTP::SetStatus("404 Not Found");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
?>

<link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/error-page.min.css?v=1664554795104">
<main class="main">
    <section class="section section_crumbs">
        <div class="container">
            <div class="crumbs">
                <ul class="list crumbs__list">
                    <li class="list__item"><a class="list__link" href="/">Главная</a></li>
                    <li class="list__item"><a class="list__link">404</a></li>
                </ul>
            </div>
        </div>
    </section>
    <!-- section-->

    <section class="section section_error">
        <div class="container">
            <div class="error">
                <div class="error__title">404</div>
                <div class="error__footnote">Страница не найдена, либо ещё не создана</div>
                <div class="error__text">Вы можете <a href="/catalog/">перейти в каталог</a> и выбрать кемпинг</div>
            </div>
        </div>
    </section>
    <!-- section-->
</main>
<!-- main-->

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>