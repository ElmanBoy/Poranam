<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';
$dateArr = explode('.', $_GET['date']);
$date = $dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0];
$appealType = ($_GET['appealType'] == 'Первичный прием' || $_GET['appealType'] == '') ? 1 : 2;
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Изменение даты записи на приём</title>
    <link href="style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/css/flatpickr.min.css">
    <script src="/js/jquery-1.11.0.min.js"></script>
    <script src="/js/flatpickr.js"></script>
    <script src="/js/ru.js"></script>
    <script src="/js/tooltip.js"></script>
    <script>
        function inWeekends(fullYear, date) {
            var weekends = ["01/01", "01/02", "01/03", "01/04", "01/05", "01/06", "01/07", "01/08", "02/23", "03/08", "05/01", "05/09", "06/12", "11/04"];
            for (var i = 0; i < weekends.length; i++) {
                if (new Date(weekends[i] + "/" + fullYear).valueOf() === date.valueOf()) return true;
            }
            return false;
        }

        function changeDate(id, date, time){
            $("#tr" + id, window.top.frames[2].document).addClass("changed");
            $("#tr" + id + " .dateCell", window.top.frames[2].document).text(date);
            $("#tr" + id + " .timeCell", window.top.frames[2].document).text(time + ":00");
            setTimeout(function(){
                $("#tr" + id, window.top.frames[2].document).removeClass("changed");
            }, 4000);
            top.closeDialog();
        }

        $(document).ready(function () {
            $('*').tooltip({showURL: false});

            $("#date").flatpickr({
                locale: 'ru',
                time_24hr: true,
                inline: true,
                dateFormat: 'Y-m-d',
                defaultDate: '<?= $date ?>',
                altFormat: 'd.m.Y',
                altInput: true,
                altInputClass: "calFormat",
                firstDayOfWeek: 1,
                minDate: "today",
                "disable": [
                    function (date) {
                        //Отключаем выходные
                        var currYear = date.getFullYear();
                        return (date.getDay() === 0 || date.getDay() === 6 || inWeekends(currYear, date));
                    }
                ],
                onReady: function (selectedDates, dateStr, instance) {
                    $.post("/", {ajax: 1, action: "avalableTimes", mode: "availableDates", appealtype: <?=$appealType?>}, function (data) {
                        var disable = [];
                        if (typeof data != "undefined" && data !== "") {
                            var answer = JSON.parse(data);
                            disable = answer.dates;
                            disable.push(function (date) {
                                var currYear = date.getFullYear();
                                return (date.getDay() === 0 || date.getDay() === 6 || inWeekends(currYear, date));
                            });
                            instance.set("disable", disable);
                        }

                    });
                },
                onChange: function (selectedDates, dateStr, instance) {
                    if (dateStr.length > 0) {
                        $.post("/", {ajax: 1, action: "avalableTimes", date: dateStr, appealtype: <?=$appealType?>}, function (data) {
                            if (data.length > 0) {
                                $("#timechoice").html(data).removeClass("hidden");
                            } else {
                                $("#timechoice").addClass("hidden");
                            }
                        });
                    }
                }
            });

        });
        <?
        if(isset($_POST['Submit'])){
            $u = el_dbselect("UPDATE catalog_queue_data 
            SET field4 = '".addslashes($_POST['date'])."', field5 = '".addslashes($_POST['time']).":00' 
            WHERE id = '".intval($_GET['qId'])."' AND site_id = '".intval($_SESSION['site_id'])."'",
                0, $u, 'result', true);
            if($u != false) {
                $datePostArr = explode('-', $_POST['date']);
                $datePost = $datePostArr[2] . '.' . $datePostArr[1] . '.' . $datePostArr[0];
                echo 'changeDate("' . intval($_GET['qId']) . '", "' . $datePost . '", "' . $_POST['time'] . '");';
            }else{
                echo 'alert("Ошибка! Не удалось изменить дату и время записи.")';
            }
        }
        ?>
    </script>
    <style>
        body {
            padding: 0 10px;
        }

        .divider td {
            background-color: #F5F5F5;
            color: #2d76a7;
        }

        .hidden {
            /*visibility: hidden;*/
        }

        #timechoice {
            margin-top: 20px;
            font-size: 16px;
        }

        #timechoice select {
            font-size: 14px;
            width: 254px;
        }

        .calFormat {
            display: none;
        }

        #buttons {
            position: absolute;
            bottom: 0;
            width: 90%;
        }

    </style>
</head>
<body>
<form method="post">
    <div id="calendar"><input name="date" id="date" type="hidden" value="<?= $_GET['date'] ?>"></div>
    <div class="item hidden hide" id="timechoice"></div>
    <div id="buttons">
        <input name="Submit" type="submit" class="but agree" value="Сохранить">
        <input name="Close" type="button" class="but close" onclick="top.closeDialog()" value="Закрыть">
    </div>
</form>
</body>
</html>
