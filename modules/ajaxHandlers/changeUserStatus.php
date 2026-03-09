<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';

if (el_checkAjax()) {
    $err = 0;
    $errStr = array();
    $errFields = array();
    $result = false;
    $subQuery = "";

    $statuses = getRegistry('userstatus');
    $allowedStatuses = (intval($_SESSION['user_level']) == 11) ? $statuses : filterStatuses($statuses);

    if(intval($_POST['status']) == 0){
        $err++;
        $errStr[] = 'Укажите новый ранг';
        $errFields[] = 'userStatus';
    }

    if(intval($_SESSION['user_level']) == 10){
        $err++;
        $errStr[] = 'У Вас нет прав на это действие';
        $errFields[] = 'userStatus';
    }

    if(!array_key_exists($_POST['status'], $allowedStatuses)){
        $err++;
        $errStr[] = 'Вы не можете назначить ранг выше своего.';
        $errFields[] = 'userStatus';
    }

    if ($err == 0) {

        if(intval($_POST['status']) == 10){
            $subQuery = ", field16 = ''";
        }

        $result = el_dbselect("UPDATE catalog_users_data SET field6=".intval($_POST['status'])."$subQuery WHERE id=".intval($_POST['user']),
            0, $result, 'result', true);

        if($result != false){
            echo json_encode(array(
                'result' => true,
                'resultText' => 'Ранг успешно изменен. Изменения вступят в силу<br>
                после повторной авторизации пользователя.',
                'errorFields' => array()));
        }else{
            echo json_encode(array(
                'result' => false,
                'resultText' => 'Во время смены ранга произошла программная ошибка.<br>
                Сообщите об этом администратору.',
                'errorFields' => array()));
        }

    }else{
        echo json_encode(array(
            'result' => false,
            'resultText' => implode('<br>', $errStr),
            'errorFields' => $errFields));
    }

}
