<?php
$siteId = intval($_SESSION['site_id']);

$work_times = null;


function checkField ()
{
    $errStr = [];
    $errFields = [];
    $times = $_POST['from_time1'] . $_POST['to_time1'] .
        $_POST['from_time2'] . $_POST['to_time2'] .
        $_POST['from_time3'] . $_POST['to_time3'] .
        $_POST['from_time4'] . $_POST['to_time4'] .
        $_POST['from_time5'] . $_POST['to_time5'] .
        $_POST['from_time6'] . $_POST['to_time6'] .
        $_POST['from_time7'] . $_POST['to_time7'];
    $fields = $fields = [
        'Адреса' => $_POST['adresses'],
        'Телефоны' => $_POST['phones'],
        'E-mail' => $_POST['email'],
        'Часы работы' => $times
    ];

    foreach ($fields as $name => $value) {
        if (strlen(trim($value)) == 0) {
            $errStr[] = 'Заполните поле \"' . $name . '\"';
            $errFields[] = 'input[name=' . $value . '], textareat[name=' . $value . ']';
        }
    }

    if (count($errStr) > 0) {
        echo '<script>
        alert("Ошибка:\\n' . implode('\\n', $errStr) . '");
        $(document).ready(function(){
            $("' . implode(', ', $errFields) . '").addClass("error");
        });
        </script>';
        return false;
    }
    return true;
}

function buildWorkTimes ()
{
    $days = array('понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота', 'воскресенье');
    $work_times = array();
    for ($i = 1; $i <= 7; $i++) {
        $work_times[$i] = (isset($_POST['weekend' . $i])) ? array('weekend' . $i => true) : array('from' => $_POST['from_time' . $i], 'to' => $_POST['to_time' . $i], 'weekend' . $i => false);
    }
    $work_times['dinner'] = (isset($_POST['wdinner'])) ? array('wdinner' => true) : array('from' => $_POST['dinner_from'], 'to' => $_POST['dinner_to'], 'wdinner' => false);
    return json_encode($work_times);
}

function buildBranches(){
    $branchArr = array();
    for($i = 0; $i < count($_POST['branch_adresses']); $i++) {
        if(strlen(trim($_POST['branch_adresses'][$i])) > 0 && strlen(trim($_POST['branch_phones'][$i])) > 0) {
            $coords = getCoordsFromAddress($_POST['branch_adresses'][$i]);
            $branchArr[] = array(
                'adresses' => addslashes($_POST['branch_adresses'][$i]),
                'phones' => addslashes($_POST['branch_phones'][$i]),
                'fax' => addslashes($_POST['branch_fax'][$i]),
                'email' => addslashes($_POST['branch_email'][$i]),
                'coords' => $coords
            );
        }
    }
    return (count($branchArr) > 0) ? addslashes(json_encode($branchArr)) : '';
}


if (isset($_POST['saveContacts'])) {
    if (checkField()) {
        $res = el_dbselect("UPDATE sites SET 
            `adresses` = '" . addslashes($_POST['adresses']) . "',
            `phones` = '" . addslashes($_POST['phones']) . "',
            `fax` = '" . addslashes($_POST['fax']) . "',
            `email` = '" . addslashes($_POST['email']) . "',
            `work_times` = '" . buildWorkTimes() . "',
            `instagramm` = '" . addslashes($_POST['instagramm']) . "',
            `vkontakte` = '" . addslashes($_POST['vkontakte']) . "',
            `twitter` = '" . addslashes($_POST['twitter']) . "',
            `mailru` = '" . addslashes($_POST['mailru']) . "',
            `facebook` = '" . addslashes($_POST['facebook']) . "',
            `odnoklassniki` = '" . addslashes($_POST['odnoklassniki']) . "',
            `branches` = '" . buildBranches() . "' 
            WHERE id = '" . $siteId . "'",
            0, $res, 'result', true);
        if ($res == false) {
            $errStr[] = 'Не удалось сохранить изменения.';
        } else {
            echo '<script>alert("Изменения сохранены")</script>';
        }
    }
}

