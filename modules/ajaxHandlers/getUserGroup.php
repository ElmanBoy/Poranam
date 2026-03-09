<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';

if (el_checkAjax()) {
    $err = 0;
    $errStr = array();
    $errFields = array();
    $result = false;
    $groups = "";

    $result = el_dbselect("SELECT * FROM catalog_users_data WHERE id = '".intval($_POST['uid'])."'", 0, $result, 'row', true);
    if(count($result) > 0){
        $groups = buildSelectFromRegistry(getGroupByUser($result), []);
    }

    if($result != false){
        echo json_encode(array(
            'result' => true,
            'resultText' => $groups,
            'errorFields' => array()));
    }else{
        echo json_encode(array(
            'result' => false,
            'resultText' => 'В этом индексе пока нет групп',
            'errorFields' => array()));
    }
}