<?php
@session_start();

include $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';
//Получение настроек модуля
$queue_props = el_getModuleProps('messages');
$themes = getRegistry('themes');
$status = getRegistry('messagestatus');
$stuffArr = $queue_props['stuff'];
//Массив обязательных полей
$reqFields = array(
    'q_name' => 'Имя',
    'q_surname' => 'Имя',
    'q_email' => 'Email',
    'q_tel' => 'Телефон',
    'message' => 'Тема обращения',
    'text' => 'Текст сообщения'
);
$err = 0;
$errStr = array();
$errFields = array();
$site_id = intval($_SESSION['view_site_id']);
$humanFio = addslashes($_POST['q_surname'] . ' ' . $_POST['q_name'] . ' ' . $_POST['q_thirdname']);
$currDate = date('Y-m-d H:i:s');

//Проверка обязательных полей
foreach ($reqFields as $key => $val) {
    if (!isset($_POST[$key]) || strlen(trim($_POST[$key])) == 0) {
        $err++;
        $errStr[] = 'Пожалуйста, заполните поле &laquo;' . $val . '&raquo;';
        $errFields[] = $key;
    }
}

$fileArr = array();
if(count($_FILES['files']) > 0){
    for($i = 0; $i < count($_FILES['files']['name']); $i++){
        if(($_FILES['files']['size'][$i] / 1024 / 1024) > 10){
            $err++;
            $errStr[] = 'Размер файла &laquo;'.$_FILES['files']['name'][$i].'&raquo; превышает допустимый максимум в 10Мб.';
        }else{
            $fileArr[] = array('tmp_name' => $_FILES['files']['tmp_name'][$i], 'name' => $_FILES['files']['name'][$i]);
        }
    }
}

