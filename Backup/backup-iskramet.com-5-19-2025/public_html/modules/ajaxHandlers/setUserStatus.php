<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';
$result = '';

if (el_checkAjax()) {

    $status = intval($_POST['value']);
    $query = $action1 = $action2 = '';
    $adminsCount = 0;
    $adminsIds = [];

    if($_SESSION['user_level'] != 11) {
        for ($i = 0; $i < count($_POST['id']); $i++) {
            $currStatus = el_dbselect("SELECT user_id, field6 FROM catalog_users_data WHERE id = " .
                intval($_POST['id'][$i]), 0, $result, 'row', true
            );
            if (intval($currStatus['field6']) == 11) {
                $adminsIds[] = $currStatus['user_id'];
                $adminsCount++;
            }
        }
    }

    if(count($_POST['id']) > 1){
        $query = "id IN (".implode(',', $_POST['id']).")";
        $obj1 = "Пользователи";
        $obj2 = "пользователей";
        $ending = 'ы';
    }else{
        $query = "id = " . intval($_POST['id'][0]);
        $obj1 = "Пользователь";
        $obj2 = "пользователи";
        $ending = '';
    }

    if($status > 0) {

        switch ($status) {
            case 1:
                $action1 = 'разблокирован'.$ending;
                $action2 = 'разблокировки';
                $status = ' active = 1 ';
                break;
            case 2:
                $action1 = 'заблокирован'.$ending;
                $action2 = 'блокировки';
                $status = ' active = 0 ';
                break;


        }

        if($adminsCount == 0) {
            $result = el_dbselect("UPDATE catalog_users_data SET $status WHERE " . $query, 0, $result, 'result', true);

            if ($result != false) {
                echo json_encode(array(
                    'result' => true,
                    'resultText' => $obj1 . ' успешно ' . $action1,
                    'errorFields' => array())
                );
            } else {
                echo json_encode(array(
                    'result' => false,
                    'resultText' => 'Во время ' . $action2 . ' ' . $obj2 . ' произошла программная ошибка.<br>
                Сообщите об этом администратору.',
                    'errorFields' => array())
                );
            }
        }else{
            echo json_encode(array(
                    'result' => false,
                    'resultText' => 'Во время ' . $action2 . ' ' . $obj2 . ' произошла ошибка.<br>
                Вы не можете блокировать Администратор'.el_postfix($adminsCount, 'а', 'ов', 'ов').' '.implode(', ', $adminsIds),
                    'errorFields' => array())
            );
        }
    }else{
        $us = el_dbselect("SELECT COUNT(id) AS `count` FROM catalog_users_data WHERE $query AND field6 = 11", 0, $result, 'row', true);
        $message = 'Сообщите об этом администратору.';
        if($us['count'] == 0) {
            $result = el_dbselect("DELETE FROM catalog_users_data WHERE " . $query, 0, $result, 'result', true);
        }else{
            $result = false;
            $message = 'Запрещено удалять Администраторов.';
        }

        if ($result != false) {
            echo json_encode(array(
                'result' => true,
                'resultText' => $obj1.' успешно удален'.$ending,
                'errorFields' => array()));
        } else {
            echo json_encode(array(
                'result' => false,
                'resultText' => 'Во время удаления '.$obj2.' произошла программная ошибка.<br>
                ' . $message,
                'errorFields' => array()));
        }
    }
}
?>
