<script language="javascript">
function deltheme(id){
	document.del.id_del.value=id;
	document.del.submit();
}
</script>
<?
$cat=$_GET['cat'];

if(isset($_POST['id_del']) && strlen($_POST['id_del'])>0){
	$d=el_dbselect("DELETE FROM contact_prop WHERE id=".GetSQLValueString($_POST['id_del'], "int"),0,$d);
	echo "<script>alert('Тема удалена!')</script>";
}

if(isset($_POST['save']) && $_POST['save']==1){
	$theme1=el_dbselect("SELECT * FROM contact_prop WHERE `default`=0 AND cat=".$cat,0,$theme1);
	$th1=el_dbfetch($theme1);
	do{
		$theme2=el_dbselect("UPDATE contact_prop SET name='".$_POST['name_'.$th1['id']]."', email='".$_POST['email_'.$th1['id']]."', sort='".$_POST['sort_'.$th1['id']]."'  WHERE id=".$th1['id'],0,$theme2);
	}while($th1=el_dbfetch($theme1));
	echo "<script>alert('Изменения сохранены!')</script>";
}

if(isset($_POST['def']) && $_POST['def']==1){
	if(empty($_POST['email'])){
		echo "<script>alert('Укажите Email!')</script>";
	}else{
		$ad=el_dbselect("INSERT INTO contact_prop (cat, email, `default`) VALUES (".$cat.", '".$_POST['email']."', 1)",0,$ad);
		echo "<script>alert('Email по умолчанию добавлен!')</script>";
	}
}

if(isset($_POST['def']) && $_POST['def']==0){
	$ad=el_dbselect("UPDATE contact_prop SET email='".$_POST['email']."' WHERE cat=".$cat." AND `default`=1",0,$ad);
	echo "<script>alert('Изменения сохранены!')</script>";
}

if(isset($_POST['add']) && $_POST['add']==1){
	if(empty($_POST['name']) || empty($_POST['email'])){
		echo "<script>alert('Не все поля заполнены!')</script>";
	}else{
		$ad=el_dbselect("INSERT INTO contact_prop (cat, name, email, `default`, sort) VALUES (".$cat.", '".$_POST['name']."', '".$_POST['email']."', 0, '".$_POST['sort']."')",0,$ad);
		echo "<script>alert('Тема добавлена!')</script>";
	}
}


$theme=el_dbselect("SELECT * FROM contact_prop WHERE cat=".$cat." AND `default`=0 ORDER BY sort ASC",0,$theme);
$th=el_dbfetch($theme);
?>
<form method="post" name="del"><input type="hidden" name="id_del"></form>
<? if(mysqli_num_rows($theme)>0){ ?>
<form method="post"><input type="hidden" name="save" value="1">
<table width="80%" border="0" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
  <tr>
    <td><strong>Название темы</strong></td>
    <td><strong>Email</strong></td>
    <td><strong>Номер</strong></td>
    <td>&nbsp;</td>
  </tr>
  <? do{?>
  <tr>
    <td><input name="name_<?=$th['id']?>" type="text" id="name_<?=$th['id']?>" size="50" value="<?=$th['name']?>"></td>
    <td><input name="email_<?=$th['id']?>" type="text" id="email_<?=$th['id']?>" size="30" value="<?=$th['email']?>"></td>
    <td><input name="sort_<?=$th['id']?>" type="text" id="sort_<?=$th['id']?>" size="3" value="<?=$th['sort']?>"></td>
    <td>&nbsp;&nbsp;&nbsp;
    <input type="button" name="Button" value="Удалить" class="but" onClick="deltheme(<?=$th['id']?>)"></td>
  </tr>
  <? }while($th=el_dbfetch($theme)); ?>
</table>
<p align="center">
  <input type="submit" name="Submit" value="Сохранить изменения" class="but"> </form>
</p>
<? }
$t=el_dbselect("SELECT * FROM contact_prop WHERE cat=".$cat." AND `default`=1",0,$t);
$td=el_dbfetch($t);
?><center>
<form method="post">Email по умолчанию для этого раздела: <input name="email" type="text" id="email" size="30" value="<?=$td['email']?>"><input type="submit" name="Submit" value="Сохранить" class="but"><input type="hidden" name="def" value="<?=(strlen($td['email'])>0)?'0':'1'?>"></form>
</center>
<table width="80%" border="0" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
<form method="post"><input type="hidden" name="add" value="1">
  <caption><b>Добавить тему</b></caption>
  <tr>
    <td>Тема:
    <input name="name" type="text" id="name" size="50"></td>
    <td>Email:
    <input name="email" type="text" id="email" size="30"></td>
    <td>Номер: 
	<? $theme2=el_dbselect("SELECT MAX(sort) FROM contact_prop WHERE cat=".$cat." AND `default`=0",0,$theme2, 'row'); ?>
    <input name="sort" type="text" id="sort" value="<?=$theme2['MAX(sort)']+1?>" size="3"></td>
  </tr>
  <tr>
    <td colspan="3" align="center"><input type="submit" name="Submit" value="Добавить" class="but"></td>
  </tr>
  </form>
</table>
