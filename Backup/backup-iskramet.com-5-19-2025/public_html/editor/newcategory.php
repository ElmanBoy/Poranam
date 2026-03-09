<?php
session_start();
require_once('../Connections/dbconn.php');
//error_reporting(E_ALL);
$requiredUserLevel = array(0, 1, 2);
include($_SERVER['DOCUMENT_ROOT'] . "/editor/secure/secure.php");

$site_id = (intval($_GET['site_id']) > 0) ? intval($_GET['site_id']) : $_SESSION['site_id'];
$cat_type = (intval($_GET['cat_type']) == 0) ? 1 : $_GET['cat_type'];
$err = 0;

if ($_SESSION['user_level'] > 1 && $site_id != $_SESSION['user_group']) {
    echo '<h4 style="color:red">У Вас недостаточно прав для работы с этим сайтом</h4>';
    exit();
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . $_SERVER['QUERY_STRING'];
}
//create_cat($_GET['cat']);
$cat_adv = el_dbselect("SELECT * FROM cat WHERE id='" . $_GET['parentid'] . "'", 0, $cat_adv, 'row');
$cat_adv_pach = el_dbselect("SELECT * FROM cat WHERE id='" . $cat_adv['parent'] . "'", 0, $cat_adv_pach, 'row');

#################################################################################################################
$foldexist = 0;
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
    $parid = $_POST['parent'];
    if (function_exists('el_translit')) {
        $_POST['path'] = el_translit($_POST['path']);
    }
    $parentfolder = el_dbselect("select * from cat where id='$parid'", 0, $res, 'result', true);
    $parentfold = el_dbfetch($parentfolder);
    if ($parentfold['path']) {
        $parentf = $parentfold['path'];
    } else {
        $parentf = "";
    }
    $newpath = $parentf . "/" . $_POST['path'];

    $exist = el_dbselect("SELECT * FROM cat WHERE path = '$newpath' AND site_id = $site_id", 0, $exist, 'result', true);
    $foldexist = el_dbnumrows($exist);
}
if ($foldexist == 0) {
    if (!$_POST['menu']) {
        $_POST['menu'] = "Y";
    }
    //Если установлен раздел не учавствует в создание url
    if (strlen($cat_adv['nourl']) > 0) {
        $parent_adv = $_GET['parentid'];
    } else {
        $parent_adv = $_POST['parent'];
    }
    if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
        $insertSQL = sprintf("INSERT INTO cat (site_id, parent, name, `path`, menu, ptext, sort, cat_type) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($site_id, "int"),
            GetSQLValueString($parent_adv, "int"),
            GetSQLValueString($_POST['name'], "text"),
            GetSQLValueString($newpath, "text"),
            GetSQLValueString($_POST['menu'], "text"),
            GetSQLValueString($_POST['ptext'], "text"),
            GetSQLValueString($_POST['sort'], "int"),
            GetSQLValueString($cat_type, "int"));

        $Result1 = el_dbselect($insertSQL, 0, $res, 'result', true);
        //Определяем id новой записи
        $parid = $_POST['parent'];
        $parentfolder = el_dbselect("SELECT * FROM cat WHERE path='$newpath' AND site_id = $site_id", 0, $res, 'result', true);
        $parentfold = el_dbfetch($parentfolder);
        $idnew = intval($parentfold['id']);
        $ucid = el_dbselect("UPDATE cat SET cat_id = $idnew WHERE id = $idnew", 0, $ucid, 'result', true);
        mysqli_free_result($parentfolder);
    }
    if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
        $insertSQL = sprintf("INSERT INTO content (site_id, cat, `path`, text, caption, title, description, kod, template) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($site_id, "int"),
            GetSQLValueString($idnew, "int"),
            GetSQLValueString($newpath, "text"),
            GetSQLValueString($_POST['contenttext'], "text"),
            GetSQLValueString($_POST['name'], "text"),
            GetSQLValueString($_POST['name'], "text"),
            GetSQLValueString($_POST['text'], "text"),
            GetSQLValueString($_POST['kod'], "text"),
            GetSQLValueString($_POST['template'], "text"));

        $Result1 = el_dbselect($insertSQL, 0, $res, 'result', true);
    }
    el_log('Создан раздел &laquo;' . $_POST['name'] . '&raquo;', 1);
    el_clearcache('menu');
    el_genSiteMap();

} else {
    $rexist = el_dbfetch($exist);
    $err++;
    echo '<script>alert("Раздел с папкой \"' . $newpath . '\" уже назначен разделу \"' . $rexist['name'] . '\".")</script>';
}
#################################################################################################################


