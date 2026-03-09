<?
require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php');
//error_reporting(E_ALL);
$requiredUserLevel = array(0, 1);
include($_SERVER['DOCUMENT_ROOT'] . "/editor/secure/secure.php");

//error_reporting(E_ALL);
$requiredUserLevel = array(0, 1, 2);
/*include($_SERVER['DOCUMENT_ROOT']."/editor/secure/secure.php");
$work_mode = (isset($submit)) ? "write" : "read";
el_reg_work($work_mode, $login, $_GET['cat']);*/

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Управление каталогами</title>
	<link href="/editor/style.css" rel="stylesheet" type="text/css">
	<script language="javascript">
		<!--
		function checkAll(form, name, mode) {
			var el = form.elements;
			for (var i = 0; i < el.length; i++) {
				if (el[i].type == 'checkbox' && el[i].name.indexOf(name, 0) != -1) {
					el[i].checked = mode;
				}
			}
		}

		function isChecked(form, name) {
			var el = form.elements;
			var count = 0;
			for (var i = 0; i < el.length; i++) {
				if (el[i].type == 'checkbox' && el[i].name.indexOf(name, 0) != -1) {
					var ch = el[i].checked;
					if (ch == false)count++;
				}
			}
			return (count == 0) ? true : false;
		}

		function check(item_name) {
			var OK = confirm('Вы действительно хотите удалить каталог "' + item_name + '" ?');
			if (OK) {
				return true
			} else {
				return false
			}
		}
		function check_el(item_name) {
			var OK = confirm('Вы действительно хотите удалить поле "' + item_name + '" ?');
			if (OK) {
				return true
			} else {
				return false
			}
		}

		function fill(id, name) {
			var error = 0;
			var errmess = "";
			var id1 = new Array;
			var name1 = new Array;
			id1 = id;
			name1 = name;
			for (var i = 0; i < id1.length; i++) {
				if (document.getElementById(id1[i]).value == "") {
					errmess += 'Заполните поле "' + name1[i] + '"\n';
					error++;
				}
			}
			if (error != 0) {
				alert(errmess);
				return false;
			} else {
				return true;
			}
		}

		function select_size() {
			var tf = document.getElementById("type");
			var sf = document.getElementById("sizefield");
			var ar = document.getElementById("area");
			var op = document.getElementById("oplist");
			var db = document.getElementById("fromdb");
			switch (tf.options[tf.selectedIndex].value) {
				case "textarea":
					ar.style.display = "block";
					sf.style.display = "none";
					op.style.display = "none";
					db.style.display = "none";
					break;
				case "option":
				case "optionlist":
					op.style.display = "block";
					sf.style.display = "block";
					ar.style.display = "none";
					db.style.display = "none";
					break;
				case "list_fromdb":
					db.style.display = "block";
					sf.style.display = "block";
					ar.style.display = "none";
					op.style.display = "none";
					break;
				default:
					sf.style.display = "block";
					ar.style.display = "none";
					op.style.display = "none";
					db.style.display = "none";
			}
		}

		function check_digit(el_id) {
			var cf = document.getElementById(el_id);
			if ((cf.value != '0') || (cf.value != '1') || (cf.value != '2') || (cf.value != '3') || (cf.value != '4') || (cf.value != '5') || (cf.value != '6') || (cf.value != '7') || (cf.value != '8') || (cf.value != '9') || (cf.value != '')) {
				alert("В это поле можно вводить только целые числа!");
				cf.value = "";
			}
		}

		pszFont = "Tahoma,8,,BOLD";

		function MM_findObj(n, d) {
			var p, i, x;
			if (!d) d = document;
			if ((p = n.indexOf("?")) > 0 && parent.frames.length) {
				d = parent.frames[n.substring(p + 1)].document;
				n = n.substring(0, p);
			}
			if (!(x = d[n]) && d.all) x = d.all[n];
			for (i = 0; !x && i < d.forms.length; i++) x = d.forms[i][n];
			for (i = 0; !x && d.layers && i < d.layers.length; i++) x = MM_findObj(n, d.layers[i].document);
			if (!x && d.getElementById) x = d.getElementById(n);
			return x;
		}

		function MM_validateForm() {
			var i, p, q, nm, test, num, min, max, errors = '', args = MM_validateForm.arguments;
			for (i = 0; i < (args.length - 2); i += 3) {
				test = args[i + 2];
				val = MM_findObj(args[i]);
				if (val) {
					nm = val.name;
					if ((val = val.value) != "") {
						if (test.indexOf('isEmail') != -1) {
							p = val.indexOf('@');
							if (p < 1 || p == (val.length - 1)) errors += '- ' + nm + ' must contain an e-mail address.\n';
						} else if (test != 'R') {
							num = parseFloat(val);
							if (isNaN(val)) errors += '- ' + nm + ' должен содержать только целые числа.\n';
							if (test.indexOf('inRange') != -1) {
								p = test.indexOf(':');
								min = test.substring(8, p);
								max = test.substring(p + 1);
								if (num < min || max < num) errors += '- ' + nm + ' must contain a number between ' + min + ' and ' + max + '.\n';
							}
						}
					} else if (test.charAt(0) == 'R') errors += '- ' + nm + ' is required.\n';
				}
			}
			if (errors) alert('Оишбка заполнения формы:\n' + errors);
			document.MM_returnValue = (errors == '');
		}

		function line_over(id) {
			document.getElementById("string" + id).style.backgroundColor = "#DEE7EF";
			document.getElementById("img" + id).src = "/editor/img/leftmenu_arrow.gif";
		}
		function line_out(id) {
			document.getElementById("string" + id).style.backgroundColor = "#CCDCE6";
			document.getElementById("img" + id).src = "/editor/img/spacer.gif";
		}

		function savechange() {
			document.frm1.cat_mode_edit1.value = 'save';
//document.frm1.submit();
		}

		function act1(mode, row) {
			if (mode == "edit") {
				location.href = "catalogs.php?mode=templates&id_temp=" + row;
			}
			if (mode == "del") {
				var OK = confirm("Вы уверены, что хотите удалить шаблон №" + row + " ?");
				if (OK) {
					document.act.action.value = mode;
					document.act.id.value = row;
					document.act.submit();
				}
			}
		}

		function type_form(obj) {
			//var obj=document.getElementById("type");
			var fa = document.getElementById("rowa");
			var fb = document.getElementById("rowb");
			var fс = document.getElementById("rowс");
			var f0 = document.getElementById("row0");
			var f1 = document.getElementById("row1");
			var f2 = document.getElementById("row2");
			var f3 = document.getElementById("row3");

			switch (obj.options[obj.selectedIndex].value) {
				case "Общий дизайн" :
					fa.style.display = "none";
					fb.style.display = "none";
					fс.style.display = "none";
					f0.style.display = "block";
					f1.style.display = "block";
					f2.style.display = "block";
					f3.style.display = "block";
					break;
				case "Дизайн строки":
					fa.style.display = "block";
					fb.style.display = "block";
					fс.style.display = "block";
					f0.style.display = "none";
					f1.style.display = "none";
					f2.style.display = "none";
					f3.style.display = "none";
					break;
			}
		}

		function showhideDiv(name) {
			var d = document.getElementById(name);
			var dc = document.getElementById(name + "_child");
			if (dc.style.display == "none") {
				d.className = "row_block";
				dc.style.marginLeft = 40 + "px";
				dc.style.display = "block";
				document.cookie = "template" + name + "=Y; expires=Thu, 31 Dec 2020 23:59:59 GMT; path=/editor/;";
			} else {
				d.className = "row_none";
				dc.style.display = "none";
				document.cookie = "template" + name + "=N; expires=Thu, 31 Dec 2020 23:59:59 GMT; path=/editor/;";
			}
		}

		function check_type() {
			var f = document.getElementById("list");
			var f1 = document.getElementById("top_list");
			var t = document.getElementById("type");
			var n = document.getElementById("name");

			if (n.value == '') {
				alert("Укажите название шаблона!");
				return false;
			} else if (t.options[t.selectedIndex].value == '') {
				alert("Определите тип шаблона!");
				return false;
			} else if (temp_form.table[0].checked == false && temp_form.table[1].checked == false) {
				alert("Укажите является ли шаблон строковым или табличным!");
				return false;
			} else {
				f.value.toLowerCase();
				if ((f.value.search(/<\s*tr|td\s*[^>]*>/ig) > -1 || f1.value.search(/<\s*tr|td\s*[^>]*>/ig) > -1) && temp_form.table[0].checked == false) {
					var OK = confirm("В шаблоне найдены табличные HTML-тэги.\nВы уверены, что хотите сохранить шаблон, как строковый?");
					if (OK) {
						return true;
					} else {
						return false;
					}
				} else {
					return true;
				}
			}
		}

		<?
		$temp = el_dbselect("SELECT id, `table` FROM catalog_templates", 0, $temp);
		$temps = el_dbfetch($temp);
		if(mysqli_num_rows($temp) > 0){?>
		function check_type_template(obj) {
			var temps = new Array();
			var temSel = document.getElementById("template");
			var temSelSet = document.getElementById("template_set");
			if (obj == temSel) {
				clearObj = temSelSet;
			} else {
				clearObj = temSel;
			}
			<?
			do {
				echo "temps[" . $temps['id'] . "]=" . $temps['table'] . ";
		";
			} while ($temps = el_dbfetch($temp));
			?>
			if (temps[temSel.options[temSel.selectedIndex].value] != temps[temSelSet.options[temSelSet.selectedIndex].value]) {
				alert("Шаблоны должны быть одного типа!");
				clearObj.options[0].selected = true;
			}
		}
		<? }?>


		function new_template() {
			var oldname = document.getElementById("name");
			var newname = prompt("Название нового шаблона:\n\n", oldname.value);
			if (newname != "undefined" && newname != "" && newname != null) {
				if (newname == oldname.value) {
					alert("Шаблон с таким названием уже есть!");
					new_template();
				} else {
					oldname.value = newname;
					temp_form.act_mode.value = 'new';
					temp_form.submit();
				}

			} else {
				return false;
			}
		}

		var sInitColor = null;
		function callColorDlg(field, td) {
			if (sInitColor == null)
				var sColor = dlgHelper.ChooseColorDlg();
			else
				var sColor = dlgHelper.ChooseColorDlg(sInitColor);
			sColor = sColor.toString(16);
			if (sColor.length < 6) {
				var sTempString = "000000".substring(0, 6 - sColor.length);
				sColor = sTempString.concat(sColor);
			}
			document.execCommand("ForeColor", false, sColor);
			sInitColor = sColor;
			document.getElementById(field).value = sInitColor;
			document.getElementById(td).style.backgroundColor = sInitColor;
		}

		function selectOne(obj) {
			var des = document.getElementsByTagName("INPUT");
			for (i = 0; i < des.length; i++) {
				if (des[i].id == "titleRow") {
					des[i].checked = false;
				}
			}
			obj.checked = true;
		}

		function delParam(id, name, field) {
			if (check_el(name)) {
				frm1.cat_mode_edit1.value = 'del';
				frm1.field_id.value = id;
				frm1.field.value = field;
				frm1.submit();
			}
		}
		//-->
	</script>
	<style type="text/css">
		.over {
			background-color: #CCDCE6
		}

		.out {
			background-color: #003399
		}

		.style1 {
			color: #FF0000;
			font-size: 10px
		}
	</style>
</head>

<body>
<form method="post" name="act"><input type="hidden" name="action"><input type="hidden" name="id"></form>
<? if ($_GET['mode'] != "editfield" && !isset($_GET['viewfield'])) { ?>
	<p>
		<a href="params.php">Список характеристик</a>
		| <?= ($_GET['mode'] == 'new') ? '<b>Создать каталог</b>' : '<a href="?mode=new">Создать каталог</a> ' ?>
		| <?= ($_GET['mode'] == 'list') ? '<b>Список каталогов</b>' : '<a href="?mode=list">Список каталогов</a> ' ?>
		| <?= ($_GET['mode'] == 'templates') ? '<b>Шаблоны внешнего вида</b>' : '<a href="?mode=templates">Шаблоны внешнего вида</a>' ?>
	</p>
<? }

