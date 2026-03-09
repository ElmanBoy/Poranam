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
        <th>ID организатора</th>
        <th>Дата</th>
        <th>
            <div class="button icon sort"><span class="material-icons">filter_list</span></div>
            Тема
        </th>
        <th>Вопрос</th>

        <th>
            <div class="button icon sort"><span class="material-icons">filter_list</span></div>
            Статус
        </th>
        <th>Ответ</th>
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
        <td><?= stripslashes($row_catalog['field1']) ?></td>
        <td>
            <?
            switch ($row_catalog['field14']) {
                case 1: //Редактировать только что созданную может зарегистрированный автор инициативы или КЦ
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
                case 6: //Остановить может Администратор
                    if ($_SESSION['user_level'] == 11) {
                        ?>
                        <button class="button icon votes_stop" data-id="<?= $row_catalog['id'] ?>" title="Завершить">
                            <span class="material-icons">stop</span></button>
                        <?
                    } else {
                        echo 'Идёт';
                    }
                    break;
                case 7: //Завершенная инициатива
                    if ($_SESSION['user_level'] > 0) {
                        ?>
                        <button class="button icon votes_statement" data-id="<?= $row_catalog['id'] ?>"
                                title="Показать ведомость"><span class="material-icons">get_app</span>
                        </button>
                        <?
						/*//Перевести в Голосование может Администратор или КЦ
                        if ($_SESSION['user_level'] == 11 || $_SESSION['user_level'] == 4) {
                            ?><!--
                            <button class="icon red init_vote" data-id="<?= $row_catalog['id'] ?>" title="В голосование">
                                <span class="material-icons">thumb_up_alt</span></button>
                            --><?/*
                        }*/
                    } else {
                        echo 'Завершено';
                    }
                    break;
                case 4: //Голосование создано и рассматривает КЦ
				case 5: //Голосование рассматривает Администратор
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
            <?
            //Определяем может ли юзер голосовать
            $allowVote = (intval($_SESSION['user_id']) > 0 &&
					($row_catalog['field14'] == 2 || $row_catalog['field14'] == 6));

            //Ищем его ответы по этой инициативе
            if(intval($_SESSION['user_id']) > 0) {
                $rvote = el_dbselect("SELECT field4 FROM catalog_initresult_data WHERE 
            field2 = '" . intval($row_catalog['id']) . "' AND field1 = '" . intval($_SESSION['user_id']) . "'",
                    0, $rvote, 'row', true);
                if($rvote['field4'] == ''){
                    $rvote['field4'] = 2;
                }
            }

			$voteStat = el_calcVoteUsers($row_catalog['id']);
			$voteResults = el_calcVoteResults($row_catalog['id']);
			$totalResults = array_sum($voteResults);

            if(!$is_vote) {
				//Подсчитываем результаты голосования
				$positive = el_calcPercent($voteResults[1], $totalResults);
				$negative = el_calcPercent($voteResults[0], $totalResults);
			}else{
            	$ans = el_dbselect("SELECT * FROM catalog_votesQuestions_data 
            	WHERE field2 = ".intval($row_catalog['id'])." ORDER BY id",
						0, $ans, 'result', true);
            	if(el_dbnumrows($ans) > 0){
            		$rans = el_dbfetch($ans);
				}
			}
            ?>
            <div class="description">
                <div class="votes">
                    <form>
						<? if(!$is_vote){ ?>
                        <div class="vote">
                            <div class="custom_checkbox">
                                <label class="container">
                                    <input type="radio" <?=($allowVote) ? '' : 'disabled'?>
                                           data-id="<?= $row_catalog['id'] ?>" name="init_vote"
                                           <?=(intval($rvote['field4']) == 1) ? 'checked' : ''?> value="1">
                                    <span class="checkmark radio"></span>
                                </label>
                            </div>
                            <div class="answer yes">Да
								<span class="voteResultYes"><?=(intval($voteResults[1]) > 0) ? '('.$voteResults[1].')' : ''?></span>
                                <div class="bar" style="width: <?=intval($positive)?>%;"><span><?=intval($positive)?></span></div>
                            </div>
                        </div>
                        <div class="vote">
                            <div class="custom_checkbox">
                                <label class="container">
                                    <input type="radio" <?=($allowVote) ? '' : 'disabled'?>
                                           data-id="<?= $row_catalog['id'] ?>" name="init_vote"
                                           <?=(intval($rvote['field4']) == 0) ? 'checked' : ''?> value="0">
                                    <span class="checkmark radio"></span>
                                </label>
                            </div>
                            <div class="answer no">Нет
								<span class="voteResultNo"><?=(intval($voteResults[0]) > 0) ? '('.$voteResults[0].')' : ''?></span>
                                <div class="bar" style="width: <?=intval($negative)?>%;"><span><?=intval($negative)?></span></div>
                            </div>
                        </div>
						<?
						}else{
							do{
								$voices = el_calcPercent($voteResults[$rans['id']], $totalResults);
								?>
								<div class="vote">
									<div class="custom_checkbox">
										<label class="container">
											<input type="radio" <?=($allowVote) ? '' : 'disabled'?>
												   data-id="<?= $row_catalog['id'] ?>" name="votes_vote"
													<?=(intval($rvote['field4']) == 0 || $rvote['field4'] == $rans['id']) ? 'checked' : ''?> value="<?=$rans['id']?>">
											<span class="checkmark radio"></span>
										</label>
									</div>
									<div class="answer choice<?=$rans['id']?>"><?=$rans['field1']?>
										<span class="voteResult<?=$rans['id']?>">
											<?=(intval($voteResults[$rans['id']]) > 0) ? '('.$voteResults[$rans['id']].')' : ''?>
										</span>
										<div class="bar" style="width: <?=intval($voices)?>%;">
											<span><?=intval($voices)?></span></div>
									</div>
								</div>
						<?
							}while($rans = el_dbfetch($ans));
						}
						?>

                    </form>
                </div>
                <div class="interes">
                    <div class="svg_wrap">
                        <svg viewBox="0 0 32 32">
                            <circle class='svg_background'></circle>
                            <circle class='svg_calc' stroke-dasharray="<?=$voteStat['percent']?> 100"></circle>
                        </svg>
                        <div class="svg_value"><?=$voteStat['percent']?></div>
                    </div>
                    <p class="statInfo"><?=$voteStat['votes']?> из <?=$voteStat['total']?></p>
                </div>
            </div>
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
<?php
el_paging($pn, $currentPage, $queryString_catalog, $totalPages_catalog, $maxRows_catalog, $tr);
?>
<script>
    $(document).ready(function(){
        initiatives.buttons_init();
    });
</script>
