<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php');
//error_reporting(E_ALL);
$requiredUserLevel = array(0, 1, 2, 3, 4);
include($_SERVER['DOCUMENT_ROOT'] . "/editor/secure/secure.php");

$site_id = intval($_SESSION['site_id']);
$user_id = intval($_SESSION['user_id']);

$themes = getRegistry('themes');
$status = getRegistry('messagestatus');

$where_site = '';
if ($_SESSION['user_level'] > 1) {
    $where_site = " AND site_id = $site_id";
}

$q = el_dbselect("SELECT * FROM catalog_messages_data WHERE 
id = " . intval($_GET['id']) . " $where_site ORDER BY field2 DESC, field4 DESC",
    20, $q, 'result', true);
$rq = el_dbfetch($q);
?>
<html>
<head>
    <title>Обращение</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="style.css?v=<?= el_genpass() ?>" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="/css/flatpickr.min.css">
    <script src="/js/jquery-1.11.0.min.js"></script>
    <script type="text/javascript" src="/editor/e_modules/ckeditor2/ckeditor.js"></script>
    <script type="text/javascript" src="/editor/e_modules/ckfinder/ckfinder.js"></script>
    <script>
        $(document).ready(function () {
            $("#showAnswer").on("click", function (e) {
                e.preventDefault();
                var $answerWrap = $("#answerWrap"),
                    $templates = $("#templates"),
                    $message = $(".message"),
                    $answerActions = $("#answerActions"),
                    $sendAnswer = $("#sendAnswer"),
                    $attachZone = $("#attachZone"),
                    duration = 200;
                if ($answerWrap.css("display") === "block") {
                    $sendAnswer.hide();
                    $attachZone.hide();
                    $answerActions.animate({left: '10px'}, duration);
                    $templates.animate({left: '-21%'}, duration);
                    $(this).html("<i class=\"material-icons\">forum</i> Ответить на обращение");
                    $answerWrap.hide();
                    $message.animate({width: '100%', height: '90%'}, duration);
                } else {
                    $answerActions.animate({left: '22.3%'}, duration);
                    $templates.animate({left: '5px'}, duration);
                    $(this).html("<i class=\"material-icons\">speaker_notes_off</i> Отменить ответ");
                    $message.animate({width: '78%', height: '38%'}, duration,
                        function () {
                            $answerWrap.show();
                            $attachZone.css("display", "inline");
                            $sendAnswer.show();
                        });

                }
                return false;
            });

            $("#attachZone input[type=file]").on("change", function(e){
                let maxSize = parseInt(("<?=ini_get("upload_max_filesize")?>").replace("M", ""));
                let file = $(this)[0].files;
                let fileArr = [];
                if(typeof file !== "undefined"){
                    for(var i = 0; i < file.length; i++) {
                        let size = Math.floor((file[i].size / 1024 / 1024) * 100) / 100;
                        if (size > maxSize) {
                            alert("Размер файла \"" + file[i].name + "\" составляет " + size + "Mb и превышает допустимый максимальный размер " + maxSize + "Mb");
                        } else {
                            fileArr.push(file[i].name);
                            $("#attachZone .removeUpload").css("display", "inline-block");
                            //console.log(data, size, file.type, maxSize);
                        }
                    }
                    var fileList = fileArr.join(", ");
                    $(this).parents(".attachRow").find(".fileInfo").html(fileList).attr("title", fileList);
                }
            });

            $("#attachZone .removeUpload").off("click").on("click", function(e){
                e.preventDefault();
                $("#attachZone input[type=file]").val("");
                $(".fileInfo").html("");
                $("#attachZone .removeUpload").hide();
                return false;
            });
        });
    </script>
    <style>
        #templates {
            border-right: 1px dashed #d2d0d0;
            width: 20%;
            top: 0;
            position: fixed;
            left: -21%;
            bottom: 5px;
            overflow: hidden;
        }

        #tmplList {
            padding: 6px;
            overflow: auto;
            margin-top: 10px;
            height: 93vh;
        }

        #templates .item {
            padding: 8px 4px;
            border-top: 1px solid #d2d0d0;
            border-bottom: 1px solid #d2d0d0;
            margin-top: -1px;
            cursor: pointer;
        }

        #templates .item:hover {
            background-color: #eaf4f9;
        }

        .message {
            width: 100%;
            height: 90%;
            overflow: auto;
            float: right;
        }

        #answerWrap {
            float: right;
            width: 78%;
            display: none;
        }

        #sendAnswer {
            margin-left: 15px;
            display: none;
        }

        #answerActions {
            position: fixed;
            left: 15px;
            bottom: 22px;
            height: 40px;
        }
        #attachZone{
            display: none;
        }
        #attachZone .attachRow{
            display: inline;
        }
        #attachZone input[type=file],
        #attachZone small{
            display: none;
        }

        #attachZone label{
            margin-left: 5px;
            margin-top: 0;
        }
        #attachZone label i{
            color: #fff;
        }
        #attachZone .fileInfo{
            font-size: smaller;
            display: inline-block;
            max-width: 250px;
            max-height: 32px;
            overflow-x: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
            margin-left: 5px;
        }
        #attachZone .removeUpload{
            display: none;
            vertical-align: middle;
            background-color: #cf4520;
            cursor: pointer;
            width:16px;
            height: 16px;
            padding: 4px;
        }
    </style>
