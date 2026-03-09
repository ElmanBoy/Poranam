<script language="javascript">
function deltheme(id){
	document.del.id_del.value=id;
	document.del.submit();
}
</script>
<?
$cat=$_GET['cat'];

if(isset($_POST['add']) && $_POST['add']==1){
	if(empty($_POST['text']) || empty($_POST['email'])){
		echo "<script>alert('Не все поля заполнены!')</script>";
	}else{
		el_2ini('regModerator'.$cat, $_POST['email']);
		el_2ini('regText'.$cat, $_POST['text']);
		echo "<script>alert('Изменения сохранены!')</script>";
	}
}


$theme=el_dbselect("SELECT * FROM contact_prop WHERE cat=".$cat." AND `default`=0 ORDER BY sort ASC",0,$theme);
$th=el_dbfetch($theme);
?>
<table border="0" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
<form method="post" name="add">
  <tr><td align="center">
  Email по умолчанию для этого раздела:<br>
   <input name="email" type="text" id="email" size="30" value="<?=$site_property['regModerator'.$cat]?>">
  </td>
  </tr> 
  <tr>
    <td align="center">Текст после отправки данных<br>
    <textarea name="text" id="text" rows="5" cols="40"><?=stripslashes($site_property['regText'.$cat])?></textarea>
    <br><input name="Button" type="button" onClick="MM_openBrWindow('/editor/newseditor.php?field=text&form=add','editor','','595','500','true')" value="HTML-редактор" class="but"><br></td>
  </tr>
  <tr>
    <td colspan="3" align="center">
    <input type="hidden" name="add" value="1">
    <input type="submit" name="Submit" value="Сохранить" class="but"></td>
  </tr>
  </form>
</table>
