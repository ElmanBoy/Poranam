<?php
require_once('../Connections/dbconn.php');
//error_reporting(E_ALL);
$requiredUserLevel = array(0, 1, 2);
include($_SERVER['DOCUMENT_ROOT'] . "/editor/secure/secure.php");
(isset($submit)) ? $work_mode = "write" : $work_mode = "read";
el_reg_work($work_mode, $login, $_GET['cat']);

$currentPage = $_SERVER["PHP_SELF"];

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
//Удаление пользователя
if ((isset($_POST['id_del'])) && ($_POST['id_del'] != "")) {
    $deleteSQL = sprintf("DELETE FROM phpSP_users WHERE primary_key=%s",
        GetSQLValueString($_POST['id_del'], "int"));;
    $Result1 = el_dbselect($deleteSQL, 0, $Result1, 'result', true);

}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "userstatus")) {
    $insertSQL = sprintf("INSERT INTO userstatus (name) VALUES (%s)",
        GetSQLValueString($_POST['name'], "text"));;
    $Result1 = el_dbselect($insertSQL, 0, $Result1, 'result', true);
}

if (isset($_POST['delGroup']) && $_POST['delGroup'] == '1') {
    el_dbselect("DELETE FROM userstatus WHERE id='" . intval($_POST['status']) . "'", 0, $res);
}

// Добавление нового пользователя
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "newuser")) {
    $pass = str_replace("$1$", "", crypt(md5($_POST['pass']), '$1$'));;
    $query_users = "SELECT * FROM phpSP_users WHERE user='" . $_POST['email'] . "'";
    $users = el_dbselect($query_users, 0, $users, 'result', true);
    if (mysqli_num_rows($users) < 1) {
        $bdayArr = explode('-', $_POST['birthday']);
        $validBday = $bdayArr[2] . '-' . $bdayArr[1] . '-' . $bdayArr[0];
        $insertSQL = sprintf("INSERT INTO phpSP_users (`user`, password, userlevel, usergroup, fio, email, birthday, ip, time_reg,  date_reg, phones) 
        VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($_POST['email'], "text"),
            GetSQLValueString($pass, "text"),
            GetSQLValueString($_POST['status'], "int"),
            GetSQLValueString($_POST['group'], "int"),
            GetSQLValueString($_POST['fio'], "text"),
            GetSQLValueString($_POST['email'], "text"),
            GetSQLValueString($validBday, "date"),
            GetSQLValueString($_SERVER['REMOTE_ADDR'], "text"),
            GetSQLValueString(date("H:i:s"), "date"),
            GetSQLValueString(date("Y-m-d"), "date"),
            GetSQLValueString($_POST['phones'], "text"));

        $Result1 = el_dbselect($insertSQL, 0, $Result1, 'result', true);

    } else {
        echo "<script language=javascript>alert('Пользователь с таким логином уже есть!\\nВыберите другой логин.')</script>";
    }
}

