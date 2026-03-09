<?
require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php'); 
include $_SERVER['DOCUMENT_ROOT'].'/Connections/site_props.php';
$requiredUserLevel = array(1, 2);
include($_SERVER['DOCUMENT_ROOT']."/editor/secure/secure.php"); 
$_GET['cat']=intval($_GET['cat']);

function changePath($idcat, $level, $new_dirname){
	$exCat=el_dbselect("SELECT id, path FROM cat WHERE parent='".$idcat."'", 0, $exCat);
	if(mysqli_num_rows($exCat)>0){
		$rex=el_dbfetch($exCat);
		do{
			$dirArr=array();
			$new_path='';
			$dirArr=explode('/', $rex['path']);
			$dirArr[$level]=str_replace('/', '', $new_dirname);
			$new_path=implode('/', $dirArr);
			$ch=el_dbselect("SELECT id FROM cat WHERE parent='".$rex['id']."'", 0, $ch);
			if(mysqli_num_rows($ch)>0){
				changePath($rex['id'], $level, $new_dirname);
			} 
			el_dbselect("UPDATE cat SET path='".$new_path."' WHERE id='".$rex['id']."'", 0, $pa);
			el_dbselect("UPDATE content SET path='".$new_path."' WHERE cat='".$rex['id']."'", 0, $pa);
		}while($rex=el_dbfetch($exCat));
	}
}

if(isset($_POST['Submit'])){
	$err=0;
	$old_path=explode('/', $_POST['oldPath']);
	array_splice($old_path, -1, 1, $_POST['path']); 
	$new_dirname=implode('/', $old_path);
	($_POST['path']=='/')?$_POST['path']='':$_POST['path']=$_POST['path'];
	($new_dirname=='/')?$new_dirname='':$new_dirname=$new_dirname;
	
	if($_POST['oldPath']!=$new_dirname){
		if(!rename($_SERVER['DOCUMENT_ROOT'].$_POST['oldPath'], $_SERVER['DOCUMENT_ROOT'].$new_dirname)){
			echo "<script>alert('Не удается переименовать папку.')</script>";
			$err++;
		}else{
			changePath($_GET['cat'], 0, $_POST['path']);
		}
	}
	$AlbumW=(strlen($site_property['galleryAlbumSWidth'.$cat])>0)?$site_property['galleryAlbumSWidth'.$cat]:$site_property['gallerySWidth'.$cat];
	$AlbumH=(strlen($site_property['galleryAlbumSHeight'.$cat])>0)?$site_property['galleryAlbumSHeight'.$cat]:$site_property['gallerySHeight'.$cat];
	if(strlen($AlbumW)==0){$AlbumW=200;}
	if(strlen($AlbumH)==0){$AlbumH=200;} 
	if(strlen($_FILES['cover']['name'])>0){
		$targetFileName=el_newName($_SERVER['DOCUMENT_ROOT'].'/images/small/', 'cover_'.el_translit($_FILES['cover']['name']));
		if(el_resize_images($_FILES['cover']['tmp_name'], $targetFileName, $AlbumW, $AlbumH, 'small/')){
			@chmod($_SERVER['DOCUMENT_ROOT'].'/images',0755);
			@chmod($_SERVER['DOCUMENT_ROOT'].'/images/small',0755);
			$coverPath='/images/small/'.$targetFileName;
		}else{
			$errStr.='Не удалось закачать обложку.\\nПроверьте права доступа у папки images/small\\n';
			$err++;
		}
		$new_cover=($coverPath!=$_POST['oldCover'])?$coverPath:$_POST['oldCover']; 
	}else{
		$new_cover=$_POST['oldCover'];
	}
	if($err==0){
	  $updateSQL = sprintf("UPDATE content SET path=%s, template=%s WHERE cat=%s",
						   GetSQLValueString($new_dirname, "text"),
						   GetSQLValueString($_POST['template'], "text"),
						   GetSQLValueString($_GET['cat'], "int"));
	
	  $Result1=el_dbselect($updateSQL, 0, $Result1);
	  if($_POST['menu']!="N"){$_POST['menu']="Y";}else{$_POST['menu']="N";}
	  $updateSQL1 = sprintf("UPDATE cat SET path=%s, name=%s, sort=%s, menu=%s, ptext=%s WHERE id=%s",
						   GetSQLValueString($new_dirname, "text"),
						   GetSQLValueString($_POST['name'], "text"),
						   GetSQLValueString($_POST['sort'], "int"),
						   GetSQLValueString($_POST['menu'], "text"),
						   GetSQLValueString($_POST['ptext'], "text"),
						   GetSQLValueString($_GET['cat'], "int"));
	
	  $Result2=el_dbselect($updateSQL1, 0, $Result2);
	  
	  $updateSQL1 = sprintf("UPDATE photo_albums SET name=%s, date_create=%s, cover=%s, sort=%s, active=%s WHERE cat=%s",
						   GetSQLValueString(str_replace('``','"',str_replace('`',"'",$_POST['name'])), "text"),
						   GetSQLValueString($_POST['date_create'], "text"),
						   GetSQLValueString($new_cover, "text"),
						   GetSQLValueString($_POST['sort'], "int"),
						   GetSQLValueString(isset($_POST['active'])?"true":"", "defined","'1'","'0'"),
						   GetSQLValueString($_GET['cat'], "int"));
	
	  $Result2=el_dbselect($updateSQL1, 0, $Result2);
	  el_2ini('galleryAlbumSWidth'.$cat, $AlbumW);
	  el_2ini('galleryAlbumSHeight'.$cat, $AlbumH);
	  el_clearCache('catalogs');
	  echo '<script language=javascript>alert("Изменения сохранены!")</script>';
  }
}

