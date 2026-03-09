<?php require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php'); 
$requiredUserLevel = array(1, 2); 
include($_SERVER['DOCUMENT_ROOT']."/editor/secure/secure.php"); 
?>
<?php
$currentPage = $_SERVER["PHP_SELF"];


//Записываем в базу
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "add")) {

list($user, $domain) = split("@", $_POST['email'], 2);
if(checkdnsrr($domain, "MX")==false){
	echo "<script>alert('Введен не верный адрес Email!')</script>";
}else{

;
$query_check = "SELECT * FROM mail_list WHERE email='".$_POST['email']."'";
$check = el_dbselect($query_check, 0, $check, 'result', true);
$row_check = el_dbfetch($check);

if(mysqli_num_rows($check)>0){echo "<script language=javascript>alert('Подписчик с таким ящиком уже есть.')</script>";}else{
$themes=implode(';', $_POST['themes']);
  $insertSQL = sprintf("INSERT INTO mail_list (email, pass, name, type, codepage, themes, active) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['email'], "text"),
					   GetSQLValueString(crypt(md5($_POST['pass'])), "text"),
                       GetSQLValueString($_POST['name'], "text"),
					   GetSQLValueString($_POST['type'], "text"),
					   GetSQLValueString($_POST['codepage'], "text"),
                       GetSQLValueString($themes, "text"),
					   GetSQLValueString(isset($_POST['active']) ? "true" : "", "defined","'1'","'0'"));

  ;
  $Result1 = el_dbselect($insertSQL, 0, $Result1, 'result', true);
  }

}
}

if ((isset($_POST['id'])) && ($_POST['id'] != "")) {
  $deleteSQL = sprintf("DELETE FROM mail_list WHERE id=%s",
                       GetSQLValueString($_POST['id'], "int"));

  ;
  $Result1 = el_dbselect($deleteSQL, 0, $Result1, 'result', true);
}

$maxRows_dbmail_list = 50;
$pageNum_dbmail_list = 0;
if (isset($_GET['pageNum_dbmail_list'])) {
  $pageNum_dbmail_list = $_GET['pageNum_dbmail_list'];
}
$startRow_dbmail_list = $pageNum_dbmail_list * $maxRows_dbmail_list;

;
$query_dbmail_list = "SELECT * FROM mail_list ORDER BY name ASC";
$query_limit_dbmail_list = sprintf("%s LIMIT %d, %d", $query_dbmail_list, $startRow_dbmail_list, $maxRows_dbmail_list);
$dbmail_list = el_dbselect($query_limit_dbmail_list, 0, $dbmail_list, 'result', true);
$row_dbmail_list = el_dbfetch($dbmail_list);

if (isset($_GET['totalRows_dbmail_list'])) {
  $totalRows_dbmail_list = $_GET['totalRows_dbmail_list'];
} else {
  $all_dbmail_list = mysqli_query($dbconn, $query_dbmail_list);
  $totalRows_dbmail_list = mysqli_num_rows($all_dbmail_list);
}
$totalPages_dbmail_list = ceil($totalRows_dbmail_list/$maxRows_dbmail_list)-1;

$queryString_dbmail_list = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_dbmail_list") == false && 
        stristr($param, "totalRows_dbmail_list") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_dbmail_list = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_dbmail_list = sprintf("&totalRows_dbmail_list=%d%s", $totalRows_dbmail_list, $queryString_dbmail_list);
