<?php
$cat=$_GET['cat'];

$currentPage = $_SERVER["PHP_SELF"];

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE guest SET email=%s, city=%s, `date`=%s, site=%s, name=%s, text=%s, answer=%s, sitename=%s, active=%s WHERE id=%s",
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['city'], "text"),
                       GetSQLValueString($_POST['date'], "date"),
                       GetSQLValueString($_POST['site'], "text"),
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['text'], "text"),
                       GetSQLValueString($_POST['answer'], "text"),
                       GetSQLValueString($_POST['sitename'], "text"),
					   GetSQLValueString((isset($_POST['active']))?1:0, "int"),
                       GetSQLValueString($_POST['id'], "int"));

  ;
  $Result1 = el_dbselect($updateSQL, 0, $Result1, 'result', true);

echo "<script language=javascript>alert('Изменения сохранены!');</script>";
}

if ((isset($_POST['del_id'])) && ($_POST['del_id'] != "")) {
  $deleteSQL = sprintf("DELETE FROM guest WHERE id=%s",
                       GetSQLValueString($_POST['del_id'], "int"));

  ;
  $Result1 = el_dbselect($deleteSQL, 0, $Result1, 'result', true);
}

$maxRows_tbguest = 10;
$pageNum_tbguest = 0;
if (isset($_GET['pageNum_tbguest'])) {
  $pageNum_tbguest = $_GET['pageNum_tbguest'];
}
$startRow_tbguest = $pageNum_tbguest * $maxRows_tbguest;

;
$query_tbguest = "SELECT * FROM guest ORDER BY id DESC";
$query_limit_tbguest = sprintf("%s LIMIT %d, %d", $query_tbguest, $startRow_tbguest, $maxRows_tbguest);
$tbguest = el_dbselect($query_limit_tbguest, 0, $tbguest, 'result', true);
$row_tbguest = el_dbfetch($tbguest);

if (isset($_GET['totalRows_tbguest'])) {
  $totalRows_tbguest = $_GET['totalRows_tbguest'];
} else {
  $all_tbguest = mysqli_query($dbconn, $query_tbguest);
  $totalRows_tbguest = mysqli_num_rows($all_tbguest);
}
$totalPages_tbguest = ceil($totalRows_tbguest/$maxRows_tbguest)-1;

$queryString_tbguest = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_tbguest") == false && 
        stristr($param, "totalRows_tbguest") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_tbguest = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_tbguest = sprintf("&totalRows_tbguest=%d%s", $totalRows_tbguest, $queryString_tbguest);

if(isset($_POST['prop']) && $_POST['prop']==1){
	if($_POST['guestadmin']!=$site_property['guestbook'.$cat]){
		el_2ini('guestbook'.$cat, $_POST['guestadmin']);
	}
}
?>


