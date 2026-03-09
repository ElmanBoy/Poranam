<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';
if (el_checkAjax()) {

$_GET = $_POST['params'];
?>
<div class="pop_up">
    <div class="title">
        <h2>Фильтр по пользователям</h2>
        <div class="close" onclick="pop_up_filter_users_close(); return false"><span class="material-icons">highlight_off</span></div>
    </div>
    <section>

        <form method="get">
            <h3>Основные</h3>
            <div class="group">
                <div class="item">
                    <div class="el_data">
                        <label for="uid">ID</label>
                        <input class="el_input" id="uid" name="uid" type="text">
                    </div>
                </div>
                <div class="item w_100">
                    <select data-label="Статус" id="ustatus" name="status">
                        <option selected value="1"<?=$_GET['status'] == '1' ? ' selected' : '' ?>>Активный</option>
                        <option value="0"<?=$_GET['status'] == '0' ? ' selected' : '' ?>>Заблокирован</option>
                    </select>

                </div>
            </div>
            <h3>Личные данные</h3>
            <div class="group">
                <div class="item">
                    <div class="el_data">
                        <label for="sname">Фамилия</label>
                        <input class="el_input" id="sname" name="sname" value="<?=$_GET['sname']?>" type="text">
                    </div>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="name">Имя</label>
                        <input class="el_input" id="name" name="name" value="<?=$_GET['name']?>" type="text">
                    </div>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="tname">Отчество</label>
                        <input class="el_input" id="tname" name="tname" value="<?=$_GET['tname']?>" type="text">
                    </div>
                </div>
                <div class="item">
                    <select multiple data-label="Профессия" data-place="Выберите" name="sf7">
                        <?= el_buildRegistryList('proffesions', $_GET['sf7'], false) ?>
                    </select>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="uphone">Телефон</label>
                        <input class="el_input" id="uphone" name="sf5" value="<?=$_GET['sf5']?>" type="tel">
                    </div>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="umail">Почта</label>
                        <input class="el_input" id="umail" name="sf2" value="<?=$_GET['sf2']?>" type="text">
                    </div>
                </div>

            </div>
            <h3>Местонахождение</h3>
            <div class="group">
                <div class="item">
                    <select multiple data-label="Субъект" data-place="Выберите" name="sf8"
                            data-multibarshow="false">
                        <?= el_buildRegistryList('subjects', $_GET['sf8'], false) ?>
                    </select>
                </div>
                <div class="item">
                    <select multiple data-label="Район / Округ" data-place="Выберите" name="sf9">
                    </select>
                </div>
                <div class="item">
                    <input class="el_input" value="<?= $_GET['sf10'] ?>" id="fcity" name="sf10" type="text">
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="upost_index">Индекс</label>
                        <input class="el_input" value="<?=$_GET['sf11']?>" id="upost_index" name="sf11" type="text">
                    </div>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="ustreet">Улица</label>
                        <input class="el_input" value="<?=$_GET['sf12']?>" id="ustreet" name="sf12" type="text">
                    </div>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="ubuild">Номер дома</label>
                        <input class="el_input" value="<?=$_GET['sf13']?>" id="ubuild" name="sf13" type="text">
                    </div>
                </div>
            </div>
            <h3>Уровень доступа</h3>
            <div class="group">
                <div class="item">
                    <select multiple data-label="Ранг" data-place="Выберите" name="sf6">
                        <?= el_buildRegistryList('userstatus', $_GET['sf6'], false) ?>
                    </select>
                </div>
            </div>
            <h3>Дополнительно</h3>
            <div class="group">
                <div class="item">
                    <select multiple data-label="Куратор пользователя" data-place="Выберите" name="sf24">
                        <option value="значение 1ц">значение 1ц</option>
                        <option value="значение 2ц">значение 2ц</option>
                        <option value="значение 3ц">значение 3ц</option>
                    </select>
                </div>

            </div>
            <div class="group">
                <div class="item">
                    <button class="button icon text"><span class="material-icons">search</span>Найти</button>
                </div>
                <div class="item">
                    <button class="button icon text"><span class="material-icons">restart_alt</span>Сбросить</button>
                </div>
            </div>
        </form>
    </section>

</div>
    <?php
}
?>