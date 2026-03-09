<?php
session_start();
//error_reporting(E_ALL);

include $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';
include $_SERVER['DOCUMENT_ROOT'] . '/Connections/site_props.php';

if (el_checkAjax() && intval($_SESSION['user_id']) > 0) {
    $cat = 6;

    $err = 0;
    $errStr = array();
    $message = '';
    $result = false;

    if (isset($_POST["ajax"]) && $_POST["ajax"] == "1") {

        $oldData = el_dbselect("DELETE FROM catalog_users_data WHERE id = '" . intval($_SESSION['user_id']) . "'",
            0, $res, 'row', true);


            $result = true;
            $message = 'Ваш профиль удалён.<script>setTimeout(function(){document.location.href = "/?logout"}, 2000)</script>';


        echo json_encode(array(
            'container' => 'message_login',
            'result' => $result,
            'resultText' => $message,
            'errorFields' => []));
    }
}else{
    echo json_encode(array(
        'container' => 'message_login',
        'result' => false,
        'resultText' => 'Пожалуйста, авторизуйтесь',
        'errorFields' => []));
}
?>
