<?php
session_start();

// checking lang value
if(isset($_COOKIE['sy_lang'])) {
	$load_lang_code = $_COOKIE['sy_lang'];
} else {
	$load_lang_code = "en";
}

// including lang files
switch ($load_lang_code) {
	case "en":
		require(__DIR__ . '/lang/en.php');
		break;
	case "pl":
		require(__DIR__ . '/lang/pl.php');
		break;
}

//Remove folder and files
function el_delDir($dirName)
{
	$err = 0;
	$resultStr = array();
	if (empty($dirName)) {
		return false;
	}
	if (file_exists($dirName)) {
		$dir = dir($dirName);
		while ($file = $dir->read()) {
			if ($file != '.' && $file != '..') {
				if (is_dir($dirName . '/' . $file)) {
					el_delDir($dirName . '/' . $file);
				} else {
					if (!unlink($dirName . '/' . $file)) {
						$resultStr[] = "Файл \"' . $dirName . '/' . $file . '\" не удалось удалить!";
						$err++;
					}
				}
			}
		}
		if (!rmdir($dirName . '/' . $file)) {
			$resultStr[] = "Папку \"' . $dirName . '/' . $file . '\" не удалось удалить!";
			$err++;
		}
	} else {
		$resultStr[] = "Папка \"' . $dirName . '\" не существует.";
		$err++;
	}
	return ($err > 0 ) ? $resultStr : true;
}

// Including the plugin config file, don't delete the following row!
require(__DIR__ . '/pluginconfig.php');

if(isset($_SESSION['username'])){

	if(isset($_POST['new'])){
		$pathArr = explode('/', $_POST['currentFolder']);
		array_pop($pathArr);
		$pathBefore = implode('/', $pathArr);
		$createFolder = $pathBefore.$_POST['name'];
		mkdir($_POST['currentFolder'].'/'.$_POST['name'], 0777);
	}

	if(isset($_POST['delete'])){
		$result = el_delDir($_POST['currentFolder']);
		if($result != true){
			echo implode("\n", $result);
		}else{
			//echo 'Папка удалена!';
		}
	}

	if(isset($_POST['rename'])){
		$pathArr = explode('/', $_POST['currentFolder']);
		array_pop($pathArr);
		rename($_POST['currentFolder'], implode('/', $pathArr).'/'.$_POST['name']);
	}
}

?>