//Редактирование пользователя
if (isset($_POST['Edit'])) {
    if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "user")) {
        $query_users = "SELECT * FROM phpSP_users WHERE email='" . $_POST['email2'] . "'";
        $users = el_dbselect($query_users, 0, $users, 'result', true);
        $row_users = el_dbfetch($users);
        if (mysqli_num_rows($users) > 0 && $row_users['primary_key'] != $_POST['id']) {
            echo "<script language=javascript>alert('Пользователь с таким логином уже есть!\\nВыберите другой логин.')</script>";
        } else {
            if (strlen($_POST['pass2']) > 0) {
                if (strlen($_POST['pass2']) < 6) {
                    echo "<script language=javascript>alert('Пароль должен состоять из 6 и более символов.\\nНовый пароль не принят.')</script>";
                    $pass1 = $row_users['password'];
                } elseif (strlen($_POST['pass2']) >= 6) {
                    $pass1 = str_replace("$1$", "", crypt(md5($_POST['pass2']), '$1$'));
                }
            } else {
                $pass1 = $row_users['password'];
            }
            if (!isset($_POST['group'])) {
                $_POST['group'] = (isset($_POST['active'])) ? '3' : '0';
            }
            $updateSQL = sprintf("UPDATE phpSP_users SET `user`=%s, password=%s, userlevel=%s, usergroup=%s, clstatus=%s, fio=%s, email=%s, birthday=%s, INN=%s, post_adress=%s, dev_adress=%s, phones=%s, ip=%s WHERE primary_key=%s",
                GetSQLValueString($_POST['email2'], "text"),
                GetSQLValueString($pass1, "text"),
                GetSQLValueString($_POST['status'], "int"),
                GetSQLValueString($_POST['group'], "int"),
                GetSQLValueString($_POST['clstatus'], "text"),
                GetSQLValueString($_POST['fio2'], "text"),
                GetSQLValueString($_POST['email2'], "text"),
                GetSQLValueString($_POST['birthday2'], "date"),
                GetSQLValueString($_POST['INN'], "text"),
                GetSQLValueString($_POST['post_adress2'], "text"),
                GetSQLValueString($_POST['dev_adress'], "text"),
                GetSQLValueString($_POST['phones'], "text"),
                GetSQLValueString($_POST['ip'], "text"),
                GetSQLValueString($_POST['id'], "text"));

            $Result1 = el_dbselect($updateSQL, 0, $Result1, 'result', true);

            $mText = 'Здравствуйте, ' . $_POST['fio2'] . '!
			
Ваша учетная запись на сайте ' . $_SERVER['SERVER_NAME'] . ' активирована.
			
Добро пожаловать на http://' . $_SERVER['SERVER_NAME'] . ' !
			
			';
            if ($mode == '0') {
                require_once($_SERVER['DOCUMENT_ROOT'] . '/modules/htmlMimeMail.php');
                $mail = new htmlMimeMail();
                $mail->setText($mText);
                $mail->setReturnPath('info@' . str_replace('www.', '', $_SERVER['SERVER_NAME']));
                $mail->setFrom($_SERVER['SERVER_NAME'] . ' <info@' . str_replace('www.', '', $_SERVER['SERVER_NAME']) . '>');
                $mail->setBcc('flobus@mail.ru');
                $mail->setSubject('Регистрация на сайте ' . $_SERVER['SERVER_NAME']);
                $mail->setHeader('X-Mailer', 'HTML Mime mail');
                $mail->setTextCharset('UTF-8');
                $result = $mail->send(array($_POST['email2']));
                if ($result) {
                    echo "<script language=javascript>alert('Изменения сохранены.')</script>";
                } else {
                    echo "<script language=javascript>alert('Не удалось отправить письмо пользователю')</script>";
                }
            } else {
                echo "<script language=javascript>alert('Изменения сохранены!')</script>";
            }
        }
    }
}

$maxRows_users = 25;
$pageNum_users = 0;
if (isset($_GET['pageNum_users'])) {
    $pageNum_users = $_GET['pageNum_users'];
}
$startRow_users = $pageNum_users * $maxRows_users;

$subquery = array('primary_key > 1');
if (strlen($_GET['flogin']) > 0) {
    $subquery[] = "user LIKE '%" . $_GET['flogin'] . "%'";
}
if (strlen($_GET['factive']) > 0) {
    $subquery[] = ($_GET['factive'] == 0) ? "userlevel=0" : "userlevel>0";
}
if (strlen($_GET['fstatus']) > 0) {
    $subquery[] = "userlevel=" . intval($_GET['fstatus']);
}
if (strlen($_GET['ffio']) > 0) {
    $subquery[] = "fio LIKE '%" . $_GET['ffio'] . "%'";
}
if (strlen($_GET['forg']) > 0) {
    $subquery[] = "user LIKE '%" . $_GET['forg'] . "%'";
}
if (intval($_SESSION['user_group']) > 1) {
    $subquery[] = "usergroup = " . intval($_SESSION['user_group']);
}
if (count($subquery) > 0) {
    $filterquery = 'WHERE ' . implode(' AND ', $subquery);
};

$query_users = "SELECT * FROM phpSP_users $filterquery ORDER BY `primary_key` DESC";
$query_limit_users = sprintf("%s LIMIT %d, %d", $query_users, $startRow_users, $maxRows_users);
$users = el_dbselect($query_limit_users, 0, $users, 'result', true);
$row_users = el_dbfetch($users);