$query_cat = "SELECT * FROM cat WHERE site_id=$site_id";
$cat = el_dbselect($query_cat, 0, $cat, 'result', true);
$row_cat = el_dbfetch($cat);
$totalRows_cat = mysqli_num_rows($cat);


$query_typepage = "SELECT * FROM modules ".(($cat_type == 1) ? "WHERE is_register = 0" : "")." ORDER BY sort ASC";
$typepage = el_dbselect($query_typepage, 0, $cat, 'result', true);
$row_typepage = el_dbfetch($typepage);
$totalRows_typepage = mysqli_num_rows($typepage);


$query_template = "SELECT * FROM template WHERE `master`<>1";
$template = el_dbselect($query_template, 0, $template, 'result', true);
$row_template = el_dbfetch($template);
$totalRows_template = mysqli_num_rows($template);

$colname_parent = "1";
if (isset($_GET['parentid'])) {
    $colname_parent = (get_magic_quotes_gpc()) ? $_GET['parentid'] : addslashes($_GET['parentid']);
}

$query_parent = sprintf("SELECT id, name, nourl FROM cat WHERE id = %s", $colname_parent);
$parent = el_dbselect($query_parent, 0, $parent, 'result', true);
$row_parent = el_dbfetch($parent);
$totalRows_parent = mysqli_num_rows($parent);


?>

<html>
<head>
    <title>Создание нового раздела в меню</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="/js/css/start/jquery.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="/js/jquery.js"></script>
    <script type="text/javascript" src="/js/jquery-ui.js"></script>
    <script language="JavaScript" type="text/JavaScript">
        <!--
        function MM_goToURL() { //v3.0
            var i, args = MM_goToURL.arguments;
            document.MM_returnValue = false;
            for (i = 0; i < (args.length - 1); i += 2) eval(args[i] + ".location='" + args[i + 1] + "'");
        }

        function MM_openBrWindow(theURL, winName, features, myWidth, myHeight, isCenter) { //v3.0
            if (window.screen) if (isCenter) if (isCenter == "true") {
                var myLeft = (screen.width - myWidth) / 2;
                var myTop = (screen.height - myHeight) / 2;
                features += (features != '') ? ',' : '';
                features += ',left=' + myLeft + ',top=' + myTop;
            }
            window.open(theURL, winName, features + ((features != '') ? ',' : '') + 'width=' + myWidth + ',height=' + myHeight);
        }

        function checkForm() {
            if (form1.name.value.length == 0) {
                alert("Укажите название раздела!");
                return false
            } else if (form1.path.value.length == 0) {
                alert("Укажите название новой папки!");
                return false;
            } else {
                return true;
            }
        }


        String.prototype.translit = (function () {
            var L = {
                    'А': 'a', 'а': 'a', 'Б': 'b', 'б': 'b', 'В': 'v', 'в': 'v', 'Г': 'g', 'г': 'g',
                    'Д': 'd', 'д': 'd', 'Е': 'e', 'е': 'e', 'Ё': 'yo', 'ё': 'yo', 'Ж': 'zh', 'ж': 'zh',
                    'З': 'z', 'з': 'z', 'И': 'i', 'и': 'i', 'Й': 'y', 'й': 'y', 'К': 'k', 'к': 'k',
                    'Л': 'l', 'л': 'l', 'М': 'm', 'м': 'm', 'Н': 'n', 'н': 'n', 'О': 'o', 'о': 'o',
                    'П': 'p', 'п': 'p', 'Р': 'r', 'р': 'r', 'С': 's', 'с': 's', 'Т': 't', 'т': 't',
                    'У': 'u', 'у': 'u', 'Ф': 'f', 'ф': 'f', 'Х': 'kh', 'х': 'kh', 'Ц': 'ts', 'ц': 'ts',
                    'Ч': 'ch', 'ч': 'ch', 'Ш': 'sh', 'ш': 'sh', 'Щ': 'sch', 'щ': 'sch',
                    'Ы': 'y', 'ы': 'y', 'Э': 'e', 'э': 'e', 'Ю': 'yu', 'ю': 'yu', 'ь': '', 'Ь': '', 'ъ': '', 'Ъ': '',
                    'Я': 'ya', 'я': 'ya', ' ': '-', '?': '', ',': '-', '.': '-'
                },
                r = '',
                k;
            for (k in L) r += k;
            r = new RegExp('[' + r + ']', 'g');
            k = function (a) {
                return a in L ? L[a] : '';
            };
            return function () {
                return this.replace(r, k);
            };
        })();
        $(document).ready(function (e) {
            $("input[name='name']").keyup(function (e) {
                $("input[name='path']").val($(this).val().translit())
            });
            $("#allCheckGood").click(function () {
                $(".checkGood").prop("checked", $(this).prop("checked"));
            })
        });

        //-->
    </script>
    <link href="style.css" rel="stylesheet" type="text/css">
    <style type="text/css">
        <!--
        .style1 {
            font-size: 12px;
            font-style: italic;
            color: #006633;
        }

        .style2 {
            color: #FF0000;
            font-weight: bold;
        }

        .style3 {
            color: #FF0000
        }

        table tr td {
            padding: 5px;
        }

        -->
    </style>
