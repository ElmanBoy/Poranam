<nav id="admin_menu">
    <?
    include $_SERVER['DOCUMENT_ROOT'].'/tmpl/page/blocks/admin_menu.php';
    $init = '';
    $perPage = 15;
    $init = el_dbselect("SELECT * FROM catalog_init_data ORDER BY id DESC",
        $perPage, $init, 'result', true);
    $tInit = el_dbnumrows($init);

    ?>
</nav>

<div class="content">
    <div class="wrap">

        <main>
            <div class="box">
                <h1>Инициативы</h1>
            </div>
            <div class="text"><? el_text('el_pageprint', 'text'); ?></div>
            <div class="box">
                <div class="control">
                    <button class="text icon" id="button_initiative_filter"><span class="material-icons">filter_alt</span>Фильтр</button>
                    <button class="text icon" id="button_initiative_new"><span class="material-icons">add_circle</span>Создать</button>
                    <?
                    if($_SESSION['user_level'] == 11){
                    ?>
                    <button class="text icon group_action" id="button_initiative_start" disabled><span class="material-icons">play_arrow</span>Запустить</button>
                    <button class="text icon group_action" id="button_initiative_stop" disabled onclick="pop_up_warning(); return false"><span class="material-icons">stop</span>Завершить</button>
                    <?
                    }
                    ?>
                    <button class="text icon group_action" id="button_initiative_remove" disabled onclick="pop_up_warning(); return false"><span class="material-icons">delete_forever</span>Удалить</button>

                </div>
            </div>
            <div class="box">
                <div class="scroll_wrap">
                    <?
                    if($tInit > 0){
                    ?>
                    <table class="table_data">
                        <thead>
                        <tr>
                            <?
                            if($_SESSION['user_level'] == 11){
                            ?>
                            <th>
                                <div class="custom_checkbox">
                                    <label class="container"><input type="checkbox" id="check_all"><span class="checkmark"></span></label>
                                </div>
                            </th>
                            <?
                            }
                            ?>
                            <th>Организатор</th>
                            <th>Дата</th>
                            <th>
                                <div class="button icon sort"><span class="material-icons">filter_list</span></div>Тема
                            </th>
                            <th>Вопрос</th>

                            <th>
                                <div class="button icon sort"><span class="material-icons">filter_list</span></div>Статус
                            </th>
                            <th>Ответ</th>
                        </tr>
                        </thead>

                        <tbody>
                        <!-- row -->
                        <? el_module('el_pagemodule', '');?>
                        <?/*tr>
                            <td>
                                <div class="custom_checkbox">
                                    <label class="container">
                                        <input type="checkbox"><span class="checkmark"></span>
                                    </label>
                                </div>
                            </td>



                            <td><a href="#" onclick="pop_up_profile(); return false">ID 12543524365</a>
                                <button class="button icon text more"><span class="material-icons">unfold_more</span>Участники</button>
                            </td>

                            <td></td>
                            <td>Экономика</td>
                            <td>
                                <p>Голосование завершено.</p>
                                <p>Можем скачать ведомость.</p>
                                <p>Можем видеть за какой вариант отдали свой голос, но не можем изменить.</p>
                                <p>Результаты сортируются автоматически от большего к меньшему.</p>
                                <p>Может создать голосование на основе инициативы</p>


                            </td>
                            <td>
                                <button class="icon" title="Скачать ведомость"><span class="material-icons">get_app</span></button>
                                <button class="icon red" title="В голосование" onclick="pop_up_vote(); return false">
                                    <span class="material-icons">thumb_up_alt</span></button>
                            </td>
                            <td>
                                <div class="description">
                                    <div class="votes">
                                        <form>
                                            <div class="vote">
                                                <div class="custom_checkbox">
                                                    <label class="container">
                                                        <input type="radio" disabled name="proba"><span class="checkmark radio"></span> </label>
                                                </div>
                                                <div class="answer">Да
                                                    <div class="bar" style="width: 96%;"><span>96</span></div>
                                                </div>
                                            </div>
                                            <div class="vote">
                                                <div class="custom_checkbox">
                                                    <label class="container">
                                                        <input type="radio" disabled checked name="proba"><span class="checkmark radio"></span> </label>
                                                </div>
                                                <div class="answer">Нет
                                                    <div class="bar" style="width: 75%;"><span>75</span></div>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                    <div class="interes">
                                        <div class="svg_wrap">
                                            <svg viewBox="0 0 32 32">
                                                <circle class='svg_background'></circle>
                                                <circle class='svg_calc' stroke-dasharray="16 100"></circle>
                                            </svg>
                                            <div class="svg_value">16</div>
                                        </div>
                                    </div>
                                </div>


                            </td>
                        </tr>
                        <tr class="hidden">

                            <td colspan="7">
                                <div class="description">
                                    <div class="title">Регион:</div>
                                    <div class="value">Центральный, Уральский, Магадан</div>
                                </div>
                                <div class="description">
                                    <div class="title">Город:</div>
                                    <div class="value">Брянск, Петропавловск, Магадан, Центральный, Уральский, Магадан, Брянск, Петропавловск, Магадан, Центральный, Уральский, Магадан, Брянск, Петропавловск, Магадан, Центральный, Уральский, Магадан</div>
                                </div>
                                <div class="description">
                                    <div class="title">Район:</div>
                                    <div class="value">Кутузовский, Западный, Ленинский</div>
                                </div>
                                <div class="description">
                                    <div class="title">Профессия:</div>
                                    <div class="value">Педагогика, Политика, IT, Право</div>
                                </div>
                            </td>
                        </tr>
                        <!-- row -->
                        <tr>
                            <td>
                                <div class="custom_checkbox">
                                    <label class="container">
                                        <input type="checkbox"><span class="checkmark"></span>
                                    </label>
                                </div>
                            </td>


                            <td><a href="#" onclick="pop_up_profile(); return false">ID 12543524365</a>
                                <button class="button text icon more"><span class="material-icons">unfold_more</span>Участники</button>
                            </td>

                            <td>16.09.21</td>
                            <td>Гражданское право</td>
                            <td>
                                <p>Голосование идёт.</p>
                                <p>До окончания голосования можем менять голос сколько угодно раз.</p>
                                <p>Админ может завершить голосование или оно автоматически закончится в установленную дату.</p>
                            </td>
                            <td><button onclick="pop_up_warning(); return false" class="button icon" title="Завершить">
                                    <span class="material-icons">stop</span></button>

                            </td>
                            <td>
                                <div class="description">
                                    <div class="votes">
                                        <form>
                                            <div class="vote">
                                                <div class="custom_checkbox">
                                                    <label class="container">
                                                        <input type="radio" name="proba"><span class="checkmark radio"></span> </label>
                                                </div>
                                                <div class="answer">Нет
                                                    <div class="bar" style="width: 25%;"><span>25</span></div>
                                                </div>
                                            </div>
                                            <div class="vote">
                                                <div class="custom_checkbox">
                                                    <label class="container">
                                                        <input type="radio" name="proba"><span class="checkmark radio"></span> </label>
                                                </div>
                                                <div class="answer">Да
                                                    <div class="bar" style="width: 25%;"><span>25</span></div>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                    <div class="interes">
                                        <div class="svg_wrap">
                                            <svg viewBox="0 0 32 32">
                                                <circle class='svg_background'></circle>
                                                <circle class='svg_calc' stroke-dasharray="25 100"></circle>
                                            </svg>
                                            <div class="svg_value">25</div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="hidden">

                            <td colspan="7">
                                <div class="description">
                                    <div class="title">Регион:</div>
                                    <div class="value">Центральный, Уральский, Магадан</div>
                                </div>
                                <div class="description">
                                    <div class="title">Город:</div>
                                    <div class="value">Брянск, Петропавловск, Магадан, Центральный, Уральский, Магадан</div>
                                </div>
                                <div class="description">
                                    <div class="title">Район:</div>
                                    <div class="value">Кутузовский, Западный, Ленинский</div>
                                </div>
                                <div class="description">
                                    <div class="title">Профессия:</div>
                                    <div class="value">Педагогика, Политика, IT, Право</div>
                                </div>
                            </td>
                        </tr>
                        <!-- row -->
                        <?

                        ?>
                        <tr>
                            <td>
                                <div class="custom_checkbox">
                                    <label class="container">
                                        <input type="checkbox"><span class="checkmark"></span>
                                    </label>
                                </div>
                            </td>


                            <td><a href="#" onclick="pop_up_profile(); return false" >ID 12543524365</a>
                                <button class="button text icon more"><span class="material-icons">unfold_more</span>Участники</button>
                            </td>

                            <td>16.09.21</td>
                            <td>Гражданское право</td>
                            <td>
                                <p>Голосование ещё не началось, это черновик.</p>
                                <p>Можно отредактировать или запустить голосование по инициативе.</p>
                            </td>
                            <td><button  class="button icon red edit_init" data-id="3" title="редактировать">
                                    <span class="material-icons">edit</span></button>
                                <button onclick="pop_up_warning(); return false"  class="button icon" title="Запустить">
                                    <span class="material-icons">play_arrow</span></button>

                            </td>
                            <td>
                                <div class="description">
                                    <div class="votes">
                                        <form>
                                            <div class="vote">
                                                <div class="custom_checkbox">
                                                    <label class="container">
                                                        <input type="radio" disabled name="proba"><span class="checkmark radio"></span> </label>
                                                </div>
                                                <div class="answer">Да
                                                    <div class="bar" style="width: 0%;"><span>0</span></div>
                                                </div>
                                            </div>
                                            <div class="vote">
                                                <div class="custom_checkbox">
                                                    <label class="container">
                                                        <input type="radio" disabled ame="proba"><span class="checkmark radio"></span> </label>
                                                </div>
                                                <div class="answer">Нет
                                                    <div class="bar" style="width: 0%;"><span>0</span></div>
                                                </div>
                                            </div>


                                        </form>
                                    </div>
                                    <div class="interes">
                                        <div class="svg_wrap">
                                            <svg viewBox="0 0 32 32">
                                                <circle class='svg_background'></circle>
                                                <circle class='svg_calc' stroke-dasharray="0 100"></circle>
                                            </svg>
                                            <div class="svg_value">0</div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="hidden">

                            <td colspan="7">
                                <div class="description">
                                    <div class="title">Регион:</div>
                                    <div class="value">Центральный, Уральский, Магадан</div>
                                </div>
                                <div class="description">
                                    <div class="title">Город:</div>
                                    <div class="value">Брянск, Петропавловск, Магадан, Центральный, Уральский, Магадан</div>
                                </div>
                                <div class="description">
                                    <div class="title">Район:</div>
                                    <div class="value">Кутузовский, Западный, Ленинский</div>
                                </div>
                                <div class="description">
                                    <div class="title">Профессия:</div>
                                    <div class="value">Педагогика, Политика, IT, Право</div>
                                </div>
                            </td>
                        </tr*/?>


                        </tbody>
                    </table>
                    <?
                    }else{
                        echo 'Пока нет ни одной инициативы.';
                    }
                    ?>
                </div>
                <?/*div class="pagination">
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