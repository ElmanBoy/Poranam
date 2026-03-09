<?
if(ob_get_length())ob_clean();
header("Content-type: text/html; charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
include $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';
$mrk=el_dbselect("SELECT `field2` FROM catalog_".$_REQUEST['parent_catalog']."_data WHERE field".$_REQUEST['parent_field']."='".trim($_REQUEST['val'])."'", 0, $mrk, 'row'/*, true, true*/);
$mod=el_dbselect("SELECT `field2` FROM catalog_".$_REQUEST['child_catalog']."_data  WHERE field".$_REQUEST['child_field']."='".$mrk['field2']."' ORDER BY field2 ASC", 0, $mod, 'result'/*, true, true*/);
$rmod=el_dbfetch($mod);
do{
	$sel=(addslashes($_REQUEST['curr_value'])==addslashes($rmod['field2']))?' selected':'';
	echo '<option value="'.addslashes($rmod['field2']).'"'.$sel.'>'.addslashes($rmod['field2']).'</option>'."\n";
}while($rmod=el_dbfetch($mod));
?>