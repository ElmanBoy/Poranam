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
					<h1>Мероприятия</h1>
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
							<button class="text icon" id="button_events_new"><span
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
										class="material-icons">play_arrow</span>Одобрить
							</button>
							<button class="text icon group_action" id="button_votes_stop" disabled>
								<span class="material-icons">how_to_reg</span>Завершить
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
							$_GET['sf14'] = 14; //мероприятие запущено
						}
						//Показываем мероприятия Куратору центра
						if (intval($_SESSION['user_level']) == 4) {
							$_GET['sf5'] = [0, '', $_SESSION['user_subject']];
							$_GET['sf6'] = [0, '', $_SESSION['user_region']];
							$_GET['sf14_from'] = 10; //Мероприятие создано
							//Показываем Администратору утвержденные мероприятия
						} elseif (intval($_SESSION['user_level']) == 11) {
							$_GET['sf14_from'] = 10; //Мероприятие на утверждении и выше
						} elseif (intval($_SESSION['user_level']) > 0 && intval($_SESSION['user_level']) < 11) {
							//Показываем мероприятия всем остальным зарегистрированным пользователям
							$_GET['sf5'] = ['0', '', 'null', $_SESSION['user_subject']];
							$_GET['sf6'] = ['0', '', 'null', $_SESSION['user_region']];
							$_GET['sf7'] = ['0', '', 'null', $_SESSION['user_prof']];
							$_GET['sf8'] = ['0', '', 'null', $_SESSION['user_city']];
							$_GET['sf9'] = ['0', '', 'null', $_SESSION['user_index']];
							$_GET['sf10'] = ['0', '', 'null', $_SESSION['user_street']];
							$_GET['sf11'] = ['0', '', 'null', $_SESSION['user_house']];
							$_GET['sf14_from'] = 14; //мероприятие запущено
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


    <!-- Универсальный wrapper для Pop-Up-ов -->
    <div id="pop_up_filter_meet" class="wrap_pop_up">
        <div class="pop_up">
            <div class="title">
                <h2>Фильтр мероприятий</h2>
                <div class="close" onclick="pop_up_filter_meet_close(); return false"><span class="material-icons">highlight_off</span></div>
            </div>
            <section>
                <form>
                    <h3>Дата проведения</h3>
                    <div class="group">
                        <div class="item">
                            <div class="el_data">
                                <label for="metka_1">С</label>
                                <input class="el_input" id="metka_2м" value="18.06.2020" type="data">
                            </div>
                        </div>
                        <div class="item">
                            <div class="el_data">
                                <label for="metka_1">По</label>
                                <input class="el_input" id="metka_2м" value="30.06.2020" type="data">
                            </div>
                        </div>

                        <div class="item">
                            <select data-label="Статус" data-place="Выберите">
                                <option value="Завершено">Завершено</option>
                                <option value="В плане">Одобрено</option>
                                <option selected value="Черновик">Черновик</option>
                            </select>
                        </div>
                    </div>
                    <h3>Организаторы мероприятия</h3>
                    <div class="group">
                        <div class="item">
                            <div class="el_data">
                                <label for="metka_1">ID организатора</label>
                                <input class="el_input" id="metka_2м" value="" type="text">
                            </div>
                        </div>
                        <div class="item">
                            <select multiple data-label="Субъект" data-place="Выберите">
                                <option value="Адыгея">Адыгея</option>
                                <option value="Алтай">Алтай</option>
                                <option value="Башкортостан">Башкортостан</option>
                                <option value="Бурятия">Бурятия</option>
                                <option value="Дагестан">Дагестан</option>
                                <option value="Ингушетия">Ингушетия</option>
                                <option value="Кабардино-Балкария">Кабардино-Балкария</option>
                                <option value="Калмыкия">Калмыкия</option>
                                <option value="Карачаево-Черкессия">Карачаево-Черкессия</option>
                                <option value="Карелия">Карелия</option>
                                <option value="Коми">Коми</option>
                                <option value="Крым">Крым</option>
                                <option value="Марий Эл">Марий Эл</option>
                                <option value="Саха (Якутия)">Саха (Якутия)</option>
                                <option value="Северная Осетия">Северная Осетия</option>
                                <option value="Тыва">Тыва</option>
                                <option value="Удмуртия">Удмуртия</option>
                            </select>
                        </div>
                        <div class="item">
                            <select multiple data-label="Район / Округ" data-place="Выберите">
                                <option value="Район / Округ 1">Район / Округ 1</option>
                                <option value="Район / Округ 2">Район / Округ 2</option>
                                <option value="Район / Округ 3">Район / Округ 3</option>
                                <option value="Район / Округ 4">Район / Округ 4</option>
                            </select>
                        </div>
                        <div class="item">
                            <select multiple data-label="Населённый пункт" data-place="Выберите">
                                <option value="Населённый пункт 1">Населённый пункт 1</option>
                                <option value="Населённый пункт 2">Населённый пункт 2</option>
                                <option value="Населённый пункт 3">Населённый пунктйон 3</option>
                            </select>
                        </div>
                        <div class="item">
                            <div class="el_data">
                                <label for="post_index">Индекс</label>
                                <input class="el_input" value="123456" type="text">
                            </div>
                        </div>
                        <div class="item">
                            <select multiple data-label="Улица" data-place="Выберите">
                                <option value="Улица пункт 1">Улица пункт 1</option>
                                <option value="Улица пункт 2">Улица пункт 2</option>
                                <option value="Улица пункт 3">Улица пунктйон 3</option>
                            </select>
                        </div>
                        <div class="item">
                            <div class="el_data">
                                <label for="post_index">Номер дома</label>
                                <input class="el_input" value="" type="text">
                            </div>
                        </div>
                        <div class="item">
                            <select multiple data-label="Профессия" data-place="Выберите">
                                <option value="Профессия пункт 1">Профессия пункт 1</option>
                                <option value="Профессия пункт 2">Профессия пункт 2</option>
                                <option value="Профессия пункт 3">Профессия пунктйон 3</option>
                            </select>
                        </div>
                    </div>
                    <div class="group">
                        <div class="item">
                            <select multiple data-label="Ранг" data-place="Выберите">
                                <option value="Администратор">Администратор</option>
                                <option selected value="Куратор">Куратор</option>
                                <option value="Пользователь">Пользователь</option>
                            </select>
                        </div>
                        <div class="item">
                            <select multiple data-label="Полномочия" data-place="Выберите">
                                <option value="Ст.: Субъекта">Ст.: Субъект</option>
                                <option value="Ст.: Район / округ">Ст.: Район / округ</option>
                                <option selected value="Ст.: Населённый пункт">Ст.: Населённый пункт</option>
                                <option value="Ст.: Индекс">Ст.: Индекс</option>
                                <option value="Ст.: Профессия">Ст.: Профессия</option>
                                <option value="Нет">Нет</option>
                            </select>
                        </div>

                    </div>

                    <h3>Участники мероприятия</h3>
                    <div class="group">

                        <div class="item">
                            <select multiple data-label="Субъект" data-place="Выберите">
                                <option value="Адыгея">Адыгея</option>
                                <option value="Алтай">Алтай</option>
                                <option value="Башкортостан">Башкортостан</option>
                                <option value="Бурятия">Бурятия</option>
                                <option value="Дагестан">Дагестан</option>
                                <option value="Ингушетия">Ингушетия</option>
                                <option value="Кабардино-Балкария">Кабардино-Балкария</option>
                                <option value="Калмыкия">Калмыкия</option>
                                <option value="Карачаево-Черкессия">Карачаево-Черкессия</option>
                                <option value="Карелия">Карелия</option>
                                <option value="Коми">Коми</option>
                                <option value="Крым">Крым</option>
                                <option value="Марий Эл">Марий Эл</option>
                                <option value="Саха (Якутия)">Саха (Якутия)</option>
                                <option value="Северная Осетия">Северная Осетия</option>
                                <option value="Тыва">Тыва</option>
                                <option value="Удмуртия">Удмуртия</option>
                            </select>
                        </div>
                        <div class="item">
                            <select multiple data-label="Район / Округ" data-place="Выберите">
                                <option value="Район / Округ 1">Район / Округ 1</option>
                                <option value="Район / Округ 2">Район / Округ 2</option>
                                <option value="Район / Округ 3">Район / Округ 3</option>
                                <option value="Район / Округ 4">Район / Округ 4</option>
                            </select>
                        </div>
                        <div class="item">
                            <select multiple data-label="Населённый пункт" data-place="Выберите">
                                <option value="Населённый пункт 1">Населённый пункт 1</option>
                                <option value="Населённый пункт 2">Населённый пункт 2</option>
                                <option value="Населённый пункт 3">Населённый пунктйон 3</option>
                            </select>
                        </div>
                        <div class="item">
                            <div class="el_data">
                                <label for="post_index">Индекс</label>
                                <input class="el_input" value="123456" type="text">
                            </div>
                        </div>
                        <div class="item">
                            <select multiple data-label="Улица" data-place="Выберите">
                                <option value="Улица пункт 1">Улица пункт 1</option>
                                <option value="Улица пункт 2">Улица пункт 2</option>
                                <option value="Улица пункт 3">Улица пунктйон 3</option>
                            </select>
                        </div>
                        <div class="item">
                            <div class="el_data">
                                <label for="post_index">Номер дома</label>
                                <input class="el_input" value="" type="text">
                            </div>
                        </div>
                        <div class="item">
                            <select multiple data-label="Профессия" data-place="Выберите">
                                <option value="Профессия пункт 1">Профессия пункт 1</option>
                                <option value="Профессия пункт 2">Профессия пункт 2</option>
                                <option value="Профессия пункт 3">Профессия пунктйон 3</option>
                            </select>
                        </div>
                    </div>
                    <div class="group">
                        <div class="item">
                            <select multiple data-label="Ранг" data-place="Выберите">
                                <option value="Администратор">Администратор</option>
                                <option selected value="Куратор">Куратор</option>
                                <option value="Пользователь">Пользователь</option>
                            </select>
                        </div>
                        <div class="item">
                            <select multiple data-label="Полномочия" data-place="Выберите">
                                <option value="Ст.: Субъекта">Ст.: Субъект</option>
                                <option value="Ст.: Район / округ">Ст.: Район / округ</option>
                                <option selected value="Ст.: Населённый пункт">Ст.: Населённый пункт</option>
                                <option value="Ст.: Индекс">Ст.: Индекс</option>
                                <option value="Ст.: Профессия">Ст.: Профессия</option>
                                <option value="Нет">Нет</option>
                            </select>
                        </div>

                    </div>

                    <div class="group">
                        <div class="item">
                            <button class="button icon text"><span class="material-icons">search</span>Найти</button>
                        </div>
                        <div class="item">
                            <button class="button icon text"><span class="material-icons">
                                    restart_alt
                                </span>Сбросить</button>
                        </div>
                    </div>

                </form>
            </section>

        </div>
    </div>
    <!-- Конец "универсальный wrapper" для Pop-Up-ов -->
    <!-- Универсальный wrapper для Pop-Up-ов -->
    <div id="pop_up_profile" class="wrap_pop_up">
        <div class="pop_up">
            <div class="title">
                <h2>Профиль пользователь</h2>
                <div class="close" onclick="pop_up_profile_close(); return false"><span class="material-icons">highlight_off</span></div>
            </div>
            <section>
                <h3>ID 1546734187</h3>
                <form>
                    <h3>Статистика</h3>
                    <div class="group">
                        <div class="item w_100">
                            <select disabled data-label="Статус">
                                <option selected value="Активный">Активный</option>
                                <option value="Заблокирован">Заблокирован</option>
                            </select>

                        </div>
                        <div class="item">
                            <div class="el_data input_number">
                                <label for="metka_1">Баллы</label>

                                <input disabled class="el_input" id="metka_1" value="40" type="number" min="0">

                            </div>
                        </div>
                        <div class="item">
                            <div class="el_data">
                                <label for="metka_1">Инициативы</label>
                                <input class="el_input" id="metka_1x" disabled value="25" type="text">
                            </div>
                        </div>
                        <div class="item">
                            <div class="el_data">
                                <label for="metka_1">Голосования</label>
                                <input class="el_input" id="metka_1c" disabled value="12" type="text">
                            </div>
                        </div>
                        <div class="item">
                            <div class="el_data">
                                <label for="metka_1">Мероприятия</label>
                                <input class="el_input" id="metka_1v" disabled value="3" type="text">
                            </div>
                        </div>
                        <div class="item">
                            <div class="el_data">
                                <label for="metka_1">Финансы</label>
                                <input class="el_input" id="metka_1b" disabled value="12 500" type="text">
                            </div>
                        </div>
                        <div class="item">
                            <div class="el_data">
                                <label for="metka_1">За привлечения</label>
                                <input class="el_input" id="metka_1b" disabled value="1" type="text">
                            </div>
                        </div>
                    </div>
                    <h3>Личные данные</h3>
                    <div class="group">
                        <div class="item">
                            <div class="el_data">
                                <label for="metka_1">Фамилия</label>
                                <input disabled class="el_input" id="metka_1n" value="Константинопольский" type="text">
                            </div>
                        </div>
                        <div class="item">
                            <div class="el_data">
                                <label for="metka_1">Имя</label>
                                <input disabled class="el_input" id="metka_1m" value="Константин" type="text">
                            </div>
                        </div>
                        <div class="item">
                            <div class="el_data">
                                <label for="metka_1">Отчество</label>
                                <input disabled class="el_input" id="metka_1j" value="Константинович" type="text">
                            </div>
                        </div>
                        <div class="item">
                            <select disabled data-label="Профессия" data-place="Выберите">
                                <option value="значение 11">значение 11</option>
                                <option selected value="значение 21">значение 21</option>
                                <option value="значение 31">значение 31</option>
                            </select>
                        </div>
                        <div class="item">
                            <div class="el_data">
                                <label for="metka_1">Телефон</label>
                                <input disabled class="el_input" id="metka_1h" value="+7 903 123-45-67" type="text">
                            </div>
                        </div>
                        <div class="item">
                            <div class="el_data required">
                                <label for="metka_1">Почта</label>
                                <input disabled required class="el_input" id="metka_1g" value="mail@post.ru" type="text">
                            </div>
                        </div>
                        <!-- <div class="item">
                            <div class="el_data">
                                <label for="metka_1">Пароль</label>
                                <input class="el_input" id="metka_1f" value="H84_en3Hk43-dJkK3k4" type="password">
                            </div>
                            <button class="button text">Сгенерировать</button>
                        </div> -->
                    </div>
                    <h3>Местонахождение</h3>
                    <div class="group">
                        <div class="item">
                            <select disabled data-label="Субъект" data-place="Выберите">
                                <option value="Адыгея">Адыгея</option>
                                <option value="Алтай">Алтай</option>
                                <option value="Башкортостан">Башкортостан</option>
                                <option value="Бурятия">Бурятия</option>
                                <option value="Дагестан">Дагестан</option>
                                <option value="Ингушетия">Ингушетия</option>
                                <option value="Кабардино-Балкария">Кабардино-Балкария</option>
                                <option selected value="Калмыкия">Калмыкия</option>
                                <option value="Карачаево-Черкессия">Карачаево-Черкессия</option>
                                <option value="Карелия">Карелия</option>
                                <option value="Коми">Коми</option>
                                <option value="Крым">Крым</option>
                                <option value="Марий Эл">Марий Эл</option>
                                <option value="Саха (Якутия)">Саха (Якутия)</option>
                                <option value="Северная Осетия">Северная Осетия</option>
                                <option value="Тыва">Тыва</option>
                                <option value="Удмуртия">Удмуртия</option>
                            </select>
                        </div>
                        <div class="item">
                            <select disabled data-label="Район / Округ" data-place="Выберите">
                                <option value="Район / Округ 1">Район / Округ 1</option>
                                <option value="Район / Округ 2">Район / Округ 2</option>
                                <option selected value="Район / Округ 3">Район / Округ 3</option>
                                <option value="Район / Округ 4">Район / Округ 4</option>
                            </select>
                        </div>
                        <div class="item">
                            <select disabled data-label="Населённый пункт" data-place="Выберите">
                                <option value="Населённый пункт 1">Населённый пункт 1</option>
                                <option selected value="Населённый пункт 2">Населённый пункт 2</option>
                                <option value="Населённый пункт 3">Населённый пунктйон 3</option>
                            </select>
                        </div>
                        <div class="item">
                            <div class="el_data">
                                <label for="post_index">Индекс</label>
                                <input disabled class="el_input" value="123456" id="post_index" type="text">
                            </div>
                        </div>
                        <div class="item">
                            <div class="el_data">
                                <label for="post_index">Улица</label>
                                <input disabled class="el_input" value="123456" id="post_index" type="text">
                            </div>
                        </div>
                        <div class="item">
                            <div class="el_data">
                                <label for="post_index">Номер дома</label>
                                <input disabled class="el_input" value="123456" id="post_index" type="text">
                            </div>
                        </div>
                    </div>
                    <h3>Уровень доступа</h3>
                    <div class="group">
                        <div class="item">
                            <select disabled data-label="Ранг" data-place="Выберите">
                                <option value="Администратор">Администратор</option>
                                <option selected value="Куратор">Куратор</option>
                                <option value="Пользователь">Пользователь</option>
                            </select>
                        </div>
                        <div class="item">
                            <select disabled data-label="Полномочия" data-place="Выберите">
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
                            <select disabled data-label="Куратор пользователя" data-place="Выберите">
                                <option value="значение 1ц">значение 1ц</option>
                                <option value="значение 2ц">значение 2ц</option>
                                <option value="значение 3ц">значение 3ц</option>
                            </select>
                        </div>
                        <!-- <div class="item">
                            <div class="el_data">
                                <label for="metka_1">Ссылка приглашения</label>
                                <input disabled class="el_input" id="metka_1d" value="http://www.sitename.ru/?invite_link_113783581" type="text">
                            </div>
                            <button class="button icon text"><span class="material-icons">file_copy</span>Копировать</button>
                        </div> -->
                    </div>
                    <!-- <div class="group">
                        <div class="item">
                            <button class="button text icon"><span class="material-icons">save</span>Сохранить</button>
                        </div>
                    </div> -->
                </form>
            </section>
        </div>
    </div>
    <!-- Конец " универсальный wrapper" для Pop-Up-ов -->
    <!-- Универсальный wrapper для Pop-Up-ов -->
    <div id="pop_up_meeting_list" class="wrap_pop_up">
        <div class="pop_up">
            <div class="title">
                <h2>Список участников</h2>
                <div class="close" onclick="pop_up_meeting_list_close(); return false"><span class="material-icons">highlight_off</span></div>
            </div>
            <section>
                <form>
                    <h3>Встреча сторонников Трампа в Новодевичьем монастыре под покровом ночи</h3>
                    <h4>Участников: 478 542</h4>
                    <div class="group">
                        <div class="item">

                            <div class="control">
                                <button class="text icon" onclick="pop_up_warning(); return false"><span class="material-icons">save</span>Сохранить</button>
                                <button class="text icon"><span class="material-icons">file_download</span>Скачать</button>
                            </div>

                            <table class="table_data">
                                <thead>
                                <tr>
                                    <th>
                                        <div class="custom_checkbox">
                                            <label class="container"><input type="checkbox"><span class="checkmark"></span></label>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="button icon sort"><span class="material-icons">filter_list</span></div>ID
                                    </th>
                                    <th>
                                        <div class="button icon sort"><span class="material-icons">filter_list</span></div>Фамилия
                                    </th>
                                    <th>Имя</th>
                                    <th>Отчество</th>
                                </tr>
                                </thead>
                                <tbody>
                                <!-- row -->
                                <tr>
                                    <td>
                                        <div class="custom_checkbox">
                                            <label class="container">
                                                <input type="checkbox"><span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>12543-524365</td>
                                    <td>Константинопольский</td>
                                    <td>Константин</td>
                                    <td>Константинович</td>
                                </tr>
                                <!-- row -->
                                <!-- row -->
                                <tr>
                                    <td>
                                        <div class="custom_checkbox">
                                            <label class="container">
                                                <input checked type="checkbox"><span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>12543-524365</td>
                                    <td>пупкин</td>
                                    <td>слава</td>
                                    <td></td>
                                </tr>
                                <!-- row -->
                                <!-- row -->
                                <tr>
                                    <td>
                                        <div class="custom_checkbox">
                                            <label class="container">
                                                <input type="checkbox"><span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>48325-524365</td>
                                    <td>Сеня Вяземский</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <!-- row -->
                                </tbody>
                            </table>
                            <div class="pagination">
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
                            </div>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>

	<script src="/js/meetings.js?ver=<?= el_genpass() ?>"></script>
<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/tmpl/page/blocks/footer.php';
?>