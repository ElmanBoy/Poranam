<?php
require_once('../Connections/dbconn.php');
//error_reporting(E_ALL);

$_GET['id'] = intval($_GET['id']);
$requiredUserLevel = array(0, 1, 2);
include($_SERVER['DOCUMENT_ROOT'] . "/editor/secure/secure.php");
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
	$editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

function el_moveCat($id, $parent)
{
	global $_POST, $_GET, $_SERVER;
	$exPath = el_dbselect("SELECT path FROM cat WHERE id='" . $id . "'", 0, $pa, 'row');
	$parPath = el_dbselect("SELECT path FROM cat WHERE id='" . $parent . "'", 0, $pa, 'row');
	$realPath = explode('/', $exPath['path']);
	$new_path = $parPath['path'] . '/' . $realPath[count($realPath) - 1];

	$updateSQL = sprintf("UPDATE cat SET parent=%s WHERE id=%s",
		GetSQLValueString($parent, "int"),
		GetSQLValueString($id, "int"));
	$Result1 = el_dbselect($updateSQL, 0, $Result1);
	el_dbselect("UPDATE cat SET path='" . $new_path . "' WHERE id='" . $id . "'", 0, $pa);
	el_dbselect("UPDATE content SET path='" . $new_path . "' WHERE cat='" . $id . "' AND site_id=".intval($_SESSION['site_id']), 0, $pa);

	$child = el_dbselect("SELECT id FROM cat WHERE parent='" . $id . "' AND site_id=".intval($_SESSION['site_id']), 0, $pa);
	if (el_dbnumrows($child) > 0) {
		$rchild = el_dbfetch($child);
		do {
			el_moveCat($rchild['id'], $id);
		} while ($rchild = el_dbfetch($child));
	}
}

$imenu = 0;
function el_menuadmin()
{//Parent items, first level only
	global $SERVER_NAME;

	$querymenutree = "SELECT * FROM cat WHERE parent=0 AND site_id=".intval($_SESSION['site_id'])." ORDER BY sort ASC";
	$menutree = el_dbselect($querymenutree, 0, $menutree);
	$row_menutree = el_dbfetch($menutree);
	do {
		$parent = $row_menutree['id'];
		$child = el_dbselect("SELECT * FROM cat WHERE parent='$parent' AND site_id=".intval($_SESSION['site_id']), 0, $child);
		?>
		<table border="0" cellpadding="3" cellspacing="0" style="border-bottom:1px solid gray;">

			<tr id="<?php echo $row_menutree['id']; ?>"
			    onMouseOver='document.getElementById("<?php echo $row_menutree['id']; ?>").style.backgroundColor="#E7E7E7"'
			    onMouseOut='document.getElementById("<?php echo $row_menutree['id']; ?>").style.backgroundColor=""'>
				<td><input name="parent" type="radio"
				           value="<?php echo $row_menutree['id']; ?>" <?= ($row_menutree['id'] == $_GET['id']) ? 'disabled=disabled' : '' ?>>
					<b><?php echo $row_menutree['name']; ?></b></td>
			</tr>
			<input type="hidden" name="MM_update" value="edit">
		</table>
		<?
		menuadminchild($parent, 'cat');
	} while ($row_menutree = el_dbfetch($menutree));
	el_dbresultfree($menutree);
}

function menuadminchild($parent, $table)
{//Child Items
	global $imenu, $_SERVER;
	$querymenuchild = "SELECT * FROM cat WHERE parent='$parent' AND site_id=".intval($_SESSION['site_id'])." ORDER BY sort ASC";
	$menuchild = el_dbselect($querymenuchild, 0, $menuchild);
	$row_menuchild = el_dbfetch($menuchild);
	$idchild = $row_menuchild['id'];
	if ($idchild) {//if item is exist...
		$imenu++;
		$parentchild = el_dbselect("SELECT * FROM cat WHERE parent='$idchild' AND site_id=".intval($_SESSION['site_id']), 0, $parentchild);
		echo "<div id=\"menudiv" . $row_menuchild['parent'] . "\" style=\"padding-left:20px;\">\n";
		do {
			?>
			<table border="0" cellpadding="3" cellspacing="0" style="border-bottom:1px solid gray;">
				<tr id="<?php echo $row_menuchild['id']; ?>"
				    onMouseOver='document.getElementById("<?php echo $row_menuchild['id']; ?>").style.backgroundColor="#E7E7E7"'
				    onMouseOut='document.getElementById("<?php echo $row_menuchild['id']; ?>").style.backgroundColor=""'>
					<td><img src="/editor/img/vetka.gif" border=0 align=middle on></td>
					<td><input name="parent" type="radio"
					           value="<?php echo $row_menuchild['id']; ?>" <?= ($row_menuchild['id'] == $_GET['id']) ? 'disabled=disabled' : '' ?>>
					</td>
					<td><?php echo $row_menuchild['name']; ?></td>
				</tr>
				<input type="hidden" name="MM_update" value="edit">
			</table>
			<?
			menuadminchild($row_menuchild['id'], $table);
		} while ($row_menuchild = el_dbfetch($menuchild));
		el_dbresultfree($menuchild);
		echo "</div>\n";
	}
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "editparent")) {
	el_moveCat($_POST['id'], $_POST['parent']);
	echo '<script>top.reloadFrame()</script>';
}

$colname_catmigrate = "1";
if (isset($_GET['id'])) {
	$colname_catmigrate = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}

$query_catmigrate = sprintf("SELECT * FROM cat WHERE id = %s", $colname_catmigrate);
$catmigrate = el_dbselect($query_catmigrate, 0, $catmigrate);
$row_catmigrate = el_dbfetch($catmigrate);
$totalRows_catmigrate = el_dbnumrows($catmigrate);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>Перенос в другой раздел</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link href="style.css" rel="stylesheet" type="text/css">
    <style>
        body{
            padding: 0 15px;
        }
    </style>
</head>

<body>
<h4>Перенос раздела &laquo;<?php echo $row_catmigrate['name']; ?>&raquo; в...</h4>
(веберите родительский раздел)
<?
?>
<form method="POST" action="<?php echo $editFormAction; ?>" name="editparent">
	<input name="parent" type="radio" value="0">
	<b>В главное меню</b> или в... <br>
	<br>
	<? el_menuadmin() ?>
	<input name="id" type="hidden" id="id" value="<?php echo $row_catmigrate['id']; ?>">
	<input type="hidden" name="MM_update" value="editparent">
	<br>
	<input type="submit" name="Submit" value="Перенести" class="but agree">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="button" name="Button" value="Закрыть" class="but close" onClick="top.closeDialog()" style="float: right;">
</form>
</body>
</html>