if ($siteId > 0) {
    $s = el_dbselect("SELECT * FROM sites WHERE id = " . $siteId, 0, $s, 'row', true);
    $work_times = json_decode($s['work_times']);
}
?>
<style>
    #contactsFrm a{
        border: none;
    }
    #contactsFrm a:hover, #contactsFrm a:hover .material-icons{
        color: #E53935;
        text-decoration: none;
    }
</style>
<script src="/js/jquery-1.11.0.min.js"></script>
<script>
    $(document).ready(function () {
        $(".tblVertMiddle tr td input[type=checkbox]").on("change", function () {
            var $tr = $(this).parent("label").parent("td").parent("tr");
            $tr.find(".times").css("display", $(this).prop("checked") ? "none" : "inline");
        });

        $("#addBranch").on("click", function(e){
            e.preventDefault();
            var lastTbl = $(".branchTbl").last();
            lastTbl.clone().insertAfter(".branchTbl:last").show();
            $(".branchTbl:last tr td input").val("");

            $(".branchRemove").on("click", function(e){
                e.preventDefault(); console.log($(this).parents(".branchTbl"));
                $(this).parents(".branchTbl").remove();
            });
        });
    });
</script>
<form method="post" id="contactsFrm">
    <table class="el_tbl tblVertMiddle">
        <tr>
            <td>Адреса <sup class="red">*</sup></td>
            <td><input type="text" name="adresses" size="65" value="<?= htmlentities($s['adresses']) ?>"></td>
        </tr>
        <tr>
            <td>Телефоны <sup class="red">*</sup></td>
            <td><input type="text" name="phones" size="65" value="<?= htmlentities($s['phones']) ?>"></td>
        </tr>
        <tr>
            <td>Факс</td>
            <td><input type="text" name="fax" size="65" value="<?= htmlentities($s['fax']) ?>"></td>
        </tr>
        <tr>
            <td>E-mail <sup class="red">*</sup></td>
            <td><input type="text" name="email" size="65" value="<?= htmlentities($s['email']) ?>"></td>
        </tr>
        <tr>
            <td valign="top">Часы работы <sup class="red">*</sup></td>
            <td>
                <table border="0" cellpadding="4" cellspacing="0" class="tblVertMiddle">
                    <tr>
                        <td>Пн.</td>
                        <td><span class="times"<?= ($work_times != null) ? (($work_times->{1}->{'weekend1'}) ? ' style="display: none"' : '') : '' ?>>
                        с <input type="time" name="from_time1" value="<?= ($work_times->{1}->{'from'} != null) ? $work_times->{1}->{'from'} : '09:00' ?>"> до
                        <input type="time" name="to_time1" value="<?= ($work_times->{1}->{'to'} != null) ? $work_times->{1}->{'to'} : '18:00' ?>"></span>
                            <label><input type="checkbox" name="weekend1"
                                    <?= ($work_times != null) ? (($work_times->{1}->{'weekend1'}) ? ' checked' : '') : '' ?>> выходной</label>
                        </td>
                    </tr>
                    <tr>
                        <td>Вт.</td>
                        <td><span class="times"<?= ($work_times != null) ? (($work_times->{2}->{'weekend2'}) ? ' style="display: none"' : '') : '' ?>>
                        с <input type="time" name="from_time2" value="<?= ($work_times->{2}->{'from'} != null) ? $work_times->{2}->{'from'} : '09:00' ?>"> до
                        <input type="time" name="to_time2" value="<?= ($work_times->{2}->{'to'} != null) ? $work_times->{2}->{'to'} : '18:00' ?>"></span>
                            <label><input type="checkbox" name="weekend2"
                                    <?= ($work_times != null) ? (($work_times->{2}->{'weekend2'}) ? ' checked' : '') : '' ?>> выходной</label>
                        </td>
                    </tr>
                    <tr>
                        <td>Ср.</td>
                        <td><span class="times"<?= ($work_times != null) ? (($work_times->{3}->{'weekend3'}) ? ' style="display: none"' : '') : '' ?>>
                        с <input type="time" name="from_time3" value="<?= ($work_times->{3}->{'from'} != null) ? $work_times->{3}->{'from'} : '09:00' ?>"> до
                        <input type="time" name="to_time3" value="<?= ($work_times->{3}->{'to'} != null) ? $work_times->{3}->{'to'} : '18:00' ?>"></span>
                            <label><input type="checkbox" name="weekend3"
                                    <?= ($work_times != null) ? (($work_times->{3}->{'weekend3'}) ? ' checked' : '') : '' ?>> выходной</label>
                        </td>
                    </tr>
                    <tr>
                        <td>Чт.</td>
                        <td><span class="times"<?= ($work_times != null) ? (($work_times->{4}->{'weekend4'}) ? ' style="display: none"' : '') : '' ?>>
                        с <input type="time" name="from_time4" value="<?= ($work_times->{4}->{'from'} != null) ? $work_times->{4}->{'from'} : '09:00' ?>"> до
                        <input type="time" name="to_time4" value="<?= ($work_times->{4}->{'to'} != null) ? $work_times->{4}->{'to'} : '18:00' ?>"></span>
                            <label><input type="checkbox" name="weekend4"
                                    <?= ($work_times != null) ? (($work_times->{4}->{'weekend4'}) ? ' checked' : '') : '' ?>> выходной</label>
                        </td>
                    </tr>
                    <tr>
                        <td>Пт.</td>
                        <td><span class="times"<?= ($work_times != null) ? (($work_times->{5}->{'weekend5'}) ? ' style="display: none"' : '') : '' ?>>
                        с <input type="time" name="from_time5" value="<?= ($work_times->{5}->{'from'} != null) ? $work_times->{5}->{'from'} : '09:00' ?>"> до
                        <input type="time" name="to_time5" value="<?= ($work_times->{5}->{'to'} != null) ? $work_times->{5}->{'to'} : '18:00' ?>"></span>
                            <label><input type="checkbox" name="weekend5"
                                    <?= ($work_times != null) ? (($work_times->{5}->{'weekend5'}) ? ' checked' : '') : '' ?>> выходной</label>
                        </td>
                    </tr>
                    <tr>
                        <td>Сб.</td>
                        <td><span class="times"<?= ($work_times != null) ? (($work_times->{6}->{'weekend6'}) ? ' style="display: none"' : '') : ' style="display: none"' ?>>
                        с <input type="time" name="from_time6" value="<?= ($work_times->{6}->{'from'} != null) ? $work_times->{6}->{'from'} : '09:00' ?>"> до
                        <input type="time" name="to_time6" value="<?= ($work_times->{6}->{'to'} != null) ? $work_times->{6}->{'to'} : '18:00' ?>"></span>
                            <label><input type="checkbox" name="weekend6"
                                    <?= ($work_times != null) ? (($work_times->{6}->{'weekend6'}) ? ' checked' : '') : '' ?>> выходной</label>
                        </td>
                    </tr>
                    <tr>
                        <td>Вс.</td>
                        <td><span class="times"<?= ($work_times != null) ? (($work_times->{7}->{'weekend7'}) ? ' style="display: none"' : '') : ' style="display: none"' ?>>
                        с <input type="time" name="from_time7" value="<?= ($work_times->{7}->{'from'} != null) ? $work_times->{7}->{'from'} : '09:00' ?>"> до
                        <input type="time" name="to_time7" value="<?= ($work_times->{7}->{'to'} != null) ? $work_times->{7}->{'to'} : '18:00' ?>"></span>
                            <label><input type="checkbox" name="weekend7"
                                    <?= ($work_times != null) ? (($work_times->{7}->{'weekend7'}) ? ' checked' : '') : '' ?>> выходной</label>
                        </td>
                    </tr>
                    <tr>
                        <td>Обед</td>
                        <td><span class="times"<?= ($work_times != null) ? (($work_times->{'dinner'}->{'wdinner'}) ? ' style="display: none"' : '') : ' style="display: none"' ?>>
                        с <input type="time" name="dinner_from"
                                 value="<?= ($work_times->{'dinner'}->{'from'} != null) ? $work_times->{'dinner'}->{'from'} : '13:00' ?>"> до
                        <input type="time" name="dinner_to"
                               value="<?= ($work_times->{'dinner'}->{'to'} != null) ? $work_times->{'dinner'}->{'to'} : '14:00' ?>"></span>
                            <label><input type="checkbox" name="wdinner"
                                    <?= ($work_times != null) ? (($work_times->{'dinner'}->{'wdinner'}) ? ' checked' : '') : '' ?>> без обеда</label>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>Ссылка на Instagram</td>
            <td><input type="text" name="instagramm" size="65" value="<?= htmlentities($s['instagramm']) ?>"></td>
        </tr>
        <tr>
            <td>Ссылка на Vkontakte</td>
            <td><input type="text" name="vkontakte" size="65" value="<?= htmlentities($s['vkontakte']) ?>"></td>
        </tr>
        <tr>
            <td>Ссылка на Мой мир (Mail.ru)</td>
            <td><input type="text" name="mailru" size="65" value="<?= htmlentities($s['mailru']) ?>"></td>
        </tr>
        <tr>
            <td>Ссылка на Facebook</td>
            <td><input type="text" name="facebook" size="65" value="<?= htmlentities($s['facebook']) ?>"></td>
        </tr>
        <tr>
            <td>Ссылка на Twitter</td>
            <td><input type="text" name="twitter" size="65" value="<?= htmlentities($s['twitter']) ?>"></td>
        </tr>
        <tr>
            <td>Ссылка на Одноклассники</td>
            <td><input type="text" name="odnoklassniki" size="65" value="<?= htmlentities($s['odnoklassniki']) ?>"></td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <?
                if(strlen(trim($s['branches'])) > 0){
                    $b = json_decode($s['branches'], true);
                    for($c = 0; $c < count($b); $c++){
                        ?>
                        <table class="el_tbl branchTbl">
                            <caption>Филиал</caption>
                            <tr>
                                <td>Адреса <sup class="red">*</sup></td>
                                <td><input type="text" name="branch_adresses[]" size="65" value="<?= htmlentities($b[$c]['adresses']) ?>"></td>
                            </tr>
                            <tr>
                                <td>Телефоны <sup class="red">*</sup></td>
                                <td><input type="text" name="branch_phones[]" size="65" value="<?= htmlentities($b[$c]['phones']) ?>"></td>
                            </tr>
                            <tr>
                                <td>Факс</td>
                                <td><input type="text" name="branch_fax[]" size="65" value="<?= htmlentities($b[$c]['fax']) ?>"></td>
                            </tr>
                            <tr>
                                <td>E-mail</td>
                                <td><input type="text" name="branch_email[]" size="65" value="<?= htmlentities($b[$c]['email']) ?>"></td>
                            </tr>
                            <tr>
                                <td colspan="2" align="right">
                                    <a href="javascript:void(0)" class="branchRemove"><i class="material-icons">delete_forever</i> Удалить</a>
                                </td>
                            </tr>
                        </table>
                <?
                    }
                }
                ?>
                <table class="el_tbl branchTbl" style="display:none">
                    <caption>Филиал</caption>
                    <tr>
                        <td>Адреса <sup class="red">*</sup></td>
                        <td><input type="text" name="branch_adresses[]" size="65" value=""></td>
                    </tr>
                    <tr>
                        <td>Телефоны <sup class="red">*</sup></td>
                        <td><input type="text" name="branch_phones[]" size="65" value=""></td>
                    </tr>
                    <tr>
                        <td>Факс</td>
                        <td><input type="text" name="branch_fax[]" size="65" value=""></td>
                    </tr>
                    <tr>
                        <td>E-mail</td>
                        <td><input type="text" name="branch_email[]" size="65" value=""></td>
                    </tr>
                    <tr>
                        <td colspan="2" align="right">
                            <a href="javascript:void(0)" class="branchRemove"><i class="material-icons">delete_forever</i> Удалить</a>
                        </td>
                    </tr>
                </table>
                <a href="javascript:void()" id="addBranch"><i class="material-icons">add_circle</i> Добавить филиал</a>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center"><input type="submit" name="saveContacts" value="Сохранить" class="but agree"></td>
        </tr>
    </table>
</form>