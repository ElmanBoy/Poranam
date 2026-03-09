<?php
session_start();
require_once('../Connections/dbconn.php');

$requiredUserLevel = array(0, 1, 2, 3, 4);
include($_SERVER['DOCUMENT_ROOT'] . "/editor/secure/secure.php");

if (isset($_POST['ajax']) && $_POST['ajax'] == 1) {
    switch ($_POST['action']) {
        case 'operator':
            $u = el_dbselect("UPDATE catalog_queue_data SET field8=" . intval($_POST['operator']) . " 
            WHERE id=" . intval($_POST['messId']), 0, $u, 'result', true);
            echo ($u) ? 'true' : 'false';
            break;
        case 'status':
            $u = el_dbselect("UPDATE catalog_queue_data SET field9=" . intval($_POST['status']) . " 
            WHERE id=" . intval($_POST['messId']), 0, $u, 'result', true);
            echo ($u) ? 'true' : 'false';
            break;
    }
} else {

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
    $u = el_dbselect("SELECT primary_key, fio FROM phpSP_users WHERE active=1".(($_SESSION['user_level'] > 2) ? " AND usergroup=$site_id" : ""),
        0, $u, 'result', true);
    $ru = el_dbfetch($u);
    $users = array();
    do {
        $users[$ru['primary_key']] = $ru['fio'];
    } while ($ru = el_dbfetch($u));

    $s = el_dbselect("SELECT id, short_name FROM sites WHERE id > 1", 0, $s, 'result', true);
    $rs = el_dbfetch($s);
    $sites = array();
    do {
        $sites[$rs['id']] = $rs['short_name'];
    } while ($rs = el_dbfetch($s));

//Фильтры
    /*if ($_SESSION['user_level'] > 1) {
        $where_site = " AND site_id = $site_id";
    }
    if ($_SESSION['user_level'] == 3) {
        $where_stuff = " AND field8 = $user_id";
    }
    if (isset($_POST['date'])) {
        if (substr_count($_POST['date'], " — ") > 0) {
            $dateArr = explode(" — ", $_POST['date']);
            $where_date = " AND field4 >= '" . $dateArr[0] . "' AND field4 <= '" . $dateArr[1] . "'";
        } else {
            $where_date = " AND field4 = '" . $_POST['date'] . "'";
        }
    }

    $subQuery = $where_site . $where_stuff . $where_date;
*/
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
    if ($_SESSION['user_level'] < 3 && strlen(trim($_GET['czn'])) > 0) {
        $where_fields .= " AND site_id = '" . intval($_GET['czn']) . "'";
    }

    $subQuery = $where_site . $where_stuff . $where_date . $where_fields;


    $pn = 0;
    $maxRows_catalog = 18;
    if (isset($_GET['pn'])) {
        $pn = intval($_GET['pn']);
    }
    $q = el_dbselect("SELECT * FROM catalog_queue_data WHERE active=1 " . $subQuery . " ORDER BY field4 DESC, field5 DESC", $maxRows_catalog, $q, 'result', true);
    $rq = el_dbfetch($q);

    if (isset($_GET['tr'])) {
        $tr = $_GET['tr'];
    } else {
        $all_catalog = el_dbselect("SELECT * FROM catalog_queue_data WHERE active=1 " . $subQuery . " ORDER BY field4 DESC, field5 DESC", 0, $all_catalog, 'result', true);
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

    $queryGet = '';
    if (count($_GET) > 0) {
        $getParams = array();
        foreach ($_GET as $key => $val) {
            if (strlen($val) > 0) {
                $getParams[] = $key . '=' . $val;
            }
        }
        $queryGet = '?' . implode('&', $getParams);
    }


//TODO: Добавить:
// оповещение сотрудника звуком и балуном в названии раздела, может быть индикатор вверху
    ?>
    <html>
    <head>
        <title>Электронная очередь</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link href="style.css?v=<?= el_genpass() ?>" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="/css/flatpickr.min.css">
        <script src="/js/jquery-1.11.0.min.js"></script>

        <script src="/js/flatpickr.js"></script>
        <script src="/js/ru.js"></script>
        <script src="/js/tooltip.js"></script>
        <script>
            //Пример: setcookie("cookiePanel", "hide", (new Date).getTime() + (20 * 365 * 24 * 60 * 60 * 1000));
            function setcookie(name, value, expires, path, domain, secure) {
                document.cookie = name + "=" + escape(value) +
                    ((expires) ? "; expires=" + (new Date(expires)) : "") +
                    ((path) ? "; path=/" : "; path=/") +
                    ((domain) ? "; domain=" + domain : "") +
                    ((secure) ? "; secure" : "");
            }


            /**
             * Получить значение куки по имени name
             *
             */
            function getcookie(name) {
                var cookie = " " + document.cookie;
                var search = " " + name + "=";
                var setStr = null;
                var offset = 0;
                var end = 0;
                if (cookie.length > 0) {
                    offset = cookie.indexOf(search);
                    if (offset != -1) {
                        offset += search.length;
                        end = cookie.indexOf(";", offset)
                        if (end == -1) {
                            end = cookie.length;
                        }
                        setStr = unescape(cookie.substring(offset, end));
                    }
                }
                return (setStr);
            }

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
                    firstDayOfWeek: 1,
                    minDate: "today"
                });

                $("#reset").on("click", function (e) {
                    e.preventDefault();
                    document.location.href = "queue.php";
                });

                $("#module_settings").on("click", function (e) {
                    e.preventDefault();
                    top.MM_openBrWindow('modules_settings.php?settings=queue&mode=local', 'metainfo', 'scrollbars=yes,resizable=yes', '650', '450', 'true');
                });

                $(".status").on("change", function () {
                    var self = $(this);
                    $.post("/editor/queue.php", {ajax: 1, action: "status", status: self.val(), messId: self.data("value")},
                        function (data) {
                            if (data === 'true') {
                                self.parents(".action").find(".mess").addClass("show");
                            }
                        });
                });

                $("tr:not(.canceled) .remove").on("click", function () {
                    var dateTime = $(this).parents("tr").children("td");
                    var ok = confirm("Уверены, что хотите безовзратно отменить запись на " + $(dateTime[0]).text() + " " + $(dateTime[1]).text() + "?"),
                        self = $(this);
                    if (ok) {
                        $.post("/editor/queue.php", {ajax: 1, action: "status", status: 3, messId: self.data("value")},
                            function (data) {
                                if (data === 'true') {
                                    self.parents("tr").addClass("canceled").find(".action select").attr("disabled", true);
                                }
                            });
                    }
                });

                $("tr:not(.canceled) .move").on("click", function () {
                    var dateTime = $(this).parents("tr").children("td");
                    var self = $(this);
                    top.MM_openBrWindow('queue_move.php?qId=' + self.data("value") + '&date=' + $(dateTime[0]).text() + '&time=' + $(dateTime[1]).text() +
                        '&appealType=' + $(dateTime[6]).text(),
                        'metainfo',
                        'scrollbars=yes,' +
                        'resizable=yes',
                        '340',
                        '400', 'true');
                });

                $("#queue_add").on("click", function () {
                    top.MM_openBrWindow('queue_add.php',
                        'metainfo',
                        'scrollbars=yes,' +
                        'resizable=yes',
                        '700',
                        '500', 'true');
                });

                $("#queue_print").on("click", function () {
                    var totalPages = <?=$totalPages_catalog?>,
                        allow = false;
                    if (totalPages > 5) {
                        allow = confirm("Количество страниц - примерно " + totalPages + ".\nВсе равно продолжить?");
                    } else {
                        allow = true;
                    }
                    if (allow) {
                        $("#print_queue").attr("src", "/editor/queue_print.php<?=$queryGet?>");
                    }
                });

                $("#openCloseFilters").on("click", function () {
                    var $filter = $("#filter"),
                        $icon = $("#openCloseFilters i"),
                        $span = $("#openCloseFilters span");
                    if ($filter.css("display") === "block") {
                        setcookie("showQueueFilter", "hide", (new Date).getTime() + (20 * 365 * 24 * 60 * 60 * 1000));
                        $filter.slideUp("fast", function () {
                            $span.text("Показать фильтры");
                            $icon.removeClass("up").addClass("down");
                        });
                    } else {
                        setcookie("showQueueFilter", "show", (new Date).getTime() + (20 * 365 * 24 * 60 * 60 * 1000));
                        $filter.slideDown("fast", function () {
                            $span.text("Скрыть фильтры");
                            $icon.removeClass("down").addClass("up");
                        });
                    }
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
                margin-left: 30px;
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
                height: 62px;
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

            .fields table tr td input, .fields table tr td select {
                width: 260px;
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

            .canceled td {
                color: #aec7d4;
            }

            .canceled .material-icons {
                opacity: .2;
            }

            #qActions {
                position: relative;
                top: 10px;
                width: 333px;
                height: 80px;
            }

            #qActions .but {
                margin-right: 20px;
                background-color: #69b3e7;
            }

            #qActions .but:hover {
                background-color: #005c85;
            }

            .changed td.dateCell, .changed td.timeCell {
                -webkit-animation: change-color 4s ease 0s 1 normal;
                -moz-animation: change-color 4s ease 0s 1 normal;
                -ms-animation: change-color 4s ease 0s 1 normal;
                animation: change-color 4s ease 0s 1 normal;
            }

            body div.pagination:first-child {
                margin-top: -70px !important;
                min-height: 33px;
            }

            #openCloseFilters {
                position: absolute;
                left: 575px;
                top: 10px;
            }

            #openCloseFilters i {
                transition: all .2s;
            }

            #openCloseFilters i.material-icons.up {
                transform: rotate(0deg);
            }

            #openCloseFilters i.material-icons.down {
                transform: rotate(180deg);
            }

            #openCloseFilters a:hover {
                color: #E53935 !important;
            }

            @-webkit-keyframes change-color {
                0% {
                    background-color: yellow;
                }
                100% {
                    background-color: white;
                }
            }

            @keyframes change-color {
                0% {
                    background-color: yellow;
                }
                100% {
                    background-color: white;
                }
            }
        </style>
    </head>

    <body>
    <h5>Электронная очередь</h5>
    <form method="get" name="filter" id="filter"
          style="display:<?= (!isset($_COOKIE['showQueueFilter']) || $_COOKIE['showQueueFilter'] == 'show') ? 'block' : 'none' ?>">
        <div id="calendar"><input name="date" id="date" type="hidden" value="<?= $_POST['date'] ?>"></div>

        <div class="fields">
            <table class="el_tbl">
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
                <?
                if ($_SESSION['user_level'] < 3) {
                    ?>
                    <tr>
                        <td>ЦЗН</td>
                        <td>
                            <select name="czn">
                                <?
                                mysqli_data_seek($s, 0);
                                do {
                                    $sel = ($rs['id'] == $_GET['czn']) ? ' selected' : '';
                                    echo '<option value="' . $rs['id'] . '"' . $sel . '>' . $rs['short_name'] . '</option>';
                                } while ($rs = el_dbfetch($s));
                                ?></select>
                        </td>
                    </tr>
                    <?
                }
                ?>
                <tr>
                    <td>Статус</td>
                    <td>
                        <select name="status">
                            <option></option>
                            <?
                            reset($status);
                            foreach ($status as $key => $val) {
                                $sel = ($key == $_GET['status']) ? ' selected' : '';
                                echo '<option value="' . $key . '"' . $sel . '>' . $val . '</option>';
                            }
                            ?></select>
                    </td>
                </tr>
                <tr>
                    <td>Тип обращения</td>
                    <td><select name="theme">
                            <option></option>
                            <?
                            reset($qtype);
                            foreach ($qtype as $key => $val) {
                                $sel = ($key == $_GET['theme']) ? ' selected' : '';
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
                                $sel = ($key == $_GET['operator']) ? ' selected' : '';
                                echo '<option value="' . $key . '"' . $sel . '>' . $val . '</option>';
                            }
                            ?></select>
                    </td>
                </tr>
            </table>
        </div>
        <input type="button" id="reset" class="but close" value="Сброс">
        <input type="submit" name="Submit" class="but" value="Фильтровать">
    </form>
    <div id="toggleFilter">
        <a href="#" id="openCloseFilters">
            <i class="material-icons<?= (!isset($_COOKIE['showQueueFilter']) || $_COOKIE['showQueueFilter'] == 'show') ? ' up' : ' down' ?>">expand_less</i>
            <span><?= (!isset($_COOKIE['showQueueFilter']) || $_COOKIE['showQueueFilter'] == 'show') ? 'Скрыть фильтры' : 'Показать фильтры' ?></span>
        </a>
    </div>
    <input type="button" class="but" id="module_settings" value="Настройки модуля">
    <div style="clear: both; height: 0"></div>
    <div id="qActions">
        <button class="but" id="queue_add"><i class="material-icons">event_note</i> Новая запись</button>
        <button class="but" id="queue_print"><i class="material-icons">print</i> Печать списка</button>
    </div>
    <? if (el_dbnumrows($q) > 0){
        el_paging($pn, '', $queryString_catalog, $totalPages_catalog, $maxRows_catalog, $tr);
        ?>
    <table class="el_tbl" style="margin-left:10px;margin-bottom: 80px;">
        <tr>
            <th>Дата</th>
            <th>Время</th>
            <th>Ф.И.О.</th>
            <th>Email</th>
            <th>Телефон</th>
            <th>Статус</th>
            <th>Тип обращения</th>
            <?= (($_SESSION['user_level'] < 3) ? '<th>ЦЗН</th>' : '') ?>
            <th>Сотрудник</th>
            <th>Действие</th>
            <th>Результат</th>
        </tr>
        <?php
        $prevDate = '';
        do {
            if ($prevDate != $rq['field4']) {
                echo '<tr class="divider"><td colspan="12" align="center">' . el_date1($rq['field4']) . '</td></tr>';
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
    ' . (($_SESSION['user_level'] < 3) ? '<td>' . $sites[$rq['site_id']] . '</td>' : '') . '
    <td>' . $users[$rq['field8']] . '</td>
    <td>
        <i class="remove material-icons" data-value="' . $rq['id'] . '" title="Отменить запись">event_busy</i> 
        <i class="move material-icons" data-value="' . $rq['id'] . '"title="Перенести запись">event</i>
    </td>
    <td class="action">
        <select class="status" data-value="' . $rq['id'] . '"' . (($rq['field9'] == 3) ? ' disabled' : '') . '><option></option>';
            reset($qresults);
            foreach ($qresults as $key => $val) {
                if (intval($key) != 3) {
                    $sel = ($key == $rq['field9']) ? ' selected' : '';
                    echo '<option value="' . $key . '"' . $sel . '>' . $val . '</option>';
                }
            }
            echo '</select>
        <span class="mess">Изменения сохранены</span>
    </td>
    </tr>';
        } while ($rq = el_dbfetch($q));
        } else {
            echo 'Ничего не найдено.';
        }
        ?>
    </table>
    <? el_paging($pn, '', $queryString_catalog, $totalPages_catalog, $maxRows_catalog, $tr); ?>

    <iframe width="1px" height="1px" frameborder="0" src="" id="print_queue"></iframe>
    <p>&nbsp;</p>
    </body>
    </html>
    <?php
}
?>