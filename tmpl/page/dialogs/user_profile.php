<?php
$user_id_arr = explode('_', $_POST['params']);
echo $user_id = $user_id_arr[1];
?>
<div class="pop_up">
    <div class="title">
        <h2>Пользователь</h2>
        <div class="close" onclick="pop_up_profile_close(); return false"><span class="material-icons">highlight_off</span></div>
    </div>
    <section>
        <h3>ID 1546734187</h3>


        <form>

            <h3>Статистика</h3>
            <div class="group">
                <div class="item w_100">
                    <div class="el_data">
                        <label for="metka_1">Статус</label>
                        <input class="el_input" disabled id="metka_1" value="Активный" type="text">
                    </div>
                    <button class="button icon text" onclick="pop_up_warning(); return false"><span class="material-icons">lock</span>Заблокировать</button>
                    <button class="button text icon red" onclick="pop_up_warning(); return false"><span class="material-icons">delete_forever</span>Удалить</button>
                </div>
                <div class="item">
                    <div class="el_data input_number">
                        <label for="metka_1">Баллы</label>


                        <div class="number-minus button icon" type="button" onclick="this.nextElementSibling.stepDown(); this.nextElementSibling.onchange();"><span class="material-icons">keyboard_arrow_down</span></div>
                        <input class="el_input" id="metka_1" value="40" type="number" min="0">
                        <div class="number-plus button icon" type="button" onclick="this.previousElementSibling.stepUp(); this.previousElementSibling.onchange();"><span class="material-icons">keyboard_arrow_up</span></div>


                    </div>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="metka_1">Инициативы</label>
                        <input class="el_input" id="metka_1" disabled value="25" type="text">
                    </div>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="metka_1">Голосования</label>
                        <input class="el_input" id="metka_1" disabled value="12" type="text">
                    </div>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="metka_1">Мероприятия</label>
                        <input class="el_input" id="metka_1" disabled value="3" type="text">
                    </div>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="metka_1">Финансы</label>
                        <input class="el_input" id="metka_1" disabled value="12 500" type="text">
                    </div>
                </div>
            </div>

            <h3>Личные данные</h3>
            <div class="group">
                <div class="item">
                    <div class="el_data">
                        <label for="metka_1">Фамилия</label>
                        <input class="el_input" id="metka_1" value="Константинопольский" type="text">
                    </div>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="metka_1">Имя</label>
                        <input class="el_input" id="metka_1" value="Константин" type="text">
                    </div>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="metka_1">Отчество</label>
                        <input class="el_input" id="metka_1" value="Константинович" type="text">
                    </div>
                </div>


                <div class="item">
                    <div class="el_data">
                        <label for="metka_1">Профессия</label>
                        <div class="el_select" id="metka_1" value="Садовник 3 разряда" type="text">
                            <div class="holder">Выберите</div>
                        </div>
                    </div>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="metka_1">Телефон</label>
                        <input class="el_input" id="metka_1" value="" type="text">
                    </div>
                </div>
                <div class="item">
                    <div class="el_data required">
                        <label for="metka_1">Почта</label>
                        <input required class="el_input" id="metka_1" value="" type="text">
                    </div>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="metka_1">Пароль</label>
                        <input class="el_input" id="metka_1" value="" type="password">
                    </div>
                    <button class="button text">Сгенерировать</button>
                </div>
            </div>

            <h3>Местонахождение</h3>
            <div class="group">
                <div class="item">
                    <div class="el_data">
                        <label for="metka_1">Регион</label>
                        <div class="el_select" id="metka_1" value="Центральный" type="text">
                            <div class="holder">Выберите</div>
                        </div>
                    </div>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="metka_1">Город</label>
                        <input class="el_input" id="metka_1" value="Мытищи" type="text">
                    </div>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="metka_1">Район города</label>
                        <input class="el_input" id="metka_1" value="Лихоборский" type="text">
                    </div>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="metka_1">Номер дома</label>
                        <input class="el_input" id="metka_1" value="13" type="text">
                    </div>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="metka_1">Индекс</label>
                        <input class="el_input" id="metka_1" value="123456" type="text">
                    </div>
                </div>
            </div>

            <h3>Дополнительно</h3>
            <div class="group">
                <div class="item">
                    <div class="el_data">
                        <label for="metka_1">Куратор</label>
                        <div class="el_select" id="metka_1" value="Центральный" type="text">
                            <div class="holder">Выберите</div>
                        </div>
                    </div>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="metka_1">Ссылка приглашения</label>
                        <input disabled class="el_input" id="metka_1" value="http://www.sitename.ru/?invite_link_113783581" type="text">
                    </div>
                    <button class="button icon text"><span class="material-icons">file_copy</span>Копировать</button>
                </div>
            </div>
            <div class="group">
                <div class="item">
                    <button class="button text icon"><span class="material-icons">save</span>Сохранить</button>
                </div>
            </div>

        </form>
    </section>
</div>