?>
<html>
<head>
<title>Список подписчиков</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css">
<!--
.style1 {
	color: #009900;
	font-size: 18px;
	font-weight: bold;
}
.style2 {color: #FF0000}
-->
</style>
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
<link href="/editor/style.css" rel="stylesheet" type="text/css">
</head>

<body>
<h4 align="center">Управление списком подписчиков</h4>
<a href="#add" class="style1">Добавить подписчика</a><br>
<br>
<? if($totalRows_dbmail_list>0){ 
if($totalRows_dbmail_list>$maxRows_dbmail_list){
?>
<table border="0" width="50%" align="center">
  <tr>
    <td width="20%" align="center">
      <?php if ($pageNum_dbmail_list > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_dbmail_list=%d%s", $currentPage, 0, $queryString_dbmail_list); ?>">В начало </a>
      <?php } // Show if not first page ?>
    </td>
    <td width="20%" align="center">
      <?php if ($pageNum_dbmail_list > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_dbmail_list=%d%s", $currentPage, max(0, $pageNum_dbmail_list - 1), $queryString_dbmail_list); ?>">Назад</a>
      <?php } // Show if not first page ?>
    </td>
    <td width="20%" align="center"><? $page=1; $pagen=0;  $countpage=$totalRows_dbmail_list/$maxRows_dbmail_list; 
	 if($countpage>1){ do  { if ($pageNum_dbmail_list!=$pagen) {echo "<a href=".$_SERVER['SCRIPT_NAME']."?pageNum_dbmail_list=".$pagen."&totalRows_dbmail_list=".$totalRows_dbmail_list."&cat=".$cat.">".$page."</a>&nbsp;&nbsp;"; } else
	  { echo "<b>".$page."</b>&nbsp;&nbsp;"; }
	  $page++;  $pagen++; $countpage--;}
while  ($countpage>=0); }
?></td>
    <td width="20%" align="center">
      <?php if ($pageNum_dbmail_list < $totalPages_dbmail_list) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_dbmail_list=%d%s", $currentPage, min($totalPages_dbmail_list, $pageNum_dbmail_list + 1), $queryString_dbmail_list); ?>">Вперед</a>
      <?php } // Show if not last page ?>
    </td>
    <td width="20%" align="center">
      <?php if ($pageNum_dbmail_list < $totalPages_dbmail_list) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_dbmail_list=%d%s", $currentPage, $totalPages_dbmail_list, $queryString_dbmail_list); ?>">В конец </a>
      <?php } // Show if not last page ?>
    </td>
  </tr>
</table>
<br>
<? }?>
 <table width="95%"  border="0" align="center" cellpadding="6" cellspacing="0" class="el_tbl">
 <tr>
 			 <td><strong>ID</strong></td>
             <td><strong>Имя</strong></td>
             <td><strong>E-mail</strong></td>
             <td><strong>Состояние</strong></td>
             <td><strong>Действия</strong></td>
</tr>
<?php do { ?>
<tr id="string<?=$row_dbmail_list['id']; ?>" onMouseOver='document.getElementById("string<?=$row_dbmail_list['id']; ?>").style.backgroundColor="#DEE7EF"' onMouseOut='document.getElementById("string<?=$row_dbmail_list['id']; ?>").style.backgroundColor=""'> 
<form name=<?php echo $row_dbmail_list['id']; ?> method="post" onSubmit="return checkdel('<?php echo htmlspecialchars($row_dbmail_list['name'], ENT_QUOTES) ?>')">
			 <td><?php echo $row_dbmail_list['id']; ?></td>
             <td><?php echo $row_dbmail_list['name']; ?></td>
             <td><?php echo $row_dbmail_list['email']; ?></td>
             <td><?php if($row_dbmail_list['active']==1){echo "<font color=green>активный</font>";}else{echo "<font color=red>не активный</font>";} ?></td>
             <td><nobr><input name="Submit" type="submit" id="Submit" value="Удалить" class="but">
<input name="Button" type="button" onClick="MM_openBrWindow('/editor/modules/subscribe/mail_listedit.php?id=<?php echo $row_dbmail_list['id']; ?>','edit','scrollbars=yes,resizable=yes, status=no','660','600','true')" value="Редактировать" class="but"><input name="id" type="hidden" id="id" value="<?php echo $row_dbmail_list['id']; ?>"></nobr></td></form></tr>
          <?php } while ($row_dbmail_list = el_dbfetch($dbmail_list)); ?>
	</table>
