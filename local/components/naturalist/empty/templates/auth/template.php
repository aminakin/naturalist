<main class="main">
    <section class="section section_auth">
        <div class="container">
            <p>Пожалуйста, авторизуйтесь!</p>
            <a class="auth__link button" href="#login-phone" data-modal="">Войти</a>
        </div>
    </section>
</main>

<script>
    document.addEventListener("DOMContentLoaded", function(){
        let button = document.querySelector('.auth__link.button');
        if (button) {
            button.click();
        }
    });
</script>