//Смотрим список каталогов
if (($_GET['mode'] == "list") || (!isset($_GET['mode'])) && (!isset($_GET['cid'])) && (!isset($_GET['id'])) && (!isset($_GET['viewfield']))) {//Показ списка каталогов

	if ($_POST['cat_mode'] == "del") {//Удаление каталога
		$deleteSQL = sprintf("DELETE FROM catalogs WHERE catalog_id=%s",
			GetSQLValueString($_POST['id_del'], "text"));

		$Result1 = el_dbselect($deleteSQL, 0, $Result1);

		$id_del = $_POST['id_del'];
		$deleteSQL = "DROP TABLE IF EXISTS catalog_" . $id_del . "_data";

		$Result1 = el_dbselect($deleteSQL, 0, $Result1);

		$deleteSQL = sprintf("DELETE FROM modules WHERE name=%s",
			GetSQLValueString($_POST['name'], "text"));

		$Result1 = el_dbselect($deleteSQL, 0, $Result1);

		$deleteSQL = sprintf("DELETE FROM catalog_prop WHERE catalog_id=%s",
			GetSQLValueString($_POST['id_del'], "text"));

		$Result1 = el_dbselect($deleteSQL, 0, $Result1);
		echo "<script>alert('Каталог удален!')</script>";
	}

	if ($_POST['cat_mode'] == "save") {//Сохранение изменений
		$query_cats_check = "SELECT * FROM catalogs WHERE name='" . $_POST['name'] . "' AND catalog_id<>'" . $_POST['id_del'] . "'";
		$cats_check_exist = el_dbselect($query_cats_check, 0, $cats_check_exist);
		$totalRows_cats = mysqli_num_rows($cats_check_exist);
		if ($totalRows_cats > 0) {
			echo "<script>alert('Каталог с таким названием уже есть! Выберите другое название.')</script>";
		} else {
			$updateSQL = sprintf("UPDATE catalogs SET name=%s WHERE catalog_id=%s",
				GetSQLValueString($_POST['name'], "text"),
				GetSQLValueString($_POST['id_del'], "text"));

			$Result1 = el_dbselect($updateSQL, 0, $Result1);
			$mod_check = el_dbselect("SELECT id FROM modules WHERE type='catalog" . $_POST['id_del'] . "'", 0, $mod_check);
			if (mysqli_num_rows($mod_check) > 0) {
				el_dbselect("UPDATE modules SET name='Каталог " . $_POST['name'] . "' WHERE type='catalog" . $_POST['id_del'] . "'", 0, $res);
			} else {
				el_dbselect("INSERT INTO modules (type, name, status, path, sort) VALUES 
				('catalog" . $_POST['id_del'] . "', 'Каталог " . $_POST['name'] . "', 'Y', 'modules/catalog', 1000)", 0, $res);
			}
			echo "<script>alert('Изменения сохранены!')</script>";
		}
	}


	$query_cats = "SELECT * FROM catalogs";
	$cats = el_dbselect($query_cats, 0, $cats);
	$row_cats = el_dbfetch($cats);
	$totalRows_cats = mysqli_num_rows($cats);
	if ($totalRows_cats < 1) {
		echo "<h4 align='center'>Нет ни одного каталога</h4>";
	} else {
		?>
		<table width="80%" border="0" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
			<tr>
				<td align="center" style="background-color:#b1c5d2">Название</td>
				<td colspan="4" align="center" style="background-color:#b1c5d2">Действия</td>
			</tr>
			<? do { ?>
				<tr>
					<form method="post" action="<?= $_SERVER['REQUEST_URI'] ?>">
						<td width="50%" id="string<?= $row_cats['id']; ?>" style="color:#003399"
							onMouseOver='document.getElementById("string<?= $row_cats['id']; ?>").style.backgroundColor="#DEE7EF"'
							onMouseOut='document.getElementById("string<?= $row_cats['id']; ?>").style.backgroundColor="#CCDCE6"'>
							<input name="name" type="text" value="<?= $row_cats['name'] ?>" size="40"></td>
						<td><input name="id_del" type="hidden" id="id_del" value="<?= $row_cats['catalog_id'] ?>">
							<input name="cat_mode" type="hidden" id="cat_mode">
							<a href="?cid=<?= $row_cats['catalog_id'] ?>"><img src="/editor/img/leftmenu_tools.gif"
																			   alt="Настройки каталога" border="0"></a>
						</td>
						<td>
							<a href="?id=<?= $row_cats['catalog_id'] ?>"><img src="/editor/img/menu_edit.gif"
																			  alt="Редактировать структуру" width="23"
																			  height="17" border="0"></a>
						</td>
						<td>
							<input name="submit_save" type="image" id="submit_save2" src="/editor/img/menu_save.gif"
								   alt="Сохранить изменения" onClick="cat_mode.value='save';">
						</td>
						<td>
							<input name="submit_del" type="image" id="submit_del" src="/editor/img/menu_delete.gif"
								   alt="Удалить каталог"
								   onClick="cat_mode.value='del';return check('<?= $row_cats['name'] ?>'); ">
						</td>
					</form>
				</tr>
			<? } while ($row_cats = el_dbfetch($cats)); ?>
		</table>
	<? }
} ?>



<? //Наполняем каталог полями

if ((isset($_GET['id']) || isset($_GET['edit_cat'])) && $_GET['mode'] != "new") {
	if (isset($_GET['id']) && strlen($_GET['id']) > 0) {
		$id_cat = $_GET['id'];
	} else {
		$id_cat = $_GET['new_id'];
	}
	if (strlen($id_cat) < 1) {
		$id_cat = $_POST['catalog_id'];
	}


	$query_cats = "SELECT * FROM catalogs WHERE catalog_id='$id_cat'";
	$cats = el_dbselect($query_cats, 0, $cats);
	$row_cats = el_dbfetch($cats);

	if ($_POST['cat_mode_edit1'] == "del") {
		$deleteSQL = sprintf("DELETE FROM catalog_prop WHERE id=%s",
			GetSQLValueString($_POST['field_id'], "int"));

		$Result1 = el_dbselect($deleteSQL, 0, $Result1);


		$query_catdata = "ALTER TABLE catalog_" . $_POST['catalog_id'] . "_data DROP COLUMN field" . $_POST['field'];
		$catdata = el_dbselect($query_catdata, 0, $catdata);
		echo "<script>alert('Поле удалено!')</script>";

	}

	#############################################################
	/*		$query_save = "SELECT * FROM catalog_options";
		$catsave=el_dbselect($query_save, 0, $catsave);
		$row_catsave = el_dbfetch($catsave);
		do{
	  		$updateSQL = sprintf("UPDATE catalog_prop SET option_id=%s WHERE name=%s",
                       GetSQLValueString($row_catsave['id'], "int"),
					   GetSQLValueString($row_catsave['name'], "text"));
 			echo $row_catsave['name'].'<br>';
  			$Result1=el_dbselect($updateSQL, 0, $Result1);
		}while($row_catsave = el_dbfetch($catsave));*/

	############################################################

	if ($_POST['cat_mode_edit1'] == "save") {

		$query_save = "SELECT * FROM catalog_prop WHERE catalog_id='$id_cat'";
		$catsave = el_dbselect($query_save, 0, $catsave);
		$row_catsave = el_dbfetch($catsave);
		do {
			$updateSQL = sprintf("UPDATE catalog_prop SET name=%s, sort=%s, title=%s, list=%s, detail=%s, search=%s, show_name=%s, inform=%s, `required`=%s, `hidden`=%s, `readonly`=%s WHERE id=%s",
				GetSQLValueString($_POST['name' . $row_catsave['id']], "text"),
				GetSQLValueString($_POST['sort' . $row_catsave['id']], "int"),
				GetSQLValueString(isset($_POST['title' . $row_catsave['id']]) ? "true" : "", "defined", "'1'", "'0'"),
				GetSQLValueString(isset($_POST['list' . $row_catsave['id']]) ? "true" : "", "defined", "'1'", "'0'"),
				GetSQLValueString(isset($_POST['detail' . $row_catsave['id']]) ? "true" : "", "defined", "'1'", "'0'"),
				GetSQLValueString(isset($_POST['search' . $row_catsave['id']]) ? "true" : "", "defined", "'1'", "'0'"),
				GetSQLValueString(isset($_POST['show_name' . $row_catsave['id']]) ? "true" : "", "defined", "'1'", "'0'"),
				GetSQLValueString(isset($_POST['inform' . $row_catsave['id']]) ? "true" : "", "defined", "'1'", "'0'"),
				GetSQLValueString(isset($_POST['required' . $row_catsave['id']]) ? "true" : "", "defined", "'1'", "'0'"),
				GetSQLValueString(isset($_POST['hidden' . $row_catsave['id']]) ? "true" : "", "defined", "'1'", "'0'"),
				GetSQLValueString(isset($_POST['readonly' . $row_catsave['id']]) ? "true" : "", "defined", "'1'", "'0'"),
				GetSQLValueString($row_catsave['id'], "int"));

			$Result1 = el_dbselect($updateSQL, 0, $Result1);
		} while ($row_catsave = el_dbfetch($catsave));
		el_reindex("catalog_" . $id_cat . "_data");
		echo "<script>alert('Изменения сохранены!')</script>";
	}

	if ((isset($_POST['new_element'])) && ($_POST['new_element'] == "1")) { //print_r($_POST);
		for ($i = 0; $i < count($_POST['type']); $i++) {
			$option = el_dbselect("SELECT * FROM catalog_options WHERE id=" . intval($_POST['type'][$i]), 0, $option, 'row');
			$row_cat2 = el_dbselect("SELECT MAX(field), MAX(sort) FROM catalog_prop WHERE catalog_id='" . $_POST['catalog_id'] . "'", 0, $row_cat2, 'row');
			$lastField = $row_cat2['MAX(field)'] + 1;
			$insertSQL = sprintf("INSERT INTO catalog_prop (option_id, name, type, size, cols, rows, sort, title, list, detail, search, show_name,  inform, `required`, `hidden`, `readonly`, catalog_id, field, options, listdb, from_field) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
				GetSQLValueString($option['id'], "int"),
				GetSQLValueString($option['name'], "text"),
				GetSQLValueString($option['type'], "text"),
				GetSQLValueString($option['size'], "int"),
				GetSQLValueString($option['cols'], "int"),
				GetSQLValueString($option['rows'], "int"),
				GetSQLValueString($option['sort'], "int"),
				GetSQLValueString(isset($_POST['title']) ? "true" : "", "defined", "'1'", "'0'"),
				GetSQLValueString(isset($_POST['list']) ? "true" : "", "defined", "'1'", "'0'"),
				GetSQLValueString(isset($_POST['detail']) ? "true" : "", "defined", "'1'", "'0'"),
				GetSQLValueString(isset($_POST['search']) ? "true" : "", "defined", "'1'", "'0'"),
				GetSQLValueString(isset($_POST['show_name']) ? "true" : "", "defined", "'1'", "'0'"),
				GetSQLValueString(isset($_POST['inform']) ? "true" : "", "defined", "'1'", "'0'"),
				GetSQLValueString(isset($_POST['required']) ? "true" : "", "defined", "'1'", "'0'"),
				GetSQLValueString(isset($_POST['hidden' . $row_catsave['id']]) ? "true" : "", "defined", "'1'", "'0'"),
				GetSQLValueString(isset($_POST['readonly' . $row_catsave['id']]) ? "true" : "", "defined", "'1'", "'0'"),
				GetSQLValueString($_POST['catalog_id'], "text"),
				GetSQLValueString($lastField, "int"),
				GetSQLValueString($option['options'], "text"),
				GetSQLValueString($option['listdb'], "text"),
				GetSQLValueString($option['from_field'], "int"));


			$Result1 = el_dbselect($insertSQL, 0, $Result1, 'result', true);

			$name = "field" . $lastField;
			switch ($option['type']) {
				case "integer":
                case 'depend_list':
					$type = "INTEGER";
					break;
				case "textarea":
				case "optionlist":
				case "option":
				case "basic_html":
				case "full_html":
				case 'select':
				case "multi_date":
					$type = "LONGTEXT";
					break;
				case 'calendat':
				case 'curr_date':
					$type = 'DATE';
					break;
				case 'datetime':
					$type = 'DATETIME';
					break;
                case 'time':
                    $type = 'TIME';
					break;
				case "float":
				case "price":
					$type = "DOUBLE";
					break;
				default:
					$type = "TEXT";
					break;
			}

			$query_catdata = "ALTER TABLE catalog_" . $id_cat . "_data ADD `$name` $type NULL COMMENT '".$option['name']."'";
			$catdata = el_dbselect($query_catdata, 0, $catdata);
			el_reindex("catalog_" . $id_cat . "_data");
		}
	}


	$query_cat = "SELECT * FROM catalog_prop WHERE catalog_id='$id_cat' ORDER BY sort";
	$cat = el_dbselect($query_cat, 0, $cat);
	$row_cat = el_dbfetch($cat);
	$paramArray = array();
	if (mysqli_num_rows($cat) > 0) {
		?>
		<form method="post" name="frm1">
		<input name="cat_mode_edit1" type="hidden" id="cat_mode_edit1">
		<input name="field_id" type="hidden" id="field_id">
		<input name="field" type="hidden" id="field">
		<input name="catalog_id" type="hidden" id="catalog_id" value="<?= $id_cat ?>">
		<div align="right"><input type="submit" value="Сохранить изменения" onClick="savechange()" class="but"></div>
		<table border="0" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
			<caption>Свойства каталога <b>"<?= $row_cats['name'] ?>"</b></caption>

			<tr>
				<td align="center" style="background-color:#b1c5d2">Название</td>
				<td align="center" style="background-color:#b1c5d2">Заголовок <img src="/editor/img/help_button.gif"
																				   alt="Кликните для получения справки"
																				   width="12" height="12"
																				   style="cursor:pointer"
																				   onClick="test.TextPopup ('Этот параметр показывает, является ли это поле заголовком записи',pszFont,10,10,-1,-1)">
				</td>
				<td align="center" style="background-color:#b1c5d2">Сортировка <img src="/editor/img/help_button.gif"
																					alt="Кликните для получения справки"
																					width="12" height="12"
																					style="cursor:pointer"
																					onClick="test.TextPopup ('Этот параметр показывает, будет ли производиться сортировка по этому полю',pszFont,10,10,-1,-1)">
				</td>
				<td align="center" style="background-color:#b1c5d2">Поиск <img src="/editor/img/help_button.gif"
																			   alt="Кликните для получения справки"
																			   width="12" height="12"
																			   style="cursor:pointer"
																			   onClick="test.TextPopup ('Этот параметр показывает, будет ли по этому полю производиться расширенный поиск',pszFont,10,10,-1,-1)">
				</td>
				<td align="center" style="background-color:#b1c5d2">В форме <img src="/editor/img/help_button.gif"
																				 alt="Кликните для получения справки"
																				 width="12" height="12"
																				 style="cursor:pointer"
																				 onClick="test.TextPopup ('Этот параметр показывает, будет ли это поле присутствовать в форме ввода на сайте',pszFont,10,10,-1,-1)">
				</td>
				<td align="center" style="background-color:#b1c5d2">Обязательное<br>
					поле<img src="/editor/img/help_button.gif" alt="Кликните для получения справки" width="12"
							 height="12" style="cursor:pointer"
							 onClick="test.TextPopup ('Этот параметр показывает, будет ли это поле обязательным к заполнению',pszFont,10,10,-1,-1)">
				</td>
				<td align="center" style="background-color:#b1c5d2">Скрытое поле</td>
				<td align="center" style="background-color:#b1c5d2">Только чтение</td>
				<td align="center" style="background-color:#b1c5d2">Номер <img src="/editor/img/help_button.gif"
																			   alt="Кликните для получения справки"
																			   width="12" height="12"
																			   style="cursor:pointer"
																			   onClick="test.TextPopup ('Номер необходим, что бы указать в какой последовательностидолжны выводится поля',pszFont,10,10,-1,-1)">
				</td>
				<td align="center" style="background-color:#b1c5d2">Действие</td>
			</tr>
			<? do {
				$paramArray[] = $row_cat['name'];
				?>
				<tr onMouseOver="line_over('<?= $row_cat['id'] ?>')" onMouseOut="line_out('<?= $row_cat['id'] ?>')">
					<td valign="top" id="string<?= $row_cat['id'] ?>"><?
						$chek0 = ($row_cat['title'] == '1') ? "checked" : "";
						$chek = ($row_cat['list'] == 1) ? "checked" : "";
						$chek1 = ($row_cat['search'] == 1) ? "checked" : "";
						$chek2 = ($row_cat['show_name'] == 1) ? "checked" : "";
						$chek3 = ($row_cat['detail'] == 1) ? "checked" : "";
						$chek4 = ($row_cat['inform'] == 1) ? "checked" : "";
						$chek5 = ($row_cat['required'] == 1) ? "checked" : "";
						$chek6 = ($row_cat['hidden'] == 1) ? "checked" : "";
						$chek7 = ($row_cat['readonly'] == 1) ? "checked" : "";
						?>
						<img src="/editor/img/spacer.gif" name="img<?= $row_cat['id'] ?>" width="19" height="17"
							 align="absmiddle" id="img<?= $row_cat['id'] ?>">
						<nobr><input name="name<?= $row_cat['id'] ?>" type="text" id="name<?= $row_cat['id'] ?>"
									 value="<?= $row_cat['name'] ?>" size="30">
						</nobr>
						<div style="text-align:center; font-size:10px; color:#006600">Имя поля:
							field<?= $row_cat['field'] ?></div>
					</td>
					<td align="center"
						valign="top"><?php /*?><input id="titleRow" onClick="selectOne(this)" name="title<?=$row_cat['id']?>" type="radio"  <?=$chek0?>><?php */ ?>
						<input id="titleRow" name="title<?= $row_cat['id'] ?>" type="checkbox" <?= $chek0 ?>></td>
					<td align="center" valign="top"><input name="list<?= $row_cat['id'] ?>" type="checkbox"
														   id="list<?= $row_cat['id'] ?>" <?= $chek ?>></td>
					<td align="center" valign="top"><input name="search<?= $row_cat['id'] ?>" type="checkbox"
														   id="search<?= $row_cat['id'] ?>" <?= $chek1 ?>></td>

					<td align="center" valign="top"><input name="inform<?= $row_cat['id'] ?>" type="checkbox"
														   id="inform<?= $row_cat['id'] ?>" <?= $chek4 ?>></td>
					<td align="center" valign="top"><input name="required<?= $row_cat['id'] ?>" type="checkbox"
														   id="required<?= $row_cat['id'] ?>" <?= $chek5 ?>></td>
					<td align="center" valign="top"><input name="hidden<?= $row_cat['id'] ?>" type="checkbox"
														   id="hidden<?= $row_cat['id'] ?>" <?= $chek6 ?>></td>
					<td align="center" valign="top"><input name="readonly<?= $row_cat['id'] ?>" type="checkbox"
														   id="readonly<?= $row_cat['id'] ?>" <?= $chek7 ?>></td>
					<td align="right" valign="top"><input name="sort<?= $row_cat['id'] ?>" type="text"
														  id="sort<?= $row_cat['id'] ?>"
														  onBlur="MM_validateForm('sort<?= $row_cat['id'] ?>','','NisNum');return document.MM_returnValue"
														  value="<?= $row_cat['sort'] ?>" size="3"></td>
					<td align="center"><input name="submit_del2" type="image" id="submit_del2"
											  src="/editor/img/menu_delete.gif" alt="Удалить"
											  onClick="delParam(<?= $row_cat['id'] ?>, '<?= $row_cat['name'] ?>', <?= $row_cat['field'] ?>)">
					</td>
				</tr>
			<? } while ($row_cat = el_dbfetch($cat)); ?>
			<tr>
				<td colspan="4">&nbsp;</td>
				<td><label for="informTotal">
						<script language="javascript">
							document.writeln('<input type="checkbox" id="informTotal" onClick="checkAll(this.form,\'inform\',this.checked)"');
							if (isChecked(document.frm1, 'inform'))document.writeln(' checked');
							document.writeln('>');
						</script>
						Отметить все</label></td>
				<td><label for="requiredTotal">
						<script language="javascript">
							document.writeln('<input type="checkbox" id="requiredTotal" onClick="checkAll(this.form,\'required\',this.checked)"');
							if (isChecked(document.frm1, 'required'))document.writeln(' checked');
							document.writeln('>');
						</script>
						Отметить все</label></td>
				<td colspan="2">&nbsp;</td>
			</tr>
		</table>
		<div align="right"><input type="submit" value="Сохранить изменения" onClick="savechange()" class="but"></div>
		</form>
	<? } ?>
	<table width="500" border="0" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
		<caption>
			Список характеристик
		</caption>
		<form method="post" action="<?= $_SERVER['REQUEST_URI'] ?>" name="newform">
			<tr>

				<td><input name="cat_action" type="hidden" id="cat_action" value="step2">
					<input name="catalog_id" type="hidden" id="catalog_id" value="<?= $id_cat ?>">
					<?
					$op = el_dbselect("SELECT id, name FROM catalog_options ORDER BY sort", 0, $op);
					$rop = el_dbfetch($op);
					$totalParams = mysqli_num_rows($op);
					if ($totalParams > 0){
					?>
					<label for="checkTotal"><input type="checkbox" id="checkTotal"
												   onClick="checkAll(this.form,'type[]',this.checked)">
						Отметить все</label>
					<hr>
					<div style="width:500px; height:<?= (($totalParams - count($paramArray)) * 25) ?>px; overflow:auto">
						<? $countParams = 0;
						do {
							if (!in_array($rop['name'], $paramArray)) {
								$countParams++;
								?>
								<input type="checkbox" name="type[]" value="<?= $rop['id'] ?>"
									   id="prop<?= $rop['id'] ?>">&nbsp;
								<label for="prop<?= $rop['id'] ?>"><?= $rop['name'] ?></label><br>
								<?
							}
						} while ($rop = el_dbfetch($op));
						if ($countParams == 0) {
							echo 'В этом каталоге использованы все созданные характеристики';
						}
						} else {
							echo '<span style="color:red">Не создана ни одна характеристика.<br>Пожалуйста, перейдите в 
			<a href="params.php">Список характеристик</a><br>и создайте набор характеристик.</span>';
						}
						?>
					</div>
					<br>
					<div id="oplist" style="display:none"><br>
						Впишите пункты списка через точку с запятой, без пробелов.<br>
						<textarea name="options" cols="40" rows="6" id="options"></textarea>
					</div>
					<div id="fromdb" style="display:none"><br>
						<?
						$list_db = el_dbselect("select catalog_id, name from catalogs", 0, $list_db);
						$row_list_db = el_dbfetch($list_db);
						?>
						<nobr>Каталог: <select name="listdb" id="listdb"
											   onChange="field_frame.location.href('catalogs.php?catalog_id='+listdb.options[listdb.selectedIndex].value+'&viewfield'); field_div.style.display='block'">
								<option></option>
								<? do { ?>
									<option
										value="<?= $row_list_db['catalog_id'] ?>"><?= $row_list_db['name'] ?></option>
								<? } while ($row_list_db = el_dbfetch($list_db)); ?>
							</select><input type="hidden" id="from_field" name="from_field"></nobr>
						<br><br>
						<div style="display:none" id="field_div">
							<iframe frameborder="0" width="300" height="30" scrolling="no" id="field_frame"
									name="field_frame" hspace="0" marginheight="0" marginwidth="0" vspace="0"></iframe>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<? if ($countParams > 0) { ?>
						<input type="submit" name="Submit3" value="Добавить" class="but">
						<input name="new_element" type="hidden" id="new_element" value="1">
					<? } ?>
				</td>
			</tr>
		</form>
	</table>
<? }


//Выбираем поля из другого каталога
if (isset($_GET['catalog_id'])) {
	if (isset($_GET['viewfield']) && !isset($_GET['depend'])) {
		$list_field = el_dbselect("select field, name from catalog_prop where catalog_id='" . $_GET['catalog_id'] . "'", 0, $list_field);
		$row_list_field = el_dbfetch($list_field);
		?>
		<div style="background-color:#CCDCE6; width:100%; height:100%; margin:0px">
		Поле: <select name="fromfield" id="fromfield"
							onChange="if(this.options[this.selectedIndex].value!=''){parent.document.getElementById('from_field').value=this.options[this.selectedIndex].value}else{parent.document.getElementById('from_field').value=''}">
				<option></option>
				<? do { ?>
					<option
						value="<?= $row_list_field['field'] ?>" <?= (isset($_GET['from_field']) && $_GET['from_field'] == $row_list_field['field']) ? "selected" : "" ?>><?= $row_list_field['name'] ?></option>
				<? } while ($row_list_field = el_dbfetch($list_field)); ?>
			</select>
			<input name="field2" type="hidden" id="field2" value="<?= $row_cat2['MAX(field)'] + 1 ?>">
		</div>
	<? }


//Выбираем поля из другого родительского каталога
	if (isset($_GET['depend'])) {
		$list_field = el_dbselect("select field, name from catalog_prop where catalog_id='" . $_GET['catalog_id'] . "'", 0, $list_field);
		$row_list_field = el_dbfetch($list_field);
		?>
		<div style="background-color:#CCDCE6; width:100%; height:100%; margin:0px">
		<? if (isset($_GET['from_field'])){ ?>
		Родительское поле: <select name="fromfield" id="fromfield"
										 onChange="if(this.options[this.selectedIndex].value!=''){parent.document.getElementById('from_field').value=this.options[this.selectedIndex].value}else{parent.document.getElementById('from_field').value=''}">
			<option></option>
			<? do { ?>
				<option
					value="<?= $row_list_field['field'] ?>" <?= (isset($_GET['from_field']) && $_GET['from_field'] == $row_list_field['field']) ? "selected" : "" ?>><?= $row_list_field['name'] ?></option>
			<? } while ($row_list_field = el_dbfetch($list_field)); ?>
		</select>
	<? }
		if (isset($_GET['child_field'])) {
			$ch = el_dbselect("SELECT name, field FROM catalog_prop WHERE catalog_id='" . $_GET['catalog_id'] . "'", 0, $ch, 'result', true);
			$rch = el_dbfetch($ch);
			$op = el_dbselect("SELECT name, field FROM catalog_prop WHERE type='list_fromdb'", 0, $op, 'result', true);
			$rop = el_dbfetch($op);
			?>
			Дочерняя характеристика: <select name="childopt" id="childopt"
												   onChange="if(this.options[this.selectedIndex].value!=''){parent.document.getElementById('child_opt').value=this.options[this.selectedIndex].value}else{parent.document.getElementById('child_opt').value=''}">
					<option></option>
					<? do { ?>
						<option
							value="<?= $rop['field'] ?>" <?= (isset($_GET['child_opt']) && $_GET['child_opt'] == $rop['field']) ? "selected" : "" ?>><?= $rop['name'] ?></option>
					<? } while ($rop = el_dbfetch($op)); ?>
				</select>
				<br>
				Ключевое поле: <select name="childfield" id="childfield"
											 onChange="if(this.options[this.selectedIndex].value!=''){parent.document.getElementById('child_field').value=this.options[this.selectedIndex].value}else{parent.document.getElementById('child_field').value=''}">
						<option></option>
						<? do { ?>
							<option
								value="<?= $rch['field'] ?>" <?= (isset($_GET['child_field']) && $_GET['child_field'] == $rch['field']) ? "selected" : "" ?>><?= $rch['name'] ?></option>
						<? } while ($rch = el_dbfetch($ch)); ?>
					</select>


					<input name="field2" type="hidden" id="field2" value="<?= $row_cat2['MAX(field)'] + 1 ?>">
			</div>
		<? }
	}
}

//Редактируем характиристики поля в новом окне
if ($_GET['mode'] == "editfield") {
	if (isset($_POST['update'])) {
		$id_cat = $_GET['catalog'];
		$query_opt = "SELECT field, catalog_id FROM catalog_prop WHERE option_id='" . intval($_GET['field']) . "'";
		$op = el_dbselect($query_opt, 0, $op);
		$opt = el_dbfetch($op);

		if (strlen(trim($_POST['listdb_child'])) > 0) $_POST['options'] = $_POST['listdb_child'];
		$updateSQL = sprintf("UPDATE catalog_options SET type=%s, size=%s, cols=%s, rows=%s, options=%s, listdb=%s, from_field=%s, to_field=%s, catalog_id=%s, field=%s WHERE id=%s",
			GetSQLValueString($_POST['type'], "text"),
			GetSQLValueString($_POST['size'], "int"),
			GetSQLValueString($_POST['cols'], "int"),
			GetSQLValueString($_POST['rows'], "int"),
			GetSQLValueString($_POST['options'], "text"),
			GetSQLValueString($_POST['listdb'], "text"),
			GetSQLValueString($_POST['from_field'], "int"),
			GetSQLValueString($_POST['child_field'], "int"),
			GetSQLValueString($_POST['listdb_child'], "text"),
			GetSQLValueString($_POST['child_opt'], "int"),
			GetSQLValueString($_GET['field'], "int"));

		$Result1 = el_dbselect($updateSQL, 0, $Result1/*, 'result', true, true*/);

		$updateSQL = sprintf("UPDATE catalog_prop SET type=%s, size=%s, cols=%s, rows=%s, options=%s, listdb=%s, from_field=%s, to_field=%s, default_value=%s WHERE option_id=%s",
			GetSQLValueString($_POST['type'], "text"),
			GetSQLValueString($_POST['size'], "int"),
			GetSQLValueString($_POST['cols'], "int"),
			GetSQLValueString($_POST['rows'], "int"),
			GetSQLValueString($_POST['options'], "text"),
			GetSQLValueString($_POST['listdb'], "text"),
			GetSQLValueString($_POST['from_field'], "int"),
			GetSQLValueString($_POST['child_field'], "int"),
			GetSQLValueString($_POST['child_opt'], "int"),
			GetSQLValueString($_GET['field'], "int"));
		el_dbselect($updateSQL, 0, $res, 'result', true/*, true*/);

		do {
			switch ($_POST['type']) {
                case "integer":
                case 'list_fromdb':
                case 'depend_list':
                    $type = "INTEGER";
                    break;
                case "textarea":
                case "optionlist":
                case "option":
                case "basic_html":
                case "full_html":
                case 'select':
                case "multi_date":
                    $type = "LONGTEXT";
                    break;
                case 'calendat':
                case 'curr_date':
                    $type = 'DATE';
                    break;
                case 'datetime':
                    $type = 'DATETIME';
                    break;
                case "float":
                case "price":
                    $type = "DOUBLE";
                    break;
                default:
                    $type = "TEXT";
                    break;
			}

			$query_catdata = "ALTER TABLE catalog_" . $opt['catalog_id'] . "_data MODIFY field" . $opt['field'] . " $type NULL";
			$catdata = el_dbselect($query_catdata, 0, $catdata);
		} while ($opt = el_dbfetch($op));
		el_clearcache('catalog', '');

		echo "<script>alert('Изменения сохранены!')</script>";
	}

	$query_cats = "SELECT * FROM catalog_options WHERE id='" . intval($_GET['field']) . "'";
	$cats = el_dbselect($query_cats, 0, $cats);
	$row_cat = el_dbfetch($cats);

	?>
	<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
		<caption><?= "Поле: &laquo;" . $row_cat['name'] . "&raquo;" ?></caption>
		<form method="post">
			<tr>
				<td>Тип поля формы в административной части:<span style="background-color:#b1c5d2"><img
							src="/editor/img/help_button.gif" alt="Кликните для получения справки" width="12"
							height="12" style="cursor:pointer"
							onClick="test.TextPopup ('Здесь можно задать тип поля в форме добавления позиции в каталог в административной части',pszFont,10,
							10,-1,-1)"></span>
				</td>
				<td><select name="type" id="type">
						<?
						include $_SERVER['DOCUMENT_ROOT'] . '/editor/modules/catalog/props_array.php';
						while (list($key, $val) = each($props_array)) {
							($row_cat['type'] == $key) ? $sel = 'selected' : $sel = '';
							echo '<option value="' . $key . '" ' . $sel . '>' . $val . '</option>' . "\n";
						}
						?>
					</select></td>
			</tr>
			<tr>
				<td>Размер поля формы:<span style="background-color:#b1c5d2"><img src="/editor/img/help_button.gif"
																				  alt="Кликните для получения справки"
																				  width="12" height="12"
																				  style="cursor:pointer"
																				  onClick="test.TextPopup ('Этот параметр указывает размер поля формы',pszFont,10,10,-1,-1)"></span>
				</td>
				<td><? if ($row_cat['type'] == "textarea" || $row_cat['type'] == "full_html" || $row_cat['type'] == "basic_html") { ?>
						Ширина:
						<input name="cols" type="text" id="cols<?= $row_cat['id'] ?>" value="<?= $row_cat['cols'] ?>"
							   size="3"
							   onBlur="MM_validateForm('cols<?= $row_cat['id'] ?>','','NisNum');return document.MM_returnValue">
						</nobr><br>
						<nobr>Высота: <input name="rows" type="text" id="rows<?= $row_cat['id'] ?>"
											 value="<?= $row_cat['rows'] ?>" size="3"
											 onBlur="MM_validateForm('rows<?= $row_cat['id'] ?>','','NisNum');return document.MM_returnValue"><? } else { ?>
						<input name="size" type="text" id="size<?= $row_cat['id'] ?>" value="<?= $row_cat['size'] ?>"
							   size="3"
							   onBlur="MM_validateForm('size<?= $row_cat['id'] ?>','','NisNum');return document.MM_returnValue"></nobr><? } ?>
				</td>
			</tr>


			<? if ($row_cat['type'] == "option" || $row_cat['type'] == "optionlist") { ?>
				<tr>
					<td>Опции:<span style="background-color:#b1c5d2"><img src="/editor/img/help_button.gif"
																		  alt="Кликните для получения справки"
																		  width="12" height="12" style="cursor:pointer"
																		  onClick="test.TextPopup ('Здесь указываются пункты списка. Весь список будет показан в административной части, а на сайте только выбранные в административной части пункты.',pszFont,10,10,-1,-1)"></span>
					</td>
					<td>Впишите пункты списка через точку с запятой, без пробелов.<br>
						<textarea name="options" cols="40" rows="6" id="options"><?= $row_cat['options'] ?></textarea>
					</td>
				</tr>
			<? }


			if ($row_cat['type'] == "list_fromdb" || $row_cat['type'] == "propTable") { ?>
				<tr>
					<td>
						<?
						$list_db = el_dbselect("select catalog_id, name from catalogs", 0, $list_db, 'result', true);
						$row_list_db = el_dbfetch($list_db);
						?>
						<nobr>Каталог: <select name="listdb" id="listdb"
											   onChange="field_frame.location.href('catalogs.php?catalog_id='+listdb.options[listdb.selectedIndex].value+'&viewfield&from_field=<?= $row_cat['from_field'] ?>')">
								<option></option>
								<? do { ?>
									<option
										value="<?= $row_list_db['catalog_id'] ?>" <?= ($row_list_db['catalog_id'] == $row_cat['listdb']) ? "selected" : "" ?>><?= $row_list_db['name'] ?></option>
								<? } while ($row_list_db = el_dbfetch($list_db)); ?>
							</select><input type="hidden" id="from_field" name="from_field"></nobr>
					</td>
					<td>
						<script language="javascript">
						</script>
						<iframe frameborder="0" width="300" height="30" scrolling="no" id="field_frame"
								name="field_frame" hspace="0" marginheight="0" marginwidth="0" vspace="0"
								src="catalogs.php?catalog_id=<?= $row_cat['listdb'] ?>&viewfield&from_field=<?= $row_cat['from_field'] ?>"></iframe>
					</td>
				</tr>
			<? }

			if ($row_cat['type'] == "depend_list") { ?>
				<tr>
					<td>
						<?
						$list_db = el_dbselect("select catalog_id, name from catalogs", 0, $list_db, 'result', true);
						$row_list_db = el_dbfetch($list_db);
						?>
						<nobr>Родительский каталог: <select name="listdb" id="listdb"
															onChange="field_frame.location.href('catalogs.php?catalog_id='+listdb.options[listdb.selectedIndex].value+'&viewfield&depend&from_field=<?= $row_cat['from_field'] ?>')">
								<option></option>
								<? do { ?>
									<option
										value="<?= $row_list_db['catalog_id'] ?>" <?= ($row_list_db['catalog_id'] == $row_cat['listdb']) ? "selected" : "" ?>><?= $row_list_db['name'] ?></option>
								<? } while ($row_list_db = el_dbfetch($list_db)); ?>
							</select>
							<input type="hidden" id="from_field" name="from_field"
								   value="<?= $row_cat['from_field'] ?>">
							<input type="hidden" id="child_opt" name="child_opt" value="<?= $row_cat['field'] ?>">
							<input type="hidden" id="child_field" name="child_field"
								   value="<?= $row_cat['to_field'] ?>"></nobr>
					</td>
					<td>
						<script language="javascript">
						</script>
						<iframe frameborder="0" width="300" height="30" scrolling="no" id="field_frame"
								name="field_frame" hspace="0" marginheight="0" marginwidth="0" vspace="0"
								src="catalogs.php?catalog_id=<?= $row_cat['listdb'] ?>&viewfield&depend&from_field=<?= $row_cat['from_field'] ?>"></iframe>
					</td>
				</tr>
				<tr>
					<td>
						<?
						$list_db = el_dbselect("select catalog_id, name from catalogs", 0, $list_db);
						$row_list_db = el_dbfetch($list_db);
						?>
						<nobr>Дочерний каталог: <select name="listdb_child" id="listdb_child"
														onChange="field_frame_child.location.href('catalogs.php?catalog_id='+listdb_child.options[listdb_child.selectedIndex].value+'&viewfield&depend&child_field=<?= $row_cat['to_field'] ?>&child_opt=<?= $row_cat['default_value'] ?>')">
								<option></option>
								<? do { ?>
									<option
										value="<?= $row_list_db['catalog_id'] ?>" <?= ($row_list_db['catalog_id'] == $row_cat['options']) ? "selected" : "" ?>><?= $row_list_db['name'] ?></option>
								<? } while ($row_list_db = el_dbfetch($list_db)); ?>
							</select></nobr>
					</td>
					<td>
						<script language="javascript">
						</script>
						<iframe frameborder="0" width="300" height="60" scrolling="no" id="field_frame_child"
								name="field_frame_child" hspace="0" marginheight="0" marginwidth="0" vspace="0"
								src="catalogs.php?catalog_id=<?= $row_cat['options'] ?>&viewfield&depend&child_field=<?= $row_cat['to_field'] ?>&child_opt=<?= $row_cat['field'] ?>"></iframe>
					</td>
				</tr>

			<? } ?>

			<tr>
				<td><input type="submit" name="Submit4" value="Сохранить" class="but">
					<input name="update" type="hidden" id="update" value="1"></td>
				<td align="right"><input type="button" name="Submit5" value="Закрыть" onClick="window.close()"
										 class="but"></td>
			</tr>
		</form>
	</table>
<? }


