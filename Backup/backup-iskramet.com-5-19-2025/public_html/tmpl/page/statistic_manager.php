    <div class="content">
        <div class="wrap">

            <main>
                <div class="box">
                    <h1>Статистика ID <?=$_SESSION['user_index']?>-<?=$_SESSION['user_id']?></h1>
                </div>
                <div class="box">
                    <div class="static_data">
                        <div class="group">
                            <h3>Начислено баллов</h3>
                            <div class="item">
                                <div class="el_data">
                                    <label>Всего</label>
                                    <input disabled class="el_input" type="text" value="<?=$totalScore?>">
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>За инициативы</label>
                                    <input disabled class="el_input" type="text" value="<?=$initScore?>">
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>За мероприятия</label>
                                    <input disabled class="el_input" type="text" value="<?=$eventScore?>">
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>За голосования</label>
                                    <input disabled class="el_input" type="text" value="<?=$pollScore?>">
                                </div>
                            </div>
                            <?/*div class="item">
                                <div class="el_data">
                                    <label>За пожертвования</label>
                                    <input disabled class="el_input" type="text" value="0">
                                </div>
                            </div*/?>
                            <div class="item">
                                <div class="el_data">
                                    <label>За привлечение</label>
                                    <input disabled class="el_input" type="text" value="<?=$referScore?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="static_data">
                        <h2>Полномочия</h2>
                        <div class="group">

                            <h3>Куратор</h3>
                            <div class="item">
                                <div class="el_data">
                                    <label>Субъектов</label>
                                    <input disabled class="el_input" type="text" value="0">

                                </div>
                            </div>

                            <div class="item">
                                <div class="el_data">
                                    <label>Населённых пунктов</label>
                                    <input disabled class="el_input" type="text" value="20">

                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Районов/округов</label>
                                    <input disabled class="el_input" type="text" value="1">

                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Индексов</label>
                                    <input disabled class="el_input" type="text" value="1 354">

                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Профессий</label>
                                    <input disabled class="el_input" type="text" value="4">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="static_data">
                        <h2>Пользователей под управлением</h2>
                        <div class="group">
                            <h3>Зарегистрировано</h3>
                            <div class="item">
                                <div class="el_data">
                                    <label>Всего</label>
                                    <input disabled class="el_input" type="text"
                                           value="<?=getStatUser(' field24 = '.$_SESSION['user_id'])?>">
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Из них активных</label>
                                    <input disabled class="el_input" type="text"
                                           value="<?=getStatUser(' active = 1 AND field24 = '.$_SESSION['user_id'])?>">
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Заблокировано</label>
                                    <input disabled class="el_input" type="text"
                                           value="<?=getStatUser(' active = 0 AND field24 = '.$_SESSION['user_id'])?>">
                                    <a href="/lichnyy-kabinet/polzovateli/?sf24=<?=$_SESSION['user_id']?>&inactive">
                                        <button class="button icon"><span class="material-icons">visibility</span></button></a>
                                </div>
                            </div>
                            <?/*div class="item">
                                <div class="el_data">
                                    <label>Центр</label>
                                    <input disabled class="el_input" type="text" value="321">
                                    <button class="button icon"><span class="material-icons">visibility</span></button>
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Страна</label>
                                    <input disabled class="el_input" type="text" value="321">
                                    <button class="button icon"><span class="material-icons">visibility</span></button>
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Субъект</label>
                                    <input disabled class="el_input" type="text" value="321">
                                    <button class="button icon"><span class="material-icons">visibility</span></button>
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Населённый пункт</label>
                                    <input disabled class="el_input" type="text" value="1 354">
                                    <button class="button icon"><span class="material-icons">visibility</span></button>
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Район/округ</label>
                                    <input disabled class="el_input" type="text" value="1 354">
                                    <button class="button icon"><span class="material-icons">visibility</span></button>
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Индекс</label>
                                    <input disabled class="el_input" type="text" value="1 354">

                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Професся</label>
                                    <input disabled class="el_input" type="text" value="1 354">
                                    <button class="button icon"><span class="material-icons">visibility</span></button>
                                </div>
                            </div*/?>
                            <h3 class="red">Не назначен куратор</h3>
                            <div class="item">
                                <div class="el_data">
                                    <label>Пользователей</label>
                                    <input disabled class="el_input" type="text" value="<?=getStatUser(' active = 1 AND field24 IS NULL OR field24 = 0')?>">
                                    <button class="button icon"><span class="material-icons">visibility</span></button>
                                </div>
                            </div>



                        </div>
                        <!-- -->
                    </div>
                    <?/*div class="static_data">
                        <h2>Инициативы</h2>
                        <div class="group">
                            <h3>Подано</h3>
                            <?
                            $initRunned = getUserInits([2, 3]);
                            $initScorePrice = getUserScorePrice(3);
                            $voteInitPrice = getUserScorePrice(2);
                            ?>
                            <div class="item">
                                <div class="el_data">
                                    <label>Всего</label>
                                    <input disabled class="el_input" type="text" value="<?=getUserInits([1, 2, 3])?>">
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Одобрено</label>
                                    <input disabled class="el_input" type="text" value="<?=$initRunned?>">

                                </div>
                            </div>

                            <div class="item">
                                <div class="el_data">
                                    <label>Начислено баллов</label>
                                    <input disabled class="el_input" type="text" value="<?=($initRunned * $initScorePrice)?>">

                                </div>
                            </div>


                            <h3>Голосований по инициативам</h3>
                            <div class="item">
                                <div class="el_data">
                                    <label>Идёт</label>
                                    <input disabled class="el_input" type="text" value="<?=getUserInits([6])?>">
                                    <a href="/initsiativy/?sf14=2"><button class="button icon"><span class="material-icons">visibility</span></button></a>
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Завершено</label>
                                    <input disabled class="el_input" type="text" value="<?=getUserInits([7])?>">

                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Начислено баллов</label>
                                    <input disabled class="el_input" type="text" value="<?=($initRunned * $initScorePrice)?>">

                                </div>
                            </div>

                            <h3 class="red">Ожидает одобрения</h3>
                            <div class="item">
                                <div class="el_data">
                                    <label>Инициатив</label>
                                    <input disabled class="el_input" type="text" value="<?=getUserInits([4, 5])?>">
                                    <a href="/initsiativy/?sf14=5"><button class="button icon"><span class="material-icons">visibility</span></button></a>
                                </div>
                            </div>
                        </div>
                    </div*/?>
                    <div class="static_data">
                        <h2>Голосования</h2>
                        <div class="group">
                            <h3>Создано</h3>
                            <div class="item">
                                <div class="el_data">
                                    <?
                                    $votesTotal = getUserInits([4, 5, 6, 7]);
                                    $votesRun = getUserInits([6]);
                                    $votesNotRun = $votesTotal - $votesRun;
                                    ?>
                                    <label>Всего</label>
                                    <input disabled class="el_input" type="text" value="<?=$votesTotal?>">
                                </div>
                            </div>

                            <div class="item">
                                <div class="el_data">
                                    <label>Не началось</label>
                                    <input disabled class="el_input" type="text" value="<?=$votesNotRun?>">
                                    <a href="/golosovanie/?sf14=5&sf4=<?=$_SESSION['user_index'].'-'.$_SESSION['user_id']?>">
                                        <button class="button icon"><span class="material-icons">visibility</span></button></a>
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Идёт</label>
                                    <input disabled class="el_input" type="text" value="<?=$votesRun?>">
                                    <a href="/golosovanie/?sf14=6&sf4=<?=$_SESSION['user_index'].'-'.$_SESSION['user_id']?>">
                                        <button class="button icon"><span class="material-icons">visibility</span></button></a>
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Начислено баллов</label>
                                    <input disabled class="el_input" type="text" value="0">

                                </div>
                            </div>
                            <h3>Участие в голосовании</h3>
                            <?
                            $votesResults = getUserVoteResults();
                            $votesPrice = getUserScorePrice(1);
                            ?>
                            <div class="item">
                                <div class="el_data">
                                    <label>Всего</label>
                                    <input disabled class="el_input" type="text" value="<?=$votesResults?>">

                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Проголосуйте</label>
                                    <input disabled class="el_input" type="text" value="<?=getUserInits([7])?>">
                                    <a href="/golosovanie/?sf14=6"><button class="button icon"><span class="material-icons">visibility</span></button></a>
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Проголосовано</label>
                                    <input disabled class="el_input" type="text" value="<?=($votesResults * $votesPrice)?>">
                                    <a href="/golosovanie/?sf14=6"><button class="button icon"><span class="material-icons">visibility</span></button></a>
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Начислено баллов</label>
                                    <input disabled class="el_input" type="text" value="<?=($votesResults * $votesPrice)?>">

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
                                    <?
                                    $votesTotal = getUserInits([4, 5, 6, 7]);
                                    $votesRun = getUserInits([6]);
                                    $votesNotRun = $votesTotal - $votesRun;
                                    ?>
                                    <label>Всего</label>
                                    <input disabled class="el_input" type="text" value="<?=$votesTotal?>">
                                </div>
                            </div>

                            <div class="item">
                                <div class="el_data">
                                    <label>Не началось</label>
                                    <input disabled class="el_input" type="text" value="<?=$votesNotRun?>">
                                    <a href="/meropriyatiya/?sf14=13&sf4=<?=$_SESSION['user_index'].'-'.$_SESSION['user_id']?>">
                                        <button class="button icon"><span class="material-icons">visibility</span></button></a>
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Идёт</label>
                                    <input disabled class="el_input" type="text" value="<?=$votesRun?>">
                                    <a href="/meropriyatiya/?sf14=14&sf4=<?=$_SESSION['user_index'].'-'.$_SESSION['user_id']?>"><button class="button icon"><span class="material-icons">visibility</span></button></a>
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Начислено баллов</label>
                                    <input disabled class="el_input" type="text" value="0">

                                </div>
                            </div>
                            <h3>Участие в мероприятиях</h3>
                            <div class="item">
                                <div class="el_data">
                                    <label>Всего</label>
                                    <input disabled class="el_input" type="text" value="<?=getUserInits([14])?>">
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Примите участие</label>
                                    <input disabled class="el_input" type="text" value="<?=getUserInits([15])?>">
                                    <a href="/meropriyatiya/?sf14=6"><button class="button icon"><span class="material-icons">visibility</span></button></a>
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Принято участие</label>
                                    <input disabled class="el_input" type="text" value="<?=getUserInits([15])?>">
                                    <a href="/meropriyatiya/?sf14=6"><button class="button icon"><span class="material-icons">visibility</span></button></a>
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Начислено баллов</label>
                                    <input disabled class="el_input" type="text" value="<?=getUserScore(5, [14]);?>">

                                </div>
                            </div>


                        </div>
                    </div>
                    <?/*div class="static_data">
                        <h2>Финансы</h2>
                        <div class="group">
                            <h3>Поступления</h3>
                            <div class="item">
                                <div class="el_data">
                                    <label>Всего р.</label>
                                    <input disabled class="el_input" type="text" value="15 328">
                                    <button class="button icon"><span class="material-icons">visibility</span></button>
                                </div>
                            </div>
                            <div class="item">
                                <div class="el_data">
                                    <label>Начислено баллов</label>
                                    <input disabled class="el_input" type="text" value="9">

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