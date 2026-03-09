<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';
$requiredUserLevel = array(0, 1, 2);
include($_SERVER['DOCUMENT_ROOT'] . "/editor/secure/secure.php");

$moduleName = $_GET['settings'];
$moduleSettingsPath = $_SERVER['DOCUMENT_ROOT'] . '/editor/e_modules/modules_settings/' . $moduleName . '.php';

$modulePropsName = ($_GET['mode'] == 'global') ? $moduleName . '_props.php' : $moduleName . '_props_'.intval($_SESSION['site_id']).'.php';
$modulePropsPath = $_SERVER['DOCUMENT_ROOT'] . '/Connections/modules_props/' . $modulePropsName;

if (is_file($moduleSettingsPath)){
    include_once $moduleSettingsPath;
    $settings = ${$moduleName . '_settings'.(($_GET['mode'] == 'global') ? '' : '_local')};



    if (isset($_POST['Submit'])) {
        unset($_POST['Submit']);
        $_POST['holydays'] = explode(',', $_POST['holydays']);
        $ev = var_export($_POST, true);
        if ($ev != '') {
            $output = "<?\n \$" . $moduleName . "_property = $ev \n ?>";
            file_put_contents($modulePropsPath, $output);
            echo '<script>alert("Изменения сохранены!")</script>';
        }
    }
    if(is_file($modulePropsPath)) {
        include_once $modulePropsPath;
        $svalues = ${$moduleName . '_property'};
    }
    ?>
    <!doctype html>
    <html>

    <head>
        <meta charset="utf-8">
        <title>Настройка модуля &laquo;<?= $settings['module_name'] ?>&raquo;</title>
        <link href="style.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="/css/flatpickr.min.css">
        <script src="/js/jquery-1.11.0.min.js"></script>
        <script src="/js/flatpickr.js"></script>
        <script src="/js/ru.js"></script>
        <script src="/js/tooltip.js"></script>
        <style>
            body{
                padding: 0 10px;
            }
            .divider td{
                background-color: #F5F5F5;
                color: #2d76a7;
            }
        </style>
    </head>

    <body>

    <h5><?= ($_GET['mode'] == 'global') ? 'Глобальные' : 'Локальные' ?> настройки модуля &laquo;<?= $settings['module_name'] ?>&raquo;</h5>
    <br>
    <form method="post" name="moduleForm" id="moduleForm">
        <table class="el_tbl" style="width: 95%;margin: 0 auto;">
            <?php
            for ($i = 0; $i < count($settings['settings']); $i++) {
                $row = $settings['settings'][$i];
                if($row['type'] == 'divider'){
                    echo '<tr class="divider"><td colspan=2>'.$row['name'].'</td></tr>';
                }else{
                    ?>
                    <tr>
                        <td>
                            <strong><?= $row['title'] ?></strong><br>
                            <small><?= $row['description'] ?></small>
                        </td>
                        <td>
                            <?php
                            $def = (isset($svalues[$row['name']])) ? $svalues[$row['name']] : $row['default'];
                            echo el_buildField($row['type'], $row['props'], $row['name'], $def, $row['required']);
                            ?>
                        </td>
                    </tr>
                    <?
                }
            }
            ?>
            <tr>
                <td><input name="Submit" type="submit" class="but agree" value="Сохранить"></td>
                <td>
                    <button class="but close" onClick="top.closeDialog()">Закрыть</button>
                </td>
            </tr>
        </table>
    </form>
    <?php

} else {
    echo '<h4 style="color:red">Для этого модуля не заданы параметры настроек</h4>';
}
?>
</body>
</html>
