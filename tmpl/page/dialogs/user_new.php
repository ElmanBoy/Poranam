<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';
if (el_checkAjax()) {
if (isset($_POST['params'])) {
    $user = '';
    if (substr_count($_POST['params'], '-') > 0) {
        $user = el_dbselect("SELECT * FROM catalog_users_data WHERE user_id='" . addslashes($_POST['params']) . "'",
            0, $user, 'row', true
        );
    } else {
        $user = el_dbselect("SELECT * FROM catalog_users_data WHERE id=" . intval($_POST['params']),
            0, $user, 'row', true
        );
    }
    $formId = 'edit_user';
    $idField = '<input type="hidden" name="user_id" value="' . intval($_POST['params']) . '">';
    $fio = explode(" ", $user['field1']);
    $title = 'Пользователь ID ' . $user['user_id'];
} else {
    $user = array();
    $formId = 'add_user';
    $idField = '';
    $title = 'Новый пользователь';
}

$readonly = $_SESSION['user_level'] != 11 ? ' disabled="disabled" ' : '';
//print_r($_POST['params']);
?>
<div class="pop_up">
    <div class="title">
        <h2><?= $title ?></h2>
        <div class="close" onclick="pop_up_profile_add_close(); return false"><span
                    class="material-icons">highlight_off</span></div>
    </div>
    <section>
        <form class="ajaxFrm" id="<?= $formId ?>">
            <?
            if (isset($_POST['params'])) {
                ?>
                <h3>ID <?= $user['user_id'] ?></h3>
                <?
            }
            ?>
            <h3>Статус</h3>
            <div class="group">
                <div class="item w_100">
                    <select data-label="Статус" name="active">
                        <option value="Активный"<?= (intval($user['active']) == 1 || intval($_POST['params']) == 0) ? ' selected' : '' ?>>
                            Активный
                        </option>
                        <option value="Заблокирован"<?= ($user['active'] == 0 && isset($_POST['params'])) ? ' selected' : '' ?>>
                            Заблокирован
                        </option>
                    </select>
                </div>
            </div>
            <h3>Личные данные</h3>
            <div class="group">
                <div class="item">
                    <div class="el_data">
                        <label for="metka_1">Фамилия</label>
                        <input class="el_input" id="metka_1" value="<?= $fio[0] ?>" type="text" name="second_name"<?=$readonly?>>
                    </div>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="metka_2">Имя</label>
                        <input class="el_input" id="metka_2" value="<?= $fio[1] ?>" type="text" name="first_name"<?=$readonly?>>
                    </div>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="metka_3">Отчество</label>
                        <input class="el_input" id="metka_3" value="<?= $fio[2] ?>" type="text" name="third_name"<?=$readonly?>>
                    </div>
                </div>
                <div class="item">
                    <select data-label="Профессия" data-place="Выберите" name="profession"<?=$readonly?>>
                        <?= el_buildRegistryList('proffesions', $user['field7']) ?>
                    </select>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="metka_4">Телефон</label>
                        <input class="el_input" id="metka_4" value="<?= $user['field5'] ?>" type="tel" name="phones"<?=$readonly?>>
                    </div>
                </div>
                <div class="item">
                    <div class="el_data required">
                        <label for="metka_5">Почта</label>
                        <input required class="el_input" id="metka_5" value="<?= $user['field2'] ?>" type="email"
                               name="email"<?=$readonly?>>
                    </div>
                </div>
                <div class='item'>
                    <?
                    $themes = $user['field26'];
                    if(is_array($user['field26'])){
                        $themes = implode(',', $user['field26']);
                    }
                    ?>
                    <select multiple data-label='Темы/Проблемы' data-place='Выберите' name='theme[]'<?=$readonly?>>
                        <?= el_buildRegistryList('registryVote', $themes, false) ?>
                    </select>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="metka_6">Пароль</label>
                        <input class="el_input" id="metka_6" value="<?= $user['password'] ?>" type="password"
                               name="password"<?=$readonly?>>
                    </div>
                    <button class="button text" id="gen_pass"<?=$readonly?>>Сгенерировать</button>
                </div>
            </div>
            <h3>Местонахождение</h3>
            <div class="group">
                <div class="item">
                    <select multiple data-label="Субъект" data-place="Выберите" name="region"
                            data-multibarshow="false" data-values="[<?= $user['field9'] ?>]"<?=$readonly?>>
                        <?= el_buildRegistryList('subjects', $user['field8'], false) ?>
                    </select>
                </div>
                <div class="item">
                    <select multiple data-label="Район / Округ" data-place="Выберите" name="district"<?=$readonly?>>

                    </select>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="fcity">Город</label>
                        <input class="el_input" value="<?= $user['field10'] ?>" id="fcity" name="city" type="text"<?=$readonly?>>
                    </div>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="upost_index">Индекс</label>
                        <input class="el_input" value="<?= $user['field11'] ?>" id="upost_index" name="post_index"
                               data-values="[<?= $user['field16'] ?>]" type="text"<?=$readonly?>>
                    </div>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="ustreet">Улица</label>
                        <input class="el_input" value="<?= $user['field12'] ?>" id="ustreet" name="street" type="text"<?=$readonly?>>
                    </div>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="ubuild">Номер дома</label>
                        <input class="el_input" value="<?= $user['field13'] ?>" id="ubuild" name="build_number"
                               type="text"<?=$readonly?>>
                    </div>
                </div>
                <div class='item' style='display: none'>
                    <div class='el_data'>
                        <select multiple data-label='Группа в индексе' data-place='Выберите' name='group'<?=$readonly?>>
                        </select>
                    </div>
                </div>
            </div>
            <h3>Уровень доступа</h3>
            <div class="group">
                <div class="item">
                    <select data-label="Ранг" data-place="Выберите" name="status"<?=$readonly?>>
                        <?= el_buildRegistryList('userstatus', $user['field6']) ?>
                    </select>
                </div>
                <?/*div class="item">
                    <select data-label="Полномочия" data-place="Выберите">
                        <option value="Ст.: Субъекта">Ст.: Субъект</option>
                        <option value="Ст.: Район / округ">Ст.: Район / округ</option>
                        <option selected value="Ст.: Населённый пункт">Ст.: Населённый пункт</option>
                        <option value="Ст.: Индекс">Ст.: Индекс</option>
                        <option value="Ст.: Профессия">Ст.: Профессия</option>
                        <option value="Нет">Нет</option>
                    </select>
                </div>
            </div>
            <h3>Дополнительно</h3>
            <div class="group">
                <div class="item">
                    <select data-label="Куратор пользователя" data-place="Выберите">
                        <option value="значение 1ц">значение 1ц</option>
                        <option value="значение 2ц">значение 2ц</option>
                        <option value="значение 3ц">значение 3ц</option>
                    </select>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="metka_1">Ссылка приглашения</label>
                        <input disabled class="el_input" id="metka_1" value="http://www.sitename.ru/?invite_link_113783581" type="text">
                    </div>
                    <button class="button icon text"><span class="material-icons">file_copy</span>Копировать</button>
                </div>
            </div*/ ?>
                <?
                if ($_SESSION['user_level'] == 11) {
                    ?>
                    <div class="group">
                        <div class="item">
                            <button class="button text icon"><span class="material-icons">save</span>Сохранить</button>
                        </div>
                    </div>
                    <?
                }
                }
                echo $idField;
                ?>
        </form>
    </section>
</div>
<script>
    users.popupNewInit();
</script>
