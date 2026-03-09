<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';
if (el_checkAjax()) {
    $_GET = $_POST;
    $row_dbcontent['cat'] = 6;
    $row_dbcontent['kod'] = 'catalogusers';
    $cat = $row_dbcontent['cat'] = 6;

    switch(intval($_SESSION['user_level'])){
        case 6:
            $_GET['sf8'] = $_SESSION['user_subject'];
            break;
        case 7:
            $_GET['sf10'] = $_SESSION['user_city'];
            break;
        case 8:
            $_GET['sf9'] = $_SESSION['user_region'];
            break;
        case 9:
            $_GET['sf16'] = $_SESSION['user_direct_group'];
            break;
        default:
            $_GET['status'] = 'both';
            $_GET['sf8'] = !empty($_GET['sf8']) ? $_GET['sf8'] : '';
            $_GET['sf9'] = !empty($_GET['sf9']) ? $_GET['sf9'] : '';;
            $_GET['sf10'] = strlen(trim($_GET['sf10'])) > 0 ? $_GET['sf10'] : '';;
            $_GET['sf11'] = strlen(trim($_GET['sf11'])) > 0 ? $_GET['sf11'] : '';
        /*if(intval($_GET['sf16']) > 0) {
            $_GET['sf16|sf25'] = $_GET['sf16'];
        }*/
    }

    if(isset($_SESSION['user_level']) && !in_array($_SESSION['user_level'], [10, 11])) {
        $groups = getSubGroupsByUser($_SESSION['user_id']);
        //print_r($groups);
        $_GET['sf16'] = implode('|', $groups);
        $_GET['sf6_from'] = $_SESSION['user_level'] + 1;
        $_GET['sf6_to'] = 10;
    }
    include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/catalog.php";
}
?>