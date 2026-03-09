<?php require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php'); 
$requiredUserLevel = array(1, 2); 
include($_SERVER['DOCUMENT_ROOT']."/editor/secure/secure.php"); 
?>
<?php
$currentPage = $_SERVER["PHP_SELF"];


//Записываем в базу
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "add")) {

;
$query_check = "SELECT * FROM mail_themes WHERE name='".$_POST['name']."'";
$check = el_dbselect($query_check, 0, $check, 'result', true);
$row_check = el_dbfetch($check);

if(mysqli_num_rows($check)>0){echo "<script language=javascript>alert('Тема с таким названием уже есть!')</script>";}else{
  $insertSQL = sprintf("INSERT INTO mail_themes (name, description) VALUES (%s, %s)",
                       GetSQLValueString($_POST['name'], "text"),
					   GetSQLValueString($_POST['description'], "text"));

  ;
  $Result1 = el_dbselect($insertSQL, 0, $Result1, 'result', true);
  }

}


if ((isset($_POST['id'])) && ($_POST['id'] != "")) {
  $deleteSQL = sprintf("DELETE FROM mail_themes WHERE id=%s",
                       GetSQLValueString($_POST['id'], "int"));

  ;
  $Result1 = el_dbselect($deleteSQL, 0, $Result1, 'result', true);
}

$maxRows_dbmail_themes = 50;
$pageNum_dbmail_themes = 0;
if (isset($_GET['pageNum_dbmail_themes'])) {
  $pageNum_dbmail_themes = $_GET['pageNum_dbmail_themes'];
}
$startRow_dbmail_themes = $pageNum_dbmail_themes * $maxRows_dbmail_themes;

;
$query_dbmail_themes = "SELECT * FROM mail_themes ORDER BY name ASC";
$query_limit_dbmail_themes = sprintf("%s LIMIT %d, %d", $query_dbmail_themes, $startRow_dbmail_themes, $maxRows_dbmail_themes);
$dbmail_themes = el_dbselect($query_limit_dbmail_themes, 0, $dbmail_themes, 'result', true);
$row_dbmail_themes = el_dbfetch($dbmail_themes);

if (isset($_GET['totalRows_dbmail_themes'])) {
  $totalRows_dbmail_themes = $_GET['totalRows_dbmail_themes'];
} else {
  $all_dbmail_themes = mysqli_query($dbconn, $query_dbmail_themes);
  $totalRows_dbmail_themes = mysqli_num_rows($all_dbmail_themes);
}
$totalPages_dbmail_themes = ceil($totalRows_dbmail_themes/$maxRows_dbmail_themes)-1;

$queryString_dbmail_themes = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_dbmail_themes") == false && 
        stristr($param, "totalRows_dbmail_themes") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_dbmail_themes = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_dbmail_themes = sprintf("&totalRows_dbmail_themes=%d%s", $totalRows_dbmail_themes, $queryString_dbmail_themes);
?>
<html>
<head>
<title>Список тем подписки</title>
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
function checkdel(mail_themes){
var OK=confirm('Вы действительно хотите удалить тему "'+mail_themes+'" ?');
if (OK) {return true} else {return false}
}
</script>
<link href="/editor/style.css" rel="stylesheet" type="text/css">
</head>

