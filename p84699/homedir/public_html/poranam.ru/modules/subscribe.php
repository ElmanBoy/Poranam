<?php
if (isset($_GET['recip']) && isset($_GET['activate']) && $_GET['activate'] == 'y') {//��������� ��������
    $recip_id = substr_replace($_GET['recip'], '', 0, 8);
    $recip_id = intval($recip_id);
    $updateSQL = sprintf("UPDATE mail_list SET active=%s WHERE id=%s",
        GetSQLValueString(1, "int"),
        GetSQLValueString($recip_id, "int"));


    if (!mysqli_query($dbconn), $updateSQL) {
        echo "<span style='color:red'><h4>�� ������� ������������ ��������.</h4>��������, ������ �������� ����� ��������.</span>";
    } else {
        echo "<span style='color:green'><h4>�������!</h4>���� �������� ������� ������������.</span><br><br><a href=''>���������� ���������</a>";
    }

} else {//���������� ���������

    if (isset($_POST['auth']) && $_POST['auth'] == 'y') {
        $recip_check = el_dbselect("select id, email, pass from mail_list where email='" . $_POST['email'] . "'", 1, $recip_check);
        $row_recip_check = el_dbfetch($recip_check);
        if ($row_recip_check['pass'] === crypt(md5($_POST['pass']))) {
            $login_recip = 'y';
            session_start();
            session_register("login_recip");
            $recip_id = $row_recip_check['id'];
        }
    }

    if ($_SESSION['login_recip'] != 'y') {//�����������
        ?>
        <table border="0" align="center" cellpadding="5" cellspacing="0">
            <caption>����������, ������� ��� E-mail � ������</caption>
            <form method="post">
                <tr>
                    <td>E-mail:</td>
                    <td><input name="email" type="text" id="email" size="40"></td>
                </tr>
                <tr>
                    <td>������:</td>
                    <td><input name="pass" type="text" id="pass" size="40"></td>
                </tr>
                <tr>
                    <td colspan="2" align="center"><input type="submit" name="Submit2" value="����">
                        <input name="auth" type="hidden" id="auth" value="y"></td>
                </tr>
            </form>
        </table>

        <?
    } else {

        if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "update")) {

            list($user, $domain) = split("@", $_POST['email'], 2);
            if (checkdnsrr($domain, "MX") == false) {
                echo "<h4 style='color:red'>������ �� ������ ����� Email!</h4>";
            } else {


                $query_check = "SELECT * FROM mail_list WHERE email='" . $_POST['email'] . "'";
                $check = el_dbselect($query_check, 0, $check);
                $row_check = el_dbfetch($check);

                if (mysqli_num_rows($check) > 0 && $row_check['id'] != $_GET['id']) {
                    echo "<h4 style='color:red'>��������� � ����� ������ ��� ����.</h4>";
                } else {
                    ($_POST['active'] == '1') ? $active1 = '1' : $active1 = '0';
                    if (isset($_POST['pass']) && isset($_POST['old_pass'])) {
                        (strlen($_POST['pass']) > 0 && $row_check['pass'] === crypt(md5($_POST['old_pass']))) ? $pass1 = crypt(md5($_POST['pass'])) : $pass1 = $row_check['pass'];
                    }
                    $themes1 = implode(';', $_POST['themes']);
                    $updateSQL = sprintf("UPDATE mail_list SET name=%s, email=%s, pass=%s, type=%s, codepage=%s, themes=%s, active=%s WHERE id=%s",
                        GetSQLValueString($_POST['name'], "text"),
                        GetSQLValueString($_POST['email'], "text"),
                        GetSQLValueString($pass1, "text"),
                        GetSQLValueString($_POST['type'], "text"),
                        GetSQLValueString($_POST['codepage'], "text"),
                        GetSQLValueString($themes1, "text"),
                        GetSQLValueString($active1, "int"),
                        GetSQLValueString($_GET['id'], "int"));


                    $Result1 = el_dbselect($updateSQL, 0, $Result1);
                    echo "<h4 style='color:green'>��������� ���������!</h4>";
                }
            }
        }

        $query_dbmail_list = "SELECT * FROM mail_list WHERE id='" . $recip_id . "'";
        $dbmail_list = el_dbselect($query_dbmail_list, 0, $dbmail_list);
        $row_dbmail_list = el_dbfetch($dbmail_list);

        ?>
        <script language="JavaScript" type="text/JavaScript">
            function fill(id, name) {
                var error = 0;
                var errmess = "";
                var id1 = new Array;
                var name1 = new Array;
                id1 = id;
                name1 = name;
                for (var i = 0; i < id1.length; i++) {
                    if (document.getElementById(id1[i]).value == "" || document.getElementById(id1[i]).value == '���' || document.getElementById(id1[i]).value == 'E-mail' || document.getElementById(id1[i]).value == '������') {
                        errmess += '����������, ��������� ���� "' + name1[i] + '"\n';
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

            function clearfield(obj) {
                if (obj.value == '���' || obj.value == 'E-mail' || obj.value == '������') {
                    obj.value = '';
                }
            }

            function checkEmail(obj) {
                if (obj.value.indexOf('@', 0) == -1 || obj.value.indexOf('.', 0) == -1) {
                    alert("\n������ �������� ����� E-mail.")
                    obj.select();
                    obj.focus();
                    return false;
                } else {
                    return true;
                }
            }
        </script>
        <form action="<?php echo $editFormAction; ?>" method="POST" name="add" onSubmit="return fill(['name', 'email'], ['���', 'E-mail'])"
              enctype="multipart/form-data">
            <table border="0" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
                <tr>
                    <td align="right">���:</td>
                    <td><input name="name" type="text" id="name" value="<?= $row_dbmail_list['name'] ?>" size="30"></td>
                </tr>
                <tr>
                    <td align="right">E-mail:</td>
                    <td><input name="email" type="text" id="email" value="<?= $row_dbmail_list['email'] ?>" size="30"></td>
                </tr>
                <? if ($myprofile != 1) { ?>
                    <tr>
                        <td align="right" valign="top">������ ������:</td>
                        <td><input name="old_pass" type="text" id="old_pass" value="" size="30"></td>
                    </tr>
                    <tr>
                        <td align="right" valign="top">����� ������:</td>
                        <td><input name="pass" type="text" id="pass" value="" size="30"></td>
                    </tr>
                <? } ?>
                <tr>
                    <td align="right" valign="top">��� ��������:</td>
                    <td><select name="type" id="type">
                            <option value="HTML" <?= ($row_dbmail_list['type'] == 'HTML') ? "selected" : "" ?>>HTML</option>
                            <option value="TEXT" <?= ($row_dbmail_list['type'] == 'TEXT') ? "selected" : "" ?>>TEXT</option>
                        </select></td>
                </tr>
                <tr>
                    <td align="right" valign="top">������� ��������:</td>
                    <td><select name="codepage" id="codepage">
                            <option value="KOI8-R" <?= ($row_dbmail_list['codepage'] == 'KOI8-R') ? "selected" : "" ?>>KOI8-R</option>
                            <option value="Windows-1251" <?= ($row_dbmail_list['codepage'] == 'Windows-1251') ? "selected" : "" ?>>Windows-1251</option>
                        </select></td>
                </tr>
                <?

                $query_themes = "SELECT * FROM mail_themes ORDER BY id DESC";
                $themes = el_dbselect($query_themes, 0, $themes);
                $row_themes = el_dbfetch($themes);
                ?>
                <tr>
                    <td align="right" valign="top">������ ��������:</td>
                    <td>
                        <?
                        echo '
	<table width="100%" border="0" cellpadding="5" cellspacing="0">
	';
                        do {
                            if (substr_count($row_dbmail_list['themes'], ';') > 0) {
                                $th = explode(';', $row_dbmail_list['themes']);
                                if (in_array($row_themes['id'], $th)) {
                                    $ch = "checked";
                                } else {
                                    $ch = "";
                                }
                            } else {
                                if ($row_themes['id'] == $row_dbmail_list['themes']) {
                                    $ch = "checked";
                                } else {
                                    $ch = "";
                                }
                            }
                            echo '<tr><td valign=top><input type="checkbox" name="themes[' . $row_themes['id'] . ']" value="' . $row_themes['id'] . '" ' . $ch . '> ' . $row_themes['name'] . '</td></tr>
	';
                        } while ($row_themes = el_dbfetch($themes));
                        echo '
	</table>
	';
                        ?>    </td>
                </tr>
                <tr>
                    <td align="right">�������� ��������:</td>
                    <td><input name="active" type="checkbox" id="active" value="1" <?= ($row_dbmail_list['active'] == '1') ? "checked" : "" ?>></td>
                </tr>
                <tr>
                    <td colspan="2" align="center"><input name="Submit" type="submit" value="���������" class="but"> &nbsp;&nbsp;</td>
                </tr>
            </table>
            <input type="hidden" name="MM_update" value="update">
        </form>
    <? }
} ?>