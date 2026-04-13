<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';
if (el_checkAjax()) {
    $_GET = $_POST;
    $row_dbcontent['cat'] = 398;
    $row_dbcontent['kod'] = 'cataloginit';
    if(intval($_SESSION['user_level']) == 0 || intval($_SESSION['user_level']) == 10){
        $_GET['sf14_from'] = 4;
    }
    if(intval($_SESSION['user_level']) > 0 && intval($_SESSION['user_level']) < 11){
        // Для обычных пользователей: показывать голосования, где они участвуют ИЛИ участвуют все
        // Это достигается через специальную логику в catalog.php - голосования фильтруются по OR условиям
        $_GET['user_filter_mode'] = 'participant_or_all'; // Специальный режим фильтрации
    }
    include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/catalog.php";
}
?>