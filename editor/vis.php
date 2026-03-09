<?php
session_start();
require_once('../Connections/dbconn.php');

$requiredUserLevel = array(0, 1);
include($_SERVER['DOCUMENT_ROOT'] . "/editor/secure/secure.php");

if(isset($_POST['Submit'])){
    foreach($_POST as $key => $val) {
        if($key != 'Submit') {
            el_2ini('visApi_'.$key, $val);
        }
    }
    echo "<script language=javascript>alert('Настройки сохранены!')</script>";
}
include_once $_SERVER['DOCUMENT_ROOT'].'/Connections/site_props.php';
?>
<html>
<head>
    <title>Взаимодействие с ВИС</title>
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

            $(".testApi").on("click", function(e){
                e.preventDefault();
                $.get($("input[name=visHost]").val(), {"method": $(this).attr("id").replace("test_", "")}, function(data){
                    var answer = JSON.parse(data);
                    $("#testAnswer").html('<h5>Ответ:</h5><pre>' + JSON.stringify(answer, null, 4) + '</pre>');
                })
            })
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

        .testApi{
            margin: 0;
        }

        #testAnswer{
            max-width: 800px;
            max-height: 300px;
            overflow: auto;
        }
        #testAnswer pre{
            white-space: pre-wrap;
        }
    </style>
</head>

<body>
<h5>Взаимодействие с ведомственными информационными системами</h5>

<h5>Настройки</h5>
<form method="post">
    <table class="el_tbl">
        <tr>
            <td>URL сервиса:</td>
            <td>
                <input type="text" size="60" name="visHost" value="<?=$site_property['visApi_visHost']?>">
            </td>
        </tr>
        <tr>
            <td>Метод запроса вакансий:</td>
            <td><input type="text" size="50" name="getVacancies" value="<?=$site_property['visApi_getVacancies']?>">
            <input type="button" id="test_getVacancies" class="but testApi" value="Тест">
            </td>
        </tr>
        <tr>
            <td>Метод запроса показателей рынка труда:</td>
            <td><input type="text" size="50" name="getLaborMarket" value="<?=$site_property['visApi_getLaborMarket']?>">
                <input type="button" id="test_getLaborMarket" class="but testApi" value="Тест"></td>
        </tr>
        <tr>
            <td>Метод запроса профессий устойчивого спроса:</td>
            <td><input type="text" size="50" name="getProfessions" value="<?=$site_property['visApi_getProfessions']?>">
                <input type="button" id="test_getProfessions" class="but testApi" value="Тест"></td>
        </tr>
        <tr>
            <td>Метод запроса статистики о вакансиях:</td>
            <td><input type="text" size="50" name="getStatistics" value="<?=$site_property['visApi_getStatistics']?>">
                <input type="button" id="test_getStatistics" class="but testApi" value="Тест"></td>
        </tr>
        <tr>
            <td>Метод запроса реестра образовательных программ и организаций:</td>
            <td><input type="text" size="50" name="getEducation" value="<?=$site_property['visApi_getEducation']?>">
                <input type="button" id="test_getEducation" class="but testApi" value="Тест"></td>
        </tr>
        <tr>
            <td>Метод запроса реестра НКО:</td>
            <td><input type="text" size="50" name="getNKO" value="<?=$site_property['visApi_getNKO']?>">
                <input type="button" id="test_getNKO" class="but testApi" value="Тест"></td>
        </tr>
        <tr>
            <td><input type="submit" name="Submit" value="Сохранить" class="but"></td>
        </tr>
    </table>
</form>

<div id="testAnswer"></div>
</body>
</html>