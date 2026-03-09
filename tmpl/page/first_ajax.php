<?php
$catsPath = el_getGoodPath();
$sliderItems = array();
$allGoods = array();

function shuffle_assoc(&$array)
{
    $keys = array_keys($array);

    shuffle($keys);

    foreach ($keys as $key) {
        $new[$key] = $array[$key];
    }

    $array = $new;

    return true;
}

function getRandomGoods($allGoods, $box_class)
{
    global $catsPath;
    if (count($allGoods) > 0) {
        $newGoodsRand = array_rand($allGoods, 3);
        for ($i = 0; $i < count($newGoodsRand); $i++) {
            $row_catalog = $allGoods[$newGoodsRand[$i]];
            $row_dbcontent['path'] = $catsPath[intval($allGoods[$newGoodsRand[$i]]['cat'])];
            include $_SERVER['DOCUMENT_ROOT'] . '/tmpl/catalog/good_item.php';
        }
    }
}

function getGoodList($n, $type)
{
    global $allGoods;
    if (el_dbnumrows($n) > 0) {
        $rn = el_dbfetch($n);
        mysqli_data_seek($n, 0);
        do {
            if (!is_null($rn) && !@in_array($rn, $allGoods[$type])) {
                $allGoods[$type][] = $rn;
            }
        } while ($rn = el_dbfetch($n));
    }
    extractToSlider($n, $type);
}

function extractToSlider($n, $type)
{
    global $sliderItems, $allGoods;
    //$randKeys = array_rand($allGoods[$type], 2); print_r($randKeys);
    shuffle($allGoods[$type]);
    if(is_array($allGoods[$type][0]) && is_array($allGoods[$type][1])) {
        for ($i = 0; $i < 2; $i++) {
            $sliderItems[] = $allGoods[$type][$i];
            array_splice($allGoods[$type], $i, 1);
        }
    }else{
        extractToSlider($n, $type);
    }
}

$discount = el_dbselect("SELECT * FROM catalog_goods_data WHERE field52='Скидка' AND field43 > 0 ORDER BY RAND(id)",
    15, $discount, 'result', true);
getGoodList($discount, 'discount');
$new = el_dbselect("SELECT * FROM catalog_goods_data WHERE active=1 AND field43 > 0 ORDER BY RAND(id)",
    15, $new, 'result', true);
getGoodList($new, 'new');
$hit = el_dbselect("SELECT * FROM catalog_goods_data WHERE field52='Хит продаж' AND field43 > 0 ORDER BY RAND(id)",
    15, $hit, 'result', true);
getGoodList($hit, 'hit');
//echo '<pre>';print_r($sliderItems);echo '</pre>';
?>

            <? include $_SERVER['DOCUMENT_ROOT'].'/tmpl/page/blocks/filter.php' ?>

                <? el_text('el_pageprint', 'text') ?>

