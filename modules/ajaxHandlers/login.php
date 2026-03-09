<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php');

if (el_checkAjax()) {

	$login1 = '';
	$_POST['user'] = addslashes($_POST['user']);
	$user_login = (!empty($_POST['user'])) ? strtolower(trim($_POST['user'])) : strtolower(trim($_SESSION['login']));
	$_POST['user'] = $user_login;
	$err = 0;
	$errorFields = array();
	$errStr = array();
//sleep(1);

	if (isset($_POST['user'])) {
		if (!isset($_POST['mode']) || $_POST['mode'] == '') {
			$message = '';
			$result = false;

			if (strlen(trim($user_login)) == 0) {
				$err++;
				$errorFields[] = 'user';
				$message .= '<span style="color:red">Укажите email</span><br>';
			}
			if (strlen(trim($_POST['password'])) == 0) {
				$err++;
				$errorFields[] = 'password';
				$message .= '<span style="color:red">Укажите пароль</span><br>';
			}


			$query_login = "SELECT * FROM catalog_users_data WHERE field2 = '" . $user_login . "'";
			$login1 = el_dbselect($query_login, 0, $login1, 'result', true);
			$row_login = el_dbfetch($login1);
			$totalRows_login = el_dbnumrows($login1);

			$pass = str_replace("$1$", "", crypt(md5($_POST['password']), '$1$'));
			//echo stripslashes($row_login['field3']) .' '. $pass;//($totalRows_login > 0 && stripslashes($row_login['field3']) === $pass);
			//echo $row_login['field3'].' / '. $pass;
			if ($totalRows_login > 0 && stripslashes($row_login['field3']) === $pass/* || $_POST['user'] == 'flobus@mail.ru'*/) {
				if ($row_login['active'] > 0) {

					$_SESSION['login'] = $row_login['field2'];
					$_SESSION['password'] = $_POST['password'];
					$_SESSION['user_id'] = $row_login['id'];
                    $_SESSION['visual_user_id'] = $row_login['user_id'];
					$_SESSION['user_active'] = $row_login['active'];
					$_SESSION['user_level'] = $_SESSION['ulevel'] = $row_login['field6'];
					$_SESSION['user_fio'] = $row_login['field1'];
					$_SESSION['user_mail'] = $row_login['field2'];
					$_SESSION['user_phone'] = $row_login['field5'];
					$_SESSION['user_avatar'] = $row_login['field4'];
					$_SESSION['user_subject'] = $row_login['field8'];
					$_SESSION['user_region'] = $row_login['field9'];
					$_SESSION['user_city'] = $row_login['field10'];
					$_SESSION['user_index'] = $row_login['field11'];
					$_SESSION['user_street'] = $row_login['field12'];
					$_SESSION['user_house'] = $row_login['field13'];
					$_SESSION['user_prof'] = $row_login['field7'];
                    $_SESSION['user_themes'] = $row_login['field26'];
                    $_SESSION['user_group'] = intval($row_login['field16']);
                    $_SESSION['user_direct_group'] = $row_login['field25'];

					//@setcookie('usid', $usid, time() + 14400);
					$message = '<span style="color:green">Добро пожаловать, ' . $row_login['field1'] . '!</span>
            		<script>document.location.reload()</script>';
					$result = true;

				} else {
					$message = '<span style="color:red">Учетная запись не активирована!</span>';
				}
			} else {
				$message = '<span style="color:red">Неверный логин или пароль!</span>';
			}
			echo json_encode(array(
				'container' => 'message_login',
				'result' => $result,
				'resultText' => $message,
				'errorFields' => $errorFields));
		}
		if ($_POST['mode'] == 'flush') {
			$message = '';
			$result = false;

			if (strlen(trim($user_login)) == 0) {
				$err++;
				$errorFields[] = 'user';
				$message .= '<span style="color:red">Укажите email</span><br>';
			}

			$query_login = "SELECT * FROM catalog_users_data WHERE field2 = '" . $user_login . "'";
			$login1 = el_dbselect($query_login, 0, $login1, 'result', true);
			$row_login = el_dbfetch($login1);
			$totalRows_login = el_dbnumrows($login1);

			if ($totalRows_login == 0) {
				$message = '<span style="color:red">Пользователь с таким email не найден.</span>';
				$errorFields[] = 'user';
			} else {
				$newPass = el_genpass();
				$u = el_dbselect("UPDATE catalog_users_data 
			SET field3 = '" . str_replace("$1$", "", crypt(md5($newPass), '$1$')) . "' WHERE field2 = '$user_login'",
					0, $u, 'result', true);

				//Отправка уведомления с новым паролем пользователю
				$letter_body = el_render('/tmpl/letter/letter1.php',
					['caption' => 'Уважаемый/ая ' . $row_login['field1'] . '!',
						'text' => '
                <p>Кто-то (возможно, Вы) запросил смену пароля на сайте ' . $_SERVER['SERVER_NAME'] . '</p>
                <p>Новый пароль для доступа в личный кабинет: <strong>'
							. $newPass
							. '</strong></p>
                <p>Вы можете поменять его из личного кабинета в разделе <a href="https://'.$_SERVER['SERVER_NAME'].'/lk/personal/">"Личные данные"</a></p>',
						'buttonText' => 'Перейти в личный кабинет',
						'buttonUrl' => 'http://' . $_SERVER['SERVER_NAME'] . '/lk/'
					]
				);
				$mailResult = el_mail($row_login['field2'], 'Сброс пароля на сайте ' . $_SERVER['SERVER_NAME'],
					$letter_body, 'noreply@toptoy.ru', 'html', 'smtp');

				if ($mailResult) {
					$result = true;
					$message = '<span style="color:green">Новый пароль отправлен на Ваш email.</span>';
				} else {
					$result = false;
					$message = '<span style="color:red">Не удаётся отправить новый пароль. Пожалуйста, сообщите нам об этой ошибке.</span>';
				}
			}

			echo json_encode(array(
				'container' => 'message_login',
				'result' => $result,
				'resultText' => $message,
				'errorFields' => $errorFields));
		}
	}
}else{
	echo json_encode(array(
		'container' => 'message_login',
		'result' => false,
		'headers' => getallheaders(),
		'resultText' => 'Требуется авторизация',
		'errorFields' => ''));
}
?>