
<?php
$cat=intval($_GET['cat']);
include $_SERVER['DOCUMENT_ROOT'].'/Connections/site_props.php';

$currentPage = $_SERVER["PHP_SELF"];
//Удаление картинки
if (isset($_GET['delete']) && $_GET['delete']=='Yes') {
	while(($row=el_dbfetch($result)));{
		$id=$row["id"];
		if(isset($_GET[$id])&&($_GET[$id]=="ON")){
			unlink($_SERVER['DOCUMENT_ROOT'].$row['path']);
			unlink($_SERVER['DOCUMENT_ROOT'].$row['smallpath']);
			if(!mysqli_query($database_dbconn,"delete from photo where id='$id'")) {
				 DisplayErrMsg(sprintf("Внутренняя ошибка %d:$s\n", mysqli_errno(),mysqli_error()));
				exit(); 
			}
		}
	}
	//@header("Location: ".$_SERVER['PHP_SELF']."?cat=".$cat);
	el_clearCache('catalogs');
}



$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
//Добавление новой картинки
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
	$err=$both=0;
	$errStr='';
	$_POST['swidth']=(strlen($_POST['swidth'])>0)?intval($_POST['swidth']):$site_property['gallerySWidth'.$cat];
	$_POST['sheight']=(strlen($_POST['sheight'])>0)?intval($_POST['sheight']):$site_property['gallerySHeight'.$cat];
	$_POST['bwidth']=(strlen($_POST['bwidth'])>0)?intval($_POST['bwidth']):$site_property['galleryBWidth'.$cat];
	$_POST['bheight']=(strlen($_POST['bheight'])>0)?intval($_POST['bheight']):$site_property['galleryBHeight'.$cat];
	$_FILES['file']['name']=(strlen($_FILES['file']['name'])>0)?$_FILES['file']['name']:$_FILES['big_file']['name'];
	$targetFileName=el_newName($_SERVER['DOCUMENT_ROOT'].'/images/gallery/', 'photo_'.el_translit($_FILES['big_file']['name']));
	$targetFileNameSmall=el_newName($_SERVER['DOCUMENT_ROOT'].'/images/small/', 'preview_'.el_translit($_FILES['file']['name']));

	if(strlen($_FILES['file']['tmp_name'])>0){
		if(el_resize_images($_FILES['file']['tmp_name'], $targetFileNameSmall, $_POST['swidth'], $_POST['sheight'], 'small/')){
			@chmod($_SERVER['DOCUMENT_ROOT'].'/images',0777);
			@chmod($_SERVER['DOCUMENT_ROOT'].'/images/small',0777);
			$_POST['smallpath']='/images/small/'.$targetFileNameSmall;
		}else{
			$errStr.="Не удалось закачать маленькую картинку.\\nПроверьте права доступа у папки images/small\\n";
			$err++;
		}
	}elseif(empty($_FILES['file']['tmp_name']) && strlen($_FILES['big_file']['name'])>0){
		
		$tempDir=$_SERVER['DOCUMENT_ROOT'].'/images/temporary/';
		$tempFileName=el_translit($_FILES['big_file']['name']);
		if(!is_dir($tempDir))mkdir($tempDir, 0777);
		copy($_FILES['big_file']['tmp_name'], $tempDir.$tempFileName);
		chmod($tempDir.$tempFileName, 0777);
		
		if(el_resize_images($tempDir.$tempFileName, $targetFileName, $_POST['bwidth'], $_POST['bheight'], 'gallery/')){
			
			el_resize_images($tempDir.$tempFileName, $targetFileNameSmall, $_POST['swidth'], $_POST['sheight'], 'small/');
			
			@chmod($_SERVER['DOCUMENT_ROOT'].'/images',0777);
			@chmod($_SERVER['DOCUMENT_ROOT'].'/images/small',0777);
			@chmod($_SERVER['DOCUMENT_ROOT'].'/images/gallery',0777);
			$_POST['path']='/images/gallery/'.$targetFileName;
			$_POST['smallpath']='/images/small/'.$targetFileNameSmall;
			$both=1;
		}else{
			$errStr.="Не удалось закачать большую картинку.\\nПроверьте права доступа у папки images/gallery\\n";
			$err++;
		}
		unlink($tempDir.$tempFileName);
	}
	if(strlen($_FILES['big_file']['name'])>0 && $both==0){
		if(el_resize_images($_FILES['big_file']['tmp_name'], $targetFileName, $_POST['bwidth'], $_POST['bheight'], 'gallery/')){
			@chmod($_SERVER['DOCUMENT_ROOT'].'/images',0777);
			@chmod($_SERVER['DOCUMENT_ROOT'].'/images/small',0777);
			@chmod($_SERVER['DOCUMENT_ROOT'].'/images/gallery',0777);
			$_POST['path']='/images/gallery/'.$targetFileName;
		}else{
			$errStr.="Не удалось закачать большую картинку.\\nПроверьте права доступа у папки images/gallery\\n";
			$err++;
		}
	}
	
	if($err==0){ 
  		el_imageLogo($_POST['path'], '/images/logo_small.gif', 'bottom-right');
		$insertSQL = sprintf("INSERT INTO photo (`path`, text, smallh, smallw, bigh, bigw, caption, smallpath, author, raiting, date_add, sort, in_comments, in_rait) 
		VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['path'], "text"),
                       GetSQLValueString(nl2br($_POST['text']), "text"),
                       GetSQLValueString($_POST['sheight'], "int"),
						GetSQLValueString($_POST['swidth'], "int"),
						GetSQLValueString($_POST['bheight'], "int"),
						GetSQLValueString($_POST['bwidth'], "int"),
                       GetSQLValueString($cat, "int"),
                       GetSQLValueString($_POST['smallpath'], "text"),
					   GetSQLValueString($_POST['author'], "text"),
					   GetSQLValueString($_POST['raiting'], "int"),
					   GetSQLValueString($_POST['date_add'], "date"),
					   GetSQLValueString($_POST['sort'], "int"),
					   GetSQLValueString(($_POST['in_comments']=='1')?1:0, "int"),
					   GetSQLValueString(($_POST['in_rait']=='1')?1:0, "int"));

		  
		  $Result1=el_dbselect($insertSQL, 0, $Result1);
		 el_clearCache('catalogs');
	}else{
		echo '<script language=javascript>alert("'.$errStr.'")</script>';
	}
}

