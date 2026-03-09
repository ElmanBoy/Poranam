<?php 
require_once('../../../Connections/dbconn.php'); 
$dbconn=el_dbconnect();
$database_dbconn=el_database();

//error_reporting(E_ALL);
$requiredUserLevel = array(1, 2); 
include($_SERVER['DOCUMENT_ROOT']."/editor/secure/secure.php");
 
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "edititem")) {

// Смотрим структуру каталога
;
$query_cat_form = "SELECT * FROM catalog_prop WHERE catalog_id='".$_GET['catalog_id']."' ORDER BY sort";
$cat_form = el_dbselect($query_cat_form, 0, $cat_form, 'result', true);
$row_cat_form = el_dbfetch($cat_form);
$totalRows_cat_form = mysqli_num_rows($cat_form);

;
$query_cat_form1 = "SELECT * FROM catalogs WHERE catalog_id='".$_GET['catalog_id']."'";
$cat_form1 = el_dbselect($query_cat_form1, 0, $cat_form1, 'result', true);
$row_cat_form1 = el_dbfetch($cat_form1);

$download_path=str_replace('//', '/', $row_cat_form1['down_files']);
if(strlen($download_path)>0){
	if(!is_dir($download_path)){
		mkdir($_SERVER['DOCUMENT_ROOT'].$download_path, 0777);
	}
}else{
	$download_path='/files';
}

  
  $query_field="";
  $fflag=1;
	do{
	$field_number="field".$row_cat_form['field'];
		switch ($row_cat_form['type']){
		case "textarea": $type="LONGTEXT";
		break;
		case "checkbox": 
		case "radio": $type="TEXT";
		break;
		case "optionlist":
		case "option":
		case "select":
		case "depend_list":
		case "list_fromdb": $type="LONGTEXT";
		break;
		case "calendar": $type="DATE";
		break;
		case "price": $type="DOUBLE"; $_POST[$field_number]=trim(str_replace(" ","",$_POST[$field_number])); $_POST[$field_number]=str_replace(",",".",$_POST[$field_number]); $_POST[$field_number]=sprintf("%01.2f", $_POST[$field_number]);
		break;
		case "small_image": $type="TEXT";
		if(!empty($_FILES[$field_number]['name'])){
			if(el_resize_images($_FILES[$field_number]['tmp_name'], 
			el_translit($_FILES[$field_number]['name']), $row_cat_form1['small_size'], $row_cat_form1['small_size'], $temp_name.'small_')){
				$_POST[$field_number]="/images/".el_translit($temp_name.'small_'.$_FILES[$field_number]['name']);
			}else{
				echo "<script>alert('Файл для предпросмотра с названием \"".$_FILES[$field_number]['name']."\" не удалось закачать!')</script>";
			}
		}else{
			$_POST[$field_number]=$_POST[$field_number."hidden"];
		}
		break;
		case "big_image": $type="TEXT"; 
		if(!empty($_FILES[$field_number]['name'])){ 
			$tempDir=$_SERVER['DOCUMENT_ROOT'].'/images/temporary/';
			$targetFileName=el_translit($_FILES[$field_number]['name']);
			if(!is_dir($tempDir))mkdir($tempDir, 0777);
			copy($_FILES[$field_number]['tmp_name'], $tempDir.$targetFileName);
			
			if(el_resize_images($tempDir.$targetFileName, $targetFileName, $row_cat_form1['big_size'], $row_cat_form1['big_size'], $temp_name)){
				$_POST[$field_number]="/images/".el_translit($temp_name.$_FILES[$field_number]['name']);
				el_imageLogo($_POST[$field_number], '/images/copyright.png', 'bottom-right');
				unlink($tempDir.$targetFileName);
			}else{
				echo "<script>alert('Файл с названием \"".$_FILES[$field_number]['name']."\" не удалось закачать!')</script>";
			}
		}else{
			$_POST[$field_number]=$_POST[$field_number."hidden"];
		}
		
		break;
		
		case "file": 
		$type="TEXT";
		if(strlen($_FILES[$field_number]['name'])>0){
			if(file_exists($_SERVER['DOCUMENT_ROOT']."/files/".$_FILES[$field_number]['name'])){
				echo "<script>alert('Файл с названием \"".$_FILES[$field_number]['name']."\" уже есть!')</script>";
				$fflag=0;
			}else{
				if(!move_uploaded_file($_FILES[$field_number]['tmp_name'], $_SERVER['DOCUMENT_ROOT']."/files/".$_FILES[$field_number]['name'])){
					echo "<script>alert('Не удалось закачать файл \"".$_FILES[$field_number]['name']."\"!\\nВозможно, не настроен доступ к папке \"files\".')</script>";
					$fflag=0;
				}else{
					$_POST[$field_number]=$_FILES[$field_number]['name'];
				}
			}
		}else{
			$_POST[$field_number]=$_POST[$field_number."f"];
		}
		break;
		
		case "hidden_file":
		chmod($_SERVER['DOCUMENT_ROOT']."/files", 0777);
		chmod($_SERVER['DOCUMENT_ROOT']."/files/secure", 0777);
		if(!is_dir($_SERVER['DOCUMENT_ROOT']."/files/secure")){
			mkdir($_SERVER['DOCUMENT_ROOT']."/files/secure", 0777);
			copy($_SERVER['DOCUMENT_ROOT']."/modules/.htaccess", $_SERVER['DOCUMENT_ROOT']."/files/secure/.htaccess");
		}
		if(strlen($_FILES[$field_number]['name'])>0){
			if(file_exists($_SERVER['DOCUMENT_ROOT']."/files/secure/".$_FILES[$field_number]['name'])){
				echo "<script>alert('Файл с названием \"".$_FILES[$field_number]['name']."\" уже есть!')</script>";
			}else{
				if(!move_uploaded_file($_FILES[$field_number]['tmp_name'], $_SERVER['DOCUMENT_ROOT']."/files/secure/".$_FILES[$field_number]['name'])){
					echo "<script>alert('Не удалось закачать файл ".$_FILES[$field_number]['name']."!')</script>";
					$fflag=0;
				}else{
					$_POST[$field_number]=$_FILES[$field_number]['name'];
				}
			}
		}else{
			$_POST[$field_number]=$_POST[$field_number."f"];
		}
		chmod($_SERVER['DOCUMENT_ROOT']."/files/", 0755);
		chmod($_SERVER['DOCUMENT_ROOT']."/files/secure", 0755);
		$type="TEXT";
		break;
		
		case "secure_file":
		function secure_file_upload(){
		global $_POST, $_FILES, $type, $field_number, $fflag;
		chmod($_SERVER['DOCUMENT_ROOT']."/files", 0777);
		chmod($_SERVER['DOCUMENT_ROOT']."/files/secure", 0777);
		$file_ext=strrchr($_FILES[$field_number]['name'], '.'); 
		$newfilename=el_genpass(20).$file_ext;
		if(!is_dir($_SERVER['DOCUMENT_ROOT']."/files/secure")){
			mkdir($_SERVER['DOCUMENT_ROOT']."/files/secure", 0777);
			copy($_SERVER['DOCUMENT_ROOT']."/modules/.htaccess", $_SERVER['DOCUMENT_ROOT']."/files/secure/.htaccess");
		}
		if(strlen($_FILES[$field_number]['name'])>0){
			if(file_exists($_SERVER['DOCUMENT_ROOT']."/files/secure/".$newfilename)){
				secure_file_upload();
			}else{
				if(!move_uploaded_file($_FILES[$field_number]['tmp_name'], $_SERVER['DOCUMENT_ROOT']."/files/secure/".$newfilename)){
					echo "<script>alert('Не удалось закачать файл ".$_FILES[$field_number]['name']."!')</script>";
					$fflag=0;
				}else{
					$_POST[$field_number]=$newfilename;
				}
			}
		}else{
			$_POST[$field_number]=$_POST[$field_number."f"];
		}
		return $_POST[$field_number];
		chmod($_SERVER['DOCUMENT_ROOT']."/files/", 0755);
		chmod($_SERVER['DOCUMENT_ROOT']."/files/secure", 0755);
		}
		
		$type="TEXT";
		$_POST[$field_number]=secure_file_upload();
		break;
		
		default: $type="TEXT"; 
		break;
		}
		if($row_cat_form['type']=='optionlist' || $row_cat_form['type']=='list_fromdb' || $row_cat_form['type']=='option' || $row_cat_form['type']=='depend_list'){
			$arop=(is_array($_POST[$field_number]) && count($_POST[$field_number])>0)?@implode(';',$_POST[$field_number]):$_POST[$field_number];
			$post_field="'".GetSQLValueString($arop, $type)."', ";
			$query_field.=$field_number."=".$post_field;
		}else{
			$post_field="'".GetSQLValueString($_POST[$field_number], $type)."', ";
			$query_field.=$field_number."=".$post_field;
		}
		
	}while($row_cat_form = el_dbfetch($cat_form));

	$cat_post=(count($_POST['cats'])>0)?' '.implode(' , ', $_POST['cats']).' ':intval($_POST['cats']);
	$active_post=GetSQLValueString(isset($_POST['active']) ? "true" : "", "defined","'1'","'0'");
	$sort_post=GetSQLValueString($_POST['sort'], "int");
	$goodid_post=GetSQLValueString($_POST['goodid'], "int");
	if($fflag!=0){	
	 $updateSQL = "UPDATE catalog_".$_GET['catalog_id']."_data SET cat='".$cat_post."', ".$query_field." active=".$active_post.", sort='".$sort_post."', goodid='".$goodid_post."'  WHERE id=".$_GET['id'];
	  ;
	  $Result1 = el_dbselect($updateSQL, 0, $Result1, 'result', true);
	  $page_name=el_dbselect("SELECT name FROM cat WHERE path='".$_GET['path']."'", 0, $page_name, 'row');
	  el_log('Изменение записи &laquo;'.$_POST['field1'].'&raquo; в каталоге в разделе &laquo;'.$page_name['name'].'&raquo;', 2);
	  el_clearcache('catalogs');
	  el_clearcache('tags');
	  el_genSiteMap();
	  echo "<script>alert('Изменения сохранены!')</script>";
	  }
}

