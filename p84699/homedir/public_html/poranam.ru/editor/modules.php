<?php require_once('../Connections/dbconn.php'); ?>
<?PHP $requiredUserLevel = array(0, 1);
include($_SERVER['DOCUMENT_ROOT'] . "/editor/secure/secure.php");
(isset($submit)) ? $work_mode = "write" : $work_mode = "read";
el_reg_work($work_mode, $login, $_GET['cat']);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if (isset($_POST['delid'])) {
    $d = el_dbselect("SELECT * FROM modules WHERE id='" . $_POST['delid'] . "'", 0, $d, 'row');
    if (substr_count($d['type'], 'catalog') == 0) {
        if (!unlink($_SERVER['DOCUMENT_ROOT'] . '/modules/' . $d['type'] . '.php')) {
            echo '<script>alert("Не удается удалить файл модуля \"' . $d['name'] . '\"!")</script>';//el_deldir()
        } else {
            el_dbselect("DELETE FROM modules WHERE id='" . $_POST['delid'] . "'", 0, $res);
            echo '<script>alert("Модуль \"' . $d['name'] . '\" удален!")</script>';
        }
    } else {
        el_dbselect("DELETE FROM modules WHERE id='" . $_POST['delid'] . "'", 0, $res);
        echo '<script>alert("Модуль \"' . $d['name'] . '\" удален!")</script>';
    }
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "update")) {
    $updateSQL = sprintf("UPDATE modules SET status=%s, `path`=%s, sort=%s WHERE id=%s",
        GetSQLValueString(isset($_POST['status']) ? "true" : "", "defined", "'Y'", "'N'"),
        GetSQLValueString($_POST['path'], "text"),
        GetSQLValueString($_POST['sort'], "int"),
        GetSQLValueString($_POST['id'], "int"));;
    $Result1 = el_dbselect($updateSQL, 0, $Result1, 'result', true);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "add")) {
    if (!empty($_FILES['file']['name'])) {
        $targetPath = $_SERVER['DOCUMENT_ROOT'].'/modules/';
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $targetPath . $_FILES['file']['name'])) {
            if (!copy($_FILES['file']['tmp_name'], $targetPath . $_FILES['file']['name'])) {
                echo "<script>alert('Не удалось закачать файл " . $_FILES['file']['name'] . "!')</script>";
                return false;
            }
        }
    }
    $insertSQL = sprintf("INSERT INTO modules (type, name, `path`, sort) VALUES (%s, %s, %s, %s)",
        GetSQLValueString($_POST['type'], "text"),
        GetSQLValueString($_POST['name'], "text"),
        GetSQLValueString($_POST['path'], "text"),
        GetSQLValueString($_POST['sort'], "int"));
    $Result1 = el_dbselect($insertSQL, 0, $Result1, 'result', true);
};
$query_modules = "SELECT * FROM modules ORDER BY sort ASC";
$modules = el_dbselect($query_modules, 0, $modules, 'result', true);
$row_modules = el_dbfetch($modules);
$totalRows_modules = mysqli_num_rows($modules);
?>
<html>
<head>
    <title>Модули</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="style.css" rel="stylesheet" type="text/css">
    <script src="/js/jquery-1.11.0.min.js"></script>
    <script src="/js/tooltip.js"></script>
    <script>
        $(document).ready(function () {
            $('*').tooltip({showURL: false});
            $(".settings").on("click", function (e) {
                e.preventDefault();
                var sid = $(this).data("value");
                top.MM_openBrWindow('modules_settings.php?settings=' + sid + '&mode=global', 'metainfo', 'scrollbars=yes,resizable=yes', '650', '450', 'true')
            });
        });

        function del(id, name) {
            var OK = confirm("Вы действительно хотите удалить модуль \"" + name + "\"");
            if (OK) {
                document.delForm.delid.value = id;
                document.delForm.submit();
            }
        }
    </script>
    <style type="text/css">
        <!--
        .notetable {
            background-color: #FFFFEC;
        }

        .text1 {
            font-size: 12px;
            color: #000000;
        }

        .el_tbl tr td:first-child .material-icons{
           opacity: .4;
        }
        -->
    </style>
</head>

<body>
<form name="delForm" method="post"><input type="hidden" name="delid"></form>
<table width="50%" border=0 align="center" cellpadding=0 cellspacing=0>
    <tr>
        <td width="7"><img height=7 alt="" src="img/inc_ltc.gif" width=7></td>
        <td background="img/inc_tline.gif"><img height=1 alt="" src="img/1.gif" width=1></td>
        <td width="7"><img height=7 alt="" src="img/inc_rtc.gif" width=7></td>
    </tr>
    <tr>
        <td width="7" background="img/inc_lline.gif"><img height=1 alt="" src="img/1.gif" width=1></td>
        <td valign=top class="notetable">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="100%">Пожалуйста, не меняйте настроек, если Вы <strong>не</strong> являетесь специалистом!</td>
                </tr>
            </table>
        </td>
        <td width="7" background="img/inc_rline.gif"><img height=1 alt="" src="img/1.gif" width=1></td>
    </tr>
    <tr>
        <td width="7"><img height=7 alt="" src="img/inc_lbc.gif" width=7></td>
        <td background="img/inc_bline.gif"><img height=1 alt="" src="img/1.gif" width=1></td>
        <td width="7"><img height=7 alt="" src="img/inc_rbc.gif" width=7></td>
    </tr>
