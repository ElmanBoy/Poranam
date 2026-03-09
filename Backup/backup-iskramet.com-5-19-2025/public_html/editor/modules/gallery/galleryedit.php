<?php 
require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php'); 
include $_SERVER['DOCUMENT_ROOT'].'/Connections/site_props.php';
$requiredUserLevel = array(1, 2); 
include($_SERVER['DOCUMENT_ROOT']."/editor/secure/secure.php");

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "update")) {
	$err=$bim=$sim=0;
	$errStr='';
	$_POST['swidth']=(strlen($_POST['swidth'])>0)?$_POST['swidth']:$site_property['gallerySWidth'.$cat];
	$_POST['sheight']=(strlen($_POST['sheight'])>0)?$_POST['sheight']:$site_property['gallerySHeight'.$cat];
	$_POST['bwidth']=(strlen($_POST['bwidth'])>0)?$_POST['bwidth']:$site_property['galleryBWidth'.$cat];
	$_POST['bheight']=(strlen($_POST['bheight'])>0)?$_POST['bheight']:$site_property['galleryBHeight'.$cat];
	$_FILES['file']['name']=(strlen($_FILES['file']['name'])>0)?$_FILES['file']['name']:$_FILES['big_file']['name'];
	$targetFileName=el_newName($_SERVER['DOCUMENT_ROOT'].'/images/gallery/', 'photo_'.el_translit($_FILES['big_file']['name']));
	$targetFileNameSmall=el_newName($_SERVER['DOCUMENT_ROOT'].'/images/small/', 'preview_'.el_translit($_FILES['file']['name']));

	if(strlen($_FILES['file']['name'])>0){
		if(el_resize_images($_FILES['file']['tmp_name'], $targetFileNameSmall, $_POST['swidth'], $_POST['sheight'], 'small/')){
			@chmod($_SERVER['DOCUMENT_ROOT'].'/images',0755);
			@chmod($_SERVER['DOCUMENT_ROOT'].'/images/small',0755);
			$_POST['smallpath']='/images/small/'.$targetFileNameSmall;
			$bsim=1;
		}else{
			$errStr.='Не удалось закачать маленькую картинку.\\nПроверьте права доступа у папки images/small';
			$err++;
		}
	}
	if(strlen($_FILES['big_file']['name'])>0){
		if(el_resize_images($_FILES['big_file']['tmp_name'], $targetFileName, $_POST['bwidth'], $_POST['bheight'], 'gallery/')){
			@chmod($_SERVER['DOCUMENT_ROOT'].'/images',0755);
			@chmod($_SERVER['DOCUMENT_ROOT'].'/images/small',0755);
			@chmod($_SERVER['DOCUMENT_ROOT'].'/images/gallery',0755);
			$_POST['path']='/images/gallery/'.$targetFileName;
			$bim=1;
		}else{
			$errStr.='Не удалось закачать большую картинку.\\nПроверьте права доступа у папки images/gallery';
			$err++;
		}
	}

	if($sim==0 && ($_POST['swidth']!=$site_property['gallerySWidth'.$cat] || $_POST['sheight']!=$site_property['gallerySHeight'.$cat])){
		$sImgPath=explode('/', $_POST['smallpath']);
		$sImgName=$sImgPath[count($sImgPath)-1];
		if(el_resize_images($_SERVER['DOCUMENT_ROOT'].$_POST['smallpath'], $_POST['swidth'].'x'.$_POST['sheight'].$sImgName, $_POST['swidth'], $_POST['sheight'], 'small/')){
			$_POST['smallpath']='/images/small/'.$_POST['swidth'].'x'.$_POST['sheight'].el_translit($sImgName);
		}
	}
	
	if($bim==0 && ($_POST['bwidth']!=$site_property['galleryBWidth'.$cat] || $_POST['bheight']!=$site_property['galleryBHeight'.$cat])){
		$bImgPath=explode('/', $_POST['path']);
		$bImgName=$bImgPath[count($bImgPath)-1];
		if(el_resize_images($_SERVER['DOCUMENT_ROOT'].$_POST['path'], $_POST['bwidth'].'x'.$_POST['bheight'].$bImgName, $_POST['bwidth'], $_POST['bheight'], 'gallery/')){
			$_POST['path']='/images/gallery/'.$_POST['bwidth'].'x'.$_POST['bheight'].el_translit($bImgName);
		}
	}
	
	if($err==0){
		$updateSQL = sprintf("UPDATE photo SET `path`=%s, text=%s, smallh=%s, smallw=%s, bigh=%s, bigw=%s, smallpath=%s, cover=%s, author=%s, raiting=%s, date_add=%s, sort=%s, in_comments=%s, in_rait=%s, caption=%s WHERE id=%s",
						   GetSQLValueString($_POST['path'], "text"),
						   GetSQLValueString(nl2br(addslashes($_POST['text'])), "text"),
						   GetSQLValueString($_POST['sheight'], "int"),
							GetSQLValueString($_POST['swidth'], "int"),
							GetSQLValueString($_POST['bheight'], "int"),
							GetSQLValueString($_POST['bwidth'], "int"),
						   GetSQLValueString($_POST['smallpath'], "text"),
						   GetSQLValueString(($_POST['cover']=='1')?1:0, "int"),
						   GetSQLValueString($_POST['author'], "text"),
						   GetSQLValueString($_POST['raiting'], "int"),
						   GetSQLValueString($_POST['date_add'], "date"),
						   GetSQLValueString($_POST['sort'], "int"),
						   GetSQLValueString(($_POST['in_comments']=='1')?1:0, "int"),
						   GetSQLValueString(($_POST['in_rait']=='1')?1:0, "int"),
						   GetSQLValueString($_POST['caption'], "int"),
						   GetSQLValueString($_POST['id'], "int"));
		
		;
		$Result1 = el_dbselect($updateSQL, 0, $Result1, 'result', true);
		if($_POST['cover']=='1'){
			el_dbselect("UPDATE photo_albums SET cover='".addslashes($_POST['smallpath'])."' WHERE cat='".intval($_POST['caption'])."'", 0, $res);
		}
		el_clearCache('catalogs');
		echo "<script language=javascript>alert('Изменения сохранены!')</script>";
	}else{
		echo '<script language=javascript>alert("'.$errStr.'")</script>';
	}
}


