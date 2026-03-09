<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php');
$requiredUserLevel = array(1, 2);
include($_SERVER['DOCUMENT_ROOT'] . "/editor/secure/secure.php");

$currentPage = $_SERVER["PHP_SELF"];


//Записываем в базу
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "add")) {

    $query_user = "SELECT fio, userlevel FROM phpSP_users WHERE user='".$_SESSION['login']."'";
    $user = el_dbselect($query_user, 0, $user, 'result', true);
    $row_user = el_dbfetch($user);

    $Result1 = '';
    $themes = implode(';', $_POST['themes']);
    $insertSQL = sprintf("INSERT INTO mail_issues (template, title, body, themes, date_create, date_send, sender) VALUES (%s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($_POST['template'], "text"),
        GetSQLValueString($_POST['title'], "text"),
        GetSQLValueString($_POST['body'], "text"),
        GetSQLValueString($themes, "text"),
        GetSQLValueString(date("Y-m-d H:i"), "date"),
        GetSQLValueString("", "date"),
        GetSQLValueString($row_user['fio'], "text"));
    $Result1 = el_dbselect($insertSQL, 0, $Result1, 'result', true);

}

if (isset($_POST['id']) && $_POST['id'] != "" && $_POST['action'] == "del") {
    echo $_POST['action'];
    echo $deleteSQL = sprintf("DELETE FROM mail_issues WHERE id=%s",
        GetSQLValueString($_POST['id'], "int"));;
    $Result1 = el_dbselect($deleteSQL, 0, $Result1, 'result', true);
}


$code = '';
//Отправка мыла
function parsemail ( $type, $tamp, $subj, $body, $theme )
{
    global $database_dbconn, $dbconn, $row_recip, $row_issue, $code;
    $template = '';
    $theme = el_dbselect("select * from mail_themes where id='" . $theme . "'", 1, $theme);
    $row_theme = el_dbfetch($theme);

    $template = el_dbselect("select * from mail_templates where id='" . $tamp . "'", 1, $template);
    $row_template = el_dbfetch($template);

    if ($row_recip['codepage'] == 'KOI-8R') {
        $row_template['body'] = convert_cyr_string($row_template['body'], w, k);
        $row_recip['name'] = convert_cyr_string($row_recip['name'], w, k);
        if ($code != 'koi') {
            $row_issue['title'] = convert_cyr_string($row_issue['title'], w, k);
            $row_issue['body'] = convert_cyr_string($row_issue['body'], w, k);
        }
        $code = 'koi';
    } else {
        $code = 'win';
    }
    $row_template['body'] = str_replace('%%name%%', $row_recip['name'], $row_template['body']);
    $row_template['body'] = str_replace('%%subject%%', $row_issue['title'], $row_template['body']);
    $row_template['body'] = str_replace('%%theme%%', $row_theme['name'], $row_template['body']);
    $row_template['body'] = str_replace('%%body%%', $row_issue['body'], $row_template['body']);
    $row_template['body'] = str_replace('%%siteurl%%', 'http://' . $_SERVER['SERVER_NAME'], $row_template['body']);
    $row_template['body'] = str_replace('%%date%%', el_date1(date("Y-m-d")), $row_template['body']);
    $row_template['body'] = str_replace('%%email%%', $row_recip['email'], $row_template['body']);
    $row_template['body'] = str_replace('%%admin%%', 'info@' . str_replace('www.', '', $_SERVER['SERVER_NAME']), $row_template['body']);
    $headers = "From: " . $_SERVER['SERVER_NAME'] . "<info@" . str_replace('www.', '', $_SERVER['SERVER_NAME']) . ">\n";
    $headers .= "X-Priority: 1\n";
    $headers .= "X-Sender: <" . $_SERVER['SERVER_NAME'] . ">\n";
    $headers .= "X-Mailer: PHP\n";
    $headers .= "Return-Path: <info@" . str_replace('www.', '', $_SERVER['SERVER_NAME']) . ">\n";
    if ($type == 'HTML') {
        $row_template['body'] = str_replace('%%sitename%%', '<a href="http://' . $_SERVER['SERVER_NAME'] . '" target=_blank>' . $_SERVER['SERVER_NAME'] . '</a>', $row_template['body']);
        $row_template['body'] = str_replace('%%sendedit%%', '<a HREF="http://' . $_SERVER['SERVER_NAME'] . '/subscribe/" TARGET=_BLANK>Управление подпиской</a>', $row_template['body']);
        $headers .= "Content-Type: text/html; charset=" . $row_recip['codepage'] . "\n";
    } else {
        $row_template['body'] = str_replace('%%sitename%%', $_SERVER['SERVER_NAME'], $row_template['body']);
        $row_template['body'] = str_replace('%%sendedit%%', 'http://' . $_SERVER['SERVER_NAME'] . '/subscribe/', $row_template['body']);
        $headers .= "Content-Type: text/plain; charset=" . $row_recip['codepage'] . "\n";
    }

    if ($row_template['type'] == $row_recip['type']) {
        if (mail($row_recip['email'], $row_issue['title'], $row_template['body'], $headers)) {
            return true;
        } else {
            return false;
        }
    }

}

