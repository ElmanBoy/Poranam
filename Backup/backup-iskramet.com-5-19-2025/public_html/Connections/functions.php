<?

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include $_SERVER['DOCUMENT_ROOT'] . '/Connections/site_props.php';
include $_SERVER['DOCUMENT_ROOT'] . '/Connections/gui.php';
include $_SERVER['DOCUMENT_ROOT'] . '/Connections/db.php';
//include_once  $_SERVER['DOCUMENT_ROOT'].'/editor/modules/votes/poll_cookie.php';

$_SESSION['solt'] = el_genpass();

function el_getvar($varname)
{
	global $$varname;
	$var = $$varname;
	return $var;
}

//Create session
/*function el_start_session()
{
	global $_COOKIE, $_GET, $database_dbconn, $dbconn, $_SESSION;
	if (!session_start()) {
		if (isset($_COOKIE['usid']) && strlen($_COOKIE['usid']) == 32) {
			$usid = $_COOKIE['usid'];
			session_id($usid);
		} elseif (isset($_GET['usid']) && strlen($_GET['usid']) == 32) {
			$usid = $_GET['usid'];
			session_id($usid);
			setcookie('usid', $usid, time() + 14400);
		} else {
			$usid = session_id(el_genpass(32));
			@setcookie('usid', $usid, time() + 14400);
		}
	}
	//Logout
	if (isset($_POST['logout'])) {
		@setcookie('usid', '', time() - 3600);
		if (!@session_destroy() || isset($_SESSION['login'])) {
			el_start_session();
		}
		$usid = "";
	}


	if (isset($_POST['user_enter'])) {
		(!empty($_POST['user'])) ? $user_login = $_POST['user'] : $user_login = $_SESSION['login'];
		;
		$query_login = "SELECT * FROM phpSP_users WHERE user = '" . $user_login . "'";
		$login1 = el_dbselect($query_login, 0, $login1, 'result', true);
		$row_login = el_dbfetch($login1);
		$totalRows_login = el_dbnumrows($login1);
		$pass = str_replace("$1$", "", crypt(md5($_POST['password']), '$1$'));
		if (($totalRows_login > 0) && (stripslashes($row_login['password']) === $pass)) {
			if ($row_login['userlevel'] > 0) {
				$login = $row_login['user'];
				$fio = $row_login['fio'];
				@setcookie('usid', $usid, time() + 14400);
			} else {
				$err = '<font color=red>Учетная запись не активирована!</font>';
			}
		} else {
			$err = '<font color=red>Неверный логин или пароль!</font>';
		}
	}
}*/

//Registering user
function el_reg_work($work_mode, $login, $cat)
{
	global $database_dbconn;
	global $dbconn;;
	$query_user = "SELECT fio FROM phpSP_users WHERE user='$login'";
	$user = el_dbselect($query_user, 0, $user, 'result', true);
	$row_user = el_dbfetch($user);
	$last_author = $row_user['fio'];

	$last_record = "UPDATE cat SET `last_time`=NOW(), `last_author`='$last_author', `last_action`='$work_mode' WHERE id='$cat'";;
	$Result1 = el_dbselect($last_record, 0, $Result1, 'result', true);
}


function el_genpass($numchar = 8)
{
	$str = "abcefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$start = mt_rand(1, (strlen($str) - $numchar));
	$string = str_shuffle($str);
	$password = substr($string, $start, $numchar);
	return ($password);
}

//Unpack packed file
function el_unpack($file)
{
	if (!is_scalar($file)) {
		user_error(gettype($file), E_USER_WARNING);
		return false;
	}
	$out = '';
	foreach (explode("\n", $file) as $line) {
		$c = count($bytes = unpack('c*', substr(trim($line), 1)));
		while ($c % 4) {
			$bytes[++$c] = 0;
		}
		foreach (array_chunk($bytes, 4) as $b) {
			$b0 = $b[0] == 0x60 ? 0 : $b[0] - 0x20;
			$b1 = $b[1] == 0x60 ? 0 : $b[1] - 0x20;
			$b2 = $b[2] == 0x60 ? 0 : $b[2] - 0x20;
			$b3 = $b[3] == 0x60 ? 0 : $b[3] - 0x20;
			$b0 <<= 2;
			$b0 |= ($b1 >> 4) & 0x03;
			$b1 <<= 4;
			$b1 |= ($b2 >> 2) & 0x0F;
			$b2 <<= 6;
			$b2 |= $b3 & 0x3F;
			$out .= pack('c*', $b0, $b1, $b2);
		}
	}
	return rtrim($out, "\0");
}

function checkUpdate()
{
	global $hostname_dbconn, $database_dbconn, $username_dbconn, $password_dbconn;
	$data = array();
	$s = el_dbselect("SELECT * FROM site_props", 0, $s, 'row');
	include $_SERVER['DOCUMENT_ROOT'] . '/editor/e_modules/modules_version.php';
	while (list($key, $val) = each($versions)) {
		$request[] = $key . '=' . $val;
	}
	$data =/*implode('&', $request)."&*/
		"ids=" . session_id() . "&sn=" . $s['serial_number'] . "\r\n\r\n";
	$hostname = "croc-scs-control.ru";
	$path = "/update/index.php";
	$line = "";

	// Устанавливаем соединение, имя которого
	// передано в параметре $hostname
	$fp = fsockopen($hostname, 80, $errno, $errstr, 30);
	// Проверяем успешность установки соединения
	if (!$fp) echo "!!!$errstr ($errno)<br />\n";
	else {
		// Формируем HTTP-заголовки для передачи
		// его серверу
		// Подделываем пользовательский агент, маскируясь
		// под пользователя WindowsXP
		$headers = "POST $path HTTP/1.1\r\n";
		$headers .= "Host: $hostname\r\n";
		$headers .= "Content-type: application/x-www-form-urlencoded\r\n";
		$headers .= "Content-Length: " . strlen($data) . "\r\n\r\n";
		//$headers .= "User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1\r\n";
		$headers .= "Connection: Close\r\n\r\n";
		// Отправляем HTTP-запрос серверу
		fwrite($fp, $headers . $data); //.$data
		// Получаем ответ
		while (!feof($fp)) {
			$line .= fgets($fp, 1024);
		}
		fclose($fp);
	}
	echo $line . strlen($data);
}

function create_cat3($getvar)
{
	global $_POST;
	global $database_dbconn;
	global $dbconn;
	/*;
	$query_sql = "SELECT sql_select FROM site_props";
	$sql = el_dbselect($query_sql, 0, $sql, 'result', true);
	$row_sql = el_dbfetch($sql);
	echo (el_unpack($row_sql['sql_select']));
	eval(el_unpack($line));
	if(substr_count($_POST['kod'], 'catalog')>0){
		$catEx=el_dbselect("SELECT id FROM catalogs WHERE catalog_id='".str_replace('catalog', '', $_POST['kod'])."'", 0, $catEx, 'row');
		if(strlen($catEx['id'])>0){
			el_dbselect("UPDATE catalogs SET cat='".$_POST['id']."' WHERE id=".$catEx['id'], 0, $res);
		}
	}*/
	$foldexist = '';
	if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
		$parid = $_POST['parent'];
		if (function_exists('el_translit')) {
			$_POST['path'] = el_translit($_POST['path']);
		};
		$parentfolder = mysqli_query($dbconn, "select * from cat where id='$parid'");
		$parentfold = el_dbfetch($parentfolder);
		if ($parentfold['path']) {
			$parentf = $parentfold['path'];
		} else {
			$parentf = "";
		}
		$rootfolder = $_SERVER['DOCUMENT_ROOT'];
		if (file_exists($rootfolder . $parentf . "/" . $_POST['path'])) {
			echo '<center><h5 style="color:red">Папка с таким названием уже существует. Выберите другое название.</h5></center>';
			$foldexist = 1;
		} else {
			mkdir($rootfolder . $parentf . "/" . $_POST['path'], 0777);
		}
		$newpath = $parentf . "/" . $_POST['path'];
		mysqli_free_result($parentfolder);
		if (!copy($rootfolder . "/tmpl/index.php", $rootfolder . $newpath . "/index.php")) {
			rmdir($rootfolder . $parentf . "/" . $_POST['path']);
			mkdir($rootfolder . $parentf . "/" . $_POST['path'], 0777);
			copy($rootfolder . "/tmpl/index.php", $rootfolder . $newpath . "/index.php");
		}
		//chmod($rootfolder.$newpath."/index.php", 0755);
	}
	if ($foldexist != 1) {
		if (!$_POST['menu']) {
			$_POST['menu'] = "Y";
		}
		if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
			$insertSQL = sprintf("INSERT INTO cat (parent, name, `path`, menu, ptext, sort) VALUES (%s, %s, %s, %s, %s, %s)",
				GetSQLValueString($_POST['parent'], "int"),
				GetSQLValueString($_POST['name'], "text"),
				GetSQLValueString($newpath, "text"),
				GetSQLValueString($_POST['menu'], "text"),
				GetSQLValueString($_POST['ptext'], "text"),
				GetSQLValueString($_POST['sort'], "int"));;
			$Result1 = el_dbselect($insertSQL, 0, $Result1, 'result', true);
			//Определяем id новой записи
			$parid = $_POST['parent'];;
			$parentfolder = mysqli_query($dbconn, "select * from cat where path='$newpath'");
			$parentfold = el_dbfetch($parentfolder);
			$idnew = $parentfold['id'];
			mysqli_free_result($parentfolder);
		}
		if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
			$insertSQL = sprintf("INSERT INTO content (cat, `path`, text, caption, title, description, kod, template) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
				GetSQLValueString($idnew, "int"),
				GetSQLValueString($newpath, "text"),
				GetSQLValueString($_POST['contenttext'], "text"),
				GetSQLValueString($_POST['name'], "text"),
				GetSQLValueString($_POST['name'], "text"),
				GetSQLValueString($_POST['text'], "text"),
				GetSQLValueString($_POST['kod'], "text"),
				GetSQLValueString($_POST['template'], "text"));;
			$Result1 = el_dbselect($insertSQL, 0, $Result1, 'result', true);
		}
	}
}

function create_cat($getvar)
{
	global $_POST;
	$foldexist = '';
	$res = '';

	if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
		$parid = $_POST['parent'];
		if (function_exists('el_translit')) {
			$_POST['path'] = el_translit($_POST['path']);
		}
		$parentfolder = el_dbselect("select * from cat where id='$parid'", 0, $parentfolder, 'result', true);
		$parentfold = el_dbfetch($parentfolder);
		if ($parentfold['path']) {
			$parentf = $parentfold['path'];
		} else {
			$parentf = "";
		}
		$newpath = $parentf . "/" . $_POST['path'];
	}
	if ($foldexist != 1) {
		if (!$_POST['menu']) {
			$_POST['menu'] = "Y";
		}
		if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
			$insertSQL = sprintf("INSERT INTO cat (parent, name, `path`, menu, ptext, sort) VALUES (%s, %s, %s, %s, %s, %s)",
				GetSQLValueString($_POST['parent'], "int"),
				GetSQLValueString($_POST['name'], "text"),
				GetSQLValueString($newpath, "text"),
				GetSQLValueString($_POST['menu'], "text"),
				GetSQLValueString($_POST['ptext'], "text"),
				GetSQLValueString($_POST['sort'], "int"));

			el_dbselect($insertSQL, 0, $res, 'result', true);
			//Определяем id новой записи
			$parid = $_POST['parent'];
			$parentfolder = el_dbselect("select * from cat where path='$newpath'", 0, $parentfolder, 'result', true);
			$parentfold = el_dbfetch($parentfolder);
			$idnew = $parentfold['id'];
		}
		if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
			$insertSQL = sprintf("INSERT INTO content (cat, `path`, text, caption, title, description, kod, template) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
				GetSQLValueString($idnew, "int"),
				GetSQLValueString($newpath, "text"),
				GetSQLValueString($_POST['contenttext'], "text"),
				GetSQLValueString($_POST['name'], "text"),
				GetSQLValueString($_POST['name'], "text"),
				GetSQLValueString($_POST['text'], "text"),
				GetSQLValueString($_POST['kod'], "text"),
				GetSQLValueString($_POST['template'], "text"));

			el_dbselect($insertSQL, 0, $res, 'result', true);
		}
	}
}

function el_deleteCat($id)
{
	$res = '';
	$p = el_dbselect("SELECT path, name FROM cat WHERE id='$id'", 0, $p, 'row', true);
	$c = el_dbselect("SELECT id FROM cat WHERE parent='$id'", 0, $p, 'result', true);
	el_dbselect("DELETE FROM cat WHERE id=$id", 0, $res, 'result', true);
	el_dbselect("DELETE FROM content WHERE cat=$id", 0, $res, 'result', true);
	if (el_dbnumrows($c) > 0) {
		$rc = el_dbfetch($c);
		do {
			if (intval($rc['id']) > 0) el_deleteCat($rc['id']);
		} while ($rc = el_dbfetch($c));
	}
	el_genSiteMap();
	el_log('Удален раздел &laquo;' . $p['name'] . '&raquo;', 1);
	el_clearcache('menu');
}

function el_deleteGlobalCat($id)
{
	$res = '';
	$p = el_dbselect("SELECT path, name, cat_id FROM cat WHERE id='$id'", 0, $p, 'row', true);
	$c = el_dbselect("SELECT id FROM cat WHERE parent='$id'", 0, $p, 'result', true);
	el_dbselect("DELETE FROM cat WHERE cat_id=$id", 0, $res, 'result', true);
	el_dbselect("DELETE FROM content WHERE cat IN (SELECT id FROM cat WHERE cat_id = $id)", 0, $res, 'result', true);
	if (el_dbnumrows($c) > 0) {
		$rc = el_dbfetch($c);
		do {
			if (intval($rc['id']) > 0) el_deleteGlobalCat($rc['id']);
		} while ($rc = el_dbfetch($c));
	}
	el_log('Глобально удален раздел &laquo;' . $p['name'] . '&raquo;', 1);
	el_clearcache('menu');
}

// Resize pictures
function el_resize_uploadpicture($simage_name, $simage, $smallW, $prefix)
{

	$bigsize = getimagesize($simage);
	$imH = $bigsize[1];
	$imW = $bigsize[0];
	$prop = $imW / $imH;
	$smallH = $smallW / $prop;
	$filesmall = imagecreatetruecolor($smallW, $smallH);

//preg_match("'^(.*)\.(gif|jpe?g|png)$'i", $simage_name, $ext);
	$imgArr = explode('.', $simage_name);
	$ext = strtolower($imgArr[count($imgArr) - 1]);

	switch (strtolower($ext)) {
		case 'jpg' :
		case 'jpeg':
			$image = imagecreatefromjpeg($simage);
			break;
		case 'gif' :
			$image = imagecreatefromgif($simage);
			break;
		case 'png' :
			$image = imagecreatefrompng($simage);
			break;
		default    :
			echo " <script language=javascript>alert('Неверный формат файла \"" . $simage_name . "\"! Пожалуйста, подберите другую картинку.'); document.location.href='" . $_SERVER['REQUEST_URI'] . "';</script>";
			break;
	}
	imagecopyresampled($filesmall, $image, 0, 0, 0, 0, $smallW, $smallH, $imW, $imH);
	imagejpeg($filesmall, $_SERVER['DOCUMENT_ROOT'] . "/images/" . $prefix . "_" . $simage_name, 100);
	imagedestroy($filesmall);
	$imagefield = "/images/" . $prefix . "_" . $simage_name;
	return $imagefield;

}

//Calculate new size in proportion
//$out[0] - height
//$out[1] - width
$sizeArray = array();
function el_propCalc($sW, $sH, $maxW, $maxH)
{
	global $sizeArray;
	$out = array();
	if ($sH > $maxH) {
		$out[0] = $maxH;
		$out[1] = round($sW / ($sH / $maxH));
	}
	if ($sW > $maxW) {
		$out[0] = round($sH / ($sW / $maxW));
		$out[1] = $maxW;
	}
	if ($out[1] > $maxW || $out[0] > $maxH) {
		el_propCalc($out[1], $out[0], $maxW, $maxH);
	} else {
		$sizeArray = $out;
		return $out;
	}
}

//If file with same name is exist, get new name
function el_newName($dirName, $filename)
{
	$fullpath = "";
	$filenumber = "";
	$ext = "";
	$tempname = $tempname1 = $tempname2 = "";
	$filenumber = array();
	$dataArr = array();
	$dir = dir($dirName);
	$ext = substr(strrchr($filename, "."), 0);
	$tempname = str_replace($ext, "", $filename);
	while ($file = $dir->read()) {
		$ext1 = substr(strrchr($file, "."), 0);
		$tempname1 = str_replace($ext1, "", $file);
		$tempname2 = preg_replace("/\[(\d+)\]/", "", $tempname1);
		if ($file != '.' && $file != '..' && ($tempname1 == $tempname || $tempname2 == $tempname)) {
			preg_match_all("/\[(\d+)\]\./", $file, $number, PREG_PATTERN_ORDER);
			if (count($number[1]) > 0) {
				$filenumber[] = $number[1][count($number[1]) - 1];
			}
		}
	}

	if (file_exists($_SERVER['DOCUMENT_ROOT'] . $dirName . $filename) || count($filenumber) > 0) {
		$filenumber1 = max($filenumber);
		$newname = $tempname . '[' . ($filenumber1 + 1) . ']' . $ext;
	}

	return (strlen($newname) > 0) ? $newname : $filename;
}


