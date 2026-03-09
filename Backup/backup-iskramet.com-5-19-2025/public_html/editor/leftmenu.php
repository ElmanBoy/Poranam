<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php');

$query_access1 = "SELECT * FROM userstatus";
$access1 = el_dbselect($query_access1, 0, $access1);
$row_access1 = el_dbfetch($access1);
$arreqlevel = array();
do {
    array_push($arreqlevel, $row_access1['level']);
} while ($row_access1 = el_dbfetch($access1));

$requiredUserLevel = $arreqlevel;
// $requiredUserLevel = array(1, 2); 
include("secure/secure.php");
$work_mode = (isset($submit)) ? "write" : "read";
el_reg_work($work_mode, $login, $_GET['cat']);
$currentPage = $_SERVER["PHP_SELF"];

$query_modules = "SELECT * FROM modules ORDER BY sort ASC";
$modules = el_dbselect($query_modules, 0, $modules);
$row_modules = el_dbfetch($modules);
$totalRows_modules = mysqli_num_rows($modules);

function user_access ( $allow_statuses = array() )
{
    return in_array($_SESSION['user_level'], $allow_statuses);
}

?>
<!doctype html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script src="/js/jquery-1.11.0.min.js"></script>
    <script src="/js/tooltip.js"></script>
    <link href="style.css" rel="stylesheet" type="text/css">
    <link href="leftmenu-01.css" rel="stylesheet" type="text/css">
    <script language="javascript">
        function hide(nomer) {
            if (document.getElementById(nomer).style.display == "none") {
                document.getElementById(nomer).style.display = "block";
                document.getElementById(nomer + "i").src = "img/up.gif"
            } else {
                document.getElementById(nomer).style.display = "none";
                document.getElementById(nomer + "i").src = "img/down.gif"
            }
        }

        function MM_displayStatusMsg(msgStr) { //v1.0
            status = msgStr;
            document.MM_returnValue = true;
        }

        function opclose(id) {
            if (document.getElementById("menudiv" + id).style.display == "none") {
                document.cookie = "idshow[" + id + "]=Y; expires=Thu, 31 Dec 2120 23:59:59 GMT; path=/editor/;";
                document.getElementById("menudiv" + id).style.display = "block";
                document.getElementById("menuimg" + id).src = "img/leftmenu_minus.gif"
            } else {
                document.cookie = "idshow[" + id + "]=N; expires=Thu, 31 Dec 2120 23:59:59 GMT; path=/editor/;";
                document.getElementById("menudiv" + id).style.display = "none";
                document.getElementById("menuimg" + id).src = "img/leftmenu_plus.gif"
            }
        }

        function opclosetree(id) {
            if (document.getElementById("menudivtree" + id).style.display == "none") {
                document.cookie = "idshowtree[" + id + "]=Y; expires=Thu, 31 Dec 2120 23:59:59 GMT; path=/editor/;";
                document.getElementById("menudivtree" + id).style.display = "block";
                document.getElementById("menuimgtree" + id).innerText = "remove";
            } else {
                document.cookie = "idshowtree[" + id + "]=N; expires=Thu, 31 Dec 2120 23:59:59 GMT; path=/editor/;";
                document.getElementById("menudivtree" + id).style.display = "none";
                document.getElementById("menuimgtree" + id).innerText = "add";
            }
            ;
        }

        function MM_reloadPage(init) {  //reloads the window if Nav4 resized
            if (init == true) with (navigator) {
                if ((appName == "Netscape") && (parseInt(appVersion) == 4)) {
                    document.MM_pgW = innerWidth;
                    document.MM_pgH = innerHeight;
                    onresize = MM_reloadPage;
                }
            }
            else if (innerWidth != document.MM_pgW || innerHeight != document.MM_pgH) location.reload();
        }

        MM_reloadPage(true);

        $(document).ready(function () {
            $('*').tooltip({showURL: false});
            $(".captlink, .capt, .subcapt").on("click", function () {
                $(".captlink, .capt, .subcapt").removeClass("active");
                $(this).addClass("active").find("a").addClass("active");
                $(this).parents(".subsect").prev(".capt").addClass("active");
            })
        });
    </script>
