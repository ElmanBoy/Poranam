<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';

if (el_checkAjax() && intval($_SESSION['user_id']) > 0) {

    $allUsers = [];

    $meeting_id = intval($_POST['params']);
    $meeting = el_dbselect("SELECT * FROM catalog_init_data WHERE id = $meeting_id", 0, $meeting, 'row', true);

    $queryArr = [];
    if(intval($meeting['field5']) > 0){
        $queryArr[] = 'field8 = '.$meeting['field5'];
    }
    if(intval($meeting['field6']) > 0){
        $queryArr[] = 'field9 = '.$meeting['field6'];
    }
    if(intval($meeting['field7']) > 0){
        $queryArr[] = 'field7 = '.$meeting['field7'];
    }
    if(intval($meeting['field8']) > 0){
        $queryArr[] = 'field10 = '.$meeting['field8'];
    }
    if(intval($meeting['field9']) > 0){
        $queryArr[] = 'field11 = \''.$meeting['field9'].'\'';
    }


    $users = el_dbselect('SELECT id FROM catalog_users_data WHERE active = 1' .
        (count($queryArr) > 0 ? implode(' AND ', $queryArr) : ''), 0, $users, 'result', true
    );
    $ru = el_dbfetch($users);
    do {
        $allUsers[] = $ru['id'];
    } while ($ru = el_dbfetch($users));

    echo json_encode($allUsers);
}