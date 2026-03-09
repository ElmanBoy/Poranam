<?php
@session_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';
if(isset($_SESSION['login'])){
	$err = 0;
	$errStr = array();
	$res = '';
	$old = '';

	switch($_POST['mode']){
		case 'setContact':
			$passQuery = "";
			if(strlen(trim($_POST['password'])) > 0) {
				if (strlen(trim($_POST['password'])) < 6) {
					$err++;
					$errStr[] = 'Придумайте пароль длинной более 5-ти символов.';
				} else {
					$old = el_dbselect("SELECT field3 FROM catalog_users_data 
					WHERE id='".intval($_SESSION['user_id'])."'",
						0, $old, 'row', true);

					$newPass = str_replace("$1$", "", crypt(md5($_POST['password']), '$1$'));

					if($old['field3'] !== $newPass) {
						$passQuery = ", field3 = '$newPass'";
					}
				}
			}
			if($err == 0) {
				$res = el_dbselect("UPDATE catalog_users_data 
				SET field1 = '" . addslashes($_POST['name']) . "',
				field2 = '" . addslashes($_POST['mail']) . "',
				field5 = '" . addslashes($_POST['phone']) . "'$passQuery 
				WHERE id = '".intval($_SESSION['user_id'])."'", 0, $res, 'result', true);
				if($res != false){
					$message = 'Изменения сохранены!';
					if($passQuery != ''){
						$message .= ' Пароль обновлен.';
					}
				}else{
					$err++;
					$errStr[] = 'Не удалось сохранить изменения.';
				}

			}
			break;

		case 'setSubscribe':
			$res = el_dbselect("UPDATE catalog_users_data SET 
			field22 = '".((intval($_POST['tracking']) == 1) ? 'Подписка на изменение статуса заказа' : '')."',
			field23 = '".((intval($_POST['discounts']) == 1) ? 'Подписка на скидки и распродажи' : '')."',
			field24 = '".((intval($_POST['news']) == 1) ? 'Подписка на новые поступления' : '')."' WHERE id = '".intval($_SESSION['user_id'])."'",
				0, $res, 'result', true);
			if($res != false){
				$message = 'Изменения сохранены!';
			}else{
				$err++;
				$errStr[] = 'Не удалось сохранить изменения.';
			}
			break;

		case 'setPayment':
			$res = el_dbselect("UPDATE catalog_users_data SET 
			field10 = '".((intval($_POST['payment']) == 1) ? 'Подписка на изменение статуса заказа' : '')."' WHERE id = '".intval($_SESSION['user_id'])."'",
				0, $res, 'result', true);
			if($res != false){
				$message = 'Изменения сохранены!';
			}else{
				$err++;
				$errStr[] = 'Не удалось сохранить изменения.';
			}
			break;

		case 'setDelivery':
			break;

		case 'setAddress':

			break;

		case 'sendMessage':
			if(strlen(trim($_POST['comment'])) > 0){
				$res = el_dbselect("INSERT INTO catalog_messages_data (active, site_id, cat, field1, field2, field5) VALUES(1, 1, 3389, '".date('Y-m-d H:i:s')."', '".addslashes(strip_tags($_POST['comment']))."', '".intval($_SESSION['user_id'])."')", 0, $res, 'result', true);
				
				$letter_body = el_render('/tmpl/letter/letter1.php',
					['caption' => 'Сообщение с сайта '.$_SERVER['SERVER_NAME'],
						'text' => '<p><strong>Дата:</strong> ' . el_date1(date('Y-m-d')) . ';</p>
	            <p><strong>Дата и время:</strong> ' . date('d.m.Y H:i:s') . ';</p>
	            <p><strong>Ф.И.О.:</strong> ' . $_SESSION['user_fio'] . ';</p>
	            <p><strong>Телефон:</strong> ' . $_SESSION['user_phone'] . ';</p>
	            <p><strong>Email:</strong> ' . $_SESSION['user_mail'] . ';</p>
	            <p><strong>Текст сообщения:</strong><br><br>'.nl2br(strip_tags($_POST['comment'])).'</p>',
						'phone' => $site['phones'],
						'cznName' => $site['full_name'],
						'buttonText' => 'Перейти в личный кабинет',
						'buttonUrl' => 'http://' . $_SERVER['SERVER_NAME'] . '/correspondence/'.md5($site_id.'_'.$lastId).'.html'
					]
				);
				$mailResult1 = el_mail( 'flobus@mail.ru', 'Новое сообщение с сайта ' . $_SERVER['SERVER_NAME'],
					$letter_body, 'noreply@toptoy.ru', 'html', 'smtp',
					'', 'ceo@toptoy.ru');
				if ($mailResult1) {
					$message = 'Ваше обращение отправлено.
					<script>setMainContent("/lk/obratnaya-svyaz/");</script>';
				}else{
					$err++;
					$errStr[] = 'К сожалению, произошла ошибка отправки уведомления. Пожалуйста, сообщите нам об этом по телефону.';
				}

			}else{
				$err++;
				$errStr[] = 'Напишите, пожалуйста, текст сообщения.';
			}
			break;
	}

	if($err == 0) {
		echo json_encode(array(
			'result' => true,
			'resultText' => '<p>'.$message.'</p>'
		));
	}else{
		echo json_encode(array(
			'result' => false,
			'resultText' => implode('<br>', $errStr)
		));
	}
}
?>