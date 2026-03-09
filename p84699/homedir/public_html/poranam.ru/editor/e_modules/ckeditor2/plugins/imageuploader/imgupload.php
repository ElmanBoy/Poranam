<?php

// Copyright (c) 2015, Fujana Solutions - Moritz Maleck. All rights reserved.
// For licensing, see LICENSE.md

session_start();

function el_translit($string, $type = '')
{
	$string = ($type == 'file') ? preg_replace('/\\.(?![^.]*$)/', '_', $string) : $string;
	$r_trans = array(
		"а", "б", "в", "г", "д", "е", "ё", "ж", "з", "и", "й", "к", "л", "м",
		"н", "о", "п", "р", "с", "т", "у", "ф", "х", "ц", "ч", "ш", "щ", "э",
		"ю", "я", "ъ", "ы", "ь", "А", "Б", "В", "Г", "Д", "Е", "Ё", "Ж", "З", "И", "Й", "К", "Л", "М",
		"Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ц", "Ч", "Ш", "Щ", "Э",
		"Ю", "Я", "Ъ", "Ы", "Ь", " ", " ", "(", ")", "'",
	);
	$e_trans = array(
		"a", "b", "v", "g", "d", "e", "e", "j", "z", "i", "i", "k", "l", "m",
		"n", "o", "p", "r", "s", "t", "u", "f", "h", "c", "ch", "sh", "sch",
		"e", "yu", "ya", "", "i", "", "a", "b", "v", "g", "d", "e", "e", "j", "z", "i", "i", "k", "l", "m",
		"n", "o", "p", "r", "s", "t", "u", "f", "h", "c", "ch", "sh", "sch",
		"e", "yu", "ya", "", "i", "", "-", "", "", "",
	);
	$string = str_replace($r_trans, $e_trans, $string);
	return $string;
}

if(!isset($_SESSION['username'])) {
    exit;
}

// checking lang value
if(isset($_COOKIE['sy_lang'])) {
    $load_lang_code = $_COOKIE['sy_lang'];
} else {
    $load_lang_code = "ru";
}

// including lang files
switch ($load_lang_code) {
    case "en":
        require(__DIR__ . '/lang/en.php');
        break;
    case "pl":
        require(__DIR__ . '/lang/pl.php');
        break;
	case "ru":
		require(__DIR__ . '/lang/ru.php');
		break;
}

// Including the plugin config file, don't delete the following row!
require(__DIR__ . '/pluginconfig.php');
include $_SERVER['DOCUMENT_ROOT'].'/editor/e_modules/ckeditor2/plugins/imageuploader/extensions.php';

$info = pathinfo($_FILES["upload"]["name"]);
$ext = $info['extension'];
if($ext == ''){
	$fileArr = explode('.', $_FILES["upload"]["name"]);
	$ext = $fileArr[count($fileArr) - 1];
}
$target_dir = $useruploadpath;
if(!is_dir($useruploadpath.date('Y'))){
	mkdir($useruploadpath.date('Y'));
	chmod($useruploadpath.date('Y'), 0777);
}
if(!is_dir($useruploadpath.date('Y').'/'.date('m'))){
	mkdir($useruploadpath.date('Y').'/'.date('m'));
	chmod($useruploadpath.date('Y').'/'.date('m'), 0777);
}
if(!is_dir($useruploadpath.date('Y').'/'.date('m').'/'.date('d'))){
	mkdir($useruploadpath.date('Y').'/'.date('m').'/'.date('d'));
	chmod($useruploadpath.date('Y').'/'.date('m').'/'.date('d'), 0777);
}
$target_dir = $useruploadpath.date('Y').'/'.date('m').'/'.date('d').'/';
$ckpath = "/$useruploadfolder/".date('Y').'/'.date('m').'/'.date('d').'/';

$randomLetters = $rand = substr(md5(microtime()),rand(0,26),6);
$imgnumber = count(scandir($target_dir));
$fileNameArr = explode('.', $_FILES["upload"]["name"]);
$filename = el_translit($fileNameArr[0], 'file')."$randomLetters.$ext";

$target_file = $target_dir . $filename;
$ckfile = $ckpath . $filename;
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
$check = true;//getimagesize($_FILES["upload"]["tmp_name"]);
if($check !== false) {
    $uploadOk = 1;
} else {
    echo "<script>alert('".$uploadimgerrors1."');</script>";
    $uploadOk = 0;
}
// Check if file already exists
if (file_exists($target_file)) {
    echo "<script>alert('".$uploadimgerrors2."');</script>";
    $uploadOk = 0;
}
// Check file size
/*if ($_FILES["upload"]["size"] > 1024000000) {
    echo "<script>alert('".$uploadimgerrors3."');</script>";
    $uploadOk = 0;
}*/

// Allow certain file formats
if(!in_array($imageFileType, $allowExt)/* != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" && $imageFileType != "ico" && $imageFileType != "avi"
	&& $imageFileType != "mpeg" && $imageFileType != "mp4" && $imageFileType != "ogv"*/) {
    echo "<script>alert('".$uploadimgerrors4."');</script>";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "<script>alert('".$uploadimgerrors5."');</script>";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["upload"]["tmp_name"], $target_file)) {
        if(isset($_GET['CKEditorFuncNum'])){
            $CKEditorFuncNum = $_GET['CKEditorFuncNum'];
            echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$ckfile', '');</script>";
        }
    } else {
        echo "<script>alert('".$uploadimgerrors6." ".$target_file." ".$uploadimgerrors7."');</script>";
    }
}
//Back to previous site
if(!isset($_GET['CKEditorFuncNum'])){
    echo '<script>history.back();</script>';
}