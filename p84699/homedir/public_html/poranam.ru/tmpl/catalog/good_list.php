<?
if (el_dbnumrows($catalog) > 0) {
    el_paging($pn, $currentPage, $queryString_catalog, $totalPages_catalog, $maxRows_catalog, $tr);
    do {
        include ($_SERVER['DOCUMENT_ROOT'].'/tmpl/catalog/good_item.php');
         } while ($row_catalog = el_dbfetch($catalog));
    el_paging($pn, $currentPage, $queryString_catalog, $totalPages_catalog, $maxRows_catalog, $tr);
}else{
    echo 'К сожалению, ничего не найдено.';
} ?>