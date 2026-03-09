<?php
//Участие в голосованиях
function getUserVoteResults(){
    $res = null;
    $res = el_dbselect("SELECT COUNT(id) AS c FROM catalog_initresult_data 
        WHERE field1 = '".intval($_SESSION['user_id'])."'",
        0, $res, 'row', true);
    return intval($res['c']);
}
//Получение количества инициатив пользователя
function getUserInits($initType = array()){
    $res = null;
    $subQuery = '';
    if(count($initType) > 0){
        $subQuery = ' AND (field14 = '.implode(' OR field14 = ', $initType).')';
    }
    $res = el_dbselect("SELECT COUNT(id) AS c FROM catalog_init_data 
        WHERE active = 1 AND field4 = '".intval($_SESSION['user_id'])."'".$subQuery,
        0, $res, 'row', true);
    return intval($res['c']);
}
//Получение ставок баллов за действия - $id
function getUserScorePrice($id){
    $res = null;
    $res = el_dbselect("SELECT field3 FROM catalog_scores_data WHERE id=".intval($id),
        0, $res, 'row', true);
    return intval($res['field3']);
}
//Рассчет полученных баллов за действия - $id
function getUserScore($id, $initType = array()){
    $initCount = getUserInits($initType);
    return intval(getUserScorePrice($id) * $initCount);
}

//Рассчет за привлечение
function getReferScore(){
    $res = el_dbselect("SELECT COUNT(id) AS c FROM catalog_users_data 
        WHERE field14 = ".intval($_SESSION['user_id']), 0, $res, 'row', true);
    return intval(getUserScorePrice(6) * $res['c']);
}

$initScore = getUserScore(3);
$eventScore = getUserScore(4);
$pollScore = getUserScore(1);
$referScore = getReferScore();

$totalScore = $initScore + $eventScore + $pollScore + $referScore;

function getStatUser($query = ''){
    $res = null;
    $subQuery = ($query == '') ? '' : ' WHERE '.$query;
    $res = el_dbselect("SELECT COUNT(id) AS count FROM catalog_users_data".$subQuery,
        0, $res, 'row', true);
    return number_format(intval($res['count']), 0, '', ' ');
}

include_once $_SERVER['DOCUMENT_ROOT'].'/tmpl/page/blocks/header.php';
?>
    <!-- Навигация для зарегистрированных. Разная в зависимости от прав -->
    <nav id="admin_menu">
        <?
        include $_SERVER['DOCUMENT_ROOT'] . '/tmpl/page/blocks/admin_menu.php';
        ?>
    </nav>
<?php
if(isset($_SESSION['user_id'])){

    el_text('el_pageprint', 'text');

    switch(intval($_SESSION['user_level'])){
        case (11):
            $settings_file = 'statistic_admin.php';
            break;
        case (4):
        case (5):
        case (6):
        case (7):
        case (8):
        case (9):
            $settings_file = 'statistic_manager.php';
            break;
        case (10):
            $settings_file = 'statistic_user.php';
            break;
        /*default:
            $settings_file = 'login.php';*/
    }
    @include_once $_SERVER['DOCUMENT_ROOT'].'/tmpl/page/'.$settings_file;

}else{
    include_once $_SERVER['DOCUMENT_ROOT'].'/tmpl/page/login.php';
}
    ?>
    <script src="/js/statistic.js?ver=<?=el_genpass()?>"></script>
<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/tmpl/page/blocks/footer.php';
?>