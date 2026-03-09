<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';
if (el_checkAjax()) {
    $_GET = $_POST;
    $row_dbcontent['cat'] = 403;
    $row_dbcontent['kod'] = 'catalogusers';
    if(intval($_SESSION['user_level']) == 0 || intval($_SESSION['user_level']) == 10){
        $_GET['sf14_from'] = 4;
    }
    if(intval($_SESSION['user_level']) > 0 && intval($_SESSION['user_level']) < 11){
        $_GET['sf5'] = array(0, '', $_SESSION['user_subject']);
        $_GET['sf6'] = array(0, '', $_SESSION['user_region']);
        $_GET['sf7'] = array(0, '', $_SESSION['user_prof']);
        $_GET['sf8'] = array(0, '', $_SESSION['user_city']);
        $_GET['sf9'] = array(0, '', $_SESSION['user_index']);
        $_GET['sf10'] = array(0, '', $_SESSION['user_street']);
        $_GET['sf11'] = array(0, '', $_SESSION['user_house']);
    }
    include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/catalog.php";
}
?>