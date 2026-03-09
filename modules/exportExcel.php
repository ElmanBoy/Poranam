<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';
$members = $meeting = $users = null;
$meeting_id = intval($_GET['id']);
$meeting = el_dbselect("SELECT * FROM catalog_init_data WHERE id = $meeting_id", 0, $meeting, 'row', true);
$members = el_dbselect("SELECT users FROM meeting_members WHERE meeting_id = $meeting_id", 0, $members, 'row', true);
$users = el_dbselect('SELECT user_id FROM catalog_users_data WHERE id IN (' .$members['users']. ') ORDER BY user_id', 0, $users, 'result', true);
?>
<html>
<head>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <script src='/js/jquery-3.5.1.min.js'></script>
    <script src='/js/jquery.table2excel.min.js'></script>
</head>
<body>
<div id='tableres'>
    <table border='1'>
        <tr><th>Список участников мероприятия &laquo;<?=$meeting['field1']?>&raquo;</th></tr>
        <tr><th>ID участника</th></tr>
        <?php
        $ru = el_dbfetch($users);
        do{
            echo '<tr><td>'.$ru['user_id'].'</td></tr>';
        }while($ru = el_dbfetch($users));
        ?>
    </table>
</div>
<script type='text/javascript'>
    $(document).ready(function () {
        $('#tableres').table2excel({
            exclude: '.excludeThisClass',
            name: 'Список участников',
            filename: 'MembersList',
            fileext: '.xls',
        });
    });
</script>
</body>
</html>