// Resize pictures(extendet)
function el_resize_images($image_tmp_name, $image_name, $maxW, $maxH, $prefix, $supportWebp = false)
{
	global $sizeArray;
	$newsize = array();
	$image_name = el_translit($image_name);
	$target_name = $prefix . $image_name;
	if (!is_dir($_SERVER['DOCUMENT_ROOT'] . "/images/" . $_SESSION['site_id'])) {
		mkdir($_SERVER['DOCUMENT_ROOT'] . "/images/" . $_SESSION['site_id'], 0777);
	}
	if (file_exists($image_tmp_name)) {
		$bigsize = getimagesize($image_tmp_name);
	} else {
		$bigsize = getimagesize($_SERVER['DOCUMENT_ROOT'] . '/images/gallery/_' . $image_name);
		$image_tmp_name = $_SERVER['DOCUMENT_ROOT'] . '/images/gallery/_' . $image_name;
	}
	$imH = $bigsize[1];
	$imW = $bigsize[0];
	//preg_match("'^(.*)\.(gif|jpe?g|png)$'i", $image_name, $ext);
	//$ext[2]=strtolower($ext[2]);
	$mimeArr = explode('/', mime_content_type($image_tmp_name));
	$ext = strtolower(end($mimeArr));

    if ($ext != 'jpg' || $ext != 'jpeg' || $ext != 'gif' || $ext != 'png' || $ext != 'webp') {
        $extArr = explode('.', $image_name);
        $ext = strtolower(end($extArr));
    }

	if ($imW > $maxW || $imH > $maxH) {
		el_propCalc($imW, $imH, $maxW, $maxH);
		$smallH = $sizeArray[0];
		$smallW = $sizeArray[1];
		$filesmall = imagecreatetruecolor($smallW, $smallH);
		try {
			switch ($ext) {
				case 'jpg' :
				case 'jpeg':
					$image = imagecreatefromjpeg($image_tmp_name);
					imagecopyresampled($filesmall, $image, 0, 0, 0, 0, $smallW, $smallH, $imW, $imH);
					if ($supportWebp) {
						imagewebp($filesmall, $_SERVER['DOCUMENT_ROOT'] . "/images/" . $_SESSION['site_id'] . '/' . $target_name, 100);
					} else {
						imagejpeg($filesmall, $_SERVER['DOCUMENT_ROOT'] . "/images/" . $_SESSION['site_id'] . '/' . $target_name, 100);
					}
					imagedestroy($filesmall);
					break;
				case 'webp':
					$image = imagecreatefromwebp($image_tmp_name);
					imagecopyresampled($filesmall, $image, 0, 0, 0, 0, $smallW, $smallH, $imW, $imH);
					imagewebp($filesmall, $_SERVER['DOCUMENT_ROOT'] . "/images/" . $_SESSION['site_id'] . '/' . $target_name, 100);
					break;
				case 'gif' :
					$image = imagecreatefromgif($image_tmp_name);
					if ($image != false) {
						$trans_index = imagecolortransparent($image);
						$palletsize = imagecolorstotal($image);
						if ($trans_index >= 0 && $trans_index < $palletsize) {
							$trans_color = imagecolorsforindex($image, $trans_index);
							$trans_indexd = imagecolorallocate($image, $trans_color['red'], $trans_color['green'], $trans_color['blue']);
							imagecolortransparent($image, $trans_indexd);
							imagefill($image, 0, 0, $trans_indexd);
						}
						imagecopyresampled($filesmall, $image, 0, 0, 0, 0, $smallW, $smallH, $imW, $imH);
						if ($supportWebp) {
							imagewebp($filesmall, $_SERVER['DOCUMENT_ROOT'] . "/images/" . $_SESSION['site_id'] . '/' . $target_name, 100);
						} else {
							imagegif($filesmall, $_SERVER['DOCUMENT_ROOT'] . "/images/" . $_SESSION['site_id'] . '/' . $target_name);
						}
						imagedestroy($filesmall);
					} else {
						echo 'Error gif';
					}
					break;
				case 'png' :
					$image = imagecreatefrompng($image_tmp_name);
					$filesmall = imagecreatetruecolor($smallW, $smallH);
					imagealphablending($filesmall, false);
					imagecopyresampled($filesmall, $image, 0, 0, 0, 0, $smallW, $smallH, $imW, $imH);
					imagesavealpha($filesmall, true);
					if ($supportWebp) {
						imagewebp($filesmall, $_SERVER['DOCUMENT_ROOT'] . "/images/" . $_SESSION['site_id'] . '/' . $target_name, 100);
					} else {
						imagepng($filesmall, $_SERVER['DOCUMENT_ROOT'] . "/images/" . $_SESSION['site_id'] . '/' . $target_name);
					}
					imagedestroy($filesmall);

					break;
				default    :
					echo "<script language=javascript>alert('Неверный формат файла \"" . $image_tmp_name . "(mime-type: ".$ext.")\"! Пожалуйста, подберите другую картинку.'); document.location.href='" . $_SERVER['REQUEST_URI'] . "';</script>";
					break;
			}
		} catch (Exception $ex) {
			//Выводим сообщение об исключении.
			echo 'Error: ' . $ex->getMessage();
		}

	} else {
		if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'png' || $ext == 'webp') {
			if (!copy($image_tmp_name, $_SERVER['DOCUMENT_ROOT'] . "/images/" . $_SESSION['site_id'] . '/' . $target_name)) {
				echo 'Не удалось скопировать файл ' . $image_tmp_name;
				return false;
			}
		} else {
			echo " <script language=javascript>alert('Неверный формат файла \"" . $image_tmp_name . "(mime-type: ".$ext.")\"! Пожалуйста, подберите другую картинку.'); document.location.href='" . $_SERVER['REQUEST_URI'] . "';</script>";
		}
	}
	$imagefield = "/images/" . $_SESSION['site_id'] . '/' . $target_name;
	clearstatcache();
	if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imagefield)) {
		chmod($_SERVER['DOCUMENT_ROOT'] . $imagefield, 0755);
		return true;
	} else {
		echo 'Файл ' . $_SERVER['DOCUMENT_ROOT'] . $imagefield . ' не создан.';
		return false;
	}
}

//imagelogo($image, $watermark, imagesx($image), imagesy($image), imagesx($watermark), imagesy($watermark), 'random');
function el_imageLogo($dst_image_path, $src_image_path, $position = 'bottom-left', $supportWebp = false, $src_image = '')
{
	if ($src_image != '') {
		$dst_image_path = $src_image;
	}

	if (!is_file($_SERVER['DOCUMENT_ROOT'] . $src_image_path)) {
		echo 'Файл ' . $_SERVER['DOCUMENT_ROOT'] . $src_image_path . ' не найден.';
		return;
	}
	$dst_pathArr = explode('/', $dst_image_path);
	$dst_name = end($dst_pathArr);

	$dst_image_path1 = $_SERVER['DOCUMENT_ROOT'] . '/images/temporary/' . $dst_name;
	if (file_exists($dst_image_path1)) unlink($dst_image_path1);
	$src_image_path = $_SERVER['DOCUMENT_ROOT'] . $src_image_path;
	$dst_image_path = $_SERVER['DOCUMENT_ROOT'] . $dst_image_path;

	if (!copy($dst_image_path, $dst_image_path1)) {
		echo 'Не удается скопировать изображение ' . $dst_image_path . ' в ' . $dst_image_path1;
		return;
	}

	$src_validImage = $dst_validImage = 1;
	$mimeArr = explode('/', mime_content_type($src_image_path));
	$ext = strtolower(end($mimeArr));
	$mimeArr = explode('/', mime_content_type($dst_image_path));
	$extd = strtolower(end($mimeArr));

	switch ($ext) {
		case 'jpg' :
		case 'jpeg':
			$src_image = imagecreatefromjpeg($src_image_path);
			break;
		case 'gif' :
			$src_image = imagecreatefromgif($src_image_path);
			break;
		case 'png' :
			$src_image = imagecreatefrompng($src_image_path);
			break;
		case 'webp':
			$src_image = imagecreatefromwebp($src_image_path);
			break;
		default    :
			echo "<script language=javascript>alert('Неверный формат файла \"" . $src_image_path . "\"! Пожалуйста, подберите другую картинку.'); document.location.href='" . $_SERVER['REQUEST_URI'] . "';</script>";
			$src_validImage = 0;
			break;
	}
	if ($src_validImage == 1) {
		$src_w = imagesx($src_image);
		$src_h = imagesy($src_image);
	}
	switch ($extd) {
		case 'jpg' :
		case 'jpeg':
			$dst_image = imagecreatefromjpeg($dst_image_path1);
			break;
		case 'gif' :
			$dst_image = imagecreatefromgif($dst_image_path1);
			imagepalettetotruecolor($dst_image);
			break;
		case 'png' :
			$dst_image = imagecreatefrompng($dst_image_path1);
			imagepalettetotruecolor($dst_image);
			break;
		case 'webp':
			$dst_image = imagecreatefromwebp($dst_image_path1);
			break;
		default    :
			echo "<script language=javascript>alert('Неверный формат файла \"" . $dst_image_path . "\". Пожалуйста, подберите другую картинку.'); document.location.href='" . $_SERVER['REQUEST_URI'] . "';</script>";
			$dst_validImage = 0;
			break;
	}
	if ($dst_validImage == 1 && $dst_image != false) {
		$dst_w = imagesx($dst_image);
		$dst_h = imagesy($dst_image);
		if ($extd == 'png') {
			imagealphablending($dst_image, false);
			imagealphablending($src_image, false);
			imagesavealpha($dst_image, true);
			imagesavealpha($src_image, true);
		} else {
			imagealphablending($dst_image, true);
			imagealphablending($src_image, true);
		}
		if ($position == 'random') {
			$position = rand(1, 8);
		}
		switch ($position) {
			case 'top-right':
			case 'right-top':
			case 1:
				imagecopy($dst_image, $src_image, ($dst_w - $src_w), 0, 0, 0, $src_w, $src_h);
				break;
			case 'top-left':
			case 'left-top':
			case 2:
				imagecopy($dst_image, $src_image, 0, 0, 0, 0, $src_w, $src_h);
				break;
			case 'bottom-right':
			case 'right-bottom':
			case 3:
				imagecopy($dst_image, $src_image, ($dst_w - $src_w), ($dst_h - $src_h), 0, 0, $src_w, $src_h);
				break;
			case 'bottom-left':
			case 'left-bottom':
			case 4:
				imagecopy($dst_image, $src_image, 0, ($dst_h - $src_h), 0, 0, $src_w, $src_h);
				break;
			case 'center':
			case 5:
				imagecopy($dst_image, $src_image, (($dst_w / 2) - ($src_w / 2)), (($dst_h / 2) - ($src_h / 2)), 0, 0, $src_w, $src_h);
				break;
			case 'top':
			case 6:
				imagecopy($dst_image, $src_image, (($dst_w / 2) - ($src_w / 2)), 0, 0, 0, $src_w, $src_h);
				break;
			case 'bottom':
			case 7:
				imagecopy($dst_image, $src_image, (($dst_w / 2) - ($src_w / 2)), ($dst_h - $src_h), 0, 0, $src_w, $src_h);
				break;
			case 'left':
			case 8:
				imagecopy($dst_image, $src_image, 0, (($dst_h / 2) - ($src_h / 2)), 0, 0, $src_w, $src_h);
				break;
			case 'right':
			case 9:
				imagecopy($dst_image, $src_image, ($dst_w - $src_w), (($dst_h / 2) - ($src_h / 2)), 0, 0, $src_w, $src_h);
				break;
		}


		if ($supportWebp == false) {
			//try {
			switch ($extd) {
				case 'jpg' :
				case 'jpeg':
					imagejpeg($dst_image, $dst_image_path, 100);
					break;
				case 'webp':
					imagewebp($dst_image, $dst_image_path, 100);
					break;
				case 'gif' :
					imagegif($dst_image, $dst_image_path);
					break;
				case 'png' :
					imagepng($dst_image, $dst_image_path);
					break;
			}
			/*} catch (Exception $ex) {
				//Выводим сообщение об исключении.
				echo 'Error: '.$ex->getMessage();
			}*/
		} else {
			//try {
			imagewebp($dst_image, $dst_image_path, 100);
			/*} //Перехватываем (catch) исключение, если что-то идет не так.
			catch (Exception $ex) {
				//Выводим сообщение об исключении.
				echo 'Error webp: '.$ex->getMessage();
			}*/
		}
		imagedestroy($dst_image);
		unlink($dst_image_path1);
	}
}


####################SECURITY###########################################################################
// Check and clean GET`s vars
function el_cleanvars($var, &$varname)
{
	if (strlen($var) > 0) {
		@preg_match_all("/((\%3D)|(=))[^\n]*((\%27)|(\')|(\-\-)|(\%3B)|(;))/i", $var, $test);
		@preg_match_all("/\w*((\%27)|(\'))((\%6F)|o|(\%4F))((\%72)|r|(\%52))/ix", $var, $test2);
		@preg_match_all("/((\%3C)|<)[^\n]+((\%3E)|>)/i", $var, $test3);
		if ((count($test[0]) > 0) || (count($test2[0]) > 0) || (count($test3[0]) > 0)) {
			$var = "";
		}
		return $_GET[$varname] = $var;
	}
}

function el_varsprocess()
{
	reset($_GET);
	array_walk($_GET, 'el_cleanvars');
}

//el_varsprocess();

function el_strongcleanvars1()
{
	global $_GET;
	while (list($varname, $var) = each($_GET)) {
		if (strlen($var) > 0) {
			$var = preg_replace("/[^a-zA-ZА-Яа-яЁё0-9 -_]|char|insert|delete|update|select|union|or|%|--|\'|/i", "", $var);//\"|\?
			$_GET[$varname] = str_replace('\\', '', $var);
		}
	}
}

function el_strongcleanvars($var, &$varname)
{
	if (strlen($var) > 0) {
		$var = preg_replace("/[^a-zA-ZА-Яа-яЁё0-9 -_]|char|insert|delete|update|select|union|or|%|--|\'|/i", "", $var);//\"|\?
	}
	return $_GET[$varname] = $var;
}

function el_digitvars($var)
{
	if (strlen($var) > 0) {
		preg_match_all("/[^0-9]|char|insert|delete|update|select|union|or|%|--|\'|\"|\?/i", $var, $test);
		if (count($test[0]) > 0) {
			$var = "";
		}
	}
	return $var;
}

function el_wordvars($var)
{
	if (strlen($var) > 0) {
		$var = preg_replace("/[^a-zA-ZА-Яа-яЁё0-9]|char|insert|delete|update|select|union|or|%|--|\'|\"|\?/i", "", $var);
	}
	return $var;
}

function el_cyrpost($var, &$varname)
{
	if (strlen($var) > 0) {
		$var = preg_replace("/[^a-zA-Zа-яА-ЯЁё0-9 -\.[or]_]|char|insert|delete|update|select|union|%|--|\'|/im", "", $var);//\"|\?
	}
	return $_POST[$varname] = $var;
}

function el_strongvarsprocess()
{
	reset($_GET);
	reset($_POST);
	array_walk($_GET, 'el_strongcleanvars');
	if (!defined('NOCLEAN') || NOCLEAN != 'NO') {
		array_walk($_POST, 'el_cyrpost');
	}
}

function noTags()
{
	reset($_GET);
	reset($_POST);
	while (list($key, $var) = each($_POST)) {
		$_POST[$key] = strip_tags($var, '<br>');
	}
	while (list($key, $var) = each($_GET)) {
		$_GET[$key] = strip_tags($var, '<br>');
	}
}

#########################################################################################################

//Create session
/*if(!isset($_COOKIE['usid'])){
session_start();

session_register("usid");
setcookie('usid', $usid, time()+14400, '/', '');
}
$_SESSION['usid']=session_id();
$usid=(isset($_SESSION['usid']))?$_SESSION['usid']:$_COOKIE['usid'];
$user_login=$_SESSION['login'];
*/

// Autorization
if (isset($_POST['user_enter'])) {
	(!empty($_POST['user'])) ? $user_login = $_POST['user'] : $user_login = $_SESSION['login'];;
	$query_login = "SELECT * FROM phpSP_users WHERE user = '$user_login'";
	$login = el_dbselect($query_login, 0, $login, 'result', true);
	$row_login = el_dbfetch($login);
	$totalRows_login = el_dbnumrows($login);
	$pass = str_replace("$1$", "", crypt(md5($_POST['password']), '$1$'));
	if (($totalRows_login > 0) && (stripslashes($row_login['password']) === $pass)) {
		session_unregister("login");
		session_unregister("fio");
		$login = $row_login['user'];
		$fio = $row_login['fio'];
		session_register("login");
		session_register("fio");
		setcookie('usid', $usid, time() + 14400, '/', '');
	}
}

if (isset($_POST['logout'])) {
	@setcookie('usid');
	session_destroy();
	$usid = "";
}

//Registration any events in admin zone
function el_admin_secure()
{
	$requiredUserLevel = array(1, 2);
	include($_SERVER['DOCUMENT_ROOT'] . "/editor/secure/secure.php");
	(isset($submit)) ? $work_mode = "write" : $work_mode = "read";
	el_reg_work($work_mode, $login, $_GET['cat']);
	return $requiredUserLevel;
	return eval(include($_SERVER['DOCUMENT_ROOT'] . "/editor/secure/secure.php"));
}


//Sort news for years
function el_news_years()
{
	global $row_dbcontent;
	global $database_dbconn;
	global $dbconn;

	if ($row_dbcontent['kod'] == "news") {
		;
		$query_sort = "SELECT year FROM news ORDER BY year ASC";
		$sort = el_dbselect($query_sort, 0, $sort, 'result', true);
		$row_sort = el_dbfetch($sort);
		$years = array();
		$n = 0;
		do {
			$allyears = "<a href='?year=" . $row_sort['year'] . "' id=link><div onMouseOver= id='line'  onMouseOut= id='line0' id='line0'>Новости за " . $row_sort['year'] . " год </div></a><br>";
			$n = array_push($years, $allyears);
		} while ($row_sort = el_dbfetch($sort));
		$years = array_unique($years);
		rsort($years);
		for ($i = 0; $i < $n; $i++) {
			echo $years[$i];
		}
	}
}

//Call visual editor
function el_html_editor($html_field)
{
	include $_SERVER['DOCUMENT_ROOT'] . "/editor/e_modules/html_editor.php";
}

$currentTitle = '';
$currentDescription = '';
$currentImage = '';
//Print meta-info
function el_meta($mode = '')
{
	global $database_dbconn;
	global $dbconn;
	global $path, $_GET, $row_dbcontent, $currentTitle, $currentDescription, $currentImage;
	$metaContent = '';
	$title = '';
	$colname_detail = "-1";
	$pn_title = ($_GET['pn'] + 1);
	$host = (($_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1') ? 'http://' : 'https://') . $_SERVER['SERVER_NAME'];

	if (isset($path)) {
		$colname_detail = addslashes($path);
	}
	$query_detail1 = sprintf("SELECT * FROM `content` WHERE path = '%s'", $colname_detail);
	$detail1 = el_dbselect($query_detail1, 0, $detail1, 'result');
	$row_detail1 = el_dbfetch($detail1);
	$totalRows_detail1 = el_dbnumrows($detail1);


	if ((strlen(trim($_GET['path'])) > 0) && strlen($row_dbcontent['kod']) > 0) {
		// Для новостей и статьей
		if (substr_count($row_dbcontent['kod'], 'catalog') > 0) {
			$catalog_id = str_replace("catalog", "", $row_dbcontent['kod']);
			$title_catalog = el_dbselect("SELECT field FROM catalog_prop WHERE title='1' AND catalog_id='$catalog_id'", 0, $title_catalog, 'result');
			$rt = el_dbfetch($title_catalog);
			if (el_dbnumrows($title_catalog) > 0) {
				$titleFields = array();
				do {
					$titleFields[] = 'field' . $rt['field'];
				} while ($rt = el_dbfetch($title_catalog));
				$query_catalog = "SELECT * FROM catalog_" .
					$catalog_id . "_data WHERE path='" . addslashes($_GET['path']) . "'";
				$catalog = el_dbselect($query_catalog, 0, $catalog, 'result', true);//, title, description, keywords
				$row_catalog = el_dbfetch($catalog);

				$metaTitle = '';
				$mtf = array();
				if (strlen($row_catalog['title']) > 0) {
					$metaTitle = $row_catalog['title'];
				} else {
					for ($m = 0; $m < count($titleFields); $m++) {
						$mtf[] = $row_catalog[$titleFields[$m]];
					}
					$metaTitle = implode(' ', $mtf);
				    if(strlen(trim($row_catalog['keywords'])) > 0){
				        $kwords = strip_tags(str_replace('"', '\'', $row_catalog['keywords']));
                    }else{
				        $kwords = $metaTitle;
                    }
				}

				$title = stripslashes(htmlspecialchars(trim($metaTitle)));
				$metaContent .= '<title>' . $title . '</title>
            <meta name="description" property="og:description" content="' . strip_tags(str_replace('"', '\'', $row_catalog['description'])) .'">
            <meta name="keywords" content="' . htmlspecialchars($kwords) . '">
            ';
				$currentTitle = $metaTitle;
				$currentDescription = $row_catalog['description'];
				$currentUrl = $host . $row_dbcontent['path'] . '/' . ((strlen($_GET['path']) > 0) ? $_GET['path'] . '.html' : '');
				$currentImage = $row_catalog['field1'];
				if (substr_count($currentImage, ',') > 0) {
					$imgArr = explode(' , ', $currentImage);
					$currentImage = $imgArr[0];
				}
				$metaContent .= '
            <meta property="og:title" content="' . $title . '" />                
            <meta property="og:image" content="' . $host . $currentImage . '" />
            <meta property="og:type" content="og:product"/>
            <meta property="og:url" content="' . $currentUrl . '" />
            <link rel="canonical" href="' . $currentUrl . '">
            <base href="https://' . $_SERVER['SERVER_NAME'] . '/">';

			}

		}
	} else {
		$site = el_dbselect("SELECT full_name FROM sites WHERE id=" . intval($_SESSION['view_site_id']), 0, $site, 'row', true);
		$row_detail1['title'] = (strlen(trim($row_detail1['title'])) > 0) ? $row_detail1['title'] : $site['full_name'];
		$title = stripslashes(htmlspecialchars($row_detail1['title'])) . stripslashes(htmlspecialchars(strlen
			($pn_title > 1)) ? ' - cтраница номер - ' . $pn_title . '' : '');
		$metaContent .= '<title>' . $title . ' </title>
	  <meta name="description" content="' . strip_tags(str_replace('"', '\'', $row_detail1['description'])) . '">
	  <meta name="keywords" content="' . strip_tags(str_replace('"', '\'', $row_detail1['keywords'])) . '">
	  <meta property="og:type" content="website"/>
	  <meta property="og:title" content="' . $title . '">
	  <meta property="og:url" content="https://' . $_SERVER['SERVER_NAME'] . $path . '"/>
	  <link rel="canonical" href="https://' . $_SERVER['SERVER_NAME'] . $path . '">
	  <base href="https://' . $_SERVER['SERVER_NAME'] . '/">
	  ';
	}
	switch ($mode) {
		case '':
			echo $metaContent;
			break;
		case 'return':
			return $metaContent;
			break;
		case 'getTitle' :
			return $title;
			break;
	}
}

