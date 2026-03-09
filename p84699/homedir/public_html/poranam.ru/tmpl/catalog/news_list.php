<?php
if(strlen(trim($_GET['tag'])) > 0){
    echo '<p>Новости с тегом &laquo;'.strip_tags(addslashes(str_replace(array('\'', '"'), '', $_GET['tag']))).'&raquo;</p>';
}
do {
    ?>

    <div class="item">
        <div class="title">
            <h3><?=checkUpperCase($row_catalog['field1'])?></h3>
        </div>
        <div class="description">
            <?
            $img = (is_file($_SERVER['DOCUMENT_ROOT'].$row_catalog['field6'])) ? $row_catalog['field6'] : '/images/blank_img.jpg';
            ?>
            <img src="<?=$img?>" alt="<?=htmlspecialchars($row_catalog['field1'])?>">
            <p><?=$row_catalog['field3']?></p>
            <div class="link more news"><a href="/o-tsentre/arkhiv-novostey/<?=$row_catalog['path']?>.html">Полный текст</a></div>
        </div>
        <div class="published">
            <div class="icons">
                <object type="image/svg+xml" data="/images/time.svg"> </object>
            </div>
            <div class="date"><?=el_date1(str_replace(' 00:00:00', '', $row_catalog['field2']))?></div>
        </div>
    </div>
    <?php
} while ($row_catalog = el_dbfetch($catalog));
?>

