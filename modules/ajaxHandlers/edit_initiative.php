<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';

if (el_checkAjax()) {
    $err = 0;
    $errStr = array();
    $errFields = array();
	$is_vote = false;
	$init_name1 = 'Инициатива';
	$init_name2 = 'инициативы';
	$init_name3 = 'изменена';

    if(!isset($_POST['init_id'])){
    	$is_vote = true;
	    $_POST['init_id'] = $_POST['vote_id'];
	    $init_name1 = 'Голосование';
	    $init_name2 = 'голосования';
	    $init_name3 = 'изменено';
    }

    if(strlen(trim($_POST['theme'])) == 0){
        $err++;
        $errStr[] = 'Укажите тему';
        $errFields[] = 'theme';
    }
    if(strlen(trim($_POST['question'])) == 0){
        $err++;
        $errStr[] = 'Укажите вопрос';
        $errFields[] = 'question';
    }
    if(!isset($_POST['init_select_all']) && $_POST['init_select_all'] != '1'
    && strlen(trim($_POST['region'])) == 0 && strlen(trim($_POST['professions'])) == 0){
        $err++;
        $errStr[] = 'Укажите регион или профессию';
        $errFields[] = 'region';
    }

    if($is_vote){
	    $emptyAnswer = 0;
	    $countAnswers = 0;
    	foreach($_POST['answers'] as $answer){
			if(strlen(trim($answer)) == 0){
				$emptyAnswer++;
			}
		    $countAnswers++;
	    }
    	if($countAnswers < 2){
		    $err++;
		    $errStr[] = 'Укажите не менее двух вариантов ответов';
		    $errFields[] = 'answers';
	    }elseif($emptyAnswer > 0) {
		    $err++;
		    $errStr[] = 'Заполните все варианты ответов';
		    $errFields[] = 'answers';
	    }
    }

    $init = el_dbselect('SELECT field14, field24 FROM catalog_init_data WHERE id = ' . intval($_POST['vote_id']), 0, $result, 'row', true);
    $message = $init['field24'];
    $sender = el_dbselect('SELECT field1 FROM catalog_userstatus_data WHERE id = ' . $_SESSION['user_level'], 0, $result, 'row', true);
    if (strlen(trim($_POST['message'])) > 0) {
        $message .= '<p><span>' . el_date1(date('Y-m-d H:i')) . ' ' .
            $_SESSION['visual_user_id'] . ' ' . $sender['field1'] . ':</span><br>' . nl2br($_POST['message']) . '</p>';
    }

    if ($err == 0) {
        $edit = array(
            'active' => 1,
            'cat' => ($is_vote) ? 398 : 397,
            'site_id' => 1,
            'field12' => intval($_POST['theme']),
            'field1' => $_POST['question'],
            'field5' => intval($_POST['region']),
            'field6' => intval($_POST['district']),
            'field8' => $_POST['city'],
            'field9' => $_POST['post_index'],
            'field10' => $_POST['street'],
            'field11' => $_POST['house'],
            'field7' => intval($_POST['professions']),
            'field13' => intval($_POST['rank']),
            'field2' => $_POST['init_start'],
            'field3' => $_POST['init_end'],
            'field24' => $message,
            'field14' => ($is_vote) ? 4 : 1
        );

        if($is_vote){
        	for($i = 0; $i < count($_POST['answers']); $i++){
				$exist = el_dbselect("SELECT id, count(id) AS exist FROM catalog_votesQuestions_data 
				WHERE field1 = '".addslashes($_POST['old_answers'][$i])."' AND field2 = ".intval($_POST['vote_id']),
					0, $exist, 'row', true);

				if($exist['exist'] > 0){
					$res = el_dbselect("UPDATE catalog_votesQuestions_data SET field1 = '".addslashes($_POST['answers'][$i])."' 
					WHERE id = ".$exist['id'], 0, $result, 'result', true);
				}else{
					$res = el_dbselect("INSERT INTO catalog_votesQuestions_data (field1, field2) 
					VALUES ('".addslashes($_POST['answers'][$i])."', '".intval($_POST['vote_id'])."')",
						0, $result, 'result', true);
				}
	        }
        }

        $queryArr = array();
        foreach($edit as $field => $value){
            $queryArr[] = $field." = '".addslashes($value)."'";
        }
        $query = implode(', ', $queryArr);

        $result = el_dbselect("UPDATE catalog_init_data SET $query WHERE id=".intval($_POST['init_id']),
            0, $result, 'result', true);

        if($result){
            echo json_encode(array(
                'result' => true,
                'resultText' => $init_name1.' успешно '.$init_name3.'.
                <script>el_app.dialog_close("initiative_new");initiatives.initListUpdate()</script>',
                'errorFields' => array()));
        }else{
            echo json_encode(array(
                'result' => false,
                'resultText' => 'Во время изменения '.$init_name2.' произошла программная ошибка.<br>
                Сообщите об этом администратору.',
                'errorFields' => array()));
        }

    }else{
        echo json_encode(array(
            'result' => false,
            'resultText' => implode('<br>', $errStr),
            'errorFields' => $errFields));
    }
}
?>