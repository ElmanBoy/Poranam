<?php
session_start();
//error_reporting(E_ALL);

include $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';
include $_SERVER['DOCUMENT_ROOT'] . '/Connections/site_props.php';

if (el_checkAjax() && intval($_POST['user_id']) > 0) {
    $cat = 6;

    $err = 0;
    $errStr = array();
    $message = '';
    $result = false;
    $errorFields = array();

    if (isset($_POST["ajax"]) && $_POST["ajax"] == "1") {

        $oldData = el_dbselect("SELECT * FROM catalog_users_data WHERE id = '" . intval($_POST['user_id']) . "'",
            0, $res, 'row', true);

        $domain = explode("@", $_POST['email']);
        if (function_exists('checkdnsrr') && checkdnsrr($domain[1], "MX") == false) {
            $errStr[] = "Введен неверный адрес Email!<br>";
            $errorFields[] = 'email';
            $err++;
        }
        $_POST['fio'] = addslashes(strip_tags(trim($_POST['surname'].' '.$_POST['firstname'].' '.$_POST['secondname'])));
        $_POST['login'] = addslashes(strip_tags($_POST['login']));
        $_POST['email'] = addslashes(strip_tags($_POST['email']));
        $_POST['phones'] = addslashes(strip_tags($_POST['phones']));
        $login = el_dbselect("SELECT * FROM catalog_users_data WHERE field2 = '" . $_POST['email'] . "'", 0, $res);
        $row_login = el_dbfetch($login);
        if ($oldData['field2'] != $_POST['email'] && el_dbnumrows($login) > 0) {
            $errStr[] = "Пользователь с таким E-mail уже есть!<br>Попробуйте ввести другой.<br>";
            $flag = 1;
            $err++;
        }
        $login = el_dbselect("SELECT * FROM catalog_users_data WHERE field15 = '" . $_POST['login'] . "'", 0, $res);
        $row_login = el_dbfetch($login);
        if ($oldData['field15'] != $_POST['login'] && el_dbnumrows($login) > 0) {
            $errStr[] = "Пользователь с таким логином уже есть!<br>Попробуйте ввести другой.<br>";
            $flag = 1;
            $err++;
        }

        if($oldData['field3'] != $_POST['password'] && !empty($_POST['password'])) {
            if (strlen(trim($_POST['password'])) < 6) {
                $errStr[] = "Пароль должен состоять из 6 и более символов<br>";
                $flag = 1;
                $err++;
            } else {
                $pass = str_replace("$1$", "", crypt(md5($_POST['password']), '$1$'));
            }
        }else{
            $pass = $oldData['field3'];
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
        }


        if ($err == 0) {

            $updateSQL = sprintf("UPDATE catalog_users_data SET field1 = %s, field2 = %s, field3 = %s, field5 = %s,  
        field7 = %s, field8 = %s, field9 = %s, field10 = %s, field11 = %s, field12 = %s, field13 = %s, field15 = %s, field16=%s, field26=%s WHERE id = %s",
                GetSQLValueString($_POST['second_name'] . ' ' . $_POST['first_name'] . ' ' . $_POST['third_name'], "text"),
                GetSQLValueString($_POST['email'], "text"),
                GetSQLValueString($pass, "text"),
                GetSQLValueString($_POST['phones'], "text"),
                GetSQLValueString($_POST['profession'], "int"),
                GetSQLValueString($_POST['region'], "int"),
                GetSQLValueString($_POST['district'], "int"),
                GetSQLValueString($_POST['city'], "text"),
                GetSQLValueString($_POST['post_index'], "text"),
                GetSQLValueString($_POST['street'], "text"),
                GetSQLValueString($_POST['build_number'], "text"),
                GetSQLValueString($_POST['login'], "text"),
                GetSQLValueString($_POST['group'], 'int'),
                GetSQLValueString((is_array($_POST['theme']) ? implode(',', $_POST['theme']) : $_POST['theme']), 'text'),
                GetSQLValueString($_POST['user_id'], "int")
            );

            $Result1 = el_dbselect($updateSQL, 0, $res, 'result', true);

            $result = true;
            $message = 'Данные пользователя успешно сохранены.<script>setTimeout(function(){document.location.reload()}, 2000)</script>';
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
}else{
    echo json_encode(array(
        'container' => 'message_login',
        'result' => false,
        'resultText' => 'Пожалуйста, авторизуйтесь',
        'errorFields' => []));
}
?>
