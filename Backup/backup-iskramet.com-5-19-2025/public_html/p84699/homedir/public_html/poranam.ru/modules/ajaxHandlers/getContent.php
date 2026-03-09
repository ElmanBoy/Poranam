<?php
@session_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';
$_GET['url'] = $_POST['url'];
//opcache_reset();
$main = '';

//Определяем поддомен
$pathArr = explode('.', strtolower($_SERVER['SERVER_NAME']));
if (count($pathArr) > 2) {
	$subDomain = $pathArr[0];
} else {
	$subDomain = strtolower($_SERVER['SERVER_NAME']);
}

//Разбираем url
$pathArr = explode('?', $_GET['url']);
if(strlen($pathArr[1]) > 0){
    parse_str($pathArr[1], $_GET);
}
$_GET['url'] = $pathArr[0];

if(substr_count($_GET['url'], '/page') > 0){
	preg_match('#/page([0-9]+)(/?)$#', $pathArr[0], $pageNum);
	$_GET['pn'] = $pageNum[1];
}
if(substr_count($_GET['url'], '.html') > 0){
	preg_match('#/([^/]+)\.html$#', $pathArr[0], $goodPath);
	$_GET['path'] = $goodPath[1];
}
$path = (isset($_GET['url'])) ? preg_replace(array('#(.*)\/$#', '/(.*)\/page([0-9]+)$/', '#(.*)\/([^\/]+)\.html$#'),'$1', $pathArr[0]) : '';


$_GET['url'] = $path;
//print_r($_GET);

$query_dbcontent = "SELECT content.*, sites.* FROM content, sites WHERE content.path = '$path' 
	AND sites.domain = '" . $subDomain . "'
	AND sites.id = content.site_id";
$dbcontent = el_dbselect($query_dbcontent, 0, $res, 'result', true);

ob_start(null, 0, PHP_OUTPUT_HANDLER_REMOVABLE );
//Если для запрашиваемого сайта есть контент
if (el_dbnumrows($dbcontent) > 0) {
	$row_dbcontent = el_dbfetch($dbcontent);
	$_SESSION['view_site_id'] = $GLOBALS['view_site_id'] = intval($row_dbcontent['site_id']);
	$totalRows_dbcontent = el_dbnumrows($dbcontent);
	$cat = $GLOBALS['cat'] = $row_dbcontent['cat'];
	if(strlen(trim($_GET['path'])) == 0) {
		$_SESSION['caption'] = $row_dbcontent['caption'];
	}
	$catalog_id = str_replace("catalog", "", $row_dbcontent['kod']);
	$catinfo = '';
	$catinfo = el_dbselect("SELECT * FROM cat WHERE id='" . $row_dbcontent['cat'] . "'", 0, $catinfo, 'result', true);
	$row_catinfo = el_dbfetch($catinfo);
	if (strlen($row_catinfo['redirect']) > 0) {
		header('Location: ' . $row_catinfo['redirect']);
	}

	if (strlen($row_dbcontent['view']) > 0 && substr_count($row_dbcontent['view'], 0) == 0) {
		if (!isset($_SESSION['login'])) {
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
                      <td><input type="Submit" name="Submit" class="btn btn-primary btn-large" value="Вход" /><br><br></td>
                    </tr>
                    </form>
              </table></center>';
			$row_dbcontent['kod'] = '';
			$totalRows_dbchildmenu = 0;
		}
		if (isset($_SESSION['login']) && @substr_count($row_dbcontent['view'], $_SESSION['ulevel']) == 0
			&& isset($_POST['user_enter'])) {
			$row_dbcontent['caption'] = 'Закрытый раздел';
			$row_dbcontent['text'] = 'Извините, у Вас недостаточно прав для просмотра этого раздела.';
			$row_dbcontent['kod'] = '';
			$totalRows_dbchildmenu = 0;
		}
	}

	if ($row_dbcontent['template'] == '') {
		$row_dbcontent['template'] = 'mainpage.php';
		$row_dbcontent['caption'] = '404. Неверный адрес страницы.';
		$row_dbcontent['text'] = 'Вы попали сюда по неверной ссылке. Пожалуйста, начните с <a href="/">главной страницы</a>.';
	}

	if(intval($row_dbcontent['active']) == 0){
		include $_SERVER['DOCUMENT_ROOT'] . "/tmpl/page/stub.php";
	}else{
		require_once $_SERVER['DOCUMENT_ROOT'] . "/tmpl/page/" . str_replace('.php', '_ajax.php', $row_dbcontent['template']);
	}

} else {
	include $_SERVER['DOCUMENT_ROOT'] . "/tmpl/page/404_ajax.php";
}
$main .= ob_get_contents();
ob_end_clean();

echo json_encode(array('url' => $_POST['url'], 'title' => el_meta('getTitle'), 'caption' =>
	$_SESSION['caption'], 'main' => $main));