$colname_dbphotocat = "1";
if (isset($_GET['cat'])) {
  $colname_dbphotocat = (get_magic_quotes_gpc()) ? $_GET['cat'] : addslashes($_GET['cat']);
}
$colname_dbphoto = "1";
if (isset($_GET['id'])) {
  $colname_dbphoto = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
;
$query_dbphoto = sprintf("SELECT * FROM photo WHERE id = %s", $colname_dbphoto);
$dbphoto = el_dbselect($query_dbphoto, 0, $dbphoto, 'result', true);
$row_dbphoto = el_dbfetch($dbphoto);
$totalRows_dbphoto = mysqli_num_rows($dbphoto);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<title>Редактирование записи в фотогалерее</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1"> 
<script>
<? 
$bw=(strlen($row_dbphoto['bigw'])>0)?$row_dbphoto['bigw']:$site_property['galleryBWidth'.$_GET['cat']];
$bh=(strlen($row_dbphoto['bigh'])>0)?$row_dbphoto['bigh']:$site_property['galleryBHeight'.$_GET['cat']];
$sw=(strlen($row_dbphoto['smallw'])>0)?$row_dbphoto['smallw']:$site_property['gallerySWidth'.$_GET['cat']];
$sh=(strlen($row_dbphoto['smallh'])>0)?$row_dbphoto['smallh']:$site_property['gallerySHeight'.$_GET['cat']];
?>
var oldBW=<?=$bw?>;
var oldBH=<?=$bh?>;
var oldSW=<?=$sw?>;
var oldSH=<?=$sh?>;

function recalc(source, target){
	var t=document.getElementById(target);
	var s=document.getElementById(source);
	var prevVal;
	switch(source){
		case 'bwidth': prevVal=oldBW; break;
		case 'bheight': prevVal=oldBH; break;
		case 'swidth': prevVal=oldSW; break;
		case 'sheight':	prevVal=oldSH; break;
	}
	switch(target){
		case 'bwidth': prevValt=oldBW; break;
		case 'bheight': prevValt=oldBH; break;
		case 'swidth': prevValt=oldSW; break;
		case 'sheight':	prevValt=oldSH; break;
	}
	var change=(prevVal>=parseInt(s.value))?(parseInt(s.value) / prevVal):(prevVal / parseInt(s.value));
	t.value='';
	t.value= parseInt(prevValt / change); //alert(prevValt+" / "+change+" = "+(prevValt / change));
}

function checkNumber(obj){
	valid="1234567890";
	tmp="";
	for(i=0;(i<obj.value.length);i++)  {
		if (valid.indexOf(obj.value.charAt(i))!=-1)  {
			tmp=tmp+obj.value.charAt(i);};
		};  
	obj.value=tmp;	  
}

function init(){
	with(document.update){
 		swidth.value='<?=$sw?>';
		sheight.value='<?=$sh?>';
		bwidth.value='<?=$bw?>';
		bheight.value='<?=$bh?>';
	}
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
</script>
<link href="/editor/style.css" rel="stylesheet" type="text/css">
</head>

<body marginheight="0" marginwidth="0" onUnload="opener.location.href='/editor/editor.php?cat=<?=$_GET['cat']?>'; window.focus()" onLoad="init()">
<form action="<?php echo $editFormAction; ?>" method="POST" enctype="multipart/form-data" name="update" style="margin:0px">
<table width="100%"  border="0" cellspacing="0" cellpadding="4" class="el_tbl">
<tr>
  <td align="right" valign="top">Маленькая картинка:</td>
  <td>
  <img name="prew" id="prew" src="http://<?=$_SERVER['SERVER_NAME'].$row_dbphoto['smallpath']; ?>">
      <input name="smallpath" type="hidden" id="smallpath" value="<?php echo $row_dbphoto['smallpath']; ?>">
      <br>
      <input type="file" name="file" id="file">
  </td>
</tr>
<tr>
  <td align="right" valign="top">Большая картинка:</td>
  <td><iframe name="prew" id="prew" height="410" width="500" marginheight="1" marginwidth="1" scrolling="auto" src="http://<?=$_SERVER['SERVER_NAME'].$row_dbphoto['path']; ?>"></iframe>
      <input name="path" type="hidden" id="path" value="<?php echo $row_dbphoto['path']; ?>">
      <input name="id" type="hidden" id="id" value="<?php echo $row_dbphoto['id']; ?>">
      <br>
      <input type="file" name="big_file" id="big_file"></td>
</tr>
<tr valign="baseline">
      <td align="right" valign="top" nowrap bgcolor="#E0E0E0">Разместить в альбоме: </td>
      <td bgcolor="#E0E0E0">
      <?
	  $curr=el_dbselect("SELECT cat, title FROM content WHERE kod='gallery'", 0, $currCat);
	  $currCat=el_dbfetch($curr);
	  ?>
      <select name="caption">
      <? /*do{ 
	  $sel=($row_dbphoto['caption']==$currCat['cat'])?' selected':'';
	  ?>
      <option value="<?=$currCat['cat']?>"<?=$sel?>><?=$currCat['title']?></option>
      <?
	  }while($currCat=el_dbfetch($curr));*/
	  $album=el_dbselect("SELECT cat, name FROM photo_albums", 0, $album);
	  $albums=el_dbfetch($album);
	  do{
	  	$sel=($row_dbphoto['caption']==$albums['cat'])?' selected':'';
		echo '<option value="'.$albums['cat'].'"'.$sel.'>'.$albums['name'].'</option>'."\n";
	  }while($albums=el_dbfetch($album));
	  ?>
      </select>
      </td>
    </tr>
    <tr valign="baseline">
      <td align="right" valign="top" nowrap bgcolor="#E0E0E0">Назначить обложкой альбома: </td>
      <td bgcolor="#E0E0E0"><input type="checkbox" name="cover" id="cover" value="1"<?=($row_dbphoto['text']=='1')?' checked':''?>></td>
    </tr>
    <tr valign="baseline">
      <td align="right" nowrap bgcolor="#E0E0E0">Макс. размеры маленькой картинки : </td>
      <td valign="baseline" bgcolor="#E0E0E0"> ширина:
        <input name="swidth" type="text" id="swidth" value="" size="5" 
        onkeyup="checkNumber(this);" onkeypress="checkNumber(this);" onblur="checkNumber(this);" > 
        px,&nbsp;&nbsp;высота 
        <input name="sheight" type="text" id="sheight" value="" size="5" 
        onkeyup="checkNumber(this)" onkeypress="checkNumber(this)" onblur="checkNumber(this)" >
        px</td>
    </tr>
	<tr valign="baseline">
      <td align="right" nowrap bgcolor="#E0E0E0">Макс. размеры большой картинки : </td>
      <td valign="baseline" bgcolor="#E0E0E0"> ширина: 
        <input name="bwidth" type="text" id="bwidth" value="" size="5" onkeyup="checkNumber(this)" onkeypress="checkNumber(this)" onblur="checkNumber(this)"> 
        px,&nbsp;&nbsp;высота 
        <input name="bheight" type="text" id="bheight" value="" size="5" 
        onkeyup="checkNumber(this)" onkeypress="checkNumber(this)" onblur="checkNumber(this)"> 
        px</td>
    </tr>

<tr>
  <td align="right" valign="top">Текст под картинкой:</td>
  <td><textarea name="text" cols="50" rows="5" id="text"><?php echo stripslashes($row_dbphoto['text']); ?></textarea><br>
      <input type="button" onClick="MM_openBrWindow('/editor/newseditor.php?field=text&form=update','newcateditor','','785','625','true')" src="img/code.gif" value="HTML-редактор" class="but"></td>
</tr>

<tr valign="baseline">
      <td align="right" valign="top" nowrap bgcolor="#E0E0E0">Автор: </td>
      <td bgcolor="#E0E0E0"><input name="author" type="text" id="author" value="<?=$row_dbphoto['author']?>" size="50"></td>
    </tr>
    <tr valign="baseline">
      <td align="right" valign="top" nowrap bgcolor="#E0E0E0">Дата добавления: </td>
      <td bgcolor="#E0E0E0"><input name="date_add" type="text" id="date_add" value="<?=$row_dbphoto['date_add']?>" size="11"></td>
    </tr>
	<tr>
  <td align="right" valign="top">Порядковый номер: </td>
  <td><input name="sort" type="text" id="sort" value="<?=$row_dbphoto['sort']?>" size="11"></td>
</tr>
	<tr valign="baseline">
      <td align="right" valign="top" nowrap bgcolor="#E0E0E0">Рейтинг: </td>
      <td bgcolor="#E0E0E0"><input name="raiting" type="text" id="raiting" value="<?=$row_dbphoto['raiting']?>" size="11"></td>
    </tr>
	<tr valign="baseline">
      <td align="right" valign="top" nowrap bgcolor="#E0E0E0">Разрешить комментирование: </td>
      <td bgcolor="#E0E0E0"><input name="in_comments" type="checkbox" id="in_comments" value="1" <?=($row_dbphoto['in_comments']=='1')?'checked':''?>></td>
    </tr>
	<tr valign="baseline">
      <td align="right" valign="top" nowrap bgcolor="#E0E0E0">Разрешить рейтингование: </td>
      <td bgcolor="#E0E0E0"><input name="in_rait" type="checkbox" id="in_rait" value="1" <?=($row_dbphoto['in_rait']=='1')?'checked':''?>></td>
    </tr>
<tr>
  <td align="right"><br>
      <input type="submit" name="Submit" value="Сохранить" class="but"></td>
  <td align="center"><br>
      <input type="button" name="Button" value="Закрыть" onClick="window.close();" class="but"></td>
</tr>
<input type="hidden" name="MM_update" value="update">
</table>
</form>
</body>
</html>