</head>

<body>

<div id="templates">
    <strong>Шаблоны ответа:</strong>
    <div id="tmplList">
        <div class="item"><i class="material-icons">quickreply</i> Стандартная отписка</div>
        <div class="item"><i class="material-icons">quickreply</i> Ответ по постановке на учет как безработного</div>
        <div class="item"><i class="material-icons">quickreply</i> Перечень документов для получения субсидий</div>
    </div>
</div>
<?php
if (el_dbnumrows($q) > 0) {
    do {
        ?>
        <div class="message">
            <h4><?= $rq['field1'] ?><br>
                <?= $rq['field3'] ?>, тел.: <a href="tel:<?= $rq['field5'] ?>"><?= $rq['field5'] ?></a>, E-mail: <a
                        href="mailto:<?= $rq['field4'] ?>"><?= $rq['field4'] ?></a></h4>
            <h5><?= $themes[$rq['field7']] ?></h5>
            <span class="date"><?= $rq['field2'] ?> </span>
            <p><?= $status[$rq['field6']] ?></p>
            <?
            if (strlen(trim($rq['field9'])) > 0) {
                $fileArr = explode(' , ', $rq['field9']);
                echo '<p>Приложенные файлы:</p><ul>';
                for ($i = 0; $i < count($fileArr); $i++) {
                    $pathArr = explode('/', $fileArr[$i]);
                    echo '<li><a href="' . $fileArr[$i] . '">' . end($pathArr) . '</a></li>';
                }
                echo '</ul>';
            }
            ?>

        </div>
        <div id="answerWrap">
     <textarea cols="80" rows="20" name="answer">
     </textarea>
            <script src="/editor/visual_editor.php?class=answer&height=200&type=basic"></script>
        </div>
        <?php
    } while ($rq = el_dbfetch($q));
}
?>
<div id="answerActions">
    <button class="but" id="showAnswer"><i class="material-icons">forum</i> Ответить на обращение</button>
    <div id="attachZone">
        <div class="attachRow">
            <label class="but">
                <i class="material-icons">attach_file</i>
                <input type="file" name="files[]" multiple aria-multiselectable="true">
                <span>Приложить файлы</span>
            </label>
            <span class="fileInfo"></span>
            <span class="removeUpload" title="Очистить">
                <img src="/images/icons-svg/delete-white.svg">
            </span>
        </div>
    </div>
    <button class="but agree" id="sendAnswer"><i class="material-icons">send</i> Отправить ответ</button>
</div>
<input type="button" name="Button" value="Закрыть" class="but close" onClick="top.closeDialog()" style="position: fixed;bottom: 0;right: 10px;">
</body>
</html>
