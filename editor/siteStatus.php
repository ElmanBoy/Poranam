<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php');
$requiredUserLevel = array(0, 1);
include($_SERVER['DOCUMENT_ROOT'] . "/editor/secure/secure.php");

$s = el_dbselect("UPDATE sites SET active='".intval($_POST['status'])."' WHERE id=".intval($_POST['id']),
    0, $s, 'result', true);
echo json_encode(($s != false) ? array("result" => true) : array("result" => false, "errors" => "Не удалось сменить статус сайта"));
?>