//Создаем новый каталог

if ($_GET['mode'] == "new") {

	$new_id = $_POST['catalog_id'];
	$fflag = 1;
	if ($_POST['cat_action'] == "step1") {


		$query_cats_check = "SELECT * FROM catalogs WHERE name='" . $_POST['name'] . "'";
		$cats_check = el_dbselect($query_cats_check, 0, $cats_check);
		$totalRows_cats = mysqli_num_rows($cats_check);
		if ($totalRows_cats > 0) {
			echo "<script>alert('Каталог с таким названием уже есть! Выберите другое название.'); location.href='" . $_SERVER['HTTP_REFERER'] . "'</script>";
			$err = 1;
		}

		$query_cats_check = "SELECT * FROM catalogs WHERE catalog_id='" . $_POST['catalog_id'] . "'";
		$cats_check = el_dbselect($query_cats_check, 0, $cats_check);
		$totalRows_cats = mysqli_num_rows($cats_check);
		if ($totalRows_cats > 0) {
			echo "<script>alert('Таблица с таким именем в базе данных уже есть! Выберите другое имя.'); location.href='" . $_SERVER['HTTP_REFERER'] . "'</script>";
			$err = 1;
		}
		if ($err != 1) {
			$new_table = "catalog_" . $_POST['catalog_id'] . "_data";
			$query_catdata = "CREATE TABLE $new_table (
		`id` int(11) NOT NULL auto_increment,
		`site_id` INT NULL,
		`cat` text,
		`active` BOOL NOT NULL,
		`sort` int(11) NOT NULL default '0',
		`goodid` INT NULL,
		`path` text NOT NULL,
		`timestamp` int(20) NOT NULL default '0',
		UNIQUE KEY `id` (`id`)
		)
		ENGINE = MYISAM";
			el_dbselect($query_catdata, 0, $res, 'result', true);//$dbconn) or die("Не могу создать таблицу $new_table! Ошибка: ".mysqli_error()()

			if (strlen($_FILES['template']['name']) > 0) {
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/tmpl/catalog/" . $_FILES['template']['name'])) {
					echo "<script>alert('Шаблон с названием файла \"" . $_FILES['template']['name'] . "\" уже есть!')</script>";
					$fflag = 0;
				} else {
					if (!move_uploaded_file($_FILES['template']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . "/tmpl/" . $_FILES['template']['name'])) {
						echo "<script>alert('Не удалось закачать файл шаблона общего списка!\\nВозможно, не настроен доступ к папке \"/tmpl/catalog\".')</script>";
						$fflag = 0;
					}
				}
			}
			if (strlen($_FILES['template_d']['name']) > 0) {
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/tmpl/catalog/" . $_FILES['template_d']['name'])) {
					echo "<script>alert('Шаблон с названием файла \"" . $_FILES['template_d']['name'] . "\" уже есть!')</script>";
					$fflag = 0;
				} else {
					if (!move_uploaded_file($_FILES['template_d']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . "/tmpl/catalog/" . $_FILES['template_d']['name'])) {
						echo "<script>alert('Не удалось закачать файл шаблона детального описания!\\nВозможно, не настроен доступ к папке \"/tmpl/catalog\".')</script>";
						$fflag = 0;
					}
				}
			}
			(strlen($_FILES['templatef']['name']) > 0) ? $ftemplate = $_FILES['templatef']['name'] : $ftemplate = $_POST['templatef'];
			(strlen($_FILES['template_df']['name']) > 0) ? $ftemplate_d = $_FILES['template_df']['name'] : $ftemplate_d = $_POST['template_df'];
			if ($fflag != 0) {
				$insertSQL = sprintf("INSERT INTO catalogs (`name`, catalog_id, lines_per_pages, cols_per_pages, modul_top, modul_bot, small_size, big_size, big_size_h, currency, sort_tab, shop, meta, template, template_set, ftemplate, ftemplate_d) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
					GetSQLValueString($_POST['name'], "text"),
					GetSQLValueString($_POST['catalog_id'], "text"),
					GetSQLValueString($_POST['lines_per_pages'], "int"),
					GetSQLValueString($_POST['cols_per_pages'], "int"),
					GetSQLValueString($_POST['modul_top'], "text"),
					GetSQLValueString($_POST['modul_bot'], "text"),
					GetSQLValueString($_POST['small_size'], "int"),
					GetSQLValueString($_POST['big_size'], "int"),
                    GetSQLValueString($_POST['big_size_h'], "int"),
					GetSQLValueString($_POST['currency'], "text"),
					GetSQLValueString($_POST['sort_tab'], "text"),
					GetSQLValueString(isset($_POST['shop']) ? "true" : "", "defined", "'1'", "'0'"),
					GetSQLValueString(isset($_POST['meta']) ? "true" : "", "defined", "'1'", "'0'"),
					GetSQLValueString($_POST['template'], "int"),
					GetSQLValueString($_POST['template_set'], "int"),
					GetSQLValueString($ftemplate, "text"),
					GetSQLValueString($ftemplate_d, "text"));
				el_dbselect($insertSQL, 0, $res);

				$insertSQL1 = sprintf("INSERT INTO modules (`type`, `name`, `status`, `path`, `sort`) VALUES (%s, %s, %s, %s, %s)",
					GetSQLValueString("catalog" . $new_id, "text"),
					GetSQLValueString('Каталог ' . $_POST['name'], "text"),
					GetSQLValueString("Y", "text"),
					GetSQLValueString("modules/catalog", "text"),
					GetSQLValueString($_POST['sort_module'], "int"));
				el_dbselect($insertSQL1, 0, $res);

				/*$insertSQL = "INSERT INTO catalog_prop (name, type, size, cols, rows, sort, list, detail, search, show_name, catalog_id, field, options) VALUES ('Название', 'text', 40, 0, 0, 0, 1, 0, 1, 0, ".GetSQLValueString($_POST['catalog_id'], 'text').", 1, 'NULL'), ('Описание', 'textarea', 0, 40, 5, 1, 1, 0, 1, 0, ".GetSQLValueString($_POST['catalog_id'], 'text').", 2, 'NULL')";
		  el_dbselect($insertSQL, 0, $res);
		  */
				echo "<script>alert('Теперь добавьте нужные именно для этого каталога характеристики.');location.href='catalogs.php?id=$new_id'</script>";
			}
		}
	}
	?>
	</p>


