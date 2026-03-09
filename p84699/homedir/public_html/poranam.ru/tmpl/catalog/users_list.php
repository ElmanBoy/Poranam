<?php
session_start();
$regions = getRegistry('regions');
$professions = getRegistry('proffesions');
$statuses = getRegistry('userstatus');
$groups = getRegistry('groups', ['id', 'field1', 'field2']);
$allowedStatuses = (intval($_SESSION['user_level']) == 11) ? $statuses : filterStatuses($statuses);

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
				<label class="container"><input type="checkbox" id="check_all"><span class="checkmark"></span></label>
			</div>
		</th>
		<?
		}
		?>
		<th>ID</th>
		<th>
			<div class="button icon sort"><span class="material-icons">filter_list</span></div>Дата
		</th>
		<th>
			<div class="button icon sort"><span class="material-icons">filter_list</span></div>Баллы
		</th>
		<th>
			<div class="button icon sort"><span class="material-icons">filter_list</span></div>Регион
		</th>
        <?
        if(isset($_SESSION['user_id'])){
        ?>
        <th>
            <div class="button icon sort"><span class="material-icons">filter_list</span></div>Группа
        </th>
		<th>
			<div class="button icon sort"><span class="material-icons">filter_list</span></div>Профессия
		</th>
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
        <?
            if(intval($_SESSION['user_level']) != 10){
            ?>
		<th>
			<div class="button icon sort"><span class="material-icons">filter_list</span></div>Ранг
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
		if ($_SESSION['user_level'] == 11 || $_SESSION['user_level'] == 4) {
		?>
		<td>
			<div class="custom_checkbox">
				<label class="container">
					<input type="checkbox"><span class="checkmark"></span>
				</label>
			</div>
		</td>
		<?
		}
		?>


		<td>
			<?
			$userId = $row_catalog['field11'].'-'.$row_catalog['id'];
			if (intval($_SESSION['user_level']) > 0 && intval($_SESSION['user_level']) != 10) {
				?>
				<a href="" class="user_profile_link" onclick="pop_up_profile(); return false" data-id="<?= $userId ?>">ID <?= $userId ?></a>
				<?
			} else {
				echo $userId;
			}
			?>
			<button class="button icon text more"><span class="material-icons">unfold_more</span>Адрес</button>
		</td>

		<td><?= $row_catalog['field17'] ?></td>
		<td><?= $row_catalog['field18'] ?></td>
        <td>
            <?=$regions[$row_catalog['field9']]?>
        </td>
        <?
        if(isset($_SESSION['user_id'])){
            ?>
		<td>
			<?=$groups[$row_catalog['field16']][1].'-'.$groups[$row_catalog['field16']][2]?>
		</td>
		<td>
			<?=$professions[$row_catalog['field7']]?>
		</td>
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
            <?
            if(intval($_SESSION['user_level']) != 10){
            ?>
		<td id="uid<?=$row_catalog['id']?>">
			<?
            //Если не рядовой пользователь и есть доступные ранги. Или это админ.
            if((intval($_SESSION['user_level']) != 10 && array_key_exists($row_catalog['field6'], $allowedStatuses)) ||
                intval($_SESSION['user_level']) == 11) {
                echo '<select data-label="" name="userStatus">' . buildSelectFromRegistry($allowedStatuses, array($row_catalog['field6'])) . '</select>';
            }else{
                echo $statuses[$row_catalog['field6']];
            }?>
		</td>
            <?
            }
        }
            ?>
	</tr>
	<tr class="hidden">

		<td colspan="11">
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
	<!-- row -->
	<?php
	} while ($row_catalog = el_dbfetch($catalog));
	}else{
		echo 'К сожалению, ничего не найдено.';
	}
	?>
	</tbody>
</table>

