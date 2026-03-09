<?
require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php');
$requiredUserLevel = array(1, 2); 
include($_SERVER['DOCUMENT_ROOT']."/editor/secure/secure.php"); 
(isset($submit))?$work_mode="write":$work_mode="read";
el_reg_work($work_mode, $login, $_GET['cat']);

$filedir=$_SERVER['DOCUMENT_ROOT'].'/files/';         //Папка для хранения импортированных файлов
$uploaddir=$_SERVER['DOCUMENT_ROOT'].'/files/import/';//Папка для закачки импортируемых файлов
//if(is_dir($uploaddir)){el_deldir($uploaddir);}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Импорт XML</title>
<link href="../../style.css" rel="stylesheet" type="text/css">
</head>
<script language="javascript1.2">
function writeCookie(name, value, hours){
  var expire = "";
  if(hours != null){
    expire = new Date((new Date()).getTime() + hours * 3600000);
    expire = "; expires=" + expire.toGMTString();
  }
  document.cookie = name + "=" + value + expire;
}


function readCookie(name){
  var cookieValue = "";
  var search = name + "=";
  if(document.cookie.length > 0){ 
    offset = document.cookie.indexOf(search);
    if (offset != -1){ 
      offset += search.length;
      end = document.cookie.indexOf(";", offset);
      if (end == -1) end = document.cookie.length;
      cookieValue = document.cookie.substring(offset, end)
    }
  }
  return cookieValue;
}

// Функция разбивает файл на кусочки заданного размера
// oFSO  - Scripting.FileSystemObject
// oFile - объект - переданный файл
// [Out] - пути к полученным файлам через запятую
var SplitSize=<?=(el_returnbytes(ini_get('upload_max_filesize'))/2)?>;
var aFileNames;  //Массив путей нарезанных файлов
var sResult; //Массив строковых данных из разбитых файлов
sResult = new Array();


function SplitFile(oFSO, oFile , SplitSize)
{
	var FOR_READING = 1, FOR_WRITING = 2, TEMPORARY_FOLDER = 2
	var oNewStream;		// поток для нового файла
	var oTextStream;	// поток для переданного файла
	var sFileName;		// имя переданного файла
	var sFileExt;		// расширение переданного файла
	var sFilePath;		// путь к файлу который требуется разбить
	var sNewFileName;	// имя нового записываемого файла
	var sTempFolder;	// путь к временной папке Windows
	var i;
	i = 0;
	
	// возьмем файл и получим его имя и расширение
	sFileName = oFile.Name.substr(0, oFile.Name.lastIndexOf("."));
	sFilePath = oFile.Path; 
	sFileExt = oFSO.GetExtensionName(sFilePath);
	// откроем поток для чтения файла
	oTextStream  = oFSO.OpenTextFile(sFilePath, FOR_READING);
	aFileNames=sFileName+"."+sFileExt; //Имя закачиваемого файла
		// и будем его читать покуда не прочтем
	   do 
	   {
				// запишем результат в массив
				sResult[i]=oTextStream.Read(SplitSize); //Массив нарезанных строк
				i += 1;
        }
		while(!oTextStream.AtEndOfStream);
}

