<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/tmpl/page/blocks/header.php';
?>
<nav id="admin_menu">
    <?
    include $_SERVER['DOCUMENT_ROOT'] . '/tmpl/page/blocks/admin_menu.php';
    ?>
</nav>
<style>
    #users_table .el_data{
        min-width: auto;
        width: 15rem;
    }
</style>
<div class="content">
    <div class="wrap">

        <main>
            <div class="box">
                <h1>Пользователи</h1>
            </div>
            <div class="text"><? el_text('el_pageprint', 'text'); ?></div>
            <div class="box">
                <div class="control">
                    <?/*button class="text icon" id="button_initiative_filter"><span
                                class="material-icons">filter_alt</span>Фильтр
                    </button*/?>
                    <?
                    //if ($_SESSION['user_level'] > 0) {
                        ?>
						<button class="text icon" id="button_user_filter"><span class="material-icons">filter_alt</span>Фильтр</button>
                        <?
                        if (isset($_SESSION['login']) && $_SESSION['user_level'] != 10) {
                            if($_SESSION['user_level'] == 11){
                            ?>
							<?/*button class="text icon" id="button_user_new"><span class="material-icons">add_circle</span>Добавить</button*/?>
                                <? }?>
							<?/*button class="text icon user_inactive"><span class="material-icons">lock</span>Заблокировать</button>
                            <button class="text icon user_active"><span class="material-icons">lock_open</span>Разблокировать</button*/?>
							<button class="text icon red user_remove"><span class="material-icons">delete_forever</span>Удалить</button>
							<button class="text icon" id="button_user_mailing"><span class="material-icons">email</span>Рассылка</button>
                        <?
                        }
                    //}
                    ?>
                </div>
            </div>
            <div class="box">
                <div class="scroll_wrap" id="users_table">

                            <? /*
                            //Черновики не показывать незарегистрированным и простым пользователям
                            if(intval($_SESSION['user_level']) == 0 || intval($_SESSION['user_level']) == 10){
                                $_GET['sf14_from'] = 2;
                            }
                            if(intval($_SESSION['user_level']) > 0 && intval($_SESSION['user_level']) < 11){
                                $_GET['sf5'] = array(0, '', $_SESSION['user_subject']);
                                $_GET['sf9'] = array(0, '', $_SESSION['user_region']);
                                $_GET['sf7'] = array(0, '', $_SESSION['user_prof']);
                                $_GET['sf8'] = array(0, '', $_SESSION['user_city']);
                                $_GET['sf9'] = array(0, '', $_SESSION['user_index']);
                                $_GET['sf10'] = array(0, '', $_SESSION['user_street']);
                                $_GET['sf11'] = array(0, '', $_SESSION['user_house']);
                            }*/


                            $cat = $row_dbcontent['cat'] = 6;

                            //echo $_SESSION['user_group'].' | '.$_SESSION['user_direct_group'];

                            /*if(in_array($_SESSION['user_level'], [0, 4, 5, 10, 11])){
                                $_GET['status'] = 'both';
                                $_GET['sf8'] = strlen(trim($_GET['sf8'])) > 0 ? $_GET['sf8'] : '';
                                $_GET['sf9'] = strlen(trim($_GET['sf9'])) > 0 ? $_GET['sf9'] : '';;
                                $_GET['sf10'] = strlen(trim($_GET['sf10'])) > 0 ? $_GET['sf10'] : '';;
                                $_GET['sf11'] = strlen(trim($_GET['sf11'])) > 0 ? $_GET['sf11'] : '';
                                $_GET['sf16'] = strlen(trim($_GET['sf16'])) > 0 ? $_GET['sf16'] : '';;
                            }elseif($_SESSION['user_level'] == 6){
                                $_GET['sf8'] = $_SESSION['user_subject'];
                            }elseif($_SESSION['user_level'] == 7){
                                $_GET['sf10'] = $_SESSION['user_city'];
                            }else{
                                $_GET['sf8'] = $_SESSION['user_subject'];
                                $_GET['sf9'] = $_SESSION['user_region'];
                                $_GET['sf10'] = $_SESSION['user_city'];
                            }*/
                            $is_filter = false;
                            if(isset($_GET['is_filter']) && $_GET['is_filter'] == 1){
                                $is_filter = true;
                            }
                            $groups = getSubGroupsByUser($_SESSION['user_id']);
                            $groupsDown = implode('|', $groups);

                            switch(intval($_SESSION['user_level'])){
                                case 5: //Куратор страна
                                case 6: //Куратор субъекта
                                case 7: //Куратор нас. пункт
                                case 8: //Куратор района
                                    $_GET['sf16'] =  (!empty($_GET['sf16'])) ? $_GET['sf16'] : $groupsDown;//(!empty($_GET['sf16'])) ? $_GET['sf16'] : $_SESSION['user_direct_group'];
                                    break;
                                case 9: //Куратор индекса
                                    $_GET['sf16'] = /*(!empty($_GET['sf16'])) ? $_GET['sf16'] : */$_SESSION['user_direct_group'];
                                    break;
                                default:
                                    $_GET['status'] = 'both';
                                    $_GET['sf8'] = !empty($_GET['sf8']) ? $_GET['sf8'] : '';
                                    $_GET['sf9'] = !empty($_GET['sf9']) ? $_GET['sf9'] : '';;
                                    $_GET['sf10'] = strlen(trim($_GET['sf10'])) > 0 ? $_GET['sf10'] : '';;
                                    $_GET['sf11'] = strlen(trim($_GET['sf11'])) > 0 && !empty($_GET['sf11']) ? $_GET['sf11'] : '';
                                    /*if(intval($_GET['sf16']) > 0) {
                                        $_GET['sf16|sf25'] = $_GET['sf16'];
                                    }*/
                            }

                            el_module('el_pagemodule', '');
                            ?>

                </div>
                <? /*div class="pagination">
                    <div class="arrow">
                        <div class="button icon"><span class="material-icons">chevron_left</span></div>
                    </div>
                    <div class="page"><a href="#">1</a></div>
                    <div class="page"><a href="#">2</a></div>
                    <div class="page"><a href="#">3</a></div>
                    <div class="page"><a href="#">4</a></div>
                    <div class="page current">5</div>
                    <div class="page"><a href="#">6</a></div>
                    <div class="page"><a href="#">7</a></div>
                    <div class="page"><a href="#">8</a></div>
                    <div class="page"><a href="#">9</a></div>
                    <div class="page"><a href="#">10</a></div>
                    <div class="page"><a href="#">11</a></div>
                    <div class="page"><a href="#">12</a></div>
                    <div class="page"><a href="#">13</a></div>
                    <div class="page"><a href="#">14</a></div>
                    <div class="page"><a href="#">15</a></div>
                    <div class="page dotted"><a href="#">....</a></div>
                    <div class="arrow">
                        <div class="button icon"><span class="material-icons">chevron_right</span></div>
                    </div>
                </div*/ ?>
            </div>


        </main>
        <!-- <div class="donate">
        <div class="wrap">
            <div class="box">Donate section</div>
        </div>

    </div> -->

    </div>
</div>
    <script src="/js/users.js?ver=<?=el_genpass()?>"></script>
<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/tmpl/page/blocks/footer.php';
?>