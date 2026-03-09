<?php
@session_start();
if (isset($_GET['logout'])) {
	session_unregister('login');
	session_destroy();
	header('Location:/');
}
echo $_SERVER['DOCUMENT_ROOT'];
$requiredUserLevel = array(0);
require_once('../Connections/dbconn.php');;

if($_POST['ajax'] == 1){
	if(intval($_POST['news_id']) > 0){
		echo '1000';
	}
}else {

//el_strongcleanvars1();
//Находим текст для этой страницы
	$serv = $_SERVER['SERVER_NAME'];
	$path = str_replace($serv, '', $_SERVER['REQUEST_URI']);
	$path = substr($path, 0, -1);
	$path = preg_replace('/\/\?.*/', '', str_replace('/index.ph', '', $path));
	if (substr_count($path, '.htm') > 0 || substr_count($path, '.sit') > 0) {
		$pArr = explode('/', $path);
		array_pop($pArr);
		$path = implode('/', $pArr);
	}


	;
	echo $query_dbcontent = "SELECT * FROM content WHERE `path` = '$path'";
	$dbcontent = el_dbselect($query_dbcontent, 0, $dbcontent, 'result', true);
	$row_dbcontent = el_dbfetch($dbcontent);
	$totalRows_dbcontent = mysqli_num_rows($dbcontent);
	$cat = $GLOBALS['cat'] = $row_dbcontent['cat'];
	$catinfo = '';
	$catinfo = el_dbselect("SELECT * FROM cat WHERE id='" . $row_dbcontent['cat'] . "'", 0, $catinfo);
	$row_catinfo = el_dbfetch($catinfo);
	if (strlen($row_catinfo['redirect']) > 0) {
		header('Location: ' . $row_catinfo['redirect']);
	}

//Перенаправляем по клику по баннеру
	if (isset($_GET['adredirect']) && strlen($_GET['adredirect']) > 0) {
		$adurl = el_dbselect("SELECT url, count FROM ad_banners WHERE id='" . intval($_GET['adredirect']) . "'", 0, $adurl, 'row', true);
		el_dbselect("UPDATE ad_banners SET count='" . ($adurl['count'] + 1) . "' WHERE id='" . intval($_GET['adredirect']) . "'", 0, $res); //echo $adurl['url'];
		header("Location: " . $adurl['url']);
	}

//Находим подразделы в этом разделе

	$colname_dbchildmenu = "1";
	$idparent = $row_dbcontent['cat'];
	if (isset($idparent)) {
		$colname_dbchildmenu = (get_magic_quotes_gpc()) ? $idparent : addslashes($idparent);
	}

	;
	$query_dbchildmenu = sprintf("SELECT * FROM cat WHERE parent = %s AND menu='Y' ORDER BY sort ASC", $colname_dbchildmenu);
	$dbchildmenu = el_dbselect($query_dbchildmenu, 0, $dbchildmenu, 'result', true);
	$row_dbchildmenu = el_dbfetch($dbchildmenu);
	$totalRows_dbchildmenu = mysqli_num_rows($dbchildmenu);

//el_strongvarsprocess();
	if (isset($_POST['rating'])) {
		if ($_POST['rating'] == 'Y') {
			$_POST['rating'] = 1;
		} elseif ($_POST['rating'] == 'N') {
			$_POST['rating'] = -1;
		}
		if ($_COOKIE['comment_' . $_GET['id']] != 1) {
			$_POST['rating'] = $_POST['rating'];
			setcookie('comment_' . $_GET['id'], 1, time() + 31104000);
		} else {
			$_POST['rating'] = 0;
		}
	}

	if (isset($_POST['user_enter'])) {
		(!empty($_POST['user'])) ? $user_login = $_POST['user'] : $user_login = $_SESSION['login'];
		;
		$query_login = "SELECT * FROM phpSP_users WHERE user = '" . $user_login . "'";
		$login1 = el_dbselect($query_login, 0, $login1, 'result', true);
		$row_login = el_dbfetch($login1);
		$totalRows_login = mysqli_num_rows($login1);
		$pass = str_replace("$1$", "", crypt(md5($_POST['password']), '$1$'));
		if (($totalRows_login > 0) && (stripslashes($row_login['password']) === $pass)) {
			if ($row_login['userlevel'] > 0) {
				session_unregister("fio");
				$login = $row_login['user'];
				$ulevel = $row_login['userlevel'];
				$fio = $row_login['fio'];
				@session_register("login");
				$_SESSION['login'] = $row_login['user'];
				@session_register("password");
				$_SESSION['password'] = $_POST['password'];
				@session_register("user_id");
				$_SESSION['user_id'] = $row_login['primary_key'];
				@session_register("user_level");
				@session_register("ulevel");
				$_SESSION['user_level'] = $_SESSION['ulevel'] = $row_login['userlevel'];
				@session_register("fio");
				$_SESSION['fio'] = $row_login['fio'];
				@session_register("user_mail");
				$_SESSION['user_mail'] = $row_login['email'];
				@session_register("user_phone");
				$_SESSION['user_phone'] = $row_login['phones'];
				@session_register("post_adress");
				$_SESSION['post_adress'] = $row_login['post_adress'];
				@session_register("dev_adress");
				$_SESSION['dev_adress'] = $row_login['dev_adress'];
				@setcookie('usid', $usid, time() + 14400);
			} else {
				$err = '<font color=red>Учетная запись не активирована!</font>';
			}
		} else {
			$err = '<font color=red>Неверный логин или пароль!</font>';
		}
	}
	$eventAddCompare = 0;
	if (!empty($_POST['smp'])) {
		if (!isset($_SESSION['smp'])) {
			session_register('smp');
		} else {
			if ($_SESSION['cat_id'] != $_POST['cat_id']) {
				$_SESSION['smp'] = $_SESSION['catalogCompare_path'] = array();
				$_SESSION['cat_id'] = '';
			}
		}
		for ($i = 0; $i < count($_POST['smp']); $i++) {
			if ($_SESSION['catalogCompare_path'][intval($_POST['smp'][$i])] != strip_tags($_POST['catalog_path'])) {
				array_push($_SESSION['smp'], intval($_POST['smp'][$i]));
				$_SESSION['catalogCompare_path'][intval($_POST['smp'][$i])] = strip_tags($_POST['catalog_path']);
			}
		}
		$_SESSION['cat_id'] = intval($_POST['cat_id']);
		//header('Location: /katalog/compare');
		$eventAddCompare = 1;
	} else {
		$eventAddCompare = 0;
	}

	if (isset($_POST['delgoodid'])) {
		if ($_POST['delgoodid'] == 'all') {
			$_SESSION['smp'] = $_SESSION['catalogCompare_path'] = array();
			$_SESSION['cat_id'] = '';
		} else {
			$pos = array_search($_POST['delgoodid'], $_SESSION['smp']);
			array_splice($_SESSION['smp'], $pos, 1);
			$_SESSION['catalogCompare_path'][$_POST['delgoodid']] = '';
		}
	}


	if (strlen($row_dbcontent['view']) > 0 && substr_count($row_dbcontent['view'], 0) == 0) {
		if (isset($_SESSION['login']) && @substr_count($row_dbcontent['view'], $_SESSION['ulevel']) > 0) {
			$row_dbcontent['caption'] = $row_dbcontent['caption'];
			$row_dbcontent['text'] = $row_dbcontent['text'];
			$row_dbcontent['kod'] = $row_dbcontent['kod'];
			$totalRows_dbchildmenu = $totalRows_dbchildmenu;
		} else {
			$row_dbcontent['caption'] = 'Требуется авторизация';
			$row_dbcontent['text'] = '<center>Пожалуйста, авторизуйтесь.<br>
    <table width="200" border="0" align="center" cellpadding="5" cellspacing="0">
  <form name="user_valid" method="post" >
    <tr>
      <td width="8%">логин:</td>
      <td width="92%"><input type="text" name="user" value="" /></td>
    </tr>
    <tr>
      <td>пароль:<br /><br></td>
      <td><input type="password" name="password" /></td>
    </tr>
    <tr>
      <td><input name="user_enter" type="hidden" id="user_enter" value="1"></td>
      <td><input type="Submit" name="Submit" class="btn btn-primary btn-large" value="Вход" /><br><br>
        <!--small><a href="/remember/">забыли пароль?</a></small><br />
        <a href="/registration/">регистрация</a--></td>
    </tr>
    </form>
  </table></center>';
			$row_dbcontent['kod'] = '';
			$totalRows_dbchildmenu = 0;
		}
		if (isset($_SESSION['login']) && @substr_count($row_dbcontent['view'], $_SESSION['ulevel']) == 0 && isset($_POST['user_enter'])) {
			$row_dbcontent['caption'] = 'Закрытый раздел';
			$row_dbcontent['text'] = 'Извините, у Вас недостаточно прав для просмотра этого раздела.';
			$row_dbcontent['kod'] = '';
			$totalRows_dbchildmenu = 0;
		} else {
			$row_dbcontent['caption'] = $row_dbcontent['caption'];
			$row_dbcontent['text'] = $row_dbcontent['text'];
			$row_dbcontent['kod'] = $row_dbcontent['kod'];
			$totalRows_dbchildmenu = $totalRows_dbchildmenu;
		}
	}

	$flag_punkt = 0;
	if (!is_array($_SESSION['goods'])) {
		$_SESSION['goods'] = array();
		$_SESSION['catalogs'] = array();
		$_SESSION['counts'] = array();
		$_SESSION['pathes'] = array();
	}
	if (isset($_POST['good_id'])) {
		if (!in_array($path . '/?id=' . $_POST['good_id'], $_SESSION['pathes'])) {
			array_push($_SESSION['goods'], el_digitvars($_POST['good_id']));
			array_push($_SESSION['catalogs'], el_wordvars($_POST['cat_id']));
			array_push($_SESSION['counts'], (el_digitvars($_POST['count']) == "") ? 1 : el_digitvars($_POST['count']));
			array_push($_SESSION['pathes'], $path . '/?id=' . $_POST['good_id']);
			$flag_punkt = 1;
		} else {
			$flag_punkt = 2;
		}
	}
	if (isset($_POST['good_del'])) {
		array_splice($_SESSION['goods'], $_POST['good_del'], 1);
		array_splice($_SESSION['catalogs'], $_POST['good_del'], 1);
		array_splice($_SESSION['counts'], $_POST['good_del'], 1);
		array_splice($_SESSION['pathes'], $_POST['good_del'], 1);
	}
	if (isset($_GET['clearcache'])) {
		el_clearcache();
		el_genSiteMap();
	}

	if (isset($_POST['send']) && $_POST['send'] == '1' && isset($_POST['Submit'])) {
		if ($_POST['securityCode'] != $_SESSION['securityCode']) {
			$suc = 0;
			$out_msg = '<h4 style="color:red">Введен неверный защитный код!</h4>';
		} else {
			include_once $_SERVER['DOCUMENT_ROOT'] . '/Connections/site_props.php';
			$mail_title = stripslashes($site_property['mail_title' . $cat]);
			$sendto = $site_property['sendto' . $cat];
			define('MYSELF', '1');
			$method = $data = $_POST;
			$sucs_mess = stripslashes($site_property['sucs_mess' . $cat]);
			include $_SERVER['DOCUMENT_ROOT'] . '/modules/form_parser.php';
		}
	}
	if ($row_dbcontent['template'] == '') {
		$row_dbcontent['template'] = 'content.php';
		$row_dbcontent['caption'] = '404. Неверный адрес страницы.';
		$row_dbcontent['text'] = 'Вы попали сюда по неверной ссылке. Пожалуйста, начните с <a href="/">главной страницы</a>.';
	}
//if(el_readcache($row_dbcontent['cat'])==1) exit;
//ob_start();
//include $_SERVER['DOCUMENT_ROOT']."/tmpl/page/".$row_dbcontent['template'];
//el_writecache($row_dbcontent['cat'], ob_get_contents());
//ob_end_flush();

	include $_SERVER['DOCUMENT_ROOT'] . "/tmpl/page/" . $row_dbcontent['template'];
}
?>