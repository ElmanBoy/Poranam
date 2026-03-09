<?php
session_start();
require_once('../Connections/dbconn.php');

$requiredUserLevel = array(0, 1, 2);
include($_SERVER['DOCUMENT_ROOT'] . "/editor/secure/secure.php");

$site_id = intval($_SESSION['site_id']);
$user_id = intval($_SESSION['user_id']);
$where_site = $where_stuff = '';
$status = getRegistry('supportstatus');
$themes = getRegistry('supporttheme');

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

//Фильтры
if ($_SESSION['user_level'] < 2) {
    $where_site = " WHERE site_id = $site_id";
}
if ($_SESSION['user_level'] > 2) {
    $where_stuff = " AND field5 = $user_id";
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
$q = el_dbselect("SELECT * FROM catalog_support_data" . $subQuery . " ORDER BY field2, field4", 20, $q, 'result', true);
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
<h5>Статистика</h5>
<form method="post" name="filter" id="filter">
    <div id="calendar"><input name="date" id="date" type="hidden" value="<?=$_POST['date']?>"></div>
    <input type="submit" name="Submit" class="but" value="Фильтровать">
    <input type="button" id="reset" class="but close" value="Сброс">
</form>


<p><strong>Количество опубликованных новостей за период:</strong> <?=ceil(rand(10, 112))?></p>
    <p><strong>Количество записей на прием за период:</strong> <?=ceil(rand(10, 112))?></p>
    <p><strong>Количество поданных обращений за период:</strong> <?=$pod = ceil(rand(5, 35))?></p>
    <p><strong>Количество закрытых обращений за период:</strong> <?=ceil(rand(5, $pod))?></p>
<p><strong>Количество обращений в техническую поддержку за период:</strong> <?=$sup = ceil(rand(10, 112))?>,
    <strong>закрытых:</strong> <?=$clo = ceil(rand(10, $sup))?>,
    <strong>открытых:</strong> <?=($sup - $clo)?>,
    <strong>среднее время решения:</strong> <?=$h = ceil(rand(1, 30))?> час<?=el_postfix($h, '', 'а', 'ов')?></p>


<p>&nbsp;</p>
</body>
</html>
