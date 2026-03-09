<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php');
//print_r($_POST);
$groups = array('script' => '<script>document.location.href="?logout"</script>');
if(isset($_SESSION['login'])){
    if(isset($_POST['tags'])){
        $groups = el_getProductsGroups($_POST['tags'], $_POST['sort']);
    }else{
        $groups = el_getProductsGroups();
    }
}
/*$groups['columns'] = '<div class="col col_01">
                        <div class="box">
                            <div class="title">
                                <h1>Товарные группы</h1>
                                <div class="icon sort" data-sort="bar"><img src="images/icon-sorting.svg" alt="Сортировать" /></div>
                            </div>
                        </div>
                    </div>
                    <div class="col col_02">
                        <div class="box">
                            <div class="title">
                                <h1>Маржинальность</h1>
                                <div class="icon sort" data-sort="margin"><img src="images/icon-sorting.svg" alt="Сортировать" /></div>
                            </div>
                        </div>
                    </div>
                    <div class="col col_03">
                        <div class="box">
                            <div class="title">
                                <h1>LTV</h1>
                                <div class="icon sort" data-sort="ltv"><img src="images/icon-sorting.svg" alt="Сортировать" /></div>
                            </div>
                        </div>
                    </div>
                    <div class="col col_04">
                        <div class="box">
                            <div class="title">
                                <h1>Узнаваемость</h1>
                                <div class="icon sort" data-sort="recog"><img src="images/icon-sorting.svg" alt="Сортировать" /></div>
                            </div>
                        </div>
                    </div>';*/
echo json_encode($groups);
?>