</table>
<h4 align="center">Программные модули </h4>
<table width="90%" border="0" align="center" cellpadding="3" cellspacing="0">
    <tr>
        <th>Тип</th>
        <th width="22%">Название</th>
        <th width="17%">Код</th>
        <th width="19%">Путь</th>
        <th width="23%">Номер</th>
        <th width="19%">Действия</th>
    </tr>
</table>
<?php do { ?>
    <form method="POST" action="<?php echo $editFormAction; ?>" name="update">
        <table width="90%" border="0" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
            <tr>
                <td>
                    <?
                    $tIcon = 'extension';
                    $tColor = '#73c585';
                    if(substr_count($row_modules['type'], 'catalog') > 0){
                        if($row_modules['is_register'] == '1'){
                            $tIcon = 'folder';//menu_book
                            $tColor = '#c4c4c4';
                        }else{
                            $tIcon = 'ballot';
                            $tColor = '#f1967b';
                        }
                    }
                    ?>
                    <i class="material-icons" style="color:<?=$tColor?>"><?=$tIcon?></i>
                </td>
                <td width="37%"><strong><?php echo $row_modules['name']; ?></strong></td>
                <td width="18%"><em><?php echo $row_modules['type']; ?></em></td>
                <td width="14%" align="right"><input name="path" type="text" id="path" value="<?php if (strlen($row_modules['path']) > 0) {
                        echo $row_modules['path'];
                    } else {
                        echo "modules/" . $row_modules['type'];
                    } ?>" size="20"></td>
                <td width="3%"><input name="sort" type="text" id="sort" value="<?php echo $row_modules['sort']; ?>" size="3"></td>
                <td width="18%" align="right"><?php if (!(strcmp($row_modules['status'], "Y"))) {
                        echo "<font color=green>Установлен</font>";
                    } else {
                        echo "<font color=red>Не установлен</font>";
                    } ?>
                    <input name="status" type="checkbox" id="status" value="checkbox" <?php if (!(strcmp($row_modules['status'], "Y"))) {
                        echo "checked";
                    } ?>>
                    <input name="id" type="hidden" id="id" value="<?php echo $row_modules['id']; ?>"></td>
                <td width="10%">
                    <?php
                    if (is_file($_SERVER['DOCUMENT_ROOT'] . '/editor/e_modules/modules_settings/' . $row_modules['type'] . '.php') ||
                        is_file($_SERVER['DOCUMENT_ROOT'] . '/editor/e_modules/modules_settings/' . str_replace('catalog', '', $row_modules['type']) . '.php')) {
                        ?>
                        <i class="material-icons settings" title="Глобальные настройки модуля" data-value="<?= str_replace('catalog', '', $row_modules['type']) ?>">settings</i>
                        <?php
                    }
                    ?>
                    <i class="material-icons" title="Сохранить изменения" onclick='$(this).parents("form").trigger("submit")'>save</i>
                    <i class="material-icons" title="Удалить модуль" onClick="del(<?= $row_modules['id'] ?>, '<?= $row_modules['name'] ?>')">delete_forever</i>
                </td>
            </tr>
            <input type="hidden" name="MM_update" value="update">
        </table>
    </form>
<?php } while ($row_modules = el_dbfetch($modules)); ?><br>

<form method="POST" action="<?php echo $editFormAction; ?>" name="add">
    <h5 align="center">Новый модуль</h5>
    <table width="90%" border="0" align="center" cellpadding="3" cellspacing="0" class="el_tbl">
        <tr>
            <td width="15%">Название:</td>
            <td width="85%"><input name="name" type="text" id="name"></td>
        </tr>
        <tr>
            <td>Код:</td>
            <td><input name="type" type="text" id="type"></td>
        </tr>
        <tr>
            <td>Путь:</td>
            <td><input name="path" type="text" id="path"></td>
        </tr>
        <tr>
            <td>Файл:</td>
            <td><input name="file" type="file" id="file"></td>
        </tr>
        <tr>
            <td>Номер:</td>
            <td><input name="sort" type="text" id="sort" size="3"></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><input name="Submit2" type="submit" class="but" value="Добавить"></td>
        </tr>
    </table>
    <input type="hidden" name="MM_insert" value="add">
</form>
<p>&nbsp;</p>
</body>
</html>
<?php
mysqli_free_result($modules);
?>