if(isset($_POST['alid'])){
	$ims=el_dbselect("SELECT path, smallpath FROM photo WHERE id='".$_POST['alid']."'",0,$ims);
	$imgs=el_dbfetch($ims);
	$oldbig=$_SERVER['DOCUMENT_ROOT'].$imgs['path'];
	$oldsmall=$_SERVER['DOCUMENT_ROOT'].$imgs['smallpath'];
	$fname=substr(strrchr($imgs['path'], '/'), 1);
	$dname=substr(strrchr(str_replace('/'.$fname, '', $imgs['path']), '/'), 1);
	$newbig=$_SERVER['DOCUMENT_ROOT'].'/images/gallery/'.$fname;
	$newsmall=$_SERVER['DOCUMENT_ROOT'].'/images/small/'.$fname;
	copy($oldbig, $newbig);
	copy($oldsmall, $newsmall);
	unlink($oldbig);
	unlink($oldsmall);
	rmdir($_SERVER['DOCUMENT_ROOT'].'/images/gallery/'.$dname);
	rmdir($_SERVER['DOCUMENT_ROOT'].'/images/small/'.$dname);
	$upim=el_dbselect("UPDATE photo SET path='/images/gallery/".$fname."', smallpath='/images/small/".$fname."', in_comments=1, in_rait=1, status=1 WHERE id='".$_POST['alid']."'",0,$upim);
	el_clearCache('catalogs');
}


//Сохранение настроек галереи для этого раздела
if(isset($_POST['propc']) && $_POST['propc']=='update'){
	if($_POST['modemail']!=$site_property['modemail'.$cat]){
		el_2ini('modemail'.$cat, $_POST['modemail']);
	}
	if($_POST['cols']!=$site_property['gallcols'.$cat]){
		el_2ini('gallcols'.$cat, $_POST['cols']);
	}
	if($_POST['rows']!=$site_property['gallphotos'.$cat]){
		el_2ini('gallphotos'.$cat, $_POST['rows']);
	}
	if($_POST['allowupload']!=$site_property['allowupload'.$cat]){
		el_2ini('allowupload'.$cat, $_POST['allowupload']);
	}
	if($_POST['showMode']!=$site_property['gallShowMode'.$cat]){
		el_2ini('gallShowMode'.$cat, $_POST['showMode']);
	}
	if($_POST['sheightAlbum']!=$site_property['galleryAlbumSHeight'.$cat])el_2ini('galleryAlbumSHeight'.$cat, intval($_POST['sheightAlbum']));
	if($_POST['swidthAlbum']!=$site_property['galleryAlbumSWidth'.$cat])el_2ini('galleryAlbumSWidth'.$cat, intval($_POST['swidthAlbum']));
	if($_POST['swidth']!=$site_property['gallerySWidth'.$cat])el_2ini('gallerySWidth'.$cat, intval($_POST['swidth']));
	if($_POST['sheight']!=$site_property['gallerySHeight'.$cat])el_2ini('gallerySHeight'.$cat, intval($_POST['sheight']));
	if($_POST['bwidth']!=$site_property['galleryBWidth'.$cat])el_2ini('galleryBWidth'.$cat, intval($_POST['bwidth']));
	if($_POST['bheight']!=$site_property['galleryBHeight'.$cat])el_2ini('galleryBHeight'.$cat, intval($_POST['bheight']));

	echo '<script language=javascript>alert("Изменения в настройках сохранены!")</script>';
}


//Удаление альбома с содержимым
if(isset($_POST['category']) && strlen($_POST['category'])>0){
	$_POST['category']=intval($_POST['category']);
	$da=el_dbselect("SELECT id, path, smallpath FROM photo WHERE caption=".$_POST['category'], 0, $da);
	$dc=el_dbselect("SELECT path, parent FROM cat WHERE id=".$_POST['category'], 0, $dc, 'row');
	$pdc=el_dbselect("SELECT id FROM cat WHERE id=".$dc['parent'], 0, $dc, 'row');
	$idc=el_dbselect("SELECT cover FROM photo_albums WHERE cat=".$_POST['category'], 0, $idc, 'row');
	$rda=el_dbfetch($da);
	do{
		if(strlen($rda['smallpath'])>0 && file_exists($_SERVER['DOCUMENT_ROOT'].$rda['smallpath']))unlink($_SERVER['DOCUMENT_ROOT'].$rda['smallpath']);
		if(strlen($rda['path'])>0 && file_exists($_SERVER['DOCUMENT_ROOT'].$rda['path']))unlink($_SERVER['DOCUMENT_ROOT'].$rda['path']);
	}while($rda=el_dbfetch($da));
	if(strlen($idc['cover'])>0 && file_exists($_SERVER['DOCUMENT_ROOT'].$idc['cover']))unlink($_SERVER['DOCUMENT_ROOT'].$idc['cover']);
	el_dbselect("DELETE FROM photo WHERE caption=".$_POST['category'], 0, $res);
	el_dbselect("DELETE FROM photo_albums WHERE cat=".$_POST['category'], 0, $res);
	el_dbselect("DELETE FROM cat WHERE id=".$_POST['category'], 0, $res);
	el_dbselect("DELETE FROM content WHERE cat=".$_POST['category'], 0, $res);
	if($_SERVER['DOCUMENT_ROOT'].$dc['path']!=$_SERVER['DOCUMENT_ROOT'])el_delDir($_SERVER['DOCUMENT_ROOT'].$dc['path']);
	$tdc=el_dbselect("SELECT * FROM photo_albums", 0, $tdc);
	if(mysqli_num_rows($tdc)==0){
		echo '<script language=javascript>document.location.href="/editor/editor.php?cat='.$pdc['id'].'"</script>';
	}
	el_clearCache('catalogs');
	el_clearCache('menu');
}