<section>
	<div class="show">
		<div class="slider">
			<div class="box">
				<div class="swiper-container">
					<div class="swiper-wrapper">
						<?
						for ($s = 0; $s < 4; $s++) {
							$item = $sliderItems[$s];
							$imgArr = explode(' , ', $item['field3']);
							$img = $imgArr[0];//str_replace('big_', 'small_', $imgArr[0]);
							$imArr = explode('.', $img);
							$ext = strtolower(end($imArr));
							$link = $catsPath[intval($item['cat'])] . '/' . $item['path'] . '.html';
							switch ($item['field52']) {
								case 'Хит продаж':
									$class = 'hit';
									$price = $item['field26'];
									$old_price = '';
									break;
								case 'Скидка':
									$class = 'discount';
									$discount = ($item['field26'] / 100) * 5;
									$price = round($item['field26'] - $discount);
									$old_price = '<div class="price_old">' . number_format($item['field26'], 0, ', ', ' ') . '</div>';
									break;
								default:
									$class = 'new';
									$price = $item['field26'];
									$old_price = '';
									break;
							}
							$goodActive = '';
							$goodTitle = 'Положить в корзину';
							if(is_array($_SESSION['cart']) && array_key_exists($item['id'], $_SESSION['cart'])) {
								$goodActive = ' active';
								$goodTitle = 'Товар в корзине';
							}
							?>
							<div class="swiper-slide">
								<div class="item">
									<div class="pic <?= $class ?>">
										<a href="<?= $link ?>"
										   title="<?= htmlspecialchars($item['field1']) ?>">
											<picture>
												<source type="image/webp" srcset="<?= $img ?>.webp"/>
												<source type="image/<?= $ext ?>" srcset="<?= $img ?>"/>
												<img src="<?= $img ?>"
												     alt="<?= htmlspecialchars($item['field1']) ?>"/>
											</picture>
										</a>
									</div>
									<div class="info">
										<div class="name"><a href="<?= $link ?>"><?= $item['field1'] ?></a>
										</div>
										<?= $old_price ?>
										<div class="price"><?= number_format($price, 0, ', ', ' ') ?></div>
										<div class="bracket icon<?=$goodActive?>">
											<a href="/cart/?add=<?=$item['id']?>"
											   data-value="<?=$item['id']?>"
											   title="<?=$goodTitle?>"> </a>
										</div>
										<div class="favorite icon">
											<a href="#" title="В избранное"> </a>
										</div>
									</div>
								</div>
							</div>
							<?
						}
						?>
					</div>
					<div class="arrow prev" title="Назад"></div>
					<div class="arrow next" title="Вперёд"></div>
				</div>
			</div>
		</div>
		<div class="right">
			<?
			$item = $sliderItems[4];
			$imgArr = explode(' , ', $item['field3']);
			$img = str_replace('big_', 'small_', $imgArr[0]);
			$imArr = explode('.', $img);
			$ext = strtolower(end($imArr));
			$link = $catsPath[intval($item['cat'])] . '/' . $item['path'] . '.html';
			switch ($item['field52']) {
				case 'Хит продаж':
					$class = 'hit';
					$price = $item['field26'];
					$old_price = '';
					break;
				case 'Скидка':
					$class = 'discount';
					$discount = ($item['field26'] / 100) * 5;
					$price = round($item['field26'] - $discount);
					$old_price = '<div class="price_old">' . number_format($price, 0, ', ', ' ') . '</div>';
					break;
				default:
					$class = 'new';
					$price = $item['field26'];
					$old_price = '';
					break;
			}
			$goodActive = '';
			$goodTitle = 'Положить в корзину';
			if(is_array($_SESSION['cart']) && array_key_exists($item['id'], $_SESSION['cart'])) {
				$goodActive = ' active';
				$goodTitle = 'Товар в корзине';
			}
			?>
			<div class="show_item">
				<div class="box">
					<div class="item">
						<div class="pic <?= $class ?>">
							<a href="<?= $link ?>" title="<?= htmlspecialchars($item['field1']) ?>">
								<picture>
									<source type="image/webp" srcset="<?= $img ?>.webp"/>
									<source type="image/<?= $ext ?>" srcset="<?= $img ?>"/>
									<img src="<?= $img ?>" alt="<?= htmlspecialchars($item['field1']) ?>"/>
								</picture>
							</a>
						</div>
						<div class="info">
							<div class="name"><a href="<?= $link ?>"><?= $item['field1'] ?></a></div>
							<?= $old_price ?>
							<div class="price"><?= number_format($price, 0, ', ', ' ') ?></div>
							<div class="bracket icon<?=$goodActive?>">
								<a href="/cart/?add=<?=$item['id']?>"
								   data-value="<?=$item['id']?>"
								   title="<?=$goodTitle?>"> </a>
							</div>
							<div class="favorite icon">
								<a href="#" title="В избранное"> </a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?
			$item = $sliderItems[5];
			$imgArr = explode(' , ', $item['field3']);
			$img = str_replace('big_', 'small_', $imgArr[0]);
			$imArr = explode('.', $img);
			$ext = strtolower(end($imArr));
			$link = $catsPath[intval($item['cat'])] . '/' . $item['path'] . '.html';
			switch ($item['field52']) {
				case 'Хит продаж':
					$class = 'hit';
					$price = $item['field26'];
					$old_price = '';
					break;
				case 'Скидка':
					$class = 'discount';
					$discount = ($item['field26'] / 100) * 5;
					$price = round($item['field26'] - $discount);
					$old_price = '<div class="price_old">' . number_format($price, 0, ', ', ' ') . '</div>';
					break;
				default:
					$class = 'new';
					$price = $item['field26'];
					$old_price = '';
					break;
			}
			$goodActive = '';
			$goodTitle = 'Положить в корзину';
			if(is_array($_SESSION['cart']) && array_key_exists($item['id'], $_SESSION['cart'])) {
				$goodActive = ' active';
				$goodTitle = 'Товар в корзине';
			}
			?>
			<div class="show_item">
				<div class="box">
					<div class="item">
						<div class="pic <?= $class ?>">
							<a href="<?= $link ?>" title="<?= htmlspecialchars($item['field1']) ?>">
								<picture>
									<source type="image/webp" srcset="<?= $img ?>.webp"/>
									<source type="image/<?= $ext ?>" srcset="<?= $img ?>"/>
									<img src="<?= $img ?>" alt="<?= htmlspecialchars($item['field1']) ?>"/>
								</picture>
							</a>
						</div>
						<div class="info">
							<div class="name"><a href="<?= $link ?>"><?= $item['field1'] ?></a></div>
							<?= $old_price ?>
							<div class="price"><?= number_format($price, 0, ', ', ' ') ?></div>
							<div class="bracket icon<?=$goodActive?>">
								<a href="/cart/?add=<?=$item['id']?>"
								   data-value="<?=$item['id']?>"
								   title="<?=$goodTitle?>"> </a>
							</div>
							<div class="favorite icon">
								<a href="#" title="В избранное"> </a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
            <section>

                <div class="title">
                    <h4><a href="/tovary-so-skidkoy/">Товары со скидками</a></h4>
                </div>
                <!-- -->
                <?
                getRandomGoods($allGoods['discount'], ' discount');
                ?>

                <div class="box medium">
                    <div class="item">
                        <div class="big">
                            <h2><a href="/tovary-so-skidkoy/" title="Товары со скидками">Все товары со скидками</a></h2>
                        </div>
                    </div>
                </div>
                <!-- -->
            </section>
            <section>
                <div class="title">
                    <h4><a href="/novinki/">Новинки</a></h4>
                </div>
                <!-- -->
                <?
                getRandomGoods($allGoods['new'], ' new');
                ?>

                <!-- -->
                <div class="box medium">
                    <div class="item">
                        <div class="big">
                            <h2><a href="/novinki/" title="Все новинки">Все новинки</a></h2>
                        </div>
                    </div>
                </div>
            </section>
            <section>
                <div class="title">
                    <h4><a href="/khity-prodazh/">Хиты продаж</a></h4>
                </div>
                <!-- -->
                <?
                getRandomGoods($allGoods['hit'], ' hit');
                ?>

                <!-- -->
                <div class="box medium">
                    <div class="item">
                        <div class="big">
                            <h2><a href="/khity-prodazh/" title="Все хиты продаж">Все хиты продаж</a></h2>
                        </div>
                    </div>
                </div>
            </section>