<? //Создаем новый каталог (Шаг 2), задаем параметры каталога
if (($_POST['cat_action'] != "step1") && ($_POST['cat_action'] != "step2") && ($_POST['cat_action'] != "step3")){
?>
	<script language="javascript">
		function selectType(typeStr) {
			var tr1 = document.getElementById('row1');
			var tr2 = document.getElementById('row2');
			var tr3 = document.getElementById('row3');
			var tr4 = document.getElementById('row4');
			if (typeStr == 'file') {
				tr1.style.display = 'block';
				tr2.style.display = 'block';
				tr3.style.display = 'none';
				tr4.style.display = 'none';
			} else if (typeStr == 'string') {
				tr1.style.display = 'none';
				tr2.style.display = 'none';
				tr3.style.display = 'block';
				tr4.style.display = 'block';
			}
			document.cookie = "idshow[cat_template]=" + typeStr + "; expires=Thu, 31 Dec 2020 23:59:59 GMT; path=/editor/;";
		}
	</script>
	<form action="<?= $_SERVER['REQUEST_URI'] ?>&id" method="post" enctype="multipart/form-data"
		  onSubmit="return fill(['name','catalog_id'],['Название каталога','Имя таблицы данных'])">
		<table width="80%" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
			<caption>
				Новый каталог
			</caption>
			<tr>
				<td>Название каталога:</td>
				<td>(<span class="style1">название должно быть уникальным</span>)<br>
					<input name="name" type="text" id="name" size="50">
					<input name="cat_action" type="hidden" id="cat_action" value="step1">
					<input name="sort_module" type="hidden" id="sort_module" value="1000">
					<input name="sort_tab" type="hidden" id="sort_tab" value="id"></td>
			</tr>
			<tr>
				<td width="42%">Имя таблицы <font color="#FF0000" size="1">(по-английски)</font> <img
						src="/editor/img/help_button.gif" alt="Кликните для получения справки" width="12" height="12"
						style="cursor:pointer"
						onClick="test.TextPopup ('В этом поле указывается имя таблицы в базе данных английскими буквами. Например, automobiles или apartments',pszFont,10,10,-1,-1)">
				</td>
				<td><input name="catalog_id" type="text" id="catalog_id" size="30"></td>
			</tr>
			<tr>
				<td>Количество позиций на странице:</td>
				<td><input name="lines_per_pages" type="text" id="lines_per_pages" value="15" size="5"></td>
			</tr>
			<tr>
				<td>Количество столбцов в списке:</td>
				<td><input name="cols_per_pages" type="text" id="cols_per_pages" value="1" size="5"></td>
			</tr>
			<tr>
				<?

				$query_modules = "SELECT * FROM modules WHERE status='Y' ORDER BY sort ASC";
				$modules = el_dbselect($query_modules, 0, $modules);
				$row_modules = el_dbfetch($modules);
				$totalRows_modules = mysqli_num_rows($modules);
				?>
				<? /*
    <td>Модуль сверху: </td>
    <td><select name="modul_top" id="modul_top">
	<option value=""> </option>
	<?php do { ?>
          <option value="<?php echo $row_modules['type']?>"><?php echo $row_modules['name']?></option>
          <?php
} while ($row_modules = el_dbfetch($modules));
  $rows = mysqli_num_rows($modules);
  if($rows > 0) {
      mysqli_data_seek($modules, 0);
	  $row_modules = el_dbfetch($modules);
  }
?>
    </select>    </td>
  </tr>

  <tr>
    <td>Модуль снизу: </td>
    <td><select name="modul_bot" id="modul_bot">
	<option value=""> </option>
      <?php do { ?>
      <option value="<?php echo $row_modules['type']?>"><?php echo $row_modules['name']?></option>
      <?php
} while ($row_modules = el_dbfetch($modules));
  $rows = mysqli_num_rows($modules);
  if($rows > 0) {
      mysqli_data_seek($modules, 0);
	  $row_modules = el_dbfetch($modules);
  }
?>
    </select></td>
   </tr>
   */ ?>
			<tr>
				<td>Ширина маленькой картинки:</td>
				<td><input name="small_size" type="text" id="small_size" value="120" size="5">
					пикселей
				</td>
			</tr>
			<tr>
				<td>Ширина большой картинки:</td>
				<td><input name="big_size" type="text" id="big_size" value="400" size="5">
					пикселей
				</td>
			</tr>
            <tr>
                <td>Высота большой картинки:</td>
                <td><input name="big_size_h" type="text" id="big_size_h" value="400" size="5">
                    пикселей
                </td>
            </tr>
			<? /*
  <tr>
    <td>Функции магазина:</td>
    <td><input name="shop" type="checkbox" id="shop" <?=$chek_shop?>>
      <? ($row_cat['shop']==1)?$chek_shop="checked":$chek_shop="";?> </td>
  </tr>

  <tr>
    <td>Обозначение валюты:</td>
    <td><input name="currency" type="text" id="currency" value="руб." size="10">
      (например, $ или у.е. или руб.) </td>
  </tr>
  */
			?>
			<tr>
				<td colspan="2">
					<center>Использовать шаблоны:<br>
						<label for="tfiles"><input type="radio" id="tfiles" name="type_template" value="file"
												   onClick="selectType('file')" <?= ($_COOKIE['idshow']['cat_template'] == 'file') ? 'checked' : '' ?>>
							В виде файлов</label>&nbsp;&nbsp;&nbsp;&nbsp;
						<label for="tstring"><input type="radio" id="tstring" name="type_template" value="string"
													onClick="selectType('string')" <?= ($_COOKIE['idshow']['cat_template'] == 'string') ? 'checked' : '' ?>>
							В виде текста</label></center>
				</td>
			</tr>
			<tr id="row1" style="display:<?= ($_COOKIE['idshow']['cat_template'] == 'file') ? 'block' : 'none' ?>">
				<td>Шаблон дизайна общего списка:</td>
				<td><? el_fileSelect('templatef', '', '/tmpl/catalog', 'php', '') ?><br>
					Закачать, если еще нет на сервере: <input name="templatef" type="file" id="templatef"></td>
			</tr>
			<tr id="row2" style="display:<?= ($_COOKIE['idshow']['cat_template'] == 'file') ? 'block' : 'none' ?>">
				<td>Шаблон дизайна подробного описания:</td>
				<td><? el_fileSelect('template_df', '', '/tmpl/catalog', 'php', '') ?><br>
					Закачать, если еще нет на сервере: <input name="template_df" type="file" id="template_df"></td>
			</tr>

			<tr id="row3" style="display:<?= ($_COOKIE['idshow']['cat_template'] == 'string') ? 'block' : 'none' ?>">
				<td>Шаблон общего дизайна:</td>
				<td>
					<select name="template" id="template" onChange="check_type_template(this)">
						<option></option>
						<?
						$tem = el_dbselect("SELECT id, name, `table` FROM catalog_templates WHERE type='Общий дизайн'", 0, $tem);
						$templ = el_dbfetch($tem);
						if (mysqli_num_rows($tem) > 0) {
							do {
								($templ['table'] == 1) ? $templType = 'Табличный' : $templType = 'Строчный';
								($row_catalogs['template'] == $templ['id']) ? $sel = ' selected' : $sel = '';
								echo "<option value='" . $templ['id'] . "'$sel>" . $templ['name'] . " [" . $templType . "]</option>";
							} while ($templ = el_dbfetch($tem));
						}
						?>
					</select></td>
			</tr>
			<tr id="row4" style="display:<?= ($_COOKIE['idshow']['cat_template'] == 'string') ? 'block' : 'none' ?>">
				<td>Шаблон дизайна строк:</td>
				<td>
					<select name="template_set" id="template_set" onChange="check_type_template(this)">
						<option></option>
						<?
						$tem = el_dbselect("SELECT id, name, `table` FROM catalog_templates WHERE type='Дизайн строки'", 0, $tem);
						$templ = el_dbfetch($tem);
						$templType = '';
						if (mysqli_num_rows($tem) > 0) {
							do {
								($templ['table'] == 1) ? $templType = 'Табличный' : $templType = 'Строчный';
								($row_catalogs['template_set'] == $templ['id']) ? $sel = ' selected' : $sel = '';
								echo "<option value='" . $templ['id'] . "'$sel>" . $templ['name'] . " [" . $templType . "]</option>";
							} while ($templ = el_dbfetch($tem));
						}
						?>
					</select></td>
			</tr>
			<tr>
				<td colspan="2" align="center"><input type="submit" name="Submit" value=" Создать " class="but"></td>
			</tr>
		</table>
	</form>
<? }
} ?>



