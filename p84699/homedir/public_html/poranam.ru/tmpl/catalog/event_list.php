<?php
session_start();
$themes = getRegistry('registryVote');
$subjects = getRegistry('subjects');
$regions = getRegistry('regions');
$professions = getRegistry('proffesions');
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
                <a href="" class="user_profile_link" data-id="<?= $row_catalog['field4'] ?>"><?= $row_catalog['field4'] ?></a>
                <?
            } else {
                echo $row_catalog['field4'];
            }
            ?>
            <button class="button text icon more"><span class="material-icons">unfold_more</span>Участники</button>
        </td>

        <td><?= $row_catalog['field2'] ?></td>
        <td><?= $themes[$row_catalog['field12']]; ?></td>
        <td><?= $row_catalog['field1'] ?></td>
        <td>
            <?
            switch ($row_catalog['field14']) {
                case 10: //Редактировать только что созданную может зарегистрированный автор инициативы или КЦ
                    if ($_SESSION['user_level'] > 0) {
                        if ($_SESSION['user_level'] != 10 ||
                            $_SESSION['user_index'] . '_' . $_SESSION['user_id'] == $row_catalog['field4'] ||
								$_SESSION['user_level'] == 4) {
                        ?>
                        <button class="button icon edit_init" data-id="<?= $row_catalog['id'] ?>"
                                title="редактировать">
                            <span class="material-icons">edit</span></button>
                        <?
                        }
                        if ($_SESSION['user_level'] == 11 || $_SESSION['user_level'] == 4) {
                            ?>
                            <button data-id="<?= $row_catalog['id'] ?>" class="button icon init_run" title="Запустить">
                                <span class="material-icons">play_arrow</span></button>
                            <?
                        }
                        //Показывать автору
                        if ($_SESSION['user_index'] . '_' . $_SESSION['user_id'] == $row_catalog['field4']) {
                            ?>
                            <button class="button icon red init_remove" data-id="<?= $row_catalog['id'] ?>" title="Удалить">
                                <span class="material-icons">delete_forever</span></button>
                            <?
                        }
                    }
                    break;
                case 14: //Остановить может Администратор
                    if ($_SESSION['user_level'] == 11) {
                        ?>
                        <button class="button icon init_stop" data-id="<?= $row_catalog['id'] ?>" title="Завершить">
                            <span class="material-icons">stop</span></button>
                        <?
                    } else {
                        echo 'Идёт';
                    }
                    break;
                case 15: //Завершенная инициатива
                    if ($_SESSION['user_level'] > 0) {
                        ?>
                        <button onclick="pop_up_meeting(); return false" class="button icon" data-id="<?= $row_catalog['id'] ?>" title="Просмотр">
                            <span class="material-icons">remove_red_eye</span>
                        </button>
                        <?
                    } else {
                        echo 'Завершено';
                    }
                    break;
				case 13: //Голосование рассматривает Администратор
                    if ($_SESSION['user_level'] > 0) { //Редактировать может автор или КЦ
                        if ($_SESSION['user_index'] . '_' . $_SESSION['user_id'] == $row_catalog['field4']
								|| $_SESSION['user_level'] == 4) {
                            ?>
                            <button class="button icon edit_votes" data-id="<?= $row_catalog['id'] ?>"
                                    title="редактировать">
                                <span class="material-icons">edit</span></button>
							<?
						}
                        if($_SESSION['user_level'] == 4){
							?>
							<button data-id="<?= $row_catalog['id'] ?>" class="button icon votes_approve" title="На утверждение">
								<span class="material-icons">play_arrow</span></button>
                            <?
                        }
                        if ($_SESSION['user_level'] == 11) { //Запускать может только Администратор
                            ?>
                            <button data-id="<?= $row_catalog['id'] ?>" class="button icon votes_run" title="Запустить">
                                <span class="material-icons">play_arrow</span></button>
                            <?
                        }
                        //Показывать автору
                        /*if ($_SESSION['user_index'] . '_' . $_SESSION['user_id'] == $row_catalog['field4']) {
                            ?>
                            <button class="button icon red init_remove" data-id="<?= $row_catalog['id'] ?>" title="Удалить">
                                <span class="material-icons">delete_forever</span></button>
                            <?
                        }*/
                    }
                    break;
            }
            ?>
        </td>
        <td>
            <button onclick="pop_up_meeting_list(); return false" class="button icon text"><span class="material-icons">list</span>Список</button>
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

        $cityString = ($row_catalog['field8'] == '') ? 'все' : $row_catalog['field8'];
        ?>
        <td colspan="7">
            <div class="description">
                <div class="title">Регион:</div>
                <div class="value"><?= $subjectString ?></div>
            </div>
            <div class="description">
                <div class="title">Город:</div>
                <div class="value"><?= $cityString ?></div>
            </div>
            <div class="description">
                <div class="title">Район:</div>
                <div class="value"><?= $regionString ?></div>
            </div>
            <div class="description">
                <div class="title">Профессия:</div>
                <div class="value"><?= $profString ?></div>
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
<script>
    $(document).ready(function(){
        meetings.buttons_init();
    });
</script>
