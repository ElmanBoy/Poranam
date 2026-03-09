<?
@session_start($idsess);
$errMsg = '';
if (isset($_SESSION['login']) && strlen($_SESSION['login']) > 0) {
    echo "������������,<br><b>" . $_SESSION['fio'] . "</b>! " ?><br/>
    <? /*a href="/myprofile/?orders" class="menupart_link_blue">��� ������</a><br*/
    ?>

    <form method="post" name="outFrm" action="<?= $_SERVER['REQUEST_URI'] ?>">
        <input type="hidden" name="logout" value="logout"><br/><br/>
        <ul>
            <li><a href="/myprofile/?private" class="menupart_link_blue">��� ������</a></li>
            <li><a href="#" onclick="document.outFrm.submit()">�����</a></li>
        </ul>
    </form>

    <?
} elseif ((!isset($_GET['logout'])) && (isset($_POST['user_enter']))) {
    $errMsg = "<font color=red>�������� ����� ��� ������!</font>";
}
if (!isset($_SESSION['login'])) {
    ?>
    <script language="javascript">
        function checkLogin(f) {
            if (f.user.value != '' && f.user.value != '�����' && f.password.value != '' && f.password.value != '������') {
                return true;
            } else {
                f.reset()
                alert('������� ����� � ������!');
                return false;
            }
        }
    </script>
    <?= $err ?>
    <form name="user_valid" method="post" onsubmit="return checkLogin(this)">
        �����:<br/>
        <input type="text" name="user" class="text"/><br/>
        ������:<br/>
        <input type="password" name="password" class="text"/><br/>
        <input type="submit" class="btn" value="����"/>
        <input name="user_enter" type="hidden" id="user_enter">
    </form>
    <ul>
        <li><a href="/registration/">�����������</a></li>
        <li><a href="/remember/">������ ������?</a></li>
    </ul>
<? } ?>