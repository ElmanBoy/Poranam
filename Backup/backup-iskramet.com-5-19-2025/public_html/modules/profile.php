<?php
if(intval($_SESSION['user_id']) > 0){

$rangs = getRegistry('userstatus');

$us = el_dbselect("SELECT * FROM catalog_users_data WHERE id = ".intval($_SESSION['user_id']),
     0, $us, 'row', true);

$curator = el_dbselect("SELECT user_id, field11 FROM catalog_users_data WHERE id = ".intval($us['field16']),
  0, $curator, 'row', true);

if(strlen(trim($us['field23'])) > 0){
    $sin = 'https://'.$_SERVER['SERVER_NAME'].'/?invite='.$us['field23'];
}else{
    $sin = 'https://'.$_SERVER['SERVER_NAME'].'/?invite='.el_genSinonim($_SESSION['user_id']);
}
?>
<main>
    <div class="box">
        <h1>Настройки ID <?=$us['user_id']?></h1>
    </div>
    <div class="box">
        <form class="ajaxFrm" id="saveProfile">
            <div class="static_data">

                <h2>Данные профиля</h2>
                <div class="group">

                    <div class="item">
                        <div class="el_data input_number">
                            <label for="metka_1">Статус</label>
                            <input disabled class="el_input"
                                   value="<?=($us['active'] == 1) ? 'Активный' : 'Не активный'?>" type="text">
                        </div>
                    </div>
                    <div class="item">
                        <div class="el_data input_number">
                            <label for="metka_1">Ранг</label>
                            <input disabled class="el_input" value="<?=$rangs[$us['field6']]?>" type="text">
                        </div>
                    </div>
                    <?/*div class="item">
                        <div class="el_data input_number">
                            <label for="metka_1">Полномочия</label>
                            <input disabled class="el_input" value="Нет" type="text">
                        </div>
                    </div*/?>
                    <div class="item">
                        <div class="el_data input_number">
                            <label for="metka_1">Куратор пользователя</label>
                            <input disabled class="el_input"
                                   value="<?=(intval($us['field16']) > 0) ? $curator['user_id'] : 'не указан'?>"
                                   type="text">
                        </div>
                    </div>

                </div>
            </div>
            <div class="static_data">

                <h2>Личные данные</h2>
                <div class="group">
                    <?
                    $fio = explode(" ", $us['field1']);
                    ?>
                    <div class="item">
                        <div class="el_data input_number">
                            <label for="metka_1">Фамилия</label>
                            <input class="el_input" name="surname" type="text" value="<?=$fio[0]?>">
                        </div>
                    </div>

                    <div class="item">
                        <div class="el_data input_number">
                            <label for="metka_1">Имя</label>
                            <input class="el_input" name="firstname" type="text" value="<?=$fio[1]?>">
                        </div>
                    </div>
                    <div class="item">
                        <div class="el_data input_number">
                            <label for="metka_1">Отчество</label>
                            <input class="el_input" name="secondname" type="text" value="<?=$fio[2]?>">
                        </div>
                    </div>
                    <div class="item">
                        <select required data-label="Профессия" name="profession" data-place="Выберите">
                            <?
                            echo el_buildRegistryList('proffesions', $us['field7'])
                            ?>
                        </select>
                    </div>
                    <div class='item w_100'>
                        <?
                        $themes = $us['field26'];
                        if(is_array($us['field26'])){
                            $themes = implode(',', $us['field26']);
                        }
                        ?>
                        <select multiple data-label='Темы/Проблемы' data-place='Выберите' name='theme[]'>
                            <?= el_buildRegistryList('registryVote', $themes, false) ?>
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
                            <input required class="el_input" name="phones" type="text" value="<?=$us['field5']?>">
                        </div>
                    </div>
                    <div class="item">
                        <div class="el_data input_number">
                            <label for="metka_1">E-mail</label>
                            <input required class="el_input" type="email" name="email" value="<?=$us['field2']?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="static_data">

                <h2>Региональная принадлежность</h2>
                <div class="group">

                    <div class="item">
                        <select required data-label="Субъект" data-place="Выберите" name="region">
                            <?
                            echo el_buildRegistryList('subjects', $us['field8'])
                            ?>
                        </select>
                    </div>
                    <div class="item">
                        <select required data-label="Район / Округ" data-place="Выберите" name="district" id="district">
                            <?
                            echo el_buildRegistryList('regions', $us['field9'])
                            ?>
                        </select>
                    </div>
                    <div class="item">
                        <div class="el_data">
                            <label for="post_index">Населённый пункт</label>
                            <input required class="el_input" value="<?=$us['field10']?>" id="city" name="city" type="text">
                        </div>
                    </div>
                    <div class="item">
                        <div class="el_data">
                            <label for="post_index">Индекс</label>
                            <input required class="el_input" value="<?=$us['field11']?>" id="post_index" name="post_index" type="text">
                        </div>
                    </div>
                    <div class="item">
                        <div class="el_data">
                            <label for="street">Улица</label>
                            <input class="el_input" value="<?=$us['field12']?>" id="street" name="street" type="text">
                        </div>
                    </div>
                    <div class="item">
                        <div class="el_data">
                            <label for="h_number">Номер дома</label>
                            <input class="el_input" value="<?=$us['field13']?>" id="h_number" name="house" type="text">
                        </div>
                    </div>
                </div>


            </div>

            <div class="static_data">

                <h2>Рекомендации</h2>
                <div class="group">
                    <div class="item channel">
                        <div class="el_data" style="width: 18rem">
                            <label for="metka_1s">Ваша ссылка для приглашения</label>
                            <input class="el_input" readonly id="metka_1s" type="text" name="link" value="<?=$sin?>">
                        </div>
                        <div class="button icon text add" id="copy_link"><span class="material-icons">link</span>Скопировать ссылку</div>
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
                            <input disabled class="el_input" id="metka_1" name="login" type="text" value="<?=$us['field2']?>">
                        </div>
                        <div class="el_data">
                            <label for="metka_1g">Пароль</label>
                            <input class="el_input" id="metka_1g" type="password" name="password" value="">
                        </div>
                        <div class="button icon text add" id="gen_pass"><span class="material-icons">gpp_good</span>Сгенерировать</div>
                    </div>
                </div>
                <button class="button icon text"><span class="material-icons">save</span>Сохранить</button>
                <button class="button icon text deleteProfile"><span class="material-icons">delete_forever</span>Удалить профиль</button>
            </div>
        </form>
    </div>
</main>
<?php
}else{
    include $_SERVER['DOCUMENT_ROOT'].'/tmpl/page/login.php';
}