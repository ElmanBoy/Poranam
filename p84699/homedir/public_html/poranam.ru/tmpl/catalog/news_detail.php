
    <div class="item">
        <div class="title">
            <h3><?=el_htext(checkUpperCase($row_catalog['field1']))?></h3>
        </div>
        <div class="description">
            <?
            $img = (is_file($_SERVER['DOCUMENT_ROOT'].$row_catalog['field6'])) ? $row_catalog['field6'] : '/images/blank_img.jpg';
            ?>
            <img src="<?=$img?>" alt="<?=htmlspecialchars($row_catalog['field1'])?>">
            <p><?=el_htext(preg_replace('/style="(.*)"/Umi', '', $row_catalog['field5']))?></p>
        </div>
        <div class="published">
            <div class="icons">
                <object type="image/svg+xml" data="/images/time.svg"> </object>
            </div>
            <div class="date"><?=el_date1(str_replace(' 00:00:00', '', $row_catalog['field2']))?></div>
        </div>
    </div>
    <div class="link more news"><a href="/o-tsentre/arkhiv-novostey/">К списку новостей</a></div>

