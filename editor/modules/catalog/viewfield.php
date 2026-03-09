<html>

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <title>Так будет выглядеть поле в форме</title>

    <script language="javascript" src="/editor/e_modules/cal2.js"></script>

    <script language="javascript">

        addCalendar("Calendar1", "Выбрать дату", "samplefield", "sampleform");

    </script>

    <link href="/editor/style.css" rel="stylesheet" type="text/css">

</head>


<body>
<center>
    <form name="sampleform">

        <?

        switch ($_GET['type']) {

            case "text":
                $input = "input";
                $prop = "";

                $output = "";

                break;

            case "textarea":
                $input = "textarea";
                $prop = "cols=" . $_GET['cols'] . " rows=" . $_GET['rows'] . "";

                $output = "</textarea>";

                break;

            case "select":
                $input = "textarea";
                $prop = "cols=30 rows=5";

                $output = "Здесь вписываются строки списка через точку с запятой ';'</textarea>";

                break;

            case "checkbox":
                $input = "input";
                $prop = "";

                $output = "";

                break;

            case "radio":
                $input = "input";
                $prop = "";

                $output = "";

                break;

            case "hidden":
                $input = "input";
                $prop = "";

                $output = "";

            case "image":
                $input = "input";
                $prop = "";

                $output = "<br>Здесь указывается местонахождение картинки на Вашем компьютере для закачки на сервер";
                $_GET['type'] = "file";

                break;

            case "file":
                $input = "input";
                $prop = "";

                $output = "<br>Здесь указывается местонахождение файла на Вашем компьютере для закачки на сервер";

                break;

            case "calendar":
                $input = "input";
                $prop = "";
                $_GET['type'] = "text";

                //$output=" <small><a href=\"javascript:showCal('Calendar1')\">Выбрать дату</a></small>";

                $output = " <button onclick=\"showCal('Calendar1')\" style='width:100px'><img src='/editor/img/b_calendar.gif' align='absmiddle' border=0><small><a href=\"javascript:showCal('Calendar1')\">Выбрать дату</a></small></button>";

        }

        echo "<$input type='" . $_GET['type'] . "' name='samplefield' size='" . $_GET['size'] . "' $prop>$output" ?>

        <br>

        <br>

        <input type="button" name="Button" value=" Закрыть " class="but" onClick="window.close()"></form>
</center>

</body>

</html>

