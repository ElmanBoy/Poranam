<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php'; //print_r($_POST);
$result = null;
$query = "SELECT field1, field2 FROM catalog_users_data WHERE active = 1";
$subQuery = "";
$subquery = [];
$excluded = ['ajax', 'init_select_all', 'action', 'title', 'text'];
$err = 0;
$errStr = [];
$errFields = [];
$mails = [];
$message = '';
$lettersCount = 0;
$failed = 0;
$siteName = str_replace('www', '', $_SERVER['SERVER_NAME']);

if(strlen(trim($_POST['title'])) == 0){
    $err++;
    $errFields[] = 'title';
    $errStr[] = 'Укажите загаловок письма.';

}

if(strlen(trim($_POST['text'])) == 0){
    $err++;
    $errFields[] = 'text';
    $errStr[] = 'Укажите текст письма.';
}

/*if(!isset($_POST['init_select_all']) && !isset($_POST['sf8']) &&
    !isset($_POST['sf7']) && !isset($_POST['sf6'])){
    $err++;
    $errFields[] = 'init_select_all';
    $errFields[] = 'sf8[]';
    $errFields[] = 'sf7';
    $errFields[] = 'sf6';
    $errStr[] = 'Не выбрана ни одна категория пользователей.';
}*/


if(el_checkAjax()) {
    if(isset($_POST)){

        foreach($_POST as $field => $val){
            $field = str_replace('sf', 'field', $field);
            $field = str_replace(['[', ']'], '', $field);

            if(!in_array($field, $excluded)) {

                if (is_array($val)) {
                    $multival = [];
                    foreach ($val as $item) {
                        if(strlen(trim($item)) > 0) {
                            $multival[] = "$field = '$item'";
                        }
                    }
                    $subquery[] = '(' . implode(' OR ', $multival) . ')';
                } else {
                    if(strlen(trim($val)) > 0) {
                        $subquery[] = "$field = '$val'";
                    }
                }
            }
        }
        if(count($subquery) > 0) {
            $query .= " AND " . implode(' AND ', $subquery);
        }
    }
    /*$result = el_dbselect($query, 0, $result, 'result', true);
    $count = el_dbnumrows($result);*/
    if(isset($_SESSION['user_checked']) && $_SESSION['user_checked'] != [0]) {
        $subQuery = " AND id IN (" . implode(', ', $_SESSION['user_checked']) . ")";
    }

    $result = el_dbselect($query.$subQuery, 0, $result, 'result', true);
    $count = el_dbnumrows($result);

    if($count > 0){
        //Если по заданным критериям найдены пользователи, то приступаем к рассылке в цикле
        $item = el_dbfetch($result);

        do {

            $letter_body = el_render('/tmpl/letter/letter2.php',
                [
                    'fio' => $item['field1'],
                    'caption' => strip_tags($_POST['title']),
                    'text' => '<p>' . nl2br($_POST['text']) . '</p>'
                ]
            );
            $mailResult = el_mail($item['field2'], 'Новое обращение с сайта ' . $siteName,
                $letter_body, 'noreply@'.$siteName, 'html', 'smtp',
                '', 'noreply@'.$siteName, 'Администраиця проекта');
            if ($mailResult != true){
                $err++;
                $errStr[] = $mailResult;
                $failed++;
            }else{
                $lettersCount++;
                $mails[] = $item['field2'];
            }

        }while($item = el_dbfetch($result));

        if($lettersCount == $count){
            $message = 'Успешно отправлен'.el_postfix($count, 'о', 'ы', 'ы').' 
            '.$count.' пис'.el_postfix($count, 'ьмо', 'ьма', 'ем').'.';
        }else{
            $message .= 'Но '.$failed.' не отправлен'.el_postfix($failed, 'о', 'ы', 'ы');
        }

    }else{
        $err++;
        $errStr[] = 'Не кому рассылать письма.'.$query;
    }



    echo json_encode([
        'result' => $err == 0,
        'resultText' => $err == 0 ? $message : implode('<br>', $errStr),
        'mails' => $mails,
        'errorFields' => $errFields
        ]
    );
}else{
    echo json_encode(array(
        'result' => false,
        'resultText' => 'Пожалуйста, авторизуйтесь',
        'errorFields' => []));
}