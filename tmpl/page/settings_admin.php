<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/tmpl/page/blocks/header.php';
?>
    <nav id="admin_menu">
        <div class="wrap">
            <?
            include $_SERVER['DOCUMENT_ROOT'] . '/tmpl/page/blocks/admin_menu.php';
            ?>
        </div>
    </nav>
    <!-- оснвоное поле -->
    <div class="content">
        <div class="wrap">

            <main>
                <div class="box">
                    <h1>Настройки</h1>
                </div>
                <div class="box">
                    <div class="static_data">
                        <form class="ajaxFrm noreset" id="setScores" onsubmit="return false">
                            <h2>Баллы</h2>
                            <div class="group">

                                <h3>За действия</h3>
                                <?
                                $scores = el_dbselect("SELECT * FROM catalog_scores_data WHERE field2 = 'За действия' 
                                AND active = 1", 0, $scores, 'result', true);
                                $rs = el_dbfetch($scores);
                                do{
                                    echo '<div class="item">
                                    <div class="el_data input_number">
                                        <label for="metkad'.$rs['id'].'">'.$rs['field1'].'</label>
                                        <div class="number-minus button icon" onclick="this.nextElementSibling.stepDown(); 
                                        this.nextElementSibling.onchange();"><span class="material-icons">keyboard_arrow_down</span></div>
                                        <input class="el_input" id="metka'.$rs['id'].'" name="score'.$rs['id'].'" value="'.$rs['field3'].'" type="number" min="0">
                                        <div class="number-plus button icon" onclick="this.previousElementSibling.stepUp(); 
                                        this.previousElementSibling.onchange();"><span class="material-icons">keyboard_arrow_up</span></div>
                                    </div>
                                </div>'."\n";
                                }while($rs = el_dbfetch($scores));
                                ?>

                                <h3>За взносы</h3>
                                <?
                                $scores = el_dbselect("SELECT * FROM catalog_scores_data WHERE field2 = 'За взносы' 
                                AND active = 1", 0, $scores, 'result', true);
                                $rs = el_dbfetch($scores);
                                $scArr = array();
                                do{
                                    echo '<div class="item">
                                    <div class="el_data input_number">
                                        <label for="metkav'.$rs['id'].'">'.$rs['field1'].'</label>
                                        <div class="number-minus button icon" onclick="this.nextElementSibling.stepDown(); 
                                        this.nextElementSibling.onchange();"><span class="material-icons">keyboard_arrow_down</span></div>
                                        <input class="el_input" id="metka'.$rs['id'].'" name="score'.$rs['id'].'" value="'.$rs['field3'].'" type="number" min="0">
                                        <div class="number-plus button icon" onclick="this.previousElementSibling.stepUp(); 
                                        this.previousElementSibling.onchange();"><span class="material-icons">keyboard_arrow_up</span></div>
                                    </div>
                                </div>'."\n";
                                }while($rs = el_dbfetch($scores));
                                ?>

                            </div>
                            <button class="button icon text"><span class="material-icons">save</span>Сохранить</button>
                        </form>
                    </div>

                    <div class="static_data">
                        <form class="ajaxFrm noreset" id="setThemes" onsubmit="return false">
                            <h2>Темы для голосования</h2>
                            <div class="group">
                                <h3>Список</h3>
                                <?
                                $themes = getRegistry('registryVote');
                                if(count($themes) > 0){
                                    $themesCount = 0;
                                    foreach($themes as $id => $theme){
                                        $themesCount ++;
                                        echo '
                                        <div class="item">
                                            <div class="el_data">
                                                <label for="metka_t'.$id.'">Название</label>
                                                <input class="el_input" id="metka_t'.$id.'" type="text" name="name'.$id.'" value="'.$theme.'">
                                                <div class="button icon add delButton" data-id="'.$id.'">
                                                <span class="material-icons">remove_circle_outline</span></div>
                                                </div>
                                        </div>';

                                    }
                                }
                                ?>
                                <div class="item">
                                    <div class="el_data">
                                        <label for="metka_newtype">Название</label>
                                        <input class="el_input" id="metka_newtype" name="new_theme" type="text" value="">
                                        <button class="button icon add"><span class="material-icons">add_circle_outline</span></button>
                                    </div>
                                </div>
                            </div>
                            <button class="button icon text"><span class="material-icons">save</span>Сохранить</button>
                        </form>
                    </div>
                    <div class="static_data">
                        <form class="ajaxFrm noreset" id="setProfessions" onsubmit="return false">
                            <h2>Профессии</h2>
                            <div class="group">
                                <h3>Список</h3>
                                <?
                                $themes = getRegistry('proffesions');
                                if(count($themes) > 0){
                                    $themesCount = 0;
                                    foreach($themes as $id => $theme){
                                        $themesCount ++;
                                        echo '
                                        <div class="item">
                                            <div class="el_data">
                                                <label for="metka_p'.$id.'">Название</label>
                                                <input class="el_input" id="metka_p'.$id.'" type="text" name="name'.$id.'" value="'.$theme.'">
                                                <div class="button icon add delButton" data-id="'.$id.'">
                                                <span class="material-icons">remove_circle_outline</span></div>
                                                </div>
                                        </div>';

                                    }
                                }
                                ?>
                                <div class="item">
                                    <div class="el_data">
                                        <label for="metka_newprof">Название</label>
                                        <input class="el_input" id="metka_newprof" type="text" name="new_profession" value="">
                                        <button class="button icon add"><span class="material-icons">add_circle_outline</span></button>
                                    </div>
                                </div>

                            </div>
                            <button class="button icon text"><span class="material-icons">save</span>Сохранить</button>
                        </form>
                    </div>

                    <div class="static_data">
                        <form>
                            <h2>Официальные информационные каналы</h2>
                            <div class="group">
                                <h3>Список</h3>
                                <?
                                $channels = el_dbselect("SELECT * FROM catalog_mainchannels_data WHERE active = 1",
                                    0, $channels, 'result', true);
                                $rc = el_dbfetch($channels);
                                do{
                                    echo '<div class="item channel w_100">
                                    <div class="el_data w_50">
                                        <label for="metkamcn'.$rc['id'].'">Название</label>
                                        <input class="el_input" id="metkamcn'.$rc['id'].'" type="text" value="'.htmlspecialchars($rc['field1']).'">
                                    </div>
                                    <div class="el_data">
                                        <label for="metkamcl'.$rc['id'].'">Ссылка</label>
                                        <input class="el_input" id="metkamcl'.$rc['id'].'" type="text" value="'.$rc['field2'].'">
                                    </div>
                                    <div class="button icon add"><span class="material-icons">remove_circle_outline</span></div>
                                </div>'."\n";
                                }while($rc = el_dbfetch($channels));
                                ?>
                                <div class="item channel w_100">
                                    <div class="el_data w_50">
                                        <label for="metka_newmc">Название</label>
                                        <input class="el_input" id="metka_newmc" type="text" value="">
                                    </div>
                                    <div class="el_data">
                                        <label for="metka_newmcl">Ссылка</label>
                                        <input class="el_input" id="metka_newmcl" type="text" value="">
                                    </div>
                                    <div class="button icon add"><span class="material-icons">add_circle_outline</span></div>
                                </div>
                            </div>
                            <button class="button icon text"><span class="material-icons">save</span>Сохранить</button>
                        </form>
                    </div>
                    <div class="static_data">
                        <form>
                            <h2>Информационные каналы по регионам</h2>
                            <div class="group">
                                <h3>Список</h3>
                                <?
                                $subjects = getRegistry('subjects');
                                $channels = el_dbselect("SELECT * FROM catalog_regionchannels_data WHERE active = 1",
                                    0, $channels, 'result', true);
                                $rc = el_dbfetch($channels);
                                do{
                                    echo '<div class="item channel w_100">
                                    <div class="el_data w_50">
                                        <label for="metkarcn'.$rc['id'].'">Название</label>
                                        <input class="el_input" id="metkarcn'.$rc['id'].'" type="text" value="'.htmlspecialchars($rc['field1']).'">
                                    </div>
                                    <div class="el_data">
                                        <label for="metkarcl'.$rc['id'].'">Ссылка</label>
                                        <input class="el_input" id="metkarcl'.$rc['id'].'" type="text" value="'.$rc['field2'].'">
                                    </div>
                                    <select multiple data-label="Субъект">';
                                    foreach($subjects as $id => $subject){
                                        $sel = (in_array($id, explode(',', $rc['field3']))) ? ' selected' : '';
                                        echo '<option value="'.$id.'"'.$sel.'>'.$subject.'</option>';
                                    }
                                    echo '</select>
                                    <div class="button icon add"><span class="material-icons">remove_circle_outline</span></div>
                                </div>'."\n";
                                }while($rc = el_dbfetch($channels));
                                ?>

                                <div class="item channel w_100">
                                    <div class="el_data w_50">
                                        <label for="metka_newrcn">Название</label>
                                        <input class="el_input" id="metka_newrcn" type="text" value="">
                                    </div>
                                    <div class="el_data">
                                        <label for="metka_newrcl">Ссылка</label>
                                        <input class="el_input" id="metka_newrcl" type="text" value="">
                                    </div>

                                    <select multiple data-label="Субъект">
                                        <?
                                        foreach($subjects as $id => $subject){
                                            echo '<option value="'.$id.'">'.$subject.'</option>';
                                        }
                                        ?>
                                    </select>
                                    <div class="button icon add"><span class="material-icons">add_circle_outline</span></div>
                                </div>
                            </div>
                            <button class="button icon text"><span class="material-icons">save</span>Сохранить</button>
                        </form>
                    </div>

                    <div class="static_data">
                        <form>
                            <h2>Профессиональные каналы</h2>
                            <div class="group">
                                <h3>Список</h3>
                                <?
                                $professions = getRegistry('proffesions');
                                $channels = el_dbselect("SELECT * FROM catalog_profchannels_data WHERE active = 1",
                                    0, $channels, 'result', true);
                                $rc = el_dbfetch($channels);
                                do{
                                    echo '<div class="item channel w_100">
                                    <div class="el_data w_50">
                                        <label for="metkapcn'.$rc['id'].'">Название</label>
                                        <input class="el_input" id="metkapcn'.$rc['id'].'" type="text" value="'.htmlspecialchars($rc['field1']).'">
                                    </div>
                                    <div class="el_data">
                                        <label for="metkapcl'.$rc['id'].'">Ссылка</label>
                                        <input class="el_input" id="metkapcl'.$rc['id'].'" type="text" value="'.$rc['field2'].'">
                                    </div>
                                    <select multiple data-label="Профессия">';
                                    foreach($professions as $id => $profession){
                                        $sel = (in_array($id, explode(',', $rc['field3']))) ? ' selected' : '';
                                        echo '<option value="'.$id.'"'.$sel.'>'.$profession.'</option>';
                                    }
                                    echo '</select>
                                    <div class="button icon add"><span class="material-icons">remove_circle_outline</span></div>
                                </div>'."\n";
                                }while($rc = el_dbfetch($channels));
                                ?>
                                <div class="item channel w_100">
                                    <div class="el_data w_50">
                                        <label for="metka_newpcn">Название</label>
                                        <input class="el_input" id="metka_newpcn" type="text" value="">
                                    </div>
                                    <div class="el_data">
                                        <label for="metka_newpcl">Ссылка</label>
                                        <input class="el_input" id="metka_newpcl" type="text" value="">
                                    </div>
                                    <select multiple data-label="Профессия">
                                        <?
                                        foreach($professions as $id => $profession){
                                            echo '<option value="'.$id.'">'.$profession.'</option>';
                                        }
                                        ?>
                                    </select>
                                    <div class="button icon add"><span class="material-icons">add_circle_outline</span></div>
                                </div>




                            </div>
                            <button class="button icon text"><span class="material-icons">save</span>Сохранить</button>
                        </form>
                    </div>

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