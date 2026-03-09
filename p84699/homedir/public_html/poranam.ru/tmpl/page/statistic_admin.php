    <div class="content">
        <div class="wrap">

            <main>
                <div class="box">
                    <h1>Статистика</h1>
                </div>
                <div class="box">
                    <?
                    if($_SESSION['user_level'] == 11){
                    ?>
                    <div class="static_data">
                        <h2>Пользователи</h2>
                        <div class="group">
                            <h3>Зарегистрировано</h3>
                            <div class="item">
                                <div class="el_data">
                                    <label>Всего</label>
                                    <input disabled class="el_input" type="text" value="<?=getStatUser()?>">
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Из них активных</label>
                                    <input disabled class="el_input" type="text" value="<?=getStatUser(' active = 1')?>">
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Заблокировано</label>
                                    <input disabled class="el_input" type="text" value="<?=getStatUser(' active = 0')?>">
                                    <a href="/lichnyy-kabinet/polzovateli/?inactive"><button class="button icon">
                                            <span class="material-icons">visibility</span></button></a>
                                </div>
                            </div>

                            <h3>Кураторы</h3>
                            <div class="item">
                                <div class="el_data">
                                    <label>Всего назначено</label>
                                    <input disabled class="el_input" type="text" value="<?=getStatUser(' field6 >= 4 AND field6 <=9')?>">
                                    <a href="/lichnyy-kabinet/polzovateli/?sf6_from=4&sf6_to=9"><button class="button icon">
                                            <span class="material-icons">visibility</span></button></a>
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Центр</label>
                                    <input disabled class="el_input" type="text" value="<?=getStatUser(' field6 = 4')?>">
                                    <a href="/lichnyy-kabinet/polzovateli/?district=4"><button class="button icon">
                                            <span class="material-icons">visibility</span></button></a>
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Страна</label>
                                    <input disabled class="el_input" type="text" value="<?=getStatUser(' field6 = 5')?>">
                                    <a href="/lichnyy-kabinet/polzovateli/?district=5"><button class="button icon">
                                            <span class="material-icons">visibility</span></button></a>
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Субъект</label>
                                    <input disabled class="el_input" type="text" value="<?=getStatUser(' field6 = 6')?>">
                                    <a href="/lichnyy-kabinet/polzovateli/?district=6"><button class="button icon">
                                            <span class="material-icons">visibility</span></button></a>
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Населённый пункт</label>
                                    <input disabled class="el_input" type="text" value="<?=getStatUser(' field6 = 7')?>">
                                    <a href="/lichnyy-kabinet/polzovateli/?district=7"><button class="button icon">
                                            <span class="material-icons">visibility</span></button></a>
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Район/округ</label>
                                    <input disabled class="el_input" type="text" value="<?=getStatUser(' field6 = 8')?>">
                                    <a href="/lichnyy-kabinet/polzovateli/?district=8"><button class="button icon">
                                            <span class="material-icons">visibility</span></button></a>
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Индекс</label>
                                    <input disabled class="el_input" type="text" value="<?=getStatUser(' field6 = 9')?>">
                                    <a href="/lichnyy-kabinet/polzovateli/?district=9"><button class="button icon">
                                            <span class="material-icons">visibility</span></button></a>
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Профессий</label>
                                    <input disabled class="el_input" type="text" value="<?=getStatUser(' active = 1 GROUP BY field7')?>">
                                    <a href="/lichnyy-kabinet/polzovateli/?gf7"><button class="button icon">
                                            <span class="material-icons">visibility</span></button></a>
                                </div>
                            </div>
                            <h3 class="red">Не назначен куратор</h3>
                            <div class="item">
                                <div class="el_data">
                                    <label>Пользователей</label>
                                    <input disabled class="el_input" type="text" value="<?=getStatUser(' active = 1 AND field24 IS NULL OR field24 = 0')?>">
                                    <a href="/lichnyy-kabinet/polzovateli/?sf24=0"><button class="button icon">
                                            <span class="material-icons">visibility</span></button></a>
                                </div>
                            </div>
                        </div>
                        <!-- -->
                    </div>
                    <?
                    }
                    ?>
                    <div class="static_data">
                        <h2>Инициативы</h2>
                        <div class="group">
                            <h3>Подано</h3>
                            <div class="item">
                                <div class="el_data">
                                    <label>Всего</label>
                                    <input disabled class="el_input" type="text" value="312">
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Одобрено</label>
                                    <input disabled class="el_input" type="text" value="15">
                                    <button class="button icon"><span class="material-icons">visibility</span></button>
                                </div>
                            </div>
                            <?
                            if($_SESSION['user_level'] == 11){
                            ?>
                            <div class="item">
                                <div class="el_data">
                                    <label>Ожидает одобрения</label>
                                    <input disabled class="el_input" type="text" value="48">
                                    <button class="button icon"><span class="material-icons">visibility</span></button>
                                </div>
                            </div>
                            <?
                            }
                            ?>

                            <h3>Голосований по инициативам</h3>
                            <div class="item">
                                <div class="el_data">
                                    <label>Идёт</label>
                                    <input disabled class="el_input" type="text" value="12">
                                    <button class="button icon"><span class="material-icons">visibility</span></button>
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Завершено</label>
                                    <input disabled class="el_input" type="text" value="26">
                                    <button class="button icon"><span class="material-icons">visibility</span></button>
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Передано в "Голосование"</label>
                                    <input disabled class="el_input" type="text" value="17">
                                    <button class="button icon"><span class="material-icons">visibility</span></button>
                                </div>
                            </div>
                            <h3 class="red">Ожидает передачи для голосования</h3>
                            <div class="item">
                                <div class="el_data">
                                    <label>Инициатив</label>
                                    <input disabled class="el_input" type="text" value="4">
                                    <button class="button icon"><span class="material-icons">visibility</span></button>
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="static_data">
                        <h2>Голосования</h2>
                        <div class="group">
                            <h3>Создано</h3>
                            <div class="item">
                                <div class="el_data">
                                    <label>Всего</label>
                                    <input disabled class="el_input" type="text" value="38">
                                </div>
                            </div>

                            <div class="item">
                                <div class="el_data">
                                    <label>Не началось</label>
                                    <input disabled class="el_input" type="text" value="12">
                                    <button class="button icon"><span class="material-icons">visibility</span></button>
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Идёт</label>
                                    <input disabled class="el_input" type="text" value="6">
                                    <button class="button icon"><span class="material-icons">visibility</span></button>
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Завершено</label>
                                    <input disabled class="el_input" type="text" value="8">
                                    <button class="button icon"><span class="material-icons">visibility</span></button>
                                </div>
                            </div>
                            <h3 class="red">Ожидает запуска</h3>
                            <div class="item">
                                <div class="el_data">
                                    <label>Голосований</label>
                                    <input disabled class="el_input" type="text" value="5">
                                    <button class="button icon"><span class="material-icons">visibility</span></button>
                                </div>
                            </div>



                        </div>
                    </div>
                    <div class="static_data">
                        <h2>Мероприятия</h2>
                        <div class="group">
                            <h3>Создано</h3>
                            <div class="item">
                                <div class="el_data">
                                    <label>Всего</label>
                                    <input disabled class="el_input" type="text" value="38">
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Проведено</label>
                                    <input disabled class="el_input" type="text" value="8">
                                    <button class="button icon"><span class="material-icons">visibility</span></button>
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Запланировано</label>
                                    <input disabled class="el_input" type="text" value="6">
                                    <button class="button icon"><span class="material-icons">visibility</span></button>
                                </div>
                            </div>

                            <h3 class="red">Ожидает одобрения</h3>
                            <div class="item">
                                <div class="el_data">
                                    <label>Мероприятий</label>
                                    <input disabled class="el_input" type="text" value="5">
                                    <button class="button icon"><span class="material-icons">visibility</span></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?/*div class="static_data">
                        <h2>Финансы</h2>
                        <div class="group">
                            <h3>Баланс</h3>
                            <div class="item">
                                <div class="el_data">
                                    <label>В кассе р.</label>
                                    <input disabled class="el_input" type="text" value="-2 352">
                                </div>
                            </div>
                            <h3>Поступления</h3>
                            <div class="item">
                                <div class="el_data">
                                    <label>Всего р.</label>
                                    <input disabled class="el_input" type="text" value="15 328">
                                    <button class="button icon"><span class="material-icons">visibility</span></button>
                                </div>
                            </div>
                            <h3>Расход</h3>
                            <div class="item">
                                <div class="el_data">
                                    <label>Всего р.</label>
                                    <input disabled class="el_input" type="text" value="528">
                                    <button class="button icon"><span class="material-icons">visibility</span></button>
                                </div>
                            </div>
                        </div>
                    </div*/?>
                </div>
            </main>
            <!-- <div class="donate">
            <div class="wrap">
                <div class="box">Donate section</div>
            </div>

        </div> -->

        </div>
    </div>