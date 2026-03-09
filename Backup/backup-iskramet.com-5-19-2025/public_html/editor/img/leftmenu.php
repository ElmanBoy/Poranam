<?php require_once('../Connections/dbconn.php');

$query_access1 = "SELECT * FROM userstatus";
$access1 = el_dbselect($query_access1, 0, $access1);
$row_access1 = el_dbfetch($access1);
$arreqlevel = array();
do {
	array_push($arreqlevel, $row_access1['id']);
} while ($row_access1 = el_dbfetch($access1));

$requiredUserLevel = $arreqlevel;
// $requiredUserLevel = array(1, 2); 
include("secure/secure.php");
(isset($submit)) ? $work_mode = "write" : $work_mode = "read";
el_reg_work($work_mode, $login, $_GET['cat']);
$currentPage = $_SERVER["PHP_SELF"];

$query_modules = "SELECT * FROM modules ORDER BY sort ASC";
$modules = el_dbselect($query_modules, 0, $modules);
$row_modules = el_dbfetch($modules);
$totalRows_modules = mysqli_num_rows($modules);
?>
	<!doctype html>
	<html>

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link href="style.css" rel="stylesheet" type="text/css">
		<link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:300,400,700&display=swap&subset=cyrillic" rel="stylesheet">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link href="leftmenu-01.css" rel="stylesheet" type="text/css">
		<script language="javascript">
			<!--
			function hide(nomer) {
				if (document.getElementById(nomer).style.display == "none") {
					document.getElementById(nomer).style.display = "block";
					document.getElementById(nomer + "i").src = "img/up.gif"
				}
				else {
					document.getElementById(nomer).style.display = "none";
					document.getElementById(nomer + "i").src = "img/down.gif"
				};
			}

			function MM_displayStatusMsg(msgStr) { //v1.0
				status = msgStr;
				document.MM_returnValue = true;
			}

			function flvFPW1() { //v1.44
				// Copyright 2002-2004, Marja Ribbers-de Vroed, FlevOOware (www.flevooware.nl/dreamweaver/)
				var v1 = arguments
					, v2 = v1[2].split(",")
					, v3 = (v1.length > 3) ? v1[3] : false
					, v4 = (v1.length > 4) ? parseInt(v1[4]) : 0
					, v5 = (v1.length > 5) ? parseInt(v1[5]) : 0
					, v6, v7 = 0
					, v8, v9, v10, v11, v12, v13, v14, v15, v16;
				v11 = new Array("width,left," + v4, "height,top," + v5);
				for (i = 0; i < v11.length; i++) {
					v12 = v11[i].split(",");
					l_iTarget = parseInt(v12[2]);
					if (l_iTarget > 1 || v1[2].indexOf("%") > -1) {
						v13 = eval("screen." + v12[0]);
						for (v6 = 0; v6 < v2.length; v6++) {
							v10 = v2[v6].split("=");
							if (v10[0] == v12[0]) {
								v14 = parseInt(v10[1]);
								if (v10[1].indexOf("%") > -1) {
									v14 = (v14 / 100) * v13;
									v2[v6] = v12[0] + "=" + v14;
								}
							}
							if (v10[0] == v12[1]) {
								v16 = parseInt(v10[1]);
								v15 = v6;
							}
						}
						if (l_iTarget == 2) {
							v7 = (v13 - v14) / 2;
							v15 = v2.length;
						}
						else if (l_iTarget == 3) {
							v7 = v13 - v14 - v16;
						}
						v2[v15] = v12[1] + "=" + v7;
					}
				}
				v8 = v2.join(",");
				v9 = window.open(v1[0], v1[1], v8);
				if (v3) {
					v9.focus();
				}
				document.MM_returnValue = false;
				return v9;
			}
			//-->
		</script>
		<SCRIPT language=JavaScript>
			<!--
			function opclose(id) {
				if (document.getElementById("menudiv" + id).style.display == "none") {
					document.cookie = "idshow[" + id + "]=Y; expires=Thu, 31 Dec 2020 23:59:59 GMT; path=/editor/;";
					document.getElementById("menudiv" + id).style.display = "block";
					document.getElementById("menuimg" + id).src = "img/leftmenu_minus.gif"
				}
				else {
					document.cookie = "idshow[" + id + "]=N; expires=Thu, 31 Dec 2020 23:59:59 GMT; path=/editor/;";
					document.getElementById("menudiv" + id).style.display = "none";
					document.getElementById("menuimg" + id).src = "img/leftmenu_plus.gif"
				};
			}

			function opclosetree(id) {
				if (document.getElementById("menudivtree" + id).style.display == "none") {
					document.cookie = "idshowtree[" + id + "]=Y; expires=Thu, 31 Dec 2020 23:59:59 GMT; path=/editor/;";
					document.getElementById("menudivtree" + id).style.display = "block";
					document.getElementById("menuimgtree" + id).src = "img/leftmenu_minus.gif"
				}
				else {
					document.cookie = "idshowtree[" + id + "]=N; expires=Thu, 31 Dec 2020 23:59:59 GMT; path=/editor/;";
					document.getElementById("menudivtree" + id).style.display = "none";
					document.getElementById("menuimgtree" + id).src = "img/leftmenu_plus.gif"
				};
			}
			//-->
		</SCRIPT>
		<script language="JavaScript" type="text/JavaScript">
			<!--


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

		//-->
		</script>
	</head>

	<body onmouseover="MM_displayStatusMsg('');return document.MM_returnValue">
		<div class="capt">
        	<i class="material-icons">face</i>
		        <a href="menuadmin.php" target="Main" title="Управление структурой сайта" class="captlink">Управление разделами</a>
        </div>
		<div class="capt">
        	<i class="material-icons">face</i>
            <a href="infoblocks.php" target="Main" title="Инфоблоки сайта" class="captlink">Инфоблоки сайта</a>
        </div>
		<div class="capt" title="Управление файлами на сайте">
	        <i class="material-icons">face</i>
        	<a href="upfile.php" target="Main" class="captlink">Файлы</a>
        </div>
