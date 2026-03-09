<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';
$result = '';

if (el_checkAjax()) {

    $query = '';
    $status = intval($_POST['value']);

    if(is_array($_POST['id'])){
        $query = "id IN (".implode(',', $_POST['id']).")";
        $obj1 = "Инициативы";
        $obj2 = "инициатив";
        $ending = 'ы';
    }else{
        $query = "id = " . intval($_POST['id']);
        $obj1 = "Инициатива";
        $obj2 = "инициативы";
        $ending = 'a';
    }

    if($status > 0) {

        switch ($status) {
            case 2:
                $action1 = 'запущен'.$ending;
                $action2 = 'запуска';
                $query .= ' AND field14 = 1';
                break;
            case 3:
                $action1 = 'завершен'.$ending;
                $action2 = 'завершения';
                $query .= ' AND field14 = 2';
                break;
            case 4:
                $action1 = 'переведен'.$ending.' в Голосования';
                $action2 = 'перевода';
                $query .= ' AND field14 = 3';
                $status .= ', cat = 398';
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
