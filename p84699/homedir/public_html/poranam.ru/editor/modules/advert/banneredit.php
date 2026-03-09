<? require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php');

$requiredUserLevel = array(1, 2); 
include($_SERVER['DOCUMENT_ROOT']."/editor/secure/secure.php"); 
(isset($submit))?$work_mode="write":$work_mode="read";
el_reg_work($work_mode, $login, $_GET['cat']);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Изменение параметров баннера</title>
<link href="/editor/style.css" rel="stylesheet" type="text/css">
</head>
<script src="/js/jquery.js"></script>
<script language="javascript">
function doact(act, id){
	var err=0;
	if(act=='del'){
		var OK=confirm("Вы уверены, что хотите удалить баннер №"+id+" ?");
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
	var tp=$("#type1").val();
	if(tp=="image"){
		r1.show();
		r2.hide();
	}else if(tp=="html"){
		r1.hide();
		r2.show();
	}
}

</script>
<?


//Сохраняем изменения в площадке
if($_POST['action']=='save'){

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
	
	$place=implode(',', $_POST['places']);
	if(strlen($file_name)>0){
		$file_name='picture_'.$file_name;
		$subquery="`sizew`=".$sizew.", `sizeh`=".$sizeh.",";
	}else{
		$file_name=$_POST['fileold'];
		$subquery="";
	}
	$query="UPDATE ad_banners SET 
	`sort`=".GetSQLValueString($_POST['sort'], "text").",
	`name`=".GetSQLValueString($_POST['name'], "text").",
	`places`='".$place."',
	`file`='".$file_name."',
	`html`=".GetSQLValueString($_POST['html'], "text").",
	`text`=".GetSQLValueString($_POST['text'], "text").",
	`url`=".GetSQLValueString($_POST['url'], "text").",
	`target`='".addslashes($_POST['target'])."',
	`type`=".GetSQLValueString($_POST['type'], "text").",
	".$subquery."
	`alt`=".GetSQLValueString($_POST['alt'], "text").", 
	`active`='".$_POST['active']."' WHERE id='".$_GET['id']."'";
	if(el_dbselect($query, 0, $res, 'result', true)!=FALSE){
		$mess="\\nИзменения сохранены!";
		el_updateDBanner();
	}else{
		$mess.="\\nНе удалось сохранить изменения!\\nВозможно, введены неверные данные.";
	}
}
//Выводим сообщения об ошибках
if($mess!=""){echo "<script>alert('$mess')</script>";}

//Выводим список площадок
$ad=el_dbselect("SELECT * FROM ad_banners WHERE id='".$_GET['id']."'", 0, $ad);
$adv=el_dbfetch($ad);
?>
<body>
<h5 align="center">Изменение баннера &laquo;<?=$adv['name']?>&raquo;</h5>
<table border="0" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
<form method="post" name="new_banner" enctype="multipart/form-data">
  <tr>
    <td align="right">Название:</td>
    <td><input name="name" type="text" id="name" size="40" value="<?=$adv['name']?>"></td>
  </tr>
  <tr>
    <td align="right">Номер:</td>
    <td><input name="sort" type="text" id="sort" size="10" value="<?=$adv['sort']?>"></td>
  </tr>
  <tr>
    <td align="right" valign="top">Показывать на площадках:</td>
    <td>
	<select name="places[]" id="places" multiple="multiple">
	<?
	$p=el_dbselect("SELECT * FROM ad_places", 0, $p);
	$pl=el_dbfetch($p);
	do{
		$placArr=explode(',', $adv['places']);
		(in_array($pl['id'], $placArr))?$sel=' selected':$sel='';
		echo "<option value='".$pl['id']."'$sel>".$pl['name']."</option>";
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
	<option value="image"<?=($adv['type']=='image')?' selected':''?>>Баннер (jpg, gif)</option>
    <option value="flash"<?=($adv['type']=='flash')?' selected':''?>>Flash-баннер (swf)</option>
	<option value="html"<?=($adv['type']=='html')?' selected':''?>>HTML-текст</option>
	</select>
	</td>
  </tr>
  <tr>
    <td align="right">Открывать в:</td>
    <td>
	<select name="target" id="target">
	<option></option>
	<option value="_blank"<?=($adv['target']=='_blank' || $adv['target']=='')?' selected':''?>>Новом окне</option>
    <option value="_self"<?=($adv['target']=='_self')?' selected':''?>>Текущем окне</option>
	</select>
	</td>
  </tr>
    <tr id="flwh" style="display:<?=($adv['type']=='flash')?'table-row':'none'?>">
    <td align="right">Размеры (px):</td>
    <td>ширина <input name="flash_w" type="text" id="flash_w" size="10" value="<?=$adv['sizew']?>"> px, 
    высота <input name="flash_h" type="text" id="flash_h" size="10" value="<?=$adv['sizeh']?>"> px
    </td>
  </tr>
  <tr id="row1" style="display:<?=($adv['type']=='html')?'none':'table-row'?>">
    <td align="right">Закачать:</td>
    <td>
	<? 
	if(strlen($adv['file'])>0){ 
		if($adv['type']=='image'){
			echo '<img src="http://'.$_SERVER['SERVER_NAME'].'/images/pictures/'.$adv['file'].'" 
			width='.$adv['sizew'].' height='.$adv['sizeh'].' alt='.$adv['alt'].'>';
		}elseif($adv['type']=='flash'){
			echo '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" 
			codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" 
			width="'.$adv['sizew'].'" height="'.$adv['sizeh'].'" title="'.$adv['alt'].'">
			<param name="movie" value="/images/pictures/'.$adv['file'].'" />
			<param name="quality" value="high" />
			<embed src="/images/pictures/'.$adv['file'].'" quality="high"
		   pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" 
		   type="application/x-shockwave-flash" width="'.$adv['sizew'].'" height="'.$adv['sizeh'].'"></embed>
			</object>';
		}
	}else{
		echo $adv['html'];
	}
		?><br>
	<input name="file" type="file" id="file">
	<input type="hidden" name="fileold" value="<?=$adv['file']?>">
	</td>
  </tr>
  <tr id="row2" style="display:<?=($adv['type']=='html')?'table-row':'none'?>">
    <td align="right" valign="top">HTML-текст:</td>
    <td><textarea name="html" cols="40" rows="10"><?=$adv['html']?></textarea><br>      
      			<input name='Button' type='button' onClick="MM_openBrWindow('/editor/newseditor.php?form=new_banner&field=html','editor_ext','resizable=yes','750','640','true','description','new')" value='Визуальный редактор' class='but'><br><br> </td>
  </tr>
  <tr>
    <td align="right" valign="top">Текст под баннером:</td>
    <td>
	<textarea name="text" id="text" cols="40" rows="5"><?=$adv['text']?></textarea>
	<br>      
    <input name='Button' type='button' onClick="MM_openBrWindow('/editor/newseditor.php?form=new_banner&field=text','editor_ext','resizable=yes','750','640','true','description','new')" value='Визуальный редактор' class='but'><br><br> 
	</td>
  </tr>
  <tr>
    <td align="right" valign="top">URL:</td>
    <td>
	<input name="url" type="text" id="url" size="40" value="<?=$adv['url']?>">
	
	</td>
  </tr>
    <tr>
    <td align="right">Текст всплывающей подсказки:</td>
    <td><input name="alt" type="text" id="alt" size="40" value="<?=htmlspecialchars(stripslashes($adv['alt']))?>"></td>
  </tr>

  <tr>
    <td align="right">Включен:</td>
    <td><input name="active" type="checkbox" id="active" value="1"<?=($adv['active']==1)?' checked':''?>>
	<input type="hidden" name="action" value="save">
	</td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" name="Submit" value="Сохранить" class="but">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" onClick="window.close()" value="Закрыть" class="but"></td>
  </tr>
  </form>
</table>

</body>
</html>
