<?
if(!isset($_GET['path'])){
    include $_SERVER['DOCUMENT_ROOT'].'/tmpl/page/blocks/filter.php';
}
?>
<section>
    <? el_text('el_pageprint','text')?>
</section>
    <? el_module('el_pagemodule', '')?>