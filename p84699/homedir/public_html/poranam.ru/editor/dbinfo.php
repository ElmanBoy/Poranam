<?
include $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';
$i=el_dbselect("SELECT name, catalog_id FROM catalogs", 0, $i);
$ri=el_dbfetch($i);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Информация о каталогах</title>
</head>
<body>
<?
do{
	echo '<b>'.$ri['name'].' - таблица catalog_'.$ri['catalog_id'].'_data</b><br>';
	$f=el_dbselect("SELECT name, field FROM catalog_prop WHERE catalog_id='".$ri['catalog_id']."'", 0, $f);
	$rf=el_dbfetch($f);
	do{
		echo $rf['name'].' - field'.$rf['field'].'<br>';
	}while($rf=el_dbfetch($f));
	echo '<br><br>';
}while($ri=el_dbfetch($i));
?>

</body>
</html>