$edAlbum=el_dbselect("SELECT * FROM photo_albums WHERE cat='".$_GET['cat']."'", 0, $edAlbum, 'row');
$edCat=el_dbselect("SELECT * FROM cat WHERE id='".$_GET['cat']."'", 0, $edCat, 'row');
$pathArr=explode('/', $edCat['path']);
$path=$pathArr[count($pathArr)-1];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<title>Редактирование настроек альбома</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1"> 
<script language="javascript">
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
</head>
<link href="/editor/style.css" rel="stylesheet" type="text/css">
<body marginheight="0" marginwidth="0" onUnload="opener.location.href='/editor/editor.php?cat=<?=$_GET['cat']?>'; window.focus()">
 <form method="post" name="formAlbum" action="<?php echo $editFormAction; ?>" enctype="multipart/form-data">
<center><strong>Настройки альбома &laquo;<?=$edAlbum['name']?>&raquo;</strong></center>
<table align="center" class="el_tbl">
    <tr valign="baseline"> 
      <td align="right" nowrap>Название<span class="style3">*</span>:</td>
      <td><input type="text" name="name" size="32" value="<?=str_replace('"','``',str_replace("'",'`',$edAlbum['name']))?>"></td>
    </tr>
    <tr valign="baseline">
      <td align="right" valign="top" nowrap>Описание:</td>
      <td valign="top"><textarea name="ptext" cols="40" rows="5" id="ptext"><?=$edCat['ptext']?></textarea><br>
      <input type="button" onClick="MM_openBrWindow('/editor/newseditor.php?field=ptext&form=formAlbum','newcateditor','','785','625','true')" src="img/code.gif" value="HTML-редактор" class="but"></td>
    </tr>
    <tr valign="baseline">
      <td align="right" valign="top" nowrap>Название папки <br> 
      одним словом (<span class="style2">обязательно</span>),<br> 
      используйте все маленькие латинские буквы<span class="style3">*</span>: </td>
      <td valign="bottom"><input name="path" type="text" id="path" size="32" value="<?=$path?>">
      <input type="hidden" name="oldPath" value="<?=$edCat['path']?>" />
      </td>
    </tr>
    <tr valign="baseline">
      <td align="right" valign="top" nowrap>Порядковый номер в меню: </td>
      <td valign="top"><input name="sort" type="text" id="sort" value="<?=$edAlbum['sort']?>" size="5"></td>
    </tr>
    <tr valign="baseline">
      <td align="right" valign="top" nowrap>Дата создания альбома: </td>
      <td valign="top"><input name="date_create" type="text" id="sort" value="<?=$edAlbum['date_create']?>" size="15"></td>
    </tr>
<tr>
      <td align="right">Обложка альбома: </td>
      <td valign="top">
      <img src="<?=$edAlbum['cover']?>" /><br />
      <input type="hidden" name="oldCover" value="<?=$edAlbum['cover']?>" />
      <input type="file" size=40 name="cover"></td>
    </tr>    
    <tr valign="baseline">
      <td align="right" valign="top" nowrap>Шаблон страницы: </td>
      <td valign="top"><select name="template" id="template">
        <?php
		$template = el_dbselect("SELECT * FROM template WHERE `master`<>1", 0, $template);
		$row_template = el_dbfetch($template);
		do {  
		?>
        <option value="<?php echo $row_template['path']?>" <?=($row_template['default']==1)?'selected':''?>><?php echo $row_template['name']?></option>
        <?php
		} while ($row_template = el_dbfetch($template));
		?>
      </select></td>
    </tr>
    <tr valign="baseline">
      <td align="right" nowrap>Не показывать  в меню: </td>
      <td><input name="menu" type="checkbox" id="menu" value="N" <?=($edCat['menu']=='N')?'checked':''?>></td>
    </tr>
    <tr>
      <td align="right" valign="top">Активный:</td>
      <td><input <?php if (!(strcmp($edAlbum['active'],"1"))) {echo "checked";} ?> name="active" type="checkbox" id="active" value="1"></td>
    </tr>
   <tr>
  <td align="right"><br>
      <input type="submit" name="Submit" value="Сохранить" class="but"></td>
  <td align="center"><br>
      <input type="button" name="Button" value="Закрыть" onClick="window.close();" class="but"></td>
</tr>
  </table>
  </form>
</body>
</html>
