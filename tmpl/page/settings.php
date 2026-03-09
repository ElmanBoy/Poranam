<?php
session_start();

if(intval($_SESSION['user_id']) > 0){

include_once $_SERVER['DOCUMENT_ROOT'].'/tmpl/page/blocks/header.php';
?>



                    <?php
                    el_text('el_pageprint', 'text');

                    switch(intval($_SESSION['user_level'])){
                        case (11):
                            $settings_file = 'settings_admin.php';
                            break;
                        case (4):
                        case (5):
                        case (6):
                        case (7):
                        case (8):
                        case (9):
                            $settings_file = 'settings_manager.php';
                            break;
                        case (10):
                            $settings_file = 'settings_user.php';
                            break;
                        /*default:
                            $settings_file = 'login.php';*/
                    }
                    include_once $_SERVER['DOCUMENT_ROOT'].'/tmpl/page/'.$settings_file;
                    ?>

<?
include_once $_SERVER['DOCUMENT_ROOT'].'/tmpl/page/blocks/footer.php';
}else{
    include $_SERVER['DOCUMENT_ROOT'].'/tmpl/page/login.php';
}
?>