<script language="javascript">
function chek(id) {
if (confirm("Вы уверены, что хотите удалить запись №"+ id + " ?")) {
return true
} else { return false}
}
</script>
<? if ($totalRows_tbguest>0){?>

Записи с &nbsp;<?php echo ($startRow_tbguest + 1) ?> по <?php echo min($startRow_tbguest + $maxRows_tbguest, $totalRows_tbguest) ?> из <?php echo $totalRows_tbguest ?>
<p>
<table border="0" width="50%" align="center">
<tr>
  <td width="20%" align="center">
    <?php if ($pageNum_tbguest > 0) { // Show if not first page ?>
    <a href="<?php printf("%s?pageNum_tbguest=%d%s", $currentPage, 0, $queryString_tbguest); ?>"><img src="First.gif" border=0></a>
    <?php } // Show if not first page ?>
  </td>
  <td width="20%" align="center">
    <?php if ($pageNum_tbguest > 0) { // Show if not first page ?>
    <a href="<?php printf("%s?pageNum_tbguest=%d%s", $currentPage, max(0, $pageNum_tbguest - 1), $queryString_tbguest); ?>"><img src="Previous.gif" border=0></a>
    <?php } // Show if not first page ?>
  </td>
  <td width="20%" align="center"><? $page=1; $pagen=0;  $countpage=$totalRows_tbguest/$maxRows_tbguest; 
	  do  { if ($pageNum_tbguest!=$pagen) {echo "<a href=guestadmin.php?pageNum_tbguest=".$pagen."&totalRows_tbguest=".$totalRows_tbguest.">".$page."</a>&nbsp;&nbsp;"; } else
	  { echo "<b>".$page."</b>&nbsp;&nbsp;"; }
	  $page++;  $pagen++; $countpage--;}
while  ($countpage>=0); 
?></td>
  <td width="20%" align="center">
    <?php if ($pageNum_tbguest < $totalPages_tbguest) { // Show if not last page ?>
    <a href="<?php printf("%s?pageNum_tbguest=%d%s", $currentPage, min($totalPages_tbguest, $pageNum_tbguest + 1), $queryString_tbguest); ?>"><img src="Next.gif" border=0></a>
    <?php } // Show if not last page ?>
  </td>
  <td width="20%" align="center">
    <?php if ($pageNum_tbguest < $totalPages_tbguest) { // Show if not last page ?>
    <a href="<?php printf("%s?pageNum_tbguest=%d%s", $currentPage, $totalPages_tbguest, $queryString_tbguest); ?>"><img src="Last.gif" border=0></a>
    <?php } // Show if not last page ?></td>
</tr>
</table>
</p>
<?php do { ?>
<table width=420 height="200" border=0 align="center" cellpadding=1 cellspacing=0 >
  <tr>
    <td valign="top"><table width=100% height="100%" border=0 cellpadding=2 cellspacing=0>
        <form action="<?php echo $editFormAction; ?>" name="form1" method="POST"><tr>
          <td colspan="3" bgcolor=#ccdce6><div align="center"><b><font color="#000099" size="2" face="verdana"> Запись № <?php echo $row_tbguest['id']; ?>
            <input name="id" type="hidden" id="id" value="<?php echo $row_tbguest['id']; ?>">
          </font></b></div></td>
        </tr>
        <tr>
          <td align=right bgcolor=#FFFFFD height=1><font color=#432b0a face=verdana size=2>
            <input name="date" style="width:150" value="<?php echo $row_tbguest['date']; ?>" title="Дата записи">
          </font></td>
          <td height=1 bgcolor=#fffffd><font color=#432b0a face=verdana size=2>
            <input name=name type=text id="name" style="width:150" title="Имя" value="<?php echo $row_tbguest['name']; ?>">
          </font> </td>
          <td bgcolor=#fffffd><input name=email type=text id="email" style="width:150" title="Email" value="<?php echo $row_tbguest['email']; ?>"></td>
        </tr>
        <tr>
          <td align=right bgcolor=#fffffd height=1><input name=city type=text id="city" style="width:150" title="Город" value="<?php echo $row_tbguest['city']; ?>"></td>
          <td height=1 bgcolor=#fffffd><input name=sitename type=text id="sitename" style="width:150" title="Название сайта" value="<?php echo $row_tbguest['sitename']; ?>"> </td>
          <td height=1 bgcolor=#fffffd><input name=site type=text id="site" style="width:150" title="URL сайта" value="<?php echo $row_tbguest['site']; ?>"></td>
        </tr>
        <tr>
          <td height=1 colspan="3" bgcolor=#FFFFFD><strong><font size="-1">&nbsp;ICQ</font></strong><font size="-1">: <?php echo $row_tbguest['icq']; ?>&nbsp;&nbsp;&nbsp; <strong>IP</strong>: <?php echo $row_tbguest['ip']/*str_replace('\.','.',$row_tbguest['ip'])*/; ?> </font> </td>
        </tr>
        <tr align="center">
          <td height=1 colspan="3" valign="top" bgcolor=#fffffd>
            <textarea name=text cols=70 rows=5 id="text" style="width:500" title="Текст записи"><?php echo $row_tbguest['text']; ?></textarea></td>
        </tr>
		<tr align="center">
          <td height=1 colspan="3" valign="top" bgcolor=#fffffd>Ответ администратора<br><textarea name=answer cols=70 rows=2 id="answer" style="width:500" title="Ответ администратора"><?php echo $row_tbguest['answer']; ?></textarea></td>
        </tr>
		<tr align="center">
          <td height=1 colspan="3" valign="top" bgcolor=#fffffd><label for="act<?=$row_tbguest['active']?>">
          <input type="checkbox" name="active" id="act<?=$row_tbguest['active']?>" value="1" <?=($row_tbguest['active']==1)?'checked':''?>/> Опубликовать</label>
          </td>
        </tr>
        <tr>
          <td bgcolor=#fffffd colspan=3>              <center>
&nbsp;&nbsp;
              <input name=a70 class="but" type=submit value="Cохранить изменения">
&nbsp;&nbsp;
              
                         </center></td>
        </tr><input type="hidden" name="MM_update" value="form1">
</form>
      </table></td></tr><tr><td>
<form action="<?php echo $editFormAction; ?>" method="post" name="<?php echo $row_tbguest['id']; ?>" style="height:30px; padding:1px; margin:0px; vertical-align:bottom;" onSubmit="return chek(<?php echo $row_tbguest['id']; ?>)">
  <input name="del_id" type="hidden" id="del_id" value="<?php echo $row_tbguest['id']; ?>">
  <center><input name="Submit" class="but" type="submit" value="Удалить запись №<?php echo $row_tbguest['id']; ?>"></center>
</form></td></tr></table>


<br>
<?php } while ($row_tbguest = el_dbfetch($tbguest)); ?>
<table border="0" width="50%" align="center">
  <tr>
    <td width="20%" align="center">
      <?php if ($pageNum_tbguest > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_tbguest=%d%s", $currentPage, 0, $queryString_tbguest); ?>"><img src="First.gif" border=0></a>
      <?php } // Show if not first page ?>
    </td>
    <td width="20%" align="center">
      <?php if ($pageNum_tbguest > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_tbguest=%d%s", $currentPage, max(0, $pageNum_tbguest - 1), $queryString_tbguest); ?>"><img src="Previous.gif" border=0></a>
      <?php } // Show if not first page ?>
    </td>
    <td width="20%" align="center"><? $page=1; $pagen=0;  $countpage=$totalRows_tbguest/$maxRows_tbguest; 
	  do  { if ($pageNum_tbguest!=$pagen) {echo "<a href=guestadmin.php?pageNum_tbguest=".$pagen."&totalRows_tbguest=".$totalRows_tbguest.">".$page."</a>&nbsp;&nbsp;"; } else
	  { echo "<b>".$page."</b>&nbsp;&nbsp;"; }
	  $page++;  $pagen++; $countpage--;}
while  ($countpage>=0); 
?></td>
    <td width="20%" align="center">
      <?php if ($pageNum_tbguest < $totalPages_tbguest) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_tbguest=%d%s", $currentPage, min($totalPages_tbguest, $pageNum_tbguest + 1), $queryString_tbguest); ?>"><img src="Next.gif" border=0></a>
      <?php } // Show if not last page ?>
    </td>
    <td width="20%" align="center">
      <?php if ($pageNum_tbguest < $totalPages_tbguest) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_tbguest=%d%s", $currentPage, $totalPages_tbguest, $queryString_tbguest); ?>"><img src="Last.gif" border=0></a>
      <?php } // Show if not last page ?></td>
  </tr>
</table>
<?php
mysqli_free_result($tbguest);
}else{ echo "<h4 align='center'>В гостевой книге нет ни одной записи</h4>";}
?>
<p>
<center>
<form method="post"> 
E-mail модератора гостевой книги<br>
<input type="text" name="guestadmin" value="<?=$site_property['guestbook'.$cat]?>"><br><br>
<input type="hidden" name="prop" value="1">
<input type="submit" value="Сохранить">
</form>
</center>
</p>