<? // Редактируем настройки каталога
if (isset($_GET['cid'])) {

	if (isset($_POST['edit'])) {
		$fflag = 1;
		if (strlen($_FILES['templatef']['name']) > 0) {
			if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/tmpl/catalog/" . $_FILES['templatef']['name'])) {
				echo "<script>alert('Шаблон с названием файла \"" . $_FILES['templatef']['name'] . "\" уже есть!')</script>";
				$fflag = 0;
			} else {
				if (!move_uploaded_file($_FILES['templatef']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . "/tmpl/catalog/" . $_FILES['templatef']['name'])) {
					echo "<script>alert('Не удалось закачать файл шаблона общего списка!\\nВозможно, не настроен доступ к папке \"/tmpl/catalog\".')</script>";
					$fflag = 0;
				}
			}
		} else {
			$_FILES['templatef']['name'] = $_POST['ftemplate'];
		}
		if (strlen($_FILES['template_df']['name']) > 0) {
			if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/tmpl/catalog/" . $_FILES['template_df']['name'])) {
				echo "<script>alert('Шаблон с названием файла \"" . $_FILES['template_df']['name'] . "\" уже есть!')</script>";
				$fflag = 0;
			} else {
				if (!move_uploaded_file($_FILES['template_df']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . "/tmpl/catalog/" . $_FILES['template_df']['name'])) {
					echo "<script>alert('Не удалось закачать файл шаблона детального описания!\\nВозможно, не настроен доступ к папке \"/tmpl/catalog\".')</script>";
					$fflag = 0;
				}
			}
		} else {
			$_FILES['template_df']['name'] = $_POST['ftemplate_d'];
		}
		(strlen($_FILES['templatef']['name']) > 0) ? $ftemplate = $_FILES['templatef']['name'] : $ftemplate = $_POST['templatef'];
		(strlen($_FILES['template_df']['name']) > 0) ? $ftemplate_d = $_FILES['template_df']['name'] : $ftemplate_d = $_POST['template_df'];
		if ($fflag != 0) {
			$updateSQL = sprintf("UPDATE catalogs SET name=%s, lines_per_pages=%s, cols_per_pages=%s, modul_top=%s, modul_bot=%s, small_size=%s, big_size=%s, big_size_h=%s, currency=%s, sort_tab=%s, sort_tab_s=%s, shop=%s, meta=%s, template=%s, template_set=%s, ftemplate=%s, ftemplate_d=%s, is_register=%s, in_search=%s WHERE catalog_id=%s",
				GetSQLValueString($_POST['name'], "text"),
				GetSQLValueString($_POST['lines_per_pages'], "int"),
				GetSQLValueString($_POST['cols_per_pages'], "int"),
				GetSQLValueString($_POST['modul_top'], "text"),
				GetSQLValueString($_POST['modul_bot'], "text"),
				GetSQLValueString($_POST['small_size'], "int"),
				GetSQLValueString($_POST['big_size'], "int"),
                GetSQLValueString($_POST['big_size_h'], "int"),
				GetSQLValueString($_POST['currency'], "text"),
				GetSQLValueString($_POST['sort_tab'], "text"),
				GetSQLValueString($_POST['sort_tab_s'], "text"),
				GetSQLValueString(isset($_POST['shop']) ? "true" : "", "defined", "'1'", "'0'"),
				GetSQLValueString(isset($_POST['meta']) ? "true" : "", "defined", "'1'", "'0'"),
				GetSQLValueString($_POST['template'], "int"),
				GetSQLValueString($_POST['template_set'], "int"),
				GetSQLValueString($ftemplate, "text"),
				GetSQLValueString($ftemplate_d, "text"),
                GetSQLValueString($_POST['is_register'], "int"),
                GetSQLValueString(isset($_POST['in_search']) ? "true" : "", "defined", "'1'", "'0'"),
				GetSQLValueString($_POST['catalog_id'], "text"));

			$Result1 = el_dbselect($updateSQL, 0, $Result1, 'result', true);

			$um = el_dbselect("UPDATE modules SET name='".addslashes($_POST['name'])."', is_register='".intval($_POST['is_register'])."' WHERE type='catalog".$_GET['cid']."'",
                $um, 0, 'result', true);

			echo "<script>alert('Изменения сохранены!')</script>";
		}
	}


	$query_catalogs = "SELECT * FROM catalogs WHERE catalog_id='" . $_GET['cid'] . "'";
	$catalogs = el_dbselect($query_catalogs, 0, $catalogs);
	$row_catalogs = el_dbfetch($catalogs);
	$totalRows_catalogs = mysqli_num_rows($catalogs);


	?>
	<script language="javascript">
		function selectType(typeStr) {
			var tr1 = document.getElementById('row1');
			var tr2 = document.getElementById('row2');
			var tr3 = document.getElementById('row3');
			var tr4 = document.getElementById('row4');
			if (typeStr == 'file') {
				tr1.style.display = 'block';
				tr2.style.display = 'block';
				tr3.style.display = 'none';
				tr4.style.display = 'none';
			} else if (typeStr == 'string') {
				tr1.style.display = 'none';
				tr2.style.display = 'none';
				tr3.style.display = 'block';
				tr4.style.display = 'block';
			}
			document.cookie = "idshow[cat_template]=" + typeStr + "; expires=Thu, 31 Dec 2020 23:59:59 GMT; path=/editor/;";
		}
	</script>

	<form method="post" enctype="multipart/form-data" action="<?= $_SERVER['REQUEST_URI'] ?>"
		  onSubmit="return fill(['name','catalog_id'],['Название каталога','Имя таблицы данных'])" name="editCat">
		<table width="80%" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
			<caption>
				Настройки каталога <b>&laquo;<?= $row_catalogs['name'] ?>&raquo;</b>
			</caption>

			<tr>
				<td>Название каталога:</td>
				<td>(<span class="style1">название должно быть уникальным</span>)<br>
					<input name="name" type="text" id="name" value="<?= $row_catalogs['name'] ?>" size="50"></td>
			</tr>
			<tr>
				<td width="42%">Имя таблицы <font color="#FF0000" size="2"> (по-английски)</font> <img
						src="/editor/img/help_button.gif" alt="Кликните для получения справки" width="12" height="12"
						style="cursor:pointer"
						onClick="test.TextPopup ('В этом поле указывается имя поля в базе данных английскими буквами. Например, name или height',pszFont,10,10,-1,-1)">
				</td>
				<td><input name="catalog_id" type="text" id="catalog_id" value="<?= $row_catalogs['catalog_id'] ?>"
						   size="30"></td>
			</tr>
            <tr>
                <td>Тип каталога:</td>
                <td>
                    <label>
                        <input <?= ($row_catalogs['is_register'] == '0') ? "checked" : "" ?> name="is_register" type="radio" value="0">
                        Контент
                    </label>
                    <br>
                    <label>
                        <input <?= ($row_catalogs['is_register'] == '1') ? "checked" : "" ?> name="is_register" type="radio" value="1">
                        Справочник
                    </label>
                </td>
            </tr>
			<tr>
				<td>Количество позиций на странице:</td>
				<td><input name="lines_per_pages" type="text" id="lines_per_pages"
						   value="<?= $row_catalogs['lines_per_pages'] ?>" size="5"></td>
			</tr>
			<tr>
				<td>Количество столбцов в списке:</td>
				<td><input name="cols_per_pages" type="text" id="cols_per_pages"
						   value="<?= $row_catalogs['cols_per_pages'] ?>" size="5"></td>
			</tr>
			<tr>
				<?

				$query_modules = "SELECT * FROM modules WHERE status='Y' ORDER BY sort ASC";
				$modules = el_dbselect($query_modules, 0, $modules);
				$row_modules = el_dbfetch($modules);
				$totalRows_modules = mysqli_num_rows($modules);

				?>
				<? /*
    <td>Модуль сверху: </td>
    <td><select name="modul_top" id="modul_top">
	<option value=""> </option>
	<?php do { ?>
          <option value="<?php echo $row_modules['type']?>" <?=($row_catalogs['modul_top']==$row_modules['type'])?"selected":"";?>><?php echo $row_modules['name']?></option>
          <?php
} while ($row_modules = el_dbfetch($modules));
  $rows = mysqli_num_rows($modules);
  if($rows > 0) {
      mysqli_data_seek($modules, 0);
	  $row_modules = el_dbfetch($modules);
  }
?>
    </select></td>
  </tr>
  <tr>
    <td>Модуль снизу: </td>
    <td><select name="modul_bot" id="modul_bot">
	<option value=""> </option>
      <?php do { ?>
      <option value="<?php echo $row_modules['type']?>" <?=($row_catalogs['modul_bot']==$row_modules['type'])?"selected":"";?>><?php echo $row_modules['name']?></option>
      <?php
} while ($row_modules = el_dbfetch($modules));
  $rows = mysqli_num_rows($modules);
  if($rows > 0) {
      mysqli_data_seek($modules, 0);
	  $row_modules = el_dbfetch($modules);
  }
?>
    </select></td>
   </tr>
   */
				?>
			<tr>
				<td>Ширина маленькой картинки:</td>
				<td><input name="small_size" type="text" id="small_size" value="<?= $row_catalogs['small_size'] ?>"
						   size="5">
					пикселей
				</td>
			</tr>
			<tr>
				<td>Ширина большой картинки:</td>
				<td><input name="big_size" type="text" id="big_size" value="<?= $row_catalogs['big_size'] ?>" size="5">
					пикселей
				</td>
			</tr>
            <tr>
                <td>Высота большой картинки:</td>
                <td><input name="big_size_h" type="text" id="big_size_h" value="<?= $row_catalogs['big_size_h'] ?>" size="5">
                    пикселей
                </td>
            </tr>
			<tr>
				<td>По какому полю сортируем данные в разделе:</td>
				<td>
					<input name="sort_tab" type="text" id="sort_tab" value="<?= $row_catalogs['sort_tab'] ?>" size="5">
				</td>
			</tr>
			<tr>
				<td>Порядок сортировки данных в разделе</td>
				<td>

					<select name="sort_tab_s" id="sort_tab_s">
						<option value="ASC" <?= ($row_catalogs['sort_tab_s'] == 'ASC') ? 'selected' : '' ?>>по
							возростанию
						</option>
						<option value="DESC" <?= ($row_catalogs['sort_tab_s'] == 'DESC') ? 'selected' : '' ?>>по
							убыванию
						</option>
					</select>
				</td>
			</tr>
            <tr>
                <td>Учавствует в поиске по сайту:</td>
                <td>
                    <input name="in_search" type="checkbox" id="in_search"<?=($row_catalogs['in_search'] == 1) ? " checked" : ""?>></td>
            </tr>
			<tr>
				<td>Мета теги:</td>
				<td><? ($row_catalogs['meta'] == 1) ? $chek_meta = "checked" : $chek_meta = ""; ?>
					<input name="meta" type="checkbox" id="meta" <?= $chek_meta ?>></td>
			</tr>
			<? /*
  <tr>
    <td>Функции магазина: </td>
    <td><? ($row_catalogs['shop']==1)?$chek_shop="checked":$chek_shop="";?>
	<input name="shop" type="checkbox" id="shop" <?=$chek_shop?>>    </td>
  </tr>
  <tr>
    <td>Обозначение валюты:</td>
    <td><input name="currency" type="text" id="currency" value="<?=$row_catalogs['currency']?>" size="10">
      (например, $ или у.е. или руб.) </td>
  </tr>
  */
			?>

			<tr>
				<td colspan="2">
					<center>Использовать шаблоны:<br>
						<label for="tfile"><input type="radio" id="tfile" name="type_template" value="file"
												  onClick="selectType('file')" <?= ($_COOKIE['idshow']['cat_template'] == 'file') ? 'checked' : '' ?>>
							В виде файлов</label>&nbsp;&nbsp;&nbsp;&nbsp;
						<label for="tstring"><input type="radio" id="tstring" name="type_template" value="string"
													onClick="selectType('string')" <?= ($_COOKIE['idshow']['cat_template'] == 'string') ? 'checked' : '' ?>>
							В виде текста</label></center>
				</td>
			</tr>
			<tr id="row1" style="display:<?= ($_COOKIE['idshow']['cat_template'] == 'file') ? 'block' : 'none' ?>">
				<td>Шаблон дизайна общего списка:</td>
				<td>
					<? el_fileSelect('templatef', '', '/tmpl/catalog/', 'php', $row_catalogs['ftemplate']) ?><br>
					Закачать, если еще нет на сервере: <input name="templatef" type="file" id="templatef">
				</td>
			</tr>
			<tr id="row2" style="display:<?= ($_COOKIE['idshow']['cat_template'] == 'file') ? 'block' : 'none' ?>">
				<td>Шаблон дизайна подробного описания:</td>
				<td>
					<? el_fileSelect('template_df', '', '/tmpl/catalog/', 'php', $row_catalogs['ftemplate_d']) ?><br>
					Закачать, если еще нет на сервере: <input name="template_df" type="file" id="template_df">
				</td>
			</tr>

			<tr id="row3" style="display:<?= ($_COOKIE['idshow']['cat_template'] == 'string') ? 'block' : 'none' ?>">
				<td>Шаблон общего дизайна:</td>
				<td>
					<select name="template" id="template" onChange="check_type_template(this)">
						<option></option>
						<?
						$tem = el_dbselect("SELECT id, name, `table` FROM catalog_templates WHERE type='Общий дизайн'", 0, $tem);
						$templ = el_dbfetch($tem);
						do {
							($templ['table'] == 1) ? $templType = 'Табличный' : $templType = 'Строчный';
							($row_catalogs['template'] == $templ['id']) ? $sel = ' selected' : $sel = '';
							echo "<option value='" . $templ['id'] . "'$sel>" . $templ['name'] . " [" . $templType . "]</option>";
						} while ($templ = el_dbfetch($tem));
						?>
					</select></td>
			</tr>
			<tr id="row4" style="display:<?= ($_COOKIE['idshow']['cat_template'] == 'string') ? 'block' : 'none' ?>">
				<td>Шаблон дизайна строк:</td>
				<td>
					<select name="template_set" id="template_set" onChange="check_type_template(this)">
						<option></option>
						<?
						$tem = el_dbselect("SELECT id, name, `table` FROM catalog_templates WHERE type='Дизайн строки'", 0, $tem);
						$templ = el_dbfetch($tem);
						$templType = '';
						do {
							($templ['table'] == 1) ? $templType = 'Табличный' : $templType = 'Строчный';
							($row_catalogs['template_set'] == $templ['id']) ? $sel = ' selected' : $sel = '';
							echo "<option value='" . $templ['id'] . "'$sel>" . $templ['name'] . " [" . $templType . "]</option>";
						} while ($templ = el_dbfetch($tem));
						?>
					</select></td>
			<tr>
				<td colspan="2" align="center"><input type="submit" name="Submit" value=" Сохранить " class="but">
					<input name="edit" type="hidden" id="edit" value="1"></td>
			</tr>
			<tr>
		</table>
	</form>
	</p>
	<?
}


