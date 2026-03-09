<?php require_once('../Connections/dbconn.php'); 
$requiredUserLevel = array(1, 2); 
include($_SERVER['DOCUMENT_ROOT']."/editor/secure/secure.php"); ?>
<?php
$colname_text = "1";
if (isset($_GET['id'])) {
  $colname_text = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
;
$query_text = sprintf("SELECT text FROM infoblocks WHERE id = %s", $colname_text);
$text = el_dbselect($query_text, 0, $text, 'result', true);
$row_text = el_dbfetch($text);
$totalRows_text = mysqli_num_rows($text);
?>
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<meta http-equiv="cache-control" content="no-cache">
<style type="text/css">
<!--
table, td {
	border: 1px dotted #999999;
}
body{padding:10px 10px 10px 10px}
.anchorlink{width:20px; height:20px; background-image:url(/editor/img/anchor.gif);}
-->
</style>
<?php $str = preg_replace("/(\n|\r)/","",$row_text['text']);  echo $str; ?> 
<?php
mysqli_free_result($text);
?>

