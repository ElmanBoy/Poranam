<?php
if(strlen($row_catalog['field3']) > 0){
    $imgArr = explode(' , ', $row_catalog['field3']);
    $img = str_replace('big_', 'small_', $imgArr[0]);
    $imArr = explode('.', $img);
    $ext = end($imArr);
}
switch($row_catalog['field52']){
    case 'Хит продаж':
        $class = ' hit';
        $old_price = '';
        $price = $row_catalog['field26'];
        break;
    case 'Скидка':
        $class = ' discount';
        $discount = ($row_catalog['field26'] / 100) * 5;
        $price = $row_catalog['field25'];//round($row_catalog['field26'] - $discount);
        $old_price = '<div class="price_old">'.number_format($row_catalog['field26'], 0, ', ', ' ').'</div>';
        break;
    default:
        $class = '';
        $old_price = '';
        $price = $row_catalog['field26'];
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
$link = str_replace('//', '/', $catsPath[intval($row_catalog['cat'])].'/'.$row_catalog['path'].'.html'.$queryString_catalog);
?>
<div class="box">
    <div class="item">
        <div class="pic<?=$class.$box_class?>">
            <a href="<?=$link?>">
                <picture>
                    <source type="image/webp" srcset="<?=$img?>.webp"/>
                    <source type="image/<?=$ext?>" srcset="<?=$img?>"/>
                    <img src="<?=$img?>"/>
                </picture>
            </a>
			<div class="quick_show" data-href="<?=$link.$queryString_catalog?>">Быстрый просмотр</div>
        </div>
        <div class="info">
            <div class="name"><a href="<?=$link?>"><?=el_htext($row_catalog['field1'])?></a></div>
            <?=$old_price?>
            <div class="price"><?=number_format($price, 0, ', ', ' ')?></div>
            <?
            if(intval($row_catalog['field43']) == 0) {
                ?>
                <div class="stock_out">Нет в наличии</div>
                <?
            }else{
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
        </div>
    </div>
</div>