//Шаблоны дизайна
function show_structure()
{
	global $_POST;
	echo "<form method=post name=struc>
	<select name='catalog_struc' onChange=struc.submit()>
	<option></option>";
	$c = el_dbselect("SELECT name, catalog_id FROM catalogs", 0, $c, 'result', true);
	$ca = el_dbfetch($c);
	do {
		($_POST['catalog_struc'] == $ca['catalog_id']) ? $sel = ' selected' : $sel = '';
		echo "<option value='" . $ca['catalog_id'] . "'$sel>" . $ca['name'] . "</option>
		";
	} while ($ca = el_dbfetch($c));
	echo "</select>
	</form>
	";
	if (isset($_POST['catalog_struc']) && strlen($_POST['catalog_struc']) > 0) {
		$s = el_dbselect("SELECT name, field FROM catalog_prop WHERE catalog_id='" . $_POST['catalog_struc'] . "' ORDER BY sort", 0, $s, 'result', true);
		$st = el_dbfetch($s);
		do {
			echo "&laquo;" . $st['name'] . "&raquo;&nbsp;->&nbsp;[i]field" . $st['field'] . "[/i]<br>
			";
		} while ($st = el_dbfetch($s));
	}
}

function tagToLower($str)
{
	preg_match_all("/<(.*)>/Ui", $str, $lowerList);
	for ($i = 0; $i < count($lowerList[1]); $i++) {
		$str = str_replace($lowerList[1][$i], strtolower($lowerList[1][$i]), $str);
	}
	preg_match_all("/\[(.*)\]/Ui", $str, $lowerList);
	for ($i = 0; $i < count($lowerList[1]); $i++) {
		$str = str_replace($lowerList[1][$i], strtolower($lowerList[1][$i]), $str);
	}
	return $str;
}

