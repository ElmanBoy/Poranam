<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';
if (el_checkAjax() && intval($_SESSION['user_id']) > 0) {

    $res = $update = $insert = '';
    $initId = intval($_POST['id']);
    $userId = $_SESSION['user_id'];
    $vote = intval($_POST['vote']);
    $score = el_dbselect("SELECT field3 FROM catalog_scores_data WHERE id = 1", 0, $score, 'row', true);

    // ПРОВЕРКА ПРАВ НА ГОЛОСОВАНИЕ
    $voteData = el_dbselect("SELECT * FROM catalog_init_data WHERE id = '$initId' AND field14 = 6", 1, $voteData, 'row', true);
    if(!$voteData) {
        echo json_encode(array(
            'result' => false,
            'resultText' => 'Голосование не найдено или не активно.',
            'errorFields' => array()));
        exit;
    }

    // Проверяем соответствие пользователя критериям голосования
    $userCanVote = false;
    
    // Если все поля участников пустые (голосование для всех), то любой пользователь может голосовать
    if(empty($voteData['field5']) && empty($voteData['field6']) && empty($voteData['field7']) && 
       empty($voteData['field8']) && empty($voteData['field9']) && empty($voteData['field10']) && 
       empty($voteData['field11']) && empty($voteData['field13'])) {
        $userCanVote = true;
    } else {
        // Иначе проверяем соответствие пользователя критериям
        $userCanVote = true; // Начинаем с true, затем проверяем каждое условие
        
        // Проверяем субъект (если указан)
        if(intval($voteData['field5']) > 0 && intval($userData['field8']) != intval($voteData['field5'])) {
            $userCanVote = false;
        }
        // Проверяем регион (если указан)
        if(intval($voteData['field6']) > 0 && intval($userData['field9']) != intval($voteData['field6'])) {
            $userCanVote = false;
        }
        // Проверяем профессию (если указана)
        if(intval($voteData['field7']) > 0 && intval($userData['field7']) != intval($voteData['field7'])) {
            $userCanVote = false;
        }
        // Проверяем город (если указан)
        if(strlen($voteData['field8']) > 0 && $userData['field10'] != $voteData['field8']) {
            $userCanVote = false;
        }
        // Проверяем индекс (если указан)
        if(strlen($voteData['field9']) > 0 && $userData['field11'] != $voteData['field9']) {
            $userCanVote = false;
        }
        // Проверяем улицу (если указана)
        if(strlen($voteData['field10']) > 0 && $userData['field12'] != $voteData['field10']) {
            $userCanVote = false;
        }
        // Проверяем номер дома (если указан)
        if(strlen($voteData['field11']) > 0 && $userData['field13'] != $voteData['field11']) {
            $userCanVote = false;
        }
        // Проверяем ранг (если указан)
        if(intval($voteData['field13']) > 0 && intval($userData['field6']) != intval($voteData['field13'])) {
            $userCanVote = false;
        }
    }

    if(!$userCanVote) {
        echo json_encode(array(
            'result' => false,
            'resultText' => 'У вас нет прав для участия в этом голосовании.',
            'errorFields' => array()));
        exit;
    }

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

            $scoreUpdate = el_dbselect("UPDATE catalog_users_data SET field18 = (field18 + ".$score['field3'].") 
            WHERE id = $userId", 0, $scoreUpdate, 'result', true);

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
