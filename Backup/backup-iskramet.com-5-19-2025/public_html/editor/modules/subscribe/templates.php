<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php');
$requiredUserLevel = array(1, 2); 
include($_SERVER['DOCUMENT_ROOT']."/editor/secure/secure.php"); 
?>
<?php
$currentPage = $_SERVER["PHP_SELF"];


//Записываем в базу
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "add")) {

	;
	$query_check = "SELECT * FROM mail_templates WHERE name='".$_POST['name']."'";
	$check = el_dbselect($query_check, 0, $check, 'result', true);
	$row_check = el_dbfetch($check);

	if(mysqli_num_rows($check)>0){
		echo "<script language=javascript>alert('Шаблон с таким именем уже есть.')</script>";
	}else{
		if($_POST['type']=="TEXT"){
			$_POST['codepage']='';
		}
  		$insertSQL = sprintf("INSERT INTO mail_templates (name, body, type, codepage) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['name'], "text"),
					   GetSQLValueString($_POST['body'], "text"),
                       GetSQLValueString($_POST['type'], "text"),
					   GetSQLValueString($_POST['codepage'], "text"));

  		;
  		$Result1 = el_dbselect($insertSQL, 0, $Result1, 'result', true);
	}

}

if ((isset($_POST['id'])) && ($_POST['id'] != "")) {
  $deleteSQL = sprintf("DELETE FROM mail_templates WHERE id=%s",
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
$query_dbmail_list = "SELECT * FROM mail_templates ORDER BY name ASC";
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
var OK=confirm('Вы действительно хотите удалить шаблон "'+mail_list+'" ?');
if (OK) {return true} else {return false}
}

function vis_button(){
var sel=document.getElementById("type");
var but=document.getElementById("ButtonHTML");
var trow=document.getElementById("coderow");;
	if(sel.options[sel.selectedIndex].value=="HTML"){
		but.style.display="block";
		trow.style.display="block";
	}else{
		but.style.display="none";
		trow.style.display="none";
	}
}

function show_help(){
var h=document.getElementById("help");
	if(h.style.display=="none"){
		h.style.display="block";
	}else{
		h.style.display="none";
	}
}
</script>
<link href="/editor/style.css" rel="stylesheet" type="text/css">
</head>

<body>
<h4 align="center">Управление шаблонами рассылки </h4>
<a href="#add" class="style1">Добавить шаблон </a><br>
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
             <td><strong>Название</strong></td>
             <td><strong>Тип</strong></td>
             <td><strong>Действия</strong></td>
</tr>
<?php do { ?>
<tr id="string<?=$row_dbmail_list['id']; ?>" onMouseOver='document.getElementById("string<?=$row_dbmail_list['id']; ?>").style.backgroundColor="#DEE7EF"' onMouseOut='document.getElementById("string<?=$row_dbmail_list['id']; ?>").style.backgroundColor=""'> 
<form name=<?php echo $row_dbmail_list['id']; ?> method="post" onSubmit="return checkdel('<?php echo htmlspecialchars($row_dbmail_list['name'], ENT_QUOTES) ?>')">
			 <td><?php echo $row_dbmail_list['id']; ?></td>
             <td><?php echo $row_dbmail_list['name']; ?></td>
             <td><?php echo $row_dbmail_list['type']; ?></td>
             <td><nobr><input name="Submit" type="submit" id="Submit" value="Удалить" class="but">
<input name="Button" type="button" onClick="MM_openBrWindow('/editor/modules/subscribe/mail_tempedit.php?id=<?php echo $row_dbmail_list['id']; ?>&mode=<?=($row_dbmail_list['type']=="HTML")?"html":"text"?>','edit','scrollbars=yes,resizable=yes, status=no','760','600','true')" value="Редактировать" class="but"><input name="id" type="hidden" id="id" value="<?php echo $row_dbmail_list['id']; ?>"></nobr></td></form></tr>
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
<br><? }}else{echo"<h5 align=center>Нет ни одного шаблона.</h5>";} ?>


<form action="<?php echo $editFormAction; ?>" method="POST" name="add" onSubmit="SaveHTML();document.getElementById('bodyHTML').value=document.getElementById('NMH').value;return fill(['name'], ['Имя'])" enctype="multipart/form-data">
   <h4 align="center"><a name="add"></a>Новый шаблон  </h4>
   <table width="80%"  border="0" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
  <tr>
    <td colspan="2"><a href="#" onClick="show_help()">Помощь</a>
      <div id="help" style="display:none"><? el_showalert("info","<b>Подсказка:</b><br>Чтобы система смогла вставить в шаблон ваши данные используйте следующие директивы:<ul style=margin:1px><li>%%subject%% - заголовок письма</li><li>%%name%% - имя получателя</li><li>%%theme%% - тема рассылки</li><li>%%body%% - содержимое письма</li><li>%%sitename%% - название сайта</li><li>%%siteurl%% - адрес сайта</li><li>%%date%% - дата рассылки</li></ul>") ?></div></td>
    </tr>
  <tr>
    <td align="right">Название:</td>
    <td><input name="name" type="text" id="name" size="50"></td>
  </tr>
  <tr>
    <td align="right">Шаблон: </td>
    <td>
      <textarea name="body" cols="70" rows="15" id="body"></textarea>
	<div id="ButtonHTML" style="display:none">
     <input name="ButtonHTML" type="button" class="but" onClick="MM_openBrWindow('/editor/newseditor.php?field=body&form=add','editor','','590','600','true')" value="Визуальный редактор">
	  </div>	  </td>
  </tr>
  <tr>
    <td align="right" valign="top">Тип рассылки: </td>
    <td><select name="type" id="type" onChange="vis_button()">
      <option value="HTML">HTML</option>
      <option value="TEXT" selected>TEXT</option>
    </select>    </td>
  </tr>
  <tr id="coderow" style="display:none">
    <td align="right" valign="top">Кодировка: </td>
    <td><select name="codepage" id="codepage">
      <option value="KOI-8R" selected>KOI-8R</option>
      <option value="Windows-1251">Windows-1251</option>
        </select></td>
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
