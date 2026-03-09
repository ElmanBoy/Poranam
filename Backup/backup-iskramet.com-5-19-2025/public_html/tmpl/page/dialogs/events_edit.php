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

    $readonly = $init['field14'] == 15 ? ' disabled="disabled" ' : '';

    $startArr = explode(' ', $init['field2']);
    $startDate = $startArr[0];
    $startTime = $startArr[1];
    $endArr = explode(' ', $init['field3']);
    $endDate = $endArr[0];
    $endTime = $endArr[1];

    $statuses = getRegistry('initstatus');
    ?>



        <div class="pop_up">
            <div class="title">
                <h2>Редактирование мероприятия</h2>
                <div class="close"><span class="material-icons">highlight_off</span></div>
            </div>
            <section>
                <form class="ajaxFrm" id="<?= $formId ?>">
                    <h3>Параметры</h3>
                    <div class="group">
                        <div class="item w_100">
                            <div class="el_data w_100">
                                <label for="metka_2м">ID организатора</label>
                                <div disabled class="el_input" id="metka_2м" readonly><?= $init['field4'] ?></div>
                            </div>
                        </div>
                        <div class="item">
                            <div class="el_data">
                                <label for="init_start">Дата начала проведения</label>
                                <input class="el_input" id="init_start" name="init_start" value="<?= $startDate ?>" type="date">
                            </div>
                        </div>
                        <div class="item">
                            <div class="el_data">
                                <label for="init_start_time">Время</label>
                                <input class="el_input" id="init_start_time" name="init_start_time" value="<?= $startTime ?>" type="time">
                            </div>
                        </div>

                        <div class="item">
                            <div class="el_data">
                                <label for="init_start">Дата окончания проведения</label>
                                <input class="el_input" id="init_end" name="init_end" value="<?= $endDate ?>" type="date">
                            </div>
                        </div>
                        <div class="item">
                            <div class="el_data">
                                <label for="init_start_time">Время</label>
                                <input class="el_input" id="init_end_time" name="init_end_time" value="<?= $endTime ?>" type="time">
                            </div>
                        </div>
                        <div class="item">
                            Статус: <h3><?=$statuses[$init['field14']]?></h3>
                            </select>
                        </div>
                    </div>
                    <div class="group">
                        <div class="item w_100">
                            <div class="el_data w_100">
                                <label>Место проведения</label>
                                <textarea class="el_textarea"<?=$readonly?>
                                          id="address" name="address"><?= $init['field18'] ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="group">
                        <div class='item w_100'>
                            <?
                            $themes = $init['field12'];
                            if(is_array($init['field12'])){
                                $themes = implode(',', $init['field12']);
                            }
                            ?>
                            <select data-label='Темы/Проблемы' data-place='Выберите' name='theme[]'>
                                <option value='0'>Без темы</option>
                                <?= el_buildRegistryList('registryVote', $themes, false) ?>
                            </select>
                        </div>
                        <div class="item w_100">
                            <div class="el_data w_100">
                                <label for="name">Название</label>
                                <input class="el_input"<?=$readonly?> id="name" name="name" value="<?= $init['field1'] ?>" type="text">
                            </div>
                        </div>
                        <div class="item w_100">
                            <div class="el_data w_100">
                                <label>Аннотация</label>
                                <textarea class="el_textarea"<?=$readonly?>
                                          id="annotation" name="annotation"><?= $init['field21'] ?></textarea>
                            </div>
                        </div>
                        <?
                        if ($_SESSION['user_level'] == 11) {
                        echo '<div class="custom_checkbox">
                                <label class="container">
                                    <input type="checkbox" name="publish_announce" value="1"'.($init['field25'] == 1 ? ' checked' : '').'>
                                    <span class="checkmark"></span>
                                    Опубликовать на сайте
                                </label>
                            </div>';
                        }
                        ?>
                    </div>
                    <h3>Пригласить к участию</h3>
                    <div class="group">
                        <?php
                        $forAllChecked = intval($init['field5']) == 0 && intval($init['field6']) == 0 &&
                            intval($init['field7']) == 0 && strlen($init['field8']) == 0 &&
                            strlen($init['field9']) == 0 && strlen($init['field10']) == 0 &&
                            strlen($init['field11']) == 0 ? 'checked' : '';
                        ?>
                        <div class="item w_100">
                            <div class="custom_checkbox">
                                <label class="container">Выбрать всех
                                    <input type="checkbox" id="init_select_all" name="init_select_all" <?=$forAllChecked?>
                                           value="1">
                                    <span class="checkmark"></span></label>
                            </div>
                        </div>
                        <div class="item subject detail" style="display: none">
                            <select data-label="Субъект" data-place="Выберите" name="region"
                                    data-multibarshow="false" data-values="[<?=$init['field6']?>]">
                                <?= el_buildRegistryList('subjects', $init['field5']) ?>
                            </select>
                        </div>
                        <div class="item detail" style="display: none">
                            <select data-label="Район / Округ" data-place="Выберите" name="district"
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
                                       data-values="[<?= $init['field17'] ?>]" value="<?= $init['field9'] ?>">
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
                                <select data-label="Группа в индексе" data-place="Выберите" name="groups"
                                        id="groups">
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="group prof" style="display: none">
                        <div class="item w_100">

                            <select multiple data-label="Профессия" data-place="Выберите" name="professions[]">
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
                    if($init['field14'] == 15){
                    ?>
                    <h3>Контент</h3>
                    <div class="group">
                        <div class="item w_100">
                            <div class="el_data w_100">
                                <label>Текст для сайта</label>
                                <textarea class="el_textarea" name="report" id="report" rows="10"><?= $init['field23'] ?></textarea>
                            </div>
                        </div>

                        <?/*div class="item">
                            <div class="el_data file">
                                <label for="metka_2f">Фотография</label>
                                <input class="el_input" id="photo" name="photo" type="file" multiple accept="image/*">

                            </div>
                        </div*/
                        if ($_SESSION['user_level'] == 11) {
                            echo '<div class="custom_checkbox">
                                <label class="container">
                                    <input type="checkbox" name="publish_report" value="1"'.($init['field26'] == 1 ? ' checked' : '').'>
                                    <span class="checkmark"></span>
                                    Опубликовать на сайте
                                </label>
                            </div>';
                        }
                        ?>
                    </div>
                    <script type="text/javascript" src="/editor/e_modules/ckeditor2/ckeditor.js"></script>
                    <script type="text/javascript" src="/editor/e_modules/ckeditor2/adapters/jquery.js"></script>
                    <script>
                        var CKEDITOR_BASEPATH = '/editor/e_modules/ckeditor2/';
                        if (CKEDITOR.env.ie && CKEDITOR.env.version < 9)
                            CKEDITOR.tools.enableHtml5Elements(document);
                        CKEDITOR.config.height = '300';
                        CKEDITOR.config.width = '100%';


                        var initEditor = (function () {
                            var wysiwygareaAvailable = isWysiwygareaAvailable()/*,
                                isBBCodeBuiltIn = !!CKEDITOR.plugins.get( 'bbcode' )*/;

                            return function () {
                                var editorElement = CKEDITOR.document.getById('report');

                                // Depending on the wysiwygare plugin availability initialize classic or inline editor.
                                if (wysiwygareaAvailable) {
                                    CKEDITOR.replace('report', {
                                        extraPlugins: 'imageuploader,youtube,html5video,widget,widgetselection,clipboard,lineutils,video,html5video',
                                        toolbar :
                                            [
                                                ['Cut','Copy','PasteText','-'],
                                                ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat','-'],
                                                ['Format','-','Bold','Subscript','Superscript'],
                                                ['NumberedList','BulletedList','Outdent','Indent','Blockquote','-'],
                                                ['JustifyLeft','JustifyCenter','JustifyRight','-'],
                                                ['Youtube', 'Table','InsertPre','-'],
                                                ['Link','Unlink','HorizontalRule','ShowBlocks','Maximize'],

                                            ],
                                        //contentsCss: '/css/style00.css'
                                    });
                                } else {
                                    editorElement.setAttribute('contenteditable', 'true');
                                    CKEDITOR.inline('report');

                                    // TODO we can consider displaying some info box that
                                    // without wysiwygarea the classic editor may not work.
                                }
                            };

                            function isWysiwygareaAvailable() {
                                // If in development mode, then the wysiwygarea must be available.
                                // Split REV into two strings so builder does not replace it :D.
                                if (CKEDITOR.revision == ('%RE' + 'V%')) {
                                    return true;
                                }

                                return !!CKEDITOR.plugins.get('wysiwygarea');
                            }
                        })();
                        initEditor();
                    </script>
                    <?
                    }
                    ?>
                    <h3>Переписка</h3>
                    <div class="group">
                        <div class="item w_100">
                            <div class="correspondence"><?=$init['field24']?></div>
                            <div class="el_data w_100">
                                <label>Ваше сообщение</label>
                                <textarea class="el_textarea"
                                              id="message" name="message"></textarea>
                            </div>
                        </div>
                    </div>
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
        setTimeout(function(){
            $('#init_select_all').trigger('change');
            $('[name=post_index]').trigger('input');
            if(!$('#init_select_all').prop("checked")){
                $('[name=region]').trigger('change');
                $('.detail').show();
            }
        }, 500);


    </script>
    <?php
}
?>