//Remove folder and files
function el_delDir($dirName)
{
	$err = 0;
	if (empty($dirName)) {
		return false;
	}
	if (file_exists($dirName)) {
		$dir = dir($dirName);
		while ($file = $dir->read()) {
			if ($file != '.' && $file != '..') {
				if (is_dir($dirName . '/' . $file)) {
					el_delDir($dirName . '/' . $file);
				} else {
					if (!unlink($dirName . '/' . $file)) {
						echo '<script>alert("Файл \"' . $dirName . '/' . $file . '\" не удалось удалить!")</script>';
						$err++;
					}
				}
			}
		}
		if (!rmdir($dirName . '/' . $file)) {
			echo '<script>alert("Папку \"' . $dirName . '/' . $file . '\" не удалось удалить!")</script>';
			$err++;
		}
	} else {
		echo '<script>alert("Папка \"' . $dirName . '\" не существует.")</script>';
		$err++;
	}
}

function el_userSettings($mode, $user, $key = '', $value = '')
{
	$set = $edit = $ins = $sel = '';
	$users_settings = array();
	if ($mode == 'set') {
		$set = el_dbselect("SELECT * FROM users_settings WHERE `user`='$user' AND `key`='$key'", 0, $set, 'result', true);
		if (el_dbnumrows($set) > 0) {
			$edit = el_dbselect("UPDATE users_settings SET `value`='$value' WHERE `key`='$key' AND `user`='$user'", 0, $edit, 'result', true);
		} else {
			$ins = el_dbselect("INSERT INTO users_settings (`user`, `key`, `value`) 
					VALUES('$user', '$key', '$value')", 0, $ins, 'result', true);
		}
	} else {
		$subSql = ($key != '') ? " AND key='$key'" : '';
		$sel = el_dbselect("SELECT * FROM users_settings WHERE `user`='$user'" . $subSql, 0, $set, 'result', true);
		$rset = el_dbfetch($sel);
		do {
			$users_settings[$rset['key']] = $rset['value'];
		} while ($rset = el_dbfetch($sel));
		return $users_settings;
	}
}

//Write to ini-file
function el_2ini($index, $value)
{
	global $site_property;
	$flag = 0;
	$output = '';
	if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/Connections/site_props.php')) {
		$filen = fopen($_SERVER['DOCUMENT_ROOT'] . '/Connections/site_props.php', 'w');
		$outputn = "<? \$site_property=array(
    'domain'=>'" . $_SERVER['SERVER_NAME'] . "'
    ) ?>";
		if (fwrite($filen, $outputn) === FALSE) {
			el_showalert("error", "Не могу произвести запись в файл настроек.");
		}
		fclose($filen);
		el_2ini($index, $value);
	} else {
		@include $_SERVER['DOCUMENT_ROOT'] . '/Connections/site_props.php';
		while (list($insertindex, $insertvar) = each($site_property)) {
			if ($insertindex == $index) {
				$site_property[$insertindex] = $value;
				$flag = 1;
			}
		}
		if ($flag != 1) {
			$app = "'" . $index . "'=>'" . $value . "'\n";
		} else {
			$app = "";
		}
	}
	reset($site_property);
	while (list($getindex, $getvar) = each($site_property)) {
		$output .= "'" . $getindex . "'=>'" . $getvar . "',\n";
	}
	$output = "<? \$site_property=array(\n" . $output . $app . ") ?>";
	$file = fopen($_SERVER['DOCUMENT_ROOT'] . "/Connections/site_props.php", 'w');
	if (fwrite($file, $output) === FALSE) {
		el_showalert("error", "Не могу произвести запись в файл настроек.");
	}
	fclose($file);
	@include $_SERVER['DOCUMENT_ROOT'] . '/Connections/site_props.php';
}

//Write to ini-file
function el_2modulesVer($index, $value)
{
	global $site_property;
	$flag = 0;
	$output = '';
	$fileName = $_SERVER['DOCUMENT_ROOT'] . '/editor/e_modules/modules_vrsion.php';
	if (!file_exists($fileName)) {
		$filen = fopen($fileName, 'w');
		$outputn = "<? \$site_property=array(
    'domain'=>'" . $_SERVER['SERVER_NAME'] . "'
    ) ?>";
		if (fwrite($filen, $outputn) === FALSE) {
			el_showalert("error", "Не могу произвести запись в файл настроек.");
		}
		fclose($filen);
		el_2ini($index, $value);
	} else {
		@include $fileName;
		while (list($insertindex, $insertvar) = each($site_property)) {
			if ($insertindex == $index) {
				$site_property[$insertindex] = $value;
				$flag = 1;
			}
		}
		if ($flag != 1) {
			$app = "'" . $index . "'=>'" . $value . "'\n";
		} else {
			$app = "";
		}
	}
	reset($site_property);
	while (list($getindex, $getvar) = each($site_property)) {
		$output .= "'" . $getindex . "'=>'" . $getvar . "',\n";
	}
	$output = "<? \$site_property=array(\n" . $output . $app . ") ?>";
	$file = fopen($fileName, 'w');
	if (fwrite($file, $output) === FALSE) {
		el_showalert("error", "Не могу произвести запись в файл настроек.");
	}
	fclose($file);
	@include $fileName;
}

function el_getModuleProps($moduleName)
{
	$site_id = intval($_SESSION['view_site_id']);
	if ($site_id == 0) {
		$site_id = intval($_SESSION['site_id']);
	}
	$mpPath = $_SERVER['DOCUMENT_ROOT'] . "/Connections/modules_props/";
	$globalModulePath = $mpPath . $moduleName . '_props.php';
	$localModulePath = $mpPath . $moduleName . '_props_' . $site_id . '.php';

	if (is_file($localModulePath)) {
		include_once $localModulePath;
	} else {
		if (is_file($globalModulePath)) {
			include_once $globalModulePath;
		} else {
			return false;
		}
	}
	return ${$moduleName . '_property'};
}


//Reading content from cache
function el_readcache($cat)
{
	global $site_property;
	if ($site_property['cache' . $cat] == 'Y') {
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/editor/cache/' . $cat . '.htm')) {
			include($_SERVER['DOCUMENT_ROOT'] . '/editor/cache/' . $cat . '.htm');
			return 1;
		} else {
			return 0;
		}
	} else {
		return 0;
	}
}


//Writing content to cache
function el_writecache($cat, $cachedata)
{
	global $site_property;
	if ($site_property['cache' . $cat] == 'Y') {
		$cf = @fopen($_SERVER['DOCUMENT_ROOT'] . '/editor/cache/' . $cat . '.htm', 'w')
		or die('Не удается запись в кэш.');
		fputs($cf, $cachedata);
		fclose($cf);
		@chmod($_SERVER['DOCUMENT_ROOT'] . '/editor/cache/' . $cat . '.htm', 0777);
	}
}

//Clearing cache
function el_clearcache($subdir = '', $cat = '')
{
	if ($cat == '') {
		$dirName = $_SERVER['DOCUMENT_ROOT'] . '/editor/cache/' . $subdir;
		if (!is_dir($dirName)) {
			mkdir($dirName, 0777);
		}
		$dir = dir($dirName);
		while ($file = $dir->read()) {
			if ($file != '.' && $file != '..') {
				if (is_dir($dirName . '/' . $file)) {
					el_clearcache($file, $cat);
				} else {
					if (file_exists($dirName . '/' . $file)) {
						@unlink($dirName . '/' . $file);
					}
				}
			}
		}
	} else {
		@unlink($_SERVER['DOCUMENT_ROOT'] . '/editor/cache/' . $cat . '.htm');
	}
}

//Translate filesize from 2M format to 2097152
function el_returnbytes($val)
{
	$val = trim($val);
	$last = strtolower($val{strlen($val) - 1});
	switch ($last) {
		case 'g':
			$val *= 1024;
		case 'm':
			$val *= 1024;
		case 'k':
			$val *= 1024;
	}

	return $val;
}

//Glue many files to one file from srecified directory
function el_gluefiles($dirname, $outfile)
{
	$line = '';
	$dir = dir($dirname);
	while ($file = $dir->read()) {
		if ($file != '.' && $file != '..') {
			$fd = fopen($dirname . '/' . $file, 'r');
			while (!feof($fd)) {
				$line .= fgets($fd, 4096);
			}
			//@unlink($dirname.'/'.$file) or die('Файл '.$dirname.'/'.$file.' не удалось удалить из временной папки!');
		}
	}
	if (strlen($line) > 0) {
		$fs = fopen($outfile, 'w') or die('Не удается создать файл ' . $outfile . '! Возможно недостаточно прав для записи в указанную папку.');
		fputs($fs, $line);
		fclose($fs);
	}
}

//Функция перевода русских слов в транслит
function el_translit_url($string, $type = '')
{
	$string = ($type == 'file') ? preg_replace('/\\.(?![^.]*$)/', '_', $string) : $string;
	$r_trans = array(
		"а", "б", "в", "г", "д", "е", "ё", "ж", "з", "и", "й", "к", "л", "м",
		"н", "о", "п", "р", "с", "т", "у", "ф", "х", "ц", "ч", "ш", "щ", "э",
		"ю", "я", "ъ", "ы", "ь", "А", "Б", "В", "Г", "Д", "Е", "Ё", "Ж", "З", "И", "Й", "К", "Л", "М",
		"Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ц", "Ч", "Ш", "Щ", "Э",
		"Ю", "Я", "Ъ", "Ы", "Ь", "", "'", "(", ")", "+", "!", "?", "/", " ", '"', "."
	);
	$e_trans = array(
		"a", "b", "v", "g", "d", "e", "e", "j", "z", "i", "i", "k", "l", "m",
		"n", "o", "p", "r", "s", "t", "u", "f", "h", "c", "ch", "sh", "sch",
		"e", "yu", "ya", "", "i", "", "a", "b", "v", "g", "d", "e", "e", "j", "z", "i", "i", "k", "l", "m",
		"n", "o", "p", "r", "s", "t", "u", "f", "h", "c", "ch", "sh", "sch",
		"e", "yu", "ya", "", "i", "", "", "", "", "", "-", "", "-", "-", "-", "", ""
	);
	$string = str_replace($r_trans, $e_trans, $string);
	return $string;
}


//Функция перевода русских слов в транслит
function el_translit($string, $type = '', $mode = 'no_whitespace')
{
    switch($type){
        case 'file': $string = preg_replace( '/\\.(?![^.]*$)/', '_', $string ); break;
        case 'path':
        case 'url': $string = str_replace(array('?', ',', '.', '"', '@', '&', '«',
            '»', '/', '№', '(', ')'), '', $string); break;
    }
	$r_trans = array(
		"а", "б", "в", "г", "д", "е", "ё", "ж", "з", "и", "й", "к", "л", "м",
		"н", "о", "п", "р", "с", "т", "у", "ф", "х", "ц", "ч", "ш", "щ", "э",
		"ю", "я", "ъ", "ы", "ь", "А", "Б", "В", "Г", "Д", "Е", "Ё", "Ж", "З", "И", "Й", "К", "Л", "М",
		"Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ц", "Ч", "Ш", "Щ", "Э",
		"Ю", "Я", "Ъ", "Ы", "Ь", "(", ")", "'", '"'
	);
	$e_trans = array(
		"a", "b", "v", "g", "d", "e", "e", "j", "z", "i", "i", "k", "l", "m",
		"n", "o", "p", "r", "s", "t", "u", "f", "h", "c", "ch", "sh", "sch",
		"e", "yu", "ya", "", "i", "", "A", "B", "V", "G", "D", "E", "E", "J", "Z", "I", "I", "K", "L", "M",
		"N", "O", "P", "R", "S", "T", "U", "F", "H", "C", "Ch", "Sh", "Sch",
		"E", "Yu", "Ya", "", "I", "", "", "", ""
	);
	if ($mode == 'no_whitespace') {
		$string = str_replace(" ", '-', $string);
	}
	$string = str_replace($r_trans, $e_trans, $string);
	return $string;
}

function alterLang($str)
{
	global $_SESSION;
	return ($_SESSION['user_lang'] == 'en') ? el_translit($str, '', '') : $str;
}


//Функция для парсинга шаблона
function parse_template($row_catalog, $template_row, $files, $bgcolor = '', $url = '')
{
	$fnames = array();
	$link_name = array();
	$fnames = split(", ", $files);
	$template_row = str_replace('"', "'", $template_row);
	$template_row = 'echo "' . stripslashes($template_row);
	$template_row = str_replace("[i]", '$row_catalog[', $template_row);
	$template_row = str_replace("[/i]", "]", $template_row);
	$template_row = str_replace("[a]", '<a href=$path' . $url . '/?id=$row_catalog[id]>', $template_row);
	$template_row = str_replace("[/a]", "</a>", $template_row);
	$template_row = str_replace("[paging]", '".paging($pn, $currentPage, $queryString_catalog, $totalPages_catalog, $maxRows_catalog, $tr)."', $template_row);
	$template_row = str_replace("[search]", '".search_form($catalog_id, $cat)."', $template_row);

	$template_row = str_replace("[bgcolor]", " style='background-color:#" . $bgcolor . "' bgcolor='#" . $bgcolor . "'", $template_row);

	$template_row = str_replace("[videoplayer]", '".el_insertPlayer()."', $template_row);
	$template_row = preg_replace('/\[d (.+)\](.+)\[\/d\]/i', '".el_showLink($1, $2, $row_catalog)."', $template_row);
	$template_row = preg_replace('/\[comments (.+)\]/i', '".el_insertComments($1, $row_catalog)."', $template_row);
	$template_row = preg_replace('/\[img (.+)\]/iU', '".el_showImg($1, $row_catalog)."', $template_row);


	preg_match_all("/\[file\](.*)\[\/file\]/i", $template_row, $link_name);
	preg_match_all("/\[filen\](.*)\[\/filen\]/i", $template_row, $link_name1);
	if (count($fnames) > 1) {

		if (substr_count($template_row, '[file]') > 0) {
			$link_string = "";
			for ($i = 0; $i < count($fnames); $i++) {
				$c = $i + 1;
				if (is_file($_SERVER['DOCUMENT_ROOT'] . '/files/' . $fnames[$i])) {
					($i < count($fnames) - 1) ? $end = '&nbsp; | &nbsp;' : $end = '';
					$link_string .= '<a target=_blank href=/files/' . $fnames[$i] . '>' . $link_name[1][0] . ' №' . $c . '</a>' . $end;
				}
			}
			$template_row = str_replace("[file]", $link_string, $template_row);
			$template_row = str_replace($link_name[1][0] . "[/file]", "", $template_row);
			$link_string = "";
		}

		if (substr_count($template_row, '[filen]') > 0) {
			$link_string = "";
			for ($i = 0; $i < count($fnames); $i++) {
				$c = $i + 1;
				($i < count($fnames) - 1) ? $end = '&nbsp; | &nbsp;' : $end = '';
				$link_string .= '<a target=_blank href=/files/' . $fnames[$i] . '>' . $link_name1[1][0] . ' &laquo;' . $fnames[$i] . '&raquo;</a>' . $end;
			}
			$template_row = str_replace("[filen]", $link_string, $template_row);
			$template_row = str_replace($link_name1[1][0] . "[/filen]", "", $template_row);
			$link_string = "";
		}
	} else {
		if (is_file($_SERVER['DOCUMENT_ROOT'] . '/files/' . $files)) {
			$template_row = str_replace("[file]", '<a target=_blank href=/files/$row_catalog[filename]>', $template_row);
			$template_row = str_replace("[/file]", "</a>", $template_row);
			$template_row = str_replace("[filen]", '<a target=_blank href=/files/$row_catalog[filename]>', $template_row);
			$template_row = str_replace("[/filen]", ' &laquo;$row_catalog[filename]&raquo;</a>', $template_row);
		} else {
			$template_row = str_replace("[file]", '', $template_row);
			$template_row = str_replace("[/file]", '', $template_row);
			$template_row = str_replace("[filen]", '', $template_row);
			$template_row = str_replace("[/filen]", '', $template_row);
			$template_row = str_replace($link_name[1][0], '&nbsp;', $template_row);
			$template_row = str_replace($link_name1[1][0], '&nbsp;', $template_row);
		}
	}
	$template_row = $template_row . '";';
	return $template_row;
}

function el_showLink($field, $text, $row_catalog)
{
	return (strlen($row_catalog[$field]) > 0) ? "<a href='$row_catalog[$field]'>" . $text . '</a>' : '';
}

function el_showImg($field, $row_catalog)
{
	return $out = (is_file($_SERVER['DOCUMENT_ROOT'] . $row_catalog[$field])) ? "$row_catalog[$field]" : "/images/video_empty.jpg";
}


