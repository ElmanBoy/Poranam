<?php

?>
<div class="pop_up">
    <div class="title">
        <h2>Новый пользователь</h2>
        <div class="close" onclick="pop_up_profile_add_close(); return false"><span class="material-icons">highlight_off</span></div>
    </div>
    <section>
        <h3>ID 1546734187</h3>
        <form>
            <h3>Статистика</h3>
            <div class="group">
                <div class="item w_100">
                    <select data-label="Статус">
                        <option selected value="Активный">Активный</option>
                        <option value="Заблокирован">Заблокирован</option>
                    </select>
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
                    <select data-label="Профессия" data-place="Выберите">
                        <option value="значение 11">значение 11</option>
                        <option selected value="значение 21">значение 21</option>
                        <option value="значение 31">значение 31</option>
                    </select>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="metka_1">Телефон</label>
                        <input class="el_input" id="metka_1" value="+7 903 123-45-67" type="text">
                    </div>
                </div>
                <div class="item">
                    <div class="el_data required">
                        <label for="metka_1">Почта</label>
                        <input required class="el_input" id="metka_1" value="mail@post.ru" type="text">
                    </div>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="metka_1">Пароль</label>
                        <input class="el_input" id="metka_1" value="H84_en3Hk43-dJkK3k4" type="password">
                    </div>
                    <button class="button text">Сгенерировать</button>
                </div>
            </div>
            <h3>Местонахождение</h3>
            <div class="group">
                <div class="item">
                    <select data-label="Субъект" data-place="Выберите">
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
                    <select data-label="Район / Округ" data-place="Выберите">
                        <option value="Район / Округ 1">Район / Округ 1</option>
                        <option value="Район / Округ 2">Район / Округ 2</option>
                        <option selected value="Район / Округ 3">Район / Округ 3</option>
                        <option value="Район / Округ 4">Район / Округ 4</option>
                    </select>
                </div>
                <div class="item">
                    <select data-label="Населённый пункт" data-place="Выберите">
                        <option value="Населённый пункт 1">Населённый пункт 1</option>
                        <option selected value="Населённый пункт 2">Населённый пункт 2</option>
                        <option value="Населённый пункт 3">Населённый пунктйон 3</option>
                    </select>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="post_index">Индекс</label>
                        <input class="el_input" value="123456" id="post_index6" type="text">
                    </div>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="post_index">Улица</label>
                        <input class="el_input" value="123456" id="post_index6" type="text">
                    </div>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="post_index">Номер дома</label>
                        <input class="el_input" value="123456" id="post_index6" type="text">
                    </div>
                </div>
            </div>
            <h3>Уровень доступа</h3>
            <div class="group">
                <div class="item">
                    <select data-label="Ранг" data-place="Выберите">
                        <option value="Администратор">Администратор</option>
                        <option selected value="Куратор">Куратор</option>
                        <option value="Пользователь">Пользователь</option>
                    </select>
                </div>
                <div class="item">
                    <select data-label="Полномочия" data-place="Выберите">
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
                    <select data-label="Куратор пользователя" data-place="Выберите">
                        <option value="значение 1ц">значение 1ц</option>
                        <option value="значение 2ц">значение 2ц</option>
                        <option value="значение 3ц">значение 3ц</option>
                    </select>
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
