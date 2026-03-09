<?
if(ob_get_length())ob_clean();
header("Content-type: text/html; charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
include $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';
$mrk=el_dbselect("SELECT `field2` FROM catalog_marks_data WHERE field1='".trim($_REQUEST['val'])."'", 0, $mrk, 'row'/*, true, true*/);
$mod=el_dbselect("SELECT `field2` FROM catalog_groups_data WHERE field6='".$mrk['field2']."' AND field5=15 GROUP BY field2 ORDER BY `field2` ASC", 0, $mod, 'result'/*, true, true*/);
$rmod=el_dbfetch($mod);
do{
	$sel=(addslashes($_REQUEST['curr_value'])==addslashes($rmod['field2']))?' selected':'';
	echo '<option value="'.addslashes($rmod['field2']).'"'.$sel.'>'.addslashes($rmod['field2']).'</option>'."\n";
}while($rmod=el_dbfetch($mod));
?>