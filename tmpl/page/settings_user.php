<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/tmpl/page/blocks/header.php';
?>
    <!-- Навигация для зарегистрированных. Разная в зависимости от прав -->
    <nav id="admin_menu">
        <?
        include $_SERVER['DOCUMENT_ROOT'].'/tmpl/page/blocks/admin_menu.php';
        ?>
    </nav>
    <!-- оснвоное поле -->
    <div class="content">
        <div class="wrap">

            <main>
                <div class="box">
                    <h1>Настройки ID 123456-4578961</h1>
                </div>
                <div class="box">
                    <form>
                        <div class="static_data">

                            <h2>Данные профиля</h2>
                            <div class="group">

                                <div class="item">
                                    <div class="el_data input_number">
                                        <label for="metka_1">Статуст</label>
                                        <input disabled class="el_input" id="metka_1" value="Активный" type="text">
                                    </div>
                                </div>

                                <div class="item">
                                    <div class="el_data input_number">
                                        <label for="metka_1">Ранг</label>
                                        <input disabled class="el_input" id="metka_1" value="Нет" type="text">
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="el_data input_number">
                                        <label for="metka_1">Полномочия</label>
                                        <input disabled class="el_input" id="metka_1" value="Нет" type="text">
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="el_data input_number">
                                        <label for="metka_1">Куратор пользователя</label>
                                        <input disabled class="el_input" id="metka_1" value="458123-56892345" type="text">
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="static_data">

                            <h2>Личные данные</h2>
                            <div class="group">

                                <div class="item">
                                    <div class="el_data input_number">
                                        <label for="metka_1">Фамилия</label>
                                        <input class="el_input" id="metka_1" type="text">
                                    </div>
                                </div>

                                <div class="item">
                                    <div class="el_data input_number">
                                        <label for="metka_1">Имя</label>
                                        <input class="el_input" id="metka_1" type="text">
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="el_data input_number">
                                        <label for="metka_1">Отчество</label>
                                        <input class="el_input" id="metka_1" type="text">
                                    </div>
                                </div>
                                <div class="item">
                                    <select required data-label="Профессия" data-place="Выберите">
                                        <option value="значение 11">значение 11</option>
                                        <option value="значение 21">значение 21</option>
                                        <option value="значение 31">значение 31</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="static_data">
                            <h2>Контактная информация</h2>
                            <div class="group">

                                <div class="item">
                                    <div class="el_data input_number">
                                        <label for="metka_1">Телефон</label>
                                        <input required class="el_input" id="metka_1" type="text">
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="el_data input_number">
                                        <label for="metka_1">E-mail</label>
                                        <input required class="el_input" id="metka_1" type="email">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="static_data">

                            <h2>Региональная принадлежность</h2>
                            <div class="group">

                                <div class="item">
                                    <select required data-label="Субъект" data-place="Выберите">
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
                                    <select required data-label="Район / Округ" data-place="Выберите">
                                        <option value="Район / Округ 1">Район / Округ 1</option>
                                        <option value="Район / Округ 2">Район / Округ 2</option>
                                        <option selected value="Район / Округ 3">Район / Округ 3</option>
                                        <option value="Район / Округ 4">Район / Округ 4</option>
                                    </select>
                                </div>
                                <div class="item">
                                    <select required data-label="Населённый пункт" data-place="Выберите">
                                        <option value="Населённый пункт 1">Населённый пункт 1</option>
                                        <option selected value="Населённый пункт 2">Населённый пункт 2</option>
                                        <option value="Населённый пункт 3">Населённый пунктйон 3</option>
                                    </select>
                                </div>
                                <div class="item">
                                    <div class="el_data">
                                        <label for="post_index">Индекс</label>
                                        <input required class="el_input" value="" id="post_index" type="text">
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="el_data">
                                        <label for="street">Улица</label>
                                        <input required class="el_input" value="" id="street" type="text">
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="el_data">
                                        <label for="h_number">Номер дома</label>
                                        <input required class="el_input" value="" id="h_number" type="text">
                                    </div>
                                </div>
                            </div>


                        </div>


                        <div class="static_data">

                            <h2>Безопасность</h2>
                            <div class="group">
                                <h3>Авторизация</h3>
                                <div class="item channel">
                                    <div class="el_data">
                                        <label for="metka_1">Логин</label>
                                        <input disabled class="el_input" id="metka_1" type="text" value="pishite_pisma@mail.ru">
                                    </div>
                                    <div class="el_data">
                                        <label for="metka_1">Пароль</label>
                                        <input class="el_input" id="metka_1" type="password" value="hshhsthtrhrt">
                                    </div>
                                    <div class="button icon text add"><span class="material-icons">gpp_good</span>Сгенерировать</div>
                                </div>
                            </div>
                            <button class="button icon text"><span class="material-icons">save</span>Сохранить</button>
                        </div>
                    </form>
                </div>
            </main>
            <!-- <div class="donate">
            <div class="wrap">
                <div class="box">Donate section</div>
            </div>

        </div> -->

        </div>
    </div>
<script>
    var modulePath = 'settings';
</script>
<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/tmpl/page/blocks/footer.php';
?>