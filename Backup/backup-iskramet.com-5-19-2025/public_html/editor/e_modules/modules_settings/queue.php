<?php
session_start();
$queue_settings = array(
    'module_name' => 'Электронная очередь',
    'settings' => array(
        array(
            'type' => 'divider',
            'name' => 'Время'
        ),
        array(
            'title' => 'Сдвинуть начало времени приёма (минут)',
            'description' => 'Количество минут от начала рабочего дня',
            'name' => 'start',
            'type' => 'number',
            'required' => true,
            'default' => '15'
        ),
        array(
            'title' => 'Сдвинуть окончание приёма (минут)',
            'description' => 'Количество минут до конца рабочего дня',
            'name' => 'finish',
            'required' => true,
            'type' => 'number',
            'default' => '15'
        ),
        array(
            'title' => 'Длительность первичного приёма',
            'description' => 'Длительность приёма одного посетителя в минутах',
            'name' => 'duration',
            'required' => true,
            'type' => 'number',
            'default' => '30'
        ),
        array(
            'title' => 'Разрешить запись на вторичный приём',
            'description' => 'Если галочка снята, то будет доступна запись только на первичный приём',
            'name' => 'appeal',
            'type' => 'checkbox',
            'required' => false,
            'default' => false,
            'props' => array(
                'value' => 1
            )
        ),
        array(
            'title' => 'Длительность вторичного приёма',
            'description' => 'Длительность приёма одного посетителя в минутах',
            'name' => 'duration2',
            'required' => true,
            'type' => 'number',
            'default' => '10'
        ),
        array(
            'type' => 'divider',
            'name' => 'Выходные и праздничные дни'
        ),
        array(
            'title' => 'Отметьте неприёмные дни',
            'description' => 'Можно задать неприёмные даты на весь год сразу',
            'name' => 'holydays',
            'required' => false,
            'type' => 'calendar',
            'default' => "['01-01','01-02','01-03','01-07','02-23','03-08','05-01','05-09','06-12','11-04']"
        ),
        array(
            'type' => 'divider',
            'name' => 'Напоминания'
        ),
        array(
            'title' => 'Первое Email напоминание за, часов',
            'description' => 'За сколько часов до начала приёма напоминать по Email<br>0 - отключает напоминание',
            'name' => 'firstEmail',
            'required' => true,
            'type' => 'number',
            'default' => '24'
        ),
        array(
            'title' => 'Второе Email напоминание за, часов',
            'description' => 'За сколько часов до начала приёма напоминать по Email<br>0 - отключает напоминание',
            'name' => 'secondEmail',
            'required' => true,
            'type' => 'number',
            'default' => '3'
        ),
        array(
            'title' => 'Первое SMS напоминание за, часов',
            'description' => 'За сколько часов до начала приёма напоминать по SMS<br>0 - отключает напоминание',
            'name' => 'firstSms',
            'required' => true,
            'type' => 'number',
            'default' => '24'
        ),
        array(
            'title' => 'Второе SMS напоминание за, часов',
            'description' => 'За сколько часов до начала приёма напоминать по SMS<br>0 - отключает напоминание',
            'name' => 'secondSms',
            'required' => true,
            'type' => 'number',
            'default' => '3'
        )
    )
);

$queue_settings_local = array(
    'module_name' => 'Электронная очередь',
    'settings' => array(
        array(
            'title' => 'Включить модуль',
            'description' => 'Если галочка снята, то форма записи на приём не будет показываться на сайте',
            'name' => 'on',
            'type' => 'checkbox',
            'required' => false,
            //'default' => '1',
                'props' => array(
                    'value' => 1
                )
        ),
        array(
            'type' => 'divider',
            'name' => 'Время'
        ),
        array(
            'title' => 'Сдвинуть начало времени приёма (минут)',
            'description' => 'Количество минут от начала рабочего дня',
            'name' => 'start',
            'type' => 'number',
            'required' => true,
            'default' => '15'
        ),
        array(
            'title' => 'Сдвинуть окончание приёма (минут)',
            'description' => 'Количество минут до конца рабочего дня',
            'name' => 'finish',
            'required' => true,
            'type' => 'number',
            'default' => '15'
        ),
        array(
            'title' => 'Длительность первичного приёма',
            'description' => 'Длительность приёма одного посетителя в минутах',
            'name' => 'duration',
            'required' => true,
            'type' => 'number',
            'default' => '30'
        ),
        array(
            'title' => 'Разрешить запись на вторичный приём',
            'description' => 'Если галочка снята, то будет доступна запись только на первичный приём',
            'name' => 'appeal',
            'type' => 'checkbox',
            'required' => false,
            'default' => false,
            'props' => array(
                'value' => 1
            )
        ),
        array(
            'title' => 'Длительность вторичного приёма',
            'description' => 'Длительность приёма одного посетителя в минутах',
            'name' => 'duration2',
            'required' => true,
            'type' => 'number',
            'default' => '10'
        ),
        array(
            'title' => 'Сотрудники',
            'description' => 'К кому можно записаться<br>Нескольких сотрудников можно выделить с нажатой клавишей Ctrl',
            'name' => 'stuff',
            'type' => 'list_fromdb',
            'props' => array(
                'multiple' => true,
                'listdb' => 'phpSP_users',
                'where' => ' AND usergroup = '.intval($_SESSION['site_id']),
                'text' => 'fio',
                'value' => 'primary_key',
                'size' => 5
            ),
            'default' => array()
        ),
        array(
            'type' => 'divider',
            'name' => 'Выходные и праздничные дни'
        ),
        array(
            'title' => 'Отметьте неприёмные дни',
            'description' => 'Можно задать не приёмные даты на весь год сразу',
            'name' => 'holydays',
            'required' => false,
            'type' => 'calendar',
            'default' => "['".date('Y')."-01-01','".date('Y')."-01-02','".date('Y')."-01-03','".date('Y')."-01-07','".date('Y')."-02-23','".date('Y')."-03-08','".date('Y')."-05-01','".date('Y')."-05-09','".date('Y')."-06-12','".date('Y')."-11-04']"
        ),
        array(
            'type' => 'divider',
            'name' => 'Напоминания'
        ),
        array(
            'title' => 'Первое Email напоминание за, часов',
            'description' => 'За сколько часов до начала приёма напоминать по Email<br>0 - отключает напоминание',
            'name' => 'firstEmail',
            'required' => true,
            'type' => 'number',
            'default' => '24'
        ),
        array(
            'title' => 'Второе Email напоминание за, часов',
            'description' => 'За сколько часов до начала приёма напоминать по Email<br>0 - отключает напоминание',
            'name' => 'secondEmail',
            'required' => true,
            'type' => 'number',
            'default' => '3'
        ),
        array(
            'title' => 'Первое SMS напоминание за, часов',
            'description' => 'За сколько часов до начала приёма напоминать по SMS<br>0 - отключает напоминание',
            'name' => 'firstSms',
            'required' => true,
            'type' => 'number',
            'default' => '24'
        ),
        array(
            'title' => 'Второе SMS напоминание за, часов',
            'description' => 'За сколько часов до начала приёма напоминать по SMS<br>0 - отключает напоминание',
            'name' => 'secondSms',
            'required' => true,
            'type' => 'number',
            'default' => '3'
        )
    )
);
?>