<body>
<h4 align="center">Управление списком тем подписки</h4>
<a href="#add" class="style1">Добавить тему</a><br>
<br>
<? if($totalRows_dbmail_themes>0){ 
if($totalRows_dbmail_themes>$maxRows_dbmail_themes){
?>
<table border="0" width="50%" align="center">
  <tr>
    <td width="20%" align="center">
      <?php if ($pageNum_dbmail_themes > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_dbmail_themes=%d%s", $currentPage, 0, $queryString_dbmail_themes); ?>">В начало </a>
      <?php } // Show if not first page ?>
    </td>
    <td width="20%" align="center">
      <?php if ($pageNum_dbmail_themes > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_dbmail_themes=%d%s", $currentPage, max(0, $pageNum_dbmail_themes - 1), $queryString_dbmail_themes); ?>">Назад</a>
      <?php } // Show if not first page ?>
    </td>
    <td width="20%" align="center"><? $page=1; $pagen=0;  $countpage=$totalRows_dbmail_themes/$maxRows_dbmail_themes; 
	 if($countpage>1){ do  { if ($pageNum_dbmail_themes!=$pagen) {echo "<a href=".$_SERVER['SCRIPT_NAME']."?pageNum_dbmail_themes=".$pagen."&totalRows_dbmail_themes=".$totalRows_dbmail_themes."&cat=".$cat.">".$page."</a>&nbsp;&nbsp;"; } else
	  { echo "<b>".$page."</b>&nbsp;&nbsp;"; }
	  $page++;  $pagen++; $countpage--;}
while  ($countpage>=0); }
?></td>
    <td width="20%" align="center">
      <?php if ($pageNum_dbmail_themes < $totalPages_dbmail_themes) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_dbmail_themes=%d%s", $currentPage, min($totalPages_dbmail_themes, $pageNum_dbmail_themes + 1), $queryString_dbmail_themes); ?>">Вперед</a>
      <?php } // Show if not last page ?>
    </td>
    <td width="20%" align="center">
      <?php if ($pageNum_dbmail_themes < $totalPages_dbmail_themes) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_dbmail_themes=%d%s", $currentPage, $totalPages_dbmail_themes, $queryString_dbmail_themes); ?>">В конец </a>
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
             <td><strong>Описание</strong></td>
             <td><strong>Действия</strong></td>
</tr>
<?php do { ?>
<tr id="string<?=$row_dbmail_themes['id']; ?>" onMouseOver='document.getElementById("string<?=$row_dbmail_themes['id']; ?>").style.backgroundColor="#DEE7EF"' onMouseOut='document.getElementById("string<?=$row_dbmail_themes['id']; ?>").style.backgroundColor=""'> 
<form name=<?php echo $row_dbmail_themes['id']; ?> method="post" onSubmit="return checkdel('<?php echo htmlspecialchars($row_dbmail_themes['name'], ENT_QUOTES) ?>')">
			 <td><?php echo $row_dbmail_themes['id']; ?></td>
             <td><?php echo $row_dbmail_themes['name']; ?></td>
             <td><?php echo $row_dbmail_themes['description']; ?></td>
             <td><nobr><input name="Submit" type="submit" id="Submit" value="Удалить" class="but">
<input name="Button" type="button" onClick="MM_openBrWindow('/editor/modules/subscribe/mail_themesedit.php?id=<?php echo $row_dbmail_themes['id']; ?>','edit','scrollbars=no,resizable=yes, status=no','500','200','true')" value="Редактировать" class="but"><input name="id" type="hidden" id="id" value="<?php echo $row_dbmail_themes['id']; ?>"></nobr></td></form></tr>
          <?php } while ($row_dbmail_themes = el_dbfetch($dbmail_themes)); ?>
	</table>
<? if($totalRows_dbmail_themes>$maxRows_dbmail_themes){ ?>
<br>
          <table border="0" width="50%" align="center">
            <tr>
              <td width="20%" align="center">
                <?php if ($pageNum_dbmail_themes > 0) { // Show if not first page ?>
                <a href="<?php printf("%s?pageNum_dbmail_themes=%d%s", $currentPage, 0, $queryString_dbmail_themes); ?>">В начало </a>
                <?php } // Show if not first page ?>
              </td>
              <td width="20%" align="center">
                <?php if ($pageNum_dbmail_themes > 0) { // Show if not first page ?>
                <a href="<?php printf("%s?pageNum_dbmail_themes=%d%s", $currentPage, max(0, $pageNum_dbmail_themes - 1), $queryString_dbmail_themes); ?>">Назад</a>
                <?php } // Show if not first page ?>
              </td>
              <td width="20%" align="center"><? $page=1; $pagen=0;  $countpage=$totalRows_dbmail_themes/$maxRows_dbmail_themes; 
	 if($countpage>1){ do  { if ($pageNum_dbmail_themes!=$pagen) {echo "<a href=".$_SERVER['SCRIPT_NAME']."?pageNum_dbmail_themes=".$pagen."&totalRows_dbmail_themes=".$totalRows_dbmail_themes."&cat=".$cat.">".$page."</a>&nbsp;&nbsp;"; } else
	  { echo "<b>".$page."</b>&nbsp;&nbsp;"; }
	  $page++;  $pagen++; $countpage--;}
while  ($countpage>=0); }
?></td>
              <td width="20%" align="center">
                <?php if ($pageNum_dbmail_themes < $totalPages_dbmail_themes) { // Show if not last page ?>
                <a href="<?php printf("%s?pageNum_dbmail_themes=%d%s", $currentPage, min($totalPages_dbmail_themes, $pageNum_dbmail_themes + 1), $queryString_dbmail_themes); ?>">Вперед</a>
                <?php } // Show if not last page ?>
              </td>
              <td width="20%" align="center">
                <?php if ($pageNum_dbmail_themes < $totalPages_dbmail_themes) { // Show if not last page ?>
                <a href="<?php printf("%s?pageNum_dbmail_themes=%d%s", $currentPage, $totalPages_dbmail_themes, $queryString_dbmail_themes); ?>">В конец </a>
                <?php } // Show if not last page ?>
              </td>
            </tr>
          </table>
<br><? }}else{echo"<h5 align=center>Тем подписки пока нет.</h5>";} ?>


<form action="<?php echo $editFormAction; ?>" method="POST" name="add" onSubmit="return fill(['name'], ['Название'])" enctype="multipart/form-data">
   <h4 align="center"><a name="add"></a>Новая тема</h4>
   <table width="80%"  border="0" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
  <tr>
    <td align="right">Название:</td>
    <td><input name="name" type="text" id="name" size="50"></td>
  </tr>
  <tr>
    <td align="right">Описание: </td>
    <td><textarea name="description" cols="40" rows="5" id="description"></textarea></td>
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
mysqli_free_result($dbmail_themes);
?>
