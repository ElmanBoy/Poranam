<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';

if (el_checkAjax()) {
    $err = 0;
    $errStr = array();
    $errFields = array();
    $result = false;
    $groups = [];

    $result = el_dbselect("SELECT * FROM catalog_groups_data WHERE field1='".addslashes($_POST['index'])."'",
        0, $result, 'result', true);
    if(el_dbnumrows($result) > 0){
        $rg = el_dbfetch($result);
        do{
            $groups[] = '<option value="'.$rg['id'].'">'.$rg['field1'].'-'.$rg['id'].'</option>';
        }while($rg = el_dbfetch($result));
    }else{
        $groups[] = '<option value="0">Все группы</option>';
    }

    if($result != false){
        echo json_encode(array(
            'result' => true,
            'resultText' => implode("\n", $groups),
            'errorFields' => array()));
    }else{
        echo json_encode(array(
            'result' => false,
            'resultText' => 'В этом индексе пока нет групп',
            'errorFields' => array()));
    }
}