function el_insertComments($field, $row_catalog)
{
	$root = $_SERVER['DOCUMENT_ROOT'];
	if (strlen($row_catalog[$field]) > 0) {
		ob_start();
		include($root . '/modules/comments.php');
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
}

function el_insertPlayer()
{
	global $_GET, $catalog_id;
	$id = intval($_GET['id']);
	return $str = "<div id='flashbanner'>Пожалуйста, установите Flash-плеер.</div><script type=\"text/javascript\">var so = new SWFObject('/player.swf','mpl','500','450','9');so.addParam('allowfullscreen','true');so.addParam('logo','');so.addVariable('skin', 'http://mos-gorsud.ru/stylish_slim.swf');so.addParam('flashvars','file=/playlist.xml.php?id=$id|$catalog_id&autostart=true');so.addParam('stretching', 'fill');so.write('flashbanner');</script>";
}


function el_genWord()
{
	global $_POST;
	//error_reporting(E_ALL);
	$root = $_SERVER['DOCUMENT_ROOT'];
	$court = intval($_POST['court']);
	$summ = intval($_POST['summ']);
	$gp = intval($_POST['gp']);
	$name = strip_tags(trim($_POST['name']));
	$adress = strip_tags(trim($_POST['adress']));
	$sid = $_POST['sid'];
	ob_start();
	include($root . '/print_check.php');
	$contents = ob_get_contents();
	ob_end_clean();
	$rand = el_genpass();
	el_delDir($_SERVER['DOCUMENT_ROOT'] . '/files/downloads/');
	mkdir($_SERVER['DOCUMENT_ROOT'] . '/files/downloads/', 0777);
	$newDir = $_SERVER['DOCUMENT_ROOT'] . '/files/downloads/' . $rand;
	mkdir($newDir, 0777);
	$fp = fopen($newDir . '/gosposhlina.doc', 'w');
	fwrite($fp, $contents);
	fclose($fp);
	return '/files/downloads/' . $rand . '/gosposhlina.doc';
}


function cyr_strtolower($a)
{
	$offset = 32;
	$m = array();
	for ($i = 192; $i < 224; $i++) {
		$m[chr($i)] = chr($i + $offset);
	}
	return strtr($a, $m);
}

function who_online()
{
	$id_session = session_id();
	$ses = el_dbselect("SELECT * FROM session_online WHERE id_session = '$id_session'", 0, $ses);
	if (el_dbnumrows($ses) > 0) {
		$queryNew = "UPDATE session_online SET putdate = NOW(), user = '$_SESSION[user]' WHERE id_session = '$id_session'";
	} else {
		$queryNew = "INSERT INTO session_online VALUES('$id_session', NOW(), '$_SESSION[user]')";
	}
	el_dbselect($queryNew, 0, $res);
	el_dbselect("DELETE FROM session_online WHERE putdate < NOW() -  INTERVAL '20' MINUTE", 0, $res);
	$num = el_dbselect("SELECT id_session FROM session_online", 0, $num);
	return el_dbnumrows($num);
}


function el_wordEnd($number, $gender)
{
	if ($number > 20) {
		$number = substr($number, strlen($number) - 1, 1);
	}
	if ($number == 1) {
		($gender == 'm') ? $out = '' : $out = 'ка';
	} elseif ($number > 1 && $number < 5) {
		($gender == 'm') ? $out = 'а' : $out = 'ки';
	} elseif ($number >= 5) {
		($gender == 'm') ? $out = 'ов' : $out = 'ек';
	} elseif ($number == 0) {
		($gender == 'm') ? $out = 'ов' : $out = 'ек';
	}
	return $out;
}

function el_creditForm()
{
	global $_POST, $_SESSION;
	if (isset($_POST['carsid'])) {
		session_register('carsid');
		$_SESSION['carsid'] = $_POST['carsid'];
		echo "<script>location.replace(\"/kreditovanie/#creditForm\");</script>";
	}


}

function el_add2cart()
{
	global $cat, $row_dbcontent, $_POST, $_SESSION;
	if (strlen($_SESSION['catalog_id']) > 0) {
		$catalog_id = $_SESSION['catalog_id'];
	} else {
		$catalog_id = str_replace("catalog", "", $row_dbcontent['kod']);
	}
	if (isset($_POST['goodid'])) {
		$pq = ($_SESSION['ulevel'] == 4) ? "name='Цена2'" : "type='price'";
		$t = el_dbselect("SELECT field FROM catalog_prop WHERE catalog_id='$catalog_id' AND title=1", 0, $t, 'row');
		$f = el_dbselect("SELECT field FROM catalog_prop WHERE catalog_id='$catalog_id' AND type='small_image'", 0, $f, 'row');
		$p = el_dbselect("SELECT field FROM catalog_prop WHERE catalog_id='$catalog_id' AND $pq", 0, $p, 'row');
		$s = el_dbselect("SELECT cat, goodid, field" . $t['field'] . ", field" . $f['field'] . ", field" . $p['field'] . " FROM catalog_" . $catalog_id . "_data WHERE id='" . $_POST['goodid'] . "'", 0, $s, 'row');
		$c = el_dbselect("SELECT path FROM content WHERE cat=" . $s['cat'], 0, $s, 'row');
		if (!isset($_SESSION['good_id'])) {
			session_register('good_id');
			session_register('goodid');
			session_register('good_price');
			session_register('good_count');
			session_register('good_summ');
			session_register('good_image');
			session_register('good_name');
			session_register('catalogid');
			session_register('catalog_path');
			$_SESSION['good_id'] = array();
			$_SESSION['goodid'] = array();
			$_SESSION['good_price'] = array();
			$_SESSION['good_summ'] = array();
			$_SESSION['good_image'] = array();
			$_SESSION['good_count'] = array();
			$_SESSION['good_name'] = array();
			$_SESSION['catalogid'] = array();
			$_SESSION['catalog_path'] = array();
		}
		if (!in_array($_POST['goodid'], $_SESSION['good_id'])) {
			$_SESSION['good_id'][] = $_POST['goodid'];
			$_SESSION['goodid'][] = $s['goodid'];
			$_SESSION['good_summ'][] = $_SESSION['good_price'][] = $s["field" . $p['field']];
			$_SESSION['good_name'][] = $s["field" . $t['field']];
			$_SESSION['good_image'][] = $s["field" . $f['field']];
			$_SESSION['good_count'][] = 1;
			$_SESSION['catalogid'][] = $catalog_id;
			$_SESSION['catalog_path'][] = $c['path'];
		} else {
			$pos = array_search($_POST['goodid'], $_SESSION['good_id']);
			$_SESSION['good_summ'][$pos] = $_SESSION['good_summ'][$pos] + $s["field" . $p['field']];
			$_SESSION['good_count'][$pos] = $_SESSION['good_count'][$pos] + 1;
		}
		echo '<script language=javascript>showDialog("\"' . htmlspecialchars($s["field" . $t['field']]) . '\" добавлен в корзину!<br>Теперь в корзине ' . array_sum($_SESSION['good_count']) . ' товар' . el_wordEnd(array_sum($_SESSION['good_count']), 'm') . ' на сумму $' . array_sum($_SESSION['good_summ']) . ' .", "alert")</script>';

	}
	//Полная очистка карзины
	if (isset($_POST['action']) && $_POST['action'] == 'del_all') {
		session_unregister('good_id');
		session_unregister('goodid');
		session_unregister('good_price');
		session_unregister('good_summ');
		session_unregister('good_image');
		session_unregister('good_count');
		session_unregister('good_name');
		session_unregister('catalogid');
		session_unregister('catalog_path');
	}

	//Удаление одной позиции
	if (isset($_POST['good_del'])) {
		array_splice($_SESSION['good_id'], $_POST['good_del'], 1);
		array_splice($_SESSION['goodid'], $_POST['good_del'], 1);
		array_splice($_SESSION['good_price'], $_POST['good_del'], 1);
		array_splice($_SESSION['good_summ'], $_POST['good_del'], 1);
		array_splice($_SESSION['good_name'], $_POST['good_del'], 1);
		array_splice($_SESSION['good_count'], $_POST['good_del'], 1);
		array_splice($_SESSION['catalogid'], $_POST['good_del'], 1);
		array_splice($_SESSION['catalog_path'], $_POST['good_del'], 1);
		array_splice($_SESSION['good_image'], $_POST['good_del'], 1);
	}

	//Пересчет карзины
	if (isset($_POST['action']) && $_POST['action'] == 'recalc') {
		for ($i = 0; $i < count($_SESSION['good_count']); $i++) {
			$reCount = (intval($_POST['count' . $i]) <= 0) ? 1 : intval($_POST['count' . $i]);
			$_SESSION['good_count'][$i] = $reCount;
		}
	}
	el_show_cart();
}

function el_show_cart()
{
	global $_SESSION;
	if (is_array($_SESSION['good_count'])) {
		$c = array_sum($_SESSION['good_count']);
	}
	if ($c > 0 && isset($_SESSION['good_summ'])) {
		echo '<div style="padding:10px"><b>В корзине ' . $c . ' товар' . el_wordEnd($c, 'm') . ' на сумму $' . array_sum($_SESSION['good_summ']) . ' </b><br><br>
		<a href="/katalog/order/" id="addcart"><b>Оформить заказ &raquo;</b></a><?div>';
	}
}

//Логирование действий в Административном разделе
function el_log($text, $level = 3)
{
	global $database_dbconn, $dbconn, $session, $log;
	switch ($level) {
		case 1:
			$priority = 'Высокий';
			$color = 'red';
			break;
		case 2:
			$priority = 'Средний';
			$color = 'yellow';
			break;
		case 3:
			$priority = 'Низкий';
			$color = 'blue';
			break;
	}
	$log->logg('Новая запись', $text, $priority, $color);

}

function el_getPart($path, $template, $hostname = 'www.all.auto.ru', $count = 0)
{
	$line = '';
	$lis = '';
	$fauto = '';
	$out2 = array();
	$fput = '';
	$fput1 = '';
	$s = '';
	$findtext = '';
	$ftxt = '';
	$txt = '';
	$fp = fsockopen($hostname, 80, $errno, $errstr, 30);
	if (!$fp) {
		echo "!!!$errstr ($errno)<br />\n";
		if ($count < 5) {
			el_getPart($path, $template, $hostname, ++$count);
		} else {
			return false;
		}
	} else {
		$headers = "GET $path HTTP/1.1\r\n";
		$headers .= "Host: $hostname\r\n";
//		$headers .= "Referer: http://www.all.auto.ru/list/\r\n";//extsearch/cars/used
		$headers .= "User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1\r\n";
		$headers .= "Connection: Close\r\n\r\n";
		fwrite($fp, $headers);
		while (!feof($fp)) {
			$line .= fgets($fp, 1024);
		}
		fclose($fp);
	}
	$line = str_replace("\n", "", $line);
	preg_match_all($template, $line, $out, PREG_PATTERN_ORDER);
	return str_replace('</a></a>', '</a>', $out[1][0]);
}

function el_convertArray($array, $charsetFrom = 'UTF-8', $charsetTo = 'Windows-1251')
{
	foreach ($array as $key => $val) {
		if (is_array($array[$key])) {
			$array[$key] = el_convertArray($array[$key]);
		} else {
			$array[$key] = iconv($charsetFrom, $charsetTo, $val);
		}
	}
	reset($array);
	return $array;
}

function el_addslashesArray($array)
{
	while (list($key, $val) = each($array)) {
		if (is_array($array[$key])) {
			$array[$key] = el_addslashesArray($array[$key]);
		} else {
			$array[$key] = addslashes($val);
		}
	}
	reset($array);
	return $array;
}

function el_stripslashesArray($array)
{
	while (list($key, $val) = each($array)) {
		if (is_array($array[$key])) {
			$array[$key] = el_stripslashesArray($array[$key]);
		} else {
			$array[$key] = stripslashes($val);
		}
	}
	reset($array);
	return $array;
}


function send_sms($str, $id, $ownerPhone = '', $count = 0)
{
	global $hostname_dbconn, $database_dbconn, $username_dbconn, $password_dbconn;
	$pro = '';
	$prop = '';
	$phoneList = $ownerPhone;
	$data = "Speed=1&Http_username=elman&Http_password=1675894&Message=" . urlencode(str_replace('_', ' ', el_translit($str))) . "&Phone_list=" . $phoneList . "&Speed=1&Http_id=" . $id . "\r\n\r\n";
	$hostname = "www.websms.ru";
	$path = "/http_in5.asp";
	$line = "";

	$fp = @fsockopen($hostname, 80, $errno, $errstr, 30);
	if (!$fp) {
		//echo "!!!$errstr ($errno)<br />\n";
		if ($count < 5) {
			sleep(5);
			send_sms($str, $id, $ownerPhone, ++$count);
		} else {
			return false;
		}
	} else {
		$headers = "POST $path HTTP/1.0\r\n";
		$headers .= "Host: $hostname\r\n";
		$headers .= "Content-type: application/x-www-form-urlencoded\r\n";
		$headers .= "Content-Length: " . strlen($data) . "\r\n\r\n";
		//$headers .= "User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1\r\n";
		$headers .= "Connection: Close\r\n\r\n";
		//file_put_contents('/home/www3net/smsrobot.ru/docs/smsRequest.log', $headers.$data."\n\n", FILE_APPEND);
		fwrite($fp, $headers . $data);
		while (!feof($fp)) {
			$line .= fgets($fp, 1024);
		}
		fclose($fp);
		return $line;
	}
}

function el_xmlSimbols($str)
{
	return str_replace(array('&', "'", '"', '>', '<'), array('&amp;', '&apos;', '&quot;', '&gt;', '&lt;'), $str);
}

function el_genSiteMap()
{
	$q = "SELECT name, path, last_time FROM cat WHERE menu='Y' AND nourl is NULL";
	$db = el_dbselect($q, 0, $db);
	$r_cat = el_dbfetch($db);
	$xml = '<?xml version="1.0" encoding="UTF-8"?>
	<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	';
	$yml_cat = '<categories>' . "\n";
	$c = 0;
	$cat_arr = array();
	do {
		$c++;
		$xml .= '<url>
              <loc>https://' . el_xmlSimbols($_SERVER['SERVER_NAME'] . $r_cat['path']) . '/</loc>
              <lastmod>' . date('Y-m-d') ./*, $db->sf("mdate")).*/
			'</lastmod>
              <changefreq>monthly</changefreq>
           </url>
	    ';
	} while ($r_cat = el_dbfetch($db));
	$yml_cat .= '</categories>' . "\n";
	$yml_prod = $xml_url = array();
	$yml_prod[0] = '<offers>' . "\n";
	$i = 0;

    $gt = el_dbselect("SELECT count(id) AS exist, catalog_id FROM catalogs WHERE name LIKE '%товар%'", 1, $gt, 'row');
    if(intval($gt['exist']) > 0) {
        $goodTable = $gt['catalog_id'];
        //Справочник путей разделов каталога товаров
        $c = el_dbselect("SELECT cat.id AS id, cat.path AS path, catalog_".$goodTable."_data.cat AS cat 
        FROM catalog_".$goodTable."_data, cat 
        WHERE cat.id = catalog_".$goodTable."_data.cat 
        GROUP BY catalog_".$goodTable."_data.cat", 0, $c, 'result', true);
        $rc = el_dbfetch($c);
        $catsPath = array();
        do {
            $catsPath[intval($rc['id'])] = $rc['path'];
        } while ($rc = el_dbfetch($c));

        $q = "SELECT * FROM catalog_goods_data WHERE active=1";
        $db_cat = el_dbselect($q, 0, $db_cat);
        if (@el_dbnumrows($db_cat) > 0) {
            $sf = el_dbfetch($db_cat);
            do {
                $i++;
                if (intval($sf['cat']) > 0 && strlen($catsPath[intval($sf['cat'])]) > 0) {
                    $prod_url = el_xmlSimbols('https://' . $_SERVER['SERVER_NAME'] . $catsPath[intval($sf['cat'])] . $sf['path']) . '.html';
                    $xml_url[] = '<url>
                  <loc>' . $prod_url . '</loc>
                  <lastmod>' . date('Y-m-d')/*, $db->sf("mdate"))*/ . '</lastmod>
                  <changefreq>monthly</changefreq>
               </url>';
                }
            } while ($sf = el_dbfetch($db_cat));
            /*$yml_prod[]='</offers>'."\n";*/
            $xml .= implode("\n", $xml_url) . "\n</urlset>";
            el_genYML();
        }
    }
	$fp = fopen($_SERVER['DOCUMENT_ROOT'] . '/sitemap.xml', 'w');
	fwrite($fp, $xml);
	fclose($fp);
}

function el_genYML()
{
	$cat = '';
	$offer = '';
	$yml = '<?xml version="1.0" encoding="UTF-8"?>
	<yml_catalog date="'.date('Y-m-d H:i').'">
    <shop>
        <name>TOPTOY</name>
        <company>ООО "ТОПТОЙ"</company>
        <url>https://'.$_SERVER['SERVER_NAME'].'</url>
        <currencies>
            <currency id="RUR" rate="1"/>
        </currencies>
        <categories>';
	$cat = el_dbselect("SELECT cat.name AS name, cat.id AS id, cat.parent AS parent, cat.path AS path FROM content, cat WHERE cat.id > 3244 AND content.kod='cataloggoods' AND cat.id = content.cat",
		0, $cat, 'result', true);
	if(el_dbnumrows($cat) > 0){
		$rc = el_dbfetch($cat);
		do{
			$catsPath[intval($rc['id'])] = $rc['path'];
			$yml .= '<category id="'.intval($rc['id']).'"'.((intval($rc['parent']) > 3244) ? ' parentId="'.intval($rc['parent']).'"' : '').'>'.el_xmlSimbols($rc['name']).'</category>'."\n";
		}while($rc = el_dbfetch($cat));
	}

    $yml .=  '</categories>
        <delivery-options>
            <option cost="300" days="1-3"/>
        </delivery-options>
        <offers>';
	$g = el_dbselect("SELECT * FROM catalog_goods_data WHERE active = '1' AND field43 > 0", 0, $g, 'result', true);
	if(el_dbnumrows($g) > 0){
		$rg = el_dbfetch($g);


		//Получаем бренды
		$exc = el_dbselect("SELECT id, field1, field3 FROM `catalog_brands_data`", 0, $exc);
		$allBrands = array();
		$excl = el_dbfetch($exc);
		do{
			$allBrands[$excl['id']] = $excl['field3'];
		}while($excl = el_dbfetch($exc));

		do{
			if (intval($rg['cat']) > 0 && strlen($catsPath[intval($rg['cat'])]) > 0) {
				$prod_url = el_xmlSimbols('https://' . $_SERVER['SERVER_NAME'] . $catsPath[intval($rg['cat'])] . $rg['path']) . '.html';

				$imgArr = explode(' , ', $rg['field3']);
				$img = 'https://' . $_SERVER['SERVER_NAME'] . $imgArr[0];

				$ageParam = '';
				if((strlen(trim($rg['field10'])) > 0)){
					$age = intval($rg['field10']);
					if($age > 0 && $age < 6){
						$ageParam = '<age unit="year">0</age>';
					}elseif ($age >= 6 && $age < 12){
						$ageParam = '<age unit="year">6</age>';
					}else{
						$ageParam = '<age unit="year">12</age>';
					}
				}

				$brand = '';
				if(intval($rg['field17']) > 0){
					if(strlen(trim($allBrands[$rg['field17']])) > 0) {
						$brand = '
					<vendor>' . $allBrands[$rg['field17']] . '</vendor>';
					}
				}

				if(intval($rg['field6']) == 0) $rg['field6'] = 1;

				$yml .= '<offer id="'.$rg['id'].'" available="true">
				    <name>'.el_xmlSimbols($rg['field1']).'</name>'.$brand.'
				    <url>'.$prod_url.'</url>
				    <price>'.$rg['field26'].'</price>
				    '.(($rg['field52'] == 'Скидка') ? '<oldprice>'.round($rg['field26'] - (($rg['field26'] /
									100) * 5)).'</oldprice>
				    <enable_auto_discounts>true</enable_auto_discounts>' : '').'
				    <currencyId>RUR</currencyId>
				    <categoryId>'.$rg['cat'].'</categoryId>
				    <picture>'.$img.'</picture>
				    <delivery>true</delivery>
				    <pickup>true</pickup>
				    <delivery-options>
				        <option cost="300" days="1-3" order-before="18"/>
				    </delivery-options>
				    <pickup-options>
				        <option cost="300" days="1-3"/>                        
				    </pickup-options>
				    <store>false</store>
				    <description>
				        <![CDATA[     
				            <h3>'.$rg['field1'].'</h3>
				            '.$rg['field41'].'
				            '.((strlen(trim($rg['field12'])) > 0) ? '<p>Комплектация: ' . $rg['field12'] . '</p>' : '').'
				    ]]>
				    </description> 
				    <sales_notes>При заказе от 2000 руб. доставка в Москве бесплатна.</sales_notes>
				    <country_of_origin>'.$rg['field16'].'</country_of_origin>
				    '.((strlen(trim($rg['field28'])) > 0) ? '<barcode>'.$rg['field28'].'</barcode>' : '').'
				    '.((strlen(trim($rg['field33'])) > 0) ? '<param name="Упаковка">'.$rg['field33'].'</param>' : '').'
				    '.((strlen(trim($rg['field32'])) > 0) ? '<param name="Материал">'.$rg['field32'].'</param>' : '').'
				    '.((floatval($rg['field9']) > 0) ? '<param name="Вес" unit="кг">'.$rg['field9'].'</param>' :
						'').'
				    '.((intval($rg['field4']) > 0) ? '<param name="Ширина" unit="см">'.$rg['field4'].'</param>' :
						'').'
				    '.((intval($rg['field5']) > 0) ? '<param name="Высота" unit="см">'.$rg['field5'].'</param>' :
						'').'
				    '.((intval($rg['field6']) > 0) ? '<param name="Глубина" unit="см">'.$rg['field6'].'</param>' :
						'').'
				    '.((floatval($rg['field9']) > 0) ? '<weight>'.round(floatval($rg['field9']), 3).'</weight>' :
						'').'
				    '.$ageParam.'
				    '.((floatval($rg['field6']) > 0 && floatval($rg['field4']) > 0 && floatval($rg['field5']) > 0) ? '<dimensions>'.$rg['field6'].'/'.$rg['field4'].'/'.$rg['field5'].'</dimensions>' :
						'').'
				</offer>
				';
			}
		}while($rg = el_dbfetch($g));
	}

    $yml .=  '</offers>
    </shop>
	</yml_catalog>';
	unlink($_SERVER['DOCUMENT_ROOT'].'/goods.yml');
	file_put_contents($_SERVER['DOCUMENT_ROOT'].'/goods.yml', $yml);
}

