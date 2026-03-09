<?
require_once $_SERVER['DOCUMENT_ROOT']."/editor/e_modules/JsHttpRequest/lib/JsHttpRequest/JsHttpRequest.php";
$JsHttpRequest =& new JsHttpRequest("windows-1251");
include $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';
$requiredUserLevel = array(1, 2); 
include($_SERVER['DOCUMENT_ROOT']."/editor/secure/secure.php");

$data=el_getContentFromTable($_REQUEST['id'], $_REQUEST['cat']);

echo '<table border=0>'; 
while(list($key, $val)= each($data)){
	if(strlen($val['data'])>0)echo '<tr><td valign=top>'.$val['name'].'</td><td>'.$val['data'].'</td></tr>';
}
echo '</table>';
?>