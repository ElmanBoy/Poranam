<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php');
$requiredUserLevel = array(0, 1);
include($_SERVER['DOCUMENT_ROOT'] . "/editor/secure/secure.php");

/*$c = el_dbselect("SELECT * FROM sites", 0, $c, 'result', true);
$rc = el_dbfetch($c);
do{
    echo '<br>'.$coo = implode(',', getCoordsFromAddress($rc['adresses']));
    if($coo != ','){
        $u = el_dbselect("UPDATE sites SET coords='$coo' WHERE id=".intval($rc['id']), 0, $u, 'result', true);
    }
}while($rc = el_dbfetch($c));*/
//error_reporting(E_ALL);


?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Управление сайтами</title>
    <link href="style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="/js/jquery-1.11.0.min.js"></script>
    <script src="/js/tooltip.js"></script>
    <script type="text/javascript" src="/editor/e_modules/ckeditor2/ckeditor.js"></script>
    <script type="text/javascript" src="/editor/e_modules/ckfinder/ckfinder.js"></script>
    <script>
        function setcookie(name, value, expires, path, domain, secure)
        {
            document.cookie =	name + "=" + escape(value) +
                ((expires) ? "; expires=" + (new Date(expires)) : "") +
                ((path) ? "; path=/" : "; path=/") +
                ((domain) ? "; domain=" + domain : "") +
                ((secure) ? "; secure" : "");
        }


        /**
         * Получить значение куки по имени name
         *
         */
        function getcookie(name)
        {
            var cookie = " " + document.cookie;
            var search = " " + name + "=";
            var setStr = null;
            var offset = 0;
            var end = 0;

            if (cookie.length > 0)
            {
                offset = cookie.indexOf(search);

                if (offset != -1)
                {
                    offset += search.length;
                    end = cookie.indexOf(";", offset)

                    if (end == -1)
                    {
                        end = cookie.length;
                    }

                    setStr = unescape(cookie.substring(offset, end));
                }
            }

            return(setStr);
        }
        String.prototype.translit = (function () {
            var L = {
                    'А': 'a', 'а': 'a', 'Б': 'b', 'б': 'b', 'В': 'v', 'в': 'v', 'Г': 'g', 'г': 'g',
                    'Д': 'd', 'д': 'd', 'Е': 'e', 'е': 'e', 'Ё': 'yo', 'ё': 'yo', 'Ж': 'zh', 'ж': 'zh',
                    'З': 'z', 'з': 'z', 'И': 'i', 'и': 'i', 'Й': 'y', 'й': 'y', 'К': 'k', 'к': 'k',
                    'Л': 'l', 'л': 'l', 'М': 'm', 'м': 'm', 'Н': 'n', 'н': 'n', 'О': 'o', 'о': 'o',
                    'П': 'p', 'п': 'p', 'Р': 'r', 'р': 'r', 'С': 's', 'с': 's', 'Т': 't', 'т': 't',
                    'У': 'u', 'у': 'u', 'Ф': 'f', 'ф': 'f', 'Х': 'kh', 'х': 'kh', 'Ц': 'ts', 'ц': 'ts',
                    'Ч': 'ch', 'ч': 'ch', 'Ш': 'sh', 'ш': 'sh', 'Щ': 'sch', 'щ': 'sch',
                    'Ы': 'y', 'ы': 'y', 'Э': 'e', 'э': 'e', 'Ю': 'yu', 'ю': 'yu', 'ь': '', 'Ь': '', 'ъ': '', 'Ъ': '',
                    'Я': 'ya', 'я': 'ya', ' ': '-', '?': '', ',': '-', '.': '-'
                },
                r = '',
                k;
            for (k in L) r += k;
            r = new RegExp('[' + r + ']', 'g');
            k = function (a) {
                return a in L ? L[a] : '';
            };
            return function () {
                return this.replace(r, k);
            };
        })();

        $(document).ready(function () {
            $('*').tooltip({showURL: false});

            $(".tblVertMiddle tr td input[type=checkbox]").on("change", function () {
                var $tr = $(this).parent("label").parent("td").parent("tr");
                $tr.find(".times").css("display", $(this).prop("checked") ? "none" : "inline");
            });

            $(".tabs li").on("click", function () {
                var tabId = $(this).attr("id").replace("tab", "");
                $(".tabs li").removeClass("current");
                $(this).addClass("current");
                $(".tcontent").css("display", "none");
                $(".tab" + tabId).css("display", "table-row-group");
                setcookie("site_settings_tab", tabId, (new Date).getTime() + (2 * 365 * 24 * 60 * 60 * 1000));
            });

            $("input[name='short_name']").on("keyup", function (e) {
                var $target = $("input[name='domain']");
                $target.val($(this).val().translit());
                if ($.trim($target.val()).length === 0) {
                    $target.addClass("error");
                } else {
                    $target.removeClass("error");
                }
            });

            $("input, textarea").on("input", function () {
                if ($.trim($(this).val()).length === 0) {
                    $(this).addClass("error");
                } else {
                    $(this).removeClass("error");
                }
            });

            var CKEDITOR_BASEPATH = 'editor/e_modules/ckeditor2/';
            if (CKEDITOR.env.ie && CKEDITOR.env.version < 9)
                CKEDITOR.tools.enableHtml5Elements(document);
            CKEDITOR.config.height = window.innerHeight - <?=($row_content['kod'] != '') ? '300' : '280';?>;
            CKEDITOR.config.width = 'auto';


            var initEditor = (function () {
                var wysiwygareaAvailable = isWysiwygareaAvailable();

                return function () {
                    var editorElement = CKEDITOR.document.getById('description');

                    if (wysiwygareaAvailable) {
                        CKEDITOR.replace('description', {
                            toolbar :
                                [
                                    ['Source','-',''],
                                    ['Cut','Copy','Paste','PasteText','PasteFromWord','-'],
                                    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat','-'],
                                    ['Format','-','Bold','Subscript','Superscript'],
                                    ['NumberedList','BulletedList','Outdent','Indent','Blockquote','-'],
                                    ['JustifyLeft','JustifyCenter','JustifyRight','-'],
                                    ['Image','Table','HorizontalRule','SpecialChar','PageBreak','InsertPre','-'],
                                    ['Link','Unlink','Anchor','HorizontalRule','SpecialChar','ShowBlocks','Maximize'],

                                ],
                            extraPlugins: 'imageuploader,widget,widgetselection,clipboard,lineutils',//video,html5video,
                            filebrowserImageBrowseUrl: '/editor/e_modules/ckeditor2/plugins/imageuploader/main.php',
                            filebrowserImageUploadUrl: '/editor/e_modules/ckeditor2/plugins/imageuploader/imgupload.php',
                            filebrowserUploadUrl: '/editor/e_modules/ckeditor2/plugins/imageuploader/imgupload.php',
                            filebrowserBrowseUrl: '/editor/e_modules/ckeditor2/plugins/imageuploader/main.php'
                        });
                    } else {
                        editorElement.setAttribute('contenteditable', 'true');
                        CKEDITOR.inline('description');
                    }
                };

                function isWysiwygareaAvailable() {
                    if (CKEDITOR.revision == ('%RE' + 'V%')) {
                        return true;
                    }

                    return !!CKEDITOR.plugins.get('wysiwygarea');
                }
            })();
            initEditor();

            $("#addStuff").on("click", function(){
                var $table = $(".cheef_item:last");
                var $clone = $table.clone();
                $($table).after($clone);
                /*var $tables = $(".cheef_item");
                for(var i = 0; i < $tables.length; i++){
                    $($tables[i]).attr("id", i);
                }*/
            });

            var activeTab = getcookie("site_settings_tab");
            if(typeof  activeTab != "undefined"){
                $(".tabs li#tab" + activeTab).click();
            }
        });
    </script>
    <style>
        body {
            padding: 0 15px;
        }

        .el_tbl tr td {
            padding: 10px;
        }

        .tcontent {
            display: none;
        }

        .error {
            border-color: red;
        }

        #logoList {
            height: 225px;
            overflow: auto;
        }

        #logoList label {
            float: left;
            padding: 8px;
            margin-bottom: 15px;
            height: 100px;
            width: 90px;
            text-align: center;
            cursor: pointer;
        }
        #logoList label.selected{
            border: 1px dotted #39a8eb;
            width: 88px;
            height: 90px;
        }

        #logoList label img {
            display: block;
            vertical-align: middle;
            max-height: 82px;
            max-width: 80px;
            margin: 0 auto;
        }
        #logoList label .fileName{
            max-width: 80px;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
            overflow: hidden;
        }

        #logoList label input {
            margin: 0;
        }

        .tcontent textarea{
            max-width: 450px;
            min-height: 370px;
        }

        .tblVertMiddle tr td{
            padding: 4px;
        }

        .cheef_item{
            border: 1px solid #d7d7d7;
            margin-bottom: 5px;
            width:100%;
        }
        .cheef_item tr td input{
            width: 95%;
        }
        .cheef_item tr td button{
            float: right !important;
        }
        .cheef_item:first-child tr td button{
            display: none;
        }
        .cheef_item tr td button.showed{
            display: block;
            margin-left: 15px;
        }
        .cheef_item tr td button.hidden{
            margin-left: 15px;
            display: none;
        }
    </style>
