<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/tmpl/page/blocks/header.php';
?>

<!-- оснвоное поле -->
<div class="content">
    <div class="wrap">
        <main>
            <!-- screen_01 -->
            <div class="loginFrm">
                <form class="ajaxFrm" id="login" onsubmit="return false">
                    <div class="el_data w_100">
                        <label>E-mail</label>
                        <input class="el_input" type="email" name="user" required>
                    </div>
                    <div class="el_data w_100">
                        <label>Пароль</label>
                        <input class="el_input" type="password" name="password" required>
                    </div>
                    <a href="/remember/">Напомнить пароль?</a>
                    <a href="/prisoedinitsya/">Регистрация</a>
                    <button class="button text">Войти</button>
                </form>
            </div>
            <!-- конец screen_01  -->
        </main>
    </div>
</div>
<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/tmpl/page/blocks/footer.php';
?>