<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); ?>
<? $APPLICATION->AddHeadString('<meta http-equiv="refresh" content="0;url=https://b24-s0b4fy.bitrix24site.ru/crm_form_qj0m8/">', true) ?>
<main class="main">
    <div class="container">
        <p>Вы будете перенаправлены через <span id="countdown">5</span> секунд. Если перенаправление не произошло, <a href="https://b24-s0b4fy.bitrix24site.ru/crm_form_qj0m8/">нажмите здесь</a>.</p>
    </div>
</main>
<script>
    let time = 5;
    const timer = setInterval(() => {
        document.getElementById('countdown').textContent = time <= 0 ?
            clearInterval(timer) :
            time--;
    }, 1000);
</script>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>