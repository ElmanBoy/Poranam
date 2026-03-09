<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';
if (el_checkAjax()) {

    function buildArrayField($params, $fieldNum){
        if(isset($params['sf'.$fieldNum])){
            foreach($params['sf'.$fieldNum] as $val){
                echo '<input type="hidden" name="sf'.$fieldNum.'[]" value="'.$val.'">';
            }
        }
    }

    $usersCount = $_SESSION['user_checked'] == [0] || !isset($_SESSION['user_checked']) ? 'все'
        : count($_SESSION['user_checked']);
    ?>

    <div class="pop_up">
        <div class="title">
            <h2>Отправить сообщение</h2>
            <div class="close" onclick="pop_up_sender_close(); return false"><span
                        class="material-icons">highlight_off</span></div>
        </div>
        <section>
            <form class="ajaxFrm" id="user_mailing">
                <h3>Сообщение</h3>
                <div class="group">
                    <div class="item w_100">
                        <div class="el_data w_100">
                            <input class="el_input" name="title" type="text" placeholder="Тема сообщения">
                        </div>
                    </div>
                    <div class="item w_100">
                        <div class="el_data w_100">
                            <label>Текст сообщения</label>
                            <textarea class="el_textarea" name="text"></textarea>
                        </div>

                    </div>
                    <?php parse_str($_POST['params'], $params);?>
                    <?php buildArrayField($params, 6);?>
                    <input type="hidden" name="sf7" value="<?=$params['sf7']?>">
                    <?php buildArrayField($params, 8);?>
                    <?php buildArrayField($params, 9);?>
                    <input type="hidden" name="sf10" value="<?=$params['sf10']?>">
                    <input type="hidden" name="sf11" value="<?=$params['sf11']?>">
                    <input type="hidden" name="sf12" value="<?=$params['sf12']?>">
                    <input type="hidden" name="sf13" value="<?=$params['sf13']?>">
                    <?php buildArrayField($params, 16);?>
                    <!--<h3>Получатели</h3>

                    <div class="group">
                        <div class="item w_100">
                            <div class="custom_checkbox">
                                <label class="container">Отправить всем
                                    <input type="checkbox" id="init_select_all" name="init_select_all" checked
                                           value="1">
                                    <span class="checkmark"></span></label>
                            </div>
                        </div>

                    </div>
                    <div class="item subject" style="display: none">
                        <select multiple data-label="Субъект" data-place="Выберите" name="sf8[]">
                            <?php/*= el_buildRegistryList('subjects', $_GET['region'], false) */?>
                        </select>
                    </div>
                    <div class="item detail" style="display: none">
                        <select multiple data-label="Район / Округ" data-place="Выберите" name="sf9[]">
                        </select>
                    </div>
                    <div class="item detail" style="display: none">
                        <div class="el_data">
                            <label for="fcity">Населённый пункт</label>
                            <input class="el_input" value="<?php/*= $_GET['sf8'] */?>" id="fcity" name="sf10" type="text">
                        </div>
                    </div>
                    <div class="item detail" style="display: none">
                        <div class="el_data">
                            <label for="fpost_index">Индекс</label>
                            <input class="el_input" id="fpost_index" name="sf11" type="text"
                                   value="<?php/*= $_GET['sf9'] */?>">
                        </div>

                    </div>
                    <div class="item detail" style="display: none">
                        <div class="el_data">
                            <label for="fstreet">Улица</label>
                            <input class="el_input" id="fstreet" name="sf12" type="text" value="<?php/*= $_GET['sf10'] */?>">
                        </div>

                    </div>
                    <div class="item detail" style="display: none">
                        <div class="el_data">
                            <label for="fhouse">Номер дома</label>
                            <input class="el_input" id="fhouse" name="sf13" type="text" value="<?php/*= $_GET['sf11'] */?>">
                        </div>

                    </div>
                    <div class="item" style="display: none">
                        <div class="el_data">
                            <select multiple data-label="Группа в индексе" data-place="Выберите" name="sf16[]">
                            </select>
                        </div>
                    </div>
                </div>
                    <div class="group prof" style="display: none">
                        <div class="item">
                            <select multiple data-label="Профессия" data-place="Выберите" name="sf7">
                                <?php/*= el_buildRegistryList('proffesions', $_GET['sf7'], false) */?>
                            </select>
                        </div>
                    </div>


                    <div class="group prof" style="display: none">
                        <div class="item">
                            <select multiple data-label="Ранг" data-place="Выберите" name="sf6">
                                <?php/*= el_buildRegistryList('userstatus', $init['field13'], false, array(11)) */?>
                            </select>
                        </div>
                    </div>-->

                <div class="countUsers" id="found_users"><span>
                        Выбран<?=el_postfix($usersCount, '', 'ы', 'ы')?> <?=$usersCount?> получател<?=$usersCount == 'все' ? 'и' : el_postfix($usersCount, 'ь', 'я', 'ей')?></span></div>


                <div class="group">
                    <div class="item">
                        <button class="button text icon"><span class="material-icons">save</span>Отправить</button>
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