//Создание нового альбома
if(isset($_POST['new_album']) && $_POST['new_album']=='1'){
	$_POST["MM_insert"] ="form1";
	create_cat($_GET['cat']);
	$AlbumW=(strlen($site_property['galleryAlbumSWidth'.$cat])>0)?$site_property['galleryAlbumSWidth'.$cat]:$site_property['gallerySWidth'.$cat];
	$AlbumH=(strlen($site_property['galleryAlbumSHeight'.$cat])>0)?$site_property['galleryAlbumSHeight'.$cat]:$site_property['gallerySHeight'.$cat];
	if(strlen($AlbumW)==0){$AlbumW=200;}
	if(strlen($AlbumH)==0){$AlbumH=200;} 
	if(strlen($_FILES['cover']['name'])>0){
		if(el_resize_images($_FILES['cover']['tmp_name'], el_translit($_FILES['cover']['name']), $AlbumW, $AlbumH, 'small/cover_')){
			@chmod($_SERVER['DOCUMENT_ROOT'].'/images',0777);
			@chmod($_SERVER['DOCUMENT_ROOT'].'/images/small',0777);
			$coverPath='/images/small/cover_'.el_translit($_FILES['cover']['name']);
		}else{
			$errStr.='Не удалось закачать обложку.\\nПроверьте права доступа у папки images/small\\n';
			$err++;
		}
	} 
	
	$idnew=el_dbselect("SELECT LAST_INSERT_ID() as a FROM cat", 0, $idnew, 'row');
	$newId=$idnew['a']+1;
	if($idnew['a']>0){ 
		$new_alb=el_dbselect("INSERT INTO photo_albums (`name`, date_create, cover, `sort`, parent_cat, cat,active) VALUES 
		(".GetSQLValueString($_POST['name'], "text").", '".$_POST['date_create']."', '".$coverPath."', 
		'".intval($_POST['sort'])."', '".$cat."', '".$newId."', ".GetSQLValueString(isset($_POST['active'])?"true":"", "defined","'1'","'0'").")", 0, $res, 'result', true);
	}
	el_2ini('modemail'.$newId, $_POST['modemail']);
	el_2ini('gallcols'.$newId, $_POST['cols']);
	el_2ini('gallphotos'.$newId, $_POST['rows']);
	el_2ini('allowupload'.$newId, $_POST['allowupload']);
	el_2ini('gallShowMode'.$newId, $_POST['showMode']);
	el_2ini('galleryAlbumSHeight'.$newId, intval($_POST['sheightAlbum']));
	el_2ini('galleryAlbumSWidth'.$newId, intval($_POST['swidthAlbum']));
	el_2ini('gallerySWidth'.$newId, intval($_POST['swidth']));
	el_2ini('gallerySHeight'.$newId, intval($_POST['sheight']));
	el_2ini('galleryBWidth'.$newId, intval($_POST['bwidth']));
	el_2ini('galleryBHeight'.$newId, intval($_POST['bheight']));
	el_clearCache('catalogs');
	el_clearCache('menu');
	/*echo '<script language=javascript>document.location.href="/editor/editor.php?cat='.$newId.'"</script>';*/
}

$maxRows_Recordset1 = 12;
$pn = 0;
if (isset($_GET['pn'])) {
  $pn = $_GET['pn'];
}
$startRow_Recordset1 = $pn * $maxRows_Recordset1;

$colname_Recordset1 = "1";
if (isset($_GET['cat'])) {
  $colname_Recordset1 = (get_magic_quotes_gpc()) ? $_GET['cat'] : addslashes($_GET['cat']);
}


$query_Recordset1 = sprintf("SELECT * FROM photo WHERE caption = '%s' ORDER BY status ASC, sort ASC, id DESC", $colname_Recordset1);
$Recordset1=el_dbselect($query_Recordset1, $maxRows_Recordset1, $Recordset1, 'result', true);

if (isset($_GET['tr'])) {
  $tr = $_GET['tr'];
} else {
  $all_Recordset1 = el_dbselect($query_Recordset1, 0, $all_Recordset1);
  $tr = mysqli_num_rows($all_Recordset1);
}
$totalPages_Recordset1 = ceil($tr/$maxRows_Recordset1)-1;



$colname_dbphotocat = "1";
if (isset($_GET['cat'])) {
  $colname_dbphotocat = (get_magic_quotes_gpc()) ? $_GET['cat'] : addslashes($_GET['cat']);
}

$query_dbphotocat = sprintf("SELECT * FROM cat WHERE id = %s", $colname_dbphotocat);
$dbphotocat=el_dbselect($query_dbphotocat, 0, $dbphotocat);
$row_dbphotocat = el_dbfetch($dbphotocat);
$totalRows_dbphotocat = mysqli_num_rows($dbphotocat);

$queryString_Recordset1 = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pn") == false && 
        stristr($param, "tr") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_Recordset1 = "&" . implode("&", $newParams);
  }
}
$queryString_Recordset1 = sprintf("&tr=%d%s", $tr, $queryString_Recordset1);

