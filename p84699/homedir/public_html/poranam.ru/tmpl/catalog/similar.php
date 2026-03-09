<?php
$cat = intval($row_catalog['cat']);
$res = '';
$priceMin = floor($price - ($price / 100 * 10));
$priceMax = ceil($price + ($price / 100 * 10));

$res = el_dbselect("SELECT * FROM catalog_goods_data WHERE 
cat=$cat AND id <> ".intval($row_catalog['id'])." AND ($priceField >= $priceMin AND $priceField <= $priceMax)
 ORDER BY RAND()", 6, $res, 'result', true);
if(el_dbnumrows($res) > 0){
	$row_catalog = el_dbfetch($res);
?>
<section>
	<div class="title">
		<h4>Похожие товары</h4>
	</div>
	<!-- -->
	<?
	do{
		include ($_SERVER['DOCUMENT_ROOT'].'/tmpl/catalog/good_item.php');
	}while($row_catalog = el_dbfetch($res));
	?>
</section>
<?php
}
?>