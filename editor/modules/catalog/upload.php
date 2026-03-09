<?php
	/* Note: This thumbnail creation script requires the GD PHP Extension.  
		If GD is not installed correctly PHP does not render this page correctly
		and SWFUpload will get "stuck" never calling uploadSuccess or uploadError
	 */
require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php');
//error_reporting(E_ALL);
$requiredUserLevel = array(0, 1, 2);
include($_SERVER['DOCUMENT_ROOT'] . "/editor/secure/secure.php");
include $_SERVER['DOCUMENT_ROOT'].'/Connections/site_props.php';
$imgText='';
	// Get the session Id passed from SWFUpload. We have to do this to work-around the Flash Player Cookie Bug
	if (isset($_POST["PHPSESSID"])) {
		session_id($_POST["PHPSESSID"]);
	}

	@session_start();
	ini_set("html_errors", "0");
	$_SESSION['img_num']++;
	// Check the upload
	if (!isset($_FILES["Filedata"]) || !is_uploaded_file($_FILES["Filedata"]["tmp_name"]) || $_FILES["Filedata"]["error"] != 0) {
		echo "ERROR:invalid upload";
		exit(0);
	}

	// Get the image and create a thumbnail
	$img = imagecreatefromjpeg($_FILES["Filedata"]["tmp_name"]);
	if (!$img) {
		echo "ERROR:could not create image handle ". $_FILES["Filedata"]["tmp_name"];
		exit(0);
	}

	$width = imageSX($img);
	$height = imageSY($img);

	if (!$width || !$height) {
		echo "ERROR:Invalid width or height";
		exit(0);
	}
	$uniq_id=md5($_FILES["Filedata"]["tmp_name"] + rand()*100000);
	$file_id = '/images/small/thumb_'.$uniq_id.'.jpg';
	$big_file_id='big_'.$uniq_id.'.jpg';
	$orig_file_id='orig_'.$uniq_id.'.jpg';
	$tempDir=$_SERVER['DOCUMENT_ROOT'].'/images/temporary/';
	
	//Добавление новой картинки
	$err=$both=0;
	$errStr='';
	$_POST['swidth']=120;//(strlen($_POST['swidth'])>0)?intval($_POST['swidth']):$site_property['gallerySWidth'.$cat];
	$_POST['sheight']=88;//(strlen($_POST['sheight'])>0)?intval($_POST['sheight']):$site_property['gallerySHeight'.$cat];
	$_POST['bwidth']=800;//(strlen($_POST['bwidth'])>0)?intval($_POST['bwidth']):$site_property['galleryBWidth'.$cat];
	$_POST['bheight']=800;//(strlen($_POST['bheight'])>0)?intval($_POST['bheight']):$site_property['galleryBHeight'.$cat];
	$_POST['gwidth']=1500;//(strlen($_POST['bwidth'])>0)?intval($_POST['bwidth']):$site_property['galleryBWidth'.$cat];
	$_POST['gheight']=1500;//(strlen($_POST['bheight'])>0)?intval($_POST['bheight']):$site_property['galleryBHeight'.$cat];

		if(!is_dir($tempDir))mkdir($tempDir, 0777);
		copy($_FILES["Filedata"]["tmp_name"], $tempDir.$big_file_id);
		chmod($tempDir.$big_file_id, 0777);
		
		if(el_resize_images($tempDir.$big_file_id, $big_file_id, $_POST['bwidth'], $_POST['bheight'], 'gallery/')){
			@chmod($_SERVER['DOCUMENT_ROOT'].'/images',0777);
			@chmod($_SERVER['DOCUMENT_ROOT'].'/images/small',0777);
			@chmod($_SERVER['DOCUMENT_ROOT'].'/images/gallery',0777);
			$both=1;
		}else{
			$errStr.="ERROR:Не удалось закачать большую картинку.\\nПроверьте права доступа у папки images/gallery\\n";
			$err++;
		}
		if(el_resize_images($tempDir.$big_file_id, $orig_file_id, $_POST['gwidth'], $_POST['gheight'], 'gallery/')){
			$both=1;
		}else{
			$errStr.="ERROR:Не удалось закачать большую картинку.\\nПроверьте права доступа у папки images/gallery\\n";
			$err++;
		}
		/*if(el_resize_images($tempDir.$big_file_id, 'mid_'.$uniq_id.'.jpg', 110, 80, 'gallery/')){
			$both=1;
		}else{
			$errStr.="ERROR:Не удалось закачать среднюю картинку.\\nПроверьте права доступа у папки images/gallery\\n";
			$err++;
		}*/
		unlink($tempDir.$big_file_id);
	
	if($err==0){ 
  		/*$imgText=iconv('UTF-8', 'Windows-1251', nl2br($_POST['text']));
		el_imageLogo('/images/gallery/'.$big_file_id, '/images/logo_small.png', 'bottom-right');
		$insertSQL = sprintf("INSERT INTO photo (`path`, text, smallh, smallw, bigh, bigw, caption, smallpath, author, date_add, sort, in_comments, in_rait) 
		VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString('/images/gallery/'.$big_file_id, "text"),
					   GetSQLValueString($imgText, "text"),
					   GetSQLValueString($_POST['sheight'], "int"),
					   GetSQLValueString($_POST['swidth'], "int"),
					   GetSQLValueString($_POST['bheight'], "int"),
					   GetSQLValueString($_POST['bwidth'], "int"),
                       GetSQLValueString($_POST['cat'], "int"),
                       GetSQLValueString($file_id, "text"),
					   GetSQLValueString($_POST['author'], "text"),
					   GetSQLValueString(date('Y-m-d'), "date"),
					   GetSQLValueString($_SESSION['img_num'], "int"),
					   GetSQLValueString(($_POST['in_comments']=='1')?1:0, "int"),
					   GetSQLValueString(($_POST['in_rait']=='1')?1:0, "int"));

		  
		  $Result1=el_dbselect($insertSQL, 0, $Result1);
		  $lastId=mysqli_insert_id()($dbconn);
		 el_clearCache('catalogs');*/
	}else{
		echo 'ERROR:'.$errStr;
	}

	// Build the thumbnail
	$target_width = $_POST['swidth'];
	$target_height = $_POST['sheight'];
	$target_ratio = $target_width / $target_height;

	$img_ratio = $width / $height;

	if ($target_ratio > $img_ratio) {
		$new_height = $target_height;
		$new_width = $img_ratio * $target_height;
	} else {
		$new_height = $target_width / $img_ratio;
		$new_width = $target_width;
	}

	/*if ($new_height > $target_height) {
		$new_height = $target_height;
	}
	if ($new_width > $target_width) {
		$new_height = $target_width;
	}*/

	$new_img = imagecreatetruecolor($new_width, $new_height);
	if (!@imagefilledrectangle($new_img, 0, 0, $target_width-1, $target_height-1, imagecolorallocate($new_img, 255, 255, 255))) {	// Fill the image black
		echo "ERROR:Could not fill new image";
		exit(0);
	}

	if (!@imagecopyresampled($new_img, $img, ($target_width-$new_width)/2, /*($target_height-$new_height)/2*/0, 0, 0, $new_width, $new_height, $width, $height)) {
		echo "ERROR:Could not resize image";
		exit(0);
	}

	if (!isset($_SESSION["file_info"])) {
		$_SESSION["file_info"] = array();
	}

	// Use a output buffering to load the image into a variable
	ob_start();
	imagejpeg($new_img, $_SERVER['DOCUMENT_ROOT'].$file_id, 100);
	$imagevariable = ob_get_contents();
	ob_end_clean();
	
	//$file_id = md5($_FILES["Filedata"]["tmp_name"] + rand()*100000);
	
	//$_SESSION["file_info"][$file_id] = $imagevariable;
	
	
	$_SESSION["file_info"][$file_id] = $file_id;//$imagevariable;
	$_SESSION["file_info"][$last_id] = $lastId;
	echo "FILEID:" . $lastId.'|'.$file_id.'|'.$imgText;	// Return the file id to the script
	
?>