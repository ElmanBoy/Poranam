<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';

if (el_checkAjax()) {
    $init_id = intval($_POST['init_id']);
    $result = null;
    $err = 0;
    $errStr = array();
    $errFields = array();
	$is_vote = false;
	$init_name1 = 'Мероприятие';
	$init_name2 = 'мероприятия';
	$init_name3 = 'изменено';

	$init = el_dbselect("SELECT field14, field24 FROM catalog_init_data WHERE id = $init_id", 0, $result, 'row', true);
    $message = $init['field24'];
	$sender = el_dbselect("SELECT field1 FROM catalog_userstatus_data WHERE id = ".$_SESSION['user_level'], 0, $result, 'row', true);
	if(strlen(trim($_POST['message'])) > 0) {
        $message .= '<p><span>' . el_date1(date('Y-m-d H:i')) . ' ' .
            $_SESSION['visual_user_id'] . ' ' . $sender['field1'] . ':</span><br>' . nl2br($_POST['message']) . '</p>';
    }

	if($init['field14'] < 15) {

        if(strlen(trim($_POST['init_start'])) == 0){
            $err++;
            $errStr[] = 'Укажите дату начала проведения';
            $errFields[] = 'init_start';
        }
        if(strlen(trim($_POST['init_start_time'])) == 0){
            $err++;
            $errStr[] = 'Укажите время начала проведения';
            $errFields[] = 'init_start_time';
        }
        if(strlen(trim($_POST['init_end'])) == 0){
            $err++;
            $errStr[] = 'Укажите дату окончания проведения';
            $errFields[] = 'init_end';
        }
        if(strlen(trim($_POST['init_end_time'])) == 0){
            $err++;
            $errStr[] = 'Укажите время окончания проведения';
            $errFields[] = 'init_end_time';
        }
        if (strlen(trim($_POST['address'])) == 0) {
            $err++;
            $errStr[] = 'Укажите место проведения';
            $errFields[] = 'address';
        }
        if (strlen(trim($_POST['name'])) == 0) {
            $err++;
            $errStr[] = 'Укажите название мероприятия';
            $errFields[] = 'name';
        }
        if (strlen(trim($_POST['annotation'])) == 0) {
            $err++;
            $errStr[] = 'Укажите аннотацию (краткое описание)';
            $errFields[] = 'annotation';
        }
        if (!isset($_POST['init_select_all']) && $_POST['init_select_all'] != '1'
            && intval($_POST['region']) == 0 && !isset($_POST['professions'])) {
            $err++;
            $errStr[] = 'Укажите субъект или профессию';
            $errFields[] = 'region';
        }
        if (isset($_POST['init_select_all']) && $_POST['init_select_all'] == '1'){
            $_POST['region'] = $_POST['district'] = $_POST['city'] = $_POST['post_index'] = $_POST['groups'] =
                $_POST['street'] = $_POST['house'] = $_POST['professions'] = '';
        }

        /*if($is_vote){
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
        }*/

        if ($err == 0) {
            $edit = array(
                'active' => 1,
                'cat' => 405,
                'site_id' => 1,
                //'field4' => $_SESSION['visual_user_id'],
                'field1' => htmlspecialchars($_POST['name']),
                'field12' => htmlspecialchars((is_array($_POST['theme']) ? implode(',', $_POST['theme']) :
                    (intval($_POST['theme']) == 0 ? '' : $_POST['theme']))),
                'field18' => htmlspecialchars($_POST['address']),
                'field5' => (is_array($_POST['region'])) ? implode(',', $_POST['region']) : $_POST['region'],
                'field6' => (is_array($_POST['district'])) ? implode(',', $_POST['district']) : $_POST['district'],
                'field8' => addslashes($_POST['city']),
                'field9' => addslashes($_POST['post_index']),
                'field17' => (is_array($_POST['groups'])) ? implode(',', $_POST['groups']) : $_POST['groups'],
                'field10' => addslashes($_POST['street']),
                'field11' => addslashes($_POST['house']),
                'field7' => (is_array($_POST['professions'])) ? implode(',', $_POST['professions']) : $_POST['professions'],
                'field13' => intval($_POST['rank']),
                'field2' => addslashes($_POST['init_start']).' '.addslashes($_POST['init_start_time']),
                'field22' => addslashes($_POST['init_start_time']),
                'field21' => nl2br(htmlspecialchars($_POST['annotation'])),
                'field3' => addslashes($_POST['init_end']).' '.addslashes($_POST['init_end_time']),
                'field24' => $message,
                'field25' => intval($_POST['publish_announce']),
                'field26' => intval($_POST['publish_report'])
            );

            if ($is_vote) {
                for ($i = 0; $i < count($_POST['answers']); $i++) {
                    $exist = el_dbselect("SELECT id, count(id) AS exist FROM catalog_votesQuestions_data 
				WHERE field1 = '" . addslashes($_POST['old_answers'][$i]) . "' AND field2 = " . intval($_POST['vote_id']),
                        0, $exist, 'row', true);

                    if ($exist['exist'] > 0) {
                        $res = el_dbselect("UPDATE catalog_votesQuestions_data SET field1 = '" . addslashes($_POST['answers'][$i]) . "' 
					WHERE id = " . $exist['id'], 0, $result, 'result', true);
                    } else {
                        $res = el_dbselect("INSERT INTO catalog_votesQuestions_data (field1, field2) 
					VALUES ('" . addslashes($_POST['answers'][$i]) . "', '" . intval($_POST['vote_id']) . "')",
                            0, $result, 'result', true);
                    }
                }
            }

            $queryArr = array();
            foreach ($edit as $field => $value) {
                $queryArr[] = $field . " = '" . addslashes($value) . "'";
            }
            $query = implode(', ', $queryArr);

            $result = el_dbselect("UPDATE catalog_init_data SET $query WHERE id = $init_id",
                0, $result, 'result', true);

            if ($result) {
                echo json_encode(array(
                    'result' => true,
                    'resultText' => $init_name1 . ' успешно ' . $init_name3 . '.
                <script>el_app.dialog_close("events_edit");meetings.initListUpdate()</script>',
                    'errorFields' => array()));
            } else {
                echo json_encode(array(
                    'result' => false,
                    'resultText' => 'Во время изменения ' . $init_name2 . ' произошла программная ошибка.<br>
                Сообщите об этом администратору.',
                    'errorFields' => array()));
            }

        } else {
            echo json_encode(array(
                'result' => false,
                'resultText' => implode('<br>', $errStr),
                'errorFields' => $errFields));
        }
    }else{
        if (strlen(trim($_POST['report'])) == 0) {
            $err++;
            $errStr[] = 'Укажите текст для сайта';
            $errFields[] = 'report';
        }

        if ($err == 0) {
            $edit = array(
                'field23' => nl2br($_POST['report']),
                'field20' => $_POST['photo'],
                'field24' => $message,
                'field25' => intval($_POST['publish_announce']),
                'field26' => intval($_POST['publish_report'])
            );
            $queryArr = array();
            foreach ($edit as $field => $value) {
                $queryArr[] = $field . " = '" . addslashes($value) . "'";
            }
            $query = implode(', ', $queryArr);

            $result = el_dbselect("UPDATE catalog_init_data SET $query WHERE id= $init_id",
                0, $result, 'result', true);

            if ($result) {
                echo json_encode(array(
                    'result' => true,
                    'resultText' => $init_name1 . ' успешно ' . $init_name3 . '.
                <script>el_app.dialog_close("events_edit");meetings.initListUpdate()</script>',
                    'errorFields' => array()));
            } else {
                echo json_encode(array(
                    'result' => false,
                    'resultText' => 'Во время изменения ' . $init_name2 . ' произошла программная ошибка.<br>
                Сообщите об этом администратору.',
                    'errorFields' => array()));
            }
        }else {
            echo json_encode(array(
                'result' => false,
                'resultText' => implode('<br>', $errStr),
                'errorFields' => $errFields));
        }
    }
}
?>