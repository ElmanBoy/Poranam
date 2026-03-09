<?php require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php'); 
$requiredUserLevel = array(1, 2); 
include($_SERVER['DOCUMENT_ROOT']."/editor/secure/secure.php"); 

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "update")) {

list($user, $domain) = split("@", $_POST['email'], 2);
if(checkdnsrr($domain, "MX")==false){
	echo "<script>alert('Введен не верный адрес Email!')</script>";
}else{

;
$query_check = "SELECT * FROM mail_list WHERE email='".$_POST['email']."'";
$check = el_dbselect($query_check, 0, $check, 'result', true);
$row_check = el_dbfetch($check);

if(mysqli_num_rows($check)>0 && $row_check['id']!=$_GET['id']){echo "<script language=javascript>alert('Подписчик с таким ящиком уже есть.')</script>";}else{

	($_POST['active']=='1')?$active1='1':$active1='0';
	(strlen($_POST['pass'])>0)?$pass1=crypt(md5($_POST['pass'])):$pass1=$row_check['pass'];
	$themes1=implode(';', $_POST['themes']);

  $updateSQL = sprintf("UPDATE mail_list SET name=%s, email=%s, pass=%s, type=%s, codepage=%s, themes=%s, active=%s WHERE id=%s",
                       GetSQLValueString($_POST['name'], "text"),
					   GetSQLValueString($_POST['email'], "text"),
					   GetSQLValueString($pass1, "text"),
					   GetSQLValueString($_POST['type'], "text"),
					   GetSQLValueString($_POST['codepage'], "text"),
					   GetSQLValueString($themes1, "text"),
                       GetSQLValueString($active1, "int"),
					   GetSQLValueString($_GET['id'], "int"));

  ;
  $Result1 = el_dbselect($updateSQL, 0, $Result1, 'result', true);
  echo "<script>alert('Изменения сохранены!')</script>";
}
}
}
;
$query_dbmail_list = "SELECT * FROM mail_list WHERE id='".$_GET['id']."'";
$dbmail_list = el_dbselect($query_dbmail_list, 0, $dbmail_list, 'result', true);
$row_dbmail_list = el_dbfetch($dbmail_list);

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Редактирование данных подписчика</title>
<link href="/editor/style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features, myWidth, myHeight, isCenter) { //v3.0
  if(window.screen)if(isCenter)if(isCenter=="true"){
    var myLeft = (screen.width-myWidth)/2;
    var myTop = (screen.height-myHeight)/2;
    features+=(features!='')?',':'';
    features+=',left='+myLeft+',top='+myTop;
  }
  window.open(theURL,winName,features+((features!='')?',':'')+'width='+myWidth+',height='+myHeight);
}

function fill(id, name){
var error=0;
var errmess="";
var id1= new Array;
var name1=new Array;
id1=id;
name1=name;
for (var i=0; i<id1.length; i++){
	if (document.getElementById(id1[i]).value==""){
		errmess+='Заполните поле "'+name1[i]+'"\n';
		error++;
	}
}
	if(error!=0)
	{	
		alert(errmess);
		return false;
	}else{
		return true;
	}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}
function checkdel(mail_list){
var OK=confirm('Вы действительно хотите удалить подписчика "'+mail_list+'" ?');
if (OK) {return true} else {return false}
}
</script>

</head>

<body>
<form action="<?php echo $editFormAction; ?>" method="POST" name="add" onSubmit="return fill(['name', 'email'], ['Имя', 'E-mail'])" enctype="multipart/form-data">
   <table width="100%"  border="0" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
  <tr>
    <td align="right">Имя:</td>
    <td><input name="name" type="text" id="name" value="<?=$row_dbmail_list['name']?>" size="50"></td>
  </tr>
  <tr>
    <td align="right">E-mail: </td>
    <td><input name="email" type="text" id="email" value="<?=$row_dbmail_list['email']?>" size="30"></td>
  </tr>
  <tr>
    <td align="right" valign="top">Новый пароль: </td>
    <td><input name="pass" type="text" id="pass" value="" size="30"></td>
  </tr>
  <tr>
    <td align="right" valign="top">Тип рассылки: </td>
    <td><select name="type" id="type">
      <option value="HTML" <?=($row_dbmail_list['type']=='HTML')?"selected":""?>>HTML</option>
      <option value="TEXT" <?=($row_dbmail_list['type']=='TEXT')?"selected":""?>>TEXT</option>
    </select>    </td>
  </tr>
  <tr>
    <td align="right" valign="top">Большая картинка: </td>
    <td><select name="codepage" id="codepage">
      <option value="KOI8-R" <?=($row_dbmail_list['codepage']=='KOI8-R')?"selected":""?>>KOI8-R</option>
      <option value="Windows-1251" <?=($row_dbmail_list['codepage']=='Windows-1251')?"selected":""?>>Windows-1251</option>
        </select></td>
  </tr>
  <?
  ;
$query_themes = "SELECT * FROM mail_themes ORDER BY id DESC";
$themes = el_dbselect($query_themes, 0, $themes, 'result', true);
$row_themes = el_dbfetch($themes);
  ?>
  <tr>
    <td align="right" valign="top">Группы рассылок: </td>
    <td>
	<? 
	echo '
	<table width="100%" border="0" cellpadding="5" cellspacing="0">
	';
	do{
		if(substr_count($row_dbmail_list['themes'], ';')>0){
			$th=explode(';', $row_dbmail_list['themes']);
			if(in_array($row_themes['id'], $th)){$ch="checked";}else{$ch="";}
		}else{
			if($row_themes['id']==$row_dbmail_list['themes']){$ch="checked";}else{$ch="";}
		}
	echo '<tr><td valign=top><input type="checkbox" name="themes['.$row_themes['id'].']" value="'.$row_themes['id'].'" '.$ch.'> '.$row_themes['name'].'</td></tr>
	';
	}while($row_themes = el_dbfetch($themes)); 
	echo '
	</table>
	';
	?>	</td>
  </tr>
  <tr>
    <td align="right">Активирован: </td>
    <td><input name="active" type="checkbox" id="active" value="1" <?=($row_dbmail_list['active']=='1')?"checked":""?>></td>
  </tr>
  <tr>
    <td><input name="Submit" type="submit" value="Сохранить" class="but"></td>
    <td align="right"><input name="close" type="button" id="close" value="Закрыть" onClick="window.close()" class="but">      
      &nbsp;&nbsp;</td>
  </tr>
</table>
<input type="hidden" name="MM_update" value="update">
</form>
</body>
</html>
