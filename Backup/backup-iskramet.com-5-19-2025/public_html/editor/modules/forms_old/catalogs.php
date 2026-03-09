<? require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php');


$requiredUserLevel = array(1, 2); 
include($_SERVER['DOCUMENT_ROOT']."/editor/secure/secure.php"); 
(isset($_POST['Submit']))?$work_mode="write":$work_mode="read";
el_reg_work($work_mode, $login, $_GET['cat']);

 ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Управление каталогами</title>
<link href="/editor/style.css" rel="stylesheet" type="text/css">
<script src="/editor/modules/forms/forms.js"></script>
<script language="javascript">
<!--
function check(item_name){
var OK=confirm('Вы действительно хотите удалить форму "'+item_name+'" ?');
if (OK) {return true} else {return false}
}
function check_el(item_name){
var OK=confirm('Вы действительно хотите удалить поле "'+item_name+'" ?');
if (OK) {return true} else {return false}
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

function select_size(){
var tf=document.getElementById("type");
var sf=document.getElementById("sizefield");
var ar=document.getElementById("area");
var op=document.getElementById("oplist");
var db=document.getElementById("fromdb");
	switch(tf.options[tf.selectedIndex].value){
	case "textarea": 		ar.style.display="block";
							sf.style.display="none";
							op.style.display="none";
							db.style.display="none";
							break;
	case "option":
	case "optionlist":		op.style.display="block";
							sf.style.display="block";
							ar.style.display="none";
							db.style.display="none";
							break;
	case "list_fromdb":		db.style.display="block";
							sf.style.display="block";
							ar.style.display="none";
							op.style.display="none";
							break;
	default:				sf.style.display="block";
							ar.style.display="none";
							op.style.display="none";
							db.style.display="none";
	}
}

function check_digit(el_id){
var cf=document.getElementById(el_id);
	if((cf.value!='0')||(cf.value!='1')||(cf.value!='2')||(cf.value!='3')||(cf.value!='4')||(cf.value!='5')||(cf.value!='6')||(cf.value!='7')||(cf.value!='8')||(cf.value!='9')||(cf.value!='')){
		alert ("В это поле можно вводить только целые числа!");
		cf.value="";
	}
}

pszFont = "Tahoma,8,,BOLD";

function MM_findObj(n, d) { 
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_validateForm() { 
  var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
  for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=MM_findObj(args[i]);
    if (val) { nm=val.name; if ((val=val.value)!="") {
      if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
        if (p<1 || p==(val.length-1)) errors+='- '+nm+' must contain an e-mail address.\n';
      } else if (test!='R') { num = parseFloat(val);
        if (isNaN(val)) errors+='- '+nm+' должен содержать только целые числа.\n';
        if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
          min=test.substring(8,p); max=test.substring(p+1);
          if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
    } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' is required.\n'; }
  } if (errors) alert('Оишбка заполнения формы:\n'+errors);
  document.MM_returnValue = (errors == '');
}

function line_over(id){
document.getElementById("string"+id).style.backgroundColor="#DEE7EF";
document.getElementById("img"+id).src="/editor/img/leftmenu_arrow.gif";
}
function line_out(id){
document.getElementById("string"+id).style.backgroundColor="#CCDCE6";
document.getElementById("img"+id).src="/editor/img/spacer.gif";
}

function savechange(){
document.frm1.cat_mode_edit1.value='save';
document.frm1.submit();
}

function showhideDiv(name){
	var d=document.getElementById(name);
	var dc=document.getElementById(name+"_child");
	if(dc.style.display=="none"){
		d.className="row_block";
		dc.style.marginLeft=40+"px";
		dc.style.display="block";
	}else{
		d.className="row_none";
		dc.style.display="none";
	}
}

var sInitColor = null; 
function callColorDlg(field, td){
	if (sInitColor == null) 
		var sColor = dlgHelper.ChooseColorDlg(); 
	else 
		var sColor = dlgHelper.ChooseColorDlg(sInitColor);	
	sColor = sColor.toString(16); 
	if (sColor.length < 6) { 
		var sTempString = "000000".substring(0,6-sColor.length); 
		sColor = sTempString.concat(sColor);
	} 
	document.execCommand("ForeColor", false, sColor); 
	sInitColor = sColor; 
	document.getElementById(field).value = sInitColor; 
	document.getElementById(td).style.backgroundColor = sInitColor;
}

function selectOne(obj){
	var des=document.getElementsByTagName("INPUT");
	for(i=0; i<des.length; i++){
		if(des[i].id=="titleRow"){des[i].checked=false;}
	}
	obj.checked=true;
}

function delParam(id, name, field){
	if(check_el(name)){
		frm1.cat_mode_edit1.value='del'; 
		frm1.field_id.value=id;
		frm1.field.value=field;
		frm1.submit();
	}
}
//-->
</script>
<style type="text/css">
.over {
	background-color:#CCDCE6
}
.out {
	background-color:#003399
}
.style1 {
	color: #FF0000;
	font-size:10px
}
td#sampleField textarea,td#sampleField select{width:150px}

</style>
</head>
<body>
<form method="post" name="act">
  <input type="hidden" name="action">
  <input type="hidden" name="id">
</form>
<center>
<? if($_GET['mode']!="editfield" && !isset($_GET['viewfield'])){?>
<p> <a href="params.php<?=(!empty($_GET['cat']))?'?cat='.$_GET['cat']:''?>">Список полей</a> 
  |
  <?=($_GET['mode']=='new')?'<b>Создать форму</b>':'<a href="?mode=new'.((!empty($_GET['cat']))?'&cat='.$_GET['cat']:'').'">Создать форму</a> '?>
  |
  <?=($_GET['mode']=='list')?'<b>Список форм</b>':'<a href="?mode=list'.((!empty($_GET['cat']))?'&cat='.$_GET['cat']:'').'">Список форм</a> '?>

<? } 
if(!empty($_GET['cat'])){?>
|
<a href="'/editor/editor.php?cat=<?=$_GET['cat']?>"> &laquo; Вернуться к редактированию раздела</a>
<? } if(!empty($_GET['id'])){?>
|
<a href="/editor/modules/forms/edit_form.php?id=<?=$_GET['id']?><?=(!empty($_GET['cat']))?'&cat='.$_GET['cat']:''?>">Редактировать настройки формы &raquo;</a>
<? }?>
</p>
</center>
<?
//Смотрим список каталогов
 if(($_GET['mode']=="list")||(!isset($_GET['mode']))&&(!isset($_GET['cid'])) && !isset($_GET['new_id']) && (!isset($_GET['id']))&& (!isset($_GET['viewfield']))){//Показ списка каталогов

	if($_POST['cat_mode']=="del"){//Удаление каталога
  		$deleteSQL = sprintf("DELETE FROM forms WHERE id=%s",
                       GetSQLValueString($_POST['delid'], "text"));
  		
		$Result1=el_dbselect($deleteSQL, 0, $Result1);
	  
		$id_del=$_POST['id_del'];
		$deleteSQL = "DROP TABLE IF EXISTS form_".$id_del."_data";
		
		$Result1=el_dbselect($deleteSQL, 0, $Result1);
		
		 $deleteSQL = sprintf("DELETE FROM modules WHERE name=%s",
                       GetSQLValueString($_POST['name'], "text"));
  	
  		$Result1=el_dbselect($deleteSQL, 0, $Result1);
		
		$deleteSQL = sprintf("DELETE FROM form_prop WHERE catalog_id=%s",
						   GetSQLValueString($_POST['id_del'], "text"));
		
		$Result1=el_dbselect($deleteSQL, 0, $Result1);
		el_clearcache('forms', '');
		echo "<script>alert('Форма удалена!')</script>";
	}
	
	if($_POST['cat_mode']=="save"){//Сохранение изменений
	
		
		$query_cats_check = "SELECT * FROM forms WHERE name='".$_POST['name']."' AND catalog_id<>'".$_POST['id_del']."'";
		$cats_check=el_dbselect($query_cats_check, 0, $cats_check);
		$totalRows_cats = mysqli_num_rows($cats_check);
		if($totalRows_cats>0){
			echo "<script>alert('Форма с таким названием уже есть! Выберите другое название.')</script>";
		}else{
			$updateSQL = sprintf("UPDATE forms SET name=%s WHERE catalog_id=%s",
                       GetSQLValueString($_POST['name'], "text"),
					   GetSQLValueString($_POST['id_del'], "text"));
 		 	
  			$Result1=el_dbselect($updateSQL, 0, $Result1);
			$mod_check=el_dbselect("SELECT id FROM modules WHERE type='forms".$_POST['id_del']."'", 0, $mod_check); 
			if(mysqli_num_rows($mod_check)>0){  
				el_dbselect("UPDATE modules SET name='Форма ".$_POST['name']."' WHERE type='forms".$_POST['id_del']."'", 0, $res);
			}else{
				el_dbselect("INSERT INTO modules (type, name, status, path, sort) VALUES 
				('forms".$_POST['id_del']."', 'Форма ".$_POST['name']."', 'Y', 'modules/forms', 1000)", 0, $res);
			}
  			el_clearcache('forms', '');
			echo "<script>alert('Изменения сохранены!')</script>";
  		}
	}



$query_cats = "SELECT * FROM forms";
$cats=el_dbselect($query_cats, 0, $cats);
$row_cats = el_dbfetch($cats);
$totalRows_cats = mysqli_num_rows($cats);
	if($totalRows_cats<1){ echo "<h4 align='center'>Нет ни одной формы</h4>";}else{?>
<table width="80%" border="0" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
  <tr>
    <td align="center" style="background-color:#b1c5d2">Название</td>
    <td colspan="4" align="center" style="background-color:#b1c5d2">Действия</td>
  </tr>
  <? do{?>
  <tr id="string<?=$row_cats['id']; ?>">
    <form method="post" action="<?=$_SERVER['REQUEST_URI']?>">
      <td width="50%" style="color:#003399" onMouseOver='document.getElementById("string<?=$row_cats['id']; ?>").style.backgroundColor="#DEE7EF"' onMouseOut='document.getElementById("string<?=$row_cats['id']; ?>").style.backgroundColor="#CCDCE6"'><input name="name" type="text" value="<?=$row_cats['name']?>" size="40"></td>
      <td>
      <input name="id_del" type="hidden" id="id_del" value="<?=$row_cats['catalog_id']?>">
      <input name="delid" type="hidden" id="delid" value="<?=$row_cats['id']?>">
        <input name="cat_mode" type="hidden" id="cat_mode">
        <a href="edit_form.php?id=<?=$row_cats['id']?>"><img src="/editor/img/leftmenu_tools.gif" alt="Настройки формы" border="0"></a> </td>
      <td><a href="catalogs.php?id=<?=$row_cats['id']?>"><img src="/editor/img/menu_edit.gif" alt="Редактировать структуру" width="23" height="17" border="0"></a> </td>
      <td><input name="submit_save" type="image" id="submit_save2" src="/editor/img/menu_save.gif" alt="Сохранить изменения" onClick="cat_mode.value='save';">
      </td>
      <td><input name="submit_del" type="image" id="submit_del" src="/editor/img/menu_delete.gif" alt="Удалить форму" onClick="cat_mode.value='del';return check('<?=$row_cats['name']?>'); ">
      </td>
    </form>
  </tr>
  <? }while($row_cats = el_dbfetch($cats));?>
</table>
<? }}?>
<? //Наполняем каталог полями
  
if((isset($_GET['id']) || isset($_GET['edit_cat']) || isset($_GET['new_id'])) && $_GET['mode']!="new"){ 
	$id_form=(isset($_GET['id']) && strlen($_GET['id'])>0)?$_GET['id']:$_GET['new_id'];

	$query_cats = "SELECT * FROM forms WHERE id='$id_form'";
	$cats=el_dbselect($query_cats, 0, $cats);
	$row_cats = el_dbfetch($cats); 
	$id_cat=$row_cats['catalog_id'];
	
	if(isset($_GET['new_id']) && $row_cats['from_catalog']=='1'){
		$new_fields=el_dbselect("SELECT * FROM catalog_prop WHERE catalog_id='".$id_cat."' AND inform='1'", 0, $new_fields);
		$option=el_dbfetch($new_fields);
		$lastField=0;
		do{
			$lastField++;
			$title=($lastField==1)?'1':'0';
			$insertSQL = sprintf("INSERT INTO form_prop (option_id, name, type, size, cols, rows, sort, title, inform, required, catalog_id, field, options, listdb, from_field, revalid) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
								   GetSQLValueString($option['option_id'], "text"),
								   GetSQLValueString($option['name'], "text"),
								   GetSQLValueString($option['type'], "text"),
								   GetSQLValueString($option['size'], "int"),
								   GetSQLValueString($option['cols'], "int"),
								   GetSQLValueString($option['rows'], "int"),
								   GetSQLValueString($option['sort'], "int"),
								   GetSQLValueString($title, 'int'),
								   GetSQLValueString('1', 'int'),
								   GetSQLValueString('0', 'int'),
								   GetSQLValueString($id_cat, "text"),
								   GetSQLValueString($lastField, "int"),
								   GetSQLValueString($option['options'], "text"),
								   GetSQLValueString($option['listdb'], "text"),
								   GetSQLValueString($option['from_field'], "int"),
								   GetSQLValueString($option['revalid'], "int"));
			
			  
			$Result1=el_dbselect($insertSQL, 0, $Result1);
			  
			  $name="field".$lastField;
			  switch ($option['type']){
				case "integer": $type="INTEGER";
				break;
				case "textarea":
				case "optionlist":
				case "option":
				case "select": $type="LONGTEXT";
				break;
				case "float":
				case "price": $type="DOUBLE";
				break;
				default: $type="TEXT";
				break;
			  }
			  
			$query_catdata = "ALTER TABLE form_".$id_cat."_data ADD `$name` $type NULL";
			$catdata=el_dbselect($query_catdata, 0, $catdata);
			
		}while($option=el_dbfetch($new_fields));
		el_clearcache('forms', '');
		echo '<script>document.location.href="catalogs.php?id='.$id_form.'"</script>';
	}

	if($_POST['cat_mode_edit1']=="del"){
		$deleteSQL = sprintf("DELETE FROM form_prop WHERE id=%s",
						   GetSQLValueString($_POST['field_id'], "int"));
		
		$Result1=el_dbselect($deleteSQL, 0, $Result1);
		
		
		$query_catdata = "ALTER TABLE form_".$_POST['catalog_id']."_data DROP COLUMN field".$_POST['field'];
		$catdata=el_dbselect($query_catdata, 0, $catdata);
		el_clearcache('forms', '');
	}
	
	if($_POST['cat_mode_edit1']=="save"){
		
		$query_save = "SELECT * FROM form_prop WHERE catalog_id='$id_cat'";
		$catsave=el_dbselect($query_save, 0, $catsave);
		$row_catsave = el_dbfetch($catsave); 
		$frmCat=el_dbselect("SELECT from_catalog FROM forms WHERE id='".intval($_GET['id'])."'", 0, $frmCat, 'row');
		do{
	  		$_POST['defval'.$row_catsave['id']]=(is_array($_POST['defval'.$row_catsave['id']]))
			?@implode(';',$_POST['defval'.$row_catsave['id']]):$_POST['defval'.$row_catsave['id']];
			if($frmCat['from_catalog']=='1'){
				$updateSQL = sprintf("UPDATE catalog_prop SET name=%s, sort=%s, title=%s, inform=%s, required=%s, default_value=%s 
				WHERE catalog_id='$id_cat' AND name='".$row_catsave['name']."'",
						   GetSQLValueString($_POST['name'.$row_catsave['id']], "text"),
						   GetSQLValueString($_POST['sort'.$row_catsave['id']], "int"),
						   GetSQLValueString(isset($_POST['title'.$row_catsave['id']]) ? "true" : "", "defined","'1'","'0'"),
						   GetSQLValueString(isset($_POST['inform'.$row_catsave['id']]) ? "true" : "", "defined","'1'","'0'"),
						   GetSQLValueString(isset($_POST['required'.$row_catsave['id']]) ? "true" : "", "defined","'1'","'0'"),
						   GetSQLValueString($_POST['defval'.$row_catsave['id']], "text"));
				$Result1=el_dbselect($updateSQL, 0, $Result1);			
			}
			$updateSQL = sprintf("UPDATE form_prop SET name=%s, sort=%s, title=%s, inform=%s, required=%s, default_value=%s WHERE id=%s",
                       GetSQLValueString($_POST['name'.$row_catsave['id']], "text"),
					   GetSQLValueString($_POST['sort'.$row_catsave['id']], "int"),
					   GetSQLValueString(isset($_POST['title'.$row_catsave['id']]) ? "true" : "", "defined","'1'","'0'"),
					   GetSQLValueString(isset($_POST['inform'.$row_catsave['id']]) ? "true" : "", "defined","'1'","'0'"),
					   GetSQLValueString(isset($_POST['required'.$row_catsave['id']]) ? "true" : "", "defined","'1'","'0'"),
					   GetSQLValueString($_POST['defval'.$row_catsave['id']], "text"),
                       GetSQLValueString($row_catsave['id'], "int"));
  			$Result1=el_dbselect($updateSQL, 0, $Result1);		

		}while($row_catsave = el_dbfetch($catsave));
		
		el_clearcache('forms', '');
		echo "<script>alert('Изменения сохранены!')</script>";
	}
	
	if((isset($_POST['new_element']))&&($_POST['new_element']=="1")){ 
		for($i=0; $i<count($_POST['type']); $i++){
			$option=el_dbselect("SELECT * FROM form_options WHERE id=".intval($_POST['type'][$i]), 0, $option, 'row');
			$row_cat2=el_dbselect( "SELECT MAX(field), MAX(sort) FROM form_prop WHERE catalog_id='".$id_cat."'", 0, $row_cat2, 'row');
			$lastField=$row_cat2['MAX(field)']+1;
			$title=($lastField==1)?'1':'0';
			$insertSQL = sprintf("INSERT INTO form_prop (option_id, name, type, size, cols, rows, sort, title, inform, required, catalog_id, field, options, listdb, from_field) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
							   GetSQLValueString($option['id'], "text"),
							   GetSQLValueString($option['name'], "text"),
							   GetSQLValueString($option['type'], "text"),
							   GetSQLValueString($option['size'], "int"),
							   GetSQLValueString($option['cols'], "int"),
							   GetSQLValueString($option['rows'], "int"),
							   GetSQLValueString($option['sort'], "int"),
							   GetSQLValueString($title, 'int'),
							   GetSQLValueString('1', 'int'),
							   GetSQLValueString('0', 'int'),
							   GetSQLValueString($id_cat, "text"),
							   GetSQLValueString($lastField, "int"),
							   GetSQLValueString($option['options'], "text"),
							   GetSQLValueString($option['listdb'], "text"),
							   GetSQLValueString($option['from_field'], "int"));
		
		  
		  $Result1=el_dbselect($insertSQL, 0, $Result1);
		  
		  $name="field".$lastField;
		  switch ($option['type']){
			case "integer": $type="INTEGER";
			break;
			case "textarea":
			case "optionlist":
			case "option":
			case "select": $type="LONGTEXT";
			break;
			case "float":
			case "price": $type="DOUBLE";
			break;
			default: $type="TEXT";
			break;
		  }
		  
			$query_catdata = "ALTER TABLE form_".$id_cat."_data ADD `$name` $type NULL";
			$catdata=el_dbselect($query_catdata, 0, $catdata);
			el_clearcache('forms', '');
	}
}


 
$query_cat = "SELECT * FROM form_prop WHERE catalog_id='".$row_cats['catalog_id']."' ORDER BY sort";
$cat=el_dbselect($query_cat, 0, $cat);
$row_cat = el_dbfetch($cat);
$paramArray=array();
if(mysqli_num_rows($cat)>0){
?>
<form method="post" name="frm1">
  <input name="cat_mode_edit1" type="hidden" id="cat_mode_edit1">
  <input name="field_id" type="hidden" id="field_id">
  <input name="field" type="hidden" id="field">
  <input name="catalog_id" type="hidden" id="catalog_id" value="<?=$id_cat?>">
  <div align="right">
    <input type="button" value="Сохранить изменения" onClick="savechange()" class="but">
  </div>
  <table border="0" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
    <caption>
    Свойства формы <b>"
    <?=$row_cats['name']?>
    "</b>
    </caption>
    <tr>
      <td align="center" style="background-color:#b1c5d2">Название</td>
      <td align="center" style="background-color:#b1c5d2">Значение<br>
        по умолчанию <img src="/editor/img/help_button.gif" alt="Кликните для получения справки" width="12" height="12" style="cursor:pointer" onClick="test.TextPopup ('Этот параметр показывает, какое значение будет задано полю по умолчанию',pszFont,10,10,-1,-1)"></td>
      <td align="center" style="background-color:#b1c5d2">Заголовок <img src="/editor/img/help_button.gif" alt="Кликните для получения справки" width="12" height="12" style="cursor:pointer" onClick="test.TextPopup ('Этот параметр показывает, является ли это поле заголовком записи',pszFont,10,10,-1,-1)"></td>
      <td align="center" style="background-color:#b1c5d2">В форме <img src="/editor/img/help_button.gif" alt="Кликните для получения справки" width="12" height="12" style="cursor:pointer" onClick="test.TextPopup ('Этот параметр показывает, будет ли это поле присутствовать в форме ввода на сайте',pszFont,10,10,-1,-1)"></td>
      <td align="center" style="background-color:#b1c5d2">Обязательное<br>
        поле<img src="/editor/img/help_button.gif" alt="Кликните для получения справки" width="12" height="12" style="cursor:pointer" onClick="test.TextPopup ('Этот параметр показывает, будет ли это поле обязательным к заполнению',pszFont,10,10,-1,-1)"></td>
      <td align="center" style="background-color:#b1c5d2">Номер <img src="/editor/img/help_button.gif" alt="Кликните для получения справки" width="12" height="12" style="cursor:pointer" onClick="test.TextPopup ('Номер необходим, что бы указать в какой последовательностидолжны выводится поля',pszFont,10,10,-1,-1)"></td>
      <td align="center" style="background-color:#b1c5d2">Удалить</td>
    </tr>
    <? do{ 
  $paramArray[]=$row_cat['name'];
  ?>
    <tr onMouseOver="line_over('<?=$row_cat['id']?>')" onMouseOut="line_out('<?=$row_cat['id']?>')">
      <td valign="top" id="string<?=$row_cat['id']?>"><?
($row_cat['title']==1)?$chek0="checked":$chek0="";	
($row_cat['list']==1)?$chek="checked":$chek="";
($row_cat['search']==1)?$chek1="checked":$chek1="";
($row_cat['show_name']==1)?$chek2="checked":$chek2="";
($row_cat['detail']==1)?$chek3="checked":$chek3="";
($row_cat['inform']==1)?$chek4="checked":$chek4="";
($row_cat['required']==1)?$chek5="checked":$chek5="";
	?>
        <img src="/editor/img/spacer.gif" name="img<?=$row_cat['id']?>" width="19" height="17" align="absmiddle" id="img<?=$row_cat['id']?>"> <nobr>
        <input name="name<?=$row_cat['id']?>" type="text" id="name<?=$row_cat['id']?>" value="<?=$row_cat['name']?>" size="30">
        </nobr>
        <div style="text-align:center; font-size:10px; color:#006600">Имя поля: field
          <?=$row_cat['field']?>
        </div></td>
      <td valign="top" id="sampleField"><?=el_viewField($row_cat['type'], $row_cat, 'defval'.$row_cat['id'], $row_cat['default_value'])?></td>
      <td align="center" valign="top"><input id="titleRow" onClick="selectOne(this)" name="title<?=$row_cat['id']?>" type="radio"  <?=$chek0?>></td>
      <td align="center" valign="top"><input name="inform<?=$row_cat['id']?>" type="checkbox" id="inform<?=$row_cat['id']?>" <?=$chek4?>></td>
      <td align="center" valign="top"><input name="required<?=$row_cat['id']?>" type="checkbox" id="required<?=$row_cat['id']?>" <?=$chek5?>></td>
      <td align="right" valign="top"><input name="sort<?=$row_cat['id']?>" type="text" id="sort<?=$row_cat['id']?>" onBlur="MM_validateForm('sort<?=$row_cat['id']?>','','NisNum');return document.MM_returnValue" value="<?=$row_cat['sort']?>" size="3"></td>
      <td align="center"><input name="submit_del2" type="image" id="submit_del2" src="/editor/img/menu_delete.gif" alt="Удалить" onClick="delParam(<?=$row_cat['id']?>, '<?=$row_cat['name']?>', <?=$row_cat['field']?>)"></td>
    </tr>
    <? }while($row_cat = el_dbfetch($cat)); ?>
    <tr><td colspan="3">&nbsp;</td>
    <td><label for="informTotal">
	   <script language="javascript">
	   document.writeln('<input type="checkbox" id="informTotal" onClick="checkAll(this.form,\'inform\',this.checked)"');
	   if(isChecked(document.frm1,'inform'))document.writeln(' checked');
	   document.writeln('>');
	   </script>
        Отметить все</label>       </td>
       <td><label for="requiredTotal">
	   <script language="javascript">
	   document.writeln('<input type="checkbox" id="requiredTotal" onClick="checkAll(this.form,\'required\',this.checked)"');
	   if(isChecked(document.frm1,'required'))document.writeln(' checked');
	   document.writeln('>');
	   </script>
        Отметить все</label>        </td>
        <td colspan="2">&nbsp;</td>
        </tr>
  </table>
  <div align="right">
    <input type="button" value="Сохранить изменения" onClick="savechange()" class="but">
  </div>
</form>
<? }
$frmName=el_dbselect("SELECT name FROM forms WHERE id='".intval($_GET['id'])."'", 0, $frmName, 'row');
?>
<form method="post" action="<?=$_SERVER['REQUEST_URI']?>" name="newform">
<table width="500" border="0" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
  <caption>
  Список полей формы &laquo;<?=$frmName['name']?>&raquo;
  </caption>
    <tr>
      <td ><input name="cat_action" type="hidden" id="cat_action" value="step2">
        <input name="catalog_id" type="hidden" id="catalog_id" value="<?=$row_cats['catalog_id']?>">
        <?
		$op=el_dbselect("SELECT id, name FROM form_options ORDER BY sort", 0, $op);
		$rop=el_dbfetch($op);
		$totalParams=mysqli_num_rows($op);
		if($totalParams>0){
		?>
        <label for="checkTotal"><input type="checkbox" id="checkTotal" onClick="checkAll(this.form,'type[]',this.checked)"> 
        Отметить все</label>
        <hr>
        <div style="width:500px; height:<?=(($totalParams-count($paramArray))*15)?>px; overflow:auto">
          <? $countParams=0;
			do{
				if(!in_array($rop['name'], $paramArray)){
					$countParams++;
		?>
          <input type="checkbox" name="type[]" value="<?=$rop['id']?>" id="prop<?=$rop['id']?>">
          &nbsp;
          <label for="prop<?=$rop['id']?>">
          <?=$rop['name']?>
          </label>
          <br>
          <?
				}
			}while($rop=el_dbfetch($op));
			if($countParams==0){
				echo 'В этой форме использованы все созданные поля';
			}
		}else{
			echo '<span style="color:red">Не создано ни одно поле.<br>Пожалуйста, перейдите в 
			<a href="params.php">Список полей</a><br>и создайте набор полей.</span>';
		}
		?>
        </div>
        <br>
        <div id="oplist" style="display:none"><br>
          Впишите пункты списка через точку с запятой, без пробелов.<br>
          <textarea name="options" cols="40" rows="6" id="options"></textarea>
        </div>
        <div id="fromdb" style="display:none"><br>
          <?
$list_db=el_dbselect("select catalog_id, name from catalogs", 0, $list_db);
$row_list_db=el_dbfetch($list_db);
?>
          <nobr>Каталог:
          <select name="listdb" id="listdb" onChange="field_frame.location.href('catalogs.php?catalog_id='+listdb.options[listdb.selectedIndex].value+'&viewfield'); field_div.style.display='block'">
            <option></option>
            <? do{?>
            <option value="<?=$row_list_db['catalog_id']?>">
            <?=$row_list_db['name']?>
            </option>
            <? }while($row_list_db=el_dbfetch($list_db)); ?>
          </select>
          <input type="hidden" id="from_field" name="from_field">
          </nobr><br>
          <br>
          <div style="display:none" id="field_div">
            <iframe frameborder="0" width="300" height="30" scrolling="no" id="field_frame" name="field_frame" hspace="0" marginheight="0" marginwidth="0" vspace="0"></iframe>
          </div>
        </div></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><? if($countParams>0){?>
        <input type="submit" name="Submit3" value="Добавить" class="but">
        <input name="new_element" type="hidden" id="new_element" value="1">
        <? }?>
      </td>
    </tr>
</table>
</form>
<? }





//Выбираем поля из другого каталога
if(isset($_GET['catalog_id']) && isset($_GET['viewfield'])){
$list_field=el_dbselect("select field, name from form_prop where catalog_id='".$_GET['catalog_id']."'", 0, $list_field);
$row_list_field=el_dbfetch($list_field);
?>
<div style="background-color:#CCDCE6; width:100%; height:100%; margin:0px"> <nobr>Поле:
  <select name="fromfield" id="fromfield" onChange="if(fromfield.options[fromfield.selectedIndex].value!=''){parent.document.getElementById('from_field').value=fromfield.options[fromfield.selectedIndex].value}else{parent.document.getElementById('from_field').value=''}">
    <option></option>
    <? do{ ?>
    <option value="<?=$row_list_field['field']?>" <?=(isset($_GET['from_field'])&&$_GET['from_field']==$row_list_field['field'])?"selected":""?>>
    <?=$row_list_field['name']?>
    </option>
    <? }while($row_list_field=el_dbfetch($list_field)); ?>
  </select>
  </nodr>
  <input name="field2" type="hidden" id="field2" value="<?=$row_cat2['MAX(field)']+1?>">
</div>
<? } 




//Редактируем характиристики поля в новом окне
if($_GET['mode']=="editfield"){
	if(isset($_POST['update'])){
		$query_opt = "SELECT field, catalog_id FROM form_prop WHERE option_id='".intval($_GET['field'])."'";
		$op=el_dbselect($query_opt, 0, $op);
		$opt=el_dbfetch($op);

		$id_cat=$_GET['catalog'];
				  $updateSQL = sprintf("UPDATE form_options SET type=%s, size=%s, cols=%s, rows=%s, options=%s, listdb=%s, from_field=%s, revalid=%s WHERE id=%s",
						   GetSQLValueString($_POST['type'], "text"),
						   GetSQLValueString($_POST['size'], "int"),
						   GetSQLValueString($_POST['cols'], "int"),
						   GetSQLValueString($_POST['rows'], "int"),
						   GetSQLValueString($_POST['options'], "text"),
						   GetSQLValueString($_POST['listdb'], "text"),
						   GetSQLValueString($_POST['from_field'], "int"),
						   GetSQLValueString(isset($_POST['revalid']) ? "true" : "", "defined","'1'","'0'"),  
						   GetSQLValueString($_GET['field'], "int"));
	  
	  $Result1=el_dbselect($updateSQL, 0, $Result1);
	  
	echo $updateSQL = sprintf("UPDATE form_prop SET type=%s, size=%s, cols=%s, rows=%s, options=%s, listdb=%s, from_field=%s, revalid=%s WHERE option_id=%s",
						   GetSQLValueString($_POST['type'], "text"),
						   GetSQLValueString($_POST['size'], "int"),
						   GetSQLValueString($_POST['cols'], "int"),
						   GetSQLValueString($_POST['rows'], "int"),
						   GetSQLValueString($_POST['options'], "text"),
						   GetSQLValueString($_POST['listdb'], "text"),
						   GetSQLValueString($_POST['from_field'], "int"),
						   GetSQLValueString(isset($_POST['revalid']) ? "true" : "", "defined","'1'","'0'"), 
						   GetSQLValueString($_GET['field'], "int"));
	  el_dbselect($updateSQL, 0, $res);
	  
	  do{
			switch ($_POST['type']){
				case "integer": $type="INTEGER";
				break;
				case "textarea":
				case "optionlist":
				case "option":
				case 'basic_html':
				case 'full_html':
				case "select": $type="LONGTEXT";
				break;
				case "float":
				case "price": $type="DOUBLE";
				break;
				default: $type="TEXT";
				break;
			}
				
			$query_catdata = "ALTER TABLE form_".$opt['catalog_id']."_data MODIFY field".$opt['field']." $type NULL";
			$catdata=el_dbselect($query_catdata, 0, $catdata);
		}while($opt=el_dbfetch($op));
	  	el_clearcache('forms', '');
		echo "<script>alert('Изменения сохранены!')</script>";
	}
	
	
	$query_cats = "SELECT * FROM form_options WHERE id='".intval($_GET['field'])."'";
	$cats=el_dbselect($query_cats, 0, $cats);
	$row_cat = el_dbfetch($cats); 

?>
<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
  <caption>
  <?="Поле: &laquo;".$row_cat['name']."&raquo;"?>
  </caption>
  <form method="post">
    <tr>
      <td>Тип поля формы:<span style="background-color:#b1c5d2"><img src="/editor/img/help_button.gif" alt="Кликните для получения справки" width="12" height="12" style="cursor:pointer" onClick="test.TextPopup ('Здесь можно задать тип поля в форме',pszFont,10,10,-1,-1)"></span> </td>
      <td><select name="type" id="type">
          <?
		include $_SERVER['DOCUMENT_ROOT'].'/editor/modules/catalog/props_array.php';
		while(list($key, $val)= each($props_array)){
			($row_cat['type']==$key)?$sel='selected':$sel='';
			echo '<option value="'.$key.'" '.$sel.'>'.$val.'</option>'."\n";
		}
		?>
        </select> 
        <br>
      <span style="color:red">Внимание!</span> Смена строкового типа на числовой приведет к потере имеющихся данных.</td>
    </tr>
    <tr>
      <td>Размер поля формы:<span style="background-color:#b1c5d2"><img src="/editor/img/help_button.gif" alt="Кликните для получения справки" width="12" height="12" style="cursor:pointer" onClick="test.TextPopup ('Этот параметр указывает размер поля формы',pszFont,10,10,-1,-1)"></span> </td>
      <td><? if($row_cat['type']=="textarea"){ ?>
        <nobr>Ширина:
        <input name="cols" type="text" id="cols<?=$row_cat['id']?>" value="<?=$row_cat['cols']?>" size="3" onBlur="MM_validateForm('cols<?=$row_cat['id']?>','','NisNum');return document.MM_returnValue">
        </nobr><br>
        <nobr>Высота:
        <input name="rows" type="text" id="rows<?=$row_cat['id']?>" value="<?=$row_cat['rows']?>" size="3" onBlur="MM_validateForm('rows<?=$row_cat['id']?>','','NisNum');return document.MM_returnValue">
        </nodr>
        <? }else{?>
        <input name="size" type="text" id="size<?=$row_cat['id']?>" value="<?=$row_cat['size']?>" size="3" onBlur="MM_validateForm('size<?=$row_cat['id']?>','','NisNum');return document.MM_returnValue">
        </nobr>
        <? }?></td>
    </tr>
    <? if($row_cat['type']=="option"||$row_cat['type']=="optionlist"){ ?>
    <tr>
      <td valign=top>Опции:<span style="background-color:#b1c5d2"><img src="/editor/img/help_button.gif" alt="Кликните для получения справки" width="12" height="12" style="cursor:pointer" onClick="test.TextPopup ('Здесь указываются пункты списка. Весь список будет показан в административной части, а на сайте только выбранные в административной части пункты.',pszFont,10,10,-1,-1)"></span> </td>
      <td>Впишите пункты списка через точку с запятой, без пробелов.<br>
        <textarea name="options" cols="40" rows="6" id="options"><?=$row_cat['options']?>
</textarea>
      </td>
    </tr>
    <? } 
 
 
  if($row_cat['type']=="list_fromdb"){ ?>
    <tr>
      <td><?
$list_db=el_dbselect("select catalog_id, name from catalogs", 0, $list_db);
$row_list_db=el_dbfetch($list_db);
?>
        <nobr>Каталог:
        <select name="listdb" id="listdb" onChange="field_frame.location.href('catalogs.php?catalog_id='+listdb.options[listdb.selectedIndex].value+'&viewfield&from_field=<?=$row_cat['from_field']?>')">
          <option></option>
          <? do{?>
          <option value="<?=$row_list_db['catalog_id']?>" <?=($row_list_db['catalog_id']==$row_cat['listdb'])?"selected":""?>>
          <?=$row_list_db['name']?>
          </option>
          <? }while($row_list_db=el_dbfetch($list_db)); ?>
        </select>
        <input type="hidden" id="from_field" name="from_field">
        </nobr></td>
      <td><script language="javascript">/*
field_frame.location.href('catalogs.php?catalog_id='+listdb.options[listdb.selectedIndex].value+'&viewfield&from_field=<?=$row_cat['from_field']?>');*/
</script>
        <iframe frameborder="0" width="300" height="30" scrolling="no" id="field_frame" name="field_frame" hspace="0" marginheight="0" marginwidth="0" vspace="0" src="catalogs.php?catalog_id=<?=$row_cat['listdb']?>&viewfield&from_field=<?=$row_cat['from_field']?>"></iframe></td>
    </tr>
    <? } ?>
    <tr>
      <td>Заполняется дважды</td>
      <td><input name="revalid" type="checkbox" id="revalid" value="1"<?=($row_cat['revalid']=='1')?' checked':''?>></td>
    </tr>
    <tr>
      <td><input type="submit" name="Submit4" value="Сохранить" class="but">
        <input name="update" type="hidden" id="update" value="1"></td>
      <td align="right"><input type="button" name="Submit5" value="Закрыть" onClick="window.close()" class="but"></td>
    </tr>
  </form>
</table>
<? } 










//Создаем новую форму  
  
 if($_GET['mode']=="new"){
 	$last_id=el_dbselect("SELECT MAX(id) as a FROM forms", 0, $new_id, 'row');
	$new_id=(strlen($_POST['catalog_id'])>0)?trim($_POST['catalog_id']):trim($_POST['tbl_name']);
	$err=0;
 	if($_POST['formsUpdate']=="new"){
		if(strlen($_POST['catalog_id'])==0){
			$new_table="form_".trim($_POST['tbl_name'])."_data";
			$query_cats_check = "SELECT * FROM forms WHERE name='".$_POST['name']."'";
			$cats_check=el_dbselect($query_cats_check, 0, $cats_check);
			$totalRows_cats = mysqli_num_rows($cats_check);
			if($totalRows_cats>0){
				echo "<script>alert('Форма с таким названием уже есть! Выберите другое название.'); location.href='".$_SERVER['HTTP_REFERER']."'</script>"; 
				$err++;
			}
			
			$query_cats_check = "SELECT * FROM form_".$new_table."_data";
			if(el_dbselect($query_cats_check, 0, $cats_check)!=false){
				echo "<script>alert('Таблица с таким именем в базе данных уже есть! Выберите другое имя.'); location.href='".$_SERVER['HTTP_REFERER']."'</script>"; 
				$err++;
			}
		}
		if($err==0){
			
			$query_catdata = "CREATE TABLE $new_table (
			`id` int(11) NOT NULL auto_increment,
			`cat` INT NULL,
			`active` BOOL NOT NULL,
			`sort` int(11) NOT NULL default '0',
			`goodid` INT NULL,
			UNIQUE KEY `id` (`id`)
			) TYPE=MyISAM;";
			el_dbselect($query_catdata, 0, $res);
			
			$from_catalog=(strlen($_POST['catalog_id'])>0 && substr_count($_POST['catalog_id'], 'adventor_new_form')==0)?'1':'0';
			$_POST['catalog_id']=(strlen($_POST['catalog_id'])>0)?$_POST['catalog_id']:$_POST['tbl_name'];
			
			el_dbselect("INSERT INTO forms (name, title, cat, method, action, target, sorce_table, catalog_id, from_catalog, type, ajax, email, email_charset, email_type, answer, protect, prevalid) VALUE (
			".GetSQLValueString($_POST['name'], 'text').",
			".GetSQLValueString($_POST['title'], 'text').",
			".GetSQLValueString(0, 'int').",
			".GetSQLValueString($_POST['method'], 'text').",
			".GetSQLValueString($_POST['action'], 'text').",
			".GetSQLValueString($_POST['target'], 'text').",
			".GetSQLValueString($_POST['tbl_name'], 'text').",
			".GetSQLValueString($_POST['catalog_id'], 'text').",
			".GetSQLValueString($from_catalog, 'int').",
			".GetSQLValueString($_POST['type'], 'text').",  
			".GetSQLValueString(isset($_POST['ajax']) ? "true" : "", "defined","'1'","'0'").",
			".GetSQLValueString($_POST['email'], 'text').",
			".GetSQLValueString($_POST['email_charset'], 'text').",
			".GetSQLValueString($_POST['email_type'], 'text').",
			".GetSQLValueString($_POST['answer'], 'text').",
			".GetSQLValueString(isset($_POST['protect']) ? "true" : "", "defined","'1'","'0'").",
			".GetSQLValueString(isset($_POST['prevalid']) ? "true" : "", "defined","'1'","'0'")." 
			)", 0, $res);
		  
			$insertSQL1 = sprintf("INSERT INTO modules (type, name, status, `path`, sort) VALUES (%s, %s, %s, %s, %s)",
							   GetSQLValueString("forms".$new_id, "text"),
							   GetSQLValueString('Форма '.$_POST['name'], "text"),
							   GetSQLValueString("Y", "text"),
							   GetSQLValueString("modules/forms", "text"),
							   GetSQLValueString(900, "int"));
		  el_dbselect($insertSQL1, 0, $res);
		  $new_id=el_dbselect("SELECT MAX(id) as a FROM forms", 0, $new_id, 'row');
		  echo "<script>alert('Теперь добавьте нужные именно для этой формы поля.');location.href='catalogs.php?new_id=".$new_id['a']."'</script>";
		}
}	
	  ?>
</p>
<form action="<?=$_SERVER['REQUEST_URI']?>&id" method="post" enctype="multipart/form-data" onSubmit="return fill(['name','tbl_name'],['Название формы','Имя таблицы данных'])">
  <table border="0" cellspacing="0" cellpadding="4" align="center" class="el_tbl">
    <caption>
    Новая форма
    </caption>
    <tr>
      <td>Имя формы</td>
      <td><input name="name" type="text" id="name" size="40" value="">
      </td>
    <tr>
      <td>Имя таблицы данных <font color="#FF0000" size="1">(по-английски)</font> <img src="/editor/img/help_button.gif" alt="Кликните для получения справки" width="12" height="12" style="cursor:pointer" onClick="test.TextPopup ('В этом поле указывается имя таблицы в базе данных английскими буквами. Например, automobiles или apartments',pszFont,10,10,-1,-1)"></td>
      <td><input name="tbl_name" type="text" id="tbl_name" size="40" value="">
      </td>
    </tr>
    <tr>
      <td>Заголовок над формой</td>
      <td><input name="title" type="text" id="title" size="40" value=""></td>
    </tr>
    <tr>
      <td>URL обработчика формы<br>
        <small>(по умолчанию - пустое значение, форма отправляет данные своему обработчику)</small></td>
      <td><input name="action" type="text" id="action" size="40" value=""></td>
    </tr>
    <tr>
      <td valign="top">Метод отправки </td>
      <td><label for="RG0">
        <input type="radio" name="method" value="POST" id="RG0" checked>
        POST</label>
        <br>
        <label for="RG1">
        <input type="radio" name="method" value="GET" id="RG1">
        GET</label>
      </td>
    </tr>
    <tr>
      <td valign="top">Отправлять </td>
      <td><label for="RG1_0">
        <input type="radio" name="target" value="_blank" id="RG1_0">
        В новое окно</label>
        <br>
        <label for="RG1_1">
        <input type="radio" name="target" value="_self" id="RG1_1" checked>
        В текущее окно</label>
      </td>
    </tr>
    <tr>
      <td>Привязать к каталогу</td>
      <td><select name="catalog_id" id="catalog_id">
          <?
	$catalogs=el_dbselect("SELECT name, catalog_id FROM catalogs", 0, $catalogs);
	$row_cats=el_dbfetch($catalogs);
	do{
		echo '<option value="'.$row_cats['catalog_id'].'"'.$sel.'>'.$row_cats['name'].'</option>'."\n";
	}while($row_cats=el_dbfetch($catalogs));
	?>
        <option value="adventor_new_form<?=($last_id['a']+1)?>">Создать новую</option>
        </select>
      </td>
    </tr>
    <tr>
      <td valign="top">Куда помещать данные</td>
      <td><p>
          <label for="RadioGroup1_0">
          <input type="radio" name="type" value="db" id="RadioGroup1_0">
          В базу данных</label>
          <br>
          <label for="RadioGroup1_1">
          <input type="radio" name="type" value="email" id="RadioGroup1_1">
          На e-mail</label>
          <br>
          <label for="RadioGroup1_2">
          <input name="type" type="radio" id="RadioGroup1_2" value="both" checked>
          В базу данных и на e-mail</label>
          <br>
        </p></td>
    </tr>
    <tr>
      <td>Использовать AJAX</td>
      <td><input type="checkbox" name="ajax" id="ajax" checked></td>
    </tr>
    <tr>
      <td>Защитить от автоматичекого заполнения</td>
      <td><input type="checkbox" name="protect" id="protect" checked></td>
    </tr>
  <tr>
    <td>Показать введенные данные перед отправкой для проверки</td>
    <td><input type="checkbox" name="prevalid" id="prevalid"<?=($rexist['prevalid']=='1')?' checked':''?>></td>
  </tr>
    <tr>
      <td>E-mail получателя <br>
        <small>(можно несколько через запятую)</small></td>
      <td><input name="email" type="text" id="email" size="40" value=""></td>
    </tr>
    <tr>
      <td valign="top">Формат письма</td>
      <td><p>
          <label for="type1_0">
          <input type="radio" name="email_type" value="HTML" id="type1_0">
          HTML</label>
          <br>
          <label for="type1_1">
          <input type="radio" name="email_type" value="TEXT" id="type1_1">
          Текст</label>
        </p></td>
    </tr>
    <tr>
      <td valign="top">Кодировка письма</td>
      <td>
      <select name="email_charset" id="email_charset">
            <option value="Windows-1251">Windows-1251</option>
            <option value="KOI-8R">KOI-8R</option>
        </select>
      </td>
    </tr>
    <tr>
      <td valign="top">Текст ответа после <br>
        отправки данных</td>
      <td><textarea name="answer" id="answer" cols="40" rows="5"></textarea></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><input name="formsUpdate" type="hidden" id="formsUpdate" value="new">
        <input type="submit" name="Submit" id="Submit" value="Сохранить" class="but"></td>
    </tr>
  </table>
</form>
<? //} 
} 
?>
</body>
<OBJECT
  classid="clsid:adb880a6-d8ff-11cf-9377-00aa003b7a11" type="application/x-oleobject" width="1" height="1" id=test></OBJECT>
</html>
