<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';
$dateArr = explode('.', $_GET['date']);
$date = $dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0];
$appealType = ($_GET['appealType'] == 'Первичный прием' || $_GET['appealType'] == '') ? 1 : 2;
define('INCLUDED', true);
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Изменение даты записи на приём</title>
    <link href="style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/css/flatpickr.min.css">
    <link href="/css/style01.css" rel="stylesheet" type="text/css" media="all">
    <script src="/js/jquery-1.11.0.min.js"></script>
    <script src="/js/jquery.maskedinput.js"></script>
    <script src="/js/flatpickr.js"></script>
    <script src="/js/ru.js"></script>
    <script src="/js/tooltip.js"></script>
    <script src="/js/scripts.js"></script>
    <style>
        body {
            padding: 0 10px;
        }

        #buttons {
            position: absolute;
            bottom: 0;
            width: 90%;
        }

        label{
            display: inline;
        }
        input, select{
            float:right;
            width: 80% !important;
        }
        #buttons input{
            width: auto !important;
        }

    </style>
</head>
<body>
<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/modules/queue.php';
?>
</body>
</html>