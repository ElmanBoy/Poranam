<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';
if (el_checkAjax()) {
    if (isset($_POST['params'])) {
        $init = '';
        $init = el_dbselect("SELECT * FROM catalog_init_data WHERE id=" . intval($_POST['params']),
            0, $init, 'row', true);
        $formId = 'edit_initiative';
        $idField = '<input type="hidden" name="init_id" value="'.intval($_POST['params']).'">';
    } else {
        $init = array();
        $formId = 'add_initiative';
        $idField = '';
    }
    ?>
    <div class="pop_up">
        <div class="title">
            <h2>Инициатива</h2>
            <div class="close"><span class="material-icons">highlight_off</span></div>
        </div>
        <section>
            <form class="ajaxFrm" id="<?=$formId?>">
                <h3>Тема</h3>
                <div class="group">
                    <div class="item">
                        <div class="el_data w_50">
                            <select name="theme" class="el_select" data-label="Тема голосования" data-place="Выберите">
                                <?= el_buildRegistryList('registryVote', $init['field12'], false) ?>
                            </select>
                        </div>
                    </div>
                </div>
                <h3>Вопрос</h3>
                <div class="group">

                    <div class="item w_100">
                        <div class="el_data w_100">
                            <label>Текст вопроса</label>
                            <textarea class="el_textarea" name="question" id="demo-01"><?= $init['field1'] ?></textarea>
                        </div>

                    </div>
                </div>


                <?/*h3>Варинаты ответов</h3>
            <div class="group">
                <div class="item w_100 red">
                    Инициатива может иметь только два варианта ответа: Да или Нет.
                </div>

            </div*/
                ?>
                <h3>Приложить файлы</h3>
                <div class="group">

                    <div class="item w_100">
                        <div class="el_data w_100">
                            <label>Выберите файл на своем компьютере</label>
                            <input type="file" class="el_input" name="file" id="file">
                        </div>
                    </div>
                    <div class="item w_100">
                        <div class="el_data w_100">
                            <label>Дайте название файлу</label>
                            <input type="text" class="el_input" name="file_name" id="file_name">
                        </div>

                    </div>
                </div>

                <h3>Участники</h3>
                <div class="group">

                    <div class="item w_100">
                        <div class="custom_checkbox">
                            <label class="container">Выбрать всех
                                <input type="checkbox" id="init_select_all" name="init_select_all" checked value="1">
                                <span class="checkmark"></span></label>
                        </div>
                    </div>
                    <div class="item subject" style="display: none">
                        <select multiple data-label="Субъект" data-place="Выберите" name="region"
                                data-multibarshow="false">
                            <?= el_buildRegistryList('subjects', $init['field5']) ?>
                        </select>
                    </div>
                    <div class="item detail" style="display: none">
                        <select multiple data-label="Район / Округ" data-place="Выберите" name="district" id="district">
                        </select>
                    </div>
                    <div class="item detail" style="display: none">
                        <div class="el_data">
                            <label for="city">Населённый пункт</label>
                            <input class="el_input" value="<?= $init['field8'] ?>" id="city" name="city" type="text">
                        </div>
                    </div>
                    <div class="item detail" style="display: none">
                        <div class="el_data">
                            <label for="post_index">Индекс</label>
                            <input class="el_input" id="post_index" name="post_index" type="text"
                                   value="<?= $init['field9'] ?>">
                        </div>
                    </div>
                    <div class="item detail" style="display: none">
                        <div class="el_data">
                            <label for="street">Улица</label>
                            <input class="el_input" value="<?= $init['field10'] ?>" id="street" name="street"
                                   type="text">
                        </div>
                    </div>
                    <div class="item detail" style="display: none">
                        <div class="el_data">
                            <label for="house">Номер дома</label>
                            <input class="el_input" value="<?= $init['field11'] ?>" id="house" name="house" type="text">
                        </div>
                    </div>
                    <div class="item" style="display: none">
                        <div class="el_data">
                            <select multiple data-label="Группа в индексе" data-place="Выберите" name="groups[]" id="groups">
                            </select>
                        </div>
                    </div>

                </div>
                <div class="group prof" style="display: none">
                    <div class="item w_100">

                        <select multiple data-label="Профессия" data-place="Выберите" name="professions">
                            <?= el_buildRegistryList('proffesions', $init['field7']) ?>
                        </select>

                    </div>
                </div>
                <?
                if ($_SESSION['user_level'] == 11) {
                    ?>
                    <h3>Для кого</h3>
                    <div class="group">
                        <div class="item">
                            <select multiple data-label="Ранг" data-place="Выберите" name="rank">
                                <?= el_buildRegistryList('userstatus', $init['field13']) ?>
                            </select>
                        </div>
                    </div>

                <h3>Период проведения</h3>
                <div class="group">
                    <div class="item">
                        <div class="el_data">
                            <label for="init_start">Начало</label>
                            <input class="el_input" id="init_start" type="date" name="init_start"
                                   value="<?= $init['field2'] ?>">
                        </div>
                    </div>
                    <div class="item">
                        <div class="el_data">
                            <label for="init_end">Окончание</label>
                            <input class="el_input" id="init_end" type="date" name="init_end"
                                   value="<?= $init['field3'] ?>">
                        </div>
                    </div>
                </div>
                    <?
                }
                echo $idField;
                ?>
                <div class="group">
                    <div class="item">
                        <button class="button text icon"><span class="material-icons">save</span>Сохранить</button>
                    </div>
                </div>
            </form>
        </section>

    </div>

    <script>
        initiatives.popupNewInit();
    </script>
    <?php
}
?>