</head>
<?php
$siteId = intval($_GET['id']);

$work_times = null;
if ($siteId > 0) {
    $s = el_dbselect("SELECT * FROM sites WHERE id = " . $siteId, 0, $s, 'row', true);
    $work_times = json_decode($s['work_times']);
}

function checkField($mode)
{
    $errStr = [];
    $errFields = [];
    $times = $_POST['from_time1'] . $_POST['to_time1'] .
        $_POST['from_time2'] . $_POST['to_time2'] .
        $_POST['from_time3'] . $_POST['to_time3'] .
        $_POST['from_time4'] . $_POST['to_time4'] .
        $_POST['from_time5'] . $_POST['to_time5'] .
        $_POST['from_time6'] . $_POST['to_time6'] .
        $_POST['from_time7'] . $_POST['to_time7'];
    switch ($mode) {
        case 'new':
            $fields = [
                'Полное наименование' => 'full_name',
                'Краткое наименование' => 'short_name',
                'Домен' => 'domain'
            ];
            break;
        case 'edit':

        case 'clone':
            $fields = $fields = [
                'Полное наименование' => $_POST['full_name'],
                'Краткое наименование' =>  $_POST['short_name'],
                'Домен' =>  $_POST['domain'],
                'Адреса' =>  $_POST['adresses'],
                'Телефоны' =>  $_POST['phones'],
                'Часы работы' => $times
            ];
            break;
    }

    foreach ($fields as $name => $value) {
        if (strlen(trim($value)) == 0) {
            $errStr[] = 'Заполните поле \"' . $name . '\"';
            $errFields[] = 'input[name=' . $value . '], textareat[name=' . $value . ']';
        }
    }

    if (count($errStr) > 0) {
        echo '<script>
        alert("Ошибка:\\n' . implode('\\n', $errStr) . '");
        $(document).ready(function(){
            $("' . implode(', ', $errFields) . '").addClass("error");
        });
        </script>';
        return false;
    }
    return true;
}

