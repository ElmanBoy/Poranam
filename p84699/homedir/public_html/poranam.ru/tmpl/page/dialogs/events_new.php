<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';
if (el_checkAjax()) {
    if (isset($_POST['params'])) {
        $init = '';
        $init = el_dbselect("SELECT * FROM catalog_init_data WHERE id=" . intval($_POST['params']),
            0, $init, 'row', true);
        $formId = 'edit_meeting';
        $idField = '<input type="hidden" name="init_id" value="' . intval($_POST['params']) . '">';
    } else {
        $init = array();
        $formId = 'add_meeting';
        $idField = '';
    }
    ?>



        <div class="pop_up">
            <div class="title">
                <h2>Мероприятие</h2>
                <div class="close"><span class="material-icons">highlight_off</span></div>
            </div>
            <section>
                <form class="ajaxFrm" id="<?= $formId ?>">
                    <h3>Параметры</h3>
                    <div class="group">
                        <div class="item w_100">
                            <div class="el_data w_100">
                                <label for="metka_2м">ID организатора</label>
                                <div disabled class="el_input" id="metka_2м" ><?=$_SESSION['user_index'].'_'.$_SESSION['user_id']?></div>
                            </div>
                        </div>
                        <div class="item">
                            <div class="el_data">
                                <label for="init_start">Дата проведения</label>
                                <input class="el_input" id="init_start" name="init_start" value="<?= $init['field2'] ?>" type="date">
                            </div>
                        </div>
                        <div class="item">
                            <div class="el_data">
                                <label for="init_start_time">Время</label>
                                <input class="el_input" id="init_start_time" name="init_start_time" value="<?= $init['field22'] ?>" type="time">
                            </div>
                        </div>
                        <?/*div class="item">
                            <select data-label="Город проведения" data-place="Выберите">
                                <option value="Майкоп">Майкоп</option>
                                <option value="Адыгейск">Адыгейск</option>
                                <option selected value="Горно-Алтайск">Горно-Алтайск</option>
                                <option value="Уфа">Уфа</option>
                                <option value="Стерлитамак">Стерлитамак</option>
                                <option value="Салават">Салават</option>
                            </select>
                        </div*/?>
                    </div>
                    <div class="group">
                        <div class="item w_100">
                            <div class="el_data w_100">
                                <label>Место проведения</label>
                                <textarea class="el_textarea"
                                          id="address" name="address"><?= $init['field18'] ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="group">
                        <div class="item w_100">
                            <div class="el_data w_100">
                                <label for="name">Название</label>
                                <input class="el_input" id="name" name="name" value="<?= $init['field1'] ?>" type="text">
                            </div>
                        </div>
                        <div class="item w_100">
                            <div class="el_data w_100">
                                <label>Аннотация</label>
                                <textarea class="el_textarea"
                                          id="annotation" name="annotation"><?= $init['field21'] ?></textarea>
                            </div>
                        </div>
                    </div>
                    <h3>Пригласить к участию</h3>
                    <div class="group">

                        <div class="item w_100">
                            <div class="custom_checkbox">
                                <label class="container">Выбрать всех
                                    <input type="checkbox" id="init_select_all" name="init_select_all" checked
                                           value="1">
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
                            <select multiple data-label="Район / Округ" data-place="Выберите" name="district"
                                    id="district">
                            </select>
                        </div>
                        <div class="item detail" style="display: none">
                            <div class="el_data">
                                <label for="city">Населённый пункт</label>
                                <input class="el_input" value="<?= $init['field8'] ?>" id="city" name="city"
                                       type="text">
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
                                <input class="el_input" value="<?= $init['field11'] ?>" id="house" name="house"
                                       type="text">
                            </div>
                        </div>
                        <div class="item" style="display: none">
                            <div class="el_data">
                                <select multiple data-label="Группа в индексе" data-place="Выберите" name="groups[]"
                                        id="groups">
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
                        <?
                    }
                    echo $idField;
                    ?>
                    <?/*div class="group">
                        <div class="item">
                            <select multiple data-label="Ранг" data-place="Выберите">
                                <option value="Администратор">Администратор</option>
                                <option selected value="Куратор">Куратор</option>
                                <option value="Пользователь">Пользователь</option>
                            </select>
                        </div>
                        <div class="item">
                            <select multiple data-label="Полномочия" data-place="Выберите">
                                <option value="Ст.: Субъекта">Ст.: Субъект</option>
                                <option value="Ст.: Район / округ">Ст.: Район / округ</option>
                                <option selected value="Ст.: Населённый пункт">Ст.: Населённый пункт</option>
                                <option value="Ст.: Индекс">Ст.: Индекс</option>
                                <option value="Ст.: Профессия">Ст.: Профессия</option>
                                <option value="Нет">Нет</option>
                            </select>
                        </div>
                        <div class="item">
                            <select data-label="Профессия" data-place="Выберите">
                                <option value="значение 11">значение 11</option>
                                <option selected value="значение 21">значение 21</option>
                                <option value="значение 31">значение 31</option>
                            </select>
                        </div>
                    </div*/ ?>
                    <?
                    if($formId == 'edit_meeting' && $init['field14'] == 15){
                    ?>
                    <h3>Контент</h3>
                    <div class="group">
                        <div class="item w_100">
                            <div class="el_data w_100">
                                <label>Текст для сайта</label>
                                <textarea class="el_textarea" name="report" id="report"><?= $init['field23'] ?></textarea>
                            </div>
                        </div>

                        <div class="item">
                            <div class="el_data file">
                                <label for="metka_2f">Фотография</label>
                                <input required class="el_input" id="photo" name="photo" type="file">

                            </div>
                        </div>
                    </div>
                    <?
                    }
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
        meetings.popupNewInit();
    </script>
    <?php
}
?>