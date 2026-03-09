<?php
session_start();
//error_reporting(E_ALL);

include $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';
include $_SERVER['DOCUMENT_ROOT'] . '/Connections/site_props.php';

if (el_checkAjax() && intval($_SESSION['user_id']) > 0) {
    $_POST['id'] = intval($_POST['id']);
    if ($_POST['state'] == "true") {
        if($_POST['id'] == 0){
            $_SESSION['user_checked'] = [$_POST['id']];
        }else {
            $_SESSION['user_checked'][] = $_POST['id'];
        }
    } else {
        if($_POST['id'] == 0){
            $_SESSION['user_checked'] = [];
        }else{
            $pos = array_search($_POST['id'], $_SESSION['user_checked']);
            if ($pos !== false) {
                unset($_SESSION['user_checked'][$pos]);
            }
        }
    }
    echo json_encode(array(
        'container' => 'marked',
        'result' => false,
        'resultText' => $_SESSION['user_checked'],
        'errorFields' => []));
}else{
    echo json_encode(array(
        'container' => 'message_login',
        'result' => false,
        'resultText' => 'Пожалуйста, авторизуйтесь',
        'errorFields' => []));
}
?>
