<?php // require_once('../../../Connections/dbconn.php'); ?>
<?php
$currentPage = $_SERVER["PHP_SELF"];

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "add")) {
  $insertSQL = sprintf("INSERT INTO faq (question, questdesc, titleanswer, textanswer) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['title'], "text"),
                       GetSQLValueString($_POST['anons'], "text"),
                       GetSQLValueString($_POST['titleinside'], "text"),
                       GetSQLValueString($_POST['text'], "text"));

  ;
  $Result1 = el_dbselect($insertSQL, 0, $Result1, 'result', true);
}

if ((isset($_POST['id'])) && ($_POST['id'] != "")) {
  $deleteSQL = sprintf("DELETE FROM faq WHERE id=%s",
                       GetSQLValueString($_POST['id'], "int"));

  ;
  $Result1 = el_dbselect($deleteSQL, 0, $Result1, 'result', true);
}

$maxRows_dbnews = 10;
$pageNum_dbnews = 0;
if (isset($_GET['pageNum_dbnews'])) {
  $pageNum_dbnews = $_GET['pageNum_dbnews'];
}
$startRow_dbnews = $pageNum_dbnews * $maxRows_dbnews;

;
$query_dbnews = "SELECT * FROM faq ORDER BY id DESC";
$query_limit_dbnews = sprintf("%s LIMIT %d, %d", $query_dbnews, $startRow_dbnews, $maxRows_dbnews);
$dbnews = el_dbselect($query_limit_dbnews, 0, $dbnews, 'result', true);
$row_dbnews = el_dbfetch($dbnews);

if (isset($_GET['totalRows_dbnews'])) {
  $totalRows_dbnews = $_GET['totalRows_dbnews'];
} else {
  $all_dbnews = mysqli_query($dbconn, $query_dbnews);
  $totalRows_dbnews = mysqli_num_rows($all_dbnews);
}
$totalPages_dbnews = ceil($totalRows_dbnews/$maxRows_dbnews)-1;

$queryString_dbnews = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_dbnews") == false && 
        stristr($param, "totalRows_dbnews") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_dbnews = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_dbnews = sprintf("&totalRows_dbnews=%d%s", $totalRows_dbnews, $queryString_dbnews);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css">
<!--
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
body {
	background-color: #FFFFFF;
}
.style1 {
	font-size: 18px;
	font-weight: bold;
}
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

function check() {
var error1,error2,error3, error4;
if(document.add.day.value==""){
error1='Вы не указали день!\n';} else {error1=''}
if(document.add.mont.value==""){
error2='Вы не указали месяц!\n';}else {error2=''}
if(document.add.year.value==""){
error3='Вы не указали год!\n';}else {error3=''}
if(document.add.title.value==""){
error4='Вы не указали заголовок!\n';}else {error4=''}
if ((document.add.day.value=="")||(document.add.mont.value=="")||(document.add.year.value=="")||(document.add.title.value==""))
{alert (error1+error2+error3+error4);return false;} else {return true}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

</script>
<link href="/editor/style.css" rel="stylesheet" type="text/css">
</head>

<body>
<? if($row_dbnews['id'] > 0){ /*Проверка на наличие статей*/?>
<a href="#add" class="style1">Новый вопрос </a> <br>
<br>
<table border="0" width="50%" align="center">
  <tr>
    <td width="20%" align="center">
      <?php if ($pageNum_dbnews > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_dbnews=%d%s", $currentPage, 0, $queryString_dbnews); ?>">В начало </a>
        <?php } // Show if not first page ?>
    </td>
    <td width="20%" align="center">
      <?php if ($pageNum_dbnews > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_dbnews=%d%s", $currentPage, max(0, $pageNum_dbnews - 1), $queryString_dbnews); ?>">Назад</a>
        <?php } // Show if not first page ?>
    </td>
    <td width="20%" align="center"><? $page=1; $pagen=0;  $countpage=$totalRows_dbnews/$maxRows_dbnews; 
	 if($countpage>1){ do  { if ($pageNum_dbnews!=$pagen) {echo "<a href=".$_SERVER['SCRIPT_NAME']."?pageNum_dbnews=".$pagen."&totalRows_dbnews=".$totalRows_dbnews."&cat=".$cat.">".$page."</a>&nbsp;&nbsp;"; } else
	  { echo "<b>".$page."</b>&nbsp;&nbsp;"; }
	  $page++;  $pagen++; $countpage--;}
while  ($countpage>=0); }
?></td>
    <td width="20%" align="center">
      <?php if ($pageNum_dbnews < $totalPages_dbnews) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_dbnews=%d%s", $currentPage, min($totalPages_dbnews, $pageNum_dbnews + 1), $queryString_dbnews); ?>">Вперед</a>
        <?php } // Show if not last page ?>
    </td>
    <td width="20%" align="center">
      <?php if ($pageNum_dbnews < $totalPages_dbnews) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_dbnews=%d%s", $currentPage, $totalPages_dbnews, $queryString_dbnews); ?>">В конец </a>
        <?php } // Show if not last page ?>
    </td>
  </tr>
