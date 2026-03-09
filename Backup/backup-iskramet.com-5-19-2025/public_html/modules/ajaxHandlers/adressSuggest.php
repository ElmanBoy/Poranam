<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/modules/vendor/autoload.php';
$token = "eb83a00ad060d6cca3d2341f2acb15cdb76b67df";
$secret_key = '4b83adccaf675cca4dc0bd48e506d5ed8ae507a6';
$dadata = new \Dadata\DadataClient($token, $secret_key);
echo json_encode($dadata->clean("address", $_POST['address']));
?>