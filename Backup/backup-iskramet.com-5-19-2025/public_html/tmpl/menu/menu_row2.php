<?
if($row_menupart['path'] != '') {
    $pArr = explode('/', $row_dbcontent['path']);
    $cl = '';
    if($row_menupart['path'] == '/'.$pArr[1] || $row_menupart['path'] == $row_dbcontent['path']){
        $cl = ' class="current"';
    }
    if ($hasChild) {
        echo '<li'.$cl.'><a href="'.$row_menupart['path'].'">' . $row_menupart['name'] . '</a>
	<ul>';
        el_menupart($row_menupart['path'], 'active', 'current', 'menu_row2.php', false);
        echo '</ul></li>';
    } else {
        ?>
        <li<?=$cl?>><a href="<?= $row_menupart['path'] ?>"><?= $row_menupart['name'] ?></a></li>
    <? }
}?>
