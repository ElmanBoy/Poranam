<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php');
//error_reporting(E_ALL);
//������������� ����������
$currentTag="";    //������� ��� � ���������
$wordData=array(); //������ ��� �������� �������� ������
$currentId=0;      //������� ID ������
$header=array();   //������ ��� �������� ���������� �������
$fileName=array(); //������ ��� �������� ���� �������� ������
$html=array();     //������ ��� �������� html-������
$column=array();   //������ ��� �������� ������� �������� Value � ����� xml
$columnId=0;       //������� ����� �������
$DType="";         //��� ������������ �������
$ExpDate="";       //���� �������� �������������� ���������
$htmlColumn=0;     //����� �������, ���������� html-������
$columnType=array();//������ ��� �������� ����� ������� 
$errStr='';         //���������� ��� �������� ��������� �� �������

//������� ������ ����������� ���������
function jsalert($txt){
	echo "<script language=javascript>alert(\"".$txt."\")</script>";
}
//������� ����������� ���������� ����� � XML-������
function is_word($str){
	$preg='|[a-zA-Z�-��-���0-9]|i';
	preg_match_all($preg,$str,$found);
	if(count($found[0])>0){
		return TRUE;
	}else{
		return FALSE;
	}
}
//������� �������� ������� ������ iconv
if(!function_exists("iconv")){
	$errStr.="� ������ �������-���������� �� ���������� ������ ICONV!\\n � ����� � ���� ����� ���������� ��������� ������������� ������ � ��������� Windows-1251 \\n";
	if(empty($included)){
		jsalert($errStr);
	}
}
//������� ������� html-���� �� "������", ������������� MS Word
function cleanHTML($text) {
	$text = stripslashes($text);
	$text = eregi_replace('<\?xml:[^"]+\?>', "", $text);
    $text = eregi_replace('/<H[0-9]+\s?([^>]*)>/ig', "<p \\1>", $text);
    $text = eregi_replace('/<\/H[0-9]+>/ig', "</p>", $text);
    $text = eregi_replace('/<TT([^>]*)>/ig', "<p \\1>", $text);
    $text = eregi_replace('/<\/TT>/ig', "</p>", $text);
    $text = eregi_replace('<\/?font[^>]*>', "", $text);
    $text = eregi_replace('<\/?span[^>]*>', "", $text);
    $text = eregi_replace('<\/?\w+:\w+[^>]*>', "", $text);
    $text = eregi_replace('<p\s*[^>]*>', "<p>", $text);
	$text = eregi_replace('<\/p>', "</p>", $text);
	$text = eregi_replace("\s*class=Mso\w*\s*", " ", $text);
    $text = eregi_replace('(style="[^"]*)TEXT-ALIGN:\s?([a-z]*)([^"]*")', "align=\\2 \\1\\3", $text);
    $text = eregi_replace('(style="[^"]*)BACKGROUND:\s?([a-z0-9#]*)([^"]*")', "bgcolor=\\2 \\1\\3", $text);
	$text = eregi_replace('\s(lang|style|class)\s*=\s*"[^"]*"', " ", $text);
    $text = eregi_replace("\s(lang|style|class)\s*=\s*'[^']*'", " ", $text);
    $text = eregi_replace('\s(lang|style|class)\s*=\s*[^\s>]*', " ", $text);
    $text = eregi_replace('(<\/?)dir>', "$1blockquote>", $text);
    $text = eregi_replace('(<td[^>]*>)\s*<p>([^<>]*)<\/p>\s*<\/td>', "\\1\\2</td>", $text);
	$text = eregi_replace('(style\s*=\s*"[^"]+\s*")', "", $text);
	return ($text);
}