</head>

<body onmouseover="MM_displayStatusMsg('');return document.MM_returnValue">
<?php
if (user_access([0, 1])) {
    /*?>
    <div class="capt">
        <i class="material-icons">dynamic_feed</i>
        <a href="sites.php" target="Main" title="Управление сайтами" class="captlink">Управление сайтами</a>
    </div>
    <?*/
}
if (user_access([0, 1, 2, 3])) { ?>
    <div class="capt" id="menuadmin"<?//=($_SESSION['user_level'] < 2) ? ' style="display: none"' : ''?>>
        <i class="material-icons">vertical_split</i>
        <a href="menuadmin.php?site_id=<?= intval($_SESSION['site_id']) ?>" target="Main" title="Управление структурой сайта" class="captlink">Управление разделами</a>
    </div>
    <?php
}
if (user_access([0, 1])) {
    ?>
    <? /*div class="capt">
        <i class="material-icons">info</i>
        <a href="menuadminGlobal.php?site_id=1" target="Main" title="Информация, публикуемая на всех сайтах" class="captlink">Общая информация</a>
    </div*/?>
    <div class="capt">
        <i class="material-icons">folder</i>
        <a href="menuadminRegister.php?site_id=1" target="Main" title="Служебные справочники" class="captlink">Справочники</a>
    </div>
    <?
}
if (user_access([0, 1])) {
    ?>
    <div class="capt" id="infoblocks">
        <i class="material-icons">layers</i>
        <a href="infoblocks.php?site_id=<?=intval($_SESSION['site_id'])?>" target="Main" title="Инфоблоки сайта" class="captlink">Инфоблоки сайта</a>
    </div>
    <?
}
?>
<?php
/*
<div class="capt">
<i class="material-icons">contact_support</i>
<a href="support.php" target="Main" title="Обращения в техподдержку" class="captlink">Техническая поддержка</a>
</div>
<?php
/*apt" title="Управление файлами на сайте">
    <i class="material-icons">face</i>
    <a href="upfile.php" target="Main" class="captlink">Файлы</a>
</div?>
<div class="capt" onClick="opclosetree('8')" title="Свернуть/Развернуть раздел">
    <i class="material-icons">face</i>
    Реклама
    <div class="plusminus">
        <i id="menuimgtree8" class="material-icons"><?= ($_COOKIE['idshowtree'][8] != " Y ") ? "add" : "remove" ?></i>
    </div>
</div>
<div id="menudivtree8" style="display:<?= ($_COOKIE['idshowtree'][8] != " Y ") ? "none " : "inherit" ?>;" class="subsect">
    <div class="subsect_item">
        <i class="material-icons">face</i>
        <a href="/editor/modules/advert/index.php" target="Main" class="subcapt">Площадки</a>
    </div>
    <div class="subsect_item">
        <i class="material-icons">face</i>
        <a href="/editor/modules/advert/banners.php" target="Main" class="subcapt">Баннеры</a>
    </div>
</div*/?>
    <?

