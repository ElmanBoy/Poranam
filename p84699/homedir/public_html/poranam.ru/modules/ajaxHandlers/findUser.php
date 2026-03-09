<?php
session_start();
error_reporting(0);
require_once($_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php');
$res = '';

if(isset($_POST['mail']) && !isset($_SESSION['login'])){
    $res = el_dbselect("SELECT id FROM catalog_users_data WHERE field2='".addslashes($_POST['mail'])."'",
    0, $res, 'row', true);
    if(intval($res['id']) > 0){
        echo json_encode(array("exist" => true));
    }else{
        echo json_encode(array("exist" => false));
    }
}else{
    echo json_encode(array("exist" => false));
}
?>