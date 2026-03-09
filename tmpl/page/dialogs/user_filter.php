<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';
if (el_checkAjax()) {

    $_GET = $_POST['params'];
    ?>
    <div class="pop_up">
        <div class="title">
            <h2>Фильтр по пользователям</h2>
            <div class="close" onclick="pop_up_filter_users_close(); return false"><span class="material-icons">highlight_off</span>
            </div>
        </div>
        <section>

            <form method="get">
                <input type="hidden" name="is_filter" value="1">
                <h3>Основные</h3>
                <div class="group">
                    <div class="item">
                        <div class="el_data">
                            <label for="uid">ID</label>
                            <input class="el_input" id="uid" name="uid" type="text" placeholder="например: 100000-1" value="<?=$_GET['uid']?>">
                        </div>
                    </div>
                    <?
                    if ($_SESSION['user_level'] > 0) {
                        ?>
                        <?/*div class="item">
                            <select data-label="Статус" id="ustatus" name="status">
                                <option selected value="1"<?= $_GET['status'] == '1' ? ' selected' : '' ?>>Активный
                                </option>
                                <option value="0"<?= $_GET['status'] == '0' ? ' selected' : '' ?>>Заблокирован</option>
                            </select>

                        </div*/?>

                        <div class='item'>
                            <select data-label='Входит в группу' id='sf16' name='sf16'>
                                <option value="">Без группы</option>
                                <?
                                $g = null;
                                $allow = getSubGroupsByUser($_SESSION['user_id']);
                                $g = el_dbselect('SELECT id, field1, field2 FROM catalog_groups_data WHERE active = 1 ORDER BY field1, field2', 0, $g);
                                if(el_dbnumrows($g) > 0){
                                    $rg = el_dbfetch($g);
                                    do{
                                        if($_SESSION['user_level'] == 11){
                                            ?>
                                            <option value='<?=$rg['id']?>'<?= $_GET['sf16'] == $rg['id'] ? ' selected' : '' ?>>
                                                <?=$rg['field1'].(strlen($rg['field2']) > 0 ? '-'.$rg['field2'] : '')?>
                                            </option>
                                            <?
                                        }elseif(in_array($rg['id'], $allow)){
                                                ?>
                                                <option value='<?=$rg['id']?>'<?= $_GET['sf16'] == $rg['id'] ? ' selected' : '' ?>>
                                                    <?=$rg['field1'].(strlen($rg['field2']) > 0 ? '-'.$rg['field2'] : '')?>
                                                </option>
                                                <?

                                        }
                                    }while($rg = el_dbfetch($g));
                                } 
                                /*$g = getSubGroupsNamesByUser($_SESSION['user_id']); print_r($g);
                                foreach($g as $gid => $gname){
                                    ?>
                                    <option value='<?=$gname[0]?>'<?= $_GET['sf16'] == $gname[0] ? ' selected' : '' ?>>
                                        <?=$gname[1]?>
                                    </option>
                                    <?
                                }*/
                                ?>
                            </select>

                        </div>
                        <?
                    }
                    ?>
                </div>

                    <h3>Личные данные</h3>
                <div class="group">
                <?
                    if ($_SESSION['user_level'] > 0) {
                        ?>

                        <?/*div class="item">
                            <div class="el_data">
                                <label for="sname">Фамилия</label>
                                <input class="el_input" id="sname" name="sname" value="<?= $_GET['sname'] ?>"
                                       type="text">
                            </div>
                        </div>
                        <div class="item">
                            <div class="el_data">
                                <label for="name">Имя</label>
                                <input class="el_input" id="name" name="name" value="<?= $_GET['name'] ?>" type="text">
                            </div>
                        </div>
                        <div class="item">
                            <div class="el_data">
                                <label for="tname">Отчество</label>
                                <input class="el_input" id="tname" name="tname" value="<?= $_GET['tname'] ?>"
                                       type="text">
                            </div>
                        </div*/?>
                        <?
                        }
                    ?>
                        <div class="item">
                            <select multiple data-label="Профессия" data-place="Выберите" name="sf7">
                                <?= el_buildRegistryList('proffesions', $_GET['sf7'], false) ?>
                            </select>
                        </div>
                        <?
                        if($_SESSION['user_level'] > 0) {
                        ?>
                        <?/*div class="item">
                            <div class="el_data">
                                <label for="uphone">Телефон</label>
                                <input class="el_input" id="uphone" name="sf5" value="<?= urldecode($_GET['sf5']) ?>" type="tel">
                            </div>
                        </div*/?>
                        <div class="item">
                            <div class="el_data">
                                <label for="umail">Почта</label>
                                <input class="el_input" id="umail" name="sf2" value="<?= $_GET['sf2'] ?>" type="text">
                            </div>
                        </div>
                            <?
                        }
                        ?>
                        <div class='item'>
                            <?
                            $themes = $_GET['sf26%5b%5d'];
                            if(is_array($_GET['sf26%5b%5d'])){
                                $themes = implode(',', $_GET['sf26%5b%5d']);
                            }
                            ?>
                            <select multiple data-label='Темы/Проблемы' data-place='Выберите' name='sf26[]'>
                                <?= el_buildRegistryList('registryVote', $themes, false) ?>
                            </select>
                        </div>

                    </div>

                <h3>Местонахождение</h3>
                <div class="group">
                    <div class="item subject">
                        <select multiple data-label="Субъект" data-place="Выберите" name="sf8[]">
                            <?= el_buildRegistryList('subjects', $_GET['sf8'], false) ?>
                        </select>
                    </div>
                    <div class="item detail">
                        <select multiple data-label="Район / Округ" data-place="Выберите" name="sf9[]">
                        </select>
                    </div>
                    <div class="item detail">
                        <div class="el_data">
                            <label for="fcity">Населённый пункт</label>
                            <input class="el_input" value="<?= $_GET['sf10'] ?>" id="fcity" name="sf10" type="text">
                        </div>
                    </div>
                    <div class="item detail">
                        <div class="el_data">
                            <label for="fpost_index">Индекс</label>
                            <input class="el_input" id="fpost_index" name="sf11" type="text"
                                   value="<?= $_GET['sf11'] ?>">
                        </div>

                    </div>
                    <?/*div class="item detail">
                        <div class="el_data">
                            <label for="fstreet">Улица</label>
                            <input class="el_input" id="fstreet" name="sf12" type="text" value="<?= $_GET['sf12'] ?>">
                        </div>

                    </div>
                    <div class="item detail">
                        <div class="el_data">
                            <label for="fhouse">Номер дома</label>
                            <input class="el_input" id="fhouse" name="sf13" type="text" value="<?= $_GET['sf13'] ?>">
                        </div>

                    </div*/?>
                    <div class="item" style="display: none">
                        <div class="el_data">
                            <select multiple data-label="Группа в индексе" data-place="Выберите" name="sf16[]">
                            </select>
                        </div>
                    </div>
                </div>
                <?
                if ($_SESSION['user_level'] != 10 && isset($_SESSION['user_level'])) {
                    ?>
                    <h3>Уровень доступа</h3>
                    <div class="group">
                        <div class="item">
                            <select multiple data-label="Ранг" data-place="Выберите" name="sf6[]">
                                <?= el_buildRegistryList('userstatus', $_GET['sf6%5b%5d']) ?>
                            </select>
                        </div>
                    </div>
                    <?
                    if($_SESSION['user_level'] == 11 || $_SESSION['user_level'] == 4){
                    ?>
                    <h3>Дополнительно</h3>
                    <div class="group">
                        <div class="item">
                            <select multiple data-label="Куратор пользователя" data-place="Выберите" name="sf24">
                                <option value="%27%27">Нет куратора</option>
                                <?= el_buildRegistryList('users', $_GET['sf24'], false, [], ['id', 'user_id'], '', ' AND (field6 < 10 OR field6 = 11)') ?>
                            </select>
                        </div>

                    </div>
                    <?
                    }
                }
                ?>
                <div class="group">
                    <div class="item">
                        <button class="button icon text"><span class="material-icons">search</span>Найти</button>
                    </div>
                    <div class="item">
                        <button class="button icon text" id="reset_filter"><span class="material-icons">restart_alt</span>Сбросить</button>
                    </div>
                </div>
            </form>
        </section>

    </div>
    <script>
        users.popupNewInit();
    </script>
    <?php
}
?>