/*if (is_dir($_SERVER['DOCUMENT_ROOT'] . '/editor/modules/subscribe')) { ?>
    <div class="capt" onClick="opclosetree('7')" title="Свернуть/Развернуть раздел">
        <i class="material-icons">face</i>
        Рассылки
        <div class="plusminus">
            <i id="menuimgtree7" class="material-icons"><?= ($_COOKIE['idshowtree'][7] != " Y ") ? "add" : "remove" ?></i>
        </div>
    </div>
    <div id="menudivtree7" style="display:<?= ($_COOKIE['idshowtree'][7] != " Y ") ? "none " : "inherit" ?>;" class="subsect">
        <div class="subsect_item">
            <i class="material-icons">face</i>
            <a href="/editor/modules/subscribe/index.php" target="Main" class="subcapt">Подписчики</a>
        </div>
        <div class="subsect_item"><!-- активный элемент-->
            <i class="material-icons">face</i>
            <a href="/editor/modules/subscribe/templates.php" target="Main" class="subcapt">Шаблоны писем </a>
        </div>
        <div class="subsect_item">
            <i class="material-icons">face</i>
            <a href="/editor/modules/subscribe/themes.php" target="Main" class="subcapt">Темы подписки</a>
        </div>
        <div class="subsect_item">
            <i class="material-icons">face</i>
            <a href="/editor/modules/subscribe/send.php" target="Main" class="subcapt">Выпуски</a>
        </div>
    </div>
<? } */
if (user_access([0, 1])) {
    ?>
    <div class="capt" onClick="opclosetree('6')" title="Свернуть/Развернуть раздел">
        <i class="material-icons">settings_applications</i>
        Настройки
        <div class="plusminus">
            <i id="menuimgtree6" class="material-icons"><?= ($_COOKIE['idshowtree'][6] != " Y ") ? "add" : "remove" ?></i>
        </div>
    </div>
    <div id="menudivtree6" style="display:<?= ($_COOKIE['idshowtree'][6] != " Y ") ? "none " : "inherit" ?>;" class="subsect">
        <div class="subsect_item">
            <i class="material-icons">extension</i>
            <a href="modules.php" target="Main" class="subcapt">Модули</a>
        </div>
        <div class="subsect_item">
            <i class="material-icons">view_quilt</i>
            <a href="templates.php" target="Main" class="subcapt">Шаблоны страниц</a>
        </div>
        <div class="subsect_item">
            <i class="material-icons">people</i>
            <a href="users.php" target="Main" class="subcapt">Пользователи</a>
        </div>
        <div class="subsect_item">
            <i class="material-icons">face</i>
            <a href="/editor/e_modules/logging/log.php" target="Main" class="subcapt">Журнал событий</a>
        </div>
        <?php
        if (user_access([0])) {
            ?>
            <div class="subsect_item">
                <i class="material-icons">face</i>
                <a href="modules/catalog/catalogs.php" target="Main" class="subcapt">Каталоги</a>
            </div>
            <div class="subsect_item">
                <i class="material-icons">face</i>
                <a href="modules/forms/catalogs.php" target="Main" class="subcapt">Web-формы</a>
            </div>
            <div class="subsect_item">
                <i class="material-icons">face</i>
                <a href="/editor/e_modules/dbserv.php" target="Main" class="subcapt">База данных</a>
            </div>
            <div class="subsect_item">
                <i class="material-icons">face</i>
                <a href="phpinfo.php" target="Main" class="subcapt">phpinfo</a>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
}elseif(user_access([2])){
    ?>
    <div class="capt">
        <i class="material-icons">people</i>
        <a href="users.php" title="Управление пользователями" target="Main" class="captlink">Пользователи</a>
    </div>
<?
}
?>
<div class="capt">
    <i class="material-icons">exit_to_app</i>
    <a href="/editor/secure/logout.php" target="_top" title="Выйти из административного раздела" class="captlink">Выход</a>
</div>
<!--<div class="account">
    Вы вошли как
    <?

$query_user = "SELECT fio, userlevel FROM phpSP_users WHERE user='" . $_SESSION['login'] . "'";
$user = el_dbselect($query_user, 0, $user, 'result', true);
$row_user = el_dbfetch($user);
switch ($row_user['userlevel']) {
    case 1:
        $lev = "Администратор: ";
        break;
    case 2:
        $lev = "Редактор: ";
        break;
    case 3:
        $lev = "Пользователь: ";
        break;
}

echo "<font>" . $lev . "</font><br><b>" . $row_user['fio'] . "</b>" ?>
    </div>-->


</body>

</html>