//Делаем превьюшки автоматически
if (isset($automate)) {
//Определяем пути к папкам
//echo $cat;
$result10=mysqli_query($dbconn, "select * from photocat where id='$cat'");
$row10=el_dbfetch($result10);;
$par10=$row10['parent'];
  if ($par10>0) {$parent10=$par10."/";}else{$parent10="";}
  $smallpath="images/small/".$parent10.$cat."/";			
  $d=dir("../images/gallery/".$parent10.$cat."/");
  while($entry=$d->read()) {
  if ($entry!="." && $entry!="..") {
$smallW = 200;
$path1="images/gallery/".$parent10.$cat."/".$entry;
$bigsize = getimagesize("../".$path1);
$imH = $bigsize[1];
$imW = $bigsize[0];
$prop = $imW/$imH;
$smallH = $smallW/$prop;
$filesmall = imagecreatetruecolor ($smallW, $smallH);
$image = ImageCreateFromJpeg("../".$path1);
imagecopyresampled($filesmall, $image, 0, 0, 0, 0, $smallW, $smallH, $imW, $imH);
imagejpeg($filesmall, "../".$smallpath.$entry, 100);
ImageDestroy($filesmall);
copy($file, "../".$smallpath);
//Вносим запись в базу данных
$insertSQL = sprintf("INSERT INTO photo (caption, `path`,`smallpath`, text, bigh, bigw, sort) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($cat, "text"),
                       GetSQLValueString($path1, "text"),
					   GetSQLValueString($smallpath.$entry, "text"),
                       GetSQLValueString($_POST['text'], "text"),
                       GetSQLValueString($_POST['bigh'], "int"),
                       GetSQLValueString($_POST['bigw'], "int"),
					   GetSQLValueString($_POST['sort'], "int"));

  
  $Result1=el_dbselect($insertSQL, 0, $Result1);
  
 } }
  $d->close();
  mysqli_free_result($result10);
  echo "Для всех фотографий сделаны картинки для предпросмотра!";
}

$pat=el_dbselect("SELECT path FROM cat WHERE id=".$cat,0,$path,'row');
$path=$pat['path'];

$albums=el_dbselect("SELECT * FROM photo_albums ORDER BY id DESC", 0, $albums, 'result', true);
if(!$_SESSION['img_num'])session_register('img_num');
$_SESSION['img_num']=0;
?>
<link href="/js/swfu/css/default.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/swfu/swfupload.js"></script>
<script type="text/javascript" src="/js/swfu/handlers.js"></script>
<script language="JavaScript">
<!--

function flvFPW1(){
var v1=arguments,v2=v1[2].split(","),v3=(v1.length>3)?v1[3]:false,v4=(v1.length>4)?parseInt(v1[4]):0,v5=(v1.length>5)?parseInt(v1[5]):0,v6,v7=0,v8,v9,v10,v11,v12,v13,v14,v15,v16;v11=new Array("width,left,"+v4,"height,top,"+v5);for (i=0;i<v11.length;i++){v12=v11[i].split(",");l_iTarget=parseInt(v12[2]);if (l_iTarget>1||v1[2].indexOf("%")>-1){v13=eval("screen."+v12[0]);for (v6=0;v6<v2.length;v6++){v10=v2[v6].split("=");if (v10[0]==v12[0]){v14=parseInt(v10[1]);if (v10[1].indexOf("%")>-1){v14=(v14/100)*v13;v2[v6]=v12[0]+"="+v14;}}if (v10[0]==v12[1]){v16=parseInt(v10[1]);v15=v6;}}if (l_iTarget==2){v7=(v13-v14)/2;v15=v2.length;}else if (l_iTarget==3){v7=v13-v14-v16;}v2[v15]=v12[1]+"="+v7;}}v8=v2.join(",");v9=window.open(v1[0],v1[1],v8);if (v3){v9.focus();}document.MM_returnValue=false;return v9;}


function checkdel(){
if (document.getElementById("delall").checked==true){
for(var counter=0; counter < Delete.elements.length; counter++){
Delete.elements[counter].checked=true
 }
} else{
for(var counter=0; counter < Delete.elements.length; counter++){
Delete.elements[counter].checked=false
 }
}
}

function allow(obj){
	var s=document.alform.alid;
	s.value=obj;
	document.alform.submit();
}
function comments(id){
	location.href="/editor/modules/gallery/commentsedit.php?comm="+id+"&cat=<?=$cat?>";
}
function tab_switch(obj, id){
	var sw=document.getElementById("switch_tabs").childNodes;
	var pan=document.getElementById("switchPanels").childNodes;
	for(var a=0; a<pan.length; a++){
		if(pan[a].className=='switch_panel')pan[a].style.display='none';
	}
	document.getElementById(id).style.display='block';
	for(var i=0; i<sw.length; i++){
		sw[i].className='';
	}
	obj.className='current';
	document.location.href='/editor/editor.php?cat=<?=$_GET['cat']?>#tabBottom';
}

function delAlbum(cat, name){
	var ok=confirm("Вы уверены, что хотите удалить альбом \""+name+"\"?\nИзображения в составе альбома то же буду удалены.");
	if(ok){
		document.deleteAlbum.category.value=cat;
		document.deleteAlbum.submit();
	}
}

