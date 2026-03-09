<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';
$result = false;

if (el_checkAjax()) {

    $group = intval($_POST['group']);
    $userId = intval($_POST['user']);
    $u = $g = $ng = null;
    $action = '';

    //Получаем данные юзера
    $u = el_dbselect("SELECT u.field6 AS user_status, u.field11 AS user_index,
       u.field8 AS subject, u.field9 AS region, u.field10 AS city
    FROM catalog_users_data u WHERE u.id = '$userId'", 0, $u, 'row', true);

    if($group == 0){ //Если передан 0, то создаем группу
        $newGroupId = el_createUserGroup($u['subject'], $u['region'], $u['city'], $u['user_index'], 1, $u['user_status']);
        $ng = el_dbselect("SELECT field1, field2 FROM catalog_groups_data WHERE id = $newGroupId", 1, $ng, 'row', true);
        $newGroup = $ng['field1'] . '-' . $ng['field2'];
        if(strlen($newGroup) > 0) { //Назначаем вновь созданную группу юзеру
            $result = el_dbselect("UPDATE catalog_users_data SET field16 = '$newGroupId' WHERE id = '$userId'", 0, $result, 'result', true);
            $action = ' теперь входит в группу ' . $newGroup .
                '<script>$("#uid' . $userId . ' [name=\'userGroup\']")
                .html("<option value=\'' . $newGroupId . '\' selected>' . $newGroup . '</option>")</script>';
        }
    }else{ //Иначе получаем юзеров группы
        $g = el_dbselect("SELECT field1, field2 FROM catalog_groups_data WHERE id = '$group'", 0, $g, 'row', true);
        $cg = el_dbselect("SELECT COUNT(id) AS `members` FROM catalog_users_data WHERE field16 = '$group'", 0, $g, 'row', true);
        $curator = el_dbselect("SELECT id FROM catalog_users_data WHERE field25 = '$group'", 0, $g, 'row', true);
        //Если юзеров меньше GROUPS_COUNT_MEMBERS
        if(intval($cg['members']) < GROUPS_COUNT_MEMBERS) { //То помещаем юзера в группу
            $newGroup = $g['field1'] . '-' . $g['field2'];
            $action = ' теперь входит в группу ' . $newGroup;
            $result = el_dbselect("UPDATE catalog_users_data SET field16 = '$group', field24 = '".$curator['id']."' WHERE id = '$userId'", 0, $result, 'result', true);
        }else{ //Иначе создаем новую
            $newGroupId = el_createUserGroup($u['subject'], $u['region'], $u['city'], $u['user_index'], $g['field2'] + 1, $u['user_status']);
            $ng = el_dbselect("SELECT field1, field2 FROM catalog_groups_data WHERE id = $newGroupId", 1, $ng, 'row', true);
            $newGroup = $ng['field1'] . '-' . $ng['field2'];
            if(strlen($newGroup) > 0) { //И помещаем юзера в новую группу (куратора еще нет)
                $result = el_dbselect("UPDATE catalog_users_data SET field16 = '$newGroupId' WHERE id = '$userId'", 0, $result, 'result', true);
                $action = ' теперь входит в автоматически созданную группу ' . $newGroup .
                    '<script>$("#uid' . $userId . ' [name=\'userGroup\']")
                .html("<option value=\'' . $newGroupId . '\' selected>' . $newGroup . '</option>")</script>';
            }
        }
    }



    if ($result != false) {
        echo json_encode(array(
            'result' => true,
            'resultText' => 'Пользователь ' . $action,
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
