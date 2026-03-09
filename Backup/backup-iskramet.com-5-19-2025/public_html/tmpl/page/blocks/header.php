<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width" />
    <title>Общественное движение "Пора"- manager</title>

    <link href="/css/start.css" rel="stylesheet" />
    <link href="/css/fonts.css" rel="stylesheet" />
    <link href="/css/button.css" rel="stylesheet" />
    <link href="/css/check-radio.css" rel="stylesheet" />
    <link href="/css/style00.css?ver=<?=el_genpass()?>" rel="stylesheet" />
    <link href="/css/style01.css" rel="stylesheet" media="only screen and (min-width:600px) and (max-width:959px)" />
    <link href="/css/style02.css" rel="stylesheet" media="only screen and (min-width:960px)" />
    <link href="/css/el-data.css" rel="stylesheet" />
    <link href="/css/custom.css?ver=<?=el_genpass()?>" rel="stylesheet" />
    <link href="/css/pie-charts.css" rel="stylesheet" />
    <link href="/js/flatpickr.min.css" rel="stylesheet" />

    <!-- <link href="/css/style01.css" rel="stylesheet" type="text/css" media="only screen and (min-width:600px) and (max-width:1024px)" />
    <link href="/css/style02.css" rel="stylesheet" type="text/css" media="only screen and (min-width:1024px)" /> -->

    <script src="/js/jquery-3.4.1.min.js"></script>
    <script src="/js/jquery.el_select1.js"></script>
    <!-- подключаем адаптивное меню -->
    <!-- <script src="js/jquery.slimmenu.min.js"></script> -->
</head>

<body>
<header>
    <div class="wrap">

        <div class="box">
            <div class="logo">
                <a href="/"><img src="/images/logo_blue.svg" /></a>
            </div>
        </div>
        <div class="box">
            <nav id="top_menu">

                <ul>
                    <li><a href="/"<?=($path == '') ? ' class="active"' : ''?>>Главная</a></li>
                    <?/*li><a href="/initsiativy/"<?=($path == '/initsiativy') ? ' class="active"' : ''?>>Инициативы</a></li*/?>
                    <li><a href="/golosovanie/"<?=($path == '/golosovanie') ? ' class="active"' : ''?>>Голосования</a></li>
                    <li><a href="/meropriyatiya/"<?=($path == '/meropriyatiya') ? ' class="active"' : ''?>>Мероприятия</a></li>
                    <li><a href="/lichnyy-kabinet/polzovateli/"<?=($path == '/lichnyy-kabinet/polzovateli') ? ' class="active"' : ''?>>Пользователи</a></li>
                    <li><a href="/deyatelnost/"<?=($path == '/deyatelnost') ? ' class="active"' : ''?>>Деятельность</a></li>
                    <?/*li><a href="/o-nas/"<?=($path == '/o-nas') ? ' class="active"' : ''?>>О нас</a></li*/?>
                    <li><a href="/prisoedinitsya/"<?=($path == '/prisoedinitsya') ? ' class="active"' : ''?>>Присоединиться</a></li>
                </ul>

            </nav>
        </div>

        <div class="box">
            <div class="account">
                <?
                if(intval($_SESSION['user_id']) == 0){
                ?>
                <button class="button icon text"><span class="material-icons">account_box</span>Вход</button>
                <div class="panel" id="pop_up_welcome">
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
                <?
                }else{
                    $fioArr = explode(" ", $_SESSION['user_fio']);
                    $userName = $fioArr[1];
                ?>
                <button class="button icon text" onclick="pop_up_welcome(); return false">
                    <span class="material-icons">account_box</span><?=$userName?> &nbsp;
                    <small>ID <?=$_SESSION['visual_user_id']?></small>
                </button>
                <div class="panel" id="pop_up_welcome">
                    <button class="button text" id="lk">Личный кабинет</button><br>
                    <button class="button text" id="logout">Выход</button>
                </div>
                <?
                }
                ?>
            </div>

        </div>
    </div>
</header>