function el_genSiteMap_aa()
{
	$q = "SELECT name, path, last_time FROM cat WHERE menu='Y' AND nourl is NULL";
	$db = el_dbselect($q, 0, $db);
	$r_cat = el_dbfetch($db);
	$xml = '<?xml version="1.0" encoding="UTF-8"?>
	<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	';
	$yml_cat = '<categories>' . "\n";
	$c = 0;
	$cat_arr = array();
	do {
		$c++;
		$xml .= '
		<url>
		  <loc>http://' . $_SERVER['SERVER_NAME'] . $r_cat['path'] . '/</loc>
		  <lastmod>' . date('Y-m-d') ./*, $db->sf("mdate")).*/
			'</lastmod>
		  <changefreq>monthly</changefreq>
	   </url>
	';
		$cat_arr[$cat_id] = $c;
		//$yml_cat.='<category id="'.$c.'" parentId="0">'.$db->sf("category_name").'</category>
		//';
	} while ($r_cat = el_dbfetch($db));
	$xml .= implode("\n", $xml_url) . '</urlset>';


//	$yml_cat.='</categories>'."\n";
//	$yml_prod=$xml_url=array();
//	$yml_prod[0]='<offers>'."\n";
//	$i=0;
//	$p=el_dbselect("SELECT path FROM content WHERE kod='catalogpub'", 0, $p, 'row');
//	$q="SELECT * FROM catalog_pub_data WHERE active=1";
//	$db_cat=el_dbselect($q, 0, $db_cat);
//	if(@el_dbnumrows($db_cat)>0){
//		$sf=el_dbfetch($db_cat);
//		do{
//			$i++;
//			//$prod_id=$db->sf("product_id");
//			//$cat_id=$db->sf("category_id");
//			$prod_url='http://'.$_SERVER['SERVER_NAME'].$p['path'].'/?id='.$sf['id'];
//			$xml_url[]='
//			<url>
//			  <loc>'.$prod_url.'</loc>
//			  <lastmod>'.date('Y-m-d')/*, $db->sf("mdate"))*/.'</lastmod>
//			  <changefreq>monthly</changefreq>
//		   </url>';
//		}while($sf=el_dbfetch($db_cat));
//		$xml.=implode("\n", $xml_url).'</urlset>';
//	}

	$fp = fopen($_SERVER['DOCUMENT_ROOT'] . '/sitemap.xml', 'w');
	fwrite($fp, $xml);
	fclose($fp);
}

//Вычисление разницы в днях между стартовой и конечной датой
function get_duration($date_from, $date_till)
{
	$date_from = explode('-', $date_from);
	$date_till = explode('-', $date_till);

	$time_from = mktime(0, 0, 0, $date_from[1], $date_from[2], $date_from[0]);
	$time_till = mktime(0, 0, 0, $date_till[1], $date_till[2], $date_till[0]);

	$diff = ($time_till - $time_from) / 60 / 60 / 24;

	return $diff;
}

//Выдает массив дат в интервале между стартовой и конечной датой
function get_durationDates($date_from, $date_till, $mode = '')
{
	$out = array($date_from);

	switch ($mode) {
		case 'week' :
			$option = ' this week';
			break;
		case 'month':
			$option = ' this month';
			break;
		default:
			$option = '';
			break;
	}
	$date_from = explode('-', $date_from);
	$date_till = explode('-', $date_till);

	$time_from = mktime(0, 0, 0, $date_from[1], $date_from[2], $date_from[0]);
	$time_till = mktime(0, 0, 0, $date_till[1], $date_till[2], $date_till[0]);
	$nextDay = $date_from;
	while ($time_from < $time_till) {
		$nextDay = date('Y-m-d', strtotime('+1 day' . $option, $time_from));
		$date_from = explode('-', $nextDay);
		$time_from = mktime(0, 0, 0, $date_from[1], $date_from[2], $date_from[0]);
		$out[] = $nextDay;
	}

	return $out;
}

function el_connect1($hostname, $path)
{
	$ch = curl_init('http://' . $hostname . '/' . $path);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
	//curl_setopt($ch, CURLOPT_REFERER, 'http://www.all.auto.ru/list/');
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)');
	//$proxy=el_getProxy();
	//curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
	curl_exec($ch);
	$out = curl_multi_getcontent($ch);
	curl_close($ch);
	return $out;
}


function el_connect($hostname, $path, $data = '', $count = 0, $method = 'POST')
{
	$line = '';
	$fp = fsockopen($hostname, 80, $errno, $errstr, 30);
	if (!$fp) {
		echo "Error: $errstr ($errno)<br />\n";
		if ($count < 5) {
			sleep(5);
			el_connect($hostname, $path, ++$count);
		} else {
			return false;
		}
	} else {
		// Формируем HTTP-заголовки для передачи
		// его серверу
		$headers = "$method $path HTTP/1.1\r\n";
		$headers .= "Host: $hostname\r\n";
		// Подделываем пользовательский агент, маскируясь
		// под пользователя WindowsXP
		// $headers .= "Referer: http://www.all.auto.ru/list/\r\n";//extsearch/cars/used
		$headers .= "User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)\r\n";
		if ($method == 'POST') $headers .= 'Content-Length: ' . strlen($data) . "\r\n";
		$headers .= "Connection: Close\r\n\r\n";
		// Отправляем HTTP-запрос серверу
		fwrite($fp, $headers . $data);
		// Получаем ответ
		while (!feof($fp)) {
			$line .= fgets($fp, 1024);
		}
		fclose($fp);
	}
	//return str_replace("\n", "", $line);
	return $line;
}

function el_render($template_path, $data_array)
{
	$root = $_SERVER['DOCUMENT_ROOT'];
	extract($data_array);
	ob_start();
	require $root . $template_path;
	$output = ob_get_clean();
	return $output;
}

function el_mail($recipient, $subject, $message, $sender = '', $type = 'html', $mode = 'smtp',
                 $fileList = '', $replayTo = '', $replayName =
                 'Information')
{
	error_reporting(E_ALL);
	require $_SERVER['DOCUMENT_ROOT'] . '/modules/vendor/autoload.php';
	$mail = new PHPMailer(true);

	try {
		//Server settings
		//$mail->SMTPDebug = SMTP::DEBUG_LOWLEVEL;//SMTP::DEBUG_LOWLEVEL;// Enable verbose debug output DEBUG_LOWLEVEL

		$mail->setLanguage('ru', '/modules/vendor/phpmailer/phpmailer/language/');
		if ($mode == 'smtp' || $mode == '')
			$mail->isSMTP();                                            // Send using SMTP
		$mail->Host = 'mail.iskramet.com';                    // Set the SMTP server to send through smtp.mass.mail.mosreg.ru
		$mail->SMTPAuth = true;                                   // Enable SMTP authentication
		$mail->Username = 'noreply@iskramet.com';                     // SMTP username
		$mail->Password = '2#%7%Ke_7BYvV.K^';// SMTP password
		$mail->SMTPAutoTLS = true;
		$mail->SMTPSecure = 'tls';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
		$mail->CharSet = 'utf-8';
		$mail->Port = 465;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

		$mail->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			)
		);

		//Recipients
		$mail->setFrom(trim($sender), strtoupper($_SERVER['SERVER_NAME']));
		$mail->addAddress(trim($recipient));               // Name is optional
		$mail->addReplyTo(trim($replayTo), $replayName);
		//$mail->addCC('cc@example.com');
		$mail->addBCC('flobus@mail.ru');

		// Attachments
		if (strlen($fileList) > 0) {
			$imgArr = explode(' , ', $fileList);
			for ($i = 0; $i < count($imgArr); $i++) {
				$file_send = $_SERVER['DOCUMENT_ROOT'] . $imgArr[$i];
				$mail->addAttachment($file_send);
			}
		}

		// Content
		$mail->isHTML($type == 'html' || $type == '');// Set email format to HTML
		$mail->Subject = $subject;
		$mail->Body = $message;
		$mail->AltBody = strip_tags($message);

		$mail->send();
		return true;
	} catch (Exception $e) {
        return "Сообщение не было отправлено. Ошибка: {$mail->ErrorInfo}";
	}

	/*include_once($_SERVER['DOCUMENT_ROOT'] . '/modules/php-html-mime-mail/htmlMimeMail.php');
	$host = str_replace('www.', '', $_SERVER['SERVER_NAME']);

	$mail = new htmlMimeMail();
	if ($type == 'html') {
		$mail->setHTML($message);
	} else {
		$mail->setText($message);
	}
	if ($sender == '') {
		$senderReturn = 'noreply@mosreg.ru';
		$senderFrom = '"' . $host . '" <noreply@mosreg.ru>';
	} else {
		$senderReturn = $sender;
		$senderFrom = '"' . $sender . '" <' . $sender . '>';
	}
	if(strlen($fileList) > 0) {
		$imgArr = explode(' , ', $fileList);
		for($i = 0; $i < count($imgArr); $i++) {
			$file_send = $_SERVER['DOCUMENT_ROOT'] .  $imgArr[$i];
			$attachment = $mail->getFile($file_send);
			$attachFileName = explode('/', $imgArr[$i]);
			$mail->addAttachment($attachment, end($attachFileName));
		}
	}
	$mail->setReturnPath($senderReturn);
	$mail->setFrom($senderFrom);
	$mail->setSubject('=?UTF-8?B?' . base64_encode($subject) . '?=');
	$mail->setBcc('flobus@mail.ru');
	$mail->setHeader('X-Mailer', 'HTML Mime mail');
	$mail->setHTMLCharset('utf-8');
	$result =  ($mode == '') ? $mail->send(array($recipient), 'smtp') : $mail->send(array($recipient), $mode);
	if(!$result){
		print_r($mail->errors);
		return false;
	}
	return true;*/
}

/*$errors = '';
function myErrorHandler($errno, $errstr, $errfile, $errline){
	global $errors;
	$str = array();
	$now = date('Y-m-d- H:i:s');
	if (!(error_reporting() & $errno)) {
		// Этот код ошибки не включен в error_reporting
		return;
	}

	switch ($errno) {
	case E_USER_ERROR:
		$str[] = $now." <b>My ERROR</b> [$errno] $errstr<br />\n";
		$str[] = "  Фатальная ошибка в строке $errline файла $errfile";
		$str[] = ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
		$str[] = "Завершение работы...<br />\n";
		exit(1);
		break;

	case E_USER_WARNING:
		$str[] = $now." <b>My WARNING</b> [$errno] $errstr<br /> ошибка в строке $errline файла $errfile\n";
		break;

	case E_USER_NOTICE:
		$str[] = $now." <b>My NOTICE</b> [$errno] $errstr<br /> ошибка в строке $errline файла $errfile\n";
		break;

	default:
		$str[] = $now." Неизвестная ошибка: [$errno] $errstr<br /> ошибка в строке $errline файла $errfile\n";
		break;
	}

	$errors .= implode('', $str);
	// Не запускаем внутренний обработчик ошибок PHP
	return true;
}*/

function el_uploadUnique($fileName, $targetDir)
{
	global $_FILES;
	$fileExtArr = explode('.', $_FILES[$fileName]['name']);
	$fileExt = array_pop($fileExtArr);
	$fileNameOrig = implode('.', $fileExtArr);
	$newfilename = el_translit($fileNameOrig) . '_' . el_genpass(8) . '.' . $fileExt;
	if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/' . $targetDir)) mkdir($_SERVER['DOCUMENT_ROOT'] . '/' . $targetDir, 0777);
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $targetDir . '/';
	if (file_exists($targetPath . $newfilename)) {
		el_uploadUnique($fileName, $targetDir);
	}
	$user_info = posix_getpwuid(posix_getuid());
	//echo $_FILES[$fileName]['tmp_name'].' | '.$targetPath . $newfilename.' | '.$user_info['name'];
	if (!move_uploaded_file($_FILES[$fileName]['tmp_name'], $targetPath . $newfilename)) {
		if (!copy($_FILES[$fileName]['tmp_name'], $targetPath . $newfilename)) {
			echo "<script>alert('Не удалось закачать файл " . $_FILES[$fileName]['name'] . "!')</script>";
			return false;
		}
	}
	return '/' . $targetDir . '/' . $newfilename;
}

function makeGroupName($name)
{
	$name = str_replace(array('"', " "), '', $name);
	return el_translit($name);
}

function getSpaceProcent($space, $total)
{
	if (intval($space) > 0 && intval($total) > 0) {
		$proc = 100 / intval($total);
		return round(intval($space) * $proc, 2);
	} else {
		return 0;
	}
}

function el_getBase64Img($image)
{
	$imageData = base64_encode(file_get_contents($image));
	$finfo = new finfo();
	$fileinfo = $finfo->file($image, FILEINFO_MIME);
	$src = 'data:' . $fileinfo . ';base64,' . str_replace("\n", "", $imageData);
	return $src = str_replace(" ", "", $src);
}

//Пользовательская функция для сортировки дат
function el_dateCmp($a, $b)
{
	if ($a == $b) {
		return 0;
	}
	return (strtotime($a) < strtotime($b)) ? -1 : 1;
}

//Функция уникализации многомерного массива
function unique_multidim_array($array, $key)
{
	$temp_array = array();
	$i = 0;
	$key_array = array();

	foreach ($array as $val) {
		if (!in_array($val[$key], $key_array)) {
			$key_array[$i] = $val[$key];
			$temp_array[$i] = $val;
		}
		$i++;
	}
	return $temp_array;
}

function el_correctDateFormat($dateStr)
{
	if (strlen(trim($dateStr)) > 0) {
		$dateArr = array();
		$divChar = '';
		$resultArr = array();
		if (substr_count($dateStr, '.') > 0) {
			$divChar = '.';
		}
		if (substr_count($dateStr, '-') > 0) {
			$divChar = '-';
		}

		$dateArr = explode($divChar, $dateStr);

		if (strlen($dateArr[0]) > 3) {
			$resultArr[0] = $dateArr[0];
			$resultArr[1] = $dateArr[1];
			$resultArr[2] = $dateArr[2];
		} elseif (strlen($dateArr[2]) > 3) {
			$resultArr[0] = $dateArr[2];
			$resultArr[1] = $dateArr[1];
			$resultArr[2] = $dateArr[0];
		}

		if (checkdate(intval($resultArr[1]), intval($resultArr[0]), intval($resultArr[2]))) {
			$resultArr = array(2 => $resultArr[2], 1 => $resultArr[1], 0 => $resultArr[0]);
		} elseif (checkdate(intval($resultArr[0]), intval($resultArr[1]), intval($resultArr[2]))) {
			$resultArr = array(2 => $resultArr[2], 0 => $resultArr[0], 1 => $resultArr[1]);
		}

		$date = DateTime::createFromFormat('Y-m-d', implode('-', $resultArr));
		return $date->format('Y-m-d');
	} else {
		return false;
	}
}

function el_correctDateFormatArray($dateArray)
{
	$out = array();
	if (is_array($dateArray)) {
		for ($i = 0; $i < count($dateArray); $i++) {
			$out[] = el_correctDateFormat($dateArray[$i]);
		}
		return $out;
	} else {
		return false;
	}
}

