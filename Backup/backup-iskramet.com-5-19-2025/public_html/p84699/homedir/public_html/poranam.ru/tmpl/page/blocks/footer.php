<footer>
    <div class="wrap">
        <div class="box">
            <div class="logo">
                <img src="/images/logo_white.svg" />
            </div>
        </div>
        <div class="donate">
            <div class="wrap">
                <div class="box">
                    <h1>Помочь движению</h1>
                </div>
                <div class="box">
                    <div class="payment"><a href="#"><img src="/images/yoomoney.svg" /></a></div>
                </div>
                <div class="box">
                    <div class="payment"><a href="#"><img src="/images/qiwi.svg" /></a></div>
                </div>
                <div class="box">
                    <div class="payment"><a href="#"><img src="/images/webmoney.svg" /></a></div>
                </div>
                <div class="box">
                    <div class="payment"><a href="#"><img src="/images/paypal.svg" /></a></div>
                </div>
            </div>

        </div>
        <!--            -->

        <!--            -->
    </div>
</footer>
<!-- Modals section -->

<!-- Универсальный wrapper для Pop-Up-ов -->
<div id="pop_up_filter_vote" class="wrap_pop_up">
    <div class="pop_up">
        <div class="title">
            <h2>Фильтр</h2>
            <div class="close" onclick="pop_up_filter_vote_close(); return false"><span class="material-icons">highlight_off</span></div>
        </div>
        <section>
            <form>
                <div class="group">
                    <div class="item">
                        <div class="el_data">
                            <label for="metka_1">ID Организатора</label>
                            <input class="el_input" id="metka_1" type="text">
                        </div>

                    </div>
                </div>
                <h3>Участники</h3>
                <div class="group">
                    <div class="item">
                        <div class="el_data">
                            <label for="metka_1">Индекс</label>
                            <input class="el_input" id="metka_1" type="text">
                        </div>

                    </div>
                    <div class="item">
                        <div class=" el_data">
                            <label for="metka_1">Регион</label>
                            <div class="el_select" id="metka_1" type="text">
                                <div class="holder">Мульти Выберите</div>
                            </div>
                        </div>
                    </div>

                    <div class="item">
                        <div class=" el_data">
                            <label for="metka_1">Город</label>
                            <div class="el_select" id="metka_1" type="text">
                                <div class="holder">Мульти Выберите</div>
                            </div>
                        </div>
                    </div>

                    <div class="item">
                        <div class=" el_data">
                            <label for="metka_1">Район город</label>
                            <div class="el_select" id="metka_1" type="text">
                                <div class="holder">Мульти Выберите</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="group">
                    <div class="item">
                        <div class="el_data">
                            <label for="metka_1">Профессия</label>
                            <div class="el_select" id="metka_1" type="text">
                                <div class="holder">Мульти Выберите</div>
                            </div>
                        </div>
                    </div>
                </div>
                <h3>Параметры</h3>
                <div class="group">
                    <div class="item">
                        <div class="el_data">
                            <label for="metka_1">Тема</label>
                            <div class="el_select" id="metka_1" type="text">
                                <div class="holder">Мульти Выберите</div>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="el_data">
                            <label for="metka_1">Статус</label>
                            <div class="el_select" id="metka_1" type="text">
                                <div class="holder">Выберите</div>
                            </div>
                        </div>
                    </div>
                </div>
                <h3>Время проведения</h3>
                <div class="group">
                    <div class="item">
                        <div class="el_data">
                            <label for="metka_1">Начало</label>
                            <input class="el_input" id="metka_1" type="date" value="2021-06-08">


                        </div>
                    </div>
                    <div class="item">
                        <div class="el_data">
                            <label for="metka_1">Окончание</label>
                            <input class="el_input" id="metka_1" type="date">


                        </div>
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
<div id="pop_up_vote" class="wrap_pop_up">
    <div class="pop_up">
        <div class="title">
            <h2>Голосование</h2>
            <div class="close" onclick="pop_up_vote_close(); return false"><span class="material-icons">highlight_off</span></div>
        </div>
        <section>
            <form>
                <h3>Тема</h3>
                <div class="group">
                    <div class="item">
                        <div class="el_data w_50">
                            <label for="metka_1">Тема голосования</label>
                            <div class="el_select" id="metka_1" value="Центральный" type="text">
                                <div class="holder">Выберите</div>
                            </div>
                        </div>
                    </div>
                </div>
                <h3>Вопрос</h3>
                <div class="group">

                    <div class="item w_100">
                        <div class="el_data w_100">
                            <label>Текст вопроса</label>
                            <textarea class="el_textarea" name="demo-01" id="demo-01"></textarea>
                        </div>

                    </div>
                </div>


                <h3>Варинаты ответов</h3>
                <div class="group">
                    <div class="item w_100">
                        <div class="el_data w_100">
                            <label for="metka_1">Ответ 1</label>
                            <input class="el_input" id="metka_1" type="text">
                        </div>
                    </div>
                    <div class="item w_100">
                        <div class="el_data w_100">
                            <label for="metka_1">Ответ 2</label>
                            <input class="el_input" id="metka_1" type="text">
                            <div class="button icon add"><span class="material-icons"><span class="material-icons">
                                            remove_circle_outline
                                        </span></span></div>
                        </div>
                    </div>
                    <div class="item w_100">
                        <div class="el_data w_100">
                            <label for="metka_1">Ответ 3</label>
                            <input class="el_input" id="metka_1" type="text">
                            <div class="button icon add"><span class="material-icons">add_circle_outline</span></div>
                        </div>
                    </div>
                </div>

                <h3>Участники</h3>
                <div class="group">
                    <div class="item">
                        <div class=" el_data w_100">
                            <label for="metka_1">Регионы</label>
                            <div class="el_select" id="metka_1" type="text">
                                <div class="holder">Мульти Выберите</div>
                            </div>
                        </div>
                    </div>

                    <div class="item">
                        <div class=" el_data">
                            <label for="metka_1">Города</label>
                            <div class="el_select" id="metka_1" type="text">
                                <div class="holder">Мульти Выберите</div>
                            </div>
                        </div>
                    </div>

                    <div class="item">
                        <div class=" el_data">
                            <label for="metka_1">Районы города</label>
                            <div class="el_select" id="metka_1" type="text">
                                <div class="holder">Мульти Выберите</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="group">
                    <div class="item w_100">
                        <div class="el_data">
                            <label for="metka_1">Профессия</label>
                            <div class="el_select" id="metka_1" type="text">
                                <div class="holder">Мульти Выберите</div>
                            </div>
                        </div>
                    </div>
                </div>

                <h3>Время проведения</h3>
                <div class="group">
                    <div class="item">
                        <div class="el_data">
                            <label for="metka_1">Начало</label>
                            <input class="el_input" id="metka_1" type="date" value="2021-06-08">


                        </div>
                    </div>
                    <div class="item">
                        <div class="el_data">
                            <label for="metka_1">Окончание</label>
                            <input class="el_input" id="metka_1" type="date">


                        </div>
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
</div>


<!-- Meetings end -->
<script> /*    !!!!  Show hidden cells !!! Change icons don`t working :-(       */
    $(document).ready(function () {
        $('.more').click(function () {
            $(this).closest("tr").next('.hidden').slideToggle(100, function () {
                let $icon = $(this).prev('tr').find('.more .material-icons');
                $icon.text($(this).is(':hidden') ? 'unfold_more' : 'unfold_less');
            });
            return false;
        });
    });
</script>
<script src="/js/tools.js?ver=<?=el_genpass()?>"></script>
<script src="/js/jquery.maskedinput.js"></script>
<script src="/js/flatpickr.js"></script>
<script src="/js/flatpickr/l10n/ru.js"></script>
<script src="/js/app.js?ver=<?=el_genpass()?>"></script>
<script src="/js/call_popups.js?ver=<?=el_genpass()?>"></script>
<script src="/js/notify.js"></script>
</body>

</html>