//Рассылаем в цикле
if ((isset($_POST['id'])) && ($_POST['id'] != "" && $_POST['action'] == "send")) {
    $issue = $recip = '';
    $issue = el_dbselect("select * from mail_issues where id='" . $_POST['id'] . "'", 1, $issue);
    $row_issue = el_dbfetch($issue);
    $recip = el_dbselect("select * from mail_list where active=1", 100, $recip);
    $row_recip = el_dbfetch($recip);

    if (mysqli_num_rows($recip) > 0) {
        $c = 0;
        $p = 0;
        do {
            $arrthemes = explode(';', $row_recip['themes']);
            if (in_array($row_issue['themes'], $arrthemes)) {

                if (parsemail($row_recip['type'], $row_issue['template'], $row_issue['title'], $row_issue['body'], $row_issue['theme'])) {
                    $c++;
                }
            }
            $p++;
        } while ($row_recip = el_dbfetch($recip));
        echo "<script>alert('Рассылка прошла успешно!\\nОтправлено $c из $p писем')</script>";
        $updateSQL = sprintf("UPDATE mail_issues SET date_send=%s WHERE id=%s",
            GetSQLValueString(date("Y-m-d H:i"), "date"),
            GetSQLValueString($_POST['id'], "int"));;
        $Result1 = el_dbselect($updateSQL, 0, $Result1, 'result', true);
    } else {
        echo "<script>alert('Некому рассылать!\\nНет ни одного подписчика на заданную тему.')</script>";
    }
}

//Выводим список выпусков
$maxRows_dbmail_list = 25;
$pageNum_dbmail_list = 0;
if (isset($_GET['pageNum_dbmail_list'])) {
    $pageNum_dbmail_list = $_GET['pageNum_dbmail_list'];
}
$startRow_dbmail_list = $pageNum_dbmail_list * $maxRows_dbmail_list;;
$query_dbmail_list = "SELECT * FROM mail_issues ORDER BY date_create DESC";
$query_limit_dbmail_list = sprintf("%s LIMIT %d, %d", $query_dbmail_list, $startRow_dbmail_list, $maxRows_dbmail_list);
$dbmail_list = el_dbselect($query_limit_dbmail_list, 0, $dbmail_list, 'result', true);
$row_dbmail_list = el_dbfetch($dbmail_list);

if (isset($_GET['totalRows_dbmail_list'])) {
    $totalRows_dbmail_list = $_GET['totalRows_dbmail_list'];
} else {
    $all_dbmail_list = mysqli_query($dbconn, $query_dbmail_list);
    $totalRows_dbmail_list = mysqli_num_rows($all_dbmail_list);
}
$totalPages_dbmail_list = ceil($totalRows_dbmail_list / $maxRows_dbmail_list) - 1;