</table>
<br> 
<?php do { ?>
         <form name=<?php echo $row_dbnews['id']; ?> method="post">
		 <table width="70%"  border="1" align="center" cellpadding="6" cellspacing="0" bordercolor="#FFFFFF" bgcolor="#E8F0F7">
           <tr>
             <td><strong>№<?php echo $row_dbnews['id']; ?></strong> <b><?php echo $row_dbnews['question']; ?></b>
               <input name="id" type="hidden" id="id" value="<?php echo $row_dbnews['id']; ?>">
               <br>
             <?php echo $row_dbnews['questdesc']; ?> </td>
           </tr>
           <tr>
             <td><input name="Submit" type="submit" class="but" id="Submit" value="Удалить">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input name="Button" type="button" class="but" onClick="MM_openBrWindow('/editor/modules/faq/newsedit.php?id=<?php echo $row_dbnews['id']; ?>','editor','','600','450','true')" value="Редактировать"></td>
           </tr>
         </table>
		 <br>
            <br>
         </form>
          <?php } while ($row_dbnews = el_dbfetch($dbnews)); ?>
<br>
          <table border="0" width="50%" align="center">
  <tr>
    <td width="20%" align="center">
      <?php if ($pageNum_dbnews > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_dbnews=%d%s", $currentPage, 0, $queryString_dbnews); ?>">В начало </a>
        <?php } // Show if not first page ?>
    </td>
    <td width="20%" align="center">
      <?php if ($pageNum_dbnews > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_dbnews=%d%s", $currentPage, max(0, $pageNum_dbnews - 1), $queryString_dbnews); ?>">Назад</a>
        <?php } // Show if not first page ?>
    </td>
    <td width="20%" align="center"><? $page=1; $pagen=0;  $countpage=$totalRows_dbnews/$maxRows_dbnews; 
	 if($countpage>1){ do  { if ($pageNum_dbnews!=$pagen) {echo "<a href=".$_SERVER['SCRIPT_NAME']."?pageNum_dbnews=".$pagen."&totalRows_dbnews=".$totalRows_dbnews."&cat=".$cat.">".$page."</a>&nbsp;&nbsp;"; } else
	  { echo "<b>".$page."</b>&nbsp;&nbsp;"; }
	  $page++;  $pagen++; $countpage--;}
while  ($countpage>=0); }
?></td>
    <td width="20%" align="center">
      <?php if ($pageNum_dbnews < $totalPages_dbnews) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_dbnews=%d%s", $currentPage, min($totalPages_dbnews, $pageNum_dbnews + 1), $queryString_dbnews); ?>">Вперед</a>
        <?php } // Show if not last page ?>
    </td>
    <td width="20%" align="center">
      <?php if ($pageNum_dbnews < $totalPages_dbnews) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_dbnews=%d%s", $currentPage, $totalPages_dbnews, $queryString_dbnews); ?>">В конец </a>
        <?php } // Show if not last page ?>
    </td>
  </tr>
</table>
          <? }/*Конец проверки на наличие статей*/ ?>
<br>
          <form action="<?php echo $editFormAction; ?>" method="POST" name="add">
   <h4 align="center"><a name="add"></a>Новый вопрос</h4>
<table width="80%"  border="0" align="center" cellpadding="5" cellspacing="0">
  <tr>
    <td align="right">Вопрос</td>
    <td><input name="title" type="text" id="title" size="50"></td>
  </tr>
  <tr>
    <td align="right" valign="top">Текст вопроса </td>
    <td><textarea name="anons" cols="100" id="anons"></textarea>
      <input name="Button" type="button" class="but" onClick="MM_openBrWindow('/editor/newseditor.php?field=anons&form=add','editor','','800','640','true')" value="HTML-редактор"></td>
  </tr>
  <tr>
    <td align="right" valign="top">Заголовок ответа </td>
    <td><input name="titleinside" type="text" id="titleinside" size="50"></td>
  </tr>
  <tr>
    <td align="right" valign="top">Ответ полностью </td>
    <td><textarea name="text" cols="100" rows="15" id="text"></textarea>
      <input name="Button" type="button" class="but" onClick="MM_openBrWindow('/editor/newseditor.php?field=text&form=add','editor','','800','640','true')" value="HTML-редактор"></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input name="Submit" type="submit" class="but" value="Добавить"></td>
  </tr>
</table>
<input type="hidden" name="MM_insert" value="add">
</form>
<br>
</body>
</html>
<?php
mysqli_free_result($dbnews);
?>
