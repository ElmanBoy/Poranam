<?php
session_start();
$themes = getRegistry('registryVote');
$subjects = getRegistry('subjects');
$regions = getRegistry('regions');
$professions = getRegistry('proffesions');
$groups = getRegistry('groups', ['id', 'field1', 'field2']);
$is_vote = ($row_dbcontent['cat'] == 398);

if(el_dbnumrows($catalog) > 0){
?>
<table class="table_data">
    <thead>
    <tr>
        <?
        if ($_SESSION['user_level'] == 11 || $_SESSION['user_level'] == 4) {
            ?>
            <th>
                <div class="custom_checkbox">
                    <label class="container"><input type="checkbox" id="check_all"><span
                                class="checkmark"></span></label>
                </div>
            </th>
            <?
        }
        ?>
        <th>Организатор</th>
        <th>
            <div class="button icon sort"><span class="material-icons">filter_list</span></div>Дата
        </th>
        <th>
            <div class="button icon sort"><span class="material-icons">filter_list</span></div>Место
        </th>
        <th>
            Название
        </th>
        <th>
            <div class="button icon sort"><span class="material-icons">filter_list</span></div>Статус
        </th>

        <th>
            Участники
        </th>
    </tr>
    </thead>

    <tbody>
<?php
do {
    ?>
    <tr id="tr<?= $row_catalog['id'] ?>">
        <?
        if ($_SESSION['user_level'] == 11 || $_SESSION['user_level'] == 4) {
            ?>
            <td>
                <div class="custom_checkbox">
                    <label class="container">
                        <input type="checkbox" class="group_check" value="<?= $row_catalog['id'] ?>"><span class="checkmark"></span>
                    </label>
                </div>
            </td>
            <?
        }
        ?>
        <td>
            <?
            if (intval($_SESSION['user_level']) > 0 && intval($_SESSION['user_level']) != 10) {
                ?>
                <a href="" class="user_profile_link" data-user_id="<?= $row_catalog['field4'] ?>"><?= $row_catalog['field4'] ?></a>
                <?
            } else {
                echo $row_catalog['field4'];
            }
            ?>
            <button class="button text icon more"><span class="material-icons">unfold_more</span>Участники</button>
        </td>

        <td><?= el_date1($row_catalog['field2']).(!empty($row_catalog['field3']) &&
            $row_catalog['field3'] != '0000-00-00 00:00:00' ? ' - '.el_date1($row_catalog['field3']) : '') ?></td>
        <td><?= $row_catalog['field8'] ?></td>
        <td><strong><?= $row_catalog['field1'] ?></strong><br><?= $row_catalog['field21'] ?></td>
        <td class="col_actions">
            <? //echo $row_catalog['field14'];
            switch (intval($row_catalog['field14'])) {
                case 10: //Мероприятие создано
                case 11:
                    //Если автор мероприятия или КЦ
                    if($_SESSION['visual_user_id'] == $row_catalog['field4'] || $_SESSION['user_level'] == 4){
                        ?>
                        <button class='button icon edit_init' data-id="<?= $row_catalog['id'] ?>"
                                title='редактировать'>
                            <span class='material-icons'>edit</span></button>
                    <?
                    }
                    if($_SESSION['user_level'] == 4){
                    //Куратор центра
                        ?>
                        <button data-id="<?= $row_catalog['id'] ?>" class="button icon votes_approve" title="На утверждение">
                            <span class="material-icons">play_arrow</span></button>
                        <button class="button icon red init_remove" data-id="<?= $row_catalog['id'] ?>" title="Удалить">
                            <span class="material-icons">delete_forever</span></button>
                        <?

                    }else{
                        echo 'Черновик';
                    }
                    break;

                case 13: //На утверждении у админа

                   //Показывать автору или админу
                    if ($_SESSION['user_level'] == 11){
                    ?>
                    <button class="button icon edit_init" data-id="<?= $row_catalog['id'] ?>" title="Редактировать">
                        <span class="material-icons">edit</span></button>
                    <button data-id="<?= $row_catalog['id'] ?>" class="button icon init_run" title="Запустить">
                        <span class="material-icons">play_arrow</span></button>
                    <button class="button icon red init_remove" data-id="<?= $row_catalog['id'] ?>" title="Удалить">
                        <span class="material-icons">delete_forever</span></button>
                        <?
                        }elseif(($_SESSION['user_level'] != 4 || $_SESSION['user_level'] != 11)
                    && $_SESSION['visual_user_id'] == $row_catalog['field4']){
                        ?>
                        <button class='button icon edit_init' data-id="<?= $row_catalog['id'] ?>"
                                title='редактировать'>
                            <span class='material-icons'>edit</span></button>
                        <?
                    }else{
                        echo 'На рассмотрении';
                    }

                    break;
                case 14: //Остановить может Администратор или КЦ
                    if ($_SESSION['user_level'] == 11/* || $_SESSION['user_level'] == 4*/) {
                        ?>
                        <button class="button icon init_stop" data-id="<?= $row_catalog['id'] ?>" title="Завершить">
                            <span class="material-icons">stop</span></button>
                            <button class="button icon edit_init" data-id="<?= $row_catalog['id'] ?>"
                                    title="редактировать">
                                <span class="material-icons">edit</span></button>
                        <?
                    } else {
                        echo 'Идёт';
                    }
                    break;
                case 15: //Завершенная инициатива
                    if ($_SESSION['user_level'] == 11) {
                        ?>
                        <button class="button icon edit_init" data-id="<?= $row_catalog['id'] ?>" title="Просмотр">
                            <span class="material-icons">remove_red_eye</span>
                        </button>
                        <?
                    } else {
                        echo 'Завершено';
                    }
                    break;

            }
            ?>
        </td>
        <td>
            <button class="button icon text meeting_list" data-id="<?= $row_catalog['id'] ?>">
                <span class="material-icons">list</span>Список</button>
        </td>
    </tr>
    <tr class="hidden">
        <?
        $subjectString = getStringFromId($subjects, $row_catalog['field5']);
        $subjectString = ($subjectString == '') ? 'все' : $subjectString;

        $regionString = getStringFromId($regions, $row_catalog['field6']);
        $regionString = ($regionString == '') ? 'все' : $regionString;

        $profString = getStringFromId($professions, $row_catalog['field7']);
        $profString = ($profString == '') ? 'все' : $profString;

        $themesString = getStringFromId($themes, $row_catalog['field12']);
        $themesString = ($themesString == '') ? 'все' : $themesString;

        $cityString = ($row_catalog['field8'] == '') ? 'все' : $row_catalog['field8'];
        $indexString = ($row_catalog['field9'] == '') ? 'все' : $row_catalog['field9'];
        $groupsString = $groups[$row_catalog['field17']];
        $groupsString = (intval($row_catalog['field17']) == 0) ? 'все' : $groupsString[1].'-'.$groupsString[2];
        ?>
        <td colspan="7">
            <div class="description">
                <div class="title">Субъект:</div>
                <div class="value"><?= $subjectString ?></div>
            </div>
            <div class="description">
                <div class="title">Нас. пункт:</div>
                <div class="value"><?= $cityString ?></div>
            </div>
            <div class='description'>
                <div class='title'>Индекс:</div>
                <div class='value'><?= $indexString ?></div>
            </div>
            <div class="description">
                <div class="title">Район:</div>
                <div class="value"><?= $regionString ?></div>
            </div>
            <div class='description'>
                <div class='title'>Группа:</div>
                <div class='value'><?= $groupsString ?></div>
            </div>
            <div class="description">
                <div class="title">Профессия:</div>
                <div class="value"><?= $profString ?></div>
            </div>
            <div class='description'>
                <div class='title'>Темы/Проблемы:</div>
                <div class='value'><?= $themesString ?></div>
            </div>
        </td>
    </tr>
    <?php
} while ($row_catalog = el_dbfetch($catalog));
}else{
    echo 'К сожалению, ничего не найдено.';
}
?>
    </tbody>
</table>
<?php
el_paging($pn, $currentPage, $queryString_catalog, $totalPages_catalog, $maxRows_catalog, $tr);
?>
<script>
    $(document).ready(function(){
        meetings.buttons_init();
    });
</script>
