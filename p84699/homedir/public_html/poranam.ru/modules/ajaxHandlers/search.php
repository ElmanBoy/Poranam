<?php
@session_start();
$start = microtime(true);
include_once $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';
$answer = array();
$findCats = array();
$_SESSION['highlight'] = array();

$catsPath = el_getGoodPath();

/*$childCats = el_getChild('/catalog');
$catsQuery = "";
if (@count($childCats) > 0 && $childCats != false) {
	$catsQuery = " OR cat IN (" . implode(", ", $childCats) . ")";
}*/

if(strlen(trim($_POST['search'])) > 2) {
	//$subquery = el_genSearchQuery($catsQuery, $_POST['search']);

	$_GET['search'] = $_SESSION['highlight'][] = addslashes(trim(strip_tags($_POST['search'])));
	$_SESSION['highlight'][] = switcher_ru($_GET['search']);
	//$_SESSION['highlight'][] = switcher_en($_GET['search']);

	$query_catalog = "SELECT * FROM catalog_goods_data WHERE " . $subquery . " ORDER BY RAND(id)";

	$query_catalogFTS = "SELECT * FROM `catalog_goods_data` WHERE active=1 AND (
  MATCH(field17, field1, field32, field33, field34, field35, field36, field37, field38, field44, field45, field46)
  AGAINST('*".$_GET['search']."*' IN BOOLEAN MODE) OR
   MATCH(field17, field1, field32, field33, field34, field35, field36, field37, field38, field44, field45, field46)
  AGAINST('*".switcher_ru($_GET['search'])."*' IN BOOLEAN MODE) OR
   MATCH(field17, field1, field32, field33, field34, field35, field36, field37, field38, field44, field45, field46)
  AGAINST('*".switcher_en($_GET['search'])."*' IN BOOLEAN MODE)) ORDER BY FIELD(`field43`, 0), `field43`, 
  MATCH(field17, field1, field41, field32, field33, field34, field35, field36, field37, field38, field44, field45, field46)
  AGAINST('*".$_GET['search']."*' IN BOOLEAN MODE) DESC";
	$catalog = el_dbselect($query_catalogFTS, 4, $catalog, 'result', true);

	$total_catalog = el_dbselect($query_catalogFTS, 0, $total_catalog, 'result', true);

	if (el_dbnumrows($catalog) > 0) {
		$row_catalog = el_dbfetch($catalog);
		do {
			if (strlen($row_catalog['field3']) > 0) {
				$imgArr = explode(' , ', $row_catalog['field3']);
				$img = str_replace('big_', 'small_', $imgArr[0]);
				$imArr = explode('.', $img);
				$ext = strtolower(end($imArr));
			}

			$link = $catsPath[intval($row_catalog['cat'])] . '/' . $row_catalog['path'] . '.html';

			$findCats[] = $row_catalog['cat'];

			$answer['goods'] .= '<a href="' . $link . '"><div class="pic"><picture>
                    <source type="image/webp" srcset="' . $img . '.webp"/>
                    <source type="image/' . $ext . '" srcset="' . $img . '"/>
                    <img src="' . $img . '"/>
                </picture></div><div class="name">' . el_htext($row_catalog['field1']) . '</div></a>';
		} while ($row_catalog = el_dbfetch($catalog));

		$total_find = el_dbnumrows($total_catalog);

		if($total_find > 4) {
			$answer['goods'] .= '<div class="button"><a href="/catalog/?search=' . $_GET['search'] . '">Показать все '.$total_find.' товар'.el_postfix($total_find, '', 'а', 'ов').'</a></div>';
		}
	}

	$subSql = '';
	if(count($findCats) > 0){
		$subSql = " OR id IN (".implode(",", $findCats).")";
	}
	$s = el_dbselect("SELECT name, path FROM cat WHERE name LIKE '%" . $_GET['search'] . "%'".$subSql, 0, $s, 'result', true);
	if (el_dbnumrows($s) > 0) {
		$rs = el_dbfetch($s);
		do {
			$answer['cats'] .= '<a href="' . $rs['path'] . '/" title="Перейти в раздел"><div class="name">' . $rs['name'] . '</div></a>';
		} while ($rs = el_dbfetch($s));
	}
	$answer['query'] = $query_catalogFTS;
	$answer['htext'] = $_SESSION['highlight'];
	$finish = microtime(true);
	$delta = $finish - $start;
	$answer['time'] = $delta;

	echo json_encode($answer);
}

?>