$colname_detail = "-1";
if (isset($_GET['id'])) {
  $colname_detail = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
;
$query_detail = sprintf("SELECT * FROM catalog_".$_GET['catalog_id']."_data WHERE id = %s", $colname_detail);
$detail = el_dbselect($query_detail, 0, $detail, 'result', true);
$row_detail = el_dbfetch($detail);
$totalRows_detail = mysqli_num_rows($detail);

$uPars=explode('&', $_SERVER['QUERY_STRING']);
$currId=array_shift($uPars);
$urlParams=htmlentities(implode('&', $uPars));
$prevId=el_dbselect("SELECT id FROM catalog_".$_GET['catalog_id']."_data WHERE id < $colname_detail ORDER BY id DESC LIMIT 0,1", 0, $prevId, 'row');
$nextId=el_dbselect("SELECT id FROM catalog_".$_GET['catalog_id']."_data WHERE id > $colname_detail ORDER BY id ASC LIMIT 0,1", 0, $nextId, 'row');
?>
<html>
<head>
<title>Редактирование записи каталога</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="/editor/style.css" rel="stylesheet" type="text/css">
<style type="text/css">
.style1 {color: #009900}
.style2 {color: #FF0000}
.paging, .paging td{border-width:0px;}
</style>
<script language="JavaScript" type="text/JavaScript">
<!--

function opclose(id){
if (document.getElementById(id).style.display=="none"){
document.cookie = "idshow["+id+"]=Y; expires=Thu, 31 Dec 2020 23:59:59 GMT; path=/editor/;";
document.getElementById(id).style.display="block";
document.getElementById(id+"_button").value="Скрыть дополнительные цены"}else{
document.cookie = "idshow["+id+"]=N; expires=Thu, 31 Dec 2020 23:59:59 GMT; path=/editor/;";
document.getElementById(id).style.display="none";
document.getElementById(id+"_button").value="Показать дополнительные цены"};
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

function delimg(im, name){
var OK=confirm("Вы действительно хотите удалить на сервере файл \""+name.replace("/images/","")+"\" ?");
if(OK){
	document.deform.imname.value=name;
	document.deform.field.value=im;
	document.deform.submit();
}
}

//-->
</SCRIPT>
<link href="/js/css/start/jquery.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/jquery-ui.js"></script>
<!--script type="text/javascript" src="/js/scripts.js"></script-->
<script type="text/javascript" src="/js/ui.datepicker-ru.js"></script>
<script type="text/javascript" src="/editor/e_modules/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="/editor/e_modules/ckfinder/ckfinder.js"></script>
<link href="/js/swfu/css/default.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/js/swfu/swfupload.js"></script>
<script type="text/javascript" src="/js/swfu/handlers.js"></script>
<script language="javascript">
function getDependList(obj, parent_catalog, child_catalog, target_field, parent_field, child_field, curr_value){
	$('#field'+target_field).after('<span id="preload"><img src="/images/loading.gif" align=absmiddle>&nbsp; Пожалуйста, подождите...</span>');
	$.post('/editor/modules/catalog/getDependList.php', 
		  {'parent_catalog':parent_catalog, 'child_catalog':child_catalog, 'val':$(obj).val(),
		  'target_field':target_field, 'parent_field':parent_field, 'child_field':child_field, 'curr_value':curr_value}, 
		  function(data){/*alert(data);*/$('#field'+target_field).html(data); $('#preload').remove()}
	);
}

function getModels(obj, curr_val){
	$('#field11').after('<span id="preload"><img src="/images/loading.gif" align=absmiddle></span>');
	$.post('/editor/modules/catalog/getModels.php', 
		  {'val':$(obj).val(), 'curr_value':curr_val}, 
		  function(data){/*alert(data);*/$('#field11').html('');$('#field11').html(data); $('#preload').remove()}
	);
}

function getModif(obj, curr_val){
	$('#field54').after('<span id="preload"><img src="/images/loading.gif" align=absmiddle></span>');
	$.post('/editor/modules/catalog/getModif.php', 
		  {'val':$(obj).val(), 'curr_value':curr_val}, 
		  function(data){/*alert(data);*/$('#preload, #modif_mess').remove();$('#field54').html('');if(data.length>0){$('#field54').html(data);}else{$('#field54').after('<span id="modif_mess">Модификаций нет</span>')} }
	);
}

var swfu;
</script>
</head>

<body>
<form name="deform" method="post"><input type="hidden" name="imname"><input type="hidden" name="field"></form>
<form method="POST" action="<?php echo $editFormAction; ?>" name="edititem" ENCTYPE="multipart/form-data">
  <table width="98%" align="center" cellpadding="3" cellspacing="0" class="el_tbl">
    <tr>
      <td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="4" class="paging">
        <tr>
          <td width="20%">
          <? if(strlen($prevId['id'])>0){ ?>
          <input type="button" name="button" id="button" value="  &laquo;  " class="but" title="Предыдущая запись" onClick="location.href='<?=$_SERVER['SCRIPT_NAME'].'?id='.$prevId['id'].'&'.$urlParams?>'">
           <? }?>          </td>
          <td width="60%" align="center"><b>Редактирование записи #<?=$colname_detail?></b></td>
          <td width="20%" align="right">
          <? if(strlen($nextId['id'])>0){ ?>
          <input type="button" name="button" id="button" value="  &raquo;  " class="but" title="Следующая запись" onClick="location.href='<?=$_SERVER['SCRIPT_NAME'].'?id='.$nextId['id'].'&'.$urlParams?>'">
          <? }?>
          </td>
        </tr>
      </table>        </td>
    </tr>
    <tr>
      <td align="right" valign="top">Номер:</td>
      <td><input name="sort" type="text" id="sort" value="<?php echo $row_detail['sort']; ?>" size="3">
          <? /*input name="cat" type="hidden" id="cat" value="<?php echo $row_detail['cat']; ?>"*/?>
      <input name="id" type="hidden" id="id" value="<?php echo $row_detail['id']; ?>"></td>
    </tr>
	 <tr>
    <td align="right" valign="top">Артикул:</td>
    <td><input name="goodid" type="text" id="goodid" value="<?php echo $row_detail['goodid']; ?>" size="3"></td>
  </tr>
  <tr>
      <td align="right" valign="top" nowrap>Разместить в разделе: </td>
      <td>
      <?
	  $curr=el_dbselect("SELECT cat, title FROM content WHERE kod='catalog".$_GET['catalog_id']."'", 0, $currCat);
	  $currCat=el_dbfetch($curr);
	  if(substr_count($row_detail['cat'], ' , ')>0){
		  $good_cats=explode(',', $row_detail['cat']);
		  while(list($key, $val)=each($good_cats)){$good_cats[$key]=trim($val);}
	  }else{
		  $row_detail['cat'];
	  }
	  ?>
      <div id="cat" style="height:150px; overflow:auto">
      <? do{ 
			if(is_array($good_cats)){
				$sel=(in_array($currCat['cat'], $good_cats, false))?' checked':'';
			}else{
		  		$sel=(trim($row_detail['cat'])==$currCat['cat'])?' checked':'';
			}
		  ?>
          <label for="cat<?=$currCat['cat']?>">
		  <input type="checkbox" name="cats[]" id="cat<?=$currCat['cat']?>" value="<?=$currCat['cat']?>"<?=$sel?>> <?=$currCat['title']?></label><br>
		  <?
	  }while($currCat=el_dbfetch($curr));
	  ?>
      </div>
      </td>
    </tr>

	<?

if(isset($_POST['imname']) && strlen($_POST['imname'])>0){
	if(!@unlink($_SERVER['DOCUMENT_ROOT'].$_POST['imname'])){
		echo '<script>alert("Не удалось удалить файл \"'.str_replace('/images/','',$_POST['imname']).'\"!\\nВидимо, файла уже нет на сервере.");</script>';
	}else{
		echo '<script>alert("Файл \"'.str_replace('/images/','',$_POST['imname']).'\" удален!");
		document.edititem.'.$_POST['field'].'hidden.value="";
		</script>';
	}
}

	
;
$query_cat_form = "SELECT * FROM catalog_prop WHERE catalog_id='".$_GET['catalog_id']."' ORDER BY sort";
$cat_form = el_dbselect($query_cat_form, 0, $cat_form, 'result', true);
$row_cat_form = el_dbfetch($cat_form);
$totalRows_cat_form = mysqli_num_rows($cat_form);
  
if($totalRows_cat_form>0){

;
$query_cat_form1 = "SELECT * FROM catalogs WHERE catalog_id='".$_GET['catalog_id']."'";
$cat_form1 = el_dbselect($query_cat_form1, 0, $cat_form1, 'result', true);
$row_cat_form1 = el_dbfetch($cat_form1);
$depend_fields=array();
do{
$field_num="field".$row_cat_form['field']; 
$prop=$output=$script_line='';

switch ($row_cat_form['type']){
	case "text": $input="input"; $prop=" value='".$row_detail[$field_num]."'>";
	$output=""; $script_line="";
	break;
	case "textarea": $input="textarea"; $prop="cols=".$row_cat_form['cols']." rows=".$row_cat_form['rows'].">".$row_detail[$field_num];
	$output="</textarea><br>      
      <input name='Button' type='button' onClick=\"MM_openBrWindow('/editor/newseditor.php?field=".$field_num."&form=edititem','editor','resizable=yes','750','640','true','description','new')\" value='Визуальный редактор' class='but'>"; $script_line="";
	break;
	case "select": $input="textarea"; $prop="cols=30 rows=5>".$row_detail[$field_num];
	$output="</textarea><br>Здесь вписываются строки списка через точку с запятой ';'"; $script_line="";
	break;
	
	case "option":
	$item=explode(";", $row_cat_form['options']);
	$sitem=explode(";", $row_detail[$field_num]); 
	$opt=""; 
	for($i=0; $i<count($item); $i++){
		if(in_array($item[$i], $sitem)){
			$opt.="<option value='".$item[$i]."' selected>".$item[$i]."</option>\n";
		}else{
			$opt.="<option value='".$item[$i]."'>".$item[$i]."</option>\n";
		}
	}
	$output="<select name='".$field_num."' id='".$field_num."'".((strlen($row_cat_form['size'])>0)?" size=".$row_cat_form['size']." multiple":'').">\n<option></option>\n".$opt."</select>"; 
	break;
	
	case "list_fromdb": 
	$list_field=el_dbselect("select field".$row_cat_form['from_field']." from catalog_".$row_cat_form['listdb']."_data ORDER BY sort ASC", 0, $list_field/*, 'result', true, true*/);
	$row_list_field=@el_dbfetch($list_field);
	$itemlist='';
	$sitemlist=explode(";", $row_detail[$field_num]); 
	do{
		if(in_array($row_list_field["field".$row_cat_form['from_field']], $sitemlist)){
			$ch="selected";
		}else{
			$ch="";
		}
		$itemlist.="<option $ch value='".$row_list_field["field".$row_cat_form['from_field']]."'>".$row_list_field["field".$row_cat_form['from_field']]."</option>\n";
	}while($row_list_field=@el_dbfetch($list_field));
	$output="<select name='".$field_num."[]' id='".$field_num."'".((strlen($row_cat_form['size'])>0)?" size=".$row_cat_form['size']." multiple":'').">\n<option></option>\n".$itemlist."</select>"; 
	break;
	
	case "optionlist":
	$item=explode(";", $row_cat_form['options']);
	$sitem=explode(";", $row_detail[$field_num]); 
	$opt=""; 
	for($i=0; $i<count($item); $i++){
		/*if(in_array($item[$i], $sitem)){
			$opt.="<option value='".$item[$i]."' selected>".$item[$i]."</option>\n";
		}else{
			$opt.="<option value='".$item[$i]."'>".$item[$i]."</option>\n";
		}*/
		$ch=(in_array($item[$i], $sitem))?' checked="checked"':'';
		$items[]='<label for="opt'.$i.'">
		  <input type="checkbox" name="field'.$row_cat_form['field'].'[]" id="opt'.$i.'" value="'.$item[$i].'"'.$ch.'> '.$item[$i].'</label><br>';
	}
	$output="<div style='height:".((strlen($row_cat_form['size'])>0)?17 * $row_cat_form['size']."px; overflow:auto'":'100px').">\n".implode("\n", $items)."</div>"; 
	break;
	case "checkbox": $input="input"; if($row_detail[$field_num]==$row_cat_form['name']){$prop="checked value='".$row_cat_form['name']."'>";}else{$prop=" value='".$row_cat_form['name']."'>";};
	$output=""; $script_line="";
	break;
	
	case "depend_list":
	$itemlist='';
	$sitemlist=explode(";", $row_detail[$field_num]);
	$list_field=el_dbselect("select field".$row_cat_form['from_field']." from catalog_".$row_cat_form['listdb']."_data ORDER BY field".$row_cat_form['from_field']." ASC", 0, $list_field);
	$row_list_field=el_dbfetch($list_field);
	$sitemlist=explode(";", $row_detail[$field_num]); 
	do{
		$ch=(in_array($row_list_field["field".$row_cat_form['from_field']], $sitemlist))?" selected":'';
		$itemlist.="<option value='".$row_list_field["field".$row_cat_form['from_field']]."'".$ch.">".$row_list_field["field".$row_cat_form['from_field']]."</option>\n";
	}while($row_list_field=el_dbfetch($list_field));
	$output="<select name='field".$row_cat_form['field']."' id='field".$row_cat_form['field']."'".((strlen($row_cat_form['size'])>0)?" size='".$row_cat_form['size']."' multiple":'')." onchange='getDependList(this, \"".$row_cat_form['listdb']."\", \"".$row_cat_form['options']."\", \"".$row_cat_form['default_value']."\", \"".$row_cat_form['from_field']."\", \"".$row_cat_form['to_field']."\", \"".$row_detail["field".$row_cat_form['default_value']]."\")'>\n<option></option>\n".$itemlist."</select>";
	$depend_fields[]= $row_cat_form['field'];
	break;
	
	case "marks":
	$list_field=el_dbselect("select field1 from catalog_marks_data ORDER BY field1 ASC", 0, $list_field);
	$row_list_field=el_dbfetch($list_field);
	$itemlist='';
	$mark=$row_detail[$field_num];
	do{
		$sel=($row_detail[$field_num]==$row_list_field["field".$row_cat_form['from_field']])?' selected':'';
		$itemlist.="<option value='".$row_list_field["field".$row_cat_form['from_field']]."'$sel>".$row_list_field["field".$row_cat_form['from_field']]."</option>\n";
	}while($row_list_field=el_dbfetch($list_field));
	$output="<select name='field".$row_cat_form['field']."' id='field".$row_cat_form['field']."'".((strlen($row_cat_form['size'])>0)?" size=".$row_cat_form['size']." multiple":'')." onchange='getModels(this)'>\n<option></option>\n".$itemlist."</select>"; 
	break;
	
	case "models":
	$list_field=el_dbselect("select field1 from catalog_marks_data ORDER BY field1 ASC", 0, $list_field);
	$row_list_field=el_dbfetch($list_field);
	$itemlist='';
	$model=$row_detail[$field_num];
	do{
		$sel=($row_detail[$field_num]==$row_list_field["field".$row_cat_form['from_field']])?' selected':'';
		$itemlist.="<option value='".$row_list_field["field".$row_cat_form['from_field']]."'$sel>".$row_list_field["field".$row_cat_form['from_field']]."</option>\n";
	}while($row_list_field=el_dbfetch($list_field));
	$output="<select name='field".$row_cat_form['field']."' id='field".$row_cat_form['field']."'".((strlen($row_cat_form['size'])>0)?" size=".$row_cat_form['size']." multiple":'')." onchange='getModif(this, \"".$row_detail[$field_num]."\")'>\n<option></option>\n".$itemlist."</select><script>$('#field11').change();getModels($('#field1'), \"".$row_detail[$field_num]."\");</script>"; 
	break;
	
	case "modif":
	$output="<select name='field".$row_cat_form['field']."' id='field".$row_cat_form['field']."'".((strlen($row_cat_form['size'])>0)?" size=".$row_cat_form['size']." multiple":'').">\n<option></option>\n</select>"; 
	break;
	
	case "photo_album":
	$list_field=el_dbselect("select id, name from photo_albums WHERE type<>'video' ORDER BY date_create DESC, id DESC", 0, $list_field);
	$row_list_field=el_dbfetch($list_field);
	$itemlist='';
	do{
		$sel=($row_list_field['id']==$row_detail[$field_num])?' selected':'';
		$itemlist.="<option value='".$row_list_field['id']."'".$sel.">".$row_list_field['name']."</option>\n";
	}while($row_list_field=el_dbfetch($list_field));
	$output="<select name='field".$row_cat_form['field']."' id='field".$row_cat_form['field']."' size=".$row_cat_form['size'].">\n<option></option>\n".$itemlist."</select>"; 
	break;
	case "video_album":
	$list_field=el_dbselect("select id, name from photo_albums WHERE type='video' ORDER BY date_create DESC, id DESC", 0, $list_field);
	$row_list_field=el_dbfetch($list_field);
	$itemlist='';
	do{
		$sel=($row_list_field['id']==$row_detail[$field_num])?' selected':'';
		$itemlist.="<option value='".$row_list_field['id']."'".$sel.">".$row_list_field['name']."</option>\n";
	}while($row_list_field=el_dbfetch($list_field));
	$output="<select name='field".$row_cat_form['field']."' id='field".$row_cat_form['field']."' size=".$row_cat_form['size'].">\n<option></option>\n".$itemlist."</select>"; 
	break;
	
	case "radio": $input="input"; if($row_detail[$field_num]==$row_cat_form['name']){$prop="checked value='".$row_cat_form['name']."'>";}else{$prop=" value='".$row_cat_form['name']."'>";};
	$output=""; $script_line="";
	break;
	case "small_image": $input="input"; $prop=">";
	$output="<input type=hidden name='".$field_num."hidden' value='".$row_detail[$field_num]."'><br>Укажите местонахождение картинки для предпросмотра на Вашем компьютере для закачки на сервер если нужно сменить существующую"; $row_cat_form['type']="file"; $script_line='';
	if(is_file($_SERVER['DOCUMENT_ROOT'].$row_detail[$field_num])){$script_line="<img align=top id='".$field_num."img' src=".$row_detail[$field_num]." border=0><br><input type=button value='Удалить' onclick=\"document.edititem.".$field_num."hidden.value=''; document.getElementById('".$field_num."img').style.display='none';\" class=but>&nbsp;&nbsp;&nbsp;<input type=button class=but value='Удалить на сервере' onclick=\"document.edititem.".$field_num."hidden.value=''; delimg('".$field_num."', '".$row_detail[$field_num]."');\"><br><br>";}
	break;
	case "big_image": $input="input"; $prop=">";
	$output="<input type=hidden name='".$field_num."hidden' value='".$row_detail[$field_num]."'><br>Укажите местонахождение картинки на Вашем компьютере для закачки на сервер если нужно сменить существующую"; $row_cat_form['type']="file"; $script_line='';
	if(is_file($_SERVER['DOCUMENT_ROOT'].$row_detail[$field_num])){$script_line="<img align=top id='".$field_num."img' src=".$row_detail[$field_num]." border=0><br><input type=button value='Удалить' onclick=\"document.edititem.".$field_num."hidden.value=''; document.getElementById('".$field_num."img').style.display='none';\" class=but>&nbsp;&nbsp;&nbsp;<input type=button class=but value='Удалить на сервере' onclick=\"document.edititem.".$field_num."hidden.value=''; delimg('".$field_num."', '".$row_detail[$field_num]."');\"><br><br>";}
	break;
	
	case "multi_image": $input="input"; $prop="id='delThumb' value='".$row_detail[$field_num]."'>";
	$output="Укажите местонахождение картинкок на Вашем компьютере для закачки на сервер<div style='width: 200px; height: 18px; border: solid 1px #7FAAFF; background-color: #C5D9FF; padding: 2px;'><span id='spanButtonPlaceholder'></span></div><div id='divFileProgressContainer' style='height: 50px;'></div><input type='hidden' name='photoText' id='photoText' value=''><div id='albumsNew' style='margin:10px; width:550px'></div><input type='hidden' id='delThumb' name='field".$row_cat_form['field']."' value='".$row_detail[$field_num]."'>"; $row_cat_form['type']="multi_image";
	$iArr=explode(' , ', $row_detail[$field_num]);
	for($i=0; $i<count($iArr); $i++){
		$output.='<div style="float:left; width:105px; height:105px;" id="thumbE'.$i.'"><img src="'.$iArr[$i].'" title="'.$iArr[$i].'" border="0"><img title="Удалить" onclick="swf_delImg(\''.$iArr[$i].'\', \'thumbE'.$i.'\')" src="/images/components/ico_del.png" style="position:relative; top:-15px; left:85px; cursor:pointer"></div>';
	}
	break;
	
	case "file": $input="input"; $prop=">";
	$output="<br>Здесь указывается местонахождение файла на Вашем компьютере для закачки на сервер"; $script_line="<input type='hidden' value='".$row_detail[$field_num]."' name='field".$row_cat_form['field']."f'>Файл - <b>'".$row_detail[$field_num]."'</b><br>";
	break;
	case "file_list": $input="input"; 
	$output=el_createFileSelect($_SERVER['DOCUMENT_ROOT'].$site_property['down_path'.$_GET['cat']], $field_num, '', $row_detail[$field_num], 'file')."<br>Выберите файл из указанной в настройках раздела папки";
	break;
	case "file_list": $input="input"; 
	$output=el_createFileSelect($_SERVER['DOCUMENT_ROOT'].$site_property['down_path'.$_GET['cat']], $field_num, '', $row_detail[$field_num], 'file')."<br>Выберите файл из указанной в настройках раздела папки";
	break;
	case "secure_file": $input="input"; $prop=">";
	$output="<br>Здесь указывается местонахождение файла на Вашем компьютере для закачки на сервер.<br>Система даст новое нечитаемое название файлу и поместит в недоступное для посетителей сайта место."; $script_line="<input type='hidden' value='".$row_detail[$field_num]."' name='field".$row_cat_form['field']."f'>Файл - <b>'".$row_detail[$field_num]."'</b><br>";
	$row_cat_form['type']="file";
	break;
	
	case "hidden_file": $input="input"; $prop=">";
	$output="<br>Здесь указывается местонахождение файла на Вашем компьютере для закачки на сервер.<br>Система поместит файл в недоступное для посетителей сайта место."; $script_line="<input type='hidden' value='".$row_detail[$field_num]."' name='field".$row_cat_form['field']."f'>Файл - <b>'".$row_detail[$field_num]."'</b><br>";
	$row_cat_form['type']="file";
	break;
	
	case "calendar": $input="input"; $prop=" value='".$row_detail[$field_num]."'>"; $row_cat_form['type']="text";
	$output=" <script type=\"text/javascript\" src=\"/js/ui.datepicker-ru.js\"></script><script type=\"text/javascript\">$(function() {
		$.datepicker.setDefaults($.extend({showMonthAfterYear: false}, $.datepicker.regional['ru']));
		$(\"#field".$row_cat_form['field']."\").datepicker({showOn: 'button', buttonImage: '/editor/img/b_calendar.gif', buttonImageOnly: true, dateFormat: 'yy-mm-dd',	firstDay: 1, buttonText: 'Выберите дату'});
	});
	</script>";
	break;
	case "price": $input="input";  $prop=" value='".$row_detail[$field_num]."'>"; $script_line="";
	$output=" ".$row_cat_form1['currency']; 
	break;
	case "comments": $input="input"; if($row_detail[$field_num]==1){$prop="checked value='1' title='Включить/Выключить комментирование'>";}else{$prop=" value='1' title='Включить/Выключить комментирование'>";};
	$output=""; $script_line="";
	$row_cat_form['type']="checkbox";
	$output="<input name='Button' type='button' onClick=\"MM_openBrWindow('/editor/modules/catalog/commentsedit.php?pagepath=".$_GET['path']."/?id=".$_GET['id']."','editor','resizable=yes','700','640','true','description','new')\" value='Комментарии' class='but'>"; 
	break;
	case "price": $input="input"; $prop="";
	$output=" ".$row_cat_form1['currency']; 
	break;
	case 'full_html': $output="<textarea cols=".$row_cat_form['cols']." rows=".$row_cat_form['rows']." name='field".$row_cat_form['field']."'>".$row_detail[$field_num]."</textarea><script src='/editor/visual_editor.php?class=field".$row_cat_form['field']."&type=full&height=".($row_cat_form['rows']*30)."'></script>"; break; 
	case 'basic_html': $output="<textarea cols=".$row_cat_form['cols']." rows=".$row_cat_form['rows']." name='field".$row_cat_form['field']."'>".$row_detail[$field_num]."</textarea><script src='/editor/visual_editor.php?class=field".$row_cat_form['field']."&type=basic&height=".($row_cat_form['rows']*30)."'></script>"; break; 
	case "text":
	default:$input="input"; $prop=" value='".$row_detail[$field_num]."'>";
	$output=""; $script_line="";  $row_cat_form['type']='text';
	break;
}
	if($row_cat_form['type']=='option' || $row_cat_form['type']=='optionlist' || $row_cat_form['type']=='list_fromdb' || $row_cat_form['type']=='file_list' || $row_cat_form['type']=='full_html' || $row_cat_form['type']=='basic_html' || $row_cat_form['type']=='photo_album' || $row_cat_form['type']=='video_album' || $row_cat_form['type']=='multi_image' || $row_cat_form['type']=='depend_list' || $row_cat_form['type']=='marks' || $row_cat_form['type']=='models' || $row_cat_form['type']=='modif'){
		echo "<tr><td align='right' valign='top'>".$row_cat_form['name'].": </td><td>$output</td>"; 
	}else{
		echo "<tr><td align='right' valign='top'>".$row_cat_form['name'].": </td><td>$script_line<$input type='".$row_cat_form['type']."' name='field".$row_cat_form['field']."' id='field".$row_cat_form['field']."' size='".$row_cat_form['size']."' $prop $output</td>"; 
	}

}while($row_cat_form = el_dbfetch($cat_form));
}
?>	
    <tr>
      <td align="right" valign="top">Активный:</td>
      <td><input <?php if (!(strcmp($row_detail['active'],"1"))) {echo "checked";} ?> name="active" type="checkbox" id="active" value="checkbox"></td>
    </tr>
    <tr>
      <td colspan="2" align="center" valign="top">
      <input type="submit" name="Submit" value="Сохранить" class="but">
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <input type="button" name="Submit2" value="Закрыть" onClick="window.close()" class="but"></td>
    </tr>
    <input type="hidden" name="MM_update" value="edititem">
 </table>
 </form>

<?php 
mysqli_free_result($detail);
?>
<script language="javascript">
<?
if(count($depend_fields)>0){
	for($i=0; $i<count($depend_fields); $i++){
		echo '$("#field'.$depend_fields[$i].'").change();'."\n";
	}
}
?>
swfu = new SWFUpload({
		// Backend Settings
		upload_url: "/editor/modules/catalog/upload.php",
		post_params: {"PHPSESSID": "<?php echo session_id(); ?>", "cat":0, "in_comments":1, "in_rait":1},

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
		
		thumb_width: 100,
		thumb_height: 100,

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
			cat : 0,
			target_field: "delThumb"
		},
		
		// Debug Settings
		debug: false
	});
</script>
</body>
</html>