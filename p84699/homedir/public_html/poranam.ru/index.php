<?php
@session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php');

//Выход из сессии
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location:/');
}

//Ajax-запрос перенаправляем на скрипт, указанный в $_POST['action']
if (intval($_POST['ajax']) == 1 && isset($_SERVER['HTTP_X_REQUESTED_WITH'])
    && !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
    && $_SESSION['csrf-token'] == getallheaders()['x-csrf-token']) {

    if (ob_get_length()) ob_clean();
    header("Content-type: text/html; charset=utf-8");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);

    switch($_POST['mode']){
        case 'popup':
            $dialogUrl = $_SERVER['DOCUMENT_ROOT'] . '/tmpl/page/dialogs/'. $_POST['url']. '.php';
            if (is_file($dialogUrl)) {
                include $dialogUrl;
            }
            break;
        default:
            $_POST['action'] = str_replace(array('.', '/'), '', $_POST['action']);
            $ajaxHandler = $_SERVER['DOCUMENT_ROOT'] . '/modules/ajaxHandlers/' . $_POST['action'].'.php';
            if (is_file($ajaxHandler)) {
                include $ajaxHandler;
            }
            break;
    }

}else {
    //el_strongcleanvars1();
    //Находим текст для этой страницы
    //$serv = $_SERVER['SERVER_NAME'];
    //$path = str_replace($serv, '', $_SERVER['REQUEST_URI']);
    //$path = substr($path, 0, -1);
    //$path = preg_replace('/\/\?.*/', '', str_replace('/index.ph', '', $path));
    //if (substr_count($path, '.htm') > 0 || substr_count($path, '.sit') > 0) {
    //	$pArr = explode('/', $path);
    //	array_pop($pArr);
    //	$path = implode('/', $pArr);
    //}

    //Определяем поддомен
    $pathArr = explode('.', strtolower($_SERVER['SERVER_NAME']));
    if (count($pathArr) > 2) {
        $subDomain = $pathArr[0];
    } else {
        $subDomain = strtolower($_SERVER['SERVER_NAME']);
    }

    $path = (isset($_GET['url'])) ? '/' . preg_replace(array('#(.*)\/$#', '/(.*)\/page([0-9]+)$/'), '$1', $_GET['url']) : '';

//echo $path;
    preg_match_all('/(.*)\/page([0-9]+)\/$/imU', $_GET['url'], $pn);
    $_GET['pn'] = intval($pn[2][0]);

    if(isset($_GET['invite']) && strlen(trim($_GET['invite'])) > 0){
        $refer = el_dbselect("SELECT id, field11 FROM catalog_users_data WHERE field23 = '".$_GET['invite']."'",
            0, $refer, 'row', true);
        $_SESSION['referrer'] = $refer['field11'].'-'.$refer['id'];
        setcookie('ruid',  $refer['field11'].'-'.$refer['id'], time()+86400 * 30);
        header('Location: /');
    }

    $query_dbcontent = "SELECT content.*, sites.* FROM content, sites WHERE content.path = '$path' 
	AND sites.domain = '" . $subDomain . "'
	AND sites.id = content.site_id";
    $dbcontent = el_dbselect($query_dbcontent, 0, $res, 'result', true);

    //Если для запрашиваемого сайта есть контент
    if (el_dbnumrows($dbcontent) > 0) {
        $row_dbcontent = el_dbfetch($dbcontent);
        $_SESSION['view_site_id'] = $GLOBALS['view_site_id'] = intval($row_dbcontent['site_id']);
        $_SESSION['work_time'] = json_decode($row_dbcontent['work_times']);
        $totalRows_dbcontent = el_dbnumrows($dbcontent);
        $cat = $GLOBALS['cat'] = $row_dbcontent['cat'];
        $catinfo = '';
        $catinfo = el_dbselect("SELECT * FROM cat WHERE id='" . $row_dbcontent['cat'] . "'", 0, $catinfo, 'result', true);
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


        if (strlen($row_dbcontent['view']) > 0 && substr_count($row_dbcontent['view'], '0') == 0) {
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
                $row_dbcontent['template'] = 'login.php';
            }

            if (isset($_SESSION['login']) && @substr_count($row_dbcontent['view'], $_SESSION['ulevel']) == 0
                && isset($_POST['user_enter'])) {
                $row_dbcontent['caption'] = 'Закрытый раздел';
                $row_dbcontent['text'] = 'Извините, у Вас недостаточно прав для просмотра этого раздела.';
                $row_dbcontent['kod'] = '';
                $totalRows_dbchildmenu = 0;
                $row_dbcontent['template'] = 'login.php';
            }
        }

        //Создаем токен CSRF в cookie
        $csrfToken = el_buildToken();
        $_SESSION['csrf-token'] = $csrfToken;
        setcookie('CSRF-TOKEN', $csrfToken, 0, '/', $_SERVER['SERVER_NAME'], true);

        if (isset($_GET['clearcache'])) {
            el_clearcache();
            el_genSiteMap();
        }
        if ($row_dbcontent['template'] == '') {
            $row_dbcontent['template'] = 'mainpage.php';
            $row_dbcontent['caption'] = '404. Неверный адрес страницы.';
            $row_dbcontent['text'] = 'Вы попали сюда по неверной ссылке. Пожалуйста, начните с <a href="/">главной страницы</a>.';
        }

        if(intval($row_dbcontent['active']) == 0){
            include $_SERVER['DOCUMENT_ROOT'] . "/tmpl/page/stub.php";
        }else{
            include $_SERVER['DOCUMENT_ROOT'] . "/tmpl/page/" . $row_dbcontent['template'];
        }

    } else {
        header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
        include $_SERVER['DOCUMENT_ROOT'] . "/tmpl/page/404.php";
    }
}
?>