// функция подготавливает выбранный файл к отправке, 
// разбивая его на несколько отрезков если это необходимо
function prepareFile(mode){//debugger;
	var oFSO;			// AS Scripting.FileSystemObject
	var oFile;			// объект - переданный файл
	var SplitSize;		// Размер буфера
	var fFile=document.getElementById("fileUpload");//ссылка на поле типа file
	var fFile1=document.getElementById("fileUpload1");//ссылка на поле типа file
	var fMaxUploadSize=document.getElementById("MaxUploadSize");//ссылка на поле типа file
	var fUploadFiles=document.getElementById("UploadFiles");//ссылка на поле типа file
	var i;
	SplitSize = fMaxUploadSize.value;
	var file_name=fFile.value.split('\\'); 
	var fMethod=document.getElementById("mrew");
	//var mode='';
	file_c=file_name.length;
	file_ext=file_name[file_c-1].substring(file_name[file_c-1].lastIndexOf('.'),file_name[file_c-1].length)
	if(fFile.value!="" && mode!='d'){
		if(file_ext==".xml" || file_ext==".XML"){
			// создаем FSO
			try
			{
				oFSO = new ActiveXObject("Scripting.FileSystemObject");
			}
			catch(Error){
				window.alert("Не удалось создать объект Scripting.FileSystemObject \n"
				 + "Для проболжения работы добавьте эту страницу в зону Trusted Sites");
				return false;
			}
			
			// проверим размер файла
			oFile = oFSO.GetFile(fFile.value);
			if(fMethod.checked==true){
				mode='rewrite';
				writeCookie('modeImportXML', '0', 9000);
			}else{
				mode='addrow';
				writeCookie('modeImportXML', '1', 9000);
			}
			if(oFile.size > 4194000) 
			{
				var OK=confirm("Вы импротируете слишком большой файл.\nСистема может не справиться с таким объемом за один раз.\nПродолжить все равно?");
				if(OK){
					SplitFile(oFSO, oFile, SplitSize);
					doCont(0, mode);
				}
			}
			else
			{
				SplitFile(oFSO, oFile, SplitSize);
				doCont(0, mode);
			}
		}else{
			alert("Закачиваемый файл не является xml-файлом!");
			return false;
		}
	}else if(mode=='d'){
		document.getElementById('uplFrm').style.display='none';
		document.fform2.showFiles.value=1;
		document.fform2.submit();
	}else{
		alert("Сначала выберите файл!");
		return false;
	}
}

//Функция продолжения закачки кусков файла
function doCont(iter, mode){
	var fFile1=document.getElementById("fileUpload1");//ссылка на поле типа hidden
	var fFileName=document.getElementById("fileName");//ссылка на поле типа hidden
	var pMax;//сколько всего итераций закачки
	var aAll;
	if(mode=='rewrite'){
		var opt="&m=rewrite";
	}else{
		var opt="&m=addrow";
	}
	pMax=0;
	aAll=sResult.length;
	pMax=Math.round(400/aAll);
	fileform.reset();
	document.fform2.action="uploader.php?i="+iter+opt;
	fFileName.value=aFileNames;
	//if(iter!=sResult.length){
		if(iter==(sResult.length-1)){
			document.fform2.upfile1.value=1;
		}else{
			document.fform2.upfile1.value=0;
		}
		fFile1.value=sResult[iter];
		pCurrWidth=(pMax*iter);
		pEnd=((pMax*iter)+pMax);
		parent.opacity=100;
		parent.pMessage="Пожалуйста, подождите. Идет закачка файла...Кусок №"+(iter+1);
		parent.drawProgress(pCurrWidth, pEnd);
		document.fform2.submit();
	//}
}


pszFont = "Tahoma,8,,BOLD";//Параметры вывода подсказок
</script>
<body>
<form method="post" name="fform2" ENCTYPE="multipart/form-data" target="ifMsg" action="uploader.php">
	<INPUT TYPE="hidden" name="fileUpload1" id="fileUpload1" />
	<INPUT TYPE="hidden" name="fileName" id="fileName" />
	<input name="upfile1" type="hidden" id="upfile1" value="0">
	<input name="whatdo" type="hidden" id="whatdo">
    <input name="showFiles" value="0" type="hidden">
</form>

<form method="post" name="nform1" target="ifMsg" action="uploader.php">
	<input type="hidden" name="newcat" value="<?=$_POST['newcat']?>">
	<input type="hidden" name="filepath1" value="<?=$file?>">
	<input type="hidden" name="upfile1" value="1">
    <input type="hidden" name="selfSubmit" value="1">
</form>

