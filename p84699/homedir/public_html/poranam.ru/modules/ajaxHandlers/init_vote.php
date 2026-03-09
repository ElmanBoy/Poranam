<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';
if (el_checkAjax() && intval($_SESSION['user_id']) > 0) {

    $res = $update = $insert = '';
    $initId = intval($_POST['id']);
    $userId = $_SESSION['user_id'];
    $vote = intval($_POST['vote']);

    $res = el_dbselect("SELECT id FROM catalog_initresult_data WHERE field2 = '$initId' AND field1 = '$userId'",
     0, $res, 'result', true);
    if(el_dbnumrows($res) > 0){
        $update = el_dbselect("UPDATE catalog_initresult_data SET field4 = '$vote' 
        WHERE field2 = '$initId' AND field1 = '$userId'", 0, $update, 'result', true);
        if($update != false) {
            $voteResults = el_calcVoteResults($initId);
            $totalResults = array_sum($voteResults);
            $stat = el_calcVoteUsers($initId);
            echo json_encode(array(
                'result' => true,
                'votes' => $voteResults,
                'totalVotes' => $totalResults,
                'voteStat' => $stat,
                'resultText' => 'Ваш голос изменён.<br>До завершения голосования Вы можете изменить свой голос.',
                'errorFields' => array()));
        }else{
            echo json_encode(array(
                'result' => false,
                'resultText' => 'Во время изменения Вашего голоса произошла программная ошибка.<br>
                Сообщите об этом администратору.',
                'errorFields' => array()));
        }
    }else{
        $insert = el_dbselect("INSERT INTO catalog_initresult_data (field1, field2, field3, field4) 
        VALUES ($userId, $initId, '".date('Y-m-d H:i:s')."', $vote)",
            0, $insert, 'result', true);
        if($insert != false) {
            $voteResults = el_calcVoteResults($initId);
            $totalResults = array_sum($voteResults);
            $stat = el_calcVoteUsers($initId);
            echo json_encode(array(
                'result' => true,
                'votes' => $voteResults,
                'totalVotes' => $totalResults,
                'voteStat' => $stat,
                'resultText' => 'Ваш голос записан.<br>До завершения голосования Вы можете изменить свой голос.',
                'errorFields' => array()));
        }else{
            echo json_encode(array(
                'result' => false,
                'resultText' => 'Во время записи Вашего голоса произошла программная ошибка.<br>
                Сообщите об этом администратору.',
                'errorFields' => array()));
        }
    }

}
?>
