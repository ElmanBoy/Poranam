<?php
define("NOCLEAN", "NO");
require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php');
$requiredUserLevel = array(0, 1);
include($_SERVER['DOCUMENT_ROOT']."/editor/secure/secure.php"); 
//Находим раздел где применяется такой шаблон
$t=el_dbselect("SELECT path FROM template WHERE id='".$_GET['id']."'",0,$t,'row');
$r=el_dbselect("SELECT path FROM content WHERE template='".$t['path']."'",0,$r,'row');

//Находим текст для этой страницы
$path= $r['path'];
;
$query_dbcontent = "SELECT * FROM content WHERE `path` = '$path'";
$dbcontent = el_dbselect($query_dbcontent, 0, $dbcontent, 'result', true);
$row_dbcontent = el_dbfetch($dbcontent);
$totalRows_dbcontent = mysqli_num_rows($dbcontent);
$cat=$row_dbcontent['cat'];
$catinfo=el_dbselect("SELECT * FROM cat WHERE id='".$row_dbcontent['cat']."'",0,$catinfo);
$row_catinfo=el_dbfetch($catinfo);

//Находим подразделы в этом разделе

$colname_dbchildmenu = "1";
$idparent=$row_dbcontent['cat'];
if (isset($idparent)) {
  $colname_dbchildmenu = (get_magic_quotes_gpc()) ? $idparent : addslashes($idparent);
}

;
$query_dbchildmenu = sprintf("SELECT * FROM cat WHERE parent = %s AND menu='Y' ORDER BY sort ASC", $colname_dbchildmenu);
$dbchildmenu = el_dbselect($query_dbchildmenu, 0, $dbchildmenu, 'result', true);
$row_dbchildmenu = el_dbfetch($dbchildmenu);
$totalRows_dbchildmenu = mysqli_num_rows($dbchildmenu);

el_strongvarsprocess();
if(isset($_POST['rating'])){
	if($_POST['rating']=='Y'){$_POST['rating']=1;}elseif($_POST['rating']=='N'){$_POST['rating']=-1;}
	if($_COOKIE['comment_'.$_GET['id']]!=1){
		$_POST['rating']=$_POST['rating'];
		setcookie('comment_'.$_GET['id'], 1, time()+31104000);
	}else{
		$_POST['rating']=0;
	}
}

if(isset($_POST['user_enter'])){
(!empty($_POST['user']))?$user_login=$_POST['user']:$user_login=$_SESSION['login'];
;
$query_login ="SELECT * FROM phpSP_users WHERE user = '".$user_login."'"; 
$login1 = el_dbselect($query_login, 0, $login1, 'result', true);
$row_login = el_dbfetch($login1);
$totalRows_login = mysqli_num_rows($login1);
$pass=str_replace("$1$","",crypt(md5($_POST['password']),'$1$'));
	if(($totalRows_login>0) && (stripslashes($row_login['password']) === $pass)){
		if($row_login['userlevel']>0){
			session_unregister("login");
			session_unregister("fio");
			$login = $row_login['user'];
			$ulevel=$row_login['userlevel'];
			$fio=$row_login['fio'];
			@session_register("login");
			@session_register("ulevel");
			@session_register("fio");
			@setcookie('usid', $usid, time()+14400);
		}else{
			$err='<font color=red>Учетная запись не активирована!</font>';
		}
	}else{
		$err='<font color=red>Неверный логин или пароль!</font>';
	}
}

if(strlen($row_dbcontent['view'])>0 && substr_count($row_dbcontent['view'], 0)==0){
	if(isset($_SESSION['login']) && @substr_count($row_dbcontent['view'],$_SESSION['ulevel'])>0){
		$row_dbcontent['caption']=$row_dbcontent['caption'];
		$row_dbcontent['text']=$row_dbcontent['text'];
		$row_dbcontent['kod']=$row_dbcontent['kod'];
		$totalRows_dbchildmenu=$totalRows_dbchildmenu;
	}else{
		$row_dbcontent['caption']='Требуется авторизация';
		$row_dbcontent['text']='<center>Пожалуйста, авторизуйтесь.<br>
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
      <td><input type="Submit" name="Submit" class=butt value="Вход" /><br><br>
        <small><a href="/remember/">забыли пароль?</a></small><br />
        <a href="/registration/">регистрация</a></td>
    </tr>
    </form>
  </table></center>';
  		$row_dbcontent['kod']='';
		$totalRows_dbchildmenu=0;
	}
	if(isset($_SESSION['login']) && @substr_count($row_dbcontent['view'],$_SESSION['ulevel'])==0 && isset($_POST['user_enter'])){
		$row_dbcontent['caption']='Закрытый раздел';
		$row_dbcontent['text']='Извините, у Вас недостаточно прав для просмотра этого раздела.';
		$row_dbcontent['kod']='';
		$totalRows_dbchildmenu=0;
	}else{
		$row_dbcontent['caption']=$row_dbcontent['caption'];
		$row_dbcontent['text']=$row_dbcontent['text'];
		$row_dbcontent['kod']=$row_dbcontent['kod'];
		$totalRows_dbchildmenu=$totalRows_dbchildmenu;
	}
}

	$flag_punkt=0;
if(!is_array($_SESSION['punkt'])){ 
	$_SESSION['punkt']=array();
	$_SESSION['punkt_name']=array();
}
if(isset($_POST['order_punkt'])){
	if(!in_array($path, $_SESSION['punkt'])){
		array_push($_SESSION['punkt'], $path);
		array_push($_SESSION['punkt_name'], $row_dbcontent['caption']);
		$flag_punkt=1;
	}else{
		$flag_punkt=2;
	}
}
if(isset($_POST['punkt_del'])){
	array_splice($_SESSION['punkt'], $_POST['punkt_del'], 1);
	array_splice($_SESSION['punkt_name'], $_POST['punkt_del'], 1);
}
$t['path']='/page/'.$t['path'];
if(!$t['path']){
	$t['path']="page/main.php";
	
}elseif(isset($_POST['cat'])){
	
	$head_content='';
	$bottom_content='';
	$fh = fopen($_SERVER['DOCUMENT_ROOT']."/tmpl/temp/head_".$_POST['tempFile'], "r");
	while (!feof($fh)) {
		$head_content.=fgets($fh, 4096);
	}
	fclose($fh);
	$fb = fopen($_SERVER['DOCUMENT_ROOT']."/tmpl/temp/bottom_".$_POST['tempFile'], "r");
	while (!feof($fb)) {
		$bottom_content.=fgets($fb, 4096);
	}
	fclose($fb);

	$bf=fopen($_SERVER['DOCUMENT_ROOT']."/tmpl/temp/preview_".$_POST['tempFile'], 'wb');
	$newData=$head_content.stripslashes($_POST['NMH']).$bottom_content;
	fputs($bf, $newData);
	fclose($bf);
	$t['path']="temp/preview_".$_POST['tempFile'];
}
include $_SERVER['DOCUMENT_ROOT']."/tmpl/".$t['path'];
?>