function buildWorkTimes(){
    $days = array('понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота', 'воскресенье');
    $work_times = array();
    for($i = 1; $i <= 7; $i++){
        $work_times[$i] = (isset($_POST['weekend'.$i])) ? array('weekend'.$i => true ) : array('from' => $_POST['from_time'.$i], 'to' => $_POST['to_time'.$i], 'weekend'.$i => false);
    }
    $work_times['dinner'] = (isset($_POST['wdinner'])) ? array('wdinner' => true ) : array('from' => $_POST['dinner_from'], 'to' => $_POST['dinner_to'], 'wdinner' => false);
    return json_encode($work_times);
}
//printVar($_FILES); printVar($_POST);//exit();
function buildCheefs(){
    $cheefs = array();
    for($i = 0; $i < count($_POST['position']); $i++){
        if(strlen($_POST['position'][$i]) > 0 ) {
            if (strlen($_FILES['portret']['name'][$i]) > 0) {
                $temp_name = el_genpass(5) . '_';
                if (el_resize_images($_FILES['portret']['tmp_name'][$i], $_FILES['portret']['name'][$i], 400, 400, $temp_name)) {
                    $_POST['portret'][$i] = "/images/" . el_translit($temp_name . $_FILES['portret']['name'][$i]);
                } else {
                    echo "<script>alert('Файл с названием \"" . $_FILES['portret']['name'][$i] . "\" не удалось закачать!')</script>";
                }
            } elseif (strlen($_FILES['new_portret']['name'][$i]) > 0) {
                $temp_name = el_genpass(5) . '_';
                if (el_resize_images($_FILES['new_portret']['tmp_name'][$i], $_FILES['new_portret']['name'][$i], 400, 400, $temp_name)) {
                    $_POST['portret'][$i] = "/images/" . el_translit($temp_name . $_FILES['new_portret']['name'][$i]);
                } else {
                    echo "<script>alert('Файл с названием \"" . $_FILES['portret']['name'][$i] . "\" не удалось закачать!')</script>";
                }
            }
            $cheefs[$i] = array(
                'position' => $_POST['position'][$i],
                'cheef_fio' => $_POST['cheef_fio'][$i],
                'cheef_phone' => $_POST['cheef_phone'][$i],
                'cheef_email' => $_POST['cheef_email'][$i],
                'portret' => $_POST['portret'][$i]
            );
        }
    }
    return json_encode($cheefs);
}





$errStr = [];

