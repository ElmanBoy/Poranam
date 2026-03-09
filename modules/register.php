<?
if (ob_get_length()) ob_clean();
header("Content-type: text/html; charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

include $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';


$err = 0;
$errStr = array();
$_POST = $_REQUEST;
//print_r($_POST);
if (isset($_POST["email"])) {
    if (empty($_POST['fio']) || empty($_POST['email']) || empty($_POST['phone']) || empty($_POST['plot'])) {
        $errStr[] = "Пожалуйста, заполните все поля!<br>";
        $flag = 1;
        $err++;
    }
    list($user_mail, $domain) = split("@", $_POST['email'], 2);
    if (function_exists('checkdnsrr') && checkdnsrr($domain, "MX") == false) {
        $errStr[] = "Введен не верный адрес Email!<br>";
        $flag = 1;
        $err++;
    }
    $_POST['fio'] = strip_tags($_POST['fio']);
    $_POST['phone'] = strip_tags($_POST['phone']);
    $_POST['user'] = strip_tags($_POST['user']);
    $_POST['email'] = strip_tags($_POST['email']);
    $_POST['plot'] = strip_tags($_POST['plot']);
    $birthArr = explode('-', strip_tags($_POST['birtday']));
    $_POST['birthday'] = $birthArr[2] . '-' . $birthArr[1] . '-' . $birthArr[0];

    //$_POST['user'] = $_POST['email'];

    $login = el_dbselect("SELECT * FROM phpSP_users WHERE user = " . GetSQLValueString($_POST['user'], "text"), 0, $res, 'result', true);
    $row_login = el_dbfetch($login);
    if (mysqli_num_rows($login) > 0) {
        $errStr[] = "Пользователь с таким логином уже есть!<br>Попробуйте ввести другой логин.<br>";
        $flag = 1;
        $err++;
    }

    //$_POST['pass'] = el_genpass();

    if (strlen($_POST['password']) < 6) {
        $errStr[] = "Пароль должен состоять из 6 и более символов<br>";
        $flag = 1;
        $err++;
    } else {
        $pass = str_replace("$1$", "", crypt(md5($_POST['pass']), '$1$'));
    }
    if ($err == 0) {
        $insertSQL = sprintf("INSERT INTO phpSP_users (`user`, password, userlevel, fio, email, date_reg, time_reg, phones, fsource, birthday) 
		VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($_POST['user'], "text"),
            GetSQLValueString($pass, "text"),
            GetSQLValueString("0", "text"),
            GetSQLValueString($_POST['fio'], "text"),
            GetSQLValueString($_POST['email'], "text"),
            GetSQLValueString(date("Y-m-d"), "date"),
            GetSQLValueString(date("H:i:s"), "date"),
            GetSQLValueString($_POST['phone'], "text"),
            GetSQLValueString($_POST['plot'], "text"),
            GetSQLValueString($_POST['birthday'], "date"));

        $Result1 = el_dbselect($insertSQL, 0, $res, 'result', true);


        //Добавление пользователя форума
        if ($Result1 != false) {
            define('PHPBB_ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . '/forum/');
            define('IN_PHPBB', true);
            $phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
            $phpEx = substr(strrchr(__FILE__, '.'), 1);
            include($phpbb_root_path . 'common.' . $phpEx);
            require_once($phpbb_root_path . 'config.php');
            require_once($phpbb_root_path . 'includes/functions.php');
            require_once($phpbb_root_path . 'includes/functions_user.php');
            require_once($phpbb_root_path . 'includes/db/dbal.php');
            require_once($phpbb_root_path . 'includes/db/mysql.php');


            $user_row['user_password'] = phpbb_hash($_POST['password']);
            $user_row['username'] = $_POST['user'];
            $user_row['user_email'] = $_POST['email'];
            $user_row['user_birthday'] = $_POST['birthday'];
            $user_row['group_id'] = 2;
            $user_row['user_type'] = 1;

            $custom_fields['pf_phone'] = $_POST['phone'];
            $custom_fields['pf_fullname'] = $_POST['fio'];
            $custom_fields['pf_plot'] = intval($_POST['plot']);
            $custom_fields['pf_show_on_plan'] = 1;

            if (intval(user_add($user_row, $custom_fields)) == 0) {
                $errStr[] = "Ошибка добавления нового пользователя в форум!";
            }
        } else {
            $errStr[] = "Ошибка записи в базу данных<br>";
        }


        if ($Result1 != false) {
            $subs_mail = 'На сайте югорки.рф зарегистрировался пользователь!
';
            $subs_mail .= 'ФИО пользователя: ' . $_POST['fio'] . '
';
            $subs_mail .= 'Телефон: ' . $_POST['phone'] . '
';
            $subs_mail .= 'E-mail: ' . $_POST['email'] . '
';
            $subs_mail .= 'Участок: ' . $_POST['plot'] . '
';
            $subs_mail .= 'Login: ' . $_POST['user'] . '
';
//$subs_mail.='Password: '.$_POST['password'].'
//';

            $subs_mail .= ' 
Для активации или удаления этой учетной записи перейдите по ссылке:
';
            $subs_mail .= 'http://' . $_SERVER['SERVER_NAME'] . '/editor/?right=12
';

            require_once($_SERVER['DOCUMENT_ROOT'] . '/modules/htmlMimeMail.php');
            $mail = new htmlMimeMail();
            $mail->setText($subs_div_mail . $subs_mail);
            $mail->setReturnPath('info@' . str_replace('www.', '', $_SERVER['SERVER_NAME']));
            $mail->setFrom('"ЮГОРКИ.РФ" <info@' . str_replace('www.', '', $_SERVER['SERVER_NAME']) . '>');
            $mail->setBcc('flobus@mail.ru');
            $mail->setSubject(iconv('UTF-8', 'Windows-1251', 'Регистрация на сайте югорки.рф'));
            $mail->setHeader('X-Mailer', 'HTML Mime mail');
            $mail->setTextCharset('UTF-8');
            $result = $mail->send(array('viktor@vavin.ru'));
            if ($result) {
                echo (strlen($site_property['regText' . $cat]) > 0) ? stripslashes($site_property['regText' . $cat]) : '<div style="vertical-align: bottom;line-height: 32px;margin: 15px 20px 0 20px"><h4>Спасибо!<br>Ожидайте активации Вашего аккаунта.<br>С Вами свяжется администратор.</h4>
				<input type="submit" class="btn" onclick="document.location=\'/\'" value="   ОК   ">
				</div>';
            } else {
                echo '<div style="vertical-align: bottom;line-height: 32px;margin: 15px 20px 0 20px">Не удалось отправить письмо, обратитесь к администратору сайта.</div>';
            }
        }

    } else {
        echo '<div style="vertical-align: bottom;line-height: 32px;margin: 15px 20px 0 20px">' . $errStr[0] . '</h4></div>';
    }
}
?>