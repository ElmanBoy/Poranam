<div class="activity">
    <div class="item">
        <div class="title">
            <div class="date"><?=el_date1(str_replace(' 00:00:00', '', $row_catalog['field2']))?></div>
            <h2><?=el_htext(checkUpperCase($row_catalog['field1']))?></h2>
        </div>
        <div class="description">
            <p><?=el_htext(preg_replace('/style="(.*)"/Umi', '', $row_catalog['field23']))?></p>
        </div>
    </div>
    <div class="link news"><a href="/deyatelnost/">К списку мероприятий</a></div>
</div>
