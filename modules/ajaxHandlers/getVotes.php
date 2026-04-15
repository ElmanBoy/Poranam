<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';
if (el_checkAjax()) {
    $_GET = $_POST;
    $row_dbcontent['cat'] = 398;
    $row_dbcontent['kod'] = 'cataloginit';
    //Черновики не показывать незарегистрированным
    if (intval($_SESSION['user_level']) == 0) {
        if(intval($_GET['sf14']) == 0){
            $_GET['sf14_from'] = 6; //Голосование запущено
        }
    }
    //Показываем голосования Куратору центра
    if (intval($_SESSION['user_level']) == 4) {
        if(!isset($_GET['filter'])) {
            $_GET['sf5'] = [0, '', $_SESSION['user_subject']];
            $_GET['sf6'] = [0, '', $_SESSION['user_region']];
        }
        // КЦ видит голосования на утверждении (5) и запущенные (6) по своей территории
        // Свои черновики (1,4) добавляются через own_draft_uid
        if(strlen($_GET['sf14']) == 0){
            $_GET['sf14'] = [5, 6, 7];
        }

        //Показываем Администратору утвержденные голосования
    } elseif (intval($_SESSION['user_level']) == 11) {
        if(!isset($_GET['filter'])) {
            $_GET['sf14'] = [5, 6, 7]; //Голосование утверждено
        }

    } elseif (intval($_SESSION['user_level']) > 0 && intval($_SESSION['user_level']) < 11) {
        //Показываем голосования всем остальным зарегистрированным пользователям
        if(!isset($_GET['filter'])) {
            if (strlen($_GET['sf5']) == 0)
                $_GET['sf5'] = ['0', '', 'null', $_SESSION['user_subject']];
            if (strlen($_GET['sf6']) == 0)
                $_GET['sf6'] = ['0', '', 'null', $_SESSION['user_region']];
            if (strlen($_GET['sf7']) == 0)
                $_GET['sf7'] = ['0', '', 'null', $_SESSION['user_prof']];
            if (strlen($_GET['sf8']) == 0)
                $_GET['sf8'] = ['0', '', 'null', $_SESSION['user_city']];
            if (strlen($_GET['sf9']) == 0)
                $_GET['sf9'] = ['0', '', 'null', $_SESSION['user_index']];
            if (strlen($_GET['sf17']) == 0)
                $_GET['sf17'] = ['0', '', 'null', $_SESSION['user_group']];
            if (strlen($_GET['sf12']) == 0)
                $_GET['sf12'] = array_merge(['0', '', 'null'], explode(',', $_SESSION['user_themes']));
            // Добавляем фильтр по рангу пользователя
            if (strlen($_GET['sf13']) == 0)
                $_GET['sf13'] = ['0', '', 'null', $_SESSION['user_level']];
        }
        if(strlen($_GET['sf14']) == 0){
            $_GET['sf14'] = [6, 7]; //Голосование запущено
        }
    }

    // Все авторизованные пользователи (кроме Админа) видят свои черновики (статусы 1 и 4)
    if (intval($_SESSION['user_level']) > 0 && intval($_SESSION['user_level']) < 11 && intval($_SESSION['user_id']) > 0) {
        $_GET['own_draft_uid'] = intval($_SESSION['user_id']);
    }
    include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/catalog.php";
}
?>