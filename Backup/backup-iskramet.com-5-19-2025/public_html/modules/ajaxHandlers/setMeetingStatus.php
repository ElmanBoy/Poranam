<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';
$result = '';

if (el_checkAjax()) {

    $query = '';
    $status = intval($_POST['value']);

    if(is_array($_POST['id'])){
        $query = "id IN (".implode(',', $_POST['id']).")";
        $obj1 = "Мероприятия";
        $obj2 = "мероприятий";
        $ending = 'ы';
    }else{
        $query = "id = " . intval($_POST['id']);
        $obj1 = "Мероприятие";
        $obj2 = "мероприятия";
        $ending = 'о';
    }

    if($status > 0) {

        switch ($status) {
            case 13:
                $action1 = 'отправлен'.$ending.' на утверждение Администратором';
                $action2 = 'утверждения';
                //$query .= ' AND field14 = 10';
                break;
            case 14:
                $action1 = 'запущен'.$ending;
                $action2 = 'запуска';
                //$query .= ' AND field14 = 13';
                break;
            case 15:
                $action1 = 'завершен'.$ending;
                $action2 = 'завершения';
                //$query .= ' AND field14 = 14';
                break;
        }

        $result = el_dbselect("UPDATE catalog_init_data SET field14 = " . $status . " 
    WHERE ".$query, 0, $result, 'result', true);

        if ($result != false) {
            echo json_encode(array(
                'result' => true,
                'resultText' => $obj1.' успешно ' . $action1,
                'errorFields' => array()));
        } else {
            echo json_encode(array(
                'result' => false,
                'resultText' => 'Во время ' . $action2 . ' '.$obj2.' произошла программная ошибка.<br>
                Сообщите об этом администратору.',
                'errorFields' => array()));
        }
    }else{
        $result = el_dbselect("DELETE FROM catalog_init_data WHERE " . $query,
            0, $result, 'result', true);

        if ($result != false) {
            echo json_encode(array(
                'result' => true,
                'resultText' => $obj1.' успешно удален'.$ending,
                'errorFields' => array()));
        } else {
            echo json_encode(array(
                'result' => false,
                'resultText' => 'Во время удаления '.$obj2.' произошла программная ошибка.<br>
                Сообщите об этом администратору.',
                'errorFields' => array()));
        }
    }
}
?>
