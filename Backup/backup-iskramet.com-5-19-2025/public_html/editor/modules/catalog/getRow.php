<?php
if(ob_get_length())ob_clean();
header("Content-type: text/html; charset=windows-1251");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
include $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';
if(isset($_POST['id']) && isset($_POST['catalog_id'])){
	$n = el_dbselect("SELECT MAX(id) AS new_id, MAX(sort) AS new_sort FROM catalog_".$_POST['catalog_id']."_data", 0, $n, 'row', true);
	$r = el_dbselect("SELECT * FROM catalog_".$_POST['catalog_id']."_data WHERE id=".intval($_POST['id']), 0, $r, 'row', true);
	echo json_encode(array('exist' => $r, 'new' => $n));
}
?>

