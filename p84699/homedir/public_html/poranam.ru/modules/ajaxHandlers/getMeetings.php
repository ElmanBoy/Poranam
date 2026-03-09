<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';
if (el_checkAjax()) {
    $_GET = $_POST;
    $row_dbcontent['cat'] = $_REQUEST['cat'] = 405;
    $row_dbcontent['kod'] = 'cataloginit';
    //Черновики не показывать незарегистрированным
    if (intval($_SESSION['user_level']) == 0) {
        $_GET['sf14'] = 14; //мероприятие запущено
    }
//Показываем мероприятия Куратору центра
    if (intval($_SESSION['user_level']) == 4) {
        $_GET['sf5'] = [0, '', $_SESSION['user_subject']];
        $_GET['sf6'] = [0, '', $_SESSION['user_region']];
        $_GET['sf14_from'] = 10; //Мероприятие создано
        //Показываем Администратору утвержденные мероприятия
    } elseif (intval($_SESSION['user_level']) == 11) {
        $_GET['sf14_from'] = 10; //Мероприятие на утверждении и выше
    } elseif (intval($_SESSION['user_level']) > 0 && intval($_SESSION['user_level']) < 11) {
        //Показываем мероприятия всем остальным зарегистрированным пользователям
        $_GET['sf5'] = ['0', '', 'null', $_SESSION['user_subject']];
        $_GET['sf6'] = ['0', '', 'null', $_SESSION['user_region']];
        $_GET['sf7'] = ['0', '', 'null', $_SESSION['user_prof']];
        $_GET['sf8'] = ['0', '', 'null', $_SESSION['user_city']];
        $_GET['sf9'] = ['0', '', 'null', $_SESSION['user_index']];
        $_GET['sf10'] = ['0', '', 'null', $_SESSION['user_street']];
        $_GET['sf11'] = ['0', '', 'null', $_SESSION['user_house']];
        $_GET['sf14_from'] = 14; //мероприятие запущено
    }
    include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/catalog.php";
}
?>