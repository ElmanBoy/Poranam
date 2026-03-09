<?php
session_start();
require_once('../Connections/dbconn.php');

$requiredUserLevel = array(0, 1, 2, 3, 4);
include($_SERVER['DOCUMENT_ROOT'] . "/editor/secure/secure.php");

if (isset($_POST['ajax']) && $_POST['ajax'] == 1) {
    switch ($_POST['action']) {
        case 'operator':
            $u = el_dbselect("UPDATE catalog_messages_data SET field8=" . intval($_POST['operator']) . " 
            WHERE id=" . intval($_POST['messId']), 0, $u, 'result', true);
            echo ($u) ? 'true' : 'false';
            break;
        case 'status':
            $u = el_dbselect("UPDATE catalog_messages_data SET field6=" . intval($_POST['status']) . " 
            WHERE id=" . intval($_POST['messId']), 0, $u, 'result', true);
            echo ($u) ? 'true' : 'false';
            break;
    }
} else {
    $site_id = intval($_SESSION['site_id']);
    $user_id = intval($_SESSION['user_id']);
    $where_site = $where_stuff = '';
    $status = getRegistry('messagestatus');
    $qtype = getRegistry('themes');
    $qrOptions = '<option></option>';
    foreach ($status as $key => $val) {
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
    if (strlen($_POST['date']) > 0) {
        if (substr_count($_POST['date'], " — ") > 0) {
            $dateArr = explode(" — ", $_POST['date']);
            $where_date = " AND field1 >= '" . $dateArr[0] . "' AND field1 <= '" . $dateArr[1] . "'";
        } else {
            $where_date = " AND field1 = '" . $_POST['date'] . "'";
        }
    }
    if (intval($_POST['id']) > 0) {
        $where_fields .= " AND id = '" . intval($_POST['id']) . "'";
    }
    if (strlen(trim($_POST['fio'])) > 0) {
        $where_fields .= " AND field3 LIKE '%" . addslashes($_POST['fio']) . "%'";
    }
    if (strlen(trim($_POST['email'])) > 0) {
        $where_fields .= " AND field4 LIKE '%" . addslashes($_POST['email']) . "%'";
    }
    if (strlen(trim($_POST['phone'])) > 0) {
        $where_fields .= " AND field5 LIKE '%" . addslashes($_POST['phone']) . "%'";
    }
    if (strlen(trim($_POST['theme'])) > 0) {
        $where_fields .= " AND field7 = '" . intval($_POST['theme']) . "'";
    }
    if (strlen(trim($_POST['operator'])) > 0) {
        $where_fields .= " AND field8 = '" . intval($_POST['operator']) . "'";
    }
    if (strlen(trim($_POST['status'])) > 0) {
        $where_fields .= " AND field6 = '" . intval($_POST['status']) . "'";
    }

    $subQuery = $where_site . $where_stuff . $where_date . $where_fields;

    $pn = 0;
    $maxRows_catalog = 20;
    if (isset($_GET['pn'])) {
        $pn = intval($_GET['pn']);
    }
    $q = el_dbselect("SELECT * FROM catalog_messages_data WHERE active=1" . $subQuery . " ORDER BY field1 DESC", $maxRows_catalog, $q, 'result', true);
    $rq = el_dbfetch($q);

    if (isset($_GET['tr'])) {
        $tr = $_GET['tr'];
    } else {
        $all_catalog = el_dbselect("SELECT * FROM catalog_messages_data WHERE active=1" . $subQuery . " ORDER BY field1", 0, $all_catalog, 'result', true);
        $tr = el_dbnumrows($all_catalog);
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
        <title>Обращения</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link href="style.css?v=<?= el_genpass() ?>" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="/css/flatpickr.min.css">
        <script src="/js/jquery-1.11.0.min.js"></script>

        <script src="/js/flatpickr.js"></script>
        <script src="/js/ru.js"></script>
        <script src="/js/tooltip.js"></script>
        <script src="/js/jquery.maskedinput.js"></script>
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

                $("#reset").on("click", function (e) {
                    e.preventDefault();
                    document.location.href = "messages.php";
                });

                $("#module_settings").on("click", function (e) {
                    e.preventDefault();
                    top.MM_openBrWindow('modules_settings.php?settings=messages&mode=local', 'metainfo', 'scrollbars=yes,resizable=yes', '650', '450', 'true')
                });

                $(".readMessage").on("click", function (e) {
                    e.preventDefault();
                    top.MM_openBrWindow('messagesRead.php?id=' + $(this).data("value"), 'newMessage', 'scrollbars=yes,resizable=yes', '80%',
                        '80%', 'true');
                });

                $(".operator").on("change", function () {
                    var self = $(this);
                    $.post("/editor/messages.php", {ajax: 1, action: "operator", operator: self.val(), messId: self.data("value")},
                        function (data) {
                            if (data === 'true') {
                                self.parents(".action").find(".mess").addClass("show");
                            }
                        });
                });
                $(".status").on("change", function () {
                    var self = $(this);
                    $.post("/editor/messages.php", {ajax: 1, action: "status", status: self.val(), messId: self.data("value")},
                        function (data) {
                            if (data === 'true') {
                                self.parents(".action").find(".mess").addClass("show");
                            }
                        });
                });
                $("input[type=tel]").mask('+7 (999) 999-99-99');
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

            .but.close {
                float: none;
            }

            #module_settings {
                position: absolute;
                top: 20px;
                right: 2%;
            }

            .action {
                position: relative;
            }

            .mess {
                color: green;
                display: block;
                position: absolute;
                font-size: smaller;
                transition: all .5s;
                opacity: 0;
            }

            .mess.show {
                opacity: 1;
            }

            #calendar {
                float: left;
                height: 350px;
                margin-right: 20px;
            }

            .fields {
                padding-top: 20px;
            }

            .fields table tr td input, .fields table tr td select {
                width: 260px;
            }

            select {
                height: 22px;
            }
        </style>
    </head>

    <body>
    <h5>Обращения</h5>
    <form method="post" name="filter" id="filter">
        <div id="calendar"><input name="date" id="date" type="hidden" value="<?= $_POST['date'] ?>"></div>

        <div class="fields">
            <table class="el_tbl">
                <tr>
                    <td>ID обрращения</td>
                    <td><input type="number" name="id" value="<?= $_POST['id'] ?>" size="40"></td>
                </tr>
                <tr>
                    <td>Ф.И.О.</td>
                    <td><input type="text" name="fio" value="<?= $_POST['fio'] ?>" size="40"></td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td><input type="text" name="email" value="<?= $_POST['email'] ?>" size="40"></td>
                </tr>
                <tr>
                    <td>Телефон</td>
                    <td><input type="tel" name="phone" value="<?= $_POST['phone'] ?>" size="40"></td>
                </tr>
                <tr>
                    <td>Тема обращения</td>
                    <td><select name="theme">
                            <option></option>
                            <?
                            reset($qtype);
                            foreach ($qtype as $key => $val) {
                                $sel = ($key == $_POST['theme']) ? ' selected' : '';
                                echo '<option value="' . $key . '"' . $sel . '>' . $val . '</option>';
                            }
                            ?></select>
                    </td>
                </tr>
                <tr>
                    <td>Сотрудник</td>
                    <td><select name="operator">
                            <option></option>
                            <?
                            reset($users);
                            foreach ($users as $key => $val) {
                                $sel = ($key == $_POST['operator']) ? ' selected' : '';
                                echo '<option value="' . $key . '"' . $sel . '>' . $val . '</option>';
                            }
                            ?></select>
                    </td>
                </tr>
                <tr>
                    <td>Статус</td>
                    <td>
                        <select name="status">
                            <option></option>
                            <?
                            reset($status);
                            foreach ($status as $key => $val) {
                                $sel = ($key == $_POST['status']) ? ' selected' : '';
                                echo '<option value="' . $key . '"' . $sel . '>' . $val . '</option>';
                            }
                            ?></select>
                    </td>
                </tr>
            </table>
        </div>
        <input type="submit" name="Submit" class="but" value="Фильтровать">
        <input type="button" id="reset" class="but close" value="Сброс">
    </form>


    <input type="button" class="but" id="module_settings" value="Настройки модуля">
    <div style="clear: both">
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
                <th>Сотрудник</th>
                <th>Статус</th>
                <th></th>
            </tr>
            <?php
            $prevDate = '';
            do {
                $datetimeArr = explode(" ", $rq['field1']);
                if ($prevDate != $datetimeArr[0]) {
                    echo '<tr class="divider"><td colspan="9" align="center">' . el_date1($datetimeArr[0]) . '</td></tr>';
                    $prevDate = $datetimeArr[0];
                }
                echo '<tr>
    <td>' . correctDateFormatFromMysql($datetimeArr[0]) . '</td>
    <td>' . $datetimeArr[1] . '</td>
    <td>' . $rq['field3'] . '</td>
    <td>' . $rq['field4'] . '</td>
    <td>' . $rq['field5'] . '</td>
    <td>' . $qtype[$rq['field7']] . '</td>
    <td class="action">
        <select class="operator" data-value="' . $rq['id'] . '"><option></option>';
                reset($users);
                foreach ($users as $key => $val) {
                    $sel = ($key == $rq['field8']) ? ' selected' : '';
                    echo '<option value="' . $key . '"' . $sel . '>' . $val . '</option>';
                }
                echo '</select>
        <span class="mess">Изменения сохранены</span>
    </td>
    <td class="action">
        <select class="status" data-value="' . $rq['id'] . '"><option></option>';
                reset($status);
                foreach ($status as $key => $val) {
                    $sel = ($key == $rq['field6']) ? ' selected' : '';
                    echo '<option value="' . $key . '"' . $sel . '>' . $val . '</option>';
                }
                echo '</select>
        <span class="mess">Изменения сохранены</span>
    </td>
    <td><input type="button" class="but readMessage" data-value="' . $rq['id'] . '" value="Читать"></td>
    </tr>';
            } while ($rq = el_dbfetch($q));
            } else {
                echo 'Ничего не найдено.';
            }
            ?>
        </table>
        <? el_paging($pn, '', $queryString_catalog, $totalPages_catalog, $maxRows_catalog, $tr); ?>
    </div>
    <p>&nbsp;</p>
    </body>
    </html>
    <?php
}
?>