<? if($totalRows_dbmail_list>$maxRows_dbmail_list){ ?>
<br>
          <table border="0" width="50%" align="center">
            <tr>
              <td width="20%" align="center">
                <?php if ($pageNum_dbmail_list > 0) { // Show if not first page ?>
                <a href="<?php printf("%s?pageNum_dbmail_list=%d%s", $currentPage, 0, $queryString_dbmail_list); ?>">В начало </a>
                <?php } // Show if not first page ?>
              </td>
              <td width="20%" align="center">
                <?php if ($pageNum_dbmail_list > 0) { // Show if not first page ?>
                <a href="<?php printf("%s?pageNum_dbmail_list=%d%s", $currentPage, max(0, $pageNum_dbmail_list - 1), $queryString_dbmail_list); ?>">Назад</a>
                <?php } // Show if not first page ?>
              </td>
              <td width="20%" align="center"><? $page=1; $pagen=0;  $countpage=$totalRows_dbmail_list/$maxRows_dbmail_list; 
	 if($countpage>1){ do  { if ($pageNum_dbmail_list!=$pagen) {echo "<a href=".$_SERVER['SCRIPT_NAME']."?pageNum_dbmail_list=".$pagen."&totalRows_dbmail_list=".$totalRows_dbmail_list."&cat=".$cat.">".$page."</a>&nbsp;&nbsp;"; } else
	  { echo "<b>".$page."</b>&nbsp;&nbsp;"; }
	  $page++;  $pagen++; $countpage--;}
while  ($countpage>=0); }
?></td>
              <td width="20%" align="center">
                <?php if ($pageNum_dbmail_list < $totalPages_dbmail_list) { // Show if not last page ?>
                <a href="<?php printf("%s?pageNum_dbmail_list=%d%s", $currentPage, min($totalPages_dbmail_list, $pageNum_dbmail_list + 1), $queryString_dbmail_list); ?>">Вперед</a>
                <?php } // Show if not last page ?>
              </td>
              <td width="20%" align="center">
                <?php if ($pageNum_dbmail_list < $totalPages_dbmail_list) { // Show if not last page ?>
                <a href="<?php printf("%s?pageNum_dbmail_list=%d%s", $currentPage, $totalPages_dbmail_list, $queryString_dbmail_list); ?>">В конец </a>
                <?php } // Show if not last page ?>
              </td>
            </tr>
          </table>
<br><? }}else{echo"<h5 align=center>Подписчиков пока нет.</h5>";} ?>


<form action="<?php echo $editFormAction; ?>" method="POST" name="add" onSubmit="return fill(['name', 'email', 'pass'], ['Имя', 'E-mail', 'Пароль'])" enctype="multipart/form-data">
   <h4 align="center"><a name="add"></a>Новый подписчик  </h4>
   <table width="80%"  border="0" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
  <tr>
    <td align="right">Имя:</td>
    <td><input name="name" type="text" id="name" size="50"></td>
  </tr>
  <tr>
    <td align="right">E-mail: </td>
    <td><input name="email" type="text" id="email" size="30"></td>
  </tr>
  <tr>
    <td align="right" valign="top">Пароль: </td>
    <td><input name="pass" type="text" id="pass" value="" size="30"></td>
  </tr>
  <tr>
    <td align="right" valign="top">Тип рассылки: </td>
    <td><select name="type" id="type">
      <option value="HTML">HTML</option>
      <option value="TEXT">TEXT</option>
    </select>    </td>
  </tr>
  <tr>
    <td align="right" valign="top">Кодировка: </td>
    <td><select name="codepage" id="codepage">
      <option value="KOI8-R">KOI8-R</option>
      <option value="Windows-1251">Windows-1251</option>
        </select></td>
  </tr>
  <?
  ;
$query_themes = "SELECT * FROM mail_themes ORDER BY id DESC";
$themes = el_dbselect($query_themes, 0, $themes, 'result', true);
$row_themes = el_dbfetch($themes);
  ?>
  <tr>
    <td align="right" valign="top">Темы рассылок: </td>
    <td>
	<? 
	echo '
	<table width="100%" border="0" cellpadding="5" cellspacing="0">
	';
	do{
	echo '<tr><td valign=top><input type="checkbox" name="themes['.$row_themes['id'].']" value="'.$row_themes['id'].'"> '.$row_themes['name'].'</td></tr>
	';
	}while($row_themes = el_dbfetch($themes)); 
	echo '
	</table>
	';
	?>
	</td>
  </tr>
  <tr>
    <td align="right">Активирован: </td>
    <td><input name="active" type="checkbox" id="active" value="1" checked></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input name="Submit" type="submit" value="Добавить" class="but"></td>
  </tr>
</table>
<input type="hidden" name="MM_insert" value="add">
</form>
<br>
</body>
</html>
<?php
mysqli_free_result($dbmail_list);
?>
