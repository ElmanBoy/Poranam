<div class="activity">
<?php
do {
    $status = '<span style="color:green">Идёт</span>';
    if(intval($row_catalog['field14']) == 15){
        $status = '<span style="color:red">Завершено</span>';
    }
    ?>

    <div class="item">
        <div class="title">
            <div class="date"><?=el_date1(str_replace(' 00:00:00', '', $row_catalog['field2'])).' '.$status?> </div>
            <h2><?=checkUpperCase($row_catalog['field1'])?></h2>
        </div>
        <?
        if(strlen($row_catalog['field23']) > 0){
        ?>
        <div class="description">
            <p><?=$row_catalog['field21']?></p>
            <div class="link news"><a href="/deyatelnost/?id=<?=$row_catalog['id']?>">Как всё прошло</a></div>
        </div>
        <?
        }
        ?>
    </div>
    <?php
} while ($row_catalog = el_dbfetch($catalog));
?>
</div>
