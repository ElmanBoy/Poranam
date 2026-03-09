<?php
//error_reporting(E_ALL);
@session_start();
if (isset($_REQUEST['ajax'])) {
	if (ob_get_length()) ob_clean();
	@header("Content-type: text/html; charset=utf-8");
	@header("Cache-Control: no-store, no-cache, must-revalidate");
	@header("Cache-Control: post-check=0, pre-check=0", false);
	include_once $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';
	$cat = $_REQUEST['cat'];
	$catalog_id = $_REQUEST['catalog_id'];
	parse_str($_REQUEST['params'], $_REQUEST);
	$_REQUEST['active'] = 1;
} else {
	//el_strongvarsprocess();
	$dbconn = el_dbconnect();
	$database_dbconn = el_database();
	$cat = el_getvar('cat');
	$path = el_getvar('path');
}
/*print_r($_REQUEST);
print_r($_GET);*/

$searchOper = "AND";

//$catsPath = el_getGoodPath();

$_GET['id'] = intval($_GET['id']);
$_GET['path'] = addslashes(trim(strip_tags($_GET['path'])));

$parentid = (intval($row_dbcontent['cat']) > 0) ? $row_dbcontent['cat'] : $cat;
$catalog_id = $row_dbcontent['kod'] ? str_replace("catalog", "", $row_dbcontent['kod']) : $_GET['catalog_id'];

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
/*
$_REQUEST = $_GET;
//Создаем поисковый подзапрос
$childCats = el_getChild($row_dbcontent['path']);
$catsQuery = "";
if (@count($childCats) > 0 && $childCats != false) {
	$catsQuery = " OR cat IN (" . implode(", ", $childCats) . ")";
}
$subquery = " active=1 AND (cat = '" . $parentid . "' OR cat LIKE '% " . $parentid . " %'$catsQuery)  AND 
                                    (site_id = 1 OR site_id = " . intval($_SESSION['view_site_id']) . ") ";

switch (intval($row_dbcontent['cat'])) {
	case 3360:
		$subquery = " active=1 ";
		$_SESSION['catalog_total_limit'] = 100;
		$_REQUEST['filter'] = 'new';
		break;
	case 3363:
		$subquery = " active=1 AND field52 = 'Скидка'";
		$_REQUEST['filter'] = 'new';
		break;
	case 3361:
		$subquery = " active=1 AND field52 = 'Хит продаж'";
		$_REQUEST['filter'] = 'new';
		break;
}

if (count($_REQUEST) > 1) {
	foreach ($_REQUEST as $varname => $var) {
		$sfieldNum = str_replace(array('sf', '_d', '_from', '_to'), '', $varname);
		$preOper = "";
		if ($var && substr_count($varname, 'sf') > 0) {
			if (@substr_count($var, '|') > 0 || is_array($var)) {
				$avar = array();
				$asubquery = array();

				if(is_array($var)){
					$avar = $var;
				}elseif (substr_count($var, '|') > 0) {
					$avar = explode('|', $var);
				}

				if (count($avar) > 0) {
					$ct = el_dbselect("DESCRIBE catalog_" . $catalog_id . "_data field" . $sfieldNum, 0, $ct, 'row', true);
					for ($v = 0; $v < count($avar); $v++) {
						switch (strtolower($ct['Type'])) {
							case 'text'    :
							case 'longtext':
								$soper = " LIKE '%" . addslashes($avar[$v]) . "%'";
								if($sfieldNum == 32){
									$soper = ") > 0";
									$preOper = " FIND_IN_SET(".addslashes($avar[$v]).", ";
								}
								break;
							case 'year' :
							case 'year(4)'    :
								(substr_count($varname, '_d') > 0) ? $soper = "<='" . $avar[$v] . "'" : $soper = ">='" . $avar[$v] . "'";
								break;
							case 'int(11)' :
							case 'tinyint(4)' :
							case 'float' :
							case 'double' :
								if (substr_count($avar[$v], '-') > 0) {
									$varArr = explode('-', $avar[$v]);
									$asubquery[] = "(field" . $sfieldNum." >= ".$varArr[0]." AND field" .
										$sfieldNum." <= ".$varArr[1].")";
									$soper = "='" . intval($avar[$v]) . "'";
								}else {
									if (substr_count($varname, '_from') > 0) {
										$soper = ">='" . intval($avar[$v]) . "'";
									} elseif (substr_count($varname, '_to') > 0) {
										$soper = "<='" . intval($avar[$v]) . "'";
									} else {
										$soper = "='" . intval($avar[$v]) . "'";
									};
								}
								break;
						}
						$asubquery[] = $preOper."field" . $sfieldNum . $soper;
					}
					$subquery .= ' '.$searchOper.' (' . implode(' OR ', $asubquery) . ')';
				}
			} else {
				$ct = el_dbselect("DESCRIBE catalog_" . $catalog_id . "_data field" . $sfieldNum, 0, $ct, 'row');
				switch (strtolower($ct['Type'])) {
					case 'text'    :
					case 'longtext':
						$soper = " LIKE '%" . addslashes($var) . "%'";
						break;
					case 'date'    :
						(substr_count($varname, '_d') > 0) ? $soper = "<='" . $var . "'" : $soper = ">='" . $var . "'";
						break;
					case 'int(11)' :
					case 'tinyint(4)' :
					case 'year(4)' :
					case 'double' :
					case 'float' :
						if (substr_count($varname, '_from') > 0) {
							$soper = ">='" . (intval($var)) . "'";
						} elseif (substr_count($varname, '_to') > 0) {
							$soper = "<='" . (intval($var)) . "'";
						} elseif (intval($var) < 114) {
							$soper = "='" . intval($var) . "'";
						};
						break;
				}
				$subquery .= " $searchOper field" . $sfieldNum . $soper;
			}
		}
		if (trim($varname) == 'cat' && intval($varname) > 0) {
			$subquery .= " AND (cat='" . intval($var) . "' OR cat LIKE '% " . intval($var) . " %')";
		}
	}
} else {
	$subquery .= " AND (cat = '" . $parentid . "' OR cat LIKE '% " . $parentid . " %')";
}



//Создаем подзапрос для сортировки
$sortquery = '';
if (isset($_REQUEST['sort'])) {
	if (substr_count($_REQUEST['sort'], '|') > 0) {
		$_REQUEST['sort'] = explode('|', $_REQUEST['sort']);
		for ($i = 0; $i < count($_REQUEST['sort']); $i++) {
			$sfieldNum1 = str_replace('sf', '', $_REQUEST['sort'][$i]);
			$ord = ($_REQUEST['sort'][$i] == 'sf' . intval($sfieldNum1)) ? 'ASC' : 'DESC';
			$sfieldNum[] = 'field' . str_replace('_r', '', $sfieldNum1) . ' ' . $ord;
		}
		$sortquery .= " ORDER BY " . implode(', ', $sfieldNum).", FIELD(`field43`, 0), `field43`";
	} else {
		$sfieldNum1 = str_replace('sf', '', $_REQUEST['sort']);
		$ord = ($_REQUEST['sort'] == 'sf' . intval($sfieldNum1)) ? 'ASC' : 'DESC';
		$sfieldNum = str_replace('_r', '', $sfieldNum1);

		if (intval($sfieldNum) != 0) {
			$sortquery .= " ORDER BY field" . intval($sfieldNum) . " $ord,  FIELD(`field43`, 0), `field43`";
			//echo $sortquery;
		} else {
			$sortquery .= " ORDER BY id DESC, sort ASC, FIELD(`field43`, 0), `field43`";
			//echo $sortquery;
		}
	}
} else {
	if (strlen($row_cat_form1['sort_tab_s']) == 0) $row_cat_form1['sort_tab_s'] = 'DESC';
	$sortquery = $row_cat_form1['sort_tab'] . " " . $row_cat_form1['sort_tab_s'].", ORDER BY FIELD(`field43`, 0), `field43`";
}

if(strlen(trim($_GET['search'])) > 0) {
	//$subquery = el_genSearchQuery($catsQuery,$_GET['search']);
	$subquery = "active=1 AND (
  MATCH(field17, field1, field41, field2, field32, field33, field34, field35, field36, field37, field38, field44, field45, field46)
  AGAINST('*".$_GET['search']."*' IN BOOLEAN MODE) OR
   MATCH(field17, field1, field41, field2, field32, field33, field34, field35, field36, field37, field38, field44, field45, field46)
  AGAINST('*".switcher_ru($_GET['search'])."*' IN BOOLEAN MODE) OR
   MATCH(field17, field1, field41, field2, field32, field33, field34, field35, field36, field37, field38, field44, field45, field46)
  AGAINST('*".switcher_en($_GET['search'])."*' IN BOOLEAN MODE)) ORDER BY 
  MATCH(field17, field1, field41, field2, field32, field33, field34, field35, field36, field37, field38, field44, field45, field46)
  AGAINST('*".$_GET['search']."*' IN BOOLEAN MODE) DESC, FIELD(`field43`, 0), `field43`";
	$sortquery = "";
}
*/
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

	if ($row_cat_form1['shop'] == '1') {

		$query_pro = "SELECT * FROM shop_props WHERE catalog_id='" . $catalog_id . "'";
		$pro = el_dbselect($query_pro, 0, $pro);
		$row_pro = el_dbfetch($pro);
	}


    //print_r($_GET); print_r($_REQUEST);

	$pn = 0;
	if (isset($_GET['pn'])) {
		$pn = intval($_GET['pn']);
	}
	$startRow_catalog = $pn * $maxRows_catalog;

	if($catalog_id == 'init') {
        $_GET['sf5'] = $_GET['region'];
        $_GET['sf6'] = $_GET['district'];
    }

    $subquery = el_buildCatalogSubQuery();

	$query_catalog = "SELECT * FROM catalog_" . $catalog_id . "_data WHERE " . $subquery[0] . " " . $subquery[1];//id<>0
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

	 if($cat == 405){
         $row_cat_form1['ftemplate'] = 'event_list.php';
     }

	// Выводим краткий список каталога
	if ($tr > 0) {
		if (strlen($row_cat_form1['ftemplate']) > 0) {

			if ($totalPages_catalog > 0) {
				// el_paging($pn, $currentPage, $queryString_catalog, $totalPages_catalog, $maxRows_catalog, $tr);
			}
			//do{
			include $_SERVER['DOCUMENT_ROOT'] . '/tmpl/catalog/' . $row_cat_form1['ftemplate'];

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
		$colname_detail = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
		$query_detail = sprintf("SELECT * FROM catalog_" . $catalog_id . "_data WHERE id = %s AND cat=" . intval($cat), $colname_detail);

		if (intval($_GET['id']) > 0) {
			$detail = el_dbselect($query_detail, 0, $detail, 'result', true);
			$row_catalog = el_dbfetch($detail);
			if (strlen(trim($row_catalog['path'])) > 0) {
				//header("Location: " . $path . "/" . $row_catalog['path'] . ".html", true, 301);
			}
		}

	}
	if (isset($_GET['path'])) {
		$catalog_id = str_replace("catalog", "", $row_dbcontent['kod']);
		$colname_detail = (get_magic_quotes_gpc()) ? urldecode($_GET['path']) : addslashes(urldecode($_GET['path']));
		$query_detail = sprintf("SELECT * FROM catalog_" . $catalog_id . "_data WHERE path = '%s'", $colname_detail);
	}
	$detail = el_dbselect($query_detail, 0, $detail, 'result', true);
	$row_catalog = el_dbfetch($detail);
	$totalRows_detail = el_dbnumrows($detail);


	$query_cat_form = "SELECT * FROM catalog_prop WHERE catalog_id='" . $catalog_id . "' AND detail=1 ORDER BY sort ASC";
	$cat_form = el_dbselect($query_cat_form, 0, $cat_form);
	$row_cat_form = el_dbfetch($cat_form);
	$totalRows_cat_form = el_dbnumrows($cat_form);


	$query_cat_form1 = "SELECT * FROM catalogs WHERE catalog_id='" . $catalog_id . "'";
	$cat_form1 = el_dbselect($query_cat_form1, 0, $cat_form1);
	$row_cat_form1 = el_dbfetch($cat_form1);
	if (strlen($row_cat_form1['ftemplate_d']) == 0) {
		//Находим шаблон для отображения строк списка
		$template_row = el_dbselect("SELECT detail FROM catalog_templates WHERE id='" . $row_cat_form1['template_set'] . "'", 0, $template_row, 'row');
	}
	if ($row_cat_form1['shop'] == '1') {

		$query_pro = "SELECT * FROM shop_props WHERE catalog_id='" . $catalog_id . "'";
		$pro = el_dbselect($query_pro, 0, $pro);
		$row_pro = el_dbfetch($pro);
	}
	?>
	<?php /* if(!isset($_GET['p'])){ ?><a href="<?=$path?>/">&laquo; Вернуться к списку</a><br><br><? } */
	?>

	<?
	if ($totalRows_detail > 0) {
		if (strlen($row_cat_form1['ftemplate_d']) > 0) {
			if($_POST['mode'] == 'popup'){
				$row_cat_form1['ftemplate_d'] = 'good_popup.php';
			}
			include $_SERVER['DOCUMENT_ROOT'] . '/tmpl/catalog/' . $row_cat_form1['ftemplate_d'];
		} elseif (strlen($row_cat_form1['template_set']) > 0) {
			eval(parse_template($template_row['detail'], $row_catalog['filename'], '', '', $row_catalog));
		} else {
			echo 'Не задан шаблон отображения.';
		}
	} else {
		echo 'Введен неверный адрес.';
		//echo header('HTTP/1.1 404 Not Found');
		echo '<p>На сайте нет такой страницы.</p>
<p>Возможно, Вы ошиблись в написании адреса, а может быть, страницу удалили.</p>
<p>Перейдите на <a href="/">Главную страницу</a>, воспользуйтесь <a href="/sitemaps/">Картой сайта</a>';
	}
	?>


	<? mysqli_free_result($detail);
}
?> 

