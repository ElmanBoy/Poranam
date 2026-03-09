<?php
//error_reporting(E_ALL);
@session_start();
$catalog_id = 'init';
$catalog = null;
$detail = null;

if (isset($_REQUEST['ajax'])) {
	if (ob_get_length()) ob_clean();
	@header("Content-type: text/html; charset=utf-8");
	@header("Cache-Control: no-store, no-cache, must-revalidate");
	@header("Cache-Control: post-check=0, pre-check=0", false);
	include_once $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';
	$cat = 405;//$_REQUEST['cat'];
	parse_str($_REQUEST['params'], $_REQUEST);
	$_REQUEST['active'] = 1;
} else {
	//el_strongvarsprocess();
	$dbconn = el_dbconnect();
	$database_dbconn = el_database();
	$cat = 405;//el_getvar('cat');
	$path = el_getvar('path');
}

$searchOper = "AND";

//$catsPath = el_getGoodPath();

$_GET['id'] = intval($_GET['id']);
$_GET['path'] = addslashes(trim(strip_tags($_GET['path'])));

$parentid = (intval($row_dbcontent['cat']) > 0) ? $row_dbcontent['cat'] : $cat;
//$catalog_id = str_replace("catalog", "", $row_dbcontent['kod']);

$query_cat_form1 = "SELECT * FROM catalogs WHERE catalog_id='" . $catalog_id . "'";
$cat_form1 = el_dbselect($query_cat_form1, 0, $cat_form1);
$row_cat_form1 = el_dbfetch($cat_form1);

$queryString_catalog = "";
if($_POST['ajax'] == 1){
	$ajaxParams = array();
	foreach($_GET as $key => $val){
		if(is_array($val)){
			for($i = 0; $i < count($val); $i++)
				if(stristr($key, "pn") == false &&
					stristr($key, "url") == false &&
					stristr($key, "path") == false)
					$ajaxParams[] = $key .'[]='. $val[$i];
		}else{
			if(is_string($val) &&
				strlen($val) > 0 &&
				stristr($key, "pn") == false &&
				stristr($key, "url") == false &&
				stristr($key, "path") == false)
				$ajaxParams[] = $key.'='.$val;
		}
	}
	$_SERVER['QUERY_STRING'] = (implode('&', $ajaxParams));
	$queryString_catalog = (implode('&', $ajaxParams));
}
if (!empty($_SERVER['QUERY_STRING'])) {
	$params = explode("&", $_SERVER['QUERY_STRING']);
	$newParams = array();
	foreach ($params as $param) {
		if (stristr($param, "pn") == false &&
			stristr($param, "tr") == false &&
			stristr($param, "url") == false &&
            stristr($param, "path") == false
		) {
			array_push($newParams, $param);
		}
	}
	$newParams = array_unique($newParams);
	if (count($newParams) != 0) {
		$queryString_catalog = "?" . htmlentities(implode("&", $newParams));
	}
}


if(empty($_GET['id']) && strlen(trim($_GET['path'])) == 0){
    unset($_SESSION['highlight']);
}

