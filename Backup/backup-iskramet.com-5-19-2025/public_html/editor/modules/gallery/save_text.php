<?
//error_reporting(E_ALL);
if(ob_get_length())ob_clean();
header("Content-type: text/html; charset=windows-1251");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
include $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';
$requiredUserLevel = array(1, 2); 
//$_POST['text']=iconv('UTF-8', 'WINDOWS-1251', $_POST['text']);
include($_SERVER['DOCUMENT_ROOT']."/editor/secure/secure.php");
el_dbselect("UPDATE photo SET `text`=".GetSQLValueString(nl2br($_POST['text']), "text")." WHERE id=".intval($_POST['id']), 0, $res, true, true);
echo nl2br(iconv('UTF-8', 'WINDOWS-1251', $_POST['text']));
?>