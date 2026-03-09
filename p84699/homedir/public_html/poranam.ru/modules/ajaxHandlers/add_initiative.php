<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';

if (el_checkAjax()) {
    $err = 0;
    $errStr = array();
    $errFields = array();
    $is_vote = ($_POST['is_vote'] == '1');
    $res = null;

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

    if ($err == 0) {

    	if($is_vote){
    		$label = 'Голосование';
    		$label1 = 'голосования';
    		$end = 'о';
    		$statField = 20;
	    }else{
		    $label = 'Инициатива';
		    $label1 = 'инициативы';
		    $end = 'а';
            $statField = 21;
	    }

        $insert = array(
            'active' => 1,
            'cat' => ($is_vote) ? 398 : 397,
            'site_id' => 1,
            'field4' => $_SESSION['user_index'].'_'.$_SESSION['user_id'],
            'field12' => intval($_POST['theme']),
            'field1' => addslashes($_POST['question']),
            'field5' => intval($_POST['region']),
            'field6' => intval($_POST['district']),
            'field8' => addslashes($_POST['city']),
            'field9' => addslashes($_POST['post_index']),
            'field17' => (is_array($_POST['groups'])) ? implode(', ', $_POST['groups']) : $_POST['groups'],
            'field10' => addslashes($_POST['street']),
            'field11' => addslashes($_POST['house']),
            'field7' => intval($_POST['professions']),
            'field13' => intval($_POST['rank']),
            'field2' => addslashes($_POST['init_start']),
            'field3' => addslashes($_POST['init_end']),
            'field14' => ($is_vote) ? 4 : 1
        );
        $result = el_dbinsert('catalog_init_data', $insert);
	    $last_id = el_dbselect("SELECT LAST_INSERT_ID() AS `last`", 0, $last_id, 'row');

	    //Обновление информации о созданных голосованиях у автора
        el_dbselect("UPDATE catalog_users_data SET field".$statField."=(field".$statField." + 1) 
        WHERE id=".intval($_SESSION['user_id']), 0, $res, 'result', true);

        if($is_vote){
	        for($i = 0; $i < count($_POST['answers']); $i++) {
		        $res = el_dbselect("INSERT INTO catalog_votesQuestions_data (field1, field2) 
					VALUES ('" . addslashes($_POST['answers'][$i]) . "', '" . intval($last_id['last']) . "')",
			        0, $result, 'result', true);
	        }
        }

        if($result){
            echo json_encode(array(
                'result' => true,
                'resultText' => $label.' успешно создан'.$end.'<br>и направлен'.$end.' на проверку админстратору
                <script>el_app.dialog_close("initiative_new");initiatives.initListUpdate()</script>',
                'errorFields' => array()));
        }else{
            echo json_encode(array(
                'result' => false,
                'resultText' => 'Во время создания '.$label1.' произошла программная ошибка.<br>
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