<?php
//Получаем бренды
$exc = el_dbselect("SELECT id, field1, field3 FROM `catalog_brands_data`", 0, $exc);
$allBrands = array();
$excl = el_dbfetch($exc);
do{
	$allBrands[$excl['id']] = $excl['field3'];
}while($excl = el_dbfetch($exc));
//Фотки
if (strlen($row_catalog['field3']) > 0) {
	$imgArr = explode(' , ', $row_catalog['field3']);
}
//Регистрируем просмотр товара у пользователя
$goodsArr = array();
$goodsArr = json_decode($_COOKIE['goods_view']);
if (!@in_array($row_catalog['id'], $goodsArr)) {
	$goodsArr[] = $row_catalog['id'];
	@setcookie('goods_view', json_encode($goodsArr), time() + 60 * 60 * 24 * 30 * 12);
	$res = el_dbselect("UPDATE catalog_goods_data SET field50 = field50 + 1 
WHERE id=" . $row_catalog['id'], 0, $res, 'result', true);
}
switch ($row_catalog['field52']) {
	case 'Хит продаж':
		$class = ' pic hit';
		$old_price = '';
		$price = $row_catalog['field26'];
        $bonuses = true;
		break;
	case 'Скидка':
		$class = ' pic discount';
		$discount = ($row_catalog['field26'] / 100) * 5;
		$price = $row_catalog['field25'];//round($row_catalog['field26'] - $discount);
		$old_price = '<div class="price_old">' . number_format($row_catalog['field26'], 0, ', ', ' ') . '</div>';
        $bonuses = false;
		break;
	default:
		$class = '';
		$old_price = '';
		$price = $row_catalog['field26'];
        $bonuses = true;
		break;
}
$goodActive = '';
$goodTitle = 'Положить в корзину';
if(is_array($_SESSION['cart']) && array_key_exists($row_catalog['id'], $_SESSION['cart'])) {
	$goodActive = ' active';
	$goodTitle = 'Товар в корзине';
}

$favActive = '';
$favTitle = 'В избранное';
if(is_array($_SESSION['favorites']) && array_key_exists($row_catalog['id'], $_SESSION['favorites'])) {
	$favActive = ' active';
	$favTitle = 'В избранном';
}

//Следующий товар
/*
if(strlen($_GET['search']) < 2) {
	$prev = el_dbselect(str_replace("AND  AND", " AND ", "SELECT `path`, cat FROM catalog_goods_data WHERE cat = " .
		$row_catalog['cat'] . " AND id > ". $row_catalog['id'] . " AND " . $subquery . " " . $sortquery), 1,
		$prev, 'row', true);
//Предыдущий товар
	$next = el_dbselect(str_replace("AND  AND", " AND ", "SELECT `path`, cat FROM catalog_goods_data WHERE cat = " . $row_catalog['cat'] . " AND id < "
		. $row_catalog['id'] . " AND " . $subquery . " " . $sortquery), 1,
		$next, 'row', true);
}
if (strlen(trim($prev['path'])) > 0) {
	?>
	<div id="pop_nav_prev" class="pop_nav" title="Предыдущий товар" data-href="<?= $catsPath[intval($prev['cat'])]. '/' . $prev['path'] ?>.html<?= $queryString_catalog ?>"></div>
	<?php
}*/
?>
	<div class="card">
		<div class="photo">
			<div class="box">
				<div class="item">
					<div class="fotorama<?= $class ?>">
						<?
						for ($i = 0; $i < count($imgArr); $i++) {
							$imArr = explode('.', $imgArr[$i]);
							$ext = end($imArr);
							?>
							<?/*picture>
                            <source type="image/webp" srcset="<?=$imgArr[$i]?>.webp"/>
                            <source type="image/<?=$ext?>" srcset="<?=$imgArr[$i]?>"/>
                            <img loading="lazy" src="<?=$imgArr[$i]?>"/>
                        </picture*/
							?>
							<img itemprop="image" loading="lazy" src="<?= $imgArr[$i] ?>"
							     alt='<?= $row_catalog['field1'] ?>'>
							<?
						}
						if (strlen(trim($row_catalog['field42'])) > 0) {
							echo '<a href="' . $row_catalog['field42'] . '"></a>';
						}
						?>
					</div>
				</div>
			</div>
		</div>
		<div class="desc">
			<div class="box">
				<div class="item">
					<div class="text">
						<div class="title">
							<h1><?= el_htext($row_catalog['field1']) ?></h1>
						</div>
						<!-- <p><?= el_htext($row_catalog['field41']) ?></p> -->
					</div>
					<div class="buy">
						<?= $old_price ?>
						<div class="price"><?= number_format($price, 0, ', ', ' ') ?></div>
						<?
						if (intval($row_catalog['field43']) == 0) {
							?>
							<div class="stock_out">Нет в наличии</div>
							<?
						} else {
							?>
							<div class="bracket icon<?=$goodActive?>">
								<a href="/cart/?add=<?=$row_catalog['id']?>" data-value="<?=$row_catalog['id']?>"
								   title="<?=$goodTitle?>"> </a>
							</div>
							<?
						}
						?>
						<div class="icon favorite<?=$favActive?>">
							<a href="/favorits/?add=<?=$row_catalog['id']?>" data-value="<?=$row_catalog['id']?>"
							   title="<?=$favTitle?>"> </a>
						</div>
                        <? if($bonuses){ ?>
						    <div class="bonus" title="Бонусные баллы"><?= el_calcBonus($row_catalog['field26']) ?></div>
                        <? } ?>
					</div>
					<div class="addon">
						<?
						if (strlen($row_catalog['field10']) > 0) {
							?>
							<div class="param">
								Возраст:<span> от <?= $row_catalog['field10'] ?> <?= el_postfix(intval($row_catalog['field10']), 'года', 'лет', 'лет') ?></span>
							</div>
							<?
						}
						if (strlen($row_catalog['field11']) > 0) {
							?>
							<div class="param">
								Пол:<span><?= (strlen($row_catalog['field11']) > 0) ? $row_catalog['field11'] : 'любой' ?></span>
							</div>
							<?
						}
						if (strlen($row_catalog['field17']) > 0) {
							?>
							<div class="param">Бренд:<span itemprop="brand"><?= $allBrands[$row_catalog['field17']] ?></span></div>
							<?
						}
						if (strlen($row_catalog['field16']) > 0) {
							?>
							<div class="param">Страна производства:<span><?= $row_catalog['field16'] ?></span></div>
							<?
						}
						?>
					</div>
				</div>
			</div>
		</div>
		<div class="c_u_but">
			<button id="more_info"
			        data-href="<?= $catsPath[intval($row_catalog['cat'])] . '/' .$row_catalog['path'] ?>.html"
			        class="button">Подробнее
			</button>
			<button id="close_info" class="button">Закрыть</button>
		</div>
	</div>
<?php
/*if (strlen(trim($next['path'])) > 0) {
	?>
	<div id="pop_nav_next" class="pop_nav" title="Следующий товар" data-href="<?= $catsPath[intval($next['cat'])]. '/' . $next['path'] ?>.html<?= $queryString_catalog ?>"></div>
	<?php
}*/
?>