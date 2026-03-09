<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';

if (el_checkAjax()) {
    $err = 0;
    $errStr = array();
    $errFields = array();
    $result = false;
    $groups = ['<option value="0">Без группы</option>'];
    $sel = '';

    $result = el_dbselect("SELECT * FROM catalog_groups_data WHERE field1='И-".addslashes($_POST['index'])."'",
        0, $result, 'result', true);
    if(el_dbnumrows($result) > 0){
        $rg = el_dbfetch($result);
        do{
            if(isset($_POST['values']) && count($_POST['values']) > 0){
                $sel = in_array($rg['id'], $_POST['values']) ? ' selected' : '';
            }
            $groups[] = '<option value="'.$rg['id'].'"'.$sel.'>'.$rg['field1'].'-'.$rg['field2'].'</option>';
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