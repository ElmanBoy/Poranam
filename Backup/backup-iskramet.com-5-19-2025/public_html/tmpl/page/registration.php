<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/tmpl/page/blocks/header.php';

$professions = getRegistry('proffesions');
$subjects = getRegistry('subjects');
?>
    <!-- Навигация для зарегистрированных. Разная в зависимости от прав -->
    <nav id="admin_menu">

    </nav>
    <!-- оснвоное поле -->
    <div class="content">
        <div class="wrap">
            <main>
                <?
                //if(isset($_GET['allow_reg'])){
                ?>
                <div class="box">
                    <h1>Регистрация</h1>
                    <span class="red">*</span> - обязательные поля
                </div>
                <div class="box">
                    <form class="ajaxFrm" id="registration" onsubmit="return false">
                        <div class="static_data">

                            <h2>Личные данные</h2>
                            <div class="group">

                                <?/*div class="item">
                                    <div class="el_data">
                                        <label for="metka_1y">Фамилия (не обязательно)</label>
                                        <input class="el_input" id="metka_1y" name="second_name" type="text">
                                    </div>
                                </div>

                                <div class="item">
                                    <div class="el_data">
                                        <label for="metka_1u">Имя (желательно) <span class="red">*</span></label>
                                        <input class="el_input" id="metka_1u" name="name" type="text" required>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="el_data">
                                        <label for="metka_1i">Отчество (не обязательно)</label>
                                        <input class="el_input" id="metka_1i" name="third_name" type="text">
                                    </div>
                                </div*/?>
                                <div class="item">
                                    <select required data-label="Профессия <span class='red'>*</span>" data-place="Выберите" name="profession">
                                        <?
                                        foreach ($professions as $id => $name){
                                            echo '<option value="'.$id.'">'.$name.'</option>'."\n";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class='item w_100'>
                                    <select multiple data-label='Темы/Проблемы' data-place='Выберите' name='theme[]'>
                                        <?= el_buildRegistryList('registryVote', '', false) ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="static_data">
                            <h2>Контактная информация</h2>
                            <div class="group">

                                <?/*div class="item">
                                    <div class="el_data">
                                        <label for="metka_1o">Телефон <span class="red">*</span></label>
                                        <input required class="el_input" id="metka_1o" name="phones" type="tel">
                                    </div>
                                </div*/?>
                                <div class="item">
                                    <div class="el_data">
                                        <label for="metka_1p">E-mail <span class="red">*</span></label>
                                        <input required class="el_input" id="metka_1p" name="email" type="email">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="static_data">

                            <h2>Региональная принадлежность</h2>
                            <div class="group">

                                <div class="item">
                                    <select required data-label="Субъект <span class='red'>*</span>" name="region" data-place="Выберите">
                                        <?
                                        foreach ($subjects as $id => $name){
                                            echo '<option value="'.$id.'">'.$name.'</option>'."\n";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="item">
                                    <select required data-label="Район / Округ <span class='red'>*</span>" name="district" data-place="Выберите">

                                    </select>
                                </div>
                                <div class="item">
                                    <div class="el_data">
                                        <label for="city">Населённый пункт <span class="red">*</span></label>
                                        <input required class="el_input" value="" name="city" id="city" type="text">
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="el_data">
                                        <label for="post_index">Индекс <span class="red">*</span></label>
                                        <input required class="el_input" name="post_index" value="" id="post_index" type="text">
                                    </div>
                                </div>
                                <?/*div class="item">
                                    <div class="el_data">
                                        <label for="street">Улица (не обязательно)</label>
                                        <input class="el_input" value="" name="street" id="street" type="text">
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="el_data">
                                        <label for="h_number">Номер дома (не обязательно)</label>
                                        <input class="el_input" name="build_number" value="" id="h_number" type="text">
                                    </div>
                                </div*/?>
                            </div>
                        </div>
                        <div class="static_data">
                            <h2>Безопасность</h2>
                            <div class="group">
                                <h3>Авторизация</h3>
                                <div class="item channel">
                                    <div class="el_data">
                                        <label for="metka_1f">Логин <span class="red">*</span></label>
                                        <input class="el_input" id="metka_1f" type="text" value="" required name="login">
                                    </div>
                                    <div class="el_data">
                                        <label for="metka_1g">Пароль <span class="red">*</span></label>
                                        <input class="el_input" id="metka_1g" name="password" type="password" value="" required>
                                    </div>
                                    <div class="button icon text add" id="gen_pass"><span class="material-icons">gpp_good</span>Сгенерировать</div>
                                </div>

                                <div class="item channel" style="width:100%">
                                    <div class="el_data">
                                        <label for="metka_1r">ID пригласившего (желательно)</label>
                                        <input class="el_input" id="metka_1r" type="text" name="referrer" value="<?=$_SESSION['referrer']?>">
                                    </div>
                                    <span class="note"> - требуется для развития движения</span>
                                </div>
                            </div>
                            <div class="agree">
                                <div class="custom_checkbox">
                                    <label class="container">Я ознакомлен с
                                        <a href="/politika-konfidentsialnosti/" target="_blank">Пользовательским соглашением,
                                        политикой конфиденциальности и политикой использования файлов Cookie</a>
                                        <input type="checkbox" name="agree" value="y" required><span class="checkmark"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="agree">
                                <div class="custom_checkbox">
                                    <label class="container">Даю согласие на обработку своих персональных данных
                                        <input type="checkbox" name="agree2" value="y" required><span class="checkmark"></span>
                                    </label>
                                </div>
                            </div>
                            <button class="button icon text" disabled type="submit"><span class="material-icons">check</span>Отправить</button>
                        </div>
                    </form>
                </div>
                    <?/*
                }else{
                    */?><!--
                <div class="box">Регистрация временно закрыта</div>
                    --><?/*
                }*/
                    ?>
            </main>
            <!-- <div class="donate">
            <div class="wrap">
                <div class="box">Donate section</div>
            </div>

        </div> -->

        </div>
    </div>
<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/tmpl/page/blocks/footer.php';
?>