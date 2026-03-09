<?php
session_start();
require_once('../Connections/dbconn.php');

$requiredUserLevel = array(0, 1, 2, 3, 4);
include($_SERVER['DOCUMENT_ROOT'] . "/editor/secure/secure.php");


$site_id = intval($_SESSION['site_id']);
$user_id = intval($_SESSION['user_id']);
$where_site = $where_stuff = '';
$status = getRegistry('statusappeal');
$qtype = getRegistry('typesappeal');
$qresults = getRegistry('qresults');
$qrOptions = '<option></option>';
foreach ($qresults as $key => $val) {
    $qrOptions .= '<option value="' . $key . '">' . $val . '</option>';
}
//Справочник сотрудников
$u = el_dbselect("SELECT primary_key, fio FROM phpSP_users WHERE active=1 AND usergroup=$site_id", 0, $u, 'result', true);
$ru = el_dbfetch($u);
$users = array();
do {
    $users[$ru['primary_key']] = $ru['fio'];
} while ($ru = el_dbfetch($u));

//Фильтры
if ($_SESSION['user_level'] > 1) {
    $where_site = " AND site_id = $site_id";
}
if ($_SESSION['user_level'] == 4) {
    $where_stuff = " AND field8 = $user_id";
}
if (strlen($_GET['date']) > 0) {
    if (substr_count($_GET['date'], " — ") > 0) {
        $dateArr = explode(" — ", $_GET['date']);
        $where_date = " AND field4 >= '" . $dateArr[0] . "' AND field4 <= '" . $dateArr[1] . "'";
    } else {
        $where_date = " AND field4 = '" . $_GET['date'] . "'";
    }
}
if (strlen(trim($_GET['fio'])) > 0) {
    $where_fields .= " AND field1 LIKE '%" . addslashes($_GET['fio']) . "%'";
}
if (strlen(trim($_GET['email'])) > 0) {
    $where_fields .= " AND field2 LIKE '%" . addslashes($_GET['email']) . "%'";
}
if (strlen(trim($_GET['phone'])) > 0) {
    $where_fields .= " AND field3 LIKE '%" . addslashes($_GET['phone']) . "%'";
}
if (strlen(trim($_GET['theme'])) > 0) {
    $where_fields .= " AND field7 = '" . intval($_GET['theme']) . "'";
}
if (strlen(trim($_GET['operator'])) > 0) {
    $where_fields .= " AND field8 = '" . intval($_GET['operator']) . "'";
}
if (strlen(trim($_GET['status'])) > 0) {
    $where_fields .= " AND field6 = '" . intval($_GET['status']) . "'";
}

$subQuery = $where_site . $where_stuff . $where_date . $where_fields;

if (isset($_GET['pn'])) {
    $pn = intval($_GET['pn']);
}
$q = el_dbselect("SELECT * FROM catalog_queue_data WHERE active=1 " . $subQuery . " ORDER BY field4 DESC, field5 DESC", 0, $q, 'result', true);
$rq = el_dbfetch($q);
?>
<html>
<head>
    <title>Электронная очередь</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="style.css?v=<?= el_genpass() ?>" rel="stylesheet" type="text/css">
    <script src="/js/jquery-1.11.0.min.js"></script>

    <script src="/js/flatpickr.js"></script>
    <script src="/js/ru.js"></script>
    <script src="/js/tooltip.js"></script>
    <script>
        $(document).ready(function () {
            window.print();
        });
    </script>
    <style>
        .canceled td {
            color: #aec7d4;
        }
    </style>
</head>

<body>
<h5 style="text-align: center;color: #000;">Электронная очередь</h5>
<?
if (el_dbnumrows($q) > 0){
?>

<table class="el_tbl">
    <tr>
        <th>Дата</th>
        <th>Время</th>
        <th>Ф.И.О.</th>
        <th>Email</th>
        <th>Телефон</th>
        <th>Статус</th>
        <th>Тип обращения</th>
        <th>Сотрудник</th>
        <th>Результат</th>
    </tr>
    <?php
    $prevDate = '';
    do {
        if ($prevDate != $rq['field4']) {
            echo '<tr class="divider_print"><td colspan="10" align="center">' . el_date1($rq['field4']) . '</td></tr>';
            $prevDate = $rq['field4'];
        }
        echo '<tr' . (($rq['field9'] == 3) ? ' class="canceled"' : '') . ' id="tr' . $rq['id'] . '">
    <td class="dateCell">' . correctDateFormatFromMysql($rq['field4']) . '</td>
    <td class="timeCell">' . $rq['field5'] . '</td>
    <td>' . $rq['field1'] . '</td>
    <td>' . $rq['field2'] . '</td>
    <td>' . $rq['field3'] . '</td>
    <td>' . $status[$rq['field6']] . '</td>
    <td>' . ((intval($rq['field7']) > 0) ? $qtype[$rq['field7']] : 'Первичный прием') . '</td>
    <td>' . $users[$rq['field8']] . '</td>
    <td class="action">' . $qresults[$rq['field9']] . '</td>
    </tr>';
    } while ($rq = el_dbfetch($q));
    } else {
        echo 'Ничего не найдено.';
    }
    ?>
</table>
</div>
<tfoot>TEST</tfoot>
</body>
</html>