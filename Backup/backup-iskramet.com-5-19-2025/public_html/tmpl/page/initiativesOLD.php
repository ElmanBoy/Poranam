<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/tmpl/page/blocks/header.php';



switch(intval($_SESSION['user_level'])){
    case (11):
        $initiative_file = 'initiative_admin.php';
        break;
    case (4):
    case (5):
    case (6):
    case (7):
    case (8):
    case (9):
        $initiative_file = 'initiative_manager.php';
        break;
    case (10):
        $initiative_file = 'initiative_user.php';
        break;
    default:
        $initiative_file = 'initiatives.php';
}
include_once $_SERVER['DOCUMENT_ROOT'].'/tmpl/page/'.$initiative_file;
?>
<script src="/js/initiatives.js"></script>
<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/tmpl/page/blocks/footer.php';
?>