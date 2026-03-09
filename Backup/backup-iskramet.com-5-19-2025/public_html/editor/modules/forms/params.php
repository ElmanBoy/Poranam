<? require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php');


$requiredUserLevel = array(1, 2); 
include($_SERVER['DOCUMENT_ROOT']."/editor/secure/secure.php"); 
(isset($submit))?$work_mode="write":$work_mode="read";
el_reg_work($work_mode, $login, $_GET['cat']);

 ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Управление каталогами</title>
<link href="/editor/style.css" rel="stylesheet" type="text/css">
<script src="/editor/colors.js" language="JavaScript"></script>
<script language="javascript">
<!--
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

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
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

function savechange(){
document.frm1.cat_mode_edit1.value='save';
//document.frm1.submit();
}

//-->
</script>
<style type="text/css">
.over{background-color:#CCDCE6}
.out{background-color:#003399}
.style1 {color: #FF0000; font-size:10px}
</style>
</head>
<body>
<form method="post" name="act"><input type="hidden" name="action"><input type="hidden" name="id"></form>
<center>
<p>
 <b>Список полей</b>
| <a href="catalogs.php?mode=new<?=(!empty($_GET['cat']))?'&cat='.$_GET['cat']:''?>">Создать форму</a>
| <a href="catalogs.php?mode=list<?=(!empty($_GET['cat']))?'&cat='.$_GET['cat']:''?>">Список форм</a>
<?
if(!empty($_GET['cat'])){?>
|
<a href="'/editor/editor.php?cat=<?=$_GET['cat']?>"> &laquo; Вернуться к редактированию раздела</a>
<? } if(!empty($_GET['id'])){?>
|
<a href="/editor/modules/forms/edit_form.php?cat=<?=$_GET['cat']?>&id=<?=$_GET['id']?>">Редактировать настройки формы &raquo;</a>
<? }?>
</p>
</center>
<h4 align="center">Список полей для форм</h4>
<? error_reporting(E_ALL & ~E_NOTICE);
//Наполняем каталог полями
  
  if(isset($_GET['id']) && strlen($_GET['id'])>0){$id_cat=$_GET['id'];}else{$id_cat=$_GET['new_id'];}
  
	$cats=el_dbselect("SELECT * FROM forms", 0, $res);
	$row_cats = el_dbfetch($cats); 

	if($_POST['cat_mode_edit1']=="del"){
		$deleteSQL = sprintf("DELETE FROM form_options WHERE id=%s",
						   GetSQLValueString($_POST['field_id'], "int"));
		
		$Result1=el_dbselect($deleteSQL, 0, $res);
		echo "<script>alert('Поле удалено!')</script>";
	}
	
	if($_POST['cat_mode_edit1']=="save"){
		
		$catsave=el_dbselect("SELECT * FROM form_options", 0, $res);
		$row_catsave = el_dbfetch($catsave); 
		do{
	  		$updateSQL = sprintf("UPDATE form_options SET name=%s, sort=%s WHERE id=%s",
                       GetSQLValueString($_POST['name'.$row_catsave['id']], "text"),
					   GetSQLValueString($_POST['sort'.$row_catsave['id']], "int"),
                       GetSQLValueString($row_catsave['id'], "int"));
 			
  			$Result1=el_dbselect($updateSQL, 0, $res);
		}while($row_catsave = el_dbfetch($catsave));
		echo "<script>alert('Изменения сохранены!')</script>";
	}

	if(isset($_POST['new_element']) && $_POST['new_element']=="1"){
		$insertSQL = sprintf("INSERT INTO form_options (name, type, size, cols, rows, sort, options, listdb, from_field) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['type'], "text"),
                       GetSQLValueString($_POST['size'], "int"),
					   GetSQLValueString($_POST['cols'], "int"),
					   GetSQLValueString($_POST['rows'], "int"),
					   GetSQLValueString($_POST['sort'], "int"),
					   GetSQLValueString($_POST['options'], "text"),
					   GetSQLValueString($_POST['listdb'], "text"),
					   GetSQLValueString($_POST['from_field'], "int"));

 	$Result1=el_dbselect($insertSQL, 0, $Result1); if($Result1)echo '!!!';
}

$cat2=el_dbselect("SELECT MAX(sort) FROM form_options", 0, $res);
if($cat2)$row_cat2 = el_dbfetch($cat2);

$cat=el_dbselect("SELECT * FROM form_options ORDER BY sort", 0, $cat);
if($cat)$row_cat = el_dbfetch($cat);

if(mysqli_num_rows($cat)>0){
?><form method="post" name="frm1"><input name="cat_mode_edit1" type="hidden" id="cat_mode_edit1"><input name="field_id" type="hidden" id="field_id"><div align="right"><input type="submit" value="Сохранить изменения" onClick="savechange()" class="but"></div>
<table border="0" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
  <tr>
    <td align="center" style="background-color:#b1c5d2">Название</td>
    <td align="center" style="background-color:#b1c5d2">Номер <img src="/editor/img/help_button.gif" alt="Кликните для получения справки" width="12" height="12" style="cursor:pointer" onClick="test.TextPopup ('Номер необходим, что бы указать в какой последовательностидолжны выводится поля',pszFont,10,10,-1,-1)"></td>
    <td colspan="2" align="center" style="background-color:#b1c5d2">Действие</td>
  </tr>
  <? do{ ?>
  <tr onMouseOver="line_over('<?=$row_cat['id']?>')" onMouseOut="line_out('<?=$row_cat['id']?>')">
    <td valign="top" id="string<?=$row_cat['id']?>">
        <img src="/editor/img/spacer.gif" name="img<?=$row_cat['id']?>" width="19" height="17" align="absmiddle" id="img<?=$row_cat['id']?>">
      <input name="name<?=$row_cat['id']?>" type="text" id="name<?=$row_cat['id']?>" value="<?=$row_cat['name']?>" size="30">
      </td>
    <td align="right" valign="top"><input name="sort<?=$row_cat['id']?>" type="text" id="sort<?=$row_cat['id']?>" onBlur="MM_validateForm('sort<?=$row_cat['id']?>','','NisNum');return document.MM_returnValue" value="<?=$row_cat['sort']?>" size="3"></td>
    <td align="center"><img name="submit_edit" type="image" id="submit_edit" src="/editor/img/menu_edit.gif" alt="Редактировать" onClick="MM_openBrWindow('catalogs.php?mode=editfield&field=<?=$row_cat['id']?>','edit','scrollbars=no,resizable=yes,width=550,height=310')" style="cursor:pointer">
      </td>
    <td align="center"><input name="submit_del2" type="image" id="submit_del2" src="/editor/img/menu_delete.gif" alt="Удалить" onClick="cat_mode_edit1.value='del'; field_id.value='<?=$row_cat['id']?>'; return check_el('<?=$row_cat['name']?>')"></td>
  </tr> 
  <? }while($row_cat = el_dbfetch($cat)); ?>
</table>
<div align="right"><input type="submit" value="Сохранить изменения" onClick="savechange()" class="but"></div>
</form>
<p>&nbsp;</p>
<p> 
  <? }?><br>
<table width="500" border="0" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
  <caption>
    Новое поле
  </caption>
  <form method="post" action="<?=$_SERVER['REQUEST_URI']?>" name="newform">
    <tr>
      <td width="42%" align="right">Название <img src="/editor/img/help_button.gif" alt="Кликните для получения справки" width="12" height="12" style="cursor:pointer" onClick="test.TextPopup ('В этом поле указывается название поля для отображения в каталоге. Например, Имя или Высота',pszFont,10,10,-1,-1)"></td>
      <td width="58%"><input name="name" type="text" id="name" size="30">
        <input name="cat_action" type="hidden" id="cat_action" value="step2">
        <input name="field" type="hidden" id="field" value="<?=($row_cat2['MAX(field)']+1)?>"></td>
    </tr>
    <tr>
      <td align="right">Тип <img src="/editor/img/help_button.gif" alt="Кликните для получения справки" width="12" height="12" style="cursor:pointer" onClick="test.TextPopup ('Выберите тип будущего поля. Описание типов приведено в документации.',pszFont,10,10,-1,-1)"></td>
      <td>
      <select name="type" id="type" onChange="select_size()">
        <?
		include $_SERVER['DOCUMENT_ROOT'].'/editor/modules/catalog/props_array.php';
		while(list($key, $val)= each($props_array)){
			echo '<option value="'.$key.'">'.$val.'</option>'."\n";
		}
		?>
      </select>
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
	<nobr>Каталог: <select name="listdb" id="listdb" onChange="field_frame.location.href('catalogs.php?catalog_id='+listdb.options[listdb.selectedIndex].value+'&viewfield'); field_div.style.display='block'">
	<option></option>
	<? do{?>
		<option value="<?=$row_list_db['catalog_id']?>"><?=$row_list_db['name']?></option>
	<? }while($row_list_db=el_dbfetch($list_db)); ?>
	</select><input type="hidden" id="from_field" name="from_field"></nobr><br><br>
<div style="display:none" id="field_div">
<iframe frameborder="0" width="300" height="30" scrolling="no" id="field_frame" name="field_frame" hspace="0" marginheight="0" marginwidth="0" vspace="0"></iframe>
</div>
</div></td>
    </tr>
    <tr>
      <td align="right">Размер <img src="/editor/img/help_button.gif" alt="Кликните для получения справки" width="12" height="12" style="cursor:pointer" onClick="test.TextPopup ('Задайте размер будущего поля. Этот параметр важен только для административной части.',pszFont,10,10,-1,-1)"></td>
      <td><div id="sizefield" style="display:block"><input name="size" type="text" id="size" size="3"></div>
<div id="area" style="display:none"> 
	<nobr>Ширина: <input name="cols" type="text" id="cols" value="40"  size="3"></nobr><br>
	<nobr>Высота: <input name="rows" type="text" id="rows" value="5" size="3"></nodr>
</div></td>
    </tr>
    <tr>
      <td align="right">Номер <img src="/editor/img/help_button.gif" alt="Кликните для получения справки" width="12" height="12" style="cursor:pointer" onClick="test.TextPopup ('Задайте порядковый номер будущего поля. Этот номер определяет положение поля в описании товара или услуги.',pszFont,10,10,-1,-1)"></td>
      <td><input name="sort" type="text" id="sort" value="<?=$row_cat2['MAX(sort)']+100?>" size="3"></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><input type="submit" name="Submit3" value="Добавить" class="but">
      <input name="new_element" type="hidden" id="new_element" value="1"></td>
    </tr>
  </form>
</table> 