function editText(id){
	var txt=$('#textImg'+id).html().replace(/<BR>/gi, "\n");
	$('#textImg'+id).slideUp('fast');
	$('#textarea'+id).html('<textarea cols=20 rows=10 id="area'+id+'">'+txt+'</textarea><br><table border=0 width=100% cellpadding=5><tr><td><button class="but" onclick="saveTextImg('+id+', this); return false;">Сохранить</button></td><td align=right><button class="but" style="float:right" onclick="cancelSaveText('+id+')">Отмена</button></td></tr></table>');
	$('#textarea'+id).slideDown('fast');
}

function cancelSaveText(id){
	try{event.preventDefault();}catch(e){}
	$('#area'+id).text('');
	$('#textarea'+id).slideUp('fast');
	$('#textImg'+id).slideDown('fast');
}

function saveTextImg(id, self){

	$.post('/editor/modules/gallery/save_text.php', {'text':$('#area'+id).val(), 'id':id}, 
					function(data){$('#textImg'+id).html(data);cancelSaveText(id);})
					
					
}

function getimgGlbalText(){ //alert(document.getElementById('photoText').value);
	return document.getElementById('photoText').value;
}

var swfu;
		window.onload = function () {
			swfu = new SWFUpload({
				// Backend Settings
				upload_url: "/editor/modules/gallery/upload.php",
				post_params: {"PHPSESSID": "<?php echo session_id(); ?>", "cat":<?=$cat?>, "in_comments":1, "in_rait":1},

				// File Upload Settings
				file_size_limit : "20 MB",	// 2MB
				file_types : "*.jpg;*.gif;*.png",
				file_types_description : "Файлы изображений",
				file_upload_limit : 0,

				// Event Handler Settings - these functions as defined in Handlers.js
				//  The handlers are not part of SWFUpload but are part of my website and control how
				//  my website reacts to the SWFUpload events.
				swfupload_preload_handler : preLoad,
				swfupload_load_failed_handler : loadFailed,
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				upload_start_handler : uploadStart,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				upload_complete_handler : uploadComplete,
				
				thumb_width: <?=(intval($site_property['gallerySWidth'.$cat])==0)?'200':$site_property['gallerySWidth'.$cat]?>,
				thumb_height: <?=(intval($site_property['gallerySHeight'.$cat])==0)?'200':$site_property['gallerySHeight'.$cat]?>,

				// Button Settings
				button_image_url : "/js/swfu/images/SmallSpyGlassWithTransperancy_17x18.png",
				button_placeholder_id : "spanButtonPlaceholder",
				button_width: 190,
				button_height: 18,
				button_text : '<span class="button">Выберите файлы <span class="buttonSmall">(макс. 20 MB)</span></span>',
				button_text_style : '.button { font-family: Helvetica, Arial, sans-serif; font-size: 12pt; } .buttonSmall { font-size: 10pt; }',
				button_text_top_padding: 0,
				button_text_left_padding: 18,
				button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
				button_cursor: SWFUpload.CURSOR.HAND,
				
				// Flash Settings
				flash_url : "/js/swfu/swfupload.swf",
				flash9_url : "/js/swfu/swfupload_fp9.swf",

				custom_settings : {
					upload_target : "divFileProgressContainer",
					cat : "<?=$cat?>"
				},
				
				// Debug Settings
				debug: false
			});
		};
//-->
</script>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
a:link {
	text-decoration: none;
}
a:visited {
	text-decoration: none;
}
a:hover {
	text-decoration: underline;
}
a:active {
	text-decoration: none;
}
ul#albums{list-style:none; width:100%; position:relative}
ul#albums li{float:left; margin:10px;}
ul#albums .edit{color:#36C; cursor:pointer; background:url(/editor/img/page_paintbrush.gif) no-repeat; padding-left:23px; width:130px}
-->
</style>
<?

//Определяем можно ли показывать вкладку создания альбома, что бы не было вложенных альбомов.
$r=el_dbselect("SELECT parent FROM cat WHERE id=$cat", 0, $r, 'row');
$k=el_dbselect("SELECT kod FROM content WHERE id=".$r['parent'], 0, $k, 'row');


if(@mysqli_num_rows($albums)>0){ ?>
	
    <h5 align=left>Альбомы</h5>
	<div style="height:600px; overflow:auto; width:<?=(strlen($site_property['galleryAlbumSWidth'.$cat])>0)?($site_property['galleryAlbumSWidth'.$cat]+30):'220'?>px; float:left; margin-left:5px; ">
	<? el_dbrowprint($albums, '/editor/modules/gallery/album.php', '<h5 align=center>Альбомы пока не созданы</h5>'); ?>
	</div>
    
   
<? }?>


 
<h4>Альбом &laquo;<?php echo $row_dbphotocat['name']; ?>&raquo;</h4>
<form name="alform" method="post">
<input type="hidden" name="alid">
</form>
<form method="post" name="deleteAlbum">
<input type="hidden" name="category" />
</form>
 