if ($err == 0) {
    //обработка полученных файлов
    $fileList = '';
    if(count($fileArr) > 0){
        $destFiles = array();
        reset($fileArr);
        for($i = 0; $i < count($fileArr); $i++){
            $nameArr = explode('.', $fileArr[$i]['name']);
            $fileExt = array_pop($nameArr);
            $fileName = el_translit(trim(implode('.', $nameArr))).'_'.el_genpass().'.'.$fileExt;
            $destPath = $_SERVER['DOCUMENT_ROOT'].'/files/tmp/'.$fileName;
            $destFiles[] = $destPath;
            $urlFiles[] = 'https://'.$_SERVER['SERVER_NAME'].'/files/tmp/'.$fileName;
            move_uploaded_file($fileArr[$i]['tmp_name'], $destPath);
        }
        $fileList = implode(' , ', $destFiles);
    }

    //Запись в базу данных новой очереди
    $q = el_dbselect("INSERT INTO catalog_messages_data (active, site_id, field1, field2, field3, field4, field5, field6, field7, field9) VALUES 
    (
    1,
    '" . $site_id . "',
    '" . $currDate . "',
    '" . addslashes(nl2br(strip_tags($_POST['text']))) . "',
    '" . $humanFio . "',
    '" . addslashes($_POST['q_email']) . "',
    '" . addslashes($_POST['q_tel']) . "',
    '0',
    '" . addslashes($_POST['message']) . "',
    '" . addslashes(implode(' , ', $urlFiles)) . "'
    )", 0, $q, 'result', true);

    $czn = el_dbselect("SELECT full_name FROM sites WHERE id = ".$site_id, 0, $czn, 'row', true);

    if ($q != false) {
        //Номер очереди
        $lastId = mysqli_insert_id($dbconn);
        $queueNumber = date('dmYHIs').'-'.$site_id.'-'.$lastId;

        //Справочник сотрудников
        $stuffR = el_dbselect("SELECT primary_key, fio, email FROM phpSP_users 
        WHERE primary_key in (" . implode(', ', $stuffArr) . ")", 0, $stuffR, 'result',
            true);
        $str = array();
        if (el_dbnumrows($stuffR) > 0) {
            $rst = el_dbfetch($stuffR);
            do {
                if(in_array($rst['primary_key'], $stuffArr)) {
                    $str[] = $rst['email'];
                }
            } while ($rst = el_dbfetch($stuffR));
        }

        //Получение настроек сайта
        $site = el_dbselect("SELECT * FROM sites WHERE id = ".$site_id, 0, $s, 'row', true);



        //TODO: Осталось добавить отправку смс уведомление польлзователю и оператору уведомлений
        //Отправка уведомления о новой очереди операторам
        $letter_body = el_render('/tmpl/letter/letter2.php',
            ['caption' => 'Уведомляем вас о новом обращении № '.$queueNumber,
                'text' => '<p><strong>Дата:</strong> ' . el_date1(date('Y-m-d')) . ';</p>
            <p><strong>Время:</strong> ' . date('H:i:s') . ';</p>
            <p><strong>Ф.И.О.:</strong> ' . $humanFio . ';</p>
            <p><strong>Телефон:</strong> ' . $_POST['q_tel'] . ';</p>
            <p><strong>Email:</strong> ' . $_POST['q_email'] . ';</p>
            <p><strong>Тема:</strong> ' . $themes[$_POST['message']] . ';</p>
            <p><strong>Текст сообщения:</strong><br><br>'.nl2br(strip_tags($_POST['text'])).'</p>',
                'phone' => $site['phones'],
                'cznName' => $site['full_name'],
                'buttonText' => 'Перейти в личный кабинет',
                'buttonUrl' => 'http://' . $_SERVER['SERVER_NAME'] . '/correspondence/'.md5($site_id.'_'.$lastId).'.html'
            ]
        );
        $mailResult1 = el_mail( /*implode(', ', $str)*/'flobus@mail.ru', 'Новое обращение с сайта ' . $_SERVER['SERVER_NAME'], $letter_body, 'noreply@mosreg.ru', 'html', 'smtp',
            $fileList, $_POST['q_email'], $humanFio);

        //Отправка уведомления о новой очереди пользователю
        $letter_body = el_render('/tmpl/letter/letter2.php',
            ['caption' => 'Уважаемый/ая '.$humanFio.'!',
                'text' => '
                <p>Ваше обращение зарегистрировано под номером: <strong>'.$queueNumber.'</strong></p>
                <p><strong>' . el_date1(date('Y-m-d')) . ' в '.date('H:i:s').'</strong></p>
                <p><strong>ЦЗН:</strong> ' . $czn['full_name'] . ';</p>
                <p><strong>Тема:</strong> ' . $themes[$_POST['message']] . ';</p>
                <p><strong>Ваше сообщение:</strong><br><br><i>'.nl2br(strip_tags($_POST['text'])).'</i></p>',
                'phone' => $site['phones'],
                'cznName' => $site['full_name'],
                'buttonText' => 'Перейти в личный кабинет',
                'buttonUrl' => 'http://' . $_SERVER['SERVER_NAME'] . '/cancelQueue/'.md5($site_id.'_'.$lastId).'.html'
            ]
        );
        $mailResult = el_mail($_POST['q_email'], 'Принято обращение с сайта ' . $_SERVER['SERVER_NAME'], $letter_body, 'noreply@mosreg.ru', 'html', 'smtp');

        if ($mailResult1) {
            echo json_encode(array(
                'result' => true,
                'resultText' => 'Благодарим Вас за обращение! <br>Вашему сообщению присвоен номер <strong>' . $queueNumber . '</strong>'
            ));
        } else {
            echo json_encode(array(
                'result' => false,
                'resultText' => 'К сожалению, произошла ошибка отправки уведомления. Пожалуйста, сообщите нам об этом по телефону.'
            ));
        }
    } else {
        echo json_encode(array(
            'result' => false,
            'resultText' => 'К сожалению, произошла ошибка записи. Пожалуйста, сообщите нам об этом по телефону.'
        ));
    }
} else {
    echo json_encode(array(
        'result' => false,
        'resultText' => '<strong>Обнаружены ошибки:</strong><br>' . implode('<br>', $errStr),
        'errorFields' => $errFields
    ));
}
?>
