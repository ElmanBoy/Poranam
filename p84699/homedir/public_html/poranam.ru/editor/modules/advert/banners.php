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

//Сохарняем новый баннер
if($_POST['action']=='new'){
	
	//Определяем наименьшие максимальные размеры для баннера из установок площадки
	$mW=array();
	$mH=array();
	if(count($_POST['places'])>1){
		for($i=0; $i<count($place); $i++){
			$pl=el_dbselect("SELECT * FROM ad_places WHERE id='".$_POST['places'][$i]."'", 0, $pl, 'row');
			array_push($mW, $pl['maxW']);
			array_push($mH, $pl['maxH']);
		}
		$maxW=min($mW); 
		$maxH=min($mH);
	}else{
		$pl=el_dbselect("SELECT * FROM ad_places WHERE id='".$_POST['places'][0]."'", 0, $pl, 'row');
		$maxW=$pl['maxW']; 
		$maxH=$pl['maxH'];
	} 
	//Закачиваем баннеры
	if(strlen($_FILES['file']['name'])>0){
		$file=$_FILES['file']['tmp_name'];
		$file_name=$_FILES['file']['name'];
		$uploaddir=$_SERVER['DOCUMENT_ROOT']."/images/pictures/";
		if(!is_dir($uploaddir)){mkdir($uploaddir, 0777);}
		//Определяем, является ли баннер флэш
		preg_match("'^(.*)\.(swf)$'i", $file_name, $ext);
		$ext[2]=strtolower($ext[2]);
		if($ext[2]=='swf'){
			if(!move_uploaded_file($file, $uploaddir.'picture_'.$file_name)){
				$mess.="\\nНе удалось закачать файл \"$file_name\"!";
			}else{
				if(strlen($_POST['flash_w'])>0 && strlen($_POST['flash_h'])>0){
					$sizew=$_POST['flash_w'];
					$sizeh=$_POST['flash_h'];
				}else{
					$mess.="\\nНе указаны размеры баннера!";
				}
			}
		}else{
			if(getimagesize($file)){
				if(el_resize_images($file, $file_name, $maxW, $maxH, 'pictures/picture_')==true){
					@chmod($uploaddir.'picture_'.$file_name, 0777);
					$mess.="\\nФайл \"".$file_name."\" успешно закачан!";
					$sizeim=getimagesize($uploaddir.'picture_'.$file_name);
					$sizew=$sizeim[0];
					$sizeh=$sizeim[1];
				}else{
					$mess.="\\nНе удалось закачать файл \"$file_name\"!";
				}
			}else{
				$mess.="\\nЭтот файл не является изображением!";
			}
		}
	}
	$place=@implode(',', $_POST['places']);
	$query="INSERT INTO ad_banners (`sort`, `name`,`places`,`file`,`html`,`text`,`url`,`target`,`count`,`type`,`sizew`,`sizeh`,`alt`,`active`) VALUES (
	".GetSQLValueString($_POST['sort'], "text").", 
	".GetSQLValueString($_POST['name'], "text").", 
	'".$place."', 
	'picture_".$file_name."', 
	".GetSQLValueString($_POST['html'], "text").", 
	".GetSQLValueString($_POST['text'], "text").",
	".GetSQLValueString($_POST['url'], "text").",
	".GetSQLValueString($_POST['target'], "text").",
	0, 
	".GetSQLValueString($_POST['type'], "text").", 
	'".$sizew."', 
	'".$sizeh."', 
	".GetSQLValueString($_POST['alt'], "text").",	
	'".$_POST['active']."')";
	if(el_dbselect($query, 0, $res, 'result', true)!=FALSE){
		$mess.="\\nБаннер добавлен!";
		el_updateDBanner();
	}else{
		$mess.="\\nБаннер не удалось добавить в базу данных!\\nВозможно, введены неверные данные.";
	}
}

//Удаляем баннер
if($_POST['action']=='del'){
	if(el_dbselect("DELETE FROM ad_banners WHERE id='".$_POST['id']."'", 0, $res)!=FALSE){
		unlink($_SERVER['DOCUMENT_ROOT'].'/images/pictures/'.$_POST['del_file']);
		echo '<script language=javascript>alert("Баннер №'.$_POST['id'].' удален!")</script>';
	}
}

