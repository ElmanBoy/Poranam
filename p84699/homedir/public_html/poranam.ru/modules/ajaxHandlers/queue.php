<?php
@session_start();

include $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';
//Получение настроек модуля
$queue_props = el_getModuleProps('queue');
$stuffArr = $queue_props['stuff'];
//Массив обязательных полей
$reqFields = array(
    'name' => 'Имя',
    'surname' => 'Имя',
    'email' => 'Email',
    'tel' => 'Телефон',
    'status' => 'Статус',
    //'type' => 'Тип обращения',
    'date' => 'Дата',
    'time' => 'Время',
    'reception' => 'Сотрудник'
);
$err = 0;
$errStr = array();
$errFields = array();
$site_id = intval($_SESSION['view_site_id']);
//Тип обращения для номера записи
$queueType = 'Р';
$humanType = 'Работодатель';
$humanFio = addslashes($_POST['surname'] . ' ' . $_POST['name'] . ' ' . $_POST['thirdname']);

switch (intval($_POST['type'])) {
    case 1:
        $queueType = 'П';
        $humanType = 'Соискатель, первичный приём';
        break;
    case 2:
        $queueType = 'В';
        $humanType = 'Соискатель, вторичный приём';
        break;
}
//Проверка обязательных полей
foreach ($reqFields as $key => $val) {
    if (!isset($_POST[$key]) || strlen(trim($_POST[$key])) == 0) {
        $err++;
        $errStr[] = 'Пожалуйста, заполните поле &laquo;' . $val . '&raquo;';
        $errFields[] = $key;
    }
}

if ($err == 0) {

    //Запись в базу данных новой очереди
    $q = el_dbselect("INSERT INTO catalog_queue_data (active, site_id, field1, field2, field3, field4, field5, field6, field7, field8) VALUES 
    (
    1,
    '" . $site_id . "',
    '" . $humanFio . "',
    '" . addslashes($_POST['email']) . "',
    '" . addslashes($_POST['tel']) . "',
    '" . addslashes($_POST['date']) . "',
    '" . addslashes($_POST['time']) . "',
    '" . addslashes($_POST['status']) . "',
    '" . intval($_POST['type']) . "',
    '" . intval($_POST['reception']) . "'
    )", 0, $q, 'result', true);

    if ($q != false) {
        //Номер очереди
        $lastId = mysqli_insert_id($dbconn);
        $queueNumber = $queueType . $lastId;

        //Справочник сотрудников
        $stuffR = el_dbselect("SELECT primary_key, fio, email FROM phpSP_users 
        WHERE primary_key in (" . implode(', ', $stuffArr) . ")", 0, $stuffR, 'result',
            true);
        $str = array();
        if (el_dbnumrows($stuffR) > 0) {
            $rst = el_dbfetch($stuffR);
            do {
                $str[$rst['primary_key']] = array('name' => $rst['fio'], 'email' => $rst['email']);
            } while ($rst = el_dbfetch($stuffR));
        }

        //Получение настроек сайта
        $site = el_dbselect("SELECT * FROM sites WHERE id = ".$site_id, 0, $s, 'row', true);

        //TODO: Осталось добавить отправку смс уведомление польлзователю и оператору уведомлений
        //Отправка уведомления о новой очереди операторам
        $letter_body = el_render('/tmpl/letter/letter2.php',
            ['caption' => 'Уведомляем вас о записи на приём',
                'text' => '<p><strong>Дата:</strong> ' . el_date1($_POST['date']) . ';</p>
            <p><strong>Время:</strong> ' . $_POST['time'] . ';</p>
            <p><strong>Ф.И.О.:</strong> ' . $humanFio . ';</p>
            <p><strong>Статус:</strong> ' . $humanType . ';</p>
            <p><strong>Телефон:</strong> ' . $_POST['tel'] . ';</p>
            <p><strong>Email:</strong> ' . $_POST['email'] . ';</p>',
                'phone' => $site['phones'],
                'cznName' => $site['full_name'],
                'buttonText' => 'Перейти в личный кабинет',
                'buttonUrl' => 'http://' . $_SERVER['SERVER_NAME'] . '/cancelQueue/'.md5($site_id.'_'.$lastId).'.html'
            ]
        );
        $mailResult = el_mail( $str[intval($_POST['reception'])]['email'], 'Новая запись на прием с сайта ' . $_SERVER['SERVER_NAME'], $letter_body, $sender = 'noreply@mosreg.ru', $type = 'html', $mode = '');

        //Отправка уведомления о новой очереди пользователю
        $letter_body = el_render('/tmpl/letter/letter2.php',
            ['caption' => 'Уважаемый/ая '.$humanFio.'!',
                'text' => '
                <p>Вы записаны на приём в '.$site['full_name'].'. Ваш номер в очереди: <strong>'.$queueNumber.'</strong></p> 
                <p><strong>' . el_date1($_POST['date']) . ' в '.$_POST['time'].'</strong>
            Вас будет ожидать специалист <strong>'.$str[intval($_POST['reception'])]['name'].'</strong></p>
            <p>по адресу <strong>'.$site['adresses'].'</strong></p><p></p>
            <p>Контактный телефон: <strong> '.$site['phones'].'</strong></p>',
                'phone' => $site['phones'],
                'cznName' => $site['full_name'],
                'buttonText' => 'Перейти в личный кабинет',
                'buttonUrl' => 'http://' . $_SERVER['SERVER_NAME'] . '/cancelQueue/'.md5($site_id.'_'.$lastId).'.html'
            ]
        );
        $mailResult = el_mail($_POST['email'], 'Запись на прием с сайта ' . $_SERVER['SERVER_NAME'], $letter_body, $sender = 'noreply@mosreg.ru', $type = 'html', $mode = '');

        if ($mailResult) {
            echo json_encode(array(
                'result' => true,
               //'resultText' => 'Дата визита: ' . el_date1($_POST['date']) . '<br />Время: ' . $_POST['time'] . '<br>Сотрудник: ' . $str[intval
                //($_POST['reception'])]['name'] . '<br>Номер записи: <strong>' . $queueNumber . '</strong>' . '<br />Информация о записи отправлена на '.$_POST['email']
				  'resultText' => 'Вы успешно записаны на прием ' . el_date1($_POST['date']) . '<br />Время визита: ' . $_POST['time'] . '<br />Сотрудник: '
                      . $str[intval($_POST['reception'])]['name'] . '<br />Номер записи <strong>' . $queueNumber . '</strong><br />Информация о записи отправлена на '.$_POST['email']
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