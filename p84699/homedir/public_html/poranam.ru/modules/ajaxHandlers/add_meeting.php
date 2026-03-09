<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';

if (el_checkAjax()) {
    $err = 0;
    $errStr = array();
    $errFields = array();
    $res = null;

    if(strlen(trim($_POST['init_start'])) == 0){
        $err++;
        $errStr[] = 'Укажите дату проведения';
        $errFields[] = 'init_start';
    }
    if(strlen(trim($_POST['init_start_time'])) == 0){
        $err++;
        $errStr[] = 'Укажите время начала проведения';
        $errFields[] = 'init_start_time';
    }
    if(strlen(trim($_POST['address'])) == 0){
        $err++;
        $errStr[] = 'Укажите место проведения';
        $errFields[] = 'address';
    }
    if(strlen(trim($_POST['name'])) == 0){
        $err++;
        $errStr[] = 'Укажите название';
        $errFields[] = 'name';
    }
    if(!isset($_POST['init_select_all']) && $_POST['init_select_all'] != '1'
    && strlen(trim($_POST['region'])) == 0 && strlen(trim($_POST['professions'])) == 0){
        $err++;
        $errStr[] = 'Укажите регион или профессию';
        $errFields[] = 'region';
    }

    if ($err == 0) {

        $insert = array(
            'active' => 1,
            'cat' => 405,
            'site_id' => 1,
            'field4' => $_SESSION['user_index'].'_'.$_SESSION['user_id'],
            'field1' => addslashes($_POST['name']),
            'field18' => addslashes($_POST['address']),
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
            'field22' => addslashes($_POST['init_start_time']),
            'field21' => addslashes($_POST['annotation']),
            'field3' => addslashes($_POST['init_end']),
            'field14' => 10
        );
        $result = el_dbinsert('catalog_init_data', $insert);
	    $last_id = el_dbselect("SELECT LAST_INSERT_ID() AS `last`", 0, $last_id, 'row');

	    //Обновление информации о созданных мероприятиях у автора
        el_dbselect("UPDATE catalog_users_data SET field22=(field22 + 1) 
        WHERE id=".intval($_SESSION['user_id']), 0, $res, 'result', true);


        if($result){
            echo json_encode(array(
                'result' => true,
                'resultText' => 'Мероприятие успешно создано<br>и направлено на проверку админстратору
                <script>el_app.dialog_close("initiative_new");meetings.initListUpdate()</script>',
                'errorFields' => array()));
        }else{
            echo json_encode(array(
                'result' => false,
                'resultText' => 'Во время создания мероприятия произошла программная ошибка.<br>
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