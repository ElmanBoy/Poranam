<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';

if (el_checkAjax() && intval($_SESSION['user_id']) > 0) {

    $result = null;
    $users = null;
    $err = 0;
    $errMsg = [];
    $meeting_id = intval($_POST['id']);

    $users = el_dbselect("SELECT users FROM meeting_members WHERE meeting_id = $meeting_id", 0, $users, 'result', true);
    if(el_dbnumrows($users) > 0){
        $ru = el_dbfetch($users);
        $members = explode(',', $ru['users']);
        foreach($members as $member){
            el_removepoints(5, $member);
        }
    }

    $result = el_dbselect("DELETE FROM meeting_members WHERE meeting_id = $meeting_id", 0, $result);

    if(is_array($_POST['members']) && count($_POST['members']) > 0){
        $result = el_dbselect("INSERT INTO meeting_members (meeting_id, users) 
        VALUES($meeting_id, '".implode(',', $_POST['members'])."')", 0, $result, 'result', true);

        foreach($_POST['members'] as $member){
            el_earnpoints(5, $member);
        }
    }
    if($result != false){
        echo 'Изменения сохранены!';
    }
}else{
    echo 'Авторизуйтесь!';
}