if (isset($_GET['totalRows_users'])) {
    $totalRows_users = $_GET['totalRows_users'];
} else {
    $all_users = mysqli_query($dbconn, $query_users);
    $totalRows_users = mysqli_num_rows($all_users);
}
$totalPages_users = ceil($totalRows_users / $maxRows_users) - 1;
$query_status = "SELECT * FROM userstatus WHERE level > 0 ORDER BY `level` ASC";
$status = el_dbselect($query_status, 0, $status, 'result', true);
$row_status = el_dbfetch($status);
$statusArray = array();
do {
    $statusArray[$row_status['level']] = $row_status['name'];
} while ($row_status = el_dbfetch($status));
if ($rows > 0) {
    mysqli_data_seek($status, 0);
    $row_status = el_dbfetch($status);
}

if (intval($_SESSION['user_group']) == 1) {
    $query_groups = "SELECT * FROM sites ORDER BY `short_name` ASC";
    $groups = el_dbselect($query_groups, 0, $groups, 'result', true);
    $row_groups = el_dbfetch($groups);
} else {
    $query_groups = "SELECT * FROM sites WHERE id=" . intval($_SESSION['user_group']);
    $groups = el_dbselect($query_groups, 0, $groups, 'row', true);
}

$queryString_users = "";
if (!empty($_SERVER['QUERY_STRING'])) {
    $params = explode("&", $_SERVER['QUERY_STRING']);
    $newParams = array();
    foreach ($params as $param) {
        if (stristr($param, "pageNum_users") == false &&
            stristr($param, "totalRows_users") == false
        ) {
            array_push($newParams, $param);
        }
    }
    if (count($newParams) != 0) {
        $queryString_users = "&" . htmlentities(implode("&", $newParams));
    }
}
$queryString_users = sprintf("&totalRows_users=%d%s", $totalRows_users, $queryString_users);
?>
<html>
<head>
    <title>Пользователи</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="style.css?v=sdfs" rel="stylesheet" type="text/css">
    <link href="/js/css/start/jquery.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="/js/jquery.js"></script>
    <script type="text/javascript" src="/js/jquery.maskedinput.js"></script>
    <script src="/js/tooltip.js"></script>
    <style type="text/css">
        <!--
        .notetable {
            background-color: #FFFFEC;
        }

        -->
    </style>
    <script language="javascript">
        function showinfo(obj) {
            var sdiv = "div_" + obj;
            var s = document.getElementById(sdiv);
            if (s.style.display == 'none') {
                s.style.display = 'block';
            } else {
                s.style.display = 'none';
            }
        }

        function del_user(id, name, user) {
            var OK = confirm("Вы уверены, что хотите удалить пользователя \"" + name + "\"");
            if (OK) {
                document.delform.id_del.value = id;
                document.delform.user_del.value = user;
                document.delform.submit();
            }
        }

        function del_group() {
            var id = document.deletegroup.status.options[document.deletegroup.status.selectedIndex].value;
            var name = document.deletegroup.status.options[document.deletegroup.status.selectedIndex].text;
            var OK = confirm("Вы уверены, что хотите удалить группу \"" + name + "\"");
            if (id > 1) {
                return (OK) ? true : false;
            } else {
                alert('Эту группу нельзя удалять!');
                return false;
            }
        }

        function generatePass(len) {
            var ints = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
            var chars = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
            var out = '';
            for (var i = 0; i < len; i++) {
                var ch = Math.random(1, 2);
                if (ch < 0.5) {
                    var ch2 = parseInt(Math.random(1, ints.length) * 10);
                    if (typeof ints[ch2] == "undefined") generatePass(len);
                    out += ints[ch2];
                } else {
                    var ch2 = Math.ceil(Math.random(1, chars.length) * 10);
                    if (typeof chars[ch2] == "undefined") generatePass(len);
                    out += chars[ch2];
                }
            }
            return out;
        }


        $(document).ready(function () {
            $('*').tooltip({showURL: false});
            $("input[type=tel]").mask('+7 (999) 999-99-99');
            $("#genPassLink").click(function(e){
                e.preventDefault();
                $('#pass').val(generatePass(10));
            });
        });
    </script>
</head>