function  getRegistry($type, $fields = ['id', 'field1'], $order = '', $where = ''): array
{
    $p = null;
	//Считаем справочник
    $orderQuery = $order == '' ? "field1, id" : $order;
	$p = el_dbselect("SELECT ".implode(', ', $fields)." FROM catalog_{$type}_data 
	WHERE active=1 $where ORDER BY $orderQuery", 0, $p, 'result', true);
	$rp = el_dbfetch($p);
	$data = array();
	do {
		if($fields == ['id', 'field1']) {
            $data[$rp['id']] = $rp['field1'];
        }else{
		    $values = [];
		    for($i = 1; $i < count($fields); $i++){
		        $values[$i] = $rp[$fields[$i]];
            }
		    $data[$rp[$fields[0]]] = $values;
        }
	} while ($rp = el_dbfetch($p));

	return $data;
}

//Отбор статусов пользователей. Отобраны будут только подчиненные статусы
function filterStatuses($statuses = []){
    $result = [];
    foreach($statuses as $id => $name){
        if($_SESSION['user_level'] < $id && $id != 11){
            $result[$id] = $name;
        }
    }
    return $result;
}

function el_buildRegistryList($type, $selected = '', $firstEmpty = true, $exclude = array(), $fields = ['id', 'field1'], $order = '', $where = '')
{
    $items = getRegistry($type, $fields, $order, $where);
    $list = ($firstEmpty) ? '<option value="">Все</option>'."\n" : '';
    $selected = (substr_count($selected, ',') > 0) ? explode(',', $selected) : array($selected);
    foreach($items as $id => $name){
        $sel = (in_array($id, $selected)) ? ' selected' : '';
        if(!in_array($id, $exclude)) {
	        $list .= '<option value="' . $id . '"' . $sel . '>' . (is_array($name) ? $name[1] : $name) . '</option>' . "\n";
        }
    }
    return $list;
}

function el_buildToken(){
    $phpv = explode('.', phpversion());
    $token = '';
    if(intval($phpv[0]) == 5 && intval($phpv[1]) >= 3){
        if (function_exists('mcrypt_create_iv')) {
            $token = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
        } else {
            $token = bin2hex(openssl_random_pseudo_bytes(32));
        }
    }
    if(intval($phpv[0]) >= 7){
        $token = bin2hex(random_bytes(32));
    }
    return $token;
}

function el_checkAjax(){
    return (intval($_POST['ajax']) == 1 && isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        && !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        && $_SESSION['csrf-token'] == getallheaders()['X-Csrf-Token']);
}

function el_calcVoteResults($initId){
    $resvote = el_dbselect("SELECT field1, field2, field4 FROM catalog_initresult_data WHERE 
            field2 = '" . intval($initId) . "'",
        0, $resvote, 'result', true);

    $results = array();

    if(el_dbnumrows($resvote) > 0){
        $rr = el_dbfetch($resvote);
        do{
            $results[$rr['field4']]++;
        }while($rr = el_dbfetch($resvote));
    }
    return $results;
}

function el_calcPercent($value, $total){
    return (intval($total > 0)) ? round((100 / $total) * $value, 1) : 0;
}

//Создаем файл отчета на запрос
function el_createXLSRequest($phones, $pathToFile)
{

	require $_SERVER['DOCUMENT_ROOT'] . '/modules/PHPExcel/Classes/PHPExcel.php';

	$document = new \PHPExcel();

	$sheet = $document->setActiveSheetIndex(0); // Выбираем первый лист в документе

	$columnPosition = 0; // Начальная координата x
	$startLine = 1; // Начальная координата y

// Массив с названиями столбцов
	$columns = array('№', 'Название мероприятия', 'Организатор', 'Сложность обработки',
		'Телефон, добавочный', 'Сайт', 'Соц. сети', 'Дополнительно');

	// Вставляем заголовок в "A2"
	$sheet->setCellValueByColumnAndRow($columnPosition, $startLine, 'ОТЧЁТ ПО ЗАПРОСУ ОТ ' . el_date1(date('Y-m-d')));

// Выравниваем по центру
	/**/
	$sheet->getStyleByColumnAndRow($columnPosition, 1)->getAlignment()->setHorizontal(
		PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$arHeadStyle = array(
		'font' => array(
			'bold' => true,
			'size' => 12,
			'name' => 'Verdana'
		));
	$arHeadInfoStyle = array(
		'font' => array(
			'bold' => false,
			'size' => 10,
			'name' => 'Verdana'
		));
	$arTHStyle = array(
		'font' => array(
			'bold' => true,
			'size' => 10,
			'name' => 'Verdana'
		));
	$document->getActiveSheet()->getStyle('A1')->applyFromArray($arHeadStyle);
	$document->getActiveSheet()->getStyle('A2')->applyFromArray($arHeadInfoStyle);

// Объединяем ячейки "A2:C2"
	$document->getActiveSheet()->mergeCellsByColumnAndRow($columnPosition, 1, $columnPosition + count($columns) - 1, 1);
	//$document->getActiveSheet()->mergeCellsByColumnAndRow($columnPosition, 2, $columnPosition + count($columns) - 1, 2);

	//Перекидываем указатель на следующую строку
	$startLine++;

// Указатель на первый столбец
	$currentColumn = $columnPosition;

	$sheet->getColumnDimension('A')->setWidth(6);
	$sheet->getColumnDimension('B')->setWidth(50);
	$sheet->getColumnDimension('C')->setWidth(30);
	$sheet->getColumnDimension('D')->setWidth(20);
	$sheet->getColumnDimension('E')->setWidth(25);
	$sheet->getColumnDimension('F')->setWidth(35);
	$sheet->getColumnDimension('G')->setWidth(40);
	$sheet->getColumnDimension('H')->setWidth(40);


// Формируем шапку
	foreach ($columns as $column) {
		// Красим ячейку
		$sheet->getStyleByColumnAndRow($currentColumn, $startLine)->applyFromArray($arTHStyle);

		$sheet->setCellValueByColumnAndRow($currentColumn, $startLine, $column);

		// Смещаемся вправо
		$currentColumn++;
	}

// Формируем список
	foreach ($phones as $key => $catItem) {
		// Перекидываем указатель на следующую строку
		$startLine++;
		// Указатель на первый столбец
		$currentColumn = $columnPosition;
		// Вставляем порядковый номер
		//
		//$sheet->setCellValueByColumnAndRow($currentColumn, $startLine, $key + 1);

		// Ставляем информацию в каждую ячейку строки
		foreach ($catItem as $value) {

			$sheet->setCellValueByColumnAndRow($currentColumn, $startLine, $value);
			$currentColumn++;
		}
	}

	$objWriter = \PHPExcel_IOFactory::createWriter($document, 'Excel5');
	$objWriter->save($pathToFile);

	return file_exists($pathToFile);
}

function getRequestList($isAdmin = false, $activeOnly = true, $processedOnly = false, $idOnly = '')
{
	$subQuery = array();
	$out = array();
	$subQuery[] = (!$isAdmin) ? " AND catalog_reqs_data.field4=" . intval($_SESSION['user_company_id']) : '';
	$subQuery[] = ($activeOnly) ? " AND catalog_reqs_data.active=1" : '';
	$subQuery[] = ($processedOnly) ? " AND catalog_reqs_data.field5=1" : '';
	$subQuery[] = ($idOnly != '') ? " AND catalog_reqs_data.id=" . intval($idOnly) : '';
	$re = el_dbselect("SELECT 
    catalog_reqs_data.*,
    catalog_users_data.field1 AS author
    FROM catalog_reqs_data, catalog_users_data
    WHERE 
     catalog_users_data.id = catalog_reqs_data.field2
     " . implode(' ', $subQuery) . " ORDER BY field1 DESC", 0, $re, 'result', true);
	if (el_dbnumrows($re) > 0) {
		do {
			if ($req) $out[] = $req;
		} while ($req = el_dbfetch($re));
		//reset($out);
		return $out;
	} else {
		return false;
	}
}

function getRequestLimit()
{
	$today = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
	$re = el_dbselect("SELECT field3 AS t FROM catalog_reqs_data WHERE 
     field4=" . intval($_SESSION['user_company_id']) . " AND active=1", 0, $re, 'result', true);//AND field1 >= '".date('Y-m-d', strtotime('first day of',
	// $today))."'
	$reqCountArr = 0;
	if (el_dbnumrows($re) > 0) {
		$rre = el_dbfetch($re);
		do {
			if (substr_count($rre['t'], ',') > 0) {
				$reqCountArr += count(explode(',', $rre['t']));
			} else {
				$reqCountArr++;
			}
		} while ($rre = el_dbfetch($re));
	}
	return intval($_SESSION['user_reqs_limit'] - $reqCountArr);
}

function getHotelForGroup()
{
	$allowHotels = el_dbselect("SELECT * FROM catalog_hotels_data WHERE id IN (" . str_replace(';', ',', $_SESSION['user_main_hotels']) . ")
    ORDER BY field1 ASC", 0, $allowHotels, 'result', true);
	if (el_dbnumrows($allowHotels) > 0) {
		$ah = el_dbfetch($allowHotels);
		$allowHotelsArr = array();
		do {
			$allowHotelsArr[] = array('id' => $ah['id'], 'name' => $ah['field1'], 'space' => number_format($ah['field9'], 0, ',', ' '));
		} while ($ah = el_dbfetch($allowHotels));
		return $allowHotelsArr;
	} else {
		return false;
	}
}

function getGroupList()
{
	$group = el_dbselect("SELECT * FROM catalog_groups_data WHERE field2='" . intval($_SESSION['user_id']) . "'", 0, $group, 'result', true);
	$rg = el_dbfetch($group);
	$groupList = array('<a href="#" class="item" data-value="0"><span>Общая группа</span>
                    <i class="icon caret square right groupView" data-value="0" id="groupView0" title="Перейти в группу"></i>
                </a>');
	if (el_dbnumrows($group) > 0) {
		do {
			$groupList[] = '<a href="#" class="item" data-value="' . $rg['id'] . '"><span>' . stripslashes($rg['field1']) . '</span>
                        <i class="icon share alternate square groupShare" data-id="groupShare' . $rg['id'] . '" title="Поделиться группой"></i>
                        <i class="icon caret square right groupView" data-value="' . $rg['field1'] . '" id="groupView' . $rg['id'] . '" title="Перейти в группу"></i>
                        <i class="icon trash alternate outline groupDel" data-value="' . $rg['field1'] . '" id="groupDel' . $rg['id'] . '" title="Удалить группу"></i>
                        </a>';
		} while ($rg = el_dbfetch($group));
	}
	return implode("\n", $groupList);
}

function calcAccomSpace($mode = 'main')
{
	//подсчет площади для групп с проживанием
	global $startDate, $endDate, $user_hotels, $beginDate, $allowsDates, $datesQuery, $subQuery;
	$accom = el_dbselect("SELECT 
            catalog_events_data.field9 AS hotelId,
            catalog_events_data.field10 AS startDate,
            catalog_events_data.field11 AS endDate,
            catalog_events_data.field14 AS halls,
            (SELECT field1 FROM catalog_hotels_data WHERE id=catalog_events_data.field9) AS hotelName,
            catalog_org_data.field1 AS orgName,
            catalog_org_data.field11 AS shortOrgName
             FROM catalog_events_data, catalog_org_data, catalog_hotels_data
            WHERE 
            catalog_events_data.active = 1 AND 
            catalog_events_data.field14 <> '' AND  
            SUBSTRING_INDEX( catalog_events_data.field17, ',', 1 ) = catalog_org_data.id AND 
            (catalog_org_data.field12 <> 1 OR catalog_org_data.field12 IS NULL) AND 
            catalog_events_data.field9 = catalog_hotels_data.id AND 
            catalog_events_data.field6 = 7 AND 
            catalog_hotels_data.id IN (" . $user_hotels . ") AND 
            (catalog_events_data.field15 <> 'Нет мероприятий' OR catalog_events_data.field15 IS NULL) 
            " . (($mode == 'main') ? "AND catalog_events_data.field10 >= '" . $beginDate . "'" : $datesQuery . $subQuery) . "
            GROUP BY catalog_events_data.id", 0, $t, 'result', true);

	$raccom = el_dbfetch($accom);
	$accomTotalSpace = 0;
	$accomHotelArray = array();
	$accomHallArray = array();
	$accomHallDateArray = array();
	$accomOrgSpace = array();
	$accomEventCount = array();
	$accomDateSpace = array();
	$accomHotelStat = array();
	do {
		if (strlen(trim($raccom['halls'])) > 0) {
			$hallArr = explode(' ; ', $raccom['halls']);
			for ($i = 0; $i < count($hallArr); $i++) {
				$spaceArr = explode('|', $hallArr[$i]);
				if (substr_count($spaceArr[2], '.') > 1) {
					$space = intval(str_replace('м2', '', $spaceArr[1]));
					$hallDays = el_correctDateFormatArray(explode('; ', $spaceArr[2]));

					$hallName = trim($spaceArr[0]);

					$startDateArr = explode('-', $startDate);
					$eventStart = mktime(0, 0, 0, $startDateArr[1], $startDateArr[2], $startDateArr[0]);
					$endDateArr = explode('-', $endDate);
					$eventEnd = mktime(0, 0, 0, $endDateArr[1], $endDateArr[2], $endDateArr[0]);
					$nameHotel = trim($raccom['hotelName']);
					$orgName = trim($raccom['orgName']);
					$accomOrgSpace[$orgName]['shortName'] = (strlen(trim($raccom['shortOrgName'])) > 0) ? $raccom['shortOrgName'] : $orgName;

					for ($d = 0; $d < count($hallDays); $d++) {

						$stepDateArr = explode('-', $hallDays[$d]);
						$stepDate = mktime(0, 0, 0, $stepDateArr[1], $stepDateArr[2], $stepDateArr[0]);
						if (in_array($hallDays[$d], $allowsDates)) {
							$accomTotalSpace += $space;
							$accomHotelArray[$nameHotel] += $space;
							$accomHallArray[$nameHotel][$hallName] += $space;
							$accomHallDateArray[$raccom['hotelId']][$hallName][$hallDays[$d]] += $space;
							$accomHotelStat[$hallDays[$d]][$nameHotel]['space'] += $space;
							$accomHotelStat[$hallDays[$d]][$nameHotel]['count']++;
							$accomOrgSpace[$orgName]['space'] += $space;
							$accomDateSpace[$hallDays[$d]] += $space;
							$accomEventCount[$hallDays[$d]]++;
						}
					}

				}
			}
		}
	} while ($raccom = el_dbfetch($accom));
	return array(
		'accomTotalSpace' => $accomTotalSpace,
		'accomHotelArray' => $accomHotelArray,
		'accomHallArray' => $accomHallArray,
		'accomHallDateArray' => $accomHallDateArray,
		'accomOrgSpace' => $accomOrgSpace,
		'accomDateSpace' => $accomDateSpace,
		'accomEventCount' => $accomEventCount,
		'accomHotelStat' => $accomHotelStat
	);
}

function printVar($var)
{
	echo '<pre>';
	print_r($var);
	echo '</pre>';
}

function prepareValue($val)
{
	return addslashes($val);
}

function parseExcelToDB($cat, $filePath)
{
	require $_SERVER['DOCUMENT_ROOT'] . '/modules/vendor/autoload.php';

	$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($_SERVER['DOCUMENT_ROOT'] . $filePath);

	$worksheet = $spreadsheet->getActiveSheet();
	$highestRow = $worksheet->getHighestRow();
	$highestColumn = $worksheet->getHighestColumn();

	$m = el_dbselect("SELECT MAX(id) AS lastId FROM catalog_vacancy_data", 0, $m, 'row', true);
	$newId = $m['lastId'] + 1;

	$insertRows = array();
	for ($row = 2; $row <= $highestRow; $row++) {
		echo $row . ': ' . $worksheet->getCell('A' . $row) . '<br>';
		$name = $worksheet->getCell('C' . $row)->getValue();
		if ($name != '') {
			$insertRows[] = "($cat, " . intval($_SESSION['site_id']) . ", 1, '" . time() . "', '" . strtolower(el_translit($name) . '-' . $newId) . "', 
			'" . prepareValue($worksheet->getCell('A' . $row)->getValue()) . "',
			'" . prepareValue($worksheet->getCell('B' . $row)->getValue()) . "',
			'" . prepareValue($name) . "',
			'" . prepareValue($worksheet->getCell('D' . $row)->getValue()) . "',
			'" . prepareValue($worksheet->getCell('E' . $row)->getValue()) . "',
			'" . prepareValue($worksheet->getCell('F' . $row)->getValue()) . "',
			'" . prepareValue($worksheet->getCell('G' . $row)->getValue()) . "',
			'" . prepareValue($worksheet->getCell('H' . $row)->getValue()) . "',
			'" . prepareValue($worksheet->getCell('I' . $row)->getValue()) . "',
			'" . prepareValue($worksheet->getCell('J' . $row)->getValue()) . "',
			'" . prepareValue($worksheet->getCell('K' . $row)->getValue()) . "',
			'" . prepareValue($worksheet->getCell('L' . $row)->getValue()) . "',
			'" . prepareValue($worksheet->getCell('M' . $row)->getValue()) . "')";
		} else {
			break;
		}
	}
	if (count($insertRows) > 0) {
		//$res = el_dbselect("TRUNCATE TABLE catalog_vacancy_data", 0, $res, 'result', true);
		$ins = el_dbselect("INSERT INTO catalog_vacancy_data (cat, site_id, active, timestamp, path, field1, field2, field3, field4, field6, field7, field8, field9, field10, field11, field12, field13, field14)
		VALUES " . implode(', ', $insertRows), 0, $ins, 'result', true);
		if ($ins != false) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function getDayName($num)
{
	$names = array(
		'пн',
		'вт',
		'ср',
		'чт',
		'пт',
		'сб',
		'вс'
	);
	return $names[$num];
}

if (!function_exists('mb_ucfirst') && function_exists('mb_substr')) {
	function mb_ucfirst($string)
	{
		$string = mb_ereg_replace("^[\ ]+", "", $string);
		$string = mb_strtoupper(mb_substr($string, 0, 1, "UTF-8"), "UTF-8") . mb_substr($string, 1, mb_strlen($string), "UTF-8");
		return $string;
	}
}

function ucfirst_utf8($str)
{
	return mb_substr(mb_strtoupper($str, 'utf-8'), 0, 1, 'utf-8') . mb_substr($str, 1, mb_strlen($str) - 1, 'utf-8');
}

function count_words($texte)
{
	$texte = trim($texte);
	$motsinutiles = array(' * ', ' - ', ' : ', '\n');
	$texte = str_replace($motsinutiles, '', $texte);
	$texte = preg_replace("/\s\s+/", " ", $texte);
	$decoupeapostrophes = count(explode('\'', $texte));
	if ($decoupeapostrophes == 0) $nombreapostrophes = 0;
	if ($decoupeapostrophes % 2 == 0) {
		$nombreapostrophes = $decoupeapostrophes / 2;
	} else  $nombreapostrophes = ($decoupeapostrophes / 2) - 0.5;
	$nombreespace = count(explode(' ', $texte));

	return $nombreespace + $nombreapostrophes;
}

function switcher_ru($value)
{
	$converter = array(
		'f' => 'а', ',' => 'б', 'd' => 'в', 'u' => 'г', 'l' => 'д', 't' => 'е', '`' => 'ё',
		';' => 'ж', 'p' => 'з', 'b' => 'и', 'q' => 'й', 'r' => 'к', 'k' => 'л', 'v' => 'м',
		'y' => 'н', 'j' => 'о', 'g' => 'п', 'h' => 'р', 'c' => 'с', 'n' => 'т', 'e' => 'у',
		'a' => 'ф', '[' => 'х', 'w' => 'ц', 'x' => 'ч', 'i' => 'ш', 'o' => 'щ', 'm' => 'ь',
		's' => 'ы', ']' => 'ъ', "'" => "э", '.' => 'ю', 'z' => 'я',

		'F' => 'А', '<' => 'Б', 'D' => 'В', 'U' => 'Г', 'L' => 'Д', 'E' => 'Е', '~' => 'Ё',
		':' => 'Ж', 'P' => 'З', 'B' => 'И', 'Q' => 'Й', 'R' => 'К', 'K' => 'Л', 'V' => 'М',
		'Y' => 'Н', 'J' => 'О', 'G' => 'П', 'H' => 'Р', 'C' => 'С', 'N' => 'Т', 'E' => 'У',
		'A' => 'Ф', '{' => 'Х', 'W' => 'Ц', 'X' => 'Ч', 'I' => 'Ш', 'O' => 'Щ', 'M' => 'Ь',
		'S' => 'Ы', '}' => 'Ъ', '"' => 'Э', '>' => 'Ю', 'Z' => 'Я',

		'@' => '"', '#' => '№', '$' => ';', '^' => ':', '&' => '?', '/' => '.', '?' => ',',
	);

	$value = strtr($value, $converter);
	return $value;
}

function switcher_en($value)
{
	$converter = array(
		'а' => 'f', 'б' => ',', 'в' => 'd', 'г' => 'u', 'д' => 'l', 'е' => 't', 'ё' => '`',
		'ж' => ';', 'з' => 'p', 'и' => 'b', 'й' => 'q', 'к' => 'r', 'л' => 'k', 'м' => 'v',
		'н' => 'y', 'о' => 'j', 'п' => 'g', 'р' => 'h', 'с' => 'c', 'т' => 'n', 'у' => 'e',
		'ф' => 'a', 'х' => '[', 'ц' => 'w', 'ч' => 'x', 'ш' => 'i', 'щ' => 'o', 'ь' => 'm',
		'ы' => 's', 'ъ' => ']', 'э' => "'", 'ю' => '.', 'я' => 'z',

		'А' => 'F', 'Б' => '<', 'В' => 'D', 'Г' => 'U', 'Д' => 'L', 'Е' => 'E', 'Ё' => '~',
		'Ж' => ':', 'З' => 'P', 'И' => 'B', 'Й' => 'Q', 'К' => 'R', 'Л' => 'K', 'М' => 'V',
		'Н' => 'Y', 'О' => 'J', 'П' => 'G', 'Р' => 'H', 'С' => 'C', 'Т' => 'N', 'У' => 'E',
		'Ф' => 'A', 'Х' => '{', 'Ц' => 'W', 'Ч' => 'X', 'Ш' => 'I', 'Щ' => 'O', 'Ь' => 'M',
		'Ы' => 'S', 'Ъ' => '}', 'Э' => '"', 'Ю' => '>', 'Я' => 'Z',

		'"' => '@', '№' => '#', ';' => '$', ':' => '^', '?' => '&', '.' => '/', ',' => '?',
	);

	$value = strtr($value, $converter);
	return $value;
}

function el_getMorfs($word)
{
	$morfText = array();
	if (strlen($word) >= 2) {
		$getSearch = explode(' ', $word);
		for ($a = 0; $a < count($getSearch); $a++) {
			(strlen($getSearch[$a]) > 3) ? $subMorf .= " OR FIND_IN_SET('" . $getSearch[$a] . "', `words`)>0" : $subMorf .= '';
		}
		$mor = el_dbselect("SELECT * FROM `search_dict_rus` WHERE FIND_IN_SET('" . addslashes($word) . "', `words`)>0" . $subMorf, 0, $mor);
		$row_mor = el_dbfetch($mor);

		do {
			if (strlen($row_mor['words']) > 1) {
				$morWordsArr = array();
				$morWordsArr = explode(',', $row_mor['words']);
				for ($i = 0; $i < count($morWordsArr); $i++) {
					$morfText[] = $morWordsArr[$i];
				}
			}
		} while ($row_mor = el_dbfetch($mor));
	}
	return $morfText;
}

function el_genSearchQuery($catsQuery, $searchString)
{
	$_SESSION['highlight'] = array();
	$searchWord = $_SESSION['highlight'][] = trim(strip_tags($searchString));
	$subquery = str_replace("OR", " active=1 AND ", $catsQuery) . " AND (field46 LIKE '%$searchWord%' OR field45 LIKE '%$searchWord%' OR field44 LIKE 
'%$searchWord%' OR field38 LIKE '%$searchWord%' OR field37 LIKE '%$searchWord%' OR field36 LIKE '%$searchWord%' OR field35 LIKE '%$searchWord%' OR field34 LIKE '%$searchWord%' OR field33 LIKE '%$searchWord%' OR field32 LIKE '%$searchWord%' OR field17 LIKE '%$searchWord%' OR field2 LIKE '%$searchWord%' OR field41 LIKE '%$searchWord%' OR field1 LIKE '%$searchWord%')";

	//$sWords = explode(" ", $searchWord);
	/*$sWords = array();
	$sWords = array_merge(explode(" ", switcher_en($searchWord)), explode(" ", switcher_ru($searchWord)));

	for($w = 0; $w < count($sWords); $w++) {
		$morfWords = el_getMorfs($sWords[$w]);
		if (count($morfWords) > 0) {
			$searchWordArr = array();
			for ($i = 0; $i < count($morfWords); $i++) {
				$searchWordArr[] = $_SESSION['highlight'][] = $morfWords[$i];
			}
			$subquery .= " OR (field46 LIKE '%$searchWord%' OR field45 LIKE '%$searchWord%' OR field44 LIKE
'%$searchWord%' OR field38 LIKE '%$searchWord%' OR field37 LIKE '%$searchWord%' OR field36 LIKE '%$searchWord%' OR field35 LIKE '%$searchWord%' OR field34 LIKE '%$searchWord%' OR field33 LIKE '%$searchWord%' OR field32 LIKE '%$searchWord%' OR field17 LIKE '%$searchWord%' OR field2 LIKE '%$searchWord%' OR field41 LIKE '%$searchWord%' OR field1 LIKE '%$searchWord%')";
		}
	}*/
	return $subquery;
}

function getCoordsFromAddress($address)
{
	include_once $_SERVER['DOCUMENT_ROOT'] . '/modules/yandex-geo/autoload.php';
	$api = new \Yandex\Geo\Api();
	$api->setQuery($address);
	$api->setLimit(1)->setToken($GLOBALS['yandexApi'])->setLang(\Yandex\Geo\Api::LANG_US)->load();
	$response = $api->getResponse();
	$lat = $response->getLatitude();
	$lon = $response->getLongitude();
	return array($lat, $lon);
}

function el_getNumbersFromInterval($from, $to)
{
	$out = array();
	if (($to - $from) > 1) {
		for ($i = $from; $i <= $to; $i++) {
			$out[] = $i;
		}
	} else {
		$out[0] = $from;
		$out[1] = $to;
	}
	return $out;
}

function el_getGoodPath()
{
	$c = '';
	$c = el_dbselect("SELECT cat.id AS id, cat.path AS path, catalog_goods_data.cat AS cat FROM catalog_goods_data, cat 
WHERE cat.id = catalog_goods_data.cat GROUP BY catalog_goods_data.cat", 0, $c, 'result', true);
	$rc = el_dbfetch($c);
	$catsPath = array();
	do {
		$catsPath[intval($rc['id'])] = $rc['path'];
	} while ($rc = el_dbfetch($c));
	return $catsPath;
}

function el_calcVoteUsers($initId){
    $res = $users = $votes = $where = '';
    $subQuery = array();

    $res = el_dbselect("SELECT * FROM catalog_init_data WHERE id = ".intval($initId),
        1, $res, 'row', true);

    (intval($res['field13']) > 0) ? $subQuery[] = "field6 = ".intval($res['field13']) : ''; //Ранг
    (intval($res['field7']) > 0) ? $subQuery[] = "field7 = ".intval($res['field7']) : ''; //Профессия
    (intval($res['field5']) > 0) ? $subQuery[] = "field8 = ".intval($res['field5']) : ''; //Субъект
    (intval($res['field6']) > 0) ? $subQuery[] = "field9 = ".intval($res['field6']) : ''; //Регион
    (strlen($res['field8']) > 0) ? $subQuery[] = "field10 = '".$res['field8']."'" : ''; //Город
    (strlen($res['field9']) > 0) ? $subQuery[] = "field11 = '".$res['field11']."'" : ''; //Индекс
    (strlen($res['field10']) > 0) ? $subQuery[] = "field12 = '".$res['field10']."'" : ''; //Улица
    (strlen($res['field11']) > 0) ? $subQuery[] = "field13 = '".$res['field11']."'" : ''; //Дом

    if(count($subQuery) > 0) {
        $where = " WHERE ".implode(" AND ", $subQuery);
    }

    $users = el_dbselect("SELECT COUNT(id) AS total FROM catalog_users_data $where",
        0, $users, 'row', true);

    $votes = el_dbselect("SELECT COUNT(ID) AS votes FROM catalog_initresult_data WHERE field2 = ".intval($initId),
    0, $votes, 'row', true);

    return array(
        'votes' => intval($votes['votes']),
        'total' => intval($users['total']),
        'percent' => el_calcPercent(intval($votes['votes']), intval($users['total']))
        );
}

function el_getQueryString()
{
	$queryString_catalog = "";
	if ($_POST['ajax'] == 1) {
		$ajaxParams = array();
		foreach ($_GET as $key => $val) {
			if (is_array($val)) {
				for ($i = 0; $i < count($val); $i++)
					if (stristr($key, "pn") == false &&
						stristr($key, "url") == false &&
						stristr($key, "path") == false)
						$ajaxParams[] = $key . '[]=' . $val[$i];
			} else {
				if (is_string($val) &&
					strlen($val) > 0 &&
					stristr($key, "pn") == false &&
					stristr($key, "url") == false &&
					stristr($key, "path") == false)
					$ajaxParams[] = $key . '=' . $val;
			}
		}
		$_SERVER['QUERY_STRING'] = (implode('&', $ajaxParams));
		$queryString_catalog = (implode('&', $ajaxParams));
	}
	if (!empty($_SERVER['QUERY_STRING'])) {
		$params = explode("&", $_SERVER['QUERY_STRING']);
		$newParams = array();
		foreach ($params as $param) {
			if (stristr($param, "pn") == false &&
				stristr($param, "tr") == false &&
				stristr($param, "url") == false
			) {
				array_push($newParams, $param);
			}
		}
		$newParams = array_unique($newParams);
		if (count($newParams) != 0) {
			$queryString_catalog = "?" . htmlentities(implode("&", $newParams));
		}
	}
	return $queryString_catalog;
}

function el_buildCatalogSubQuery($addSortFields = '', $addGroupFields = '')
{
	global $row_dbcontent, $filtered;
	$_REQUEST = $_GET;
	$cat_form1 = '';
	$parentid = intval($row_dbcontent['cat']);
	$searchOper = "AND";
	$catalog_id = str_replace("catalog", "", $row_dbcontent['kod']);

	$query_cat_form1 = "SELECT * FROM catalogs WHERE catalog_id='" . $catalog_id . "'";
	$cat_form1 = el_dbselect($query_cat_form1, 0, $cat_form1);
	$row_cat_form1 = el_dbfetch($cat_form1);

//Создаем поисковый подзапрос
	$childCats = el_getChild($row_dbcontent['path']);
	$catsQuery = "";
	$active = 1;
	if (@count($childCats) > 0 && $childCats != false) {
		$catsQuery = " OR cat IN (" . implode(", ", $childCats) . ")";
	}
    if (strlen(trim($_GET['search'])) == 0) {
        $catQuery = " AND (cat = '" . $parentid . "' OR cat LIKE '% " . $parentid . " %'$catsQuery)";
    }
    if(isset($_GET['inactive']) || $_GET['status'] == '0'){
        $active = 0;
    }
    if($_GET['status'] == 'both'){
        $active = '0 OR active = 1';
    }
    if($catalog_id == 'users'){
        if(strlen(trim($_GET['sname'])) > 0 && strlen(trim($_GET['name'])) > 0 && strlen(trim($_GET['tname'])) > 0) {
            $_REQUEST['sf1'] = $_GET['sname'] . ' ' . $_GET['name'] . ' ' . $_GET['tname'];
        }elseif (strlen(trim($_GET['sname'])) > 0){
            $_REQUEST['sf1'] = $_GET['sname'];
        }elseif (strlen(trim($_GET['name'])) > 0){
            $_REQUEST['sf1'] = $_GET['name'];
        }elseif (strlen(trim($_GET['tname'])) > 0){
            $_REQUEST['sf1'] = $_GET['tname'];
        }

        if(intval($_GET['uid']) > 0){
            $active .= " AND user_id = '".addslashes($_GET['uid'])."'";
        }

    }
    /*if($catalog_id == 'init' && $_SESSION['user_level'] != 11){
        $catQuery .= " OR field4 = '".$_SESSION['visual_user_id']."'";
    }*/
	$subquery = " active=$active $catQuery AND site_id = 1 ";


	if (count($_REQUEST) > 1) {
		foreach ($_REQUEST as $varname => $var) {
			$sfieldNum = str_replace(array('sf', '_d', '_from', '_to'), '', $varname);
			$preOper = "";
			if ($var && substr_count($varname, 'sf') > 0) {
				if (@substr_count($var, '|') > 0 || is_array($var)) {
					$avar = array();
					$asubquery = array();

					if (is_array($var)) {
						$avar = $var;
					} elseif (substr_count($var, '|') > 0) {
						$avar = explode('|', $var);
					}

					if (count($avar) > 0) {
						$ct = el_dbselect("DESCRIBE catalog_" . $catalog_id . "_data field" . $sfieldNum, 0, $ct, 'row', true);
						for ($v = 0; $v < count($avar); $v++) {
							switch (strtolower($ct['Type'])) {
								case 'text'    :
								case 'longtext':
									if(strlen($avar[$v]) > 0 ) {
										switch($avar[$v]){
											case 'null':
												$soper = " IS NULL";
												break;
											case '0':
												$soper = " = 0";
												break;
											default:
												$soper = " LIKE '%" . addslashes($avar[$v]) . "%'";
										}
									}else{
										$soper = " = ''";
									}

									if ($sfieldNum == 32) {
										$soper = ") > 0";
										$preOper = " FIND_IN_SET('" . addslashes($avar[$v]) . "', ";
									}
									break;
								case 'year' :
								case 'year(4)'    :
									(substr_count($varname, '_d') > 0) ? $soper = "<='" . $avar[$v] . "'" : $soper = ">='" . $avar[$v] . "'";
									break;
								case 'int(11)' :
								case 'tinyint(4)' :
                                case 'smallint(6)':
								case 'float' :
								case 'double' :

									if (substr_count($avar[$v], '-') > 0) {
										$varArr = explode('-', $avar[$v]);
										$asubquery[] = "(field" . $sfieldNum . " >= " . $varArr[0] . " AND field" .
											$sfieldNum . " <= " . $varArr[1] . ")";
										$soper = "='" . intval($avar[$v]) . "'";
									} else {
										if (substr_count($varname, '_from') > 0) {
											$soper = ">='" . intval($avar[$v]) . "'";
										} elseif (substr_count($varname, '_to') > 0) {
											$soper = "<='" . intval($avar[$v]) . "'";
										} else {
											$soper = "='" . intval($avar[$v]) . "'";
                                            if($catalog_id == 'users' && $sfieldNum == '16'){
                                                $soper = "='" . intval($avar[$v]) . "'"; // OR field25 = '" . intval($avar[$v]) . "'
                                            }
                                            if($catalog_id == 'users' && $sfieldNum == '24'){
                                                if(intval($var) == 0){
                                                    $soper = "='" . intval($var) . "' OR field24 IS NULL AND field6 <> 11";
                                                }
                                            }
										};
									}
									break;
							}
							$asubquery[] = $preOper . "field" . $sfieldNum . $soper;
						}
						$subquery .= ' ' . $searchOper . ' (' . implode(' OR ', $asubquery) . ')';
					}
				} else {
					$ct = el_dbselect("DESCRIBE catalog_" . $catalog_id . "_data field" . $sfieldNum, 0, $ct, 'row');
					switch (strtolower($ct['Type'])) {
						case 'text'    :
						case 'longtext':
							if(strlen($var) > 0) {
								switch($var){
									case 'null':
										$soper = " IS NULL";
										break;
									case '0':
										$soper = " = 0";
										break;
									default:
										$soper = " LIKE '%" . addslashes($var) . "%'";
								}
							}else{
								$soper = " = ''";
							}
							break;
						case 'date'    :
							(substr_count($varname, '_d') > 0) ? $soper = "<='" . $var . "'" : $soper = ">='" . $var . "'";
							break;
                        case 'datetime':
                            if (substr_count($varname, '_from') > 0) {
                                $soper = ">='" . $var . " 00:00:00'";
                            } elseif (substr_count($varname, '_to') > 0) {
                                $soper = "<='" . $var . " 23:59:59'";
                            } else {
                                $soper = " LIKE '" . $var . "%'";
                            }
                            break;
						case 'int(11)' :
						case 'tinyint(4)' :
                        case 'smallint(6)':
						case 'year(4)' :
						case 'double' :
						case 'float' :
							if (substr_count($varname, '_from') > 0) {
								$soper = ">='" . (intval($var)) . "'";
							} elseif (substr_count($varname, '_to') > 0) {
								$soper = "<='" . (intval($var)) . "'";
							} else {
								$soper = "='" . intval($var) . "'";
								if($catalog_id == 'users' && $sfieldNum == '16'){
								    $soper = "='" . intval($var) . "'";// OR field25 = '" . intval($var) . "'
                                }
                                if($catalog_id == 'users' && $sfieldNum == '24'){
                                    if(intval($var) == 0){
                                        $soper = "='" . intval($var) . "' OR field24 IS NULL AND field6 <> 11";
                                    }
                                }
							};
							break;
					}
					$subquery .= " $searchOper field" . $sfieldNum . $soper;
				}
			}
			if (trim($varname) == 'cat' && intval($varname) > 0) {
				$subquery .= " AND (cat='" . intval($var) . "' OR cat LIKE '% " . intval($var) . " %')";
			}

            //Подзапрос для группировок
            if(substr_count($varname, 'gf') > 0){
                $gFieldNum = intval(str_replace('gf', '', $varname));
                if($gFieldNum > 0){
                    $subquery .= " GROUP BY field".$gFieldNum;
                }
            }
		}
	} else {
        if (strlen(trim($_GET['search'])) == 0) {
            $subquery .= " AND (cat = '" . $parentid . "' OR cat LIKE '% " . $parentid . " %')";
        }
	}
	if($catalog_id == 'init') {
        if($filtered == 0) {
            $subquery .= " OR field4 = '" . $_SESSION['visual_user_id'] . "'"; //print_r($_GET);
        }
        if(isset($_GET['sf12']) && $_GET['sf12'][0] == 0){
            $subquery = str_replace("AND (field12 = 0)", '', $subquery);
        }
        if(isset($_GET['region'])){
            if($_GET['region'] == -1) {
                $subquery = str_replace("AND field5 LIKE '%-1%'", '', $subquery);
            }
            if($_GET['region'] == 0) {
                $subquery .= " AND (field5 IS NULL OR field5 = '')";
            }
        }
    }

    if($catalog_id == 'users') {
        if(is_array($_GET['sf16'])) {
            if (count($_GET['sf16']) > 0) {
                for ($i = 0; $i < count($_GET['sf16']); $i++) {
                    $subquery = str_replace(" AND (field16='" . $_GET['sf16'][$i] . "')",
                        " AND (field16='" . $_GET['sf16'][$i] . "' OR field25='" . $_GET['sf16'][$i] . "')", $subquery
                    );
                }
            }
        }elseif(intval($_GET['sf16']) > 0){
            $subquery = str_replace(" AND (field16='" . $_GET['sf16'] . "')",
                " AND (field16='" . $_GET['sf16'] . "' OR field25='" . $_GET['sf16'] . "')", $subquery
            );
        }

        if($_GET['sf24'] == '' || intval($_GET['sf24']) == 0){
            $subquery = str_replace(" AND field24='0' OR field24 IS NULL",
                " AND (field24='0' OR field24 IS NULL) AND field6 <> 11", $subquery
            );
        }
    }



//Создаем подзапрос для сортировки
	$sortquery = '';
	if (isset($_REQUEST['sort'])) {
		if (substr_count($_REQUEST['sort'], '|') > 0) {
			$_REQUEST['sort'] = explode('|', $_REQUEST['sort']);
            $sfieldNums = [];
			for ($i = 0; $i < count($_REQUEST['sort']); $i++) {
				$sfieldNum1 = str_replace('sf', '', $_REQUEST['sort'][$i]);
				$ord = ($_REQUEST['sort'][$i] == 'sf' . intval($sfieldNum1)) ? 'ASC' : 'DESC';
				$sfieldNums[] = 'field' . str_replace('_r', '', $sfieldNum1) . ' ' . $ord;
			}
			$sortquery .= " ORDER BY " . implode(', ', $sfieldNums) ;
		} else {
			$sfieldNum1 = str_replace('sf', '', $_REQUEST['sort']);
			$ord = ($_REQUEST['sort'] == 'sf' . intval($sfieldNum1)) ? 'ASC' : 'DESC';
			$sfieldNum = str_replace('_r', '', $sfieldNum1);

			if (intval($sfieldNum) != 0) {
                $sortquery .= " ORDER BY field" . intval($sfieldNum) . " $ord";
                //echo $sortquery;
            }elseif($sfieldNum == 'user_id'){
                $sortquery .= " ORDER BY user_id ASC";
			} else {
				$sortquery .= " ORDER BY sort ASC, id DESC";
				//echo $sortquery;
			}
		}
	} else {
		if (strlen($row_cat_form1['sort_tab_s']) == 0) $row_cat_form1['sort_tab_s'] = 'DESC';
		if (strlen($row_cat_form1['sort_tab']) == '') $row_cat_form1['sort_tab'] = 'id';
		$sortquery = "ORDER BY " . $row_cat_form1['sort_tab'] . " " . $row_cat_form1['sort_tab_s'].", `sort` ASC";
	}

	if (strlen(trim($_GET['search'])) > 0) {
		//$subquery = el_genSearchQuery($catsQuery,$_GET['search']);
		$subquery .= " AND (
  MATCH(field17, field1, field32, field33, field34, field35, field36, field37, field38, field44, field45, field46)
  AGAINST('*" . $_GET['search'] . "*' IN BOOLEAN MODE) OR
   MATCH(field17, field1, field32, field33, field34, field35, field36, field37, field38, field44, field45, field46)
  AGAINST('*" . switcher_ru($_GET['search']) . "*' IN BOOLEAN MODE) OR
   MATCH(field17, field1, field32, field33, field34, field35, field36, field37, field38, field44, field45, field46)
  AGAINST('*" . switcher_en($_GET['search']) . "*' IN BOOLEAN MODE)) $addGroupFields ";
		$sortquery .= ($addSortFields == '') ? ", 
  MATCH(field17, field1, field32, field33, field34, field35, field36, field37, field38, field44, field45, field46)
  AGAINST('*" . $_GET['search'] . "*' IN BOOLEAN MODE) DESC" : ", " . implode(', ', $addSortFields);
	}
	return array($subquery, $sortquery);
}

function el_autoregistration(){
    //Авторегистрация, если юзер не авторизован
    if(intval($_SESSION['user_id']) == 0) {
        $user_exist = el_dbselect("SELECT * FROM catalog_users_data 
			WHERE field2 = '" . trim($_POST['mail']) . "'", 0, $user_exist, 'row', true);
        if (intval($user_exist['id']) == 0) {
            $newPass = el_genpass();
            $pass = str_replace("$1$", "", crypt(md5($newPass), '$1$'));
            $res = el_dbselect("INSERT INTO catalog_users_data 
			(active, site_id, cat, field1, field2, field3, field5, field8, field9, field10, field11,
			 field12, field13, field14, field15, field16, field17, field18, field19 ) VALUES 
			(1, 1, 3371, '" . $_POST['name'] . "', '" . $_POST['mail'] . "', '$pass', '" . $_POST['phone'] . "', 
			'" . $_POST['city'] . "', '" . $_POST['logist'] . "', '" . $_POST['payment'] . "', 
			'1', '" . $_POST['region'] . "', '" . $_POST['index'] . "', '" . $_POST['street'] . "', 
			'" . $_POST['house'] . "', '" . $_POST['flat'] . "', '" . $_POST['entrance'] . "', 
			'" . $_POST['floor'] . "', '" . $_POST['domofon'] . "')", 0, $res, 'result', true);
            $user_id = mysqli_insert_id($dbconn);
            $_SESSION['user_id'] = $user_id;
            $regMessage = '<p>Для Вас создан личный кабинет</p> 
			<p><a href="https://toptoy.ru/lk/istoriya-zakazov/">Вход в личный кабинет</a></p>
			<p>Логин: ' . $_POST['mail'] . '</p>
			<p>Пароль: ' . $newPass . ' (его можно поменять в разделе "Личные данные")</p>';

            //Отправка уведомления с новым паролем пользователю
            $letter_body = el_render('/tmpl/letter/letter1.php',
                ['caption' => 'Уважаемый/ая ' . $_POST['name'] . '!',
                    'text' => $regMessage,
                    'phone' => '+7 (499) 40-40-615',
                    'cznName' => 'TOPTOY.RU Для детей и родителей',
                    'buttonText' => 'Перейти в личный кабинет',
                    'buttonUrl' => 'http://' . $_SERVER['SERVER_NAME'] . '/lk/'
                ]
            );
            $mailResult = el_mail($_POST['mail'], 'Регистрация на сайте ' . $_SERVER['SERVER_NAME'],
                $letter_body, 'order@toptoy.ru', 'html', 'smtp', '', 'order@toptoy.ru');
        } else {
            $user_id = $user_exist['id'];
            $_SESSION['user_id'] = $user_id;
        }
        return $user_id;
    }else{
        return $_SESSION['user_id'];
    }
}

function el_getUpdateOrder($user_id, $ids, $totalSumm, $payType, $logist, $address){
    global $dbconn;
    $res = '';
    $exOrder = '';
    $user_ident = (intval($_SESSION['user_id']) > 0) ? "field2 = '".intval($_SESSION['user_id'])."'" : "field9 = '".session_id()."'";
    //Ищем номер текущего заказа или создаем новый
    //Ищем со способом оплаты картой онлайн и статусом "В обработке"
    if(intval($payType) == 2) {
        ///////////////////////////Неправильно ищет, находит брошенные и не оплаченные заказы. Например, 160 заказ
        $exOrder = el_dbselect("SELECT id FROM catalog_orders_data WHERE 
                   active = 1 AND field9 = '" . session_id() . "' AND field5 = 1 AND field8 = 2 ORDER BY id DESC",
            1, $exOrder, 'row', true);
        if (intval($exOrder['id']) > 0) {
            $newIdOrder = $exOrder['id'];
            //Обновляем user_id в заказе на случай, если произошла авторегистрация
            $exOrder = el_dbselect("UPDATE catalog_orders_data SET field2 = '$user_id', field4 = '$totalSumm' WHERE id = '$newIdOrder'",
                0, $exOrder, 'result', true);
        } else {
            //Вносим заказ в базу данных
            $res = el_dbselect("INSERT INTO catalog_orders_data 
			(site_id, cat, active, field1, field2, field4, field5, field6, field7, field8, field9, field10, field11, field12, field13, field14, field15) 
			VALUES 
			(1, 3392, 1, '" . date('Y-m-d H:i:s') . "', '" . $_SESSION['user_id'] . "', 
			'$totalSumm', 1, '" . addslashes($_POST['logistOrderId']) . "', '" . intval($_POST['logistType']) . "'
			, 2, '" . session_id() . "', '".addslashes($_POST['name'])."', '".addslashes($address)."', 
			'".intval($logist['field3'])."', '".addslashes($_POST['comment'])."', '".addslashes($_POST['mail'])."', '".addslashes($_POST['phone'])."')", 0,
                $res, 'result', true);
            $newIdOrder = mysqli_insert_id($dbconn);
        }
    }else{
        //Вносим заказ в базу данных в случае остальных способов оплаты
        $res = el_dbselect("INSERT INTO catalog_orders_data 
			(site_id, cat, active, field1, field2, field4, field5, field6, field7, field8, field9, field10, field11, field12, field13, field14, field15) VALUES 
			(1, 3392, 1, '" . date('Y-m-d H:i:s') . "', '" . $_SESSION['user_id'] . "', 
			'$totalSumm', 1, '" . addslashes($_POST['logistOrderId']) . "', '" . intval($_POST['logistType']) . "'
			, '" . intval($_POST['payment']) . "', '" . session_id() . "', '".addslashes($_POST['name'])."', '".addslashes($address)."', 
			'".intval($logist['field3'])."', '".addslashes($_POST['comment'])."', '".addslashes($_POST['mail'])."', '".addslashes($_POST['phone'])."')", 0,
            $res, 'result', true);
        $newIdOrder = mysqli_insert_id($dbconn);
    }

    $res = el_dbselect("UPDATE catalog_cart_data SET field8 = $newIdOrder, field2 = '$user_id' WHERE 
			field9 = '".session_id()."' AND (field8 IS NULL OR field8 = '') 
			AND field3 IN (" . implode(',', $ids) . ")", 0, $res, 'result', true);

    return $newIdOrder;
}

function el_orderIsComplete(){
    $res = '';
    $res = el_dbselect("SELECT id, field5 FROM catalog_orders_data WHERE 
    field9 = '".session_id()."' AND field8 = 2 ORDER BY id DESC", 1, $res, 'row', true);
    return array('orderId' => $res['id'], 'completed' => (is_array($_SESSION['orderInfo']) && intval($res['field5']) == 6));
}

function el_definingOwnGroupName($subject, $region, $city, $user_status, $post_index, $number): string
{
    $group_name = '';
    switch($user_status){
        case 11: $group_name = 'Администраторы'; break; //Администратор
        case 10: $group_name = 'И-'.$post_index.'-'.$number; break; //Пользователь
        case 9: $group_name = 'Р-'.$post_index.'-'.$number; break; //Куратор индекса
        case 8: $group_name = 'Н-'.$subject.'-'.$number; break; //Куратор района
        case 7: $group_name = 'С-'.$subject.'-'.$number; break; //Куратор нас. пункт
        case 6: $group_name = 'Г-'.$number; break; //Куратор субъекта
        case 5: $group_name = 'Ц-'.$number; break; //Куратор страна
        case 4: $group_name = 'А'; break; //Куратор центра
    }
    return $group_name;
}

function el_definingSubordinateGroupName($subject, $region, $city, $user_status, $post_index, $number): string
{
    $group_name = '';
    switch($user_status){
        case 11: $group_name = 'Администраторы'; break; //Администратор
        case 10: //Пользователь
        case 9: $group_name = 'И-'.$post_index.'-'.$number; break; //Куратор индекса
        case 8: $group_name = 'Р-'.$post_index.'-'.$number; break; //Куратор района
        case 7: $group_name = 'Н-'.$subject.'-'.$number; break; //Куратор нас. пункт
        case 6: $group_name = 'С-'.$subject.'-'.$number; break; //Куратор субъекта
        case 5: $group_name = 'Г-'.$number; break; //Куратор страна
        case 4: $group_name = 'Ц-'.$number; break; //Куратор центра
    }
    return $group_name;
}

function el_createUserGroup($subject, $region, $city, $post_index, $number = 1, $user_status = 10, $group_type = 'own')
{
    global $dbconn;
    $result = false;
    $curators = $exist = null;
    $ins = [];
    $server_name = str_ireplace('www.', '', strtolower($_SERVER['SERVER_NAME']));

    if ($group_type == 'own') {
        $group_name = el_definingOwnGroupName($subject, $region, $city,
            $user_status, $post_index, $number);
    } else {
        $group_name = el_definingSubordinateGroupName($subject, $region, $city,
            $user_status, $post_index, $number);
    }
    $group_param = explode('-', $group_name);
    $group_number = array_pop($group_param);
    $group_name = implode('-', $group_param);

    $exist = el_dbselect("SELECT id FROM catalog_groups_data WHERE 
        field1 = '$group_name' AND field2 = '$group_number'", 0, $exist, 'row', true);

    if (intval($exist['id']) == 0){
        $ins = array(
            'site_id' => 1,
            'cat' => 420,
            'active' => 1,
            'path' => trim($group_name),
            'field1' => trim($group_name),
            'field2' => trim($group_number)
        );
        $res = el_dbinsert('catalog_groups_data', $ins);

        if ($res) {
            $result = mysqli_insert_id($dbconn);

            //Находим адреса кураторов района и центра
            $curators = el_dbselect("SELECT field15 FROM catalog_users_data 
            WHERE field9 = '$region' AND field6 IN (4,7,8)", 0, $curators, 'result', true);
            if (el_dbnumrows($curators) > 0) {
                $rc = el_dbfetch($curators);
                $emails = [];
                do {
                    $emails[] = $rc['field15'];
                } while ($rc = el_dbfetch($curators));

                //Делаем рассылку
                $regMessage = '<p>Автоматически создана новая группа №' . $post_index . '_' . $number .
                    '</p><p>Назначьте куратора для этой группы.</p>';

                //Отправка уведомления с новым паролем пользователю
                $letter_body = el_render('/tmpl/letter/letter.php',
                    ['caption' => 'В новой группе требуется назначить куратора.',
                        'text' => $regMessage,
                        'phone' => '',
                        'cznName' => 'ПНМ «ПораНаМ»',
                        'buttonText' => 'Перейти в личный кабинет',
                        'buttonUrl' => 'http://' . $_SERVER['SERVER_NAME'] . '/lichnyy-kabinet/'
                    ]
                );
                $mailResult = el_mail(implode(',', $emails),
                    'Создана новая группа пользователей на сайте ' . $_SERVER['SERVER_NAME'],
                    $letter_body, 'info@' . $server_name, 'html', 'smtp',
                    '', 'info@' . $server_name, 'ПНМ «ПораНаМ»');
            }
        }
    }else{
        $result = $exist['id'];
    }

	return $result;

}

function getSubGroupsByUser($userId): array
{
    $userId = intval($userId);

    $result = null;
    $userInfo = null;
    static $groups = [];
    if($userId > 0) {
        $userInfo = el_dbselect("SELECT * FROM catalog_users_data WHERE id = '$userId'", 0, $userInfo, 'row', true);
        $subQuery = '';
        $groups[] = $userInfo['field25'];

        $query = "SELECT id, field16, field25 FROM catalog_users_data WHERE field24 = '$userId' $subQuery"; //field24 = '$userId'field16 = '".$userInfo['field25']."'

        $result = el_dbselect($query, 0, $result, 'result', true);


        while ($row = el_dbfetch($result)) {

            if (intval($row['field25']) > 0 && $row['field25'] != $row['id']
                && !in_array($row['field25'], $groups)) {
                //$groups[] = $row['field16'];
                $groups[] = $row['field25'];
                //echo $row['id'].' '.$row['field25'].'<hr>';
                // Рекурсивно вызываем функцию для каждой группы
                if ($userInfo['field6'] != 9) {
                    $subGroups = getSubGroupsByUser($row['id']);
                    $groups = array_merge($groups, $subGroups);
                }
            }

        }
    }
    return array_unique($groups);
}

function getSubGroupsNamesByUser($userId): array
{
    $userId = intval($userId);
    $result = null;
    $userInfo = null;
    static $output = [];

    $userInfo = el_dbselect("SELECT * FROM catalog_users_data WHERE id = '$userId'", 0, $userInfo, 'row', true);
    $subQuery = '';
    $groups[] = $userInfo['field25'];

    $query = "SELECT u.id AS `id`, u.field16 AS `group`, u.field25 AS `groups`, 
	g.id AS gid, g.field1 AS `name`, g.field2 AS `number`
	 FROM
	 catalog_users_data AS u, catalog_groups_data AS g
	 WHERE (u.field25 = g.id OR u.field16 = g.id) AND field24 = '$userId' $subQuery"; 

    $result = el_dbselect($query, 0, $result, 'result', true);


    while ($row = el_dbfetch($result)) {

        if(intval($row['groups']) > 0 && $row['groups'] != $row['id']
            && !in_array($row['groups'], $groups)) {
            //$groups[] = $row['field16'];
            $groups[] = $row['groups'];
			$output[] = [$row['gid'], $row['name'].(intval($row['number']) > 0 ? '-'.$row['number'] : '')];
            //echo $row['id'].' '.$row['field25'].'<hr>';
            // Рекурсивно вызываем функцию для каждой группы
            if($userInfo['group'] != 9) {
                $subGroups = getSubGroupsNamesByUser($row['id']);
                $output = array_merge($output, $subGroups);
            }
        }

    }

    return array_unique($output);
}

//Генация синонима ID пользователя для формирования реферральной ссылки
function el_genSinonim($id){
    $sin = el_genpass(10);
    $exist = el_dbselect("SELECT COUNT(id) AS c FROM catalog_users_data WHERE field23 = '$sin'", 0, $exist, 'row', true);
    if(intval($exist['c']) > 0){
        return el_genSinonim($id);
    }else{
        $result = el_dbselect("UPDATE catalog_users_data SET field23 = '$sin' WHERE id = $id", 0, $result, 'result', true);
        return $sin;
    }
}


/**
 * Начисление баллов пользователям за действия или донат
 *
 * @param int $score_id - id баллов из таблицы catalog_scores_data
 * @param string $user_id - id пользователя из таблицы catalog_users_data
 * @return bool
 */
function el_earnpoints(int $score_id, string $user_id): bool
{
    $res = null;
    $scores = null;

    $scores = el_dbselect("SELECT field3 FROM catalog_scores_data WHERE id = '$score_id' 
                                AND active = 1", 0, $scores, 'row', true);
    if(isset($scores['field3'])){
        $res = el_dbselect("UPDATE catalog_users_data SET field18 = IFNULL(field18, 0) + ".$scores['field3']." 
        WHERE id = '$user_id'", 0, $res, 'result', true);
        return true;
    }
    return false;
}

function el_removepoints(int $score_id, string $user_id): bool
{
    $res = null;
    $scores = null;
    $user = null;

    $scores = el_dbselect("SELECT field3 FROM catalog_scores_data WHERE id = '$score_id' 
                                AND active = 1", 0, $scores, 'row', true
    );
    $user = el_dbselect("SELECT field18 FROM catalog_users_data WHERE id = $user_id", 0, $user, 'row');
    if (isset($scores['field3']) && intval($user['field18']) >= $scores['field3']) {
        $res = el_dbselect('UPDATE catalog_users_data SET field18 = field18 - ' . $scores['field3'] . " 
        WHERE id = '$user_id'", 0, $res, 'result', true);
        return true;
    }
    return false;
}

function el_getUserIdByVisual($visual_id): int
{
    $res = null;
    $res = el_dbselect("SELECT id FROM catalog_user_data WHERE user_id = '".addslashes($visual_id)."'",
        0, $res, 'row', true);
    return intval($res['id']);
}

?>