if ($_GET['mode'] == "templates") {
	?>
	<center>
	<?= ($_GET['mode'] == 'templates' && !isset($_GET['new']) && !isset($_GET['id_temp'])) ? '<b>Список шаблонов</b>' : '<a href="catalogs.php?mode=templates">Список шаблонов</a>' ?>
	|
	<?= ($_GET['mode'] == 'templates' && isset($_GET['new'])) ? '<b>Добавить шаблон</b>' : '<a href="catalogs.php?mode=templates&new">Добавить шаблон</a>' ?>
	</center>
	<?
	if (!isset($_GET['id_temp']) && !isset($_GET['new'])) {//Выводим список шаблонов
		($_COOKIE['templatetable_temp'] == 'Y') ? $dispTable = 'block' : $dispTable = 'none';
		($_COOKIE['templatestring_temp'] == 'Y') ? $dispString = 'block' : $dispString = 'none';

		if ($_POST['action'] == 'del' && isset($_POST['id'])) {
			el_dbselect("DELETE FROM catalog_templates WHERE id='" . $_POST['id'] . "'", 0, $res);
		}

		echo '<div onClick="showhideDiv(\'table_temp\')" id="table_temp" class="row_' . $dispTable . '"><h5>Табличные шаблоны</h5></div>
		<div id="table_temp_child" style="display:' . $dispTable . '; margin-left:40px">';
		$t = el_dbselect("SELECT * FROM catalog_templates WHERE `table`=1 ORDER BY type DESC", 0, $t);
		$te = el_dbfetch($t);
		if (mysqli_num_rows($t) > 0) {
			do {
				?>
				<div class='row'>
					<div id="left">№<?= $te['id'] ?> Шаблон &laquo;<?= $te['name'] ?>&raquo; [<?= $te['type'] ?>]</div>
					<div id='right'><img border="0" src="/editor/img/menu_edit.gif"
										 onClick="act1('edit', <?= $te['id'] ?>)">&nbsp;<img border="0"
																							 src="/editor/img/menu_delete.gif"
																							 onClick="act1('del', <?= $te['id'] ?>)">
					</div>
				</div>
				<?
			} while ($te = el_dbfetch($t));
		} else {
			echo 'Пока нет ни одного шаблона.';
		}

		echo '</div>
	 <div onClick="showhideDiv(\'string_temp\')" id="string_temp" class="row_' . $dispString . '"><h5>Строчные шаблоны</h5></div>
	 <div id="string_temp_child" style="display:' . $dispString . '; margin-left:40px">';
		$t = el_dbselect("SELECT * FROM catalog_templates WHERE `table`<>1 ORDER BY type DESC", 0, $t);
		$te = el_dbfetch($t);
		if (mysqli_num_rows($t) > 0) {
			do {
				?>
				<div class='row'>
					<div id="left">№<?= $te['id'] ?> Шаблон &laquo;<?= $te['name'] ?>&raquo; [<?= $te['type'] ?>]</div>
					<div id='right'><img border="0" src="/editor/img/menu_edit.gif"
										 onClick="act1('edit', <?= $te['id'] ?>)">&nbsp;<img border="0"
																							 src="/editor/img/menu_delete.gif"
																							 onClick="act1('del', <?= $te['id'] ?>)">
					</div>
				</div>
				<?
			} while ($te = el_dbfetch($t));
		} else {
			echo 'Пока нет ни одного шаблона.';
		}
		echo '</div>';
	}


	if (isset($_GET['id_temp'])) {
		if (isset($_POST['save_template']) && $_POST['save_template'] == 1) {

			$_POST['name'] = str_replace('``', "&quot;", $_POST['name']);
			$_POST['name'] = str_replace('"', "'", $_POST['name']);
			$_POST['list'] = str_replace('"', "'", $_POST['list']);
			$_POST['detail'] = str_replace('"', "'", $_POST['detail']);
			$_POST['top_list'] = str_replace('"', "'", $_POST['top_list']);
			$_POST['bottom_list'] = str_replace('"', "'", $_POST['bottom_list']);
			$_POST['top_detail'] = str_replace('"', "'", $_POST['top_detail']);
			$_POST['bottom_detail'] = str_replace('"', "'", $_POST['bottom_detail']);
			$_POST['list'] = tagToLower($_POST['list']);
			$_POST['detail'] = tagToLower($_POST['detail']);
			$_POST['top_list'] = tagToLower($_POST['top_list']);
			$_POST['bottom_list'] = tagToLower($_POST['bottom_list']);
			$_POST['top_detail'] = tagToLower($_POST['top_detail']);
			$_POST['bottom_detail'] = tagToLower($_POST['bottom_detail']);
			if ($_POST['act_mode'] == 'new') {
				el_dbselect("INSERT INTO catalog_templates (name, list, detail, top_list, bottom_list, top_detail, bottom_detail, `table`, 1bgc, 2bgc, type) 
				VALUES (
				'" . addslashes($_POST['name']) . "', 
				'" . addslashes($_POST['list']) . "', 
				'" . addslashes($_POST['detail']) . "',
				'" . addslashes($_POST['top_list']) . "', 
				'" . addslashes($_POST['bottom_list']) . "',
				'" . addslashes($_POST['top_detail']) . "',  
				'" . addslashes($_POST['bottom_detail']) . "',
				'" . addslashes($_POST['table']) . "',
				'" . addslashes($_POST['1bgc']) . "',
				'" . addslashes($_POST['2bgc']) . "',
				'" . addslashes($_POST['type']) . "'
				)", 0, $res);
				echo "<script language=javascript>alert('Шаблон \"" . $_POST['name'] . "\" создан!')</script>";
			} else {
				el_dbselect("UPDATE catalog_templates SET 
				name='" . addslashes($_POST['name']) . "', 
				list='" . addslashes($_POST['list']) . "', 
				detail='" . addslashes($_POST['detail']) . "',
				top_list='" . addslashes($_POST['top_list']) . "', 
				bottom_list='" . addslashes($_POST['bottom_list']) . "',
				top_detail='" . addslashes($_POST['top_detail']) . "',  
				bottom_detail='" . addslashes($_POST['bottom_detail']) . "',
				`table`='" . $_POST['table'] . "',
				1bgc='" . addslashes($_POST['1bgc']) . "',
				2bgc='" . addslashes($_POST['2bgc']) . "',
				type='" . $_POST['type'] . "' 
				WHERE id='" . $_GET['id_temp'] . "'", 0, $res);
				echo "<script language=javascript>alert('Изменения сохранены!')</script>";
			}
		}
		$et = el_dbselect("SELECT * FROM catalog_templates WHERE id='" . $_GET['id_temp'] . "'", 0, $et, 'row');
		?>
		<h5 align="center">Редактирование шаблона &laquo;<?= stripslashes($et['name']) ?>&raquo;</h5>
		<table align="center" border="0" width="450">
			<tr>
				<td colspan="2"><? el_showalert('info',
						'В тексте шаблона можно использовать тэги HTML.<br>
		Для вставки полей заключите их в тэги [i] и [/i]<br>
		Например, [i]field1[/i].<br>
		Для вставки ссылки на поробное описание используйте тэги [a] и [/a]<br>
		Например, [a]подробнее...[/a].<br>
		Для вставки ссылки на файл [file] и [/file]<br>
		Например, [file]скачать[/file].<br>
		Тогда, если файлов с троке несколько, то текст ссылки будет нумероваться.<br>
		Например, Ссылка №1 Ссылка №2 Ссылка №3.<br>
		Если нужно нужно выводить вместе с текстом ссылки имя файла,<br>
		то используйте тэги [filen] и [/filen].<br>
		Для вставки изображения используйте теги [img] и [/img]<br>
		Если по какому-то полю нужно производить сортировку, используйте теги [sort] и [/sort]<br>
		Для вывода постраничной навигации впишите [paging].<br>
		Для вывода формы поиска впишите [search].<br>
		Для поочередного задания цвета фона строки впишите<br>[bgcolor]
		и задайте оба цвета в соответсвующих полях.<br>
		Что бы иметь перед глазами структуру каталога,<br>
		выберите его из списка ниже.<br>
		Когда появится список полей выбранного каталога<br>
		можно выделять и перетаскивать поля в соответсвующее поле в форме.
		 ') ?></td>
			</tr>
			<tr>
				<td valign="top">Показать структуру каталога:</td>
				<td valign="top"><? show_structure() ?></td>
			</tr>
		</table>
		<table border="0" align="center" class="el_tbl">
			<form method="post" name="temp_form" onSubmit="return check_type()">
				<tr>
					<td align="right">Название шаблона:</td>
					<td>
						<input name="name" id="name" type="text"
							   value="<?= stripslashes(str_replace("'", '``', $et['name'])) ?>" size="40">
					</td>
				</tr>
				<tr>
					<td align="right">Тип шаблона:</td>
					<td>
						<select name="type" id="type" onChange="type_form(this)">
							<option value="Общий дизайн" <?= ($et['type'] == 'Общий шаблон') ? 'selected' : '' ?>>Общий
								дизайн
							</option>
							<option value="Дизайн строки" <?= ($et['type'] == 'Дизайн строки') ? 'selected' : '' ?>>
								Дизайн строки
							</option>
						</select>
						<label><input type="radio" name="table" value="1" <?= ($et['table'] == 1) ? 'checked' : '' ?>>Табличный</label>
						&nbsp;
						<label><input type="radio" name="table" value="0" <?= ($et['table'] == 0) ? 'checked' : '' ?>>Строковый</label>
					</td>
				</tr>
				<tr id="rowс" style="display:<?= ($et['type'] == 'Дизайн строки') ? 'block' : 'none' ?>">
					<td align="right">Цвет фона строк:</td>
					<td>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td>I цвет <input type="button" name="Button" value="Выбрать" class="but"
												  onClick="callColorDlg('1bgc', 'bgc1')">
									<input type="text" size="7" id="1bgc" name="1bgc" value="<?= $et['1bgc'] ?>"></td>
								<td style="background-color:#<?= strtoupper($et['1bgc']) ?>" id="bgc1"><img
										src="/editor/img/spacer.gif" width="20" height="20"></td>
								<td>II цвет <input type="button" name="Button" value="Выбрать" class="but"
												   onClick="callColorDlg('2bgc', 'bgc2')">
									<input type="text" size="7" id="2bgc" name="2bgc" value="<?= $et['2bgc'] ?>"></td>
								<td style="background-color:#<?= strtoupper($et['2bgc']) ?>" id="bgc2"><img
										src="/editor/img/spacer.gif" width="20" height="20"></td>
							</tr>
						</table>
					</td>
				</tr>
				</tr>
				<tr id="rowa" style="display:<?= ($et['type'] == 'Дизайн строки') ? 'block' : 'none' ?>">
					<td valign="top" align="right">Шаблон строки в списке:</td>
					<td>
						<textarea name="list" rows="10" cols="50"><?= stripslashes($et['list']) ?></textarea><br>
						<!--<input name='Button' type='button' onClick="MM_openBrWindow('/editor/newseditor.php?form=temp_form&field=list','editor','resizable=yes','750','640','true','description','new')" value='Визуальный редактор' class='but'><br><br> -->
					</td>
				</tr>
				<tr id="rowb" style="display:<?= ($et['type'] == 'Дизайн строки') ? 'block' : 'none' ?>">
					<td valign="top" align="right">Шаблон детального описания:</td>
					<td>
						<textarea name="detail" rows="10" cols="50"><?= stripslashes($et['detail']) ?></textarea><br>
						<!--<input name='Button' type='button' onClick="MM_openBrWindow('/editor/newseditor.php?form=temp_form&field=detail','editor','resizable=yes','750','640','true','description','new')" value='Визуальный редактор' class='but'><br><br> -->
					</td>
				</tr>
				<tr id="row0" style="display:<?= ($et['type'] == 'Общий дизайн') ? 'block' : 'none' ?>">
					<td valign="top" align="right">Шаблон области над списком:</td>
					<td>
						<textarea name="top_list" rows="10"
								  cols="50"><?= stripslashes($et['top_list']) ?></textarea><br>
						<!--<input name='Button' type='button' onClick="MM_openBrWindow('/editor/newseditor.php?form=temp_form&field=top_list','editor','resizable=yes','750','640','true','description','new')" value='Визуальный редактор' class='but'><br><br> -->
					</td>
				</tr>
				<tr id="row1" style="display:<?= ($et['type'] == 'Общий дизайн') ? 'block' : 'none' ?>">
					<td valign="top" align="right">Шаблон области под списком:</td>
					<td>
						<textarea name="bottom_list" rows="10"
								  cols="50"><?= stripslashes($et['bottom_list']) ?></textarea><br>
						<!--<input name='Button' type='button' onClick="MM_openBrWindow('/editor/newseditor.php?form=temp_form&field=bottom_list','editor','resizable=yes','750','640','true','description','new')" value='Визуальный редактор' class='but'><br><br> -->
					</td>
				</tr>
				<tr id="row2" style="display:<?= ($et['type'] == 'Общий дизайн') ? 'block' : 'none' ?>">
					<td valign="top" align="right">Шаблон области над деталями:</td>
					<td>
						<textarea name="top_detail" rows="10"
								  cols="50"><?= stripslashes($et['top_detail']) ?></textarea><br>
						<!--<input name='Button' type='button' onClick="MM_openBrWindow('/editor/newseditor.php?form=temp_form&field=top_detail','editor','resizable=yes','750','640','true','description','new')" value='Визуальный редактор' class='but'><br><br> -->
					</td>
				</tr>
				<tr id="row3" style="display:<?= ($et['type'] == 'Общий дизайн') ? 'block' : 'none' ?>">
					<td valign="top" align="right">Шаблон области под деталями:</td>
					<td>
						<textarea name="bottom_detail" rows="10"
								  cols="50"><?= stripslashes($et['bottom_detail']) ?></textarea><br>
						<!--<input name='Button' type='button' onClick="MM_openBrWindow('/editor/newseditor.php?form=temp_form&field=bottom_detail','editor','resizable=yes','750','640','true','description','new')" value='Визуальный редактор' class='but'><br><br> -->
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<input type="submit" value="Сохранить" onClick="act_mode.value=''" class="but">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input
							type="button" value="Сохранить как новый" onClick="new_template()" class="but">
						<input type="hidden" value="1" name="save_template">
						<input type="hidden" name="act_mode" value="">
					</td>
				</tr>
			</form>
		</table>
		<OBJECT id=dlgHelper classid=clsid:3050f819-98b5-11cf-bb82-00aa00bdce0b name=dlgHelper VIEWASTEXT></OBJECT>
		<?
	}
	if (isset($_GET['new'])) {
		if (isset($_POST['new_template']) && $_POST['new_template'] == 1) {

			$_POST['name'] = str_replace('``', "&quot;", $_POST['name']);
			$_POST['name'] = str_replace('"', "'", $_POST['name']);
			$_POST['list'] = str_replace('"', "'", $_POST['list']);
			$_POST['detail'] = str_replace('"', "'", $_POST['detail']);
			$_POST['top_list'] = str_replace('"', "'", $_POST['top_list']);
			$_POST['bottom_list'] = str_replace('"', "'", $_POST['bottom_list']);
			$_POST['top_detail'] = str_replace('"', "'", $_POST['top_detail']);
			$_POST['bottom_detail'] = str_replace('"', "'", $_POST['bottom_detail']);
			$_POST['list'] = tagToLower($_POST['list']);
			$_POST['detail'] = tagToLower($_POST['detail']);
			$_POST['top_list'] = tagToLower($_POST['top_list']);
			$_POST['bottom_list'] = tagToLower($_POST['bottom_list']);
			$_POST['top_detail'] = tagToLower($_POST['top_detail']);
			$_POST['bottom_detail'] = tagToLower($_POST['bottom_detail']);

			el_dbselect("INSERT INTO catalog_templates (name, list, detail, top_list, bottom_list, top_detail, bottom_detail, `table`, 1bgc, 2bgc, type) 
			VALUES (
			'" . addslashes($_POST['name']) . "', 
			'" . addslashes($_POST['list']) . "', 
			'" . addslashes($_POST['detail']) . "',
			'" . addslashes($_POST['top_list']) . "', 
			'" . addslashes($_POST['bottom_list']) . "',
			'" . addslashes($_POST['top_detail']) . "',  
			'" . addslashes($_POST['bottom_detail']) . "',
			'" . addslashes($_POST['table']) . "',
			'" . addslashes($_POST['1bgc']) . "',
			'" . addslashes($_POST['2bgc']) . "',
			'" . addslashes($_POST['type']) . "'
			)", 0, $res);
			echo "<script language=javascript>alert('Шаблон \"" . $_POST['name'] . "\" создан!')</script>";
		}
		?>
		<h5 align="center">Создание нового шаблона</h5>
		<table align="center" border="0" width="450">
			<tr>
				<td colspan="2"><? el_showalert('info',
						'В тексте шаблона можно использовать тэги HTML.<br>
		Для вставки полей заключите их в тэги [i] и [/i]<br>
		Например, [i]field1[/i].<br>
		Для вставки ссылки на поробное описание используйте тэги [a] и [/a]<br>
		Например, [a]подробнее...[/a].<br>
		Для вставки ссылки на файл [file] и [/file]<br>
		Например, [file]скачать[/file].<br>
		Тогда, если файлов с троке несколько, то текст ссылки будет нумероваться.<br>
		Например, Ссылка №1 Ссылка №2 Ссылка №3.<br>
		Если нужно нужно выводить вместе с текстом ссылки имя файла,<br>
		то используйте тэги [filen] и [/filen].<br>
		Для вывода постраничной навигации впишите [paging].<br>
		Для вывода формы поиска впишите [search].<br>
		Для поочередного задания цвета фона строки впишите<br>[bgcolor]
		и задайте оба цвета в соответсвующих полях.<br>
		Что бы иметь перед глазами структуру каталога,<br>
		выберите его из списка ниже.<br>
		Когда появится список полей выбранного каталога<br>
		можно выделять и перетаскивать поля в соответсвующее поле в форме.
		 ') ?></td>
			</tr>
			<tr>
				<td valign="top">Показать структуру каталога:</td>
				<td valign="top"><? show_structure() ?></td>
			</tr>
		</table>
		<table border="0" align="center" class="el_tbl">
			<form method="post" name="temp_form" onSubmit="return check_type()">
				<tr>
					<td align="right">Название шаблона:</td>
					<td>
						<input name="name" id="name" type="text" size="40">
					</td>
				</tr>
				<tr>
					<td align="right">Тип шаблона:</td>
					<td>
						<select name="type" id="type" onChange="type_form(this)">
							<option></option>
							<option value="Общий дизайн">Общий дизайн</option>
							<option value="Дизайн строки">Дизайн строки</option>
						</select>
						<label><input type="radio" name="table" value="1">Табличный</label> &nbsp;
						<label><input type="radio" name="table" value="0">Строковый</label>
					</td>
				</tr>
				<tr id="rowс" style="display:none">
					<td align="right">Цвет фона строк:</td>
					<td>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td>I цвет <input type="button" name="Button" value="Выбрать" class="but"
												  onClick="callColorDlg('1bgc', 'bgc1')">
									<input type="text" size="7" id="1bgc" name="1bgc" value="<?= $et['1bgc'] ?>"></td>
								<td style="background-color:#<?= strtoupper($et['1bgc']) ?>" id="bgc1"><img
										src="/editor/img/spacer.gif" width="20" height="20"></td>
								<td>II цвет <input type="button" name="Button" value="Выбрать" class="but"
												   onClick="callColorDlg('2bgc', 'bgc2')">
									<input type="text" size="7" id="2bgc" name="2bgc" value="<?= $et['2bgc'] ?>"></td>
								<td style="background-color:#<?= strtoupper($et['2bgc']) ?>" id="bgc2"><img
										src="/editor/img/spacer.gif" width="20" height="20"></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr id="rowa" style="display:none">
					<td valign="top" align="right">Шаблон строки с списке:</td>
					<td>
						<textarea name="list" rows="10" cols="50"></textarea><br>
						<!--<input name='Button' type='button' onClick="MM_openBrWindow('/editor/newseditor.php?form=temp_form&field=list','editor','resizable=yes','750','640','true','description','new')" value='Визуальный редактор' class='but'><br><br> -->
					</td>
				</tr>
				<tr id="rowb" style="display:none">
					<td valign="top" align="right">Шаблон детального описания:</td>
					<td>
						<textarea name="detail" rows="10" cols="50"></textarea><br>
						<!--<input name='Button' type='button' onClick="MM_openBrWindow('/editor/newseditor.php?form=temp_form&field=detail','editor','resizable=yes','750','640','true','description','new')" value='Визуальный редактор' class='but'><br><br> -->
					</td>
				</tr>
				<tr id="row0" style="display:none">
					<td valign="top" align="right">Шаблон области над списком:</td>
					<td>
						<textarea name="top_list" rows="10" cols="50"></textarea><br>
						<!--	<input name='Button' type='button' onClick="MM_openBrWindow('/editor/newseditor.php?form=temp_form&field=top_list','editor','resizable=yes','750','640','true','description','new')" value='Визуальный редактор' class='but'><br><br> -->
					</td>
				</tr>
				<tr id="row1" style="display:none">
					<td valign="top" align="right">Шаблон области под списком:</td>
					<td>
						<textarea name="bottom_list" rows="10" cols="50"></textarea><br>
						<!--	<input name='Button' type='button' onClick="MM_openBrWindow('/editor/newseditor.php?form=temp_form&field=bottom_list','editor','resizable=yes','750','640','true','description','new')" value='Визуальный редактор' class='but'><br><br> -->
					</td>
				</tr>
				<tr id="row2" style="display:none">
					<td valign="top" align="right">Шаблон области над деталями:</td>
					<td>
						<textarea name="top_detail" rows="10" cols="50"></textarea><br>
						<!--<input name='Button' type='button' onClick="MM_openBrWindow('/editor/newseditor.php?form=temp_form&field=top_detail','editor','resizable=yes','750','640','true','description','new')" value='Визуальный редактор' class='but'><br><br> -->
					</td>
				</tr>
				<tr id="row3" style="display:none">
					<td valign="top" align="right">Шаблон области под деталями:</td>
					<td>
						<textarea name="bottom_detail" rows="10" cols="50"></textarea><br>
						<!--<input name='Button' type='button' onClick="MM_openBrWindow('/editor/newseditor.php?form=temp_form&field=bottom_detail','editor','resizable=yes','750','640','true','description','new')" value='Визуальный редактор' class='but'><br><br> -->
					</td>
				</tr>

				<tr>
					<td colspan="2" align="center">
						<input type="submit" value="Сохранить" class="but">
						<input type="hidden" value="1" name="new_template">
					</td>
				</tr>
			</form>
		</table>
		<OBJECT id=dlgHelper classid=clsid:3050f819-98b5-11cf-bb82-00aa00bdce0b name=dlgHelper VIEWASTEXT></OBJECT>
		<?
	}
}
?>
</body>
<OBJECT
	classid="clsid:adb880a6-d8ff-11cf-9377-00aa003b7a11" type="application/x-oleobject" width="1" height="1" id=test>
</OBJECT>
</html>