//������� ���������� �������� ������ � ����
function saveDocFile($data, $filename=""){
	global $included;
	if(strlen($data)>0){
		$fullpath="";
		$filenumber="";
		$ext="";
		$tempname=$tempname1=$tempname2="";
		$filenumber=array();
		$dataArr=array();
		$dirName=$_SERVER['DOCUMENT_ROOT'].'/files/';
		$dir = dir($dirName);
		$ext=substr(strrchr($filename, "."), 0);
		$tempname=str_replace($ext, "", $filename);
		while($file = $dir->read()) {
			$ext1=substr(strrchr($file, "."), 0);
			$tempname1=str_replace($ext1, "", $file);
			$tempname2=preg_replace("/\[(\d+)\]/", "", $tempname1);
			if($file != '.' && $file != '..' && ($tempname1==$tempname || $tempname2==$tempname)) { 
				preg_match_all("/\[(\d+)\]\./", $file, $number, PREG_PATTERN_ORDER); 
				if(count($number[1])>0){
					$filenumber[]=$number[1][count($number[1])-1];
				}
			}
		}
		
		if(file_exists($_SERVER['DOCUMENT_ROOT'].'/files/'.$filename) || strlen($filenumber)>0){
			$filenumber1=max($filenumber);
			$newname=$tempname.'['.($filenumber1+1).']'.$ext;
			$fullpath=$_SERVER['DOCUMENT_ROOT'].'/files/'.$newname;
		}else{
			$fullpath=$_SERVER['DOCUMENT_ROOT'].'/files/'.$filename;
		}
		
		$fp=fopen($fullpath, 'wb');
		if(!$fp){
			if(empty($included)){echo "<center><h4 style=color:red>�� ������� ������� ���� $filename</h4></center>";
			}else{$errStr.="�� ������� ������� ���� $filename \n";}
		}else{
			fputs($fp, base64_decode($data));
			fclose($fp);
		}
		if(strlen($newname)>0){
			return $newname;
		}else{
			return $filename;
		}
	}else{
		return false;
	}
}
//������� ����������� ������ xml-����
function startElement($parser, $name, $attrs){
   global $currentTag, $column, $columnId, $currentId, $columnType;
   $currentTag = $name;
   if($name=='COLUMN' || $name=='HEADER'){
		if(strlen($attrs['ORDERID'])>0){
			$columnId=iconv("UTF-8", "WINDOWS-1251", $attrs['ORDERID']); 
		}
		//if(strlen($attrs['VALUE'])>0){
			$column[$currentId][$columnId]=iconv("UTF-8", "WINDOWS-1251", $attrs['VALUE']); 
		//}
		if(strlen($attrs['VT'])>0){
			$columnType[$currentId][$columnId]=iconv("UTF-8", "WINDOWS-1251", $attrs['VT']); 
		}
   }
}
//������� ����������� ����� xml-����
function endElement($parser, $name){
	echo '';
}

//������� ��������� � ��������� � �������������� ���������� �������� �� xml
function characterData($parser, $data){
   global $currentId, $currentTag, $wordData, $fileName, $html, $columnId, $DType, $ExpDate, $header, $htmlColumn;
   /*if(iconv_set_encoding("input_encoding", "UTF-8") && iconv_set_encoding("output_encoding", "CP-1251")){
   		echo "<font color=red>ICONV �������� �� ���������!</font><br>";
   }*/

   $data=iconv("UTF8", "CP1251", $data); 
   switch ($currentTag){
	case 'DATATYPE': if(is_word($data)){$DType=$data;} break;
	case 'EXPORTDATE': if(is_word($data)){$ExpDate=$data;} break;
	case 'COLUMN'  : if(strlen($data)>0){$header[$columnId]=$data;}; break;
	case 'ID'      : if(is_numeric($data)){ $currentId=$data; $columnId=0;}else{ $currentId=$currentId; } break;
	case 'BINARY'  : $wordData[$currentId][$columnId].=$data; break;
	case 'FILENAME': if(is_word($data) && strlen($data)>0){$fileName[$currentId][$columnId]=$data;}else{$fileName[$currentId][$columnId]=$fileName[$currentId][$columnId];} break;
	case 'HTML'    : $html[$currentId][$columnId].=$data; $htmlColumn=$columnId; break;
	}
	
}
//�������� xml-�������
$xml_parser = xml_parser_create('UTF-8');
xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, true);
xml_set_element_handler($xml_parser, "startElement", "endElement");
xml_set_character_data_handler($xml_parser, "characterData");

//�������� ������� �����
if(filesize($file)>4194000){if(empty($included)){ echo "<font color=red>���� XML ������� �����!<br>������� �� ������� ���������� ����� ���� �� ���� ���.</font>";}else{$errStr.="���� XML ������� �����! ������� �� ������� ���������� ����� ���� �� ���� ���. \n";}};

//�������� xml-�����, ������ ������ �� ���� � ������� xml-������
if (file_exists($file) && !($fp = fopen($file, "r"))) {
   if(empty($included)){ 
   	echo "<font color=red>�� ������� ������� XML-���� &laquo; ".$file." &raquo; ��� ������!</font>";
	exit();
   }else{
   	$errStr.="�� ������� ������� XML-���� $file ��� ������! \n";
	exit();
   }
}

while ($data = fread($fp, 4096)) {
   if (!xml_parse($xml_parser, $data, feof($fp))) {
       die(sprintf("<font color=red>������ XML : %s � ������ %d</font>",
                   xml_error_string(xml_get_error_code($xml_parser)),
                   xml_get_current_line_number($xml_parser)));
   }
}
xml_parser_free($xml_parser);

