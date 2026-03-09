<?php
session_start();
//error_reporting(E_ALL);

include $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';
include $_SERVER['DOCUMENT_ROOT'] . '/Connections/site_props.php';

if (el_checkAjax()) {
    $cat = 6;
    /*if (isset($_GET['recip']) && isset($_GET['activate']) && $_GET['activate'] == 'y') {//Активация учетной записи
        $flag = 0;
        $recip_id = substr_replace($_GET['recip'], '', 0, 8);
        $recip_id = intval($recip_id);
        $enter = el_dbselect("SELECT * FROM catalog_users_data WHERE id=" . $recip_id, 0, $enter, 'result', true);
        $row_enter = el_dbfetch($enter);
        if (mysqli_num_rows($enter) > 0) {
            if ($row_enter['active'] != '0') {
                echo "<span style='color:red'><h4>Ваша учетная запись уже активирована.</h4>Возможно, Вы перешли по этой ссылке повторно.</span>";
                $flag = 0;
            } else {

                $updateSQL = sprintf("UPDATE catalog_users_data SET active=%s WHERE id=%s",
                    GetSQLValueString(1, "int"),
                    GetSQLValueString($recip_id, "int"));

                if (el_dbselect($updateSQL, 0, $res, 'result', true) == false) {
                    echo "<span style='color:red'><h4>Не удается активировать учетную запись.</h4>Возможно, введен неверный адрес страницы.</span>";
                } else {
                    echo "<span style='color:green'><h4>Спасибо!</h4>Ваша учетная запись успешно активирована.</span><br><br>";

                    $login = $row_enter['field2'];
                    $fio = $row_enter['field1'];
                    $_SESSION['login'] = $row_enter['field2'];
                    $_SESSION['fio'] = $row_enter['field1'];
                    $_SESSION['userlevel'] = 1;
                    @setcookie('usid', $usid, time() + 14400, '/', '');
                    $flag = 0;
                }
            }
        }
    }*/


    $err = 0;
    $errStr = array();
    $message = '';
    $result = false;
    $errorFields = array();
    $group = 0;

    if (isset($_POST["ajax"]) && $_POST["ajax"] == "1") {

        if (empty($_POST['password'])) {
            $errStr[] = "Пожалуйста, укажите пароль!<br>";
            $errorFields[] = 'password';
            $err++;
        }
        $domain = explode("@", $_POST['email']);
        if (function_exists('checkdnsrr') && checkdnsrr($domain[1], "MX") == false) {
            $errStr[] = "Введен неверный адрес Email!<br>";
            $errorFields[] = 'email';
            $err++;
        }
        $_POST['fio'] = addslashes(strip_tags(trim($_POST['fio'])));
        $_POST['user'] = addslashes(strip_tags($_POST['user']));
        $_POST['email'] = addslashes(strip_tags($_POST['email']));
        $_POST['phones'] = addslashes(strip_tags($_POST['phones']));
        $login = el_dbselect("SELECT * FROM catalog_users_data WHERE field2 = '" . $_POST['email'] . "'", 0, $res);
        $row_login = el_dbfetch($login);
        if (el_dbnumrows($login) > 0) {
            $errStr[] = "Пользователь с таким E-mail уже есть!<br>Попробуйте ввести другой.<br>";
            $flag = 1;
            $err++;
        }
        $login = el_dbselect("SELECT * FROM catalog_users_data WHERE field15 = '" . $_POST['email'] . "'", 0, $res);
        $row_login = el_dbfetch($login);
        if (el_dbnumrows($login) > 0) {
            $errStr[] = "Пользователь с таким логином уже есть!<br>Попробуйте ввести другой.<br>";
            $flag = 1;
            $err++;
        }

        if (strlen(trim($_POST['password'])) < 6) {
            $errStr[] = "Пароль должен состоять из 6 и более символов<br>";
            $flag = 1;
            $err++;
        } else {
            $pass = str_replace("$1$", "", crypt(md5($_POST['password']), '$1$'));
        }

        /*if(strlen(trim($_POST['second_name'])) == 0){
            $err++;
            $errStr[] = 'Укажите фамилию';
            $errorFields[] = 'second_name';
        }
        if(strlen(trim($_POST['name'])) == 0){
            $err++;
            $errStr[] = 'Укажите имя';
            $errorFields[] = 'name';
        }*/
        if(strlen(trim($_POST['email'])) == 0){
            $err++;
            $errStr[] = 'Укажите email';
            $errorFields[] = 'email';
        }
        if(strlen(trim($_POST['phones'])) == 0){
            $err++;
            $errStr[] = 'Укажите телефон';
            $errorFields[] = 'phones';
        }
        if(strlen(trim($_POST['profession'])) == 0){
            $err++;
            $errStr[] = 'Укажите профессию';
            $errorFields[] = 'profession';
        }
        if(strlen(trim($_POST['region'])) == 0){
            $err++;
            $errStr[] = 'Укажите субъект РФ';
            $errorFields[] = 'region';
        }
        if(strlen(trim($_POST['district'])) == 0){
            $err++;
            $errStr[] = 'Укажите регион';
            $errorFields[] = 'district';
        }
        if(strlen(trim($_POST['city'])) == 0){
            $err++;
            $errStr[] = 'Укажите город';
            $errorFields[] = 'city';
        }
        if(strlen(trim($_POST['post_index'])) == 0) {
            $err++;
            $errStr[] = 'Укажите индекс';
            $errorFields[] = 'post_index';
        }else{
            //Ищем или создаем группу
            $ind = el_dbselect("SELECT * FROM catalog_groups_data WHERE field1 = '".trim($_POST['post_index'])."'",
            0, $ind, 'row', true);
            //Если группа уже есть
            if(intval($ind['id']) > 0){
                //Смотрим численность группы. Если больше 50-ти или группы еще нет, создадим новую в этом же индексе
                $ucount = el_dbselect("SELECT COUNT(id) AS count FROM catalog_users_data 
                WHERE field16 = '".intval($ind['id'])."'", 0, $ucount, 'row', true);
                if($ucount['count'] >= 50 || $ucount['count'] == 0){
                    $group = el_createUserGroup($_POST['region'], $_POST['district'], $_POST['city'], $_POST['post_index'], $ind['field2'] + 1);
                }else{
                    $group = $ind['id'];
                }

            }else{
                //Иначе создаем новую группу
                $group = el_createUserGroup($_POST['region'], $_POST['district'], $_POST['city'], $_POST['post_index']);
            }
        }
        /*if(strlen(trim($_POST['street'])) == 0){
            $err++;
            $errStr[] = 'Укажите улицу';
            $errorFields[] = 'street';
        }
        if(strlen(trim($_POST['build_number'])) == 0){
            $err++;
            $errStr[] = 'Укажите дом';
            $errorFields[] = 'build_number';
        }*/

        if ($err == 0) {
            $_POST['fio'] = $_POST['second_name'] . ' ' . $_POST['name'] . ' ' . $_POST['third_name'];

            $u = el_dbselect("SELECT user_id FROM catalog_users_data WHERE field11 = '".$_POST['post_index']."' ORDER BY id DESC", 1, $u, 'row');
            $user_id = $_POST['post_index'] . '-1';
            if(strlen($u['user_id']) > 6){
                $uArr = explode('-', $u['user_id']);
                $user_id = intval($uArr[1]) + 1;
            }

            $insertSQL = sprintf("INSERT INTO catalog_users_data (user_id, field1, field2, field3, field4, field5, field6, 
        field7, field8, field9, field10, field11, field12, field13, field14, field15, field16, field17, field26, active, cat, site_id) 
		VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                GetSQLValueString($user_id, "text"),
                GetSQLValueString($_POST['second_name'] . ' ' . $_POST['first_name'] . ' ' . $_POST['third_name'], "text"),
                GetSQLValueString($_POST['email'], "text"),
                GetSQLValueString($pass, "text"),
                GetSQLValueString("", "text"),
                GetSQLValueString($_POST['phones'], "text"),
                GetSQLValueString($_POST['status'], "int"),
                GetSQLValueString($_POST['profession'], "int"),
                GetSQLValueString($_POST['region'], "int"),
                GetSQLValueString($_POST['district'], "int"),
                GetSQLValueString($_POST['city'], "text"),
                GetSQLValueString($_POST['post_index'], "text"),
                GetSQLValueString($_POST['street'], "text"),
                GetSQLValueString($_POST['build_number'], "text"),
                GetSQLValueString($_POST['referrer'], "int"),
                GetSQLValueString($_POST['email'], "text"),
                GetSQLValueString($group, "int"),
                GetSQLValueString(date('Y-m-d'), "text"),
                GetSQLValueString((is_array($_POST['theme']) ? implode(',', $_POST['theme']) : $_POST['theme']), 'text'),
                GetSQLValueString(1, "int"),
                GetSQLValueString(6, "int"),
                GetSQLValueString(1, "int"));

            $Result1 = el_dbselect($insertSQL, 0, $res, 'result', true);

            $row_recip = el_dbselect("select id from catalog_users_data WHERE id=LAST_INSERT_ID()", 0, $recip, 'row');
            $recip_code = el_genpass() . $row_recip['id'];
            el_genSinonim($row_recip['id']);

            //Начисляем баллы пригласившему пользователю
            if(strlen(trim($_POST['referrer'])) > 0){
                $refArr = explode('-', $_POST['referrer']);
                el_earnpoints(6, $refArr[1]);
            }


            $clmail = (strlen($site_property['regModerator' . $cat]) > 0) ? $site_property['regModerator' . $cat] : 'info@' . str_replace('www.', '', $_SERVER['SERVER_NAME']);
            $subs_mail = '<h4>На сайте ' . $_SERVER['SERVER_NAME'] . ' добавлен пользователь!</h4>';
            $subs_mail .= '<p>Данные пользователя:</p>';
            $subs_mail .= 'Имя пользователя: ' . $_POST['email'] . '</br>';
            $subs_mail .= 'Пароль пользователя: ' . $_POST['password'] . '</br>';
            $subs_mail .= 'E-mail: ' . $_POST['email'] . '</br>';
            /*$subs_mail.='Почтовый адрес: '.$_POST['post_adress'].'
            ';
            $subs_mail.='Адрес доставки: '.$_POST['dev_adress'].'
            ';
            //$subs_mail.='Телефоны: '.$_POST['phones'].'
            //';
            //$subs_mail.='Источник информации, из которого узнали о нас: '.$_POST['fsource'].'
            //';*/
            require_once($_SERVER['DOCUMENT_ROOT'] . '/modules/htmlMimeMail.php');
            $mail = new htmlMimeMail();
            $mail->setHtml($subs_mail);
            $mail->setReturnPath('info@' . str_replace('www.', '', $_SERVER['SERVER_NAME']));
            $mail->setFrom('"' . $_SERVER['SERVER_NAME'] . '" <info@' . str_replace('www.', '', $_SERVER['SERVER_NAME']) . '>');
            $mail->setSubject('Регистрация на сайте ' . $_SERVER['SERVER_NAME']);
            $mail->setHeader('X-Mailer', 'HTML Mime mail');
            $mail->setHTMLCharset('utf-8');
            $result = $mail->send(array($clmail));
            //}elseif($_POST['clstatus']=='Покупатель'){
            $subs_div_mail = $subs_mail = '';

            $subs_mail = '<h4>Здравствуйте, ' . $_POST['fio'] . '!</h4>';
            $subs_mail .= '<p>Вы зарегистрированы на сайте ' . $_SERVER['SERVER_NAME'] . '</p>';
            $subs_mail .= 'Ваш логин: ' . $_POST['email'] . '</br>';
            $subs_mail .= 'Ваш пароль: ' . $_POST['password'] . '</br>';
            $subs_mail .= 'Ваш E-mail: ' . $_POST['email'] . '</br>
		<p>&nbsp;</p>
		С уважением,<br>
		администрация сайта ' . $_SERVER['SERVER_NAME'] . '.
		';
            $mail = new htmlMimeMail();
            $mail->setHTML($subs_div_mail . $subs_mail);
            $mail->setReturnPath('info@' . str_replace('www.', '', $_SERVER['SERVER_NAME']));
            $mail->setFrom('"' . $_SERVER['SERVER_NAME'] . '" <info@' . str_replace('www.', '', $_SERVER['SERVER_NAME']) . '>');
            $mail->setSubject('Регистрация на сайте ' . $_SERVER['SERVER_NAME']);
            $mail->setHeader('X-Mailer', 'HTML Mime mail');
            $mail->setHTMLCharset('utf-8');
            $mailResult = $mail->send(array($_POST['email']));
            if ($mailResult) {
                $message = (strlen($site_property['regText' . $cat]) > 0) ?
                    stripslashes($site_property['regText' . $cat]) :
                    '<h4>Пользователь успешно добавлен.</h4>';
                $result = true;
            } else {
                $message = '<h4 style="color:red">Не удалось отправить письмо, обратитесь к администратору сайта.</h4>';
                $result = false;
            }

        } else {
            $message = '<center><h4 style="color:red;">' . $errStr[0] . '</h4></center>';
            $result = false;
        }

        echo json_encode(array(
            'container' => 'message_login',
            'result' => $result,
            'resultText' => $message,
            'errorFields' => $errorFields));
    }
}
?>
