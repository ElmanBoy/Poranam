<?php require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php'); 


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "edit")) {
  $updateSQL = sprintf("UPDATE faq SET question=%s, questdesc=%s, titleanswer=%s, textanswer=%s  WHERE id=%s",
                       GetSQLValueString($_POST['title'], "text"),
                       GetSQLValueString($_POST['anons'], "text"),
                       GetSQLValueString($_POST['titleinside'], "text"),
                       GetSQLValueString($_POST['text'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

  ;
  $Result1 = el_dbselect($updateSQL, 0, $Result1, 'result', true);
  echo "<script>alert('Изменения сохранены!')</script>";
}

$colname_dbnews = "1";
if (isset($_GET['id'])) {
  $colname_dbnews = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
;
$query_dbnews = sprintf("SELECT * FROM faq WHERE id = %s", $colname_dbnews);
$dbnews = el_dbselect($query_dbnews, 0, $dbnews, 'result', true);
$row_dbnews = el_dbfetch($dbnews);
$totalRows_dbnews = mysqli_num_rows($dbnews);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Редактирование новости</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script language="JavaScript" type="text/JavaScript">
<!--
<!--

function check() {
var error1,error2,error3, error4;
if(document.Add.day.value==""){
error1='Вы не указали день!\n';} else {error1=''}
if(document.Add.mont.value==""){
error2='Вы не указали месяц!\n';}else {error2=''}
if(document.Add.year.value==""){
error3='Вы не указали год!\n';}else {error3=''}
if(document.Add.title.value==""){
error4='Вы не указали заголовок!\n';}else {error4=''}
if ((document.Add.day.value=="")||(document.Add.mont.value=="")||(document.Add.year.value=="")||(document.Add.title.value==""))
{alert (error1+error2+error3+error4);return false;} else {return true}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

function clearimg(obj){
var im=document.getElementById(obj);
var img=document.getElementById(obj+"im");
var imb=document.getElementById(obj+"but");
im.value="";
img.src="/editor/img/spacer.gif";
imb.style.visibility="hidden";
alert('Изменения вступят в силу только после нажатия кнопки "Сохранить"!');
}
//-->
</script>
<link href="/editor/style.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
-->
</style></head>

<body>

  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="85%"> <form action="<?php echo $editFormAction; ?>" method="POST" name="Add">
  <table width="100%"  border="0" align="center" cellpadding="5" cellspacing="0">
    <tr>
      <td align="right">Вопрос</td>
      <td><input name="title" type="text" id="title" value="<?php echo $row_dbnews['question']; ?>" size="50"></td>
    </tr>
    <tr>
      <td align="right" valign="top">Текст вопроса </td>
      <td><textarea name="anons" cols="50" id="anons"><?php echo $row_dbnews['questdesc']; ?></textarea><br>
	  <input name="Button" type="button" onClick="MM_openBrWindow('/editor/newseditor.php?field=anons&form=Add','editor1','resizable=yes,width=600,height=500')" value="HTML-редактор" class="but"></td>
    </tr>
    <tr>
      <td align="right" valign="top">Заголовок ответа </td>
      <td><input name="titleinside" type="text" id="titleinside" value="<?php echo $row_dbnews['titleanswer']; ?>" size="50"></td>
    </tr>
    <tr>
      <td align="right" valign="top">Ответ полностью </td>
      <td><textarea name="text" cols="50" rows="15" id="text"><?php echo $row_dbnews['textanswer']; ?></textarea><br>
	  <input name="Button" type="button" class="but" onClick="MM_openBrWindow('/editor/newseditor.php?field=text&form=Add','editor1','','800','640','true')" value="HTML-редактор"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input name="Submit" type="submit" class="but" value="Сохранить">
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <input name="Button" type="button" class="but" onClick="window.close()" value="Закрыть">      </td>
    </tr>  
  </table>
  <input type="hidden" name="MM_update" value="edit"> <input type="hidden" name="id" value="<?php echo $row_dbnews['id']; ?>">
</form></td>
      
    </tr>
  </table>
</body>
</html>
<?php
mysqli_free_result($dbnews);
?>