<body>
<div class="user">
    <h5>Управление пользователями</h5>
    <div class="clear"></div>


    <form name="delform" method="post"><input type="hidden" name="id_del"><input type="hidden" name="user_del"></form>
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
                        <td width="100%">Перечень пользователей системы и зарегистрированных пользователей сайта.</td>
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

    <form method="get">
        <table border="0" align="center">
            <caption>Фильтр</caption>
            <tr>
                <td>статус
                    <select name="factive">
                        <option></option>
                        <option value="1"<?= ($_GET['factive'] == '1') ? ' selected' : '' ?>>Активирован</option>
                        <option value="0"<?= ($_GET['factive'] == '0') ? ' selected' : '' ?>>Не активирован</option>
                    </select>
                </td>
                <td>роль
                    <select name="fstatus">
                        <option></option>
                        <?php
                        if (el_dbnumrows($status) > 0) {
                            mysqli_data_seek($status, 0);
                            $row_status = el_dbfetch($status);
                        }
                        do {
                            $sel = ($_GET['fstatus'] == $row_status['id']) ? ' selected' : '';
                            ?>
                            <option
                                    value="<?php echo $row_status['id'] ?>" <?= ($row_status['id'] == $_POST['fstatus']) ? 'selected' : '' ?> <?= $sel
                            ?>><?php echo $row_status['name'] ?></option>
                            <?php
                        } while ($row_status = el_dbfetch($status));
                        $rows = mysqli_num_rows($status);
                        if ($rows > 0) {
                            mysqli_data_seek($status, 0);
                            $row_status = el_dbfetch($status);
                        }
                        ?>

                    </select>
                </td>
                <td>логин <input type="text" name="flogin" size="20" value="<?= $_GET['flogin'] ?>"></td>
                <td>ф.и.о. <input type="text" name="ffio" size="20" value="<?= $_GET['ffio'] ?>"></td>
                <!--td>организация <input type="text" name="forg" size="20" value="<?= $_GET['forg'] ?>"></td-->
                <td valign="bottom"><input type="submit" value=">>"></td>
            </tr>
        </table>
    </form>
    <br><?php if ($totalRows_users > 0) { ?>
        Пользователи с <?php echo($startRow_users + 1) ?> по <?php echo min($startRow_users + $maxRows_users, $totalRows_users) ?> из <?php echo $totalRows_users ?>
        <br>
        <table border="0" width="50%" align="center">
            <tr>
                <td width="23%" align="center">
                    <?php if ($pageNum_users > 0) { // Show if not first page ?>
                    <a href="<?php printf("%s?pageNum_users=%d%s", $currentPage, 0, $queryString_users); ?>">
                        <i class="material-icons">
                            arrow_left
                        </i>
                        <?php } // Show if not first page ?>
                </td>
                <td width="31%" align="center">
                    <?php if ($pageNum_users > 0) { // Show if not first page ?>
                        <a href="<?php printf("%s?pageNum_users=%d%s", $currentPage, max(0, $pageNum_users - 1), $queryString_users); ?>">
                            <i class="material-icons">
                                chevron_left
                            </i></a>
                    <?php } // Show if not first page ?>
                </td>
                <td width="23%" align="center">
                    <?php if ($pageNum_users < $totalPages_users) { // Show if not last page ?>
                        <a href="<?php printf("%s?pageNum_users=%d%s", $currentPage, min($totalPages_users, $pageNum_users + 1), $queryString_users); ?>"><i
                                    class="material-icons">
                                chevron_right
                            </i></a>
                    <?php } // Show if not last page ?>
                </td>
                <td width="23%" align="center">
                    <?php if ($pageNum_users < $totalPages_users) { // Show if not last page ?>
                        <a href="<?php printf("%s?pageNum_users=%d%s", $currentPage, $totalPages_users, $queryString_users); ?>">
                            <i class="material-icons">
                                arrow_right
                            </i></a>
                    <?php } // Show if not last page ?>
                </td>
            </tr>
        </table>

        <table width="100%" cellpadding="5" cellspacing="0" class="el_tbl">
            <tr>
                <th>ID</th>
                <th>Логин</th>
                <th>Активность</th>
                <th>Роль</th>
                <th>ФИО</th>
                <th>E-mail</th>
                <th>Дата регистрации</th>
                <th width="170">Действия</th>
            </tr>
            <?php do { ?>
                <tr>
                    <td width="10">
                        <?php echo $row_users['primary_key']; ?>
                    </td>
                    <td width="100">
                        <A href="#"
                           onClick="showinfo(<?php echo $row_users['primary_key']; ?>)"><?php echo $row_users['user']; ?></A>
                    </td>
                    <td width="70">
                        <?php if ($row_users['userlevel'] == 0) {
                            echo '<font color=red>Нет</font>';
                        } else {
                            echo "Активен";
                        } ?>
                    </td>
                    <td width="170">
                        <?php echo $statusArray[$row_users['userlevel']]; ?>
                    </td>
                    <td width="170">
                        <?php echo $row_users['fio']; ?>
                    </td>
                    <td width="60">
                        <a href="mailto:<?php echo $row_users['email']; ?>"><?php echo $row_users['email']; ?></a>
                    </td>
                    <td width="100">
                        <?php echo el_date($row_users['date_reg']) . '  ' . $row_users['time_reg'] ?>
                    </td>
                    <td width="200">
                        <input name="view" type="button" class="but" id="view" value="Подробнее"
                               onClick="showinfo(<?php echo $row_users['primary_key']; ?>)"> <input name="Delete"
                                                                                                    type="button"
                                                                                                    onClick="del_user(<?= $row_users['primary_key'] ?>, '<?= $row_users['fio'] ?>', '<?= $row_users['user'] ?>')"
                                                                                                    class="but"
                                                                                                    id="Delete2"
                                                                                                    value="Удалить">
                    </td>

                <tr>

                    <td colspan="9" class="user_none">

                        <div id="div_<?php echo $row_users['primary_key']; ?>" style="display:none">
                            <div class="user_inform">
                                <h3>Регистрационная информация: <span
                                            style="color:green"> <?php echo $row_users['user']; ?></span></h3>
                                <form method="POST" action="<?php echo $editFormAction; ?>" name="user">
                                    <input type="hidden" name="id" value="<?= $row_users['primary_key'] ?>">
                                    <table width="90%" height="100%" class="el_tbl">
                                        <? /*tr>
                                            <td width="25%">Логин:</td>
                                            <td width="75%"><input name="user" type="text" id="user"
                                                                   value="<?php echo $row_users['user']; ?>" size="30">
                                            </td>
                                        </tr*/ ?>
                                        <tr>
                                            <td>E-mail (логин):</td>
                                            <td><input name="email2" type="text" id="email2"
                                                       value="<?php echo $row_users['email']; ?>" size="40"></td>
                                        </tr>
                                        <tr>
                                            <td width="25%">ФИО:</td>
                                            <td width="75%"><input name="fio2" type="text" id="fio2"
                                                                   value="<?php echo $row_users['fio']; ?>" size="40">
                                                <input name="id" type="hidden" id="id"
                                                       value="<?php echo $row_users['primary_key']; ?>"></td>
                                        </tr>
                                        <tr>
                                            <td width="25%">Новый пароль:</td>
                                            <td width="75%"><input name="pass2" type="password" id="pass2" size="40">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Дата регистрации :</td>
                                            <td><?php echo el_date($row_users['date_reg']) . '  ' . $row_users['time_reg'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>IP при регистрации :</td>
                                            <td><?php echo $row_users['ip'] ?></td>
                                        </tr>
                                        <? /*tr>
                                            <td>Почтовый адрес :</td>
                                            <td><textarea name="post_adress2" cols="50"
                                                          id="post_adress2"><?php echo $row_users['post_adress']; ?></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Адрес доставки :</td>
                                            <td><textarea name="dev_adress" cols="50"
                                                          id="dev_adress"><?php echo $row_users['dev_adress']; ?></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>ИНН:</td>
                                            <td><input name="INN" type="text" id="INN"
                                                       value="<?php echo $row_users['INN']; ?>" size="40"></td>
                                        </tr*/ ?>
                                        <tr>
                                            <td>Телефоны:</td>
                                            <td><input name="phones" type="tel" id="phones"
                                                       value="<?php echo $row_users['phones']; ?>" size="40"></td>
                                        </tr>
                                        <tr>
                                        <tr>
                                            <td>Роль:</td>
                                            <td><select name="status" id="status">
                                                    <?php
                                                    do {
                                                        ?>
                                                        <option
                                                                value="<?php echo $row_status['id'] ?>" <?= ($row_status['level'] == $row_users['userlevel']) ? 'selected' : '' ?>><?php echo $row_status['name'] ?></option>
                                                        <?php
                                                    } while ($row_status = el_dbfetch($status));
                                                    if ($rows > 0) {
                                                        mysqli_data_seek($status, 0);
                                                        $row_status = el_dbfetch($status);
                                                    }
                                                    ?>
                                                </select></td>
                                        </tr>
                                        <tr>
                                            <td>Группа:</td>
                                            <td>
                                                <?
                                                if (intval($_SESSION['user_group']) < 2) {
                                                    ?>
                                                    <select name="group" id="groups">
                                                        <?php
                                                        do {
                                                            ?>
                                                            <option
                                                                    value="<?php echo $row_groups['id'] ?>" <?= ($row_groups['id'] == $row_users['usergroup']) ?
                                                                'selected' : '' ?>><?php echo $row_groups['short_name'] ?></option>
                                                            <?php
                                                        } while ($row_groups = el_dbfetch($groups));
                                                        if ($rows > 0) {
                                                            mysqli_data_seek($groups, 0);
                                                            $row_groups = el_dbfetch($groups);
                                                        }
                                                        ?>
                                                    </select>
                                                    <?
                                                } else {
                                                    echo '<strong>' . $groups['short_name'] . '<input type="hidden" name="group" value="' . $_SESSION['user_group'] . '"></strong>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Активный:</td>
                                            <td><input name="active" type="checkbox" id="active"
                                                       value="1"<?= ($row_users['userlevel'] == 0) ? '' : ' checked' ?>>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>
                                                <input type="hidden" name="MM_update" value="user">
                                                <input name="Edit" type="submit" class="but" id="Edit"
                                                       value="Сохранить"></td>
                                        </tr>
                                    </table>
                                    <input type="hidden" name="MM_update" value="user">
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>

                </tr>
            <?php } while ($row_users = el_dbfetch($users)); ?>
        </table>
        <div class="clear"></div>


        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <table border="0" width="50%" align="center">
            <tr>
                <td width="23%" align="center">
                    <?php if ($pageNum_users > 0) { // Show if not first page ?>
                    <a href="<?php printf("%s?pageNum_users=%d%s", $currentPage, 0, $queryString_users); ?>">
                        <i class="material-icons">
                            arrow_left
                        </i>
                        <?php } // Show if not first page ?>
                </td>
                <td width="31%" align="center">
                    <?php if ($pageNum_users > 0) { // Show if not first page ?>
                        <a href="<?php printf("%s?pageNum_users=%d%s", $currentPage, max(0, $pageNum_users - 1), $queryString_users); ?>">
                            <i class="material-icons">
                                chevron_left
                            </i></a>
                    <?php } // Show if not first page ?>
                </td>
                <td width="23%" align="center">
                    <?php if ($pageNum_users < $totalPages_users) { // Show if not last page ?>
                        <a href="<?php printf("%s?pageNum_users=%d%s", $currentPage, min($totalPages_users, $pageNum_users + 1), $queryString_users); ?>"><i
                                    class="material-icons">
                                chevron_right
                            </i></a>
                    <?php } // Show if not last page ?>
                </td>
                <td width="23%" align="center">
                    <?php if ($pageNum_users < $totalPages_users) { // Show if not last page ?>
                        <a href="<?php printf("%s?pageNum_users=%d%s", $currentPage, $totalPages_users, $queryString_users); ?>">
                            <i class="material-icons">
                                arrow_right
                            </i></a>
                    <?php } // Show if not last page ?>
                </td>
            </tr>
        </table>

        <?php
    } else {
        echo "<h5><center>Пока никто не зарегистрирован.</center></h5>";
    } ?>

    <table width="90%" border="0" align="center" cellpadding="5" cellspacing="0">
        <tr>
            <td valign="top"><h5 align="center">Добавление нового пользователя</h5>
                <table border="0" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
                    <form method="POST" action="<?php echo $editFormAction; ?>" name="newuser">
                        <? /*tr>
                            <td width="29%" align="right">Логин:</td>
                            <td width="71%"><input name="name2" type="text" id="name2" value="<?= $_POST['name2'] ?>">
                            </td>
                        </tr*/ ?>
                        <tr>
                            <td align="right">E-mail (логин):</td>
                            <td><input name="email" type="text" id="email" value=""></td>
                        </tr>
                        <tr>
                            <td align="right">Пароль:</td>
                            <td><input name="pass" type="text" id="pass">
                                (до 10 символов) <a href="javascript:void(0)" id="genPassLink">Сгенерировать</a>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">Ф.И.О.</td>
                            <td><input name="fio" type="text" id="fio" value=""></td>
                        </tr>
                        <tr>
                            <td align="right">Телефон:</td>
                            <td><input name="phones" type="tel" id="phone" value=""></td>
                        </tr>
                        <tr>
                            <td align="right">Роль:</td>
                            <td><select name="status" id="status">
                                    <?php
                                    if (!isset($_POST['status'])) $_POST['status'] = '4';
                                    do {
                                        ?>
                                        <option
                                                value="<?php echo $row_status['id'] ?>" <?= ($row_status['id'] == $_POST['status']) ? 'selected' : '' ?>><?php echo $row_status['name'] ?></option>
                                        <?php
                                    } while ($row_status = el_dbfetch($status));
                                    ?>
                                </select></td>
                        </tr>
                        <tr>
                            <td align="right">Группа:</td>
                            <td>
                                <?
                                if (intval($_SESSION['user_group']) < 2) {
                                    ?>
                                    <select name="group" id="groups">
                                        <?php
                                        do {
                                            ?>
                                            <option
                                                    value="<?php echo $row_groups['id'] ?>" <?= ($row_groups['id'] == $_POST['status']) ? 'selected' : '' ?>><?php echo $row_groups['short_name'] ?></option>
                                            <?php
                                        } while ($row_groups = el_dbfetch($groups));
                                        ?>
                                    </select>
                                    <?
                                } else {
                                    echo '<strong>' . $groups['short_name'] . '<input type="hidden" name="group" value="' . $_SESSION['user_group'] . '"></strong>';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td><input name="Submit" type="submit" class="but" value="Добавить"></td>
                        </tr>
                        <input type="hidden" name="MM_insert" value="newuser">
                    </form>
                </table>
            </td>
            <? /*td valign="top"><h5 align="center">Добавление новой группы пользователей </h5>
                <table width="100%" border="0" cellpadding="3" cellspacing="0" class="el_tbl">
                    <form method="post" name="addgroup">
                        <tr>
                            <td>Название группы</td>
                            <td><input name="name" type="text" id="name">
                                <input name="MM_insert" type="hidden" id="MM_insert" value="userstatus"></td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center"><input name="Submit2" type="submit" class="but"
                                                                  value="Добавить"></td>
                        </tr>
                    </form>
                </table>
                <h5 align="center">Удаление группы пользователей </h5>
                <table width="100%" border="0" cellpadding="3" cellspacing="0" class="el_tbl">
                    <form method="post" name="deletegroup" onSubmit="return del_group()">
                        <tr>
                            <td>Выберите группу</td>
                            <td><select name="status" id="status">
                                    <option></option>
                                    <?php
                                    do {
                                        ?>
                                        <option
                                                value="<?php echo $row_status['id'] ?>" <?= ($row_status['id'] == $_POST['status']) ? 'selected' : '' ?>><?php echo $row_status['name'] ?></option>
                                        <?php
                                    } while ($row_status = el_dbfetch($status));
                                    $rows = mysqli_num_rows($status);
                                    if ($rows > 0) {
                                        mysqli_data_seek($status, 0);
                                        $row_status = el_dbfetch($status);
                                    }
                                    ?>
                                </select>
                                <input name="delGroup" type="hidden" value="1"></td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center"><input name="Submit3" type="submit" class="but"
                                                                  value="Удалить"></td>
                        </tr>
                    </form>
                </table>
            </td*/ ?>
        </tr>
    </table>
    <br>

</body>
</html>