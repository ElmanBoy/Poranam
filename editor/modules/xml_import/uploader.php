<?
require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php');

$filedir=$_SERVER['DOCUMENT_ROOT'].'/files/';         //Папка для хранения импортированных файлов
$uploaddir=$_SERVER['DOCUMENT_ROOT'].'/files/import/';//Папка для закачки импортируемых файлов
//$file=$uploaddir.$_POST['fileName'];
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Закачка файла</title>
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<meta http-equiv="cache-control" content="no-cache">
<!--META http-equiv=Page-Enter content=blendTrans(Duration=0.3)>
<META http-equiv=Page-Exit content=blendTrans(Duration=0.3)-->
<link href="../../style.css" rel="stylesheet" type="text/css">
</head>
<script language="javascript">
function newname(file, fileName){
	var name1=prompt("Вы импортируете новый тип данных из файла "+fileName+".\nВведите здесь название нового каталога.\n\n", "");
	var frm=parent.document.all.nform1;
	if((name1!="undefined" || name1!="" || name1!=null) && name1 && name1.length>0){
		with(frm){
			newcat.value=name1;
			filepath1.value=file;
			submit();
		}
		//alert("После импорта обязательно задайте настройки и шаблоны для нового каталога!");
	}else{
		alert("Название все же нужно ввести!");
		newname(file, fileName);
	}
}
</script>

