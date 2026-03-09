<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/Connections/site_props.php';
$res = '';

if (isset($_GET['recip']) && isset($_GET['activate']) && $_GET['activate'] == 'y') {//Активация учетной записи
    $flag = 0;
    $recip_id = substr_replace($_GET['recip'], '', 0, 8);
    $recip_id = intval($recip_id);
    $enter = el_dbselect("SELECT * FROM catalog_users_data WHERE id=" . $recip_id, 0, $enter, 'result', true);
    $row_enter = el_dbfetch($enter);
    if (mysqli_num_rows($enter) > 0) {
        if ($row_enter['active'] != '0') {
            echo "<span style='color:red'><h4>Ваша учетная запись уже активирована.</h4>Возможно, Вы перешли по этой ссылке повторно.</span>";
            $flag = 0;
        } else {

            $updateSQL = sprintf("UPDATE catalog_users_data SET active=%s WHERE id=%s",
                GetSQLValueString(1, "int"),
                GetSQLValueString($recip_id, "int"));

            if (el_dbselect($updateSQL, 0, $res, 'result', true) == false) {
                echo "<span style='color:red'><h4>Не удается активировать учетную запись.</h4>Возможно, введен неверный адрес страницы.</span>";
            } else {
                echo "<span style='color:green'><h4>Спасибо!</h4>Ваша учетная запись успешно активирована.</span><br><br>";

                $login = $row_enter['field2'];
                $fio = $row_enter['field1'];
                $_SESSION['login'] = $row_enter['field2'];
                $_SESSION['fio'] = $row_enter['field1'];
                $_SESSION['userlevel'] = 1;
                @setcookie('usid', $usid, time() + 14400, '/', '');
                $flag = 0;
            }
        }
    }
}
?>