<? if(isset($cat)){ ?>

<form name="Delete" method="get" action="<?php echo $_SERVER['PHP_SELF']."?cat=".$_GET["cat"]; ?>">
<center>
	<? if($tr>0){
		el_dbpagecount($Recordset1, '', $maxRows_Recordset1, $tr, '/tmpl/pagecount.php');?>
    <div style="float:right; max-width:78%">
      
        Показаны картинки с <?php echo ($startRow_Recordset1 + 1) ?> по <?php echo min($startRow_Recordset1 + $maxRows_Recordset1, $tr) ?> 
        из <?php echo $tr ?> 
      
      
     
        <?
			
			echo '<center><ul id="albums">';
			el_dbrowprint($Recordset1, '/editor/modules/gallery/image.php', '<h5 align=center>Пока нет ни одной картинки</h5>');
			echo '</ul></center><br>';
		?>
        
        </div>
        <div style="clear:both"></div>
        <? el_dbpagecount($Recordset1, '', $maxRows_Recordset1, $tr, '/tmpl/pagecount.php');?>
        <input name="delete" type="hidden" id="delete" value="Yes">
        <input name="cat" type="hidden" id="cat" value="<? echo $_GET["cat"]; ?>">
        <label for="delall"><input name="delall" type="checkbox" id="delall" onClick="checkdel()" value="checkbox">
Отметить все картинки на этой странице для удаления</label><br><br>
		<input name="Submit" type="submit" value="Удалить выбранное" class="but">
		
        <? 
		}else{
			echo '<h5 align=center id="noImages">Пока нет ни одной картинки</h5>';
		}?>
        </center>
 </form>     
 <br>




<? if(isset($cat)) { ?>
<div style="clear:both"></div>
<ul class="tab_switcher" id="switch_tabs">
<? if($k['kod']!='gallery'){?>
<li id="g1" class="current" onClick="tab_switch(this, 'new_album')"><a>Создать альбом</a></li>
<? }?>
<li id="g2" <?=($k['kod']=='gallery')?'class="current"':''?> onClick="tab_switch(this, 'gall_multi')"><a>Мультизагрузка фотографий</a></li>
<li id="g3" onClick="tab_switch(this, 'new_img')"><a>Добавить фотографию</a></li>
<li id="g4" onClick="tab_switch(this, 'gall_options')"><a>Настройки модуля</a></li>
</ul>
<!--
<form name="automate" method="post" action="?autamate=1&cat=<? echo $cat ?>">
  <center>
    <label>Создать маленькие картинки авоматически 
    <input type="submit" name="Submit" value="OK">
    <input name="automate" type="hidden" id="automate">
</label>
  </center>
</form>
 -->
 <div id="switchPanels">
 <? if($k['kod']!='gallery'){?>
 <div id="new_album" class="switch_panel" style="display:block">
 <form method="post" name="new_album" action="<?php echo $editFormAction; ?>" enctype="multipart/form-data">
<div align="center" style="width:400px"><strong>Создание нового альбома</strong> <br> 
в разделе &laquo;<?php echo $row_dbphotocat['name']; ?>&raquo;</div>
<table align="center" class="el_tbl">
    <tr valign="baseline"> 
      <td align="right" nowrap>Название<span class="style3">*</span>:</td>
      <td><input type="text" name="name" value="" size="32"> 
      <input type="hidden" name="new_album" value="1">
      <input type="hidden" name="parent" value="<?=$_GET['cat'] ?>">
      </td>
    </tr>
    <tr valign="baseline">
      <td align="right" valign="top" nowrap>Описание:</td>
      <td valign="top"><textarea name="ptext" cols="40" rows="5" id="ptext"></textarea><br>
      <input type="button" onClick="MM_openBrWindow('newseditor.php?field=ptext&form=formAlbum','newcateditor','','785','625','true')" src="img/code.gif" value="HTML-редактор" class="but"></td>
    </tr>
    <tr valign="baseline">
      <td align="right" valign="top" nowrap>Название папки <br> 
      одним словом (<span class="style2">обязательно</span>),<br> 
      используйте все маленькие латинские буквы<span class="style3">*</span>: </td>
      <td valign="bottom"><input name="path" type="text" id="path" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td align="right" valign="top" nowrap>Порядковый номер в меню: </td>
      <td valign="top"><input name="sort" type="text" id="sort" value="100" size="5">
      <input type="hidden" name="kod" id="kod" value="gallery"></td>
    </tr>
    <tr valign="baseline">
      <td align="right" valign="top" nowrap>Дата создания альбома: </td>
      <td valign="top"><input name="date_create" type="text" id="sort" value="<?=date('Y-m-d')?>" size="15"></td>
    </tr>
<tr>
      <td align="right">Обложка альбома: </td>
      <td valign="top"><input type="file" size=40 name="cover"></td>
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
 <tr>
         <td align="right" nowrap>E-mail модератора :</td>
          <td><input name="modemail" type="text" id="modemail" size="20"></td>
        </tr>
        <tr>
          <td align="right" nowrap>Столбцов на страницу :</td>
          <td><input name="cols" type="text" id="cols" size="4" value="3"></td>
        </tr>
        <tr>
          <td align="right" nowrap>Фотографий на страницу :</td>
          <td><input name="rows" type="text" id="rows" size="4" value="9"></td>
        </tr>
	<tr valign="baseline">
      <td valign="top" align="right" nowrap>Макс. размеры обложки альбома : </td>
      <td> ширина:
        <input name="swidthAlbum" type="text" id="swidthAlbum" value="200" size="5"> 
        px,&nbsp;&nbsp;высота: 
        <input name="sheightAlbum" type="text" id="sheightAlbum" value="200" size="5">
        px</td>
    </tr>
    <tr valign="baseline">
      <td valign="top" align="right" nowrap>Макс. размеры маленькой картинки : </td>
      <td> ширина:
        <input name="swidth" type="text" id="swidth" value="200" size="5"> 
        px,&nbsp;&nbsp;высота: 
        <input name="sheight" type="text" id="sheight" value="200" size="5">
        px</td>
    </tr>
	<tr valign="baseline">
      <td valign="top" align="right" nowrap>Макс. размеры большой картинки : </td>
      <td> ширина: 
        <input name="bwidth" type="text" id="bwidth" value="1000" size="5"> 
        px,&nbsp;&nbsp;высота: 
        <input name="bheight" type="text" id="bheight" value="850" size="5"> 
        px</td>
    </tr>
    <tr>
      <td valign="top" align="right" nowrap>Показывать большую картинку: </td>
      <td><p>
        <label for="showMode_0">
          <input type="radio" name="showMode" value="popup" id="showMode_0">
          Во всплывающем слое</label>
        <br>
        <label for="showMode_1">
          <input type="radio" name="showMode" value="page" id="showMode_1" checked>
          На той же странице</label>
        <br>
      </p></td>
    </tr>
    <tr>
      <td align="right" nowrap>Разрешить закачку пользователям </td>
      <td><input name="allowupload" type="checkbox" id="rows" value="Y"></td>
    </tr>
    <tr valign="baseline">
      <td align="right" nowrap>Не показывать  в меню: </td>
      <td><input name="menu" type="checkbox" id="menu" value="N" checked="checked"></td>
    </tr>
    <tr valign="baseline">
      <td align="right" nowrap>Активный: </td>
      <td><input name="active" type="checkbox" id="active" value="1" checked ></td>
    </tr>
    <tr valign="baseline"> 
      <td colspan="2" align="center"><br>
      <input name="SubmitAlbum" type="submit" class="but" value="Создать"></td>
    </tr>
  </table>
  </form>
 </div>
 <? }?>
 <div id="new_img" class="switch_panel" style="display:none">
<form method="post" name="new_img" action="<?php echo $editFormAction; ?>" enctype="multipart/form-data">
  <div align="center" style="width:400px"><strong>Вставка новой картинки</strong> <br>
в альбом &laquo;<?php echo $row_dbphotocat['name']; ?>&raquo;</div>
<div align="center" style="width:600px"><? el_showalert('warning', '
		<b>Важно!</b> Название файла должно должно состоять только из латинских букв или цифр<br>
		Чтобы браузер пользователя смог отобразить Ваше изображение закачивайте файлы в следующих форматах - *.jpg, *.gif, *.png<br>
        Файл будет помещен в директорию &quot;images/gallery&quot;. <br>
		Если маленькая картинка явно не указана, то будет создана автоматически из большой.<br>
        Выберите файл, нажав на кнопку &quot;Обзор&quot;, а затем нажмите кнопку &quot;Добавить&quot;') ?> </div>   
  
  <table width="400" align="center" class="el_tbl">
     <tr>
      <td align="right" nowrap bgcolor="#E0E0E0">Маленькая картинка: </td>
      <td valign="top" nowrap bgcolor="#E0E0E0"><input type="file" size=40 name="file">
        <input type="hidden" name="smallpath" value="">
       </td>
    </tr>
    <tr>
      <td align="right" nowrap bgcolor="#E0E0E0">Большая картинка: </td>
      <td valign="top" nowrap bgcolor="#E0E0E0">
      <input type="file" size=40 name="big_file">
        <input type="hidden" name="path" value=""></td>
    </tr>
	<tr valign="baseline">
      <td align="right" nowrap bgcolor="#E0E0E0">Макс. размеры маленькой картинки : </td>
      <td valign="baseline" bgcolor="#E0E0E0"> ширина:
        <input name="swidth" type="text" id="swidth" value="<?=(intval($site_property['gallerySWidth'.$cat])==0)?'200':$site_property['gallerySWidth'.$cat]?>" size="5"> 
        px,&nbsp;&nbsp;высота 
        <input name="sheight" type="text" id="sheight" value="<?=(intval($site_property['gallerySHeight'.$cat])==0)?'200':$site_property['gallerySHeight'.$cat]?>" size="5">
        px</td>
    </tr>
	<tr valign="baseline">
      <td align="right" nowrap bgcolor="#E0E0E0">Макс. размеры большой картинки : </td>
      <td valign="baseline" bgcolor="#E0E0E0"> ширина: 
        <input name="bwidth" type="text" id="bwidth" value="<?=(intval($site_property['galleryBWidth'.$cat])==0)?'1000':$site_property['galleryBWidth'.$cat]?>" size="5"> 
        px,&nbsp;&nbsp;высота 
        <input name="bheight" type="text" id="bheight" value="<?=(intval($site_property['galleryBHeight'.$cat])==0)?'850':$site_property['galleryBHeight'.$cat]?>" size="5"> 
        px</td>
    </tr>
    <tr valign="baseline"> 
      <td align="right" valign="top" nowrap bgcolor="#E0E0E0">Текст под картинкой:</td>
      <td bgcolor="#E0E0E0"><textarea name="text" cols="50" rows="5"></textarea><br>
      <input type="button" onClick="MM_openBrWindow('newseditor.php?field=text&form=form2','newcateditor','','785','625','true')" src="img/code.gif" value="HTML-редактор" class="but"></td>
    </tr>
	<tr valign="baseline">
      <td align="right" valign="top" nowrap bgcolor="#E0E0E0">Автор: </td>
      <td bgcolor="#E0E0E0"><input name="author" type="text" id="author" size="50"></td>
    </tr>
    <tr valign="baseline">
      <td align="right" valign="top" nowrap bgcolor="#E0E0E0">Дата добавления: </td>
      <td bgcolor="#E0E0E0"><input name="date_add" type="text" id="date_add" value="<?=date('Y-m-d')?>" size="11"></td>
    </tr>
	
	<tr valign="baseline">
      <td align="right" valign="top" nowrap bgcolor="#E0E0E0">Рейтинг: </td>
      <td bgcolor="#E0E0E0"><input name="raiting" type="text" id="raiting" size="6"></td>
    </tr>
	<tr valign="baseline">
      <td align="right" valign="top" nowrap bgcolor="#E0E0E0">Порядковый номер: </td>
      <td bgcolor="#E0E0E0"><input name="sort" type="text" id="sort" size="4"></td>
    </tr>
	<tr valign="baseline">
      <td align="right" valign="top" nowrap bgcolor="#E0E0E0">Разрешить комментирование: </td>
      <td bgcolor="#E0E0E0"><input name="in_comments" type="checkbox" id="in_comments" value="1"></td>
    </tr>
	<tr valign="baseline">
      <td align="right" valign="top" nowrap bgcolor="#E0E0E0">Разрешить рейтингование : </td>
      <td bgcolor="#E0E0E0"><input name="in_rait" type="checkbox" id="in_rait" value="1"></td>
    </tr>
    <tr align="center" valign="baseline"> 
      <td colspan="2" nowrap> <input type="hidden" name="bigh" value="" > <input type="hidden" name="bigw" value=""> 
        &nbsp; <input name="Submit1" type="submit" id="Submit1" value="Добавить" class="but"></td>
    </tr>
  </table>
  <input type="hidden" name="MM_insert" value="form2">
</form>
</div>

<div id="gall_multi" class="switch_panel" style="display:<?=($k['kod']=='gallery')?'block':'none'?>"><br /><br />
    <form method="post" name="gall_multi_form" id="gall_multi_form">
        <div style="width: 180px; height: 18px; border: solid 1px #7FAAFF; background-color: #C5D9FF; padding: 2px;">
            <span id="spanButtonPlaceholder"></span>
        </div>
        <hr />
        Текст ко всем фотографиям:<br />
        <textarea name="photoText" id="photoText" cols="40" rows="5"></textarea>
    </form>       
    <div id="divFileProgressContainer" style="height: 75px;"></div>
</div>

<div id="gall_options" class="switch_panel" style="display:none">
 <form method="post" name="gall_options">
 <div align="center" style="width:400px"><strong>Настройка галереи</strong> <br>
в альбоме &laquo;<?php echo $row_dbphotocat['name']; ?>&raquo;</div>
 <table align="center" class="el_tbl">
    <tr>
         <td>E-mail модератора :</td>
          <td><input name="modemail" type="text" id="modemail" size="20" value="<?=$site_property['modemail'.$cat]?>"></td>
        </tr>
        <tr>
          <td>Столбцов на страницу :</td>
          <td><input name="cols" type="text" id="cols" size="4" value="<?=$site_property['gallcols'.$cat]?>"></td>
        </tr>
        <tr>
          <td>Фотографий на страницу :</td>
          <td><input name="rows" type="text" id="rows" size="4" value="<?=$site_property['gallphotos'.$cat]?>"></td>
        </tr>
	<tr valign="baseline">
      <td valign="top" nowrap>Макс. размеры обложки альбома : </td>
      <td> ширина:
        <input name="swidthAlbum" type="text" id="swidthAlbum" value="<?=$site_property['galleryAlbumSWidth'.$cat]?>" size="5"> 
        px,&nbsp;&nbsp;высота: 
        <input name="sheightAlbum" type="text" id="sheightAlbum" value="<?=$site_property['galleryAlbumSHeight'.$cat]?>" size="5">
        px</td>
    </tr>
    <tr valign="baseline">
      <td valign="top" nowrap>Макс. размеры маленькой картинки : </td>
      <td> ширина:
        <input name="swidth" type="text" id="swidth" value="<?=$site_property['gallerySWidth'.$cat]?>" size="5"> 
        px,&nbsp;&nbsp;высота: 
        <input name="sheight" type="text" id="sheight" value="<?=$site_property['gallerySHeight'.$cat]?>" size="5">
        px</td>
    </tr>
	<tr valign="baseline">
      <td valign="top" nowrap>Макс. размеры большой картинки : </td>
      <td> ширина: 
        <input name="bwidth" type="text" id="bwidth" value="<?=$site_property['galleryBWidth'.$cat]?>" size="5"> 
        px,&nbsp;&nbsp;высота: 
        <input name="bheight" type="text" id="bheight" value="<?=$site_property['galleryBHeight'.$cat]?>" size="5"> 
        px</td>
    </tr>
		<tr>
          <td valign="top">Показывать большую картинку: </td>
          <td><p>
            <label for="showMode_0">
              <input type="radio" name="showMode" value="popup" id="showMode_0" <?=($site_property['gallShowMode'.$cat]=='popup')?'checked':''?>>
              Во всплывающем слое</label>
            <br>
            <label for="showMode_1">
              <input type="radio" name="showMode" value="page" id="showMode_1" <?=($site_property['gallShowMode'.$cat]=='page')?'checked':''?>>
              На той же странице</label>
            <br>
          </p></td>
		</tr>
        <tr>
          <td>Разрешить закачку пользователям </td>
          <td><input name="allowupload" type="checkbox" id="rows" value="Y" <?=($site_property['allowupload'.$cat]=='Y')?'checked':''?>></td>
        </tr>
        <tr>
          <td colspan="2" align="center"><input name="propc" type="hidden" id="propc" value="update">
            <input type="submit" name="Submit" value="Сохранить" class="but"></td>
          </tr>
      </table>
</form></div>
</div>
<p><a name="tabBottom"></a></p>

<?  }} ?>
<?php
mysqli_free_result($Recordset1);

mysqli_free_result($dbphotocat);

?>