<table border="0" align="center" cellpadding="5" cellspacing="0" width="90%">
   <tr>
    <td align="center" valign="middle"><h4>Импорт XML-данных </h4></td>
  </tr>
 <tr>
    <td align="center" valign="top">
    <? if(!isset($_GET['settings'])){ ?>
    <a href="index.php?settings=1">Настройки web-сервиса</a>
    <div id="uplFrm" style="display:block; margin-bottom:10px">
	<? el_showalert("info",
	"Кликните по кнопке \"Обзор\" и выберите xml-файл(ы) на своем компьютере.<br>
	После того, как все готово, кликните по кнопке \"Импорт\".<br>
	Если у Вас есть FTP-доступ к сайту, то рекомендуется закачивать файлы<br>
	с помощью FTP-клиента непосредственно в папку files/import.
	") ?>
<fieldset>
 <legend>Закачка новых файлов <img src="/editor/img/help_button.gif" alt="Кликните для получения справки" width="12" height="12" style="cursor:pointer" onClick="test.TextPopup ('Файл будет помещен в директорию files.\n Внимание! Название файла должно состоять только\nиз английских букв и символов!\n Выберите файл , нажав на кнопку <<Обзор>>,\nа затем нажмите кнопку <<Импорт>>',pszFont,10,10,-1,-1)"></legend>
<div style="color:red"><?=$errmsg?></div>
<div style="color:green"><?=$mess?></div>
<form name="fileform" onSubmit="return false">

		<INPUT TYPE="file" name="fileUpload" id="fileUpload" /><br>
		<br>
		<table>
		  <tr>
		    <td><strong>Что сделать с импортируемыми данными:</strong> </td>
	      </tr>
		  <tr>
		    <td><label for="mrew">
		      <input name="addmethod" type="radio" id="mrew" value="rewrite" <?=($_COOKIE['modeImportXML']==0)?'checked':''?>>
		      Перезаписать каталог</label></td>
	      </tr>
		  <tr>
		    <td><label for="addro">
		      <input type="radio" name="addmethod" id="addro" value="addrow" <?=($_COOKIE['modeImportXML']==1)?'checked':''?>>
		      Добавить в каталог</label></td>
	      </tr>
	    </table>
		<br>
		<INPUT ID="MaxUploadSize" TYPE="text" VALUE="<?=(el_returnbytes(ini_get('upload_max_filesize'))/2)?>" STYLE="display:none">
		<INPUT ID="UploadFiles" TYPE="text" VALUE="" STYLE="display:none">
		<input name="upfile" type="hidden" id="upfile" value="0">
		<input name="submit" type="button" class="but" value="Импорт" onClick="prepareFile('u')">&nbsp;&nbsp;&nbsp;
        <input name="submit" type="button" class="but" value="Обработать закачанные" onClick="prepareFile('d')"><br><br>
    </form>
<OBJECT height="1" width="1" id=test type="application/x-oleobject"
  classid="clsid:adb880a6-d8ff-11cf-9377-00aa003b7a11"> 
</OBJECT>
</fieldset>
<br></div>
<iframe frameborder="0" scrolling="auto" width="700" height="350" name="ifMsg" id="ifMsg" src="uploader.php" style="visibility:hidden"></iframe>
<? 







}else{ 
if(!empty($_POST['sekret'])){
	el_2ini('sekret_key', $_POST['sekret']);
	echo '<script>alert("Изменения сохранены!")</script>'; 
}
include $_SERVER['DOCUMENT_ROOT'].'/Connections/site_props.php';
?>
<a href="index.php">Возврат к импорту</a><br>
<form method="post">
Серкетное слово для web-сервиса: <input type="text" size="20" name="sekret" value="<?=$site_property['sekret_key']?>"><br><br>
<input type="submit" value="Сохранить" class="but"></form>
<? }?>
  </td>
  </tr>
</table>
</body>
</html>
