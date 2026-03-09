<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';
$result = '';

if (el_checkAjax()) {

    $group = intval($_POST['group']);
    $userId = intval($_POST['user']);
    $u = $g = $ng = null;
    $action = '';

    $u = el_dbselect("SELECT u.user_id AS user_id, u.field6 AS user_status, u.field11 AS user_index,
       u.field8 AS subject, u.field9 AS region, u.field10 AS city
    FROM catalog_users_data u WHERE u.id = '$userId'", 0, $u, 'row', true);

    $userGroup = $u['user_id'];

    if($group == 100000000){ //Если передано 100000000, то создаем новую группу
        $newGroupId = el_createUserGroup($u['subject'], $u['region'], $u['city'], $u['user_index'], 1, $u['user_status'], 'sub');
        $ng = el_dbselect("SELECT field1, field2 FROM catalog_groups_data WHERE id = $newGroupId", 1, $ng, 'row', true);
        $newGroup = $ng['field1'] . '-' . $ng['field2'];
        if(strlen($newGroup) > 0) { //Назначаем вновь созданную группу юзеру
            $result = el_dbselect("UPDATE catalog_users_data SET field25 = '$newGroupId' WHERE id = '$userId'", 0, $result, 'result', true);
            $action = ' теперь руководит группой ' . $newGroup .
                '<script>$("#uid' . $userId . ' [name=\'userGroups\']")
                    .html("<option value=\'' . $newGroupId . '\' selected>' . $newGroup . '</option>")</script>';
        }
    }else{
        $u = el_dbselect("SELECT g.field1 AS user_index, g.field2 AS group_number, u.field6 AS user_status, 
           u.field8 AS subject, u.field9 AS region, u.field10 AS city
        FROM catalog_users_data u, catalog_groups_data g 
        WHERE u.field16 = g.id AND u.id = '$userId'", 0, $u, 'row', true);

        $g = el_dbselect("SELECT field1, field2 FROM catalog_groups_data WHERE id = '$group'", 0, $g, 'row', true);

        $newGroup = $g['field1'] . '-' . $g['field2'];

        $action = ' теперь руководит группой ' . $newGroup;

        if ($group == 0) {
            $action = ' больше не руководит никакой группой ';
        }else{
            //Удаляем старых кураторов
            el_dbselect("UPDATE catalog_users_data SET field25 = NULL WHERE field25 = '$group'", 0, $result, 'result', true);
        }

        $result = el_dbselect("UPDATE catalog_users_data SET field25 = '$group' WHERE id = '$userId'", 0, $result, 'result', true);
    }

    if ($result != false) {

        el_dbselect("UPDATE catalog_users_data SET field24 = '$userId' WHERE field16 = '$group'", 0, $result, 'result', true);

        echo json_encode(array(
            'result' => true,
            'resultText' => 'Пользователь ' . $userGroup . $action,
            'errorFields' => array()));
    } else {
        echo json_encode(array(
            'result' => false,
            'resultText' => 'Во время смены группы произошла программная ошибка.<br>
            Сообщите об этом администратору.',
            'errorFields' => array()));
    }
}
?>