//������� �������� ����� ������� ��������
function create_new_catalogtable($name, $cyrname, $column_array, $columnTypes_array){ 
	global $database_dbconn, $dbconn, $header;
	$query = "";
	$subquery = "";
	$insert_query = "";
	$coltype = "";
	$err=0;
	$err1=0;
	$textField=array();
	if(el_dbselect("SELECT * FROM catalog_".$name."_data", 0, $result2)!=FALSE){
		jsalert("����� ������� ��� ����������!");
		$err=1;
	}	
	$result3=el_dbselect("SELECT * FROM catalogs WHERE catalog_id='".$cyrname."'", 0, $result3);
	if(@mysqli_num_rows($result3)>0){
		jsalert("������� � ����� ��������� ��� ����������! �������� ������ ��������.");
		$err1=1;
	}
	if($err!=1 && $err1!=1){	
		ksort($columnTypes_array); 
		ksort($column_array);
		for($i=0; $i<count($column_array); $i++){
			switch($columnTypes_array[0][$i]){
				case 'int'    : $coltype='int(11)';  break;
				case 'string' : $coltype='text';     break;
				case 'date'   : $coltype='date'; break;
				case 'Base64' :
				case 'bin.hex': $coltype='longtext'; break;
				default       : $coltype='text';     break;
			}
			$subquery.="`field".$i."` ".$coltype.",
			";
			($i==count($column_array)-1)?$end="":$end=", ";
			$insertFields.="field".$i.$end;
			if($coltype=='text' || $coltype=='longtext'){$textField[]="`field".$i."`";}
			$insertVars.="'".addslashes($column_array[$i])."'".$end;
		}
		$query="CREATE TABLE catalog_".$name."_data (
		`id` int(11) NOT NULL auto_increment,
		`cat` INT NULL,
		`active`  int(11) NOT NULL default '1',
  		`sort` int(11) NOT NULL default '1',
		`goodid` INT NULL,
		`date` datetime,
		`filename` text,
		".$subquery."
		UNIQUE KEY `id` (`id`),
  		FULLTEXT KEY `filename` (`filename`,".implode(',', $textField).")
		) TYPE=MyISAM";
		$result=el_dbselect($query, 0, $result);
		if(!$result) return false;
		$query1="INSERT INTO catalog_".$name."_data (`date`, `filename`, ".$insertFields.") VALUES (1, 1, NULL, NULL, NULL, NULL, ".$insertVars.")";
		//el_dbselect($query1, 0, $res);
		
		if(el_dbselect("SELECT * FROM catalog_".$name."_data", 0, $result1)!=FALSE){ 
			
			el_dbselect("INSERT INTO `catalogs` (name, catalog_id, lines_per_pages, cols_per_pages, template_set) 
			VALUES ('".$cyrname."', '".$name."', 20, 1, 1)", 0, $res);
			el_dbselect("INSERT INTO `modules` (type, name, status, `path`, sort) VALUES ('catalog".$name."', '".$cyrname."', 'Y', 'modules/catalog', 999)", 0, $res);
			
			reset($columnTypes_array);
			ksort($columnTypes_array);
			reset($column_array);
			ksort($column_array);
			reset($header);
			ksort($header);
			for($i=0; $i<count($column_array); $i++){
				
				switch($columnTypes_array[0][$i]){
					case 'date'   : $fieldtype='calendar'; $exparam="0, 0";   break;
					case 'Base64' :
					case 'bin.hex': $fieldtype='textarea'; $exparam="50, 10"; break;
					default       : $fieldtype='text';     $exparam="0, 0";   break;
				}
				
				$insert_query="INSERT INTO catalog_prop (name, type, size, cols, rows, sort, list, detail, search, show_name, catalog_id, field) 
				VALUES ('".$column_array[$i]."', '".$fieldtype."', 40, ".$exparam.", ".$i.", 1, 0, 1, 1, '".$name."', ".$i.")";
				el_dbselect($insert_query, 0, $res);
			}
			return TRUE; 
		}else{
			return FALSE; 
		}
	}
}

