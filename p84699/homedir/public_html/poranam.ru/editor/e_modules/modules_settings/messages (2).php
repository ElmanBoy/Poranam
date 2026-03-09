<?php
session_start();
$messages_settings = array(
    'module_name' => 'Обращения',
    'settings' => array(
        array(
            'title' => 'Оповещать по email',
            'description' => '',
            'name' => 'alertEmail',
            'type' => 'checkbox',
            'required' => false,
            'value' => '1',
            'default' => ' checked'
        ),
        array(
            'title' => 'Оповещать по СМС',
            'description' => '',
            'name' => 'alertSms',
            'required' => false,
            'value' => '1',
            'type' => 'checkbox',
            'default' => ''
        )
    ),

);

$messages_settings_local = array(
    'module_name' => 'Обращения',
    'settings' => array(
        array(
            'title' => 'Сотрудники',
            'description' => 'Кому отправлять ссобщения<br>Нескольких сотрудников можно выделить с нажатой клавишей Ctrl',
            'name' => 'stuff',
            'type' => 'list_fromdb',
            'props' => array(
                'multiple' => true,
                'listdb' => 'phpSP_users',
                'where' => ' AND usergroup = ' . intval($_SESSION['site_id']),
                'text' => 'fio',
                'value' => 'primary_key',
                'size' => 5
            ),
            'default' => array()
        ),
        array(
            'title' => 'Оповещать по email',
            'description' => '',
            'name' => 'alertEmail',
            'type' => 'checkbox',
            'required' => false,
            'value' => '1',
            'default' => ' checked'
        ),
        array(
            'title' => 'Оповещать по СМС',
            'description' => '',
            'name' => 'alertSms',
            'required' => false,
            'value' => '1',
            'type' => 'checkbox',
            'default' => ''
        )
    )
);
?>
