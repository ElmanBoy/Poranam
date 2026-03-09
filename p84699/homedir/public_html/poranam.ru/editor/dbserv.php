<?
require_once('../Connections/dbconn.php');
$requiredUserLevel = array(0);
include($_SERVER['DOCUMENT_ROOT'] . "/editor/secure/secure.php");


if ($_POST['action'] == "backup") {
    $last_record = "SELECT * INTO OUTFILE '" . $_SERVER['DOCUMENT_ROOT'] . "/editor/dump.sql' FROM content";
    echo $last_record;

    $Result1 = el_dbselect($last_record, 0, $Result1, 'result', true);
}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Обслуживание базы данных</title>
    <link href="style.css" rel="stylesheet" type="text/css">
    <script type="text/JavaScript">
        <!--
        function MM_goToURL() { //v3.0
            var i, args = MM_goToURL.arguments;
            document.MM_returnValue = false;
            for (i = 0; i < (args.length - 1); i += 2) eval(args[i] + ".location='" + args[i + 1] + "'");
        }

        //-->
    </script>
</head>

<body>
<h4>Обслуживание базы данных</h4>
<p><? el_showalert("info", "Резервная копия базы данных позволяет сохранить контент сайта для последующего восстановления в случае технических проблем у хостинг-провайдера.") ?>
    <br>

    <input name="Submit" type="submit" class="but" onClick="MM_goToURL('self','/editor/e_modules/dumper.php');return document.MM_returnValue"
           value="Создать/Восстановить резервную копию базы данных">
</p>

</body>
</html>