//������� ������� � ������������ ������� ����� ������
function add_catalogrow($DType, $column, $columnType, $ExpDate, $currentId, $wordData, $html, $fileName){
//$i - ����� ������
//$� - ����� ������� 
	global $addMethod, $included;
	$insertFields="";
	$insertVars="";
	$newFileName=array();
	$start=array();
	$end=array();
	$docFileName="";
	reset($column);
	reset($columnType);
	reset($wordData);
	reset($html);
	reset($fileName);
	if(empty($included)){echo '���� �������� ��������� &laquo;'.$ExpDate.'&raquo;<br>';}
	ksort($column);
	ksort($html);
	
	//�������� �������, � ������� ����� ��������� ������
	for($c=0; $c<count($column[0]); $c++){
		($c==count($column[0])-1)?$end="":$end=", ";
		$insertFields.="field".$c.$end;
	}
	//�������� ����� ������
	for($i=0; $i<=$currentId; $i++){
		ksort($columnType[$i]);
		for($c=0; $c<count($column[$i]); $c++){
			if(strlen($wordData[$i][$c])>0 && strlen($fileName[$i][$c])>0){ 
				$newFileName[$i][]=saveDocFile($wordData[$i][$c], el_translit($fileName[$i][$c]));
				(count($newFileName[$i])>1)?$strend='�':$strend='';
				$currFileName[$i]=@implode(', ', $newFileName[$i]);
				if(empty($included)){echo "����$strend &laquo;".$currFileName[$i]."&raquo; ��������$strend!<br>";}
			}	
		}
	}
	reset($columnType[$i]);
	//�������� ����� ������ ������� �� ���������� �����
	for($i=0; $i<=$currentId; $i++){
		ksort($columnType[$i]);
		for($c=0; $c<count($column[$i]); $c++){
			if($c==count($column[$i])-1){
				if($i==$currentId){
					$end[$i]=")";
				}else{
					$end[$i]="), ('".$ExpDate."', '".$currFileName[$i+1]."', ";
				}
			}else{
				$end[$i]=", ";
			}
			
			switch($columnType[$i][$c]){
				case 'int'    :$insertVars.="'".addslashes($column[$i][$c])."'".$end[$i]; break;
				case 'date'   :$insertVars.="'".addslashes($column[$i][$c])."'".$end[$i]; break;
				case 'Base64' :
				case 'bin.hex':$insertVars.="'".addslashes(cleanHTML($html[$i][$c]))."'".$end[$i]; break;
				case 'string' :$insertVars.="'".addslashes($column[$i][$c])."'".$end[$i]; break;
			}
		}
	}
	
	$insertVars="('".$ExpDate."', '".$currFileName[0]."', ".$insertVars;//$start[0]
	
	$query="INSERT INTO catalog_".$DType."_data (`date`, `filename`, ".$insertFields.") VALUES ".$insertVars;//'".$ExpDate."', '".implode(', ', $newFileName)."', 

	$res1=el_dbselect("SELECT name FROM `catalogs` WHERE catalog_id='".$DType."'", 0, $res1, 'row');
	
	if($addMethod=='rewrite'){
		el_dbselect("TRUNCATE TABLE `catalog_".$DType."_data`", 0, $res);
	}
	
	if(el_dbselect($query, 0, $res)!=FALSE){
		if(empty($included)){echo "��������� ".($currentId+1)." ������� � ������� &laquo;".$res1['name']."&raquo;<br>";}
		return TRUE;
	}else{
		if(empty($included)){echo "<font color=red>�� ������� �������� ������ � ������� &laquo;".$res1['name']."&raquo;.<br>��������, ������������� ���� �������� ������������ ������.</font><br>";} else{ $errStr.="�� ������� �������� ������ � ������� \"".$res1['name']."\". ��������, ������������� ���� �������� ������������ ������. \n";}
		return FALSE;
	}
}



if(el_dbselect("SELECT * FROM catalog_".$DType."_data", 0, $exist)==FALSE && file_exists($file)){ 
	if(!isset($_POST['newcat']) ||  strlen($_POST['newcat'])==0){
		if(empty($included)){
			$pathArr=explode('/', $file);
			$fileName=$pathArr[count($pathArr)-1];
			echo "<script language=javascript>newname('".$file."', '".$fileName."')</script>";
		}else{
			$errStr.="������� ��������� ������ \"$DType\" ��� ��� �� ���������� ��������";
		}
	}
	if(isset($_POST['newcat']) && strlen($_POST['newcat'])>0 && file_exists($file)){ 
		$name=strip_tags(trim(str_replace("'", '', str_replace('"', '', $_POST['newcat'])))); 
		if(create_new_catalogtable($DType, $name, $header, $columnType)==FALSE){
			echo "<font color=red>�� ������� ������� ����� �������!</font><br>";
		}else{
			echo "������� '".$name."' ������!<br>";
			if(add_catalogrow($DType, $column, $columnType, $ExpDate, $currentId, $wordData, $html, $fileName)){
				echo '
				<!--script language=javascript>
				var OK=confirm("������ ��������� ����� ������� ������?");
				if(OK){
					parent.location.href="/editor/modules/catalog/catalogs.php?id='.$DType.'";
				}
				</script-->';
			}
			unlink($file);
		}
	}
}elseif(file_exists($file)){
	add_catalogrow($DType, $column, $columnType, $ExpDate, $currentId, $wordData, $html, $fileName);
	unlink($file);
}
if(!empty($included)){if($errStr==''){echo 'OK';}else{echo 'Error: '.$errStr;}}
?> 