<!-- **** родительский заголовок-->
        <div class="capt" onClick="opclosetree('8')" title="Свернуть/Развернуть раздел">
        <i class="material-icons">face</i>
        	Реклама 
            <div class="plusminus">
            <i id="menuimgtree8" title="Свернуть/Развернуть раздел" class="material-icons"><?= ($_COOKIE['idshowtree'][8] != " Y ") ? "add" : "remove" ?></i>
            <!-- <img src="img/< ?= ($_COOKIE['idshowtree'][8] != " Y ") ? "leftmenu_plus.gif " : "leftmenu_minus.gif " ?>" title="Свернуть/Развернуть раздел" align="absmiddle">-->
            </div>
        </div>
<!-- **** -->
        <div id="menudivtree8" style="display:<?= ($_COOKIE['idshowtree'][8] != " Y ") ? "none " : "inherit" ?>;" class="subsect">
        	<div class="subsect_item">
            <i class="material-icons">face</i>
            <a href="/editor/modules/advert/index.php" target="Main" class="subcapt">Площадки</a>
            </div>
          <div class="subsect_item">
            <i class="material-icons">face</i>
			<a href="/editor/modules/advert/banners.php" target="Main" class="subcapt">Баннеры</a>
            </div>
		</div>
		<? if (is_dir($_SERVER['DOCUMENT_ROOT'] . '/editor/modules/subscribe')) { ?>
		<div class="capt" onClick="opclosetree('7')" title="Свернуть/Развернуть раздел">
        <i class="material-icons">face</i>
                 Рассылки
                 <div class="plusminus"><img src="img/<?= ($_COOKIE['idshowtree'][7] != " Y ") ? "leftmenu_plus.gif " : "leftmenu_minus.gif " ?>" id="menuimgtree7" title="Свернуть/Развернуть раздел" align="absmiddle"></div>
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
			<? } ?>
		<div class="capt" onClick="opclosetree('6')" title="Свернуть/Развернуть раздел">
        	<i class="material-icons">face</i>
            Настройки
            <div class="plusminus"><img src="img/<?= ($_COOKIE['idshowtree'][6] != " Y ") ? "leftmenu_plus.gif " : "leftmenu_minus.gif " ?>"  align="absmiddle" id="menuimgtree6" title="Свернуть/Развернуть раздел"></div>
        </div>
		<div id="menudivtree6" style="display:<?= ($_COOKIE['idshowtree'][6] != " Y ") ? "none " : "inherit" ?>;" class="subsect">
	        <div class="subsect_item">
            <i class="material-icons">face</i>
        	<a href="/editor/e_modules/dbserv.php" target="Main" class="subcapt">База данных</a>
            </div>
            <div class="subsect_item">
            <i class="material-icons">face</i>
			<a href="/editor/e_modules/logging/log.php" target="Main" class="subcapt">Журнал событий</a>
            </div>
			<div class="subsect_item">        
            <i class="material-icons">face</i>    
			<a href="modules.php" target="Main" class="subcapt">Модули</a>
            </div>
<div class="subsect_item">            
<i class="material-icons">face</i>
    <a href="templates.php" target="Main" class="subcapt">Шаблоны страниц</a>
          </div>
<div class="subsect_item">        
<i class="material-icons">face</i>    
    <a href="modules/forms/catalogs.php" target="Main" class="subcapt">Web-формы</a>
          </div>
<div class="subsect_item">        
<i class="material-icons">face</i>    
    <a href="modules/catalog/catalogs.php" target="Main" class="subcapt">Каталоги</a>
          </div>
<div class="subsect_item">        
<i class="material-icons">face</i>    
    <a href="users.php" target="Main" class="subcapt">Пользователи</a>
          </div>
<div class="subsect_item">        
<i class="material-icons">face</i>    
	<a href="phpinfo.php" target="Main" class="subcapt">phpinfo</a>
          </div>
        </div>
		<div class="capt">
        	<i class="material-icons">exit_to_app</i>
        	<a href="logout.php" target="_top" title="Выйти из административного раздела" class="captlink">Выход</a>
        </div>
		<div style="margin-left:33px; font-size:10px">
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

	echo "<font color=#436173>" . $lev . "</font><br><b>" . $row_user['fio'] . "</b>" ?>
						<br> </div>
				<br>
				<hr align="left" width="224" style="border-top:2px solid #B1C5D2">
				<div align="right" style="width:227px">
					<br> <img src="img/leftmenu_close.gif" onClick="parent.show_hide()" title="Свернуть меню" style="cursor:e-resize" name="divider" id="divider"> </div>
	</body>

	</html>
	<?php
mysqli_free_result($modules);
?>