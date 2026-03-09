<?php
session_start();
require_once('../Connections/dbconn.php');

$requiredUserLevel = array(0, 1, 2, 3, 4);
include($_SERVER['DOCUMENT_ROOT'] . "/editor/secure/secure.php");

$site_id = intval($_SESSION['site_id']);
$user_id = intval($_SESSION['user_id']);
$where_site = $where_stuff = '';
$status = getRegistry('supportstatus');
$themes = getRegistry('supporttheme');
$qrOptions = '<option></option>';

//Справочник сотрудников
$u = el_dbselect("SELECT primary_key, fio, email, phones FROM phpSP_users WHERE active=1", 0, $u, 'result', true);
$ru = el_dbfetch($u);
$users = array();
do {
    $users[$ru['primary_key']] = array(
            'fio' => $ru['fio'],
            'email' => $ru['email'],
            'phones' => $ru['phones']
        );
} while ($ru = el_dbfetch($u));

//Добавление обращения
if(isset($_POST['Submit'])){
    $err = 0;
    $errStr = array();
    if(strlen(trim($_POST['caption'])) == 0){
        $err++;
        $errStr[] = 'Укажите заголовок';
    }
    if(strlen(trim($_POST['text'])) == 0){
        $err++;
        $errStr[] = 'Укажите текст';
    }
    if($err == 0){
        $insertVars = array(
           'site_id' => $site_id,
           'active' => 1,
           'field1' => addslashes($_POST['caption']),
           'field2' => date('Y-m-d'),
            'field3' => 1,
            'field4' => date('H:i:s'),
            'field5' => $user_id,
            'field6' => addslashes($_POST['text']),
            'field7' => intval($_POST['theme'])
        );
        el_dbinsert('catalog_support_data', $insertVars);
    }else{
        if (count($errStr) > 0) {
            echo '<script>alert("Ошибка:\\n' . implode('\\n', $errStr) . '")</script>';
        }
    }
}

//Фильтры
if ($_SESSION['user_level'] > 1) {
    $where_site = " WHERE site_id = $site_id";
}
if ($_SESSION['user_level'] > 2) {
    $where_stuff = " WHERE field5 = $user_id";
}
if (isset($_POST['date'])) {
    if (substr_count($_POST['date'], " — ") > 0) {
        $dateArr = explode(" — ", $_POST['date']);
        $where_date = " AND field2 >= '" . $dateArr[0] . "' AND field2 <= '" . $dateArr[1] . "'";
    } else {
        $where_date = " AND field2 = '" . $_POST['date'] . "'";
    }
}

$subQuery = $where_site . $where_stuff . $where_date;

$pn = 0;
$maxRows_catalog = 20;
if (isset($_GET['pn'])) {
    $pn = intval($_GET['pn']);
}
$q = el_dbselect("SELECT * FROM catalog_support_data" . $subQuery . " ORDER BY field2 DESC, field4 DESC", 20, $q, 'result', true);
$rq = el_dbfetch($q);

if (isset($_GET['tr'])) {
    $tr = $_GET['tr'];
} else {
    $all_catalog = mysqli_query($dbconn, "SELECT * FROM catalog_support_data" . $subQuery . " ORDER BY field2, field4");
    $tr = mysqli_num_rows($all_catalog);
}
$totalPages_catalog = ceil($tr / $maxRows_catalog) - 1;

$queryString_catalog = "";
if (!empty($_SERVER['QUERY_STRING'])) {
    $params = explode("&", $_SERVER['QUERY_STRING']);
    $newParams = array();
    foreach ($params as $param) {
        if (stristr($param, "pn") == false &&
            stristr($param, "tr") == false
        ) {
            array_push($newParams, $param);
        }
    }
    if (count($newParams) != 0) {
        $queryString_catalog = "&" . htmlentities(implode("&", $newParams));
    }
}