</head>

<body bgcolor="#FFFFFF">
<h4> Создание страницы и определение ее параметров.</h4>
<? if (isset($_POST['Submit']) && $err == 0) { ?>
    <center><!-- <form action="new.php" method="post" name="sendnew" target="_self"> -->
        <?
        if ($foldexist == 0) {
            $parentid = intval($_POST['parent']);
            $catname = el_dbselect("select * from cat where id=$parentid AND site_id=$site_id", 0, $res);
            $row = el_dbfetch($catname);;
            $namecat = $row['name'];
            $name = $_POST["name"];
            ?>
            Создана страница <? echo "<b>&#8220;" . $name . "&#8221;</b>" ?> в разделе <? if ($_GET['parentid'] == 0) {
                $namecat = "Главное меню";
            }
            echo "<b>&#8220;" . $namecat . "&#8221;</b>"; ?> !
            <br>
            <input name="catname" type="hidden" id="catname" value="<?
            $catnameid = el_dbselect("select * from cat where name='$name' AND site_id=$site_id", 0, $res);
            $row = el_dbfetch($catnameid);;
            $idcat = $row['id'];
            echo $idcat;
            ?>">
            <input name="name" type="hidden" id="name" value="<?
            $name1 = $_POST["name"];
            echo $name1;
            ?>">
            <input name="namecat" type="hidden" id="namecat" value="<? echo $namecat; ?>">
            <br>
            <?php
        }
        ?>
        <input name="Submit2" type="button" class="but agree"
               onClick="MM_goToURL('self','newcategory.php?parentid=<?= intval($_POST['parent']) ?>&site_id=<?= $site_id ?>');return document.MM_returnValue"
               value="Создать еще">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input name="Next" type="button" class="but close" onClick="top.reloadFrame();top.closeDialog()"
               value="Закрыть">
        <!-- </form> -->
    </center>
<? }
if(!isset($_POST['Submit']) || $err > 0){ ?>
    <form method="POST" name="form1" action="<?php echo $editFormAction; ?>" onSubmit="return checkForm()">
        <table width="95%" align="center" class="el_tbl">
            <tr valign="baseline">
                <td align="right" valign="top" nowrap>Вставить в раздел:</td>
                <td><input type="hidden" name="parent" value="<? if (strlen($row_parent['nourl']) > 0) {
                        echo $cat_adv['parent'];
                    } else {
                        echo $_GET['parentid'];
                    } ?>" size="32">
                    <input name="contenttext" type="hidden" id="contenttext" value="">
                    &laquo; <strong><?php if ($_GET['parentid'] == 0) {
                            echo "Главное меню";
                        } else {
                            echo $row_parent['name'];
                        } ?></strong> &raquo;
                </td>
            </tr>
            <tr valign="baseline">
                <td align="right" nowrap>Название раздела<span class="style3">*</span>:</td>
                <td><input type="text" name="name" value="<?=htmlspecialchars($_POST['name'])?>" size="32"></td>
            </tr>
            <tr valign="baseline">
                <td align="right" valign="top" nowrap>Описание:<br>
                    <span class="style1">(не обязательное поле)</span></td>
                <td valign="top"><textarea name="ptext" cols="40" rows="5" id="ptext"><?=htmlspecialchars($_POST['ptext'])?></textarea>
                    <img
                            onClick="MM_openBrWindow('newcatditor.php?field=ptext','newcateditor','','785','625','true')"
                            src="img/code.gif" alt="HTML-редактор" width="21" height="20" border="0"
                            style="cursor:pointer; "></td>
            </tr>
            <tr valign="baseline">
                <td align="right" valign="top" nowrap>Название папки<span class="style3">*</span>:</td>
                <td valign="bottom"><input name="path" type="text" id="path" size="32" value="<?=htmlspecialchars($_POST['path'])?>"></td>
            </tr>
            <tr valign="baseline">
                <td align="right" valign="top" nowrap>Порядковый номер в меню:</td>
                <td valign="top"><input name="sort" type="text" id="sort" value="100" size="5"></td>
            </tr>
            <tr valign="baseline">
                <td align="right" valign="top" nowrap>Тип страницы:</td>
                <td valign="top"><select name="kod" id="kod">
                        <?php
                        do {
                            if ($row_typepage['status'] == "Y") {
                                $sel = ($row_typepage['type'] == $_POST['kod']) ? ' selected' : '';
                                ?>
                                <option value="<?php echo $row_typepage['type'] ?>"<?=$sel?>><?php echo $row_typepage['name'] ?></option>
                                <?php
                            }
                        } while ($row_typepage = el_dbfetch($typepage));
                        $rows = mysqli_num_rows($typepage);
                        if ($rows > 0) {
                            mysqli_data_seek($typepage, 0);
                            $row_typepage = el_dbfetch($typepage);
                        }
                        ?>
                    </select></td>
            </tr>
            <tr valign="baseline">
                <td align="right" valign="top" nowrap>Шаблон страницы:</td>
                <td valign="top"><select name="template" id="template">
                        <?php
                        do {
                            ?>
                            <option value="<?php echo $row_template['path'] ?>"
                                    <?= ($row_template['default'] == 1) ? 'selected' : '' ?>><?php echo $row_template['name'] ?></option>
                            <?php
                        } while ($row_template = el_dbfetch($template));
                        $rows = mysqli_num_rows($template);
                        if ($rows > 0) {
                            mysqli_data_seek($template, 0);
                            $row_template = el_dbfetch($template);
                        }
                        ?>
                    </select></td>
            </tr>
            <tr valign="baseline">
                <td align="right" nowrap>Не показывать в меню:</td>
                <td><p>
                        <input name="menu" type="checkbox" id="menu" value="N"<?=($_POST['menu'] == 'N') ? ' checked' : ''?>>
                    </p>
                </td>
            </tr>
            <tr valign="baseline">
                <td nowrap align="right"><br>
                    <input name="Submit" type="submit" class="but agree" value="Создать"></td>
                <td>

                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input name="closewin" type="button" class="but close" id="closewin"
                           onClick="top.reloadFrame();top.closeDialog()" value="Закрыть"></td>
            </tr>
        </table>
        <center><span class="style3">*</span>-поля отмеченный звездочкой заполняются обязательно.</center>
        <center><? if (strlen($row_parent['nourl']) > 0) {
                echo '"Этот раздел не участвует в построение URL внутренних страниц';
            } ?></center>
        <input type="hidden" name="MM_insert" value="form1">
    </form>

<? } ?>
</body>
</html>