<body>
<?php //print_r($_POST); print_r($_FILES);
$finish=false;
if(!empty($_POST['fileName'])){
	if(isset($_POST['filepath'])){
		$file=strtolower(el_translit($_POST['filepath']));
		$fullpath=$file;
	}else{
		$fullpath=$uploaddir.strtolower(el_translit($_POST['fileName']));
	}
	
	if(isset($_POST['upfile1']) || isset($_POST['upfile'])){
		(isset($_GET['i']))?$i=$_GET['i']:$i=0;
		if(!is_dir($uploaddir)){
			if(!mkdir($uploaddir, 0777)){
				echo "<font color=red>Системе не удается создать временную папку для закачиваемых файлов.<br>
				Пожалуйста, задайте права доступа для папки files равным 777</font>";
			}
		}
		
		if(strlen($_POST['fileUpload1'])>0){ 
			if(!isset($_GET['i'])){ //Если это первая закачка, удаляем одноименный файл
				if(file_exists($fullpath)){
					unlink($fullpath);
				}
			}
			/*echo '<script>alert("Кусок №'.$_GET['i'].'")</script>';*/
			if(!($fp=fopen($fullpath, 'a'))){
				die("<font color=red>Не удается закачать файл! Возможно, файл имеет недопустимое название.</font>");
			}
			fwrite($fp, stripslashes($_POST['fileUpload1']));
			fclose($fp);
				echo "Файл \"".strtolower(el_translit($_POST['fileName']))."\" успешно закачан !<br>";
				if(isset($_GET['i']) && $_POST['upfile1']!=1){ echo "<script language=javascript>parent.doCont(".($i+1).")</script>";}
				$file = $fullpath;
		}
		
		
		if(isset($_FILES['fileUpload'])){
			if(move_uploaded_file($_FILES['fileUpload']['tmp_name'], $uploaddir.strtolower(el_translit($_FILES['fileUpload']['name'])))){
				echo  "Файл \"".strtolower(el_translit($_FILES['fileUpload']['name']))."\" успешно закачан !<br>";
				$file = $uploaddir.strtolower(el_translit($_FILES['fileUpload']['name']));
			}else{
				echo  "<font color=red>Файл \"".strtolower(el_translit($_FILES['fileUpload']['name']))."\" не удалось закачать !</font><br>";
			}
		}
	}
	
	if((isset($_POST['upfile1']) || isset($_POST['newcat']) || isset($_POST['upfile'])) && $_POST['upfile1']==1){
		$addMethod=$_GET['m'];
		require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/xml_parse.php');
	}
		el_2ini('importForms', '');
		el_2ini('importFormsStart', '');
		el_2ini('importFormsCount', '');
?>

<?	
	//Обработка закачанных файлов
}elseif($_POST['showFiles']==1 && empty($_POST['selfSubmit'])){
	$dir = dir($uploaddir);
	$co=0;
	echo '<form method=post>';
       while($file = $dir->read()) {
           if($file != '.' && $file != '..' && strtolower(substr($file, -3))=='xml'){
				echo ($co+1).'. Файл &laquo;'.$file.'&raquo; <label for="mrew'.$co.'">
		      <input name="addmethod'.$co.'" type="radio" id="mrew'.$co.'" value="rewrite" checked>
		      Перезаписать каталог</label>
				<label for="addro'.$co.'">
		      <input type="radio" name="addmethod'.$co.'" id="addro'.$co.'" value="addrow">
		      Добавить в каталог</label>
			  <label for="noact'.$co.'">
		      <input type="radio" name="addmethod'.$co.'" id="noact'.$co.'" value="skip">
		      Ничего не делать</label>
			  <input type=hidden name="fileName'.$co.'" value="'.$file.'"><hr>';
		   		$co++;
		   }
		}
	echo '<center><input type=submit name=selfSubmit value="Обработать" class=but>&nbsp;&nbsp;&nbsp;
	<input type=button value="Закачать новые" onclick="parent.document.all.uplFrm.style.display=\'block\'" class=but ></center>
	<input type=hidden name=rowCount value="'.$co.'"></form>';
}elseif(isset($_POST['selfSubmit'])){
	$formString='';
	$formCount=0;
	$formStart= -1;
	if(!empty($_POST['rowCount'])){
		for($i=0; $i<$_POST['rowCount']; $i++){ 
			if($_POST['addmethod'.$i]!='skip'){
				if($formStart<0)$formStart=$i;
				$formString.= '
				<form method=post name="uplFrm'.$i.'" action=uploader.php?numiter='.($i+1).'>
				<input type="hidden" name="filepath" value="'.$uploaddir.$_POST['fileName'.$i].'">
				<input type="hidden" name="addmethod" value="'.$_POST['addmethod'.$i].'">
				<input type="hidden" name="selfSubmit" value="1">
				</form>';
				$formCount++;
			}else{
				echo 'Файл &laquo; '.$_POST['fileName'.$i].' &raquo; пропущен.<br>';
			}
		}
		el_2ini('importForms', $formString);
		el_2ini('importFormsStart', $formStart);
		el_2ini('importFormsCount', $formCount);
	}
	
	if(isset($_POST['newcat']) && strlen($_POST['newcat'])>0){ 
		$file=$_POST['filepath1'];
		include $_SERVER['DOCUMENT_ROOT'].'/Connections/xml_parse.php';
	}
	include $_SERVER['DOCUMENT_ROOT'].'/Connections/site_props.php';
	
	if(empty($_GET['numiter'])){
		$scriptString='<script>if(typeof(document.uplFrm'.$site_property['importFormsStart'].')=="object"){ try{document.uplFrm'.$site_property['importFormsStart'].'.submit();}catch(Error){}}</script>';
	}elseif($_GET['numiter']<$site_property['importFormsCount']-1){
		$scriptString='<script>window.setTimeout(function(){ try{document.uplFrm'.$_GET['numiter'].'.submit();}catch(Error){}}, 1000)</script>';
		$finish=false;
	}else{
		$scriptString='<h4>Обработка завершена!</h4><input type=button onclick="parent.location.href=\'index.php\'" class=but value="В начало">';
		$finish=true;
	}
	
	if(isset($_POST['addmethod']) && strlen($_POST['addmethod'])>0 && !isset($_POST['newcat']) && strlen($_POST['newcat'])==0){ 
		$file=$_POST['filepath'];
		$addMethod=$_POST['addmethod'];
		include $_SERVER['DOCUMENT_ROOT'].'/Connections/xml_parse.php';
	}
	
	if($finish){
		el_2ini('importForms', '');
		el_2ini('importFormsStart', '');
		el_2ini('importFormsCount', '');
	}else{
		echo $site_property['importForms'].$scriptString;
	}
}
?>

<script language="javascript">
parent.document.all.ifMsg.style.visibility="visible";
</script>
</body>
</html>