//TODO: Добавить:
// фильтр по всем 9-ти полям
// форму самостоятельной записи сотрудником
// пагинацию
// оповещение сотрудника звуком и балуном в названии раздела, может быть индикатор вверху
?>
<html>
<head>
    <title>Техническая поддержка</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="style.css?v=<?= el_genpass() ?>" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="/css/flatpickr.min.css">
    <script src="/js/jquery-1.11.0.min.js"></script>

    <script src="/js/flatpickr.js"></script>
    <script src="/js/ru.js"></script>
    <script src="/js/tooltip.js"></script>
    <script>
        $(document).ready(function () {
            $('*').tooltip({showURL: false});
            $("#date").flatpickr({
                locale: 'ru',
                mode: 'range',
                inline: true,
                time_24hr: true,
                dateFormat: 'Y-m-d',
                altFormat: 'd.m.Y',
                altInput: true,
                altInputClass: "calFormat",
                firstDayOfWeek: 1
            });

            $("#reset").on("click", function(e){
                e.preventDefault();
                document.location.href="queue.php";
            });

            $(".readMessage").on("click", function(e){
                e.preventDefault();
                top.MM_openBrWindow('supportRead.php?id=' + $(this).data("value"),'newMessage','scrollbars=yes,resizable=yes','80%',
                    '80%','true');
            });
        });
    </script>
    <style>
        .el_tbl tr:hover td {
            background-color: #ecebeb;
        }

        .calFormat {
            border: none;
            width: 200px;
        }

        .flatpickr-calendar {
            box-shadow: none;
            border: 1px solid #c1c2c3;
            border-radius: 0;
            margin-bottom: 20px;
        }

        #filter input[type=submit] {
            margin-right: 30px;
        }

        .but.close{
            float: none;
        }
    </style>
</head>

<body>
<h5>Техническая поддержка</h5>
<form method="post" name="filter" id="filter">
    <div id="calendar"><input name="date" id="date" type="hidden" value="<?=$_POST['date']?>"></div>
    <input type="submit" name="Submit" class="but" value="Фильтровать">
    <input type="button" id="reset" class="but close" value="Сброс">
</form>
<? if (el_dbnumrows($q) > 0){
el_paging($pn, '', $queryString_catalog, $totalPages_catalog, $maxRows_catalog, $tr); ?>

<table class="el_tbl" style="margin-left:10px">
    <tr>
        <th>Дата</th>
        <th>Время</th>
        <th>Ф.И.О.</th>
        <th>Email</th>
        <th>Телефон</th>
        <th>Тема обращения</th>
        <th>Статус</th>
        <th></th>
    </tr>
    <?php
    $prevDate = '';
    do {
        if($prevDate != $rq['field2']){
            echo '<tr class="divider"><td colspan="9" align="center">' .el_date1($rq['field2']).'</td></tr>';
            $prevDate = $rq['field2'];
        }
        echo '<tr>
    <td>' . correctDateFormatFromMysql($rq['field2']) . '</td>
    <td>' . $rq['field4'] . '</td>
    <td>' . $users[$rq['field5']]['fio'] . '</td>
    <td>' . $users[$rq['field5']]['email'] . '</td>
    <td>' . $users[$rq['field5']]['phones'] . '</td>
    <td>' . $themes[$rq['field7']] . '</td>
    <td>' . $status[$rq['field3']] . '</td>
    <td>
        <select>
            ' . getOptionsList($status, $rq['field3']) . '
        </select>
    </td>
    <td><input type="button" class="but readMessage" data-value="'.$rq['id'].'" value="Читать"></td>
    </tr>';
    } while ($rq = el_dbfetch($q));
    } else {
        echo 'Ничего не найдено.';
    }
    ?>
</table>
<? el_paging($pn, '', $queryString_catalog, $totalPages_catalog, $maxRows_catalog, $tr);?>
<p>&nbsp;</p>
<h5>Создать запрос</h5>
<form method="post">
    <table class="el_tbl">
        <tr>
            <td>Тема обращения:</td>
            <td>
                <select name="theme" required>
                    <?php
                    reset($themes);
                    foreach($themes as $id => $text){
                        echo '<option value="'.$id.'">'.$text.'</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Заголовок:</td>
            <td><input type="text" size="60" name="caption" placeholder="Впишите кратко название проблемы" required></td>
        </tr>
        <tr>
            <td>Текст:</td>
            <td><textarea name="text" rows="10" cols="70" placeholder="Впишите подробно проблему" required></textarea></td>
        </tr>
        <tr>
            <td><input type="submit" name="Submit" value="Отправить" class="but"></td>
        </tr>
    </table>
</form>
</body>
</html>
