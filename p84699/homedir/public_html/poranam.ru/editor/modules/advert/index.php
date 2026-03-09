<? require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php');

$requiredUserLevel = array(1, 2); 
include($_SERVER['DOCUMENT_ROOT']."/editor/secure/secure.php"); 
(isset($submit))?$work_mode="write":$work_mode="read";
el_reg_work($work_mode, $login, $_GET['cat']);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Управление рекламой</title>
<link href="/editor/style.css" rel="stylesheet" type="text/css">
</head>
<?
if(intval($_POST['interval'])==0 || intval($_POST['interval'])=='' || empty($_POST['interval'])){$_POST['interval']=10;}

//Сохарняем новую площадку
if($_POST['action']=='new'){
	if(el_dbselect("INSERT INTO ad_places (name, maxW, maxH, `type`, `interval`, active) VALUES ('".addslashes($_POST['name'])."', '".intval
            ($_POST['maxW'])."', '".intval($_POST['maxH'])."', '".$_POST['type']."', '".intval($_POST['interval'])."', '".$_POST['active']."')", 0, $res)!=FALSE){
		echo '<script language=javascript>alert("Площадка создана!")</script>';
		el_updateDBanner();
	}
}

//Удаляем площадку
if($_POST['action']=='del'){
	if(el_dbselect("DELETE FROM ad_places WHERE id='".$_POST['id']."'", 0, $res)!=FALSE){
		echo '<script language=javascript>alert("Площадка №'.$_POST['id'].' удалена!")</script>';
		el_updateDBanner();
	}
}

//Сохраняем изменения в площадке
if($_POST['action']=='save'){
	if(el_dbselect("UPDATE ad_places SET name='".addslashes($_POST['name'])."', maxW='".addslashes($_POST['maxW'])."', maxH='".addslashes
            ($_POST['maxH'])."', `type`='".$_POST['type']."', `interval`='".$_POST['interval']."', active='".$_POST['active']."' WHERE id='".$_POST['id']."'", 0, $res)!=FALSE){
		echo '<script language=javascript>alert("Изменения сохранены!")</script>';
		el_updateDBanner();
	}
}

//Выводим список площадок
$ad=el_dbselect("SELECT * FROM ad_places", 0, $ad);
$adv=el_dbfetch($ad);
?>
<script language="javascript">
function doact(act, id){
	var err=0;
	if(act=='del'){
		var OK=confirm("Вы уверены, что хотите удалить площадку №"+id+" ?");
		if(OK){
			err=0;
		}else{
			err=1;
		}
	}
	if(err==0){
		document.act.action.value=act;
		document.act.id.value=id;
		document.act.submit();
	}
}

function dyn(obj){
	var d=document.getElementById('interv');
	if(obj.options[obj.selectedIndex].value=='dyn'){
		d.style.display='block';
	}else{
		d.style.display='none';
	}
}
</script>
<body>
<form name="act" method="post"><input name="action" type="hidden"><input type="hidden" name="id"></form>
<h4 align="center">Упраление рекламными площадками</h4>
<? 
if(mysqli_num_rows($ad)>0){
	echo '<h5 align=center>Список площадок.</h5>';
	do{ 
		if($_POST['action']=='edit' && $_POST['id']==$adv['id']){?>
			<div class='row'>
			<form method="post">
			<div id="left">№<?=$adv['id']?> 
			<input type="hidden" name="id" value="<?=$adv['id']?>">
			<input type="hidden" name="action" value="save">
			<input type="text" name="name" value="<?=$adv['name']?>">&nbsp; 
			<input size="5" type="text" name="maxW" value="<?=$adv['maxW']?>">x<input size="5" type="text" name="maxH" value="<?=$adv['maxH']?>">px&nbsp;
            Тип: <select name="type">
            <option value="std"<?=($adv['type']=='std')?' selected':''?>>Стандартная</option>
            <option value="dyn"<?=($adv['type']=='dyn')?' selected':''?>>Динамическая</option>
            </select>&nbsp;
            <? if($adv['type']=='dyn'){?>интервал <input size="3" type="text" name="interval" value="<?=$adv['interval']?>">c.&nbsp;<? }?>
			Включена: <input type="checkbox" name="active" value="1" <?=($adv['active']==1)?'checked':''?>></div>
			<div id='right'>
			<input type="image" title="Сохранить" src="/editor/img/menu_save.gif">&nbsp;
			</div>
			</form>
			</div>
		<? }else{ ?>
			<div class='row'>
			<div id="left">№<?=$adv['id']?> &laquo;<?=$adv['name']?>&raquo; [<?=$adv['maxW']?>x<?=$adv['maxH']?>px, <?=($adv['type']=='dyn')?'динамическая':'стандартная'?>, <?=($adv['active']==1)?'<font color=green>включена</font>':'<font color=red>отключена</font>'?>]</div>
			<div id='right'>
			<img border="0" title="Редактировть" src="/editor/img/menu_edit.gif" onClick="doact('edit', <?=$adv['id']?>)">&nbsp;
			<img border="0" title="Удалить" src="/editor/img/menu_delete.gif" onClick="doact('del', <?=$adv['id']?>)">
			</div>
			</div>
	<? } 
	}while($adv=el_dbfetch($ad));
}else{
	echo '<h4 align=center style="color:red">Пока нет ни одной рекламной площадки.</h4>';
}?>
<h5 align="center">Добавить новую площадку</h5>
<table border="0" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
<form method="post">
  <tr>
    <td align="right">Описание расположения:</td>
    <td><input name="name" type="text" id="name" size="40"></td>
  </tr>
<tr>
    <td align="right">Максимальные размеры :</td>
    <td>Ширина:
      <input name="maxW" type="text" id="maxW" value="468" size="5"> 
      px&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Высота: 
      <input name="maxH" type="text" id="maxH" value="60" size="5"> 
      px </td>
  </tr>
    <tr>
    <td align="right">Тип:</td>
    <td><select name="type" onChange="dyn(this)">
            <option value="std">Стандартная</option>
            <option value="dyn">Динамическая</option>
        </select>
	</td>
  </tr>
  <tr id="interv" style="display:none">
    <td align="right">Интервал смены баннеров (сек.):</td>
    <td><input type="text" name="interval" size="5" value="10">
	</td>
  </tr>
    <tr>
    <td align="right">Включена:</td>
    <td><input name="active" type="checkbox" id="active" value="1">
	<input type="hidden" name="action" value="new">
	</td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" name="Submit" value="Создать" class="but"></td>
  </tr>
  </form>
</table>
</body>
</html>
