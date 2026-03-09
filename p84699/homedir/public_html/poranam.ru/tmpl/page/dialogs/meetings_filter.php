<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';
if (el_checkAjax()) {

    $_GET = $_POST['params'];
?>
<div class="pop_up">
    <div class="title">
        <h2>Фильтр по мероприятиям</h2>
        <div class="close" onclick="pop_up_filter_init_close(); return false"><span class="material-icons">highlight_off</span></div>
    </div>
    <section>
        <form method="get">
            <h3>Автор мероприятия</h3>
            <div class="group">
                <div class="item">
                    <div class="el_data">
                        <label for="fid">ID</label>
                        <input class="el_input" id="fid" name="sf4" type="text">
                    </div>
                </div>
                <div class="item">
                    <select multiple data-label="Ранг" data-place="Выберите" name="sf13">
                        <?= el_buildRegistryList('userstatus', $_GET['sf13'], false) ?>
                    </select>
                </div>
            </div>
            <h3>Участники</h3>
            <div class="group">

                <div class="item">
                    <select multiple data-label="Субъект" data-place="Выберите" name="region"
                            data-multibarshow="false">
                        <?= el_buildRegistryList('subjects', $_GET['region'], false) ?>
                    </select>
                </div>
                <div class="item">
                    <select multiple data-label="Район / Округ" data-place="Выберите" name="district" id="district">
                    </select>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="fcity">Населённый пункт</label>
                        <input class="el_input" value="<?= $_GET['sf8'] ?>" id="fcity" name="sf8" type="text">
                    </div>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="fpost_index">Индекс</label>
                        <input class="el_input" id="fpost_index" name="sf9" type="text" value="<?= $_GET['sf9'] ?>">
                    </div>

                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="fstreet">Улица</label>
                        <input class="el_input" id="fstreet" name="sf10" type="text" value="<?= $_GET['sf10'] ?>">
                    </div>

                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="fhouse">Номер дома</label>
                        <input class="el_input" id="fhouse" name="sf11" type="text" value="<?= $_GET['sf11'] ?>">
                    </div>

                </div>
            </div>
            <div class="group">

                <div class="item">
                    <select multiple data-label="Профессия" data-place="Выберите" name="sf7">
                        <?= el_buildRegistryList('proffesions', $_GET['sf7'], false) ?>
                    </select>
                </div>
            </div>
            <h3>Параметры</h3>
            <div class="group">
                <div class="item">
                    <select multiple data-label="Тема" data-place="Выберите" name="sf12">
                        <?= el_buildRegistryList('registryVote', $_GET['sf12'], false) ?>
                    </select>
                </div>
                <div class="item">
                    <select data-label="Статус мероприятия" data-place="Выберите" name="sf14">
                        <option value=""<?=(intval($_GET['sf14']) == 0) ? ' selected' : ''?>>Все</option>
                        <option value="3"<?=(intval($_GET['sf14']) == 3) ? ' selected' : ''?>>Мероприятие завершено</option>
                        <option value="2"<?=(intval($_GET['sf14']) == 2) ? ' selected' : ''?>>Мероприятие идёт</option>
                        <?/*option value="4">Переведена в "Голосования"</option*/?>
                    </select>
                </div>
            </div>
            <h3>Время проведения</h3>
            <div class="group">
                <div class="item">
                    <div class="el_data">
                        <label for="fstart">Начало</label>
                        <input class="el_input" id="start" type="date" value="<?= $_GET['sf2'] ?>" name="sf2">
                    </div>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="fend">Окончание</label>
                        <input class="el_input" id="end" type="date" name="sf3" value="<?= $_GET['sf3'] ?>">
                    </div>
                </div>
            </div>
            <div class="group">
                <div class="item">
                    <button type="submit" class="button icon text"><span class="material-icons">search</span>Найти</button>
                </div>
                <div class="item">
                    <button type="reset" class="button icon text"><span class="material-icons">
                                    restart_alt
                                </span>Сбросить</button>
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