$queryString_dbmail_list = "";
if (!empty($_SERVER['QUERY_STRING'])) {
    $params = explode("&", $_SERVER['QUERY_STRING']);
    $newParams = array();
    foreach ($params as $param) {
        if (stristr($param, "pageNum_dbmail_list") == false &&
            stristr($param, "totalRows_dbmail_list") == false) {
            array_push($newParams, $param);
        }
    }
    if (count($newParams) != 0) {
        $queryString_dbmail_list = "&" . htmlentities(implode("&", $newParams));
    }
}
$queryString_dbmail_list = sprintf("&totalRows_dbmail_list=%d%s", $totalRows_dbmail_list, $queryString_dbmail_list);
?>
<html>
<head>
    <title>Список выпусков рассылки</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style type="text/css">
        <!--
        .style1 {
            color: #009900;
            font-size: 18px;
            font-weight: bold;
        }

        .style2 {
            color: #FF0000
        }

        -->
    </style>
    <script language="JavaScript" type="text/JavaScript">
        function MM_openBrWindow(theURL, winName, features, myWidth, myHeight, isCenter) { //v3.0
            if (window.screen) if (isCenter) if (isCenter == "true") {
                var myLeft = (screen.width - myWidth) / 2;
                var myTop = (screen.height - myHeight) / 2;
                features += (features != '') ? ',' : '';
                features += ',left=' + myLeft + ',top=' + myTop;
            }
            window.open(theURL, winName, features + ((features != '') ? ',' : '') + 'width=' + myWidth + ',height=' + myHeight);
        }

        function fill(id, name) {
            var error = 0;
            var errmess = "";
            var id1 = new Array;
            var name1 = new Array;
            id1 = id;
            name1 = name;
            for (var i = 0; i < id1.length; i++) {
                if (document.getElementById(id1[i]).value == "") {
                    errmess += 'Заполните поле "' + name1[i] + '"\n';
                    error++;
                }
            }
            if (error != 0) {
                alert(errmess);
                return false;
            } else {
                return true;
            }
        }

        function MM_findObj(n, d) { //v4.01
            var p, i, x;
            if (!d) d = document;
            if ((p = n.indexOf("?")) > 0 && parent.frames.length) {
                d = parent.frames[n.substring(p + 1)].document;
                n = n.substring(0, p);
            }
            if (!(x = d[n]) && d.all) x = d.all[n];
            for (i = 0; !x && i < d.forms.length; i++) x = d.forms[i][n];
            for (i = 0; !x && d.layers && i < d.layers.length; i++) x = MM_findObj(n, d.layers[i].document);
            if (!x && d.getElementById) x = d.getElementById(n);
            return x;
        }

        function checkdel(mail_list) {
            var OK = confirm('Вы действительно хотите удалить выпуск "' + mail_list + '" ?');
            if (OK) {
                return true
            } else {
                return false
            }
        }

        function vis_button() {
            var sel = document.getElementById("type");
            var but = document.getElementById("ButtonHTML");
            var trow = document.getElementById("coderow");
            ;
            if (sel.options[sel.selectedIndex].value == "HTML") {
                but.style.display = "block";
                trow.style.display = "block";
            } else {
                but.style.display = "none";
                trow.style.display = "none";
            }
        }

        function show_help() {
            var h = document.getElementById("help");
            if (h.style.display == "none") {
                h.style.display = "block";
            } else {
                h.style.display = "none";
            }
        }
    </script>
    <link href="/editor/style.css" rel="stylesheet" type="text/css">
</head>

<body>
<h4 align="center">Управление выпусками рассылки </h4>
<a href="#add" class="style1">Добавить выпуск </a><br>
<br>
<? if ($totalRows_dbmail_list > 0) {
    if ($totalRows_dbmail_list > $maxRows_dbmail_list) {
        ?>
        <table border="0" width="50%" align="center">
            <tr>
                <td width="20%" align="center">
                    <?php if ($pageNum_dbmail_list > 0) { // Show if not first page ?>
                        <a href="<?php printf("%s?pageNum_dbmail_list=%d%s", $currentPage, 0, $queryString_dbmail_list); ?>">В начало </a>
                    <?php } // Show if not first page
                    ?>
                </td>
                <td width="20%" align="center">
                    <?php if ($pageNum_dbmail_list > 0) { // Show if not first page ?>
                        <a href="<?php printf("%s?pageNum_dbmail_list=%d%s", $currentPage, max(0, $pageNum_dbmail_list - 1), $queryString_dbmail_list); ?>">Назад</a>
                    <?php } // Show if not first page
                    ?>
                </td>
                <td width="20%" align="center"><? $page = 1;
                    $pagen = 0;
                    $countpage = $totalRows_dbmail_list / $maxRows_dbmail_list;
                    if ($countpage > 1) {
                        do {
                            if ($pageNum_dbmail_list != $pagen) {
                                echo "<a href=" . $_SERVER['SCRIPT_NAME'] . "?pageNum_dbmail_list=" . $pagen . "&totalRows_dbmail_list=" . $totalRows_dbmail_list . "&cat=" . $cat . ">" . $page . "</a>&nbsp;&nbsp;";
                            } else {
                                echo "<b>" . $page . "</b>&nbsp;&nbsp;";
                            }
                            $page++;
                            $pagen++;
                            $countpage--;
                        } while ($countpage >= 0);
                    }
                    ?></td>
                <td width="20%" align="center">
                    <?php if ($pageNum_dbmail_list < $totalPages_dbmail_list) { // Show if not last page ?>
                        <a href="<?php printf("%s?pageNum_dbmail_list=%d%s", $currentPage, min($totalPages_dbmail_list, $pageNum_dbmail_list + 1), $queryString_dbmail_list); ?>">Вперед</a>
                    <?php } // Show if not last page
                    ?>
                </td>
                <td width="20%" align="center">
                    <?php if ($pageNum_dbmail_list < $totalPages_dbmail_list) { // Show if not last page ?>
                        <a href="<?php printf("%s?pageNum_dbmail_list=%d%s", $currentPage, $totalPages_dbmail_list, $queryString_dbmail_list); ?>">В конец </a>
                    <?php } // Show if not last page
                    ?>
                </td>
            </tr>
        </table>
        <br>
    <? } ?>
    <table width="95%" border="0" align="center" cellpadding="6" cellspacing="0" class="el_tbl">
        <tr>
            <td><strong>ID</strong></td>
            <td><strong>Название</strong></td>
            <td><strong>Дата создания</strong><strong>/рассылки</strong></td>
            <td><strong>Автор</strong></td>
            <td><strong>Действия</strong></td>
        </tr>
        <?php do { ?>
            <tr id="string<?= $row_dbmail_list['id']; ?>"
                onMouseOver='document.getElementById("string<?= $row_dbmail_list['id']; ?>").style.backgroundColor="#DEE7EF"'
                onMouseOut='document.getElementById("string<?= $row_dbmail_list['id']; ?>").style.backgroundColor=""'>
                <form name=frm<?php echo $row_dbmail_list['id']; ?> method="post">
                    <td><?php echo $row_dbmail_list['id']; ?></td>
                    <td><?php echo $row_dbmail_list['title']; ?></td>
                    <td><?php echo $row_dbmail_list['date_create']; ?>               <?= ($row_dbmail_list['date_send'] != "") ? "/ " . $row_dbmail_list['date_send'] : " <font color=red>Еще не рассылался</font>"; ?></td>
                    <td><?php echo $row_dbmail_list['sender']; ?></td>
                    <td>
                        <nobr><input name="Submit" type="button" id="Submit" value="Удалить" class="but"
                                     onClick="if(checkdel('<?php echo htmlspecialchars($row_dbmail_list['title'], ENT_QUOTES) ?>')){action.value='del'; frm<?= $row_dbmail_list['id'] ?>.submit();}"
                                     style="width:60px">
                            <input name="Button" type="button"
                                   onClick="MM_openBrWindow('/editor/modules/subscribe/mail_issueedit.php?id=<?php echo $row_dbmail_list['id']; ?>&mode=<?= ($row_dbmail_list['type'] == "HTML") ? "html" : "text" ?>','edit','scrollbars=yes,resizable=yes, status=no','760','600','true')"
                                   value="Редактировать" class="but" style="width:100px">
                            <input type="submit" name="Submit2" value="Разослать" class="but" onClick="action.value='send'" style="width:70px">
                            <input name="id" type="hidden" id="id" value="<?php echo $row_dbmail_list['id']; ?>"></nobr>
                        <input name="action" type="hidden" id="action"></td>
                </form>
            </tr>
        <?php } while ($row_dbmail_list = el_dbfetch($dbmail_list)); ?>
    </table>
    <? if ($totalRows_dbmail_list > $maxRows_dbmail_list) { ?>
        <br>
        <table border="0" width="50%" align="center">
            <tr>
                <td width="20%" align="center">
                    <?php if ($pageNum_dbmail_list > 0) { // Show if not first page ?>
                        <a href="<?php printf("%s?pageNum_dbmail_list=%d%s", $currentPage, 0, $queryString_dbmail_list); ?>">В начало </a>
                    <?php } // Show if not first page ?>
                </td>
                <td width="20%" align="center">
                    <?php if ($pageNum_dbmail_list > 0) { // Show if not first page ?>
                        <a href="<?php printf("%s?pageNum_dbmail_list=%d%s", $currentPage, max(0, $pageNum_dbmail_list - 1), $queryString_dbmail_list); ?>">Назад</a>
                    <?php } // Show if not first page ?>
                </td>
                <td width="20%" align="center"><? $page = 1;
                    $pagen = 0;
                    $countpage = $totalRows_dbmail_list / $maxRows_dbmail_list;
                    if ($countpage > 1) {
                        do {
                            if ($pageNum_dbmail_list != $pagen) {
                                echo "<a href=" . $_SERVER['SCRIPT_NAME'] . "?pageNum_dbmail_list=" . $pagen . "&totalRows_dbmail_list=" . $totalRows_dbmail_list . "&cat=" . $cat . ">" . $page . "</a>&nbsp;&nbsp;";
                            } else {
                                echo "<b>" . $page . "</b>&nbsp;&nbsp;";
                            }
                            $page++;
                            $pagen++;
                            $countpage--;
                        } while ($countpage >= 0);
                    }
                    ?></td>
                <td width="20%" align="center">
                    <?php if ($pageNum_dbmail_list < $totalPages_dbmail_list) { // Show if not last page ?>
                        <a href="<?php printf("%s?pageNum_dbmail_list=%d%s", $currentPage, min($totalPages_dbmail_list, $pageNum_dbmail_list + 1), $queryString_dbmail_list); ?>">Вперед</a>
                    <?php } // Show if not last page ?>
                </td>
                <td width="20%" align="center">
                    <?php if ($pageNum_dbmail_list < $totalPages_dbmail_list) { // Show if not last page ?>
                        <a href="<?php printf("%s?pageNum_dbmail_list=%d%s", $currentPage, $totalPages_dbmail_list, $queryString_dbmail_list); ?>">В конец </a>
                    <?php } // Show if not last page ?>
                </td>
            </tr>
        </table>
        <br><? }
} else {
    echo "<h5 align=center>Нет ни одного выпуска.</h5>";
} ?>


<form action="<?php echo $editFormAction; ?>" method="POST" name="add" onSubmit="return fill(['title', 'body'], ['Заголовок', 'Текст'])"
      enctype="multipart/form-data">
    <h4 align="center"><a name="add"></a>Новый выпуск </h4>
    <table width="80%" border="0" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
        <tr>
            <td align="right">Заголовок:</td>
            <td><input name="title" type="text" id="title" size="50"></td>
        </tr>
        <tr>
            <td align="right">Текст:</td>
            <td>
                <textarea name="body" cols="70" rows="15" id="body"></textarea>
                <br>
                <input name="ButtonHTML" type="button" class="but"
                       onClick="MM_openBrWindow('/editor/newseditor.php?field=body&form=add','editor','','590','600','true')" value="Визуальный редактор">
            </td>
        </tr>
        <?
        ;
        $query_template = "SELECT * FROM mail_templates ORDER BY id DESC";
        $template = el_dbselect($query_template, 0, $template, 'result', true);
        $row_template = el_dbfetch($template);
        ?>
        <tr>
            <td align="right" valign="top">Шаблон:</td>
            <td>
                <select name="template" id="template">
                    <? do { ?>
                        <option value="<?= $row_template['id'] ?>"><?= $row_template['name'] ?></option>
                    <? } while ($row_template = el_dbfetch($template)); ?>
                </select>
            </td>
        </tr>
        <?
        ;
        $query_themes = "SELECT * FROM mail_themes ORDER BY id DESC";
        $themes = el_dbselect($query_themes, 0, $themes, 'result', true);
        $row_themes = el_dbfetch($themes);
        ?>
        <tr>
            <td align="right" valign="top">Тема:</td>
            <td>
                <?
                echo '
	<table width="100%" border="0" cellpadding="5" cellspacing="0">
	';
                do {
                    echo '<tr><td valign=top><input type="checkbox" name="themes[' . $row_themes['id'] . ']" value="' . $row_themes['id'] . '"> ' . $row_themes['name'] . '</td></tr>
	';
                } while ($row_themes = el_dbfetch($themes));
                echo '
	</table>
	';
                ?>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><input name="Submit" type="submit" value="Добавить" class="but"></td>
        </tr>
    </table>
    <input type="hidden" name="MM_insert" value="add">
</form>
<br>
</body>
</html>
<?php
mysqli_free_result($dbmail_list);
?>
