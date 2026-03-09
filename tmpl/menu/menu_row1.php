<?
$pathArr = explode('/', $row_menupart['path']);
$res = '';
if($hasChild){
    echo '<li class="parent">
    <a href="'.$row_menupart['path'].'"'.$clas.'>'.$row_menupart['name'].'</a>
	<ul class="sub_parent">';
	el_menupart($row_menupart['path'], 'active', 'active', 'menu_row1.php', false, $level++);
	echo '</ul></li>';
}else{
    $existGoods = el_dbselect("SELECT COUNT(id) AS exist FROM catalog_goods_data 
    WHERE (cat=".$row_menupart['id']." OR cat LIKE '% ".$row_menupart['id']." %') AND active=1",
        0, $res, 'row', true);
    if($existGoods['exist'] > 0){
    ?>
	<li class="parent"><a href="<?=$row_menupart['path']?>/"<?=($row_menupart['left']=='Y')?' target="_blank"':''?> <?=$clas?>><?=$row_menupart['name']?></a></li>
<? }
}?>
