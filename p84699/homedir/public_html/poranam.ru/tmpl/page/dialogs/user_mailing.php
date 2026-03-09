<?php

?>
<div class="pop_up">
    <div class="title">
        <h2>Отправить сообщение</h2>
        <div class="close" onclick="pop_up_sender_close(); return false"><span class="material-icons">highlight_off</span></div>
    </div>
    <section>
        <form>
            <h3>Сообщение</h3>
            <div class="group">
                <div class="item w_100">
                    <div class="el_data w_100">
                        <input class="el_input" type="text" placeholder="Тема сообщения">
                    </div>
                </div>
                <div class="item w_100">
                    <div class="el_data w_100">
                        <label>Текст сообщения</label>
                        <textarea class="el_textarea" name="demo-01" id="demo-01"></textarea>
                    </div>

                </div>
            </div>
            <h3>Получатели</h3>

            <div class="group">
                <div class="item w_100">
                    <div class="custom_checkbox">
                        <label class="container">Отправить всем
                            <input type="checkbox"><span class="checkmark"></span>
                        </label>
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
                    <select multiple data-label="Населённый пункт" data-place="Выберите">
                        <option value="Населённый пункт 1">Населённый пункт 1</option>
                        <option selected value="Населённый пункт 2">Населённый пункт 2</option>
                        <option value="Населённый пункт 3">Населённый пунктйон 3</option>
                    </select>
                </div>
                <div class="item">
                    <select multiple data-label="Район / Округ" data-place="Выберите">
                        <option value="Район / Округ 1">Район / Округ 1</option>
                        <option value="Район / Округ 2">Район / Округ 2</option>
                        <option selected value="Район / Округ 3">Район / Округ 3</option>
                        <option value="Район / Округ 4">Район / Округ 4</option>
                    </select>
                </div>
                <div class="item">
                    <div class="el_data">
                        <label for="post_index">Индекс</label>
                        <input class="el_input" value="123456" id="post_index10" type="text">
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
                <div class="item">
                    <select multiple data-label="Профессия" data-place="Выберите">
                        <option value="Профессия пункт 1">Профессия пункт 1</option>
                        <option selected value="Профессия пункт 2">Профессия пункт 2</option>
                        <option value="Профессия пункт 3">Профессия пунктйон 3</option>
                    </select>
                </div>
            </div>
            <h3>Уровень доступа</h3>
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
                    <button class="button text icon"><span class="material-icons">save</span>Отправить</button>
                </div>
            </div>
        </form>
    </section>
</div>