if ($_GET['action'] == 'new') {
    $caption = 'Создание нового сайта';
    $isNew = true;
    $saveButton = 'Создать';
    if (isset($_POST['Submit'])) {
        if (checkField($_GET['action'])) {
            $res = el_dbselect("INSERT INTO sites (`active`, `domain`, `full_name`, `short_name`, logo) 
            VALUES (0, '" . addslashes($_POST['domain']) . "', '" . addslashes($_POST['full_name']) . "', '" . addslashes($_POST['short_name']) . "', '" . addslashes($_POST['logo']) . "')",
                0, $res, 'result', true);
            if ($res == false) {
                $errStr[] = 'Не удалось создать сайт.';
            } else {
                echo '<script>alert("Сайт ' . $_POST['domain'] . '.' . $_SERVER['SERVER_NAME'] . ' создан"); top.reloadFrame();</script>';
            }
        }
    }
}

if ($_GET['action'] == 'edit') {
    $caption = 'Редактирование сайта &laquo;' . $s['short_name'] . '&raquo;';
    $saveButton = 'Сохранить';
    $isNew = false;

    if (isset($_POST['Submit'])) {
        if (checkField($_GET['action'])) {
            $res = el_dbselect("UPDATE sites SET 
                `domain` = '" . addslashes($_POST['domain']) . "', 
                `full_name` = '" . addslashes($_POST['full_name']) . "', 
                `short_name` = '" . addslashes($_POST['short_name']) . "', 
                `logo` = '" . addslashes($_POST['logo']) . "',
                `adresses` = '" . addslashes($_POST['adresses']) . "',
                `coords` = '" . implode(',', getCoordsFromAddress($_POST['adresses'])) . "',
                `phones` = '" . addslashes($_POST['phones']) . "',
                `fax` = '" . addslashes($_POST['fax']) . "',
                `email` = '" . addslashes($_POST['email']) . "',
                `instagramm` = '" . addslashes($_POST['instagramm']) . "',
                `facebook` = '" . addslashes($_POST['facebook']) . "',
                `vkontakte` = '" . addslashes($_POST['vkontakte']) . "',
                `twitter` = '" . addslashes($_POST['twitter']) . "',
                `mailru` = '" . addslashes($_POST['mailru']) . "',
                `work_times` = '".buildWorkTimes()."',
                `yandex_counter` = '".$_POST['yandex_counter'] . "',
                `google_counter` = '".$_POST['google_counter'] . "',
                `description` = '" . addslashes($_POST['description']) . "',
                `cheefs` = '" . addslashes(buildCheefs()) . "'
                WHERE id = '".$siteId."'",
                0, $res, 'result', true);
            if ($res == false) {
                $errStr[] = 'Не удалось сохранить изменения.';
            } else {
                echo '<script>alert("Сайт ' . $_POST['domain'] . '.' . $_SERVER['SERVER_NAME'] . ' сохранён"); top.reloadFrame();</script>';
            }
        }
    }
}

if ($_GET['action'] == 'clone') {
    $caption = 'Клонирование сайта &laquo;' . $s['short_name'] . '&raquo;';
    $isNew = false;
    $saveButton = 'Клонировать';
    $srcSiteId = $siteId;

    if (isset($_POST['Submit'])) {
        if (checkField($_GET['action'])) {
            $err = 0;
            $errStr = array();
            $exist = el_dbselect("SELECT COUNT(id) AS exist FROM sites WHERE domain='".addslashes($_POST['domain'])."'", 0, $exist, 'row', true);
            if($exist['exist'] > 0){
                $err++;
                $errStr[] = 'Такой домен уже используется.';
            }

            if($err == 0) {
                $insertArr = Array(
                    'domain' => $_POST['domain'],
                    'full_name' => $_POST['full_name'],
                    'short_name' => $_POST['short_name'],
                    'logo' => $_POST['logo'],
                    'adresses' => $_POST['adresses'],
                    'coords' => implode(',', getCoordsFromAddress($_POST['adresses'])),
                    'phones' => $_POST['phones'],
                    'fax' => $_POST['fax'],
                    'email' => $_POST['email'],
                    'instagramm' => $_POST['instagramm'],
                    'facebook' => $_POST['facebook'],
                    'vkontakte' => $_POST['vkontakte'],
                    'twitter' => $_POST['twitter'],
                    'mailru' => $_POST['mailru'],
                    'work_times' => buildWorkTimes(),
                    'cheefs' => buildCheefs()
                );
                $ns = el_dbinsert('sites', $insertArr, 'result');
                $siteId = mysqli_insert_id($dbconn);

                $copyCat = el_dbselect("INSERT INTO `cat` (site_id, parent, name, path, menu, nourl, ptext, 
                   sort, last_time, last_author, last_action, edit, view, redirect, `left`, bottom, cat_type, cat_id) 
                SELECT $siteId, parent, name, path, menu, nourl, ptext, 
                   sort, last_time, last_author, last_action, edit, view, redirect, `left`, bottom, cat_type, cat_id FROM `cat` WHERE `site_id` = $srcSiteId",
                    0, $copyCat, 'result', true);

                $copyContent = el_dbselect("INSERT INTO `content` (site_id, cat, path, text, count, caption, title, 
                   description, keywords, ptable, `left`, `right`, kod, template, edit, view) 
                SELECT $siteId, cat, path, text, count, caption, title, 
                   description, keywords, ptable, `left`, `right`, kod, template, edit, view FROM `content` WHERE `site_id` = $srcSiteId",
                    0, $copyContent, 'result', true);


            }
        }
    }
}