if (empty($_GET['id']) && strlen(trim($_GET['path'])) == 0) {

	$currentPage = "";
	$maxRows_catalog = $row_cat_form1['lines_per_pages'];
	$_SESSION['catalog_total_limit'] = 0;

	if (strlen($row_cat_form1['ftemplate']) == 0) {
		//Находим общий шаблон
		$template_global = el_dbselect("SELECT top_list, bottom_list FROM catalog_templates WHERE id='" . $row_cat_form1['template'] . "'", 0, $template_row, 'row');

		//Находим шаблоны для строк
		$template_row = el_dbselect("SELECT list, 1bgc, 2bgc FROM catalog_templates WHERE id='" . $row_cat_form1['template_set'] . "'", 0, $template_row, 'row');
	}



    //print_r($_GET); print_r($_REQUEST);

	$pn = 0;
	if (isset($_GET['pn'])) {
		$pn = intval($_GET['pn']);
	}
	$startRow_catalog = $pn * $maxRows_catalog;

    $row_dbcontent['cat'] = 405;
    $subquery = el_buildCatalogSubQuery();

	$query_catalog = "SELECT * FROM catalog_" . $catalog_id . "_data WHERE " . $subquery[0] . " AND field14 > 13 AND field25 = 1 " . $subquery[1];//id<>0
	$query_limit_catalog = sprintf("%s LIMIT %d, %d", $query_catalog, $startRow_catalog, $maxRows_catalog);
	$catalog = el_dbselect($query_limit_catalog, 0, $catalog, 'result', true);
	$row_catalog = el_dbfetch($catalog);

	if (isset($_GET['debug'])) echo $query_limit_catalog . ' SORT' . $_REQUEST['sort'];

	if (isset($_GET['tr'])) {
		$tr = $_GET['tr'];
	} else {
		$all_catalog = el_dbselect($query_catalog, $_SESSION['catalog_total_limit'], $catalog, 'result', true);
		$tr = el_dbnumrows($all_catalog);
	}
	$totalPages_catalog = ceil($tr / $maxRows_catalog) - 1;

	 $string = 0;

	// Выводим краткий список каталога
	if ($tr > 0) {
		if (strlen($row_cat_form1['ftemplate']) > 0) {

			if ($totalPages_catalog > 0) {
				// el_paging($pn, $currentPage, $queryString_catalog, $totalPages_catalog, $maxRows_catalog, $tr);
			}
			//do{
			include $_SERVER['DOCUMENT_ROOT'] . '/tmpl/catalog/news_list.php';

			//}while($row_catalog = el_dbfetch($catalog));
			if ($totalPages_catalog > 0) {
				//el_paging($pn, $currentPage, $queryString_catalog, $totalPages_catalog, $maxRows_catalog, $tr);
			}


		} elseif (strlen($template_row['top_list']) > 0 || strlen($template_row['list']) > 0) {
			?>
			<table border="0" cellpadding="0" cellspacing="0" class="catalog_extable">
				<tr>
					<td valign="top" width="<?= ceil(100 / $row_cat_form1['cols_per_pages']) ?>%"
					    class="catalog_inttable">
						<? eval(parse_template($template_global['top_list'], $row_catalog['filename'], '', '', $row_catalog));
						do { ?>

							<? ($bgcolor == $template_row['1bgc']) ? $bgcolor = $template_row['2bgc'] : $bgcolor = $template_row['1bgc'];
							eval(parse_template($template_row['list'], $row_catalog['filename'], $bgcolor, '', $row_catalog));
							$string++;
							if ($string == $row_cat_form1['cols_per_pages'] && $row_cat_form1['cols_per_pages'] != 1) {
								echo "</td></tr><tr>";
								$string = 0;
							}
						} while ($row_catalog = el_dbfetch($catalog));
						eval(parse_template($template_global['bottom_list'], $row_catalog['filename'], '', '', $row_catalog)); ?>
					</td>
				</tr>
			</table>
			<?
		} else {
			echo 'Не заданы шаблоны отображения.';
		}

	} else {
		echo '<div class="search_resalt">
				<div class="box">
				К сожалению, ничего не найдено. 
				</div>
			</div>';
	}

	mysqli_free_result($catalog);
	mysqli_free_result($cat_form1);
} else {
//Создаем массив имен критериев
	/*$catalog_fields = array();
	$fieldsName = el_dbselect("SELECT name, field, type FROM catalog_prop WHERE catalog_id='$catalog_id'", 0, $fieldsName);
	$fieldsN = el_dbfetch($fieldsName);
	do {
		$catalog_fields['field' . $fieldsN['field']] = array('name' => $fieldsN['name'], 'type' => $fieldsN['type']);
	} while ($fieldsN = el_dbfetch($fieldsName));*/


// Показываем детальную информацию////////////////////////////////////////////////////////////
	$colname_detail = "-1";
	if (isset($_GET['id'])) {
		$colname_detail = intval($_GET['id']);
		$query_detail = sprintf("SELECT * FROM catalog_init_data WHERE id = %s AND cat = 405 AND field25 = 1", $colname_detail);

		if (intval($_GET['id']) > 0) {
			$detail = el_dbselect($query_detail, 0, $detail, 'result', true);
			$row_catalog = el_dbfetch($detail);
			if (strlen(trim($row_catalog['path'])) > 0) {
				//header("Location: " . $path . "/" . $row_catalog['path'] . ".html", true, 301);
			}
		}

	}
/*	if (isset($_GET['path'])) {
		$catalog_id = str_replace("catalog", "", $row_dbcontent['kod']);
		$colname_detail = (get_magic_quotes_gpc()) ? urldecode($_GET['path']) : addslashes(urldecode($_GET['path']));
		$query_detail = sprintf("SELECT * FROM catalog_init_data WHERE path = '%s'", $colname_detail);
	}*/
	$detail = el_dbselect($query_detail, 0, $detail, 'result', true);
	$row_catalog = el_dbfetch($detail);
	$totalRows_detail = el_dbnumrows($detail);
    if($totalRows_detail > 0){
	?>
	<?php /* if(!isset($_GET['p'])){ ?><a href="<?=$path?>/">&laquo; Вернуться к списку</a><br><br><? } */
	?>

	<?
	include $_SERVER['DOCUMENT_ROOT'] . '/tmpl/catalog/news_detail.php';
	} else {
		echo 'Введен неверный адрес.';
		//echo header('HTTP/1.1 404 Not Found');
		echo '<p>На сайте нет такой страницы.</p>
<p>Возможно, Вы ошиблись в написании адреса, а может быть, страницу удалили.</p>
<p>Перейдите на <a href="/">Главную страницу</a>';
	}
	?>


	<? mysqli_free_result($detail);
}
?> 

