<?php require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php'); ?>
<?PHP $requiredUserLevel = array(1, 2); 
include($_SERVER['DOCUMENT_ROOT']."/editor/secure/secure.php"); ?>
<?php
$currentPage = $_SERVER["PHP_SELF"];
$p=el_dbselect("SELECT path FROM cat WHERE id=".$_GET['cat'],0,$p, 'row');
$pagepath=$p['path']."/?id=".$_GET['comm'];
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "edit")) {
  $updateSQL = sprintf("UPDATE comments SET text=%s WHERE id=%s",
                       GetSQLValueString($_POST['text'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

  ;
  $Result1 = el_dbselect($updateSQL, 0, $Result1, 'result', true);
}

if (isset($_POST["id_allow"])) {
  $updateSQL = sprintf("UPDATE comments SET status=1 WHERE id=%s",
                       GetSQLValueString($_POST['id_allow'], "int"));

  ;
  $Result1 = el_dbselect($updateSQL, 0, $Result1, 'result', true);
}

if ((isset($_POST['id_comm'])) && ($_POST['id_comm'] != "")) {
  $deleteSQL = sprintf("DELETE FROM comments WHERE id=%s",
                       GetSQLValueString($_POST['id_comm'], "int"));

  ;
  $Result1 = el_dbselect($deleteSQL, 0, $Result1, 'result', true);
}

if ((isset($_POST['del_comm'])) && ($_POST['del_comm'] != "")) {
  $deleteSQL = sprintf("DELETE FROM comments WHERE pagepath='%s'", $pagepath);

  ;
  $Result1 = el_dbselect($deleteSQL, 0, $Result1, 'result', true);
  echo "<script language=javascript>alert('Все комментарии к этой новости удалены!')</script>";
}

$maxRows_comments = 10;
$pageNum_comments = 0;
if (isset($_GET['pageNum_comments'])) {
  $pageNum_comments = $_GET['pageNum_comments'];
}
$startRow_comments = $pageNum_comments * $maxRows_comments;

$colname_comments = "1";
if (isset($_GET['comm'])) {
  $colname_comments = (get_magic_quotes_gpc()) ? $_GET['comm'] : addslashes($_GET['comm']);
}

;
$query_comments = sprintf("SELECT * FROM comments WHERE pagepath = '%s' ORDER BY id DESC", $pagepath);
$query_limit_comments = sprintf("%s LIMIT %d, %d", $query_comments, $startRow_comments, $maxRows_comments);
$comments = el_dbselect($query_limit_comments, 0, $comments, 'result', true);
$row_comments = el_dbfetch($comments);

if (isset($_GET['totalRows_comments'])) {
  $totalRows_comments = $_GET['totalRows_comments'];
} else {
  $all_comments = mysql_query($query_comments);
  $totalRows_comments = mysqli_num_rows($all_comments);
}
$totalPages_comments = ceil($totalRows_comments/$maxRows_comments)-1;

$colname_titles = "1";
if (isset($_GET['comm'])) {
  $colname_titles = (get_magic_quotes_gpc()) ? $_GET['comm'] : addslashes($_GET['comm']);
}
;
$query_titles = sprintf("SELECT id, title, raiting FROM articles WHERE id = %s", $colname_titles);
$titles = el_dbselect($query_titles, 0, $titles, 'result', true);
$row_titles = el_dbfetch($titles);
$totalRows_titles = mysqli_num_rows($titles);

$queryString_comments = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_comments") == false && 
        stristr($param, "totalRows_comments") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_comments = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_comments = sprintf("&totalRows_comments=%d%s", $totalRows_comments, $queryString_comments);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Редактирование комментариев</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="/editor/style.css" rel="stylesheet" type="text/css">
<script type="text/JavaScript">
<!--
function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}

function del(id){
 document.delet.id_comm.value=id;
 document.delet.submit();
}

function allow(id){
 document.al.id_allow.value=id;
 document.al.submit();
}
//-->
</script>
</head>

<body>
<form name="delet" method="post"><input type="hidden" name="id_comm"></form><form name="al" method="post"><input type="hidden" name="id_allow"></form>
<h4 align="center">Комментарии к фотографии ID <?=$_GET['comm']?> </h4>
<? if($totalRows_comments>0){?>			
Записи с <?php echo ($startRow_comments + 1) ?> по <?php echo min($startRow_comments + $maxRows_comments, $totalRows_comments) ?> из <?php echo $totalRows_comments ?>
 <table border="0" width="50%" align="center">
  <tr>
    <td width="23%" align="center">
      <?php if ($pageNum_comments > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_comments=%d%s", $currentPage, 0, $queryString_comments); ?>"><img src="First.gif" border=0></a>
      <?php } // Show if not first page ?>
    </td>
    <td width="31%" align="center">
      <?php if ($pageNum_comments > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_comments=%d%s", $currentPage, max(0, $pageNum_comments - 1), $queryString_comments); ?>"><img src="Previous.gif" border=0></a>
      <?php } // Show if not first page ?>
    </td>
    <td width="23%" align="center">
      <?php if ($pageNum_comments < $totalPages_comments) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_comments=%d%s", $currentPage, min($totalPages_comments, $pageNum_comments + 1), $queryString_comments); ?>"><img src="Next.gif" border=0></a>
      <?php } // Show if not last page ?>
    </td>
    <td width="23%" align="center">
      <?php if ($pageNum_comments < $totalPages_comments) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_comments=%d%s", $currentPage, $totalPages_comments, $queryString_comments); ?>"><img src="Last.gif" border=0></a>
      <?php } // Show if not last page ?>
    </td>
  </tr>
</table>
<?php do { ?>
<table width="78%"  border="0" align="center" cellpadding="3" cellspacing="0" class="el_tbl">
  <tr>
    <td><form method="POST" action="<?php echo $editFormAction; ?>" name="edit">
ID #<?php echo $row_comments['id']; ?> от <strong><?php echo $row_comments['autor']; ?></strong> , IP <a href="https://www.nic.ru/whois/?ip=<?php echo $row_comments['ip']; ?>" target="_blank"><?php echo $row_comments['ip']; ?></a>, дата <?php echo $row_comments['date']; ?>, оценка <?php echo $row_comments['rating']; ?> <?=($row_comments['status']!=1)?'<font color=red>Ждет одобрения модератора</font>':''?><br>
<textarea name="text" cols="100" rows="5" id="text"><?php echo $row_comments['text']; ?></textarea><br>
      <input type="submit" name="Submit" value="Сохранить" class="but">
      &nbsp;&nbsp;&nbsp;&nbsp;
    <input name="Button" type="button" class="but" value="Удалить" onClick="del(<?=$row_comments['id']?>)">
    &nbsp;&nbsp;&nbsp;&nbsp;
	<?=($row_comments['status']!=1)?'<input name="Button" type="button" class="but" value="Опубликовать" onClick="allow('.$row_comments['id'].')">':''?>
    <input type="hidden" name="MM_update" value="edit">
    <input name="id" type="hidden" id="id" value="<?php echo $row_comments['id']; ?>">
    </form></td>
    </tr>
</table>

<br>
<?php } while ($row_comments = el_dbfetch($comments)); ?><br>

<table border="0" width="50%" align="center">
  <tr>
    <td width="23%" align="center">
      <?php if ($pageNum_comments > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_comments=%d%s", $currentPage, 0, $queryString_comments); ?>"><img src="First.gif" border=0></a>
      <?php } // Show if not first page ?>
    </td>
    <td width="31%" align="center">
      <?php if ($pageNum_comments > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_comments=%d%s", $currentPage, max(0, $pageNum_comments - 1), $queryString_comments); ?>"><img src="Previous.gif" border=0></a>
      <?php } // Show if not first page ?>
    </td>
    <td width="23%" align="center">
      <?php if ($pageNum_comments < $totalPages_comments) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_comments=%d%s", $currentPage, min($totalPages_comments, $pageNum_comments + 1), $queryString_comments); ?>"><img src="Next.gif" border=0></a>
      <?php } // Show if not last page ?>
    </td>
    <td width="23%" align="center">
      <?php if ($pageNum_comments < $totalPages_comments) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_comments=%d%s", $currentPage, $totalPages_comments, $queryString_comments); ?>"><img src="Last.gif" border=0></a>
      <?php } // Show if not last page ?>
    </td>
  </tr>
</table><? }else{ echo "<h4 align=center>Комментариев нет.</h4>"; } ?>
<p><form action="" method="post" name="all_dell" id="all_dell">
  <input type="submit" name="Submit2" value="Удалить все комментарии к этой фотографии" class="but">
  <input name="del_comm" type="hidden" id="del_comm" value="<?=$_GET['comm']?>">
</form>
  <input name="back" type="button" id="back" onClick="MM_goToURL('self','/editor/editor.php?cat=<?=$cat?>');return document.MM_returnValue" value="Вернуться к галерее" class="but">
</p>
</body>
</html>
<?php
mysqli_free_result($comments);

mysqli_free_result($titles);
?>