if (count($errStr) > 0) {
    echo '<script>alert("Ошибка:\\n' . implode('\\n', $errStr) . '")</script>';
}

$work_times = null;
if ($siteId > 0) {
    $s = el_dbselect("SELECT * FROM sites WHERE id = " . $siteId, 0, $s, 'row', true);
    $work_times = json_decode($s['work_times']);
}


?>
<script>
    var cheefs = <?=$s['cheefs']?>;

    function editStuff(num){
        $("#st" + num + " .position").html("<input type='text' name='new_position[]' value='" + $("#st" + num + " input[name='position[]']").val() + "'>");
        $("#st" + num + " .cheef_fio").html("<input type='text' name='new_cheef_fio[]' value='" + $("#st" + num + " input[name='cheef_fio[]']").val() + "'>");
        $("#st" + num + " .cheef_phone").html("<input type='text' name='new_cheef_phone[]' value='" + $("#st" + num + " input[name='cheef_phone[]']").val() + "'>");
        $("#st" + num + " .cheef_email").html("<input type='text' name='new_cheef_email[]' value='" + $("#st" + num + " input[name='cheef_email[]']").val() + "'>");
        $("#st" + num + " input[name='new_portret[]']").show();
        $("#st" + num + " button.showed").hide();
        $("#st" + num + " button.hidden").show();
        return false;
    }

    function saveStuff(num){
        var position = $("#st" + num + " input[name='new_position[]']"),
            cheef_fio = $("#st" + num + " input[name='new_cheef_fio[]']"),
            cheef_phone = $("#st" + num + " input[name='new_cheef_phone[]']"),
            cheef_email = $("#st" + num + " input[name='new_cheef_email[]']");
        position.after("<div class='position'>" + position.val() + "</div>").remove();
        $("#st" + num + " input[name='position[]']").val(position.val());
        cheef_fio.after("<div class='cheef_fio'>" + cheef_fio.val() + "</div>").remove();
        $("#st" + num + " input[name='cheef_fio[]']").val(cheef_fio.val());
        cheef_phone.after("<div class='cheef_phone'>" + cheef_phone.val() + "</div>").remove();
        $("#st" + num + " input[name='cheef_phone[]']").val(cheef_phone.val());
        cheef_email.after("<div class='cheef_email'>" + cheef_email.val() + "</div>").remove();
        $("#st" + num + " input[name='cheef_email[]']").val(cheef_email.val());

        $("#st" + num + " input[name='new_portret[]']").hide();
        $("#st" + num + " button.showed").show();
        $("#st" + num + " button.hidden").hide();
        return false;
    }

    function cancelStuff(num){
        $("#st" + num + " input[name='new_position[]']").after("<div class='position'>" + $("#st" + num + " input[name='position[]']").val() + "</div>")
            .remove();
        $("#st" + num + " input[name='new_cheef_fio[]']").after("<div class='cheef_fio'>" + $("#st" + num + " input[name='cheef_fio[]']").val() + "</div>")
            .remove();
        $("#st" + num + " input[name='new_cheef_phone[]']").after("<div class='cheef_phone'>" + $("#st" + num + " input[name='cheef_phone[]']").val() + "</div>")
            .remove();
        $("#st" + num + " input[name='new_cheef_email[]']").after("<div class='cheef_email'>" + $("#st" + num + " input[name='cheef_email[]']").val() + "</div>")
            .remove();
        $("#st" + num + " input[name='new_portret[]']").hide();
        $("#st" + num + " button.showed").show();
        $("#st" + num + " button.hidden").hide();
        return false;
    }

    function removeStuff(num){
        delete cheefs[num];
        $("#st" + num).remove();
    }

</script>


<body>

