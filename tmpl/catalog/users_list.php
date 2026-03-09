<?php
session_start();
//print_r($_GET);
$regions = getRegistry('regions');
$subjects = getRegistry('subjects');
$professions = getRegistry('proffesions');
$statuses = getRegistry('userstatus', ['id', 'field1'], 'id DESC');
$groups = getRegistry('groups', ['id', 'field1', 'field2']);
$allowedStatuses = (intval($_SESSION['user_level']) == 11) ? $statuses : filterStatuses($statuses);
$userCount = el_dbnumrows($catalog);

if($tr > 0){
    echo '<div class="user_count">Найд'.el_postfix($tr, 'ен', 'ены', 'ено').' '.$tr.' пользовател'.el_postfix($tr, 'ь', 'я', 'ей').'</div>';
?>
<table class="table_data">
	<thead>
	<tr>
		<?
		if ($_SESSION['user_level'] == 11 || $_SESSION['user_level']  < 10) {
		?>
		<th style="width: 1rem">
			<div class="custom_checkbox">
				<label class="container">
                    <input type="checkbox" id="check_all" value="0"<?=$_SESSION['user_checked'] == [0] ? ' checked' : ''?>>
                    <span class="checkmark"></span></label>
			</div>
		</th>
		<?
		}
		?>
		<th style='width: 30%'>ID</th>
		<th>
			<div class="button icon sort"><span class="material-icons">filter_list</span></div>Дата
		</th>
		<th>
			<div class="button icon sort"><span class="material-icons">filter_list</span></div>Баллы
		</th>
		<?/*th>
			<div class="button icon sort"><span class="material-icons">filter_list</span></div>Регион
		</th*/?>
        <?
        if(isset($_SESSION['user_id'])){
        ?>
		<?/*th>
			<div class="button icon sort"><span class="material-icons">filter_list</span></div>Профессия
		</th*/?>
		<th>
			<div class="button icon sort"><span class="material-icons">filter_list</span></div>Донаты
		</th>
		<th>
			<div class="button icon sort"><span class="material-icons">filter_list</span></div>Голосования
		</th>
		<th>
			<div class="button icon sort"><span class="material-icons">filter_list</span></div>Инициативы
		</th>
		<th>
			<div class="button icon sort"><span class="material-icons">filter_list</span></div>Мероприятия
		</th>
		<th>
			<div class="button icon sort"><span class="material-icons">filter_list</span></div>Статус
		</th>
        <?/*th>
            <div class="button icon sort"><span class="material-icons">filter_list</span></div>Группа
        </th*/?>
        <?
            if(intval($_SESSION['user_level']) != 10){
            ?>
		<th style="width: 15rem">
			<div class="button icon sort"><span class="material-icons">filter_list</span></div>Ранг, Группа, Руководит группой
		</th>
        <?
            }
        }
        ?>
	</tr>
	</thead>

	<tbody>
	<?
	do{
	?>
	<!-- row -->
	<tr>
		<?
		if ($_SESSION['user_level'] == 11 || $_SESSION['user_level']  < 10) {
		?>
		<td>
			<div class="custom_checkbox">
				<label class="container">
					<input type="checkbox" value="<?=$row_catalog['id']?>"<?=is_array($_SESSION['user_checked']) && in_array($row_catalog['id'],
                        $_SESSION['user_checked']) || $_SESSION['user_checked'] == [0] ? ' checked' : ''?>>
                    <span class="checkmark"></span>
				</label>
			</div>
		</td>
		<?
		}
		?>


		<td>
            <div class='vline top'></div>
			<?
			$userId = $row_catalog['user_id'];
			if (intval($_SESSION['user_level']) > 0 && intval($_SESSION['user_level']) != 10) {
				?>
				<a href="" class="user_profile_link<?=(($row_catalog['field24'] == 0 || $row_catalog['field24'] == '')
                && $row_catalog['field6'] != 11 && (intval($_SESSION['user_level']) == 4 || intval($_SESSION['user_level']) == 11) ? ' marked" title="Нет вышестоящего куратора' : '')?>"
                   onclick="pop_up_profile(); return false" data-id="<?= $row_catalog['id'] ?>"><?= $userId ?></a>
				<?
			} else {
				echo $userId;
			}
			/*if($_SESSION['user_level'] != 10){
			    echo '<span class="material-icons show_children" data-id="'.$_SESSION['user_id'].'" title="Показать/Скрыть подчиненных">add_2</span>';
            }*/
			?>
			<button class="button icon text more"><span class="material-icons">unfold_more</span>Адрес</button>
            <div class="vline bottom"></div>
		</td>

		<td><?= $row_catalog['field17'] ?></td>
		<td><?= $row_catalog['field18'] ?></td>
        <?/*td>
            <?=$regions[$row_catalog['field9']]?>
        </td*/?>
        <?
        if(isset($_SESSION['user_id'])){
            ?>
		<?/*td>
			<?=$professions[$row_catalog['field7']]?>
		</td*/?>
		<td>
			<?= floatval($row_catalog['field19']) ?>р.
		</td>
		<td>
            <?= floatval($row_catalog['field20']) ?>
		</td>
		<td>
            <?= floatval($row_catalog['field21']) ?>
		</td>
		<td>
            <?= floatval($row_catalog['field22']) ?>
		</td>
		<td>
			<?= ($row_catalog['active'] == 1) ? 'Активный' : 'Не активный' ?>
		</td>
        <?/*td>
            <?=$groups[$row_catalog['field16']][1].'-'.$groups[$row_catalog['field16']][2]?>
        </td*/?>
            <?
            if(intval($_SESSION['user_level']) != 10){
            ?>
		<td id="uid<?=$row_catalog['id']?>">
			<?
            //Если не рядовой пользователь и есть доступные ранги. Или это админ.
            $group_list = getGroupByUser($row_catalog);
            if((intval($_SESSION['user_level']) != 10 && array_key_exists($row_catalog['field6'], $allowedStatuses)) ||
                intval($_SESSION['user_level']) == 11) {
                echo '<select data-label="Ранг" name="userStatus">' . buildSelectFromRegistry($allowedStatuses, array($row_catalog['field6'])) . '</select><br>';

                //$group_list = getGroupByUser($row_catalog);
                if($row_catalog['field6'] != 11) {
                    echo '<select data-label="Входит в группу" name="userGroup">';
                    if ($group_list != []) {
                        echo buildSelectFromRegistry($group_list, array(intval($row_catalog['field16'])));
                    } else {
                        echo '<option value="0">Создать группу</option>';
                    }
                    echo '</select><br>';

                    $groups_list = [];
                    $groupsClass = '';
                    if ($row_catalog['field6'] != 10) {
                        $groups_list = getGroupsByUser($row_catalog);
                    } else {
                        $groupsClass = ' class="userGroups_hide"';
                    }
                    echo '<select data-label="Руководит группой" name="userGroups"' . $groupsClass . '> 
                <option value="0">Не руководит никакой группой</option>';
                    if ($groups_list != []) {
                        echo buildSelectFromRegistry($groups_list, array($row_catalog['field25']));
                    } else {
                        if ($row_catalog['field6'] != 10) {
                            echo '<option value="100000000">Создать группу</option>';
                        }
                    }
                    echo '</select>';
                }
            }else{ //print_r($groups);
                echo '<p>Ранг &laquo;'.$statuses[$row_catalog['field6']].'&raquo;</p>';
                echo intval($row_catalog['field16']) > 0 ? '<p>Входит в группу '.$groups[$row_catalog['field16']][1].'-'.$groups[$row_catalog['field16']][2].'</p>' : '';
                echo intval($row_catalog['field25']) > 0 ? '<p>Руководит группой '.$groups[$row_catalog['field25']][1].'-'.$groups[$row_catalog['field25']][2].'</p>' : '';
            }
            /**/?><!--<br>
            <select data-label="Входит в группу" name="userGroup">
                <?/*
                $group_list = getGroupByUser($row_catalog);
                if($group_list != []) {
                    echo buildSelectFromRegistry($group_list, array(intval($row_catalog['field16'])));
                }else{
                    echo '<p>'.$row_catalog['field16'].'</p>';
                }
                */?>
            </select><br>
            <div class="user_groups"<?/*=($row_catalog['field6'] == 10) ? ' style="display:none"' : ''*/?>>
            --><?/*
            $groups_list = getGroupsByUser($row_catalog);
            echo '<select data-label="Руководит группой" name="userGroups"> 
                <option value="0">Не руководит никакой группой</option>';
                if($groups_list != []) {
                    echo buildSelectFromRegistry($groups_list, array($row_catalog['field25']));
                }else{
                    echo '<option value="100000000">Создать группу</option>';
                }
                echo '</select>';*/?>
            </div>
		</td>
            <?
            }
        }
            ?>
	</tr>
	<tr class="hidden">
        <?
        $subjectString = getStringFromId($subjects, $row_catalog['field8']);
        $subjectString = ($subjectString == '') ? 'все' : $subjectString;

        $regionString = getStringFromId($regions, $row_catalog['field9']);
        $regionString = ($regionString == '') ? 'все' : $regionString;
        ?>
		<td colspan="11">
            <div class='description'>
                <div class='title'>Субъект:</div>
                <div class='value'><?= $subjectString ?></div>
            </div>
            <div class='description'>
                <div class='title'>Район:</div>
                <div class='value'><?= $regionString ?></div>
            </div>
            <div class="description">
				<div class="title">Город:</div>
				<div class="value"><?=$row_catalog['field10']?></div>
			</div>
			<?/*div class="description">
				<div class="title">Район города:</div>
				<div class="value">Центральный</div>
			</div*/?>
			<div class="description">
				<div class="title">Улица:</div>
				<div class="value"><?=$row_catalog['field12']?></div>
			</div>
			<div class="description">
				<div class="title">Индекс:</div>
				<div class="value"><?=$row_catalog['field11']?></div>
			</div>
		</td>
	</tr>
        <tr class="children_users"></tr>
	<!-- row -->
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

/*$g = el_dbselect("SELECT field11 FROM catalog_users_data GROUP BY field11", 0, $g);
if(el_dbnumrows($g) > 0){
    $rg = el_dbfetch($g);
    do{
        $u = el_dbselect("SELECT id, field11 FROM catalog_users_data WHERE field11 = ".$rg['field11']." ORDER BY id", 0, $u);
        if(el_dbnumrows($u) > 0){
            $ru = el_dbfetch($u);
            $user_id = 0;
            do{
                $user_id++;
                el_dbselect("UPDATE catalog_users_data SET user_id = '".$ru['field11'].'-'.$user_id."' WHERE id = ".$ru['id'], 0, $res);

            }while($ru = el_dbfetch($u));
        }
    }while($rg = el_dbfetch($g));
}*/
?>
