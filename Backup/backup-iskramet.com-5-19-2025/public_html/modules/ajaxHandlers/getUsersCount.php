<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';
$result = null;
$query = "SELECT COUNT(id) AS `count` FROM catalog_users_data WHERE  active = 1";
$subquery = [];


if(el_checkAjax()) {
    if(isset($_POST['filter'])){
        foreach($_POST['filter'] as $field => $val){
            $field = str_replace('sf', 'field', $field);
            $field = str_replace(['[', ']'], '', $field);
            if(is_array($val)){
                $multival = [];
                foreach ($val as $item){
                    $multival[] = "$field = '$item'";
                }
                $subquery[] = '('.implode(' OR ', $multival).')';
            }else {
                $subquery[] = "$field = '$val'";
            }
        }
        $query .= " AND ".implode(' AND ', $subquery);
    }
    $result = el_dbselect($query, 0, $result, 'row', true);
    $count = intval($result['count']);
    echo $count == 0 ? 'Пользователи не найдены' : 'Найдено '.$count.' пользовател'.el_postfix($count, 'ь', 'я', 'ей');
}