<h5><?= $caption ?></h5>
<form method="post" id="siteform" enctype="multipart/form-data">
    <small><sup class="red">*</sup> - поля обязательные для заполнения</small>
    <? if (!$isNew) { ?>
        <ul class="tabs">
            <li id="tab1" class="current">Основное</li>
            <li id="tab2">Контакты</li>
            <li id="tab3">Часы работы</li>
            <li id="tab5">Руководство</li>
            <li id="tab4">Счетчики</li>
        </ul>
    <? } ?>
    <table width="100%" border="0" cellspacing="0" cellpadding="5" class="el_tbl">
        <tbody class="tcontent tab1" style="display: table-row-group">
        <tr>
            <td>Полное наименование <sup class="red">*</sup></td>
            <td><input type="text" name="full_name" size="65" value="<?= htmlentities($s['full_name']) ?>"></td>
        </tr>
        <tr>
            <td>Краткое наименование <sup class="red">*</sup></td>
            <td><input type="text" name="short_name" size="65" value="<?= htmlentities($s['short_name']) ?>"></td>
        </tr>
        <tr>
            <td>Домен <sup class="red">*</sup></td>
            <td><input type="text" name="domain" size="65" value="<?= htmlentities($s['domain']) ?>"></td>
        </tr>
        <tr>
            <td>Герб территориальной единицы <sup class="red">*</sup></td>
            <td>
                <div id="logoList">
                    <?php
                    $lDir = $_SERVER['DOCUMENT_ROOT'] . '/images/logos/';
                    $extArr = array('png', 'jpg', 'jpeg', 'gif', 'svg');
                    $dir = dir($lDir);
                    while ($file = $dir->read()) {
                        if ($file != '.' && $file != '..' && !is_dir($lDir . $file)) {
                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            if (in_array($ext, $extArr)) {
                                $img = '/images/logos/' . $file;
                                echo '<label'.(($s['logo'] == $img) ? ' class="selected"' : '').'><img src="' . $img . '">
                                <span class="fileName" title="'.$file.'">'.$file.'</span> 
                                <input type="radio" name="logo" value="' . $img . '"'.(($s['logo'] == $img) ? ' checked' : '').'> выбрать</label>';
                            }
                        }
                    }
                    ?>

                </div>
            </td>
        </tr>
        </tbody>
        <tbody class="tcontent tab2">
        <tr>
            <td>Адреса <sup class="red">*</sup></td>
            <td><input type="text" name="adresses" size="65" value="<?= htmlentities($s['adresses']) ?>"></td>
        </tr>
        <tr>
            <td>Телефоны <sup class="red">*</sup></td>
            <td><input type="text" name="phones" size="65" value="<?= htmlentities($s['phones']) ?>"></td>
        </tr>
        <tr>
            <td>Факс</td>
            <td><input type="text" name="fax" size="65" value="<?= htmlentities($s['fax']) ?>"></td>
        </tr>
        <tr>
            <td>E-mail <sup class="red">*</sup></td>
            <td><input type="text" name="email" size="65" value="<?= htmlentities($s['email']) ?>"></td>
        </tr>
        <tr class="divider"><td colspan="2">Социальные сети</td></tr>
        <tr>
            <td>Инстаграм</td>
            <td><input type="text" name="instagramm" size="65" value="<?= htmlentities($s['instagramm']) ?>"></td>
        </tr>
        <tr>
            <td>Фейсбук</td>
            <td><input type="text" name="facebook" size="65" value="<?= htmlentities($s['facebook']) ?>"></td>
        </tr>
        <tr>
            <td>В Контакте</td>
            <td><input type="text" name="vkontakte" size="65" value="<?= htmlentities($s['vkontakte']) ?>"></td>
        </tr>
        <tr>
            <td>Твиттер</td>
            <td><input type="text" name="twitter" size="65" value="<?= htmlentities($s['twitter']) ?>"></td>
        </tr>
        <tr>
            <td>Майл.ру</td>
            <td><input type="text" name="mailru" size="65" value="<?= htmlentities($s['mailru']) ?>"></td>
        </tr>
        </tbody>
        <tbody class="tcontent tab3">
        <tr>
            <td>Часы работы <sup class="red">*</sup></td>
            <td>
                <table border="0" cellpadding="4" cellspacing="0" class="tblVertMiddle">
                    <tr>
                        <td>Пн.</td>
                        <td><span class="times"<?= ($work_times != null) ? (($work_times->{1}->{'weekend1'}) ? ' style="display: none"' : '') : '' ?>>
                        с <input type="time" name="from_time1" value="<?= ( $work_times->{1}->{'from'} != null) ? $work_times->{1}->{'from'} : '09:00' ?>"> до
                        <input type="time" name="to_time1" value="<?= ($work_times->{1}->{'to'} != null) ? $work_times->{1}->{'to'} : '18:00' ?>"></span>
                            <label><input type="checkbox" name="weekend1"
                            <?= ($work_times != null) ? (($work_times->{1}->{'weekend1'}) ? ' checked' : '') : '' ?>> выходной</label>
                        </td>
                    </tr>
                    <tr>
                        <td>Вт.</td>
                        <td><span class="times"<?= ($work_times != null) ? (($work_times->{2}->{'weekend2'}) ? ' style="display: none"' : '') : '' ?>>
                        с <input type="time" name="from_time2" value="<?= ($work_times->{2}->{'from'} != null) ? $work_times->{2}->{'from'} : '09:00' ?>"> до
                        <input type="time" name="to_time2" value="<?= ($work_times->{2}->{'to'} != null) ? $work_times->{2}->{'to'} : '18:00' ?>"></span>
                            <label><input type="checkbox" name="weekend2"
                                    <?= ($work_times != null) ? (($work_times->{2}->{'weekend2'}) ? ' checked' : '') : '' ?>> выходной</label>
                        </td>
                    </tr>
                    <tr>
                        <td>Ср.</td>
                        <td><span class="times"<?= ($work_times != null) ? (($work_times->{3}->{'weekend3'}) ? ' style="display: none"' : '') : '' ?>>
                        с <input type="time" name="from_time3" value="<?= ($work_times->{3}->{'from'} != null) ? $work_times->{3}->{'from'} : '09:00' ?>"> до
                        <input type="time" name="to_time3" value="<?= ($work_times->{3}->{'to'} != null) ? $work_times->{3}->{'to'} : '18:00' ?>"></span>
                            <label><input type="checkbox" name="weekend3"
                                    <?= ($work_times != null) ? (($work_times->{3}->{'weekend3'}) ? ' checked' : '') : '' ?>> выходной</label>
                        </td>
                    </tr>
                    <tr>
                        <td>Чт.</td>
                        <td><span class="times"<?= ($work_times != null) ? (($work_times->{4}->{'weekend4'}) ? ' style="display: none"' : '') : '' ?>>
                        с <input type="time" name="from_time4" value="<?= ($work_times->{4}->{'from'} != null) ? $work_times->{4}->{'from'} : '09:00' ?>"> до
                        <input type="time" name="to_time4" value="<?= ($work_times->{4}->{'to'} != null) ? $work_times->{4}->{'to'} : '18:00' ?>"></span>
                            <label><input type="checkbox" name="weekend4"
                                    <?= ($work_times != null) ? (($work_times->{4}->{'weekend4'}) ? ' checked' : '') : '' ?>> выходной</label>
                        </td>
                    </tr>
                    <tr>
                        <td>Пт.</td>
                        <td><span class="times"<?= ($work_times != null) ? (($work_times->{5}->{'weekend5'}) ? ' style="display: none"' : '') : '' ?>>
                        с <input type="time" name="from_time5" value="<?= ($work_times->{5}->{'from'} != null) ? $work_times->{5}->{'from'} : '09:00' ?>"> до
                        <input type="time" name="to_time5" value="<?= ($work_times->{5}->{'to'} != null) ? $work_times->{5}->{'to'} : '18:00' ?>"></span>
                            <label><input type="checkbox" name="weekend5"
                                    <?= ($work_times != null) ? (($work_times->{5}->{'weekend5'}) ? ' checked' : '') : '' ?>> выходной</label>
                        </td>
                    </tr>
                    <tr>
                        <td>Сб.</td>
                        <td><span class="times"<?= ($work_times != null) ? (($work_times->{6}->{'weekend6'}) ? ' style="display: none"' : '') : ' style="display: none"' ?>>
                        с <input type="time" name="from_time6" value="<?= ($work_times->{6}->{'from'} != null) ? $work_times->{6}->{'from'} : '09:00' ?>"> до
                        <input type="time" name="to_time6" value="<?= ($work_times->{6}->{'to'} != null) ? $work_times->{6}->{'to'} : '18:00' ?>"></span>
                            <label><input type="checkbox" name="weekend6"
                                    <?= ($work_times != null) ? (($work_times->{6}->{'weekend6'}) ? ' checked' : '') : '' ?>> выходной</label>
                        </td>
                    </tr>
                    <tr>
                        <td>Вс.</td>
                        <td><span class="times"<?= ($work_times != null) ? (($work_times->{7}->{'weekend7'}) ? ' style="display: none"' : '') : ' style="display: none"' ?>>
                        с <input type="time" name="from_time7" value="<?= ($work_times->{7}->{'from'} != null) ? $work_times->{7}->{'from'} : '09:00' ?>"> до
                        <input type="time" name="to_time7" value="<?= ($work_times->{7}->{'to'} != null) ? $work_times->{7}->{'to'} : '18:00' ?>"></span>
                            <label><input type="checkbox" name="weekend7"
                                    <?= ($work_times != null) ? (($work_times->{7}->{'weekend7'}) ? ' checked' : '') : '' ?>> выходной</label>
                        </td>
                    </tr>
                    <tr>
                        <td>Обед</td>
                        <td><span class="times"<?= ($work_times != null) ? (($work_times->{'dinner'}->{'wdinner'}) ? ' style="display: none"' : '') : ' style="display: none"' ?>>
                        с <input type="time" name="dinner_from" value="<?= ($work_times->{'dinner'}->{'from'} != null) ? $work_times->{'dinner'}->{'from'} : '13:00' ?>"> до
                        <input type="time" name="dinner_to" value="<?= ($work_times->{'dinner'}->{'to'} != null) ? $work_times->{'dinner'}->{'to'} : '14:00' ?>"></span>
                            <label><input type="checkbox" name="wdinner"
                                          <?= ($work_times != null) ? (($work_times->{'dinner'}->{'wdinner'}) ? ' checked' : '') : '' ?>> без обеда</label>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
        <tbody class="tcontent tab4">
        <tr>
            <td>ID Яндекс счетчика</td>
            <td><input type="text" name="yandex_counter" id="yandex_counter" value="<?= htmlentities($s['yandex_counter']) ?>"></td>
        </tr>
        <tr>
            <td>ID Google счетчика</td>
            <td><input type="text" name="google_counter" id="google_counter" value="<?= htmlentities($s['google_counter']) ?>"></td>
        </tr>
        </tbody>
        <tbody class="tcontent tab5">
        <tr>
            <td colspan="2">Структура <sup class="red">*</sup></td>
        </tr><td style="border-right: 1px solid #c7c7c7;">
        <?
        if(strlen($s['cheefs']) > 0) {
            $ch = json_decode($s['cheefs'], true);
            for ($c = 0; $c < count($ch); $c++) {
                if (strlen($ch[$c]['portret']) > 0) {
                    $port = '<img src="' . $ch[$c]['portret'] . '" width="200">';
                }else{
                    $port = '';
                }
                echo '<table class="el_tbl cheef_item" id="st'.$c.'">
                <tr><td rowspan="2">' . $port . '<input type="file" name="new_portret[]" style="display:none"></td>
                <td><div class="position">' . $ch[$c]['position'] . '</div></td></tr>
                  <tr><td><div class="cheef_fio">' . $ch[$c]['cheef_fio'] . '</div>
                  <div class="cheef_phone">' . $ch[$c]['cheef_phone'] . '</div>
                  <div class="cheef_email">' . $ch[$c]['cheef_email'] . '</div>
                  </td></tr>
                  <tr><td colspan="2">
                  <button class="but close showed" onclick="return removeStuff('.$c.')">Удалить</button>
                  <button class="but showed" onclick="return editStuff('.$c.')">Редактировать</button>
                  
                  <button class="but close hidden" onclick="return cancelStuff('.$c.')">Отмена</button>
                  <button class="but hidden" onclick="return saveStuff('.$c.')">Сохранить</button>
                  
                  <input type="hidden" name="position[]" value="' . $ch[$c]['position'] . '">
                  <input type="hidden" name="cheef_fio[]" value="' . $ch[$c]['cheef_fio'] . '">
                  <input type="hidden" name="cheef_phone[]" value="' . $ch[$c]['cheef_phone'] . '">
                  <input type="hidden" name="cheef_email[]" value="' . $ch[$c]['cheef_email'] . '">
                  <input type="hidden" name="portret[]" value="' . $ch[$c]['portret'] . '">
                  </td> </tr></table>';
            }
        }
        ?></td>
        <tr>
            <td colspan="2">
                <table class="el_tbl cheef_item">
                    <tr>
                        <td valign="top">Фотография<br>
                            <input type="file" name="portret[]"><br>
                            Ф.И.О.<br>
                            <input type="text" name="cheef_fio[]" value="">
                        </td>

                        <td style="width:50%">Должность<br>
                            <input type="text" name="position[]" value=""> <br>
                        Телефон<br>
                            <input type="tel" name="cheef_phone[]" value=""> <br>
                        Email<br>
                            <input type="tel" name="cheef_email[]" value="">
                        </td>
                    </tr>
                    <tr><td colspan="2"><button class="but close" onclick="$(this).parents('.cheef_item').remove();return false;">Удалить</button></td> </tr>
                </table>
                <button type="button" class="but" id="addStuff"><i class="material-icons">add_circle</i> Добавить сотрудника</button>
            </td>
        </tr>
        </tbody>
        <tr valign="baseline">
            <td colspan="2">
                <input name="Submit" type="submit" class="but agree" value="<?= $saveButton ?>">

                <button name="closewin" class="but close" id="closewin"
                        onClick="top.reloadFrame();top.closeDialog()">Закрыть
                </button>
            </td>
        </tr>
    </table>
</form>
</body>
</html>