//Сохраняем изменения в баннере
if($_POST['action']=='save'){
	(count($_POST['places'])>1)?$place=implode(',', $_POST['places']):$place=$_POST['places'];
	(strlen($file_name)>0)?$file_name=$file_name:$file_name=$_POST['file'];
	if(el_dbselect("UPDATE ad_banners SET 
	`sort`=".GetSQLValueString($_POST['sort'])."',
	`name`=".GetSQLValueString($_POST['name'])."',
	`places`='".$place."',
	`file`='".$file_name."',
	`html`=".GetSQLValueString($_POST['html'])."',
	`text`=".GetSQLValueString($_POST['text'])."',
	`url`=".GetSQLValueString($_POST['url'])."',
	`target`=".GetSQLValueString($_POST['target'])."',
	`type`=".GetSQLValueString($_POST['type'])."',
	`sizew`='".$sizew."',
	`sizeh`='".$sizeh."',
	`alt`=".GetSQLValueString($_POST['alt'])."', 
	`active`='".$_POST['active']."' WHERE id='".$_POST['id']."'", 0, $res)!=FALSE){
		echo '<script language=javascript>alert("Изменения сохранены!")</script>';
		el_updateDBanner();
	}
}
//Выводим сообщения об ошибках
if($mess!=""){echo "<script>alert('$mess')</script>";}

//Выводим список баннеров
$ad=el_dbselect("SELECT * FROM ad_banners ORDER BY `sort`", 0, $ad);
$adv=el_dbfetch($ad);

//Функция подсчета CTR
function el_getCTR($hits, $clicks){
	if($hits>0 && $clicks>0){
		$prop=round($hits/$clicks, 2);
		$ctr=round(100/$prop, 2);
		return $ctr;
	}else{
		return 0;
	}
}
?>
<script src="/js/jquery.js"></script>
<script language="javascript">
function doact(act, id, file){
	var err=0;
	if(act=='del'){
		var OK=confirm("Вы уверены, что хотите удалить баннер "+file+" ?");
		if(OK){
			err=0;
		}else{
			err=1;
		}
	}
	if(err==0){
		document.act.action.value=act;
		document.act.id.value=id;
		document.act.del_file.value=file;
		document.act.submit();
	}
}

function MM_findObj(n, d) { 
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_openBrWindow(theURL,winName,features, myWidth, myHeight, isCenter) { //v3.0
  if(window.screen)if(isCenter)if(isCenter=="true"){
    var myLeft = (screen.width-myWidth)/2;
    var myTop = (screen.height-myHeight)/2;
    features+=(features!='')?',':'';
    features+=',left='+myLeft+',top='+myTop;
  }
  window.open(theURL,winName,features+((features!='')?',':'')+'width='+myWidth+',height='+myHeight);
}

function check_type(){
	var r1=$("#row1");
	var r2=$("#row2");
	var r3=$("#row3");
	var r4=$("#flwh");
	var tp=$("#type1").val();
	if(tp=="image"){
		r1.show();
		r2.hide();
		r4.hide();
	}else if(tp=="html"){
		r1.hide();
		r2.show();
		r4.hide();
	}else{
		r1.show();
		r2.hide();
		//r3.hide();
		r4.show();
	}
}

</script>
<body>
<form name="act" method="post"><input name="action" type="hidden"><input type="hidden" name="id"><input type="hidden" name="del_file"></form>
<h4 align="center">Упраление баннерами</h4>
<? 
if(mysqli_num_rows($ad)>0){
	echo '<h5 align=center>Список баннеров.</h5>';
	do{ ?>
			<div class='row'>
			<div id="left">№<?=$adv['id']?> &laquo;<?=$adv['name']?>&raquo;  
			[<?=$adv['view']?> показ<?=el_postfix($adv['view'], '', 'а', 'ов')?>, <?=$adv['count']?> переход<?=el_postfix($adv['count'], '', 'а', 'ов')?>, CTR <?=el_getCTR($adv['view'], $adv['count'])?>%, 
			<?=($adv['active']==1)?'<font color=green>включен</font>':'<font color=red>отключен</font>'?>]</div>
			<div id='right'>
			<img border="0" title="Редактировть" src="/editor/img/menu_edit.gif"  onClick="MM_openBrWindow('/editor/modules/advert/banneredit.php?id=<?=$adv['id']?>','editor','resizable=yes, scrollbars=yes','650','550','true','description','new')">&nbsp;
			<img border="0" title="Удалить" src="/editor/img/menu_delete.gif" onClick="doact('del', <?=$adv['id']?>, '<?=$adv['file']?>')">
			</div>
			</div>
	<? 
	}while($adv=el_dbfetch($ad));
}else{
	echo '<h4 align=center style="color:red">Пока нет ни одного баннера.</h4>';
}?>
<h5 align="center">Добавить новый баннер</h5>
<table border="0" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
<form method="post" name="new_banner" enctype="multipart/form-data">
  <tr>
    <td align="right">Название:</td>
    <td><input name="name" type="text" id="name" size="40"></td>
  </tr>
  <? $last=el_dbselect("SELECT MAX(`sort`) as w FROM ad_banners", 0, $last, 'row'); ?>
  <tr>
    <td align="right">Номер:</td>
    <td><input name="sort" type="text" id="sort" size="10" value="<?=$last['w']+1?>"></td>
  </tr>
  <tr>
    <td align="right" valign="top">Показывать на площадках:</td>
    <td>
	<select name="places[]" id="places" multiple="multiple">
	<?
	$p=el_dbselect("SELECT * FROM ad_places", 0, $p);
	$pl=el_dbfetch($p);
	do{
		echo "<option value='".$pl['id']."'>".$pl['name']."</option>";
	}while($pl=el_dbfetch($p));
	?>
	</select>
	</td>
  </tr>
  <tr>
    <td align="right">Тип баннера:</td>
    <td>
	<select name="type" id="type1" onChange="check_type()">
	<option></option>
	<option value="image">Баннер (jpg, gif)</option>
    <option value="flash">Flash-баннер (swf)</option>
	<option value="html">HTML-текст</option>
	</select>
	</td>
  </tr>
  <tr>
    <td align="right">Открывать в:</td>
    <td>
	<select name="target" id="target">
	<option></option>
	<option value="_blank"<?=($adv['type']=='_blank')?' selected':''?>>Новом окне</option>
    <option value="_self"<?=($adv['type']=='_self')?' selected':''?>>Текущем окне</option>
	</select>
	</td>
  </tr>
  <tr id="flwh" style="display:none">
    <td align="right">Размеры (px):</td>
    <td>ширина <input name="flash_w" type="text" id="flash_w" size="10"> px, 
    высота <input name="flash_h" type="text" id="flash_h" size="10"> px
    </td>
  </tr>
  <tr id="row1" style="display:none">
    <td align="right">Закачать:</td>
    <td><input name="file" type="file" id="file"></td>
  </tr>
  <tr id="row2" style="display:none">
    <td align="right" valign="top">HTML-текст:</td>
    <td><textarea name="html" cols="40" rows="10"></textarea><br>      
      			<input name='Button' type='button' onClick="MM_openBrWindow('/editor/newseditor.php?form=new_banner&field=html','editor','resizable=yes','750','640','true','description','new')" value='Визуальный редактор' class='but'><br><br> </td>
  </tr>
  <tr>
    <td align="right" valign="top">Текст под баннером:</td>
    <td>
	<textarea name="text" id="text" cols="40" rows="5"></textarea>
	<br>      
    <input name='Button' type='button' onClick="MM_openBrWindow('/editor/newseditor.php?form=new_banner&field=text','editor','resizable=yes','750','640','true','description','new')" value='Визуальный редактор' class='but'><br><br> 
	</td>
  </tr>
  <tr id="row3">
    <td align="right" valign="top">URL:</td>
    <td>
	<input name="url" type="text" id="url" size="40">
	
	</td>
  </tr>
    <tr>
    <td align="right">Текст всплывающей подсказки:</td>
    <td><input name="alt" type="text" id="alt" size="40"></td>
  </tr>

  <tr>
    <td align="right">Включен:</td>
    <td><input name="active" type="checkbox" id="active" value="1" checked="checked">
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
