<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/tmpl/page/blocks/header.php';
?>
	<nav id="admin_menu">
		<?
		include $_SERVER['DOCUMENT_ROOT'] . '/tmpl/page/blocks/admin_menu.php';
		?>
	</nav>

	<div class="content">
		<div class="wrap">

			<main>
				<div class="box">
					<h1>Голосования</h1>
				</div>
				<div class="text"><? el_text('el_pageprint', 'text'); ?></div>
				<div class="box">
					<div class="control">
						<button class="text icon" id="button_votes_filter"><span
									class="material-icons">filter_alt</span>Фильтр
						</button>
						<?
						if ($_SESSION['user_level'] > 0) {
							?>
							<button class="text icon" id="button_votes_new"><span
										class="material-icons">add_circle</span>Создать
							</button>
							<?
						}
						if ($_SESSION['user_level'] == 4) {
							?>
							<button class="text icon group_action" id="button_votes_approve" disabled><span
										class="material-icons">play_arrow</span>Утвердить
							</button>
							<button class="text icon group_action" id="button_votes_remove" disabled>
								<span class="material-icons">delete_forever</span>Удалить
							</button>
							<?
						}
						if ($_SESSION['user_level'] == 11) {
							?>
							<button class="text icon group_action" id="button_votes_start" disabled><span
										class="material-icons">play_arrow</span>Запустить
							</button>
							<button class="text icon group_action" id="button_votes_stop" disabled>
								<span class="material-icons">stop</span>Завершить
							</button>
							<button class="text icon group_action" id="button_votes_remove" disabled>
								<span class="material-icons">delete_forever</span>Удалить
							</button>
							<?
						}
						?>
					</div>
				</div>
				<div class="box">
					<div class="scroll_wrap" id="init_table">

						<?

						//Черновики не показывать незарегистрированным
						if (intval($_SESSION['user_level']) == 0) {
                            if(intval($_GET['sf14']) == 0){
                                $_GET['sf14_from'] = 6; //Голосование запущено
                            }
						}
						//Показываем голосования Куратору центра
						if (intval($_SESSION['user_level']) == 4) {
						    $_GET['sf5'] = [0, '', $_SESSION['user_subject']];
							$_GET['sf6'] = [0, '', $_SESSION['user_region']];
							$_GET['sf14_from'] = 4; //Голосование создано

							//Показываем Администратору утвержденные голосования
						} elseif (intval($_SESSION['user_level']) == 11) {
							$_GET['sf14'] = [5, 6]; //Голосование утверждено

						} elseif (intval($_SESSION['user_level']) > 0 && intval($_SESSION['user_level']) < 11) {
							//Показываем голосования всем остальным зарегистрированным пользователям
                            if(!isset($_GET['filter'])) {
                                if (strlen($_GET['sf5']) == 0)
                                    $_GET['sf5'] = ['0', '', 'null', $_SESSION['user_subject']];
                                if (strlen($_GET['sf6']) == 0)
                                    $_GET['sf6'] = ['0', '', 'null', $_SESSION['user_region']];
                                if (strlen($_GET['sf7']) == 0)
                                    $_GET['sf7'] = ['0', '', 'null', $_SESSION['user_prof']];
                                if (strlen($_GET['sf8']) == 0)
                                    $_GET['sf8'] = ['0', '', 'null', $_SESSION['user_city']];
                                if (strlen($_GET['sf9']) == 0)
                                    $_GET['sf9'] = ['0', '', 'null', $_SESSION['user_index']];
                                if (strlen($_GET['sf17']) == 0)
                                    $_GET['sf17'] = ['0', '', 'null', $_SESSION['user_group']];
                                if (strlen($_GET['sf12']) == 0)
                                    $_GET['sf12'] = array_merge(['0', '', 'null'], explode(',', $_SESSION['user_themes']));
                                //$_GET['sf10'] = ['0', '', 'null', $_SESSION['user_street']];
                                //$_GET['sf11'] = ['0', '', 'null', $_SESSION['user_house']];
                            }
							if(strlen($_GET['sf14']) == 0){
							    $_GET['sf14'] = [6, 7]; //Голосование запущено
                            }
						}
						el_module('el_pagemodule', '');
						?>

					</div>
					<? /*div class="pagination">
                    <div class="arrow">
                        <div class="button icon"><span class="material-icons">chevron_left</span></div>
                    </div>
                    <div class="page"><a href="#">1</a></div>
                    <div class="page"><a href="#">2</a></div>
                    <div class="page"><a href="#">3</a></div>
                    <div class="page"><a href="#">4</a></div>
                    <div class="page current">5</div>
                    <div class="page"><a href="#">6</a></div>
                    <div class="page"><a href="#">7</a></div>
                    <div class="page"><a href="#">8</a></div>
                    <div class="page"><a href="#">9</a></div>
                    <div class="page"><a href="#">10</a></div>
                    <div class="page"><a href="#">11</a></div>
                    <div class="page"><a href="#">12</a></div>
                    <div class="page"><a href="#">13</a></div>
                    <div class="page"><a href="#">14</a></div>
                    <div class="page"><a href="#">15</a></div>
                    <div class="page dotted"><a href="#">....</a></div>
                    <div class="arrow">
                        <div class="button icon"><span class="material-icons">chevron_right</span></div>
                    </div>
                </div*/ ?>
				</div>


			</main>
			<!-- <div class="donate">
			<div class="wrap">
				<div class="box">Donate section</div>
			</div>

		</div> -->

		</div>
	</div>
	<script src="/js/votes.js?ver=<?= el_genpass() ?>"></script>
<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/tmpl/page/blocks/footer.php';
?>