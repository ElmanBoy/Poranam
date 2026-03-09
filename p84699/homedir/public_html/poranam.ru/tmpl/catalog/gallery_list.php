<div class="h-one"></div>
<h2>Фотогалерея</h2>
<?php
el_paging($pn, $currentPage, $queryString_catalog, $totalPages_catalog, $maxRows_catalog, $tr);
?>
<div class="row">
<?
if (el_dbnumrows($catalog) > 0) {
    $row = 0;
    $pd = 0;
    do {
        if($row == 3){
            echo '</div><div class="row">';
            $row = 0;
        }
        if($row_catalog['field3'] == 'Проектная документация' && $pd == 0){
            echo '</div><div class="h-one"></div><h2>Проектная документация</h2><div class="row">';
            $pd++;
            $row = 0;
        }

        if($row_catalog['field3'] != 'Отзыв') {
            echo '<div class="col third">
                <a class="fancybox gal-item" data-fancybox="gal" data-caption="' . $row_catalog['field2'] . '" href="' . $row_catalog['field1'] . '">
                <img src="' . $row_catalog['field4'] . '" alt="' . $row_catalog['field2'] . '">
                <span class="caption">' . $row_catalog['field2'] . '</span>
                </a>
                <a href="' . $row_dbcontent['path'] . '/' . $row_catalog['path'] . '.html" style="font-size: 50%;">Страница фотографии</a>
                <div class="h-25"></div>
            </div>';
        }
        if($row_catalog['field3'] == 'Отзыв'){
            echo '</div><div class="h-one"></div><h2>Отзыв</h2><div class="table-wrapper">';
            echo $row_catalog['field5'] . '</div>';
            $pd++;
            $row = 0;
        }
        $row++;
    } while ($row_catalog = el_dbfetch($catalog));

} else {
    echo 'К сожалению, ничего не найдено.';
}
?>
</div>
<?php
el_paging($pn, $currentPage, $queryString_catalog, $totalPages_catalog, $maxRows_catalog, $tr);
?>
<div class="h-50 mobile-hide"></div>