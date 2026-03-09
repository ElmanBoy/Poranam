<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php');
//error_reporting(E_ALL);
$requiredUserLevel = array(0, 1, 2);
include_once($_SERVER['DOCUMENT_ROOT'] . "/editor/secure/secure.php");

$cat = intval($_GET['cat']);
//error_reporting(E_ALL);
if (!isset($_REQUEST['ajax'])){
    $title_catalog = el_dbselect("SELECT field FROM catalog_prop WHERE title='1' AND catalog_id='$catalog_id'", 0, $title_catalog, 'row');
    ?>
    <link href="/js/css/start/jquery.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="/js/jquery-1.11.0.min.js"></script>
    <link rel="stylesheet" href="/js/flatpickr/flatpickr.min.css">
    <link rel="stylesheet" href="/js/flatpickr/plugins/confirmDate/confirmDate.css">
    <script src="/js/flatpickr/flatpickr.js"></script>
    <script src="/js/flatpickr/plugins/confirmDate/confirmDate.js"></script>
    <script src="/js/flatpickr/l10n/ru.js"></script>
    <script src="/js/spectrum.js"></script>
    <link rel="stylesheet" href="/js/spectrum.css">

    <script type="text/javascript" src="/js/fine-uploader/jquery.fine-uploader.js"></script>
    <link rel="stylesheet" href="/js/fine-uploader/fine-uploader-gallery.css">
    <script language="javascript">
        var cat = '<?=$cat?>';

        function getChecked(obj) {
            var out = new Array();
            for (var i = 0; i < obj.length; i++) {
                out.push($(obj[i]).val());
            }
            return out;
        }

        function csvExport() {
            var pages = getChecked($('#exportcat input[type=checkbox]:checked'));
            $('#export').html('<img src="/images/loading.gif" align=absmiddle>&nbsp; Пожалуйста, подождите...');
            $.post('/editor/modules/catalog/index.php',
                {
                    catalog_id: '<?=$catalog_id?>',
                    cat: '<?=$cat?>',
                    'pages': pages.join('|'),
                    mode: 'ajax_export', ajax: '1'
                },
                function (data) {
                    $('#export').html(data)
                }
            );
        }

        function getDependList(obj, parent_catalog, child_catalog, target_field, parent_field, child_field) {
            $('#field' + target_field).after('<span id="preload"><img src="/images/loading.gif" align=absmiddle>&nbsp; Пожалуйста, подождите...</span>');
            $.post('/editor/modules/catalog/getDependList.php',
                {
                    'parent_catalog': parent_catalog, 'child_catalog': child_catalog, 'val': $(obj).val(),
                    'target_field': target_field, 'parent_field': parent_field, 'child_field': child_field
                },
                function (data) {/*alert(data);*/
                    $('#field' + target_field).html(data);
                    $('#preload').remove()
                }
            );
        }

        function getModels(obj) {
            $('#field3').after('<span id="preload"><img src="/images/loading.gif" align=absmiddle></span>');
            $.post('/editor/modules/catalog/getModels.php',
                {'val': $(obj).val()},
                function (data) {/*alert(data);*/
                    $('#field3').html('');
                    $('#field3').html(data);
                    $('#preload').remove()
                }
            );
        }

        function getModif(obj) {
            $('#field4').after('<span id="preload"><img src="/images/loading.gif" align=absmiddle></span>');
            $.post('/editor/modules/catalog/getModif.php',
                {'val': $(obj).val()},
                function (data) {
                    $('#preload, #modif_mess').remove();
                    $('#field4').html('');
                    if (data.length > 0) {
                        $('#field4').html(data);
                    } else {
                        $('#field4').after('<span id="modif_mess">Модификаций нет</span>')
                    }
                }
            );
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
                    'Я': 'ya', 'я': 'ya', ' ': '-', '?': '', ',': '-', '.': '-', '"': '', '@': '', '&': '', '«': '', '»': ''
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
        $(document).ready(function (e) {
            $("input[name='field<?=$title_catalog['field']?>']").keyup(function (e) {
                $("input[name='path']").val($(this).val().translit() + '-' + $("#goodid").val())
                $("input[name='title']").val($(this).val())
            });
            $("#allCheckGood").click(function () {
                $(".checkGood").prop("checked", $(this).prop("checked"));
            })
        });


        function opclose(id) {
            if (document.getElementById(id).style.display == "none") {
                document.cookie = "idshow[" + id + "]=Y; expires=Thu, 31 Dec 2020 23:59:59 GMT; path=/editor/;";
                document.getElementById(id).style.display = "block";
            } else {
                document.cookie = "idshow[" + id + "]=N; expires=Thu, 31 Dec 2020 23:59:59 GMT; path=/editor/;";
                document.getElementById(id).style.display = "none";
            }
            ;
        }

        function selectMode(divId) {
            var p = document.getElementById('addForm').children;
            var d = document.getElementById(divId);
            for (var i = 0; i < p.length; i++) {
                if (p[i].tagName == 'DIV') p[i].style.display = 'none';
                document.cookie = "idshow[" + p[i].id + "]=N; expires=Thu, 31 Dec 2020 23:59:59 GMT; path=/editor/;";
            }
            d.style.display = 'block';
            document.cookie = "idshow[" + divId + "]=Y; expires=Thu, 31 Dec 2020 23:59:59 GMT; path=/editor/;";
        }

        function check(item_name) {
            var OK = confirm('Вы действительно хотите удалить запись "' + item_name + '" ?');
            if (OK) {
                return true
            } else {
                return false
            }
        }

        function goGroupAction(obj) {
            if ($(obj).val().length > 0) {
                var idColl = $(".checkGood:checked");
                var idCollLength = idColl.length;
                var selectedIds = [];
                var actionMessage;
                if (idCollLength > 0) {
                    for (var i = 0; i < idCollLength; i++) {
                        selectedIds[i] = idColl[i].value;
                    }
                    $("#selectedId").val(selectedIds.join('|'));
                    switch ($(obj).val()) {
                        case "delete":
                            actionMessage = "удалить";
                            break;
                        case "activate":
                            actionMessage = "активировать";
                            break;
                        case "deactivate":
                            actionMessage = "деактивировать";
                            break;
                    }
                    if (confirm("Вы уверены, что хотите " + actionMessage + " сразу несколько позиций?")) {
                        document.forms['groupActionFrm'].submit();
                    }
                } else {
                    alert("Не выбрана ни одна позиция!");
                    $(obj).val("");
                }
            }
        }

        function cloneRow(id, catalog_id) {
            event.stopPropagation();
            event.preventDefault();
            event.cancelBubble = true;


            $.post("/editor/modules/catalog/getRow.php", {id: id, catalog_id: catalog_id}, function (data) {
                var data = JSON.parse(data);
                var current = data.exist;
                var next = data.new;
                current.goodid = parseInt(next.new_id) + 1;
                current.sort = parseInt(next.new_sort) + 1;
                window.scrollTo(0, 1000000);
                for (field in current) {
                    var input = $("#addForm #" + field);
                    if ($("div").is("#" + field + "Upload")) {
                        $("#addForm #" + field).val(current[field]);
                    }
                    if (input.attr("type") != "file" && $("div").is("#" + field + "Upload") == false) {
                        $("#addForm #" + field).val(current[field]);
                    } else {
                        var files = current[field].split(' , ');
                        $("#" + field + "Files").remove();
                        var html = '';
                        for (var i = 0; i < files.length; i++) {
                            html += '<div style="display: inline-block; position: relative; margin-right: 5px;" id="thumbE' + i + '">' +
                                '<img src="' + files[i] + '" title="' + files[i] + '" border="0">' +
                                '<img title="Удалить" onclick="swf_delImg(\'' + files[i] + '\', \'thumbE' + i + '\', \'' + field + '\')"' +
                                'src="/images/components/ico_del.png" style="position:absolute; top:0; right:0; cursor:pointer"></div>';
                        }
                        input.after('<div id="' + field + 'Files">' + html + '</div>');
                    }
                }
            })
        }

        function MM_openBrWindow(theURL, winName, features, myWidth, myHeight, isCenter) { //v3.0
            top.MM_openBrWindow(theURL, winName, features, myWidth, myHeight, isCenter);
        }
    </script>
    <style type="text/css">
        <!--
        .style1 {
            color: #009900
        }

        .style2 {
            color: #FF0000
        }

        .pagenavi a.nom {
            padding: 2px 5px;
            border: 1px solid #069;
            margin: 2px 2px;
        }

        .pagenavi a.act {
            color: #999;
            cursor: default;
            background-color: #FFF;
            border: 1px solid #999;
        }

        -->
    </style>
    <?
    include_once $_SERVER['DOCUMENT_ROOT'] . '/Connections/site_props.php';
    if (isset($_POST['delShedule']) && $_POST['delShedule'] == 'now') {
        if (intval($_POST['keepRows' . $cat]) > 0) {
            $max = el_dbselect("SELECT id FROM catalog_" . $catalog_id . "_data ORDER BY id ASC",
                intval($_POST['keepRows' . $cat]), $max, 'result', true);
            $rmax = el_dbfetch($max);
            $maxDelId = 0;
            do {
                $maxDelId = $rmax['id'];
            } while ($rmax = el_dbfetch($max));
            if (el_dbselect("DELETE FROM catalog_" . $catalog_id . "_data WHERE id>" . $maxDelId, 0, $res, 'result', true) != false) {
                el_2ini('dateView' . $cat, date('Y-m-d'));
            }
        }
        if (intval($_POST['keepDays' . $cat]) > 0) {
            if (el_dbselect("DELETE FROM catalog_" . $catalog_id . "_data 
					   WHERE TO_DAYS(NOW()) - TO_DAYS(import_date) > " . intval($_POST['keepDays' . $cat]), 0, $res, 'result', true) != false
            ) {
                el_2ini('dateView' . $cat, date('Y-m-d'));
            }
        }
        echo '<script>alert("Заданные записи удалены.")</script>';
    }

    if (strlen($_FILES['fileUpload']['name']) > 0) {
        el_2ini('import_separator' . $cat, $_POST['separetor']);
        el_2ini('import_row_separator' . $cat, $_POST['row_separetor']);
        el_2ini('import_start' . $cat, $_POST['start']);
        el_2ini('exchange', $_POST['exchange']);
        $terget_file = $_SERVER['DOCUMENT_ROOT'] . '/files/import/' . el_translit($_FILES['fileUpload']['name']);
        $fieldList = el_dbselect("SELECT field FROM catalog_prop WHERE catalog_id='$catalog_id' ORDER BY sort", 0, $fieldList);
        $err = 0;
        $errStr = array();
        if (move_uploaded_file($_FILES['fileUpload']['tmp_name'], $terget_file)) {
            $fp1 = fopen($terget_file, 'rb');
            $f = fread($fp1, filesize($terget_file));
            fclose($fp1);
            $fp2 = eregi_replace("/\"([^\"]*)\"/im", "\\1", $f);
            $fp = explode("\n", $fp2);
            if (count($fp) > 0) {
                if ($_POST['addmethod'] == 'rewrite') {
                    $cats = array();
                    for ($co = $_POST['start'] - 1; $co < count($fp); $co++) {
                        if (strlen($fp[$co]) > 0) {
                            $delRow = explode($_POST['row_separetor'], str_replace("\r\n", '', $fp[$co]));
                            $cats[] = trim($delRow[0]);
                        }
                    }
                    reset($fp);
                    $cats = array_unique($cats);
                    if (count($cats) > 0) {
                        $delQuery = (count($cats) > 1) ? " cat=" . @implode(' OR cat=', $cats) : " cat=" . $cats[0];
                        el_dbselect("DELETE FROM `catalog_" . $catalog_id . "_data` 
					WHERE $delQuery", 0, $res, 'result', true);
                    } else {
                        $err++;
                        $errStr[] = 'В импортируемом файле не указаны id разделов!\\n';
                    }
                }
                $insertQuery = array();

                for ($co = $_POST['start'] - 1; $co < count($fp); $co++) {
                    if (strlen($fp[$co]) > 0) {
                        $impRow = str_getcsv(str_replace("\r\n", '', $fp[$co]), $_POST['row_separetor']);
                        $p = 4;
                        if ($co > $_POST['start'] - 1) {
                            mysqli_data_seek($fieldList, 0);
                            $fr = el_dbfetch($fieldList);
                        }
                        do {
                            //Собираем строку с названиями полей
                            if (strlen($fr['field']) > 0) {
                                $impRow[$p] = iconv('Windows-1251', 'UTF-8', trim($impRow[$p]));
                                $insertQuery['field' . $fr['field']] = str_replace('^', $_POST['row_separetor'], $impRow[$p]);

                                $p++;
                            }
                        } while ($fr = el_dbfetch($fieldList));
                        //ID раздела ; Название раздела ; Активность ; Сортировка ;
                        $insertQuery['path'] = el_translit($impRow[intval($title_catalog['field'] + 3)], 'path');
                        $insertQuery['cat'] = trim($impRow[0]);
                        $insertQuery['active'] = trim($impRow[2]);
                        $insertQuery['sort'] = trim($impRow[3]);
                        //Вставляем строку
                        if (el_dbinsert('catalog_' . $catalog_id . '_data', $insertQuery, 'Указаны не все данные!') == FALSE) {
                            $errStr[] = 'Не удалось добавить данные в каталог!\\nВозможно, структуры файла и каталога не совпадают в строке ' . $co . '.';
                            $err++;
                        }
                    }
                }

                if ($err == 0) {
                    el_2ini('last_update_catalog_date' . $cat, date("Y-m-d"));
                    el_2ini('last_update_catalog_time' . $cat, date("H:i:s"));
                    unlink($terget_file);
                } else {
                    echo '<script>alert("' . implode('', $errStr) . '")</script>';
                }
            } else {
                echo '<script>alert("Не удается прочесть файл \"' . $_FILES['fileUpload']['name'] . '\"\\nВозможно, файл не закачан или удален.")</script>';
            }
        } else {
            echo '<script>alert("Не удалось закачать файл!\\nВозможно, не разрешен доступ на запись в папку /files/import")</script>';
        }
    }

    if (isset($_FILES['excelFile'])) {
        if (strlen($_FILES['excelFile']['name']) > 0) {
            $newFileName = el_uploadUnique('excelFile', 'files');

            if (parseExcelToDB($cat, $newFileName)) {
                echo '<script>alert("Импорт документа успешно завершен!")</script>';
            } else {
                echo '<script>alert("Ошибка импорта документа!")</script>';
            }
            unlink($_SERVER['DOCUMENT_ROOT'] . $newFileName);
        }
    }
    ?>
    <table width="100%" border="0" cellpadding="3" cellspacing="0" class="el_tbl" title="Фильтр списка записей">
        <tr>
            <td align="right" valign="middle" style="cursor:pointer; color:#003399; font-weight:bold"
                onClick="opcloseFilter('sortform<?= $cat ?>')"><img src="/editor/img/up.gif" width="7" height="7"
                                                                    align="left">
                <div id="editor_button" style="width:98%; text-align:left">Фильтровать записи</div>
            </td>
        </tr>
    </table>
    <div id="sortform<?= $cat ?>" style="display:<? if ($_COOKIE['idshow']['sortform' . $cat] != "Y") {
        echo "none";
    } else {
        echo "block";
    }; ?>; border:2px solid #CCDCE6;">
        <form method="get" action="#start_list">
            <table border="0" cellspacing="0" cellpadding="3" class="el_tbl">
                <tr>
                    <td align='right'>ID</td>
                    <td><input type="text" size="10" name="id"></td>
                </tr>
                <tr>
                    <td align='right'>Номер</td>
                    <td><input type="text" size="10" name="sort"></td>
                </tr>
                <?

                $query_cat_form = "SELECT * FROM catalog_prop WHERE catalog_id='" . $catalog_id . "' AND search=1 ORDER BY sort";
                $cat_form = el_dbselect($query_cat_form, 0, $cat_form);
                $row_cat_form = el_dbfetch($cat_form);
                $totalRows_cat_form = mysqli_num_rows($cat_form);

                $queryString_catalog = "";
                if (!empty($_SERVER['QUERY_STRING'])) {
                    $params = explode("&", $_SERVER['QUERY_STRING']);
                    $newParams = array();
                    while (list($key, $val) = each($params)) {
                        if (strlen($val) > 0) {
                            if (stristr($val, "pageNum_catalog") == false) {
                                array_push($newParams, $val);
                            }
                        }
                    }
                    if (count($newParams) != 0) {
                        $queryString_catalog = "&" . htmlentities(implode("&", $newParams));
                    }
                }


                if ($totalRows_cat_form > 0) {


                    $query_cat_form1 = "SELECT * FROM catalogs WHERE catalog_id='" . $catalog_id . "'";
                    $cat_form1 = el_dbselect($query_cat_form1, 0, $cat_form1);
                    $row_cat_form1 = el_dbfetch($cat_form1);

                    $price_field = array();
                    $co = 0;
                    do {


                        $query_sort = "SELECT field" . $row_cat_form['field'] . " FROM catalog_" . $catalog_id . "_data WHERE cat = '" . $cat . "' ORDER BY sort ASC";//
                        $sort = el_dbselect($query_sort, 0, $sort);
                        $row_sort = el_dbfetch($sort);
                        $filter = array();
                        do {
                            array_push($filter, $row_sort['field' . $row_cat_form['field']]);
                        } while ($row_sort = el_dbfetch($sort));
                        $filter = array_unique($filter);
                        sort($filter);
                        $opt = "";
                        for ($i = 0; $i < count($filter); $i++) {
                            if (strlen($filter[$i]) > 0) {
                                if ($filter[$i] == str_replace("``", "\"", $_POST['field' . $row_cat_form['field']])) {
                                    $sel = "selected";
                                } else {
                                    $sel = "";
                                }
                                $opt .= "<OPTION value=\"" . str_replace("\"", "``", $filter[$i]) . "\" " . $sel . ">" . $filter[$i] . "</OPTION>\n";
                            }
                        }

                        switch ($row_cat_form['type']) {
                            case "textarea":
                                $row_cat_form['size'] = 50;
                                $input = "input";
                                $prop = "";
                                $output = "";
                                break;
                            case "text":
                                $input = "input";
                                $prop = "";
                                $output = "";
                                break;
                            case "select":
                                $input = "textarea";
                                $prop = "cols=30 rows=5";
                                $output = "</textarea><br>Здесь вписываются строки списка через точку с запятой ';'";
                                break;
                            case "option":
                                $item = str_replace(";", "\n<option>", $row_cat_form['options']);
                                $output = "<select name='field" . $row_cat_form['field'] . "' size=" . $row_cat_form['size'] . ">\n<option></option>\n<option>" . $item . "</select>";
                                break;
                            case "optionlist":
                                $item = str_replace(";", "\n<option>", $row_cat_form['options']);
                                $output = "<select name='field" . $row_cat_form['field'] . "[]' size=" . $row_cat_form['size'] . " multiple>\n<option></option>\n<option>" . $item . "</select>";
                                break;
                            case "list_fromdb":
                                $list_field = el_dbselect("select id, field" . $row_cat_form['from_field'] . " from catalog_" . $row_cat_form['listdb'] . "_data ORDER BY sort ASC", 0, $list_field);
                                $row_list_field = el_dbfetch($list_field);
                                $itemlist = '';
                                do {
                                    $itemlist .= "<option value='" . $row_list_field['id'] . "'>" . $row_list_field["field" . $row_cat_form['from_field']] . "</option>\n";
                                } while ($row_list_field = el_dbfetch($list_field));
                                $output = "<select name='field" . $row_cat_form['field'] . "[]' size=" . $row_cat_form['size'] . " multiple>\n<option></option>\n" . $itemlist . "</select>";
                                break;
                            case "checkbox":
                                $input = "input";
                                $prop = " value='" . $row_cat_form['name'] . "'";
                                $output = "";
                                break;
                            case "radio":
                                $input = "input";
                                $prop = "";
                                $output = "";
                                break;
                            case "small_image":
                                $input = "input";
                                $prop = "";
                                $output = "<br>Укажите местонахождение картинки для предпросмотра на Вашем компьютере для закачки на сервер";
                                $row_cat_form['type'] = "file";
                                break;
                            case "big_image":
                                $input = "input";
                                $prop = "";
                                $output = "<br>Укажите местонахождение картинки на Вашем компьютере для закачки на сервер";
                                $row_cat_form['type'] = "file";
                                break;
                            case "file":
                                $input = "input";
                                $prop = "";
                                $output = "<br>Здесь указывается местонахождение файла на Вашем компьютере для закачки на сервер";
                                break;
                            case "price":
                                $input = "input";
                                $prop = "";
                                $price_field['field'][$co] = 'field' . $row_cat_form['field'];
                                $price_field['name'][$co] = $row_cat_form['name'];
                                $output = " " . $row_cat_form1['currency'];
                                $co++;
                                break;
                            case "calendar":
                                $input = "input";
                                $row_cat_form['type'] = "text";

                                $prop = ($row_cat_form['name'] == 'Дата окончания публикации') ? " value=''" : " value='" . date('Y-m-d H:s') . "'";
                                $output = " <script type=\"text/javascript\">$(function() {
									    $(\"#field" . $row_cat_form['field'] . "\").flatpickr({
									        enableTime: false, 
									        allowInput: true, 
									        locale: 'ru', 
									        time_24hr: true,
									        'plugins': [new confirmDatePlugin({})]
									        });
										});
										</script>";
                                break;
                            case "datetime":
                                $input = "input";
                                $row_cat_form['type'] = "text";
                                $prop = " value='" . date('Y-m-d H:s') . "'";
                                $output = " <script type=\"text/javascript\">$(function() {
									    $(\"#field" . $row_cat_form['field'] . "\").flatpickr({
									        enableTime: true, 
									        allowInput: true, 
									        locale: 'ru', 
									        time_24hr: true,
									        'plugins': [new confirmDatePlugin({})]
									        });
										});
										</script>";
                                break;
                            case "multi_date":
                                $input = "input";
                                $row_cat_form['type'] = "text";
                                $prop = " value='" . date('Y-m-d H:s') . "'";
                                $output = " <script type=\"text/javascript\">$(function() {
									    $(\"#field" . $row_cat_form['field'] . "\").flatpickr({
									        enableTime: false, 
									        allowInput: true,
									        mode: 'multiple',
									        locale: 'ru', 
									        time_24hr: true,
									        'plugins': [new confirmDatePlugin({})]
									        });
										});
										</script>";
                                break;
                            case "range_date":
                                $input = "input";
                                $row_cat_form['type'] = "text";
                                $prop = " value='" . date('Y-m-d H:s') . "'";
                                $output = " <script type=\"text/javascript\">$(function() {
									    $(\"#field" . $row_cat_form['field'] . "\").flatpickr({
									        enableTime: false, 
									        allowInput: true,
									        mode: 'range',
									        locale: 'ru', 
									        time_24hr: true,
									        'plugins': [new confirmDatePlugin({})]
									        });
										});
										</script>";
                                break;
                            case "calendarext":
                                $output = '<iframe src="/editor/modules/catalog/calendar.php?field=field' . $row_cat_form['field'] . 'F&frame=ext_calendarF" frameborder="0" style="visibility:hidden" width="285" height="210" id="ext_calendarF"></iframe>';
                                $input = "input";
                                $prop = "id='field" . $row_cat_form['field'] . "F'";
                                $row_cat_form['type'] = "hidden";
                                break;
                        }
                        if ($row_cat_form['type'] == 'option' || $row_cat_form['type'] == 'optionlist' || $row_cat_form['type'] == 'list_fromdb') {
                            echo "<tr><td align='right' valign='top'>" . $row_cat_form['name'] . ": </td><td>$output</td>";
                        } else {
                            echo "<tr><td align='right' valign='top'>" . $row_cat_form['name'] . ": </td><td>$script<$input type='" . $row_cat_form['type'] . "' name='field" . $row_cat_form['field'] . "' size='" . $row_cat_form['size'] . "' $prop>$output</td>";
                        }
                        /*
    echo "<tr><td align='right' valign='top'><b>".$row_cat_form['name'].":</b> </td>
    <td>введите $script<$input type='".$row_cat_form['type']."' name='field".$row_cat_form['field']."' size='".$row_cat_form['size']."' $prop>$output</td>
    <td>или выберите
    <SELECT name='field".$row_cat_form['field']."_sel' onchange=field".$row_cat_form['field'].".value=this.options[this.selectedIndex].value>
    <OPTION value='' selected>Показать все</OPTION>".$opt."</SELECT>
    </td></tr>";*/

                    } while ($row_cat_form = el_dbfetch($cat_form));
                } else {
                    echo "<h4 align=center>Не заданы поля для сортировки</h4>";
                }
                ?>
                </td></tr>
                <tr>
                    <td colspan="3" align="center"><input type="hidden" name="frm_filter" value="search"><input
                                type="hidden" name="cat" value="<?= $cat ?>">
                        <input type="submit" value="Фильтровать" class="but">
                        <? if (isset($_GET['frm_filter'])) { ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button"
                                                                                                    onClick="location.href='?cat=<?= $cat ?>'"
                                                                                                    value="Показать все"
                                                                                                    class="but">
                        <? } ?>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <?php

    $currentPage = $_SERVER["PHP_SELF"];

// Смотрим структуру каталога

    $query_cat_form = "SELECT * FROM catalog_prop WHERE catalog_id='$catalog_id' ORDER BY sort";
    $cat_form = el_dbselect($query_cat_form, 0, $cat_form);
    $row_cat_form = el_dbfetch($cat_form);
    $totalRows_cat_form = mysqli_num_rows($cat_form);


    if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "catedit")) {
        $updateSQL = sprintf("UPDATE catalog_" . $catalog_id . "_data SET active=%s, sort=%s WHERE id=%s",
            GetSQLValueString(isset($_POST['active']) ? "true" : "", "defined", "'1'", "'0'"),
            GetSQLValueString($_POST['sort'], "int"),
            GetSQLValueString($_POST['id'], "int"));


        $Result1 = el_dbselect($updateSQL, 0, $Result1);
        el_genSiteMap();
        echo "<script>alert('Изменения сохранены!')</script>";
    }

    if ((isset($_POST['action'])) && ($_POST['action'] == "delcat")) {
        if ((isset($_POST['id'])) && ($_POST['id'] != "")) {
            //Если в этой строке каталога есть поля типа Файл, то узнаем номер филда и читаем из него путь до файла, а затем удаляем
            $f = el_dbselect("SELECT field FROM catalog_prop WHERE catalog_id='$catalog_id' AND type='file'", 0, $f, 'result', true);
            if (mysqli_num_rows($f) > 0) {
                $rf = el_dbfetch($f);
                $delFields = array();
                do {
                    $delFields[] = 'field' . $rf['field'];
                } while ($rf = el_dbfetch($f));
                $d = el_dbselect("SELECT " . implode(', ', $delFields) . " FROM catalog_" . $catalog_id . "_data WHERE id=" . intval($_POST['id']), 0, $d, 'row', true);
                for ($c = 0; $c < count($delFields); $c++) {
                    if (strlen(trim($d[$delFields[$c]])) > 0 && !unlink($_SERVER['DOCUMENT_ROOT'] . $d[$delFields[$c]])) echo '<script>alert("Не удалось удалить файл ' . $d[$delFields[$c]] . '")</script>';
                }
            }
            $deleteSQL = sprintf("DELETE FROM catalog_" . $catalog_id . "_data WHERE id=%s",
                GetSQLValueString($_POST['id'], "int"));
            $Result1 = el_dbselect($deleteSQL, 0, $Result1);
            el_genSiteMap();
        }
    }

//Групповые операции
    if (strlen($_POST['groupAction']) > 0) {
        $ids = @explode('|', $_POST['selectedId']);
        switch ($_POST['groupAction']) {
            case 'delete':
                //Если в этом каталоге есть поля типа Файл, то узнаем номер филда и читаем из него путь до файла, а затем удаляем
                $f = el_dbselect("SELECT field FROM catalog_prop WHERE catalog_id='$catalog_id' AND type='file'", 0, $f, 'result', true);
                if (mysqli_num_rows($f) > 0) {
                    $rf = el_dbfetch($f);
                    $delFields = array();
                    do {
                        $delFields[] = 'field' . $rf['field'];
                    } while ($rf = el_dbfetch($f));
                    for ($i = 0; $i < count($ids); $i++) {
                        $d = el_dbselect("SELECT " . implode(', ', $delFields) . " FROM catalog_" . $catalog_id . "_data WHERE id=" . intval($ids[$i]), 0, $d, 'row', true);
                        for ($c = 0; $c < count($delFields); $c++) {
                            if (strlen(trim($d[$delFields[$c]])) > 0 && !unlink($_SERVER['DOCUMENT_ROOT'] . $d[$delFields[$c]])) echo '<script>alert("Не удалось удалить файл ' . $d[$delFields[$c]] . '")</script>';
                        }
                    }
                }
                $query = "DELETE FROM catalog_" . $catalog_id . "_data WHERE id=" . implode(" OR id=", $ids);
                break;
            case 'activate':
                $query = "UPDATE catalog_" . $catalog_id . "_data SET active=1 WHERE id=" . implode(" OR id=", $ids);
                break;
            case 'deactivate':
                $query = "UPDATE catalog_" . $catalog_id . "_data SET active=0 WHERE id=" . implode(" OR id=", $ids);
                break;
        }
        $res = el_dbselect($query, 0, $res);
    }


//Сохранение параметров отображения каталога
    if (isset($_POST['setRows'])) {
        el_2ini('showRow' . $cat, intval($_POST['showRow' . $cat]));
        $sa = (isset($_POST['showAll' . $cat])) ? 1 : 0;
        el_2ini('showAll' . $cat, $sa);
    }

    if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "newitem")) {

        if ($totalRows_cat_form > 0) {

            if (isset($_POST['cats'])) {
                $cat_post = (count($_POST['cats']) > 1) ? ' ' . implode(' , ', $_POST['cats']) . " ' , " : implode('', $_POST['cats']) . " , ";
            } else {
                $cat_post = $cat . " , ";
            }
            $query_cat_form1 = "SELECT * FROM catalogs WHERE catalog_id='" . $catalog_id . "'";
            $cat_form1 = el_dbselect($query_cat_form1, 0, $cat_form1);
            $row_cat_form1 = el_dbfetch($cat_form1);

            $query_field = "";
            $temp_name = $_POST['goodid'] . '_' . el_genpass(5) . '_';
            do {
                $field_number = "field" . $row_cat_form['field'];
                switch ($row_cat_form['type']) {
                    case "checkbox":
                    case "radio":
                        $type = "TEXT";
                        break;
                    case "textarea":
                    case "optionlist":
                    case "option":
                    case "list_fromdb":
                    case "full_html":
                    case "select":
                        $type = "LONGTEXT";
                        break;
                    case "calendar":
                        $type = "DATE";
                        break;
                    case "calendarext":
                        $type = "TEXT";
                        $crop = array();
                        $crop = explode(",", $_POST[$field_number]);
                        asort($crop);
                        $_POST[$field_number] = implode(",", $crop);
                        break;
                    case "small_image":
                        $type = "TEXT";
                        if (!empty($_FILES[$field_number]['name'])) {
                            if (el_resize_images($_FILES[$field_number]['tmp_name'],
                                el_translit($_FILES[$field_number]['name']), $row_cat_form1['small_size'], $row_cat_form1['small_size'], $temp_name . 'small_')) {
                                $_POST[$field_number] = "/images/" . $_SESSION['site_id'] . '/' . el_translit($temp_name . 'small_' . $_FILES[$field_number]['name']);
                            } else {
                                echo "<script>alert('Файл для предпросмотра с названием \"" . $_FILES[$field_number]['name'] . "\" не удалось закачать!')</script>";
                            }
                        }

                        break;
                    case "big_image":
                        $type = "TEXT";
                        if (!empty($_FILES[$field_number]['name'])) {

                            $tempDir = $_SERVER['DOCUMENT_ROOT'] . '/images/temporary/';
                            $targetFileName = el_translit($_FILES[$field_number]['name']);
                            if (!is_dir($tempDir)) mkdir($tempDir, 0777);
                            if (move_uploaded_file($_FILES[$field_number]['tmp_name'], $tempDir . $targetFileName)) {

                                if (el_resize_images($tempDir . $targetFileName, $targetFileName,
                                    $row_cat_form1['big_size'], $row_cat_form1['big_size_h'], $temp_name)) {
                                    $_POST[$field_number] = "/images/" . $_SESSION['site_id'] . '/' . el_translit($temp_name . $_FILES[$field_number]['name']);
                                    el_imageLogo($_POST[$field_number], '/images/copyright.png', 'bottom-right');
                                    unlink($tempDir . $targetFileName);
                                } else {
                                    echo "<script>alert('Файл с названием \"" . $_FILES[$field_number]['name'] . "\" не удалось закачать!')</script>";
                                }
                            } else {
                                echo "<script>alert('Файл с названием \"" . $_FILES[$field_number]['name'] . "\" не удалось закачать!')</script>";
                            }
                        }
                        break;
                    case "file":
                        $type = "TEXT";
                        if (strlen($_FILES[$field_number]['name']) > 0) {
                            if (!is_dir($_SERVER['DOCUMENT_ROOT'] . "/files/" . $_SESSION['site_id'])) {
                                mkdir($_SERVER['DOCUMENT_ROOT'] . "/files/" . $_SESSION['site_id'], 0777);
                            }
                            if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/files/" . $_SESSION['site_id'] . '/' . $temp_name . '_' . $_FILES[$field_number]['name'])) {
                                echo "<script>alert('Файл с названием \"" . $_FILES[$field_number]['name'] . "\" уже есть!')</script>";
                                $fflag = 0;
                            } else {
                                if (!move_uploaded_file($_FILES[$field_number]['tmp_name'],
                                    $_SERVER['DOCUMENT_ROOT'] . "/files/" . $_SESSION['site_id'] . '/' . $temp_name . '_' . $_FILES[$field_number]['name'])) {
                                    echo "<script>alert('Не удалось закачать файл \"" . $_FILES[$field_number]['name'] . "\"!\\nВозможно, не настроен доступ к папке \"files\".')</script>";
                                    $fflag = 0;
                                } else {
                                    $_POST[$field_number] = "/files/" . $_SESSION['site_id'] . '/' . $temp_name . '_' . $_FILES[$field_number]['name'];
                                }
                            }
                        } else {
                            $_POST[$field_number] = $_POST[$field_number . "f"];
                        }
                        break;
                    case "password":
                        $_POST[$field_number] = str_replace("$1$", "", crypt(md5($_POST[$field_number]), '$1$'));
                        break;
                    case "secure_file":
                        $file_ext = strrchr($_FILES[$field_number]['name'], '.');
                        $newfilename = el_genpass(20) . $file_ext;
                        if (!is_dir($_SERVER['DOCUMENT_ROOT'] . "/files/secure")) {
                            mkdir($_SERVER['DOCUMENT_ROOT'] . "/files/secure", 0755);
                        }
                        if (strlen($_FILES[$field_number]['name']) > 0) {
                            if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/files/" . $_FILES[$field_number]['name'])) {
                                echo "<script>alert('Файл с названием \"" . $_FILES[$field_number]['name'] . "\" уже есть!')</script>";
                            } else {
                                if (!move_uploaded_file($_FILES[$field_number]['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . "/files/secure/" . $newfilename)) {
                                    echo "<script>alert('Не удалось закачать файл " . $_FILES[$field_number]['name'] . "!')</script>";
                                }
                            }
                        }
                        $type = "TEXT";
                        $_POST[$field_number] = $newfilename;
                        break;

                    case "integer":
                        $type = "INT";
                        break;
                    case "float":
                    case "price":
                        $type = "DOUBLE";
                        $_POST[$field_number] = str_replace(",", ".", $_POST[$field_number]);
                        $_POST[$field_number] = sprintf("%01.2f", $_POST[$field_number]);

                        break;
                    default:
                        $type = "TEXT";
                        break;
                }
                $field_number = "field" . $row_cat_form['field'];

                switch ($row_cat_form['type']) {
                    case 'option':
                    case 'optionlist':
                    case "list_fromdb":
                    case "depend_list":
                        $type = "TEXT";
                        $arop = (is_array($_POST[$field_number]) && count($_POST[$field_number]) > 0) ? @implode(';', $_POST[$field_number]) : $_POST[$field_number];
                        $post_field .= "'" . GetSQLValueString($arop, $type) . "', ";
                        $query_field .= $field_number . ", ";
                        break;
                    case "propTable":
                        $type = "TEXT";
                        $row_cat_form = el_dbselect("SELECT listdb FROM catalog_prop WHERE catalog_id='$catalog_id' AND field='" . $row_cat_form['field'] . "' 
		ORDER BY sort", 0, $row_cat_form, 'row', true);
                        $list_field = el_dbselect("select id from catalog_" . $row_cat_form['listdb'] . "_data ORDER BY sort ASC", 0, $list_field, 'result', true);
                        $row_list_field = @el_dbfetch($list_field);
                        $itemlist = array();
                        do {
                            $itemlist[$row_list_field['id']] = $_POST[$field_number . '_' . $row_list_field['id']];
                        } while ($row_list_field = @el_dbfetch($list_field));
                        $post_field .= "'" . GetSQLValueString(json_encode($itemlist), $type) . "', ";
                        $query_field .= $field_number . ", ";
                        break;
                    case 'excel_file':
                        if (strlen($_FILES[$field_number]['name']) > 0) {
                            $newFileName = el_uploadUnique($field_number, 'files');

                            $type = "TEXT";
                            $_POST[$field_number] = parseExcelToTable($cat_post, $newFileName);
                            unlink($_SERVER['DOCUMENT_ROOT'] . $newFileName);
                            $post_field .= "'" . GetSQLValueString($_POST[$field_number], $type) . "', ";
                        }
                        break;
                    default:
                        $post_field .= "'" . GetSQLValueString($_POST[$field_number], $type) . "', ";
                        $query_field .= $field_number . ", ";

                        break;
                }

            } while ($row_cat_form = el_dbfetch($cat_form));
        }


        $active_post = GetSQLValueString(isset($_POST['active']) ? "true" : "", "defined", "'1'", "'0'") . ", ";
        $sort_post = GetSQLValueString($_POST['sort'], "int") . ", ";
        $goodid_post = GetSQLValueString($_POST['goodid'], "int");

        ///Мета теги ///
        if ($row_cat_form1['meta'] > 0) {
            $post_meta = "" . GetSQLValueString($_POST['title'], 'text') . ", " . GetSQLValueString($_POST['description'], 'text') . ", " . GetSQLValueString($_POST['keywords'], 'text') . ", ";
            $query_meta = "title, description, keywords, ";
        }
        ////////
        $path = (strlen(trim($_POST['path'])) == 0) ?
            el_translit(mb_strtolower(($catalog_id == 'realauto') ? $_POST['field' . $title_catalog['field']] . '-' . $_POST['field3'] : $_POST['field' . $title_catalog['field']]), 'path') . ((strlen(trim($_POST['goodid'])) > 0) ? '-' . $_POST['goodid'] : '')
            : el_translit(mb_strtolower($_POST['path']), 'path');


        $insertSQL = "INSERT INTO catalog_" . $catalog_id . "_data (site_id, cat, " . $query_field . $query_meta . "active, sort, goodid, path) 
			VALUES ('" . $_SESSION['site_id'] . "', " . $cat_post . $post_field . $post_meta . $active_post . $sort_post . $goodid_post . ", '$path')";


        $Result1 = el_dbselect($insertSQL, 0, $Result1, 'result', true);

        el_log('Добавлена запись &laquo;' . $_POST['field1'] . '&raquo; в каталог в разделе &laquo;' . $page_name . '&raquo;', 2);
        el_clearcache('catalogs');
        el_clearcache('tags');
        el_genSiteMap();
    }
    if (isset($_GET['frm_filter']) && $_GET['frm_filter'] == 'search') {

        $query_pfilter = "SELECT * FROM catalog_prop WHERE catalog_id='" . $catalog_id . "' AND search=1 ORDER BY sort";
        $pfilter = el_dbselect($query_pfilter, 0, $pfilter);
        $row_pfilter = el_dbfetch($pfilter);
        $qw = "";
        do {
            if (strlen($_GET['field' . $row_pfilter['field']]) > 0) {
                if (is_array($_GET['field' . $row_pfilter['field']])/* && count($_GET['field'.$row_pfilter['field']])>0*/) {
                    for ($ip = 0; $ip < count($_GET['field' . $row_pfilter['field']]); $ip++) {
                        $qw .= " AND field" . $row_pfilter['field'] . " LIKE '%" . str_replace("``", "\"", $_GET['field' . $row_pfilter['field']][$ip]) . "%' ";
                    }
                } else {
                    $qw .= " AND field" . $row_pfilter['field'] . " LIKE '%" . str_replace("``", "\"", $_GET['field' . $row_pfilter['field']]) . "%' ";
                }
            }
        } while ($row_pfilter = el_dbfetch($pfilter));
        if (strlen($_GET['id']) > 0) {
            $qw .= " AND id='" . $_GET['id'] . "'";
        }
        if (strlen($_GET['sort']) > 0) {
            $qw .= " AND sort='" . $_GET['sort'] . "'";
        }
    }

    if (!$mode){
        if (intval($site_property['showRow' . $cat]) > 0) {
            $maxRows_catalog = $site_property['showRow' . $cat];
        }
        if ($site_property['showAll' . $cat] == '1') {
            $maxRows_catalog = 0;
        }
        if (intval($site_property['showRow' . $cat]) == 0 && intval($site_property['showAll' . $cat]) == 0) {
            $maxRows_catalog = 25;
        }
        $pageNum_catalog = 0;
        if (isset($_GET['pageNum_catalog'])) {
            $pageNum_catalog = $_GET['pageNum_catalog'];
        }
        $startRow_catalog = $pageNum_catalog * $maxRows_catalog;

        $colname_catalog = "1";
        if (isset($_GET['cat'])) {
            $colname_catalog = (get_magic_quotes_gpc()) ? $_GET['cat'] : addslashes($_GET['cat']);
        }

        $sort = "";
        if (isset($_GET['sortida'])) {
            $sort = " id ASC";
        } elseif (isset($_GET['sortidd'])) {
            $sort = " id DESC";
        } elseif (isset($_GET['sortnamea'])) {
            $sort = " field1 ASC";
        } elseif (isset($_GET['sortnamed'])) {
            $sort = " field1 DESC";
        } elseif (isset($_GET['sortnumbera'])) {
            $sort = " sort ASC";
        } elseif (isset($_GET['sortnumberd'])) {
            $sort = " sort DESC";
        } else {
            $sort = " id DESC, sort ASC";
        }


        $query_catalog = "SELECT * FROM catalog_" . $catalog_id . "_data 
	WHERE (cat = $cat  OR cat LIKE '% $cat %') AND site_id=" . intval($_SESSION['site_id']) . $qw . " ORDER BY$sort";//
        $query_limit_catalog = sprintf("%s LIMIT %d, %d", $query_catalog, $startRow_catalog, $maxRows_catalog);
        $result_query_catalog = ($maxRows_catalog == 0) ? $query_catalog : $query_limit_catalog;
        $catalog = el_dbselect($result_query_catalog, 0, $catalog, 'result', true);
        $row_cat_formalog = el_dbfetch($catalog);


        if (isset($_GET['totalRows_catalog'])) {
            $totalRows_catalog = $_GET['totalRows_catalog'];
        } else {
            $all_catalog = mysqli_query($dbconn, $query_catalog);
            $totalRows_catalog = mysqli_num_rows($all_catalog);
        }
        $totalPages_catalog = ($maxRows_catalog > 0) ? ceil($totalRows_catalog / $maxRows_catalog) - 1 : $totalRows_catalog;
        ?>


        <p><br><a name="start_list"></a>
            <? if ($totalRows_catalog > 0){ ?>
            Показано c <?php echo($startRow_catalog + 1) ?>
            по <?= ($maxRows_catalog > 0) ? min($startRow_catalog + $maxRows_catalog, $totalRows_catalog) : $totalRows_catalog ?> из <?php echo $totalRows_catalog ?>
            записей
            <? if ($maxRows_catalog > 0) el_paging($pageNum_catalog, $currentPage, $queryString_catalog, $totalPages_catalog, $maxRows_catalog, $totalRows_catalog, 'cat=' . $cat . '&pageNum_catalog', 'tr') ?>
        </p>
        <div id="catalog_sort">
            <a href="?cat=<?= $cat ?>&sortid<?= (isset($_GET['sortida'])) ? 'd" class="sort_down' : 'a' ?>">сортировать по
                ID</a>
            <a href="?cat=<?= $cat ?>&sortname<?= (isset($_GET['sortnamea'])) ? 'd" class="sort_down' : 'a' ?>">сортировать
                по названию</a>
            <a href="?cat=<?= $cat ?>&sortnumber<?= (isset($_GET['sortnumbera'])) ? 'd" class="sort_down' : 'a' ?>">сортировать
                по номеру</a>
        </div>
        <table width="98%" border="0" align="center" cellpadding="3" cellspacing="0" style="border-bottom:1px solid gray;">
            <tr>
                <td align="left">
                    <form method="post" name="setRowsFrm">
                        Показывать по:
                        <input type="text" name="showRow<?= $cat ?>" id="showRow" size="4"
                               value="<?= (intval($site_property['showRow' . $cat]) > 0) ? $site_property['showRow' . $cat] : $maxRows_catalog ?>">
                        строк
                        &nbsp;или показать все <input type="checkbox" name="showAll<?= $cat ?>"
                                                      value="1"
                            <?= ($site_property['showAll' . $cat] == '1') ? ' checked="checked"' : '' ?>>
                        <input type="submit" name="setRows" value="Сохранить" class="but"/>
                    </form>
                </td>
            </tr>
        </table>
        <?php do {
            $row_field1 = str_replace('"', '``', $row_cat_formalog['field1']);
            $row_field1n = str_replace('\'', '`', $row_field1);
            ?>
            <form method="POST" action="<?php echo $editFormAction; ?>" name="catedit">
                <table width="98%" border="0" align="center" cellpadding="3" cellspacing="0"
                       style="border-bottom:1px solid gray;">
                    <tr id="string<?php echo $row_cat_formalog['id']; ?>"
                        onMouseOver='document.getElementById("string<?php echo $row_cat_formalog['id']; ?>").style.backgroundColor="#E7E7E7"'
                        onMouseOut='document.getElementById("string<?php echo $row_cat_formalog['id']; ?>").style.backgroundColor=""'>
                        <td width="10%">#
                            <?php echo $row_cat_formalog['id']; ?> <input name="id" type="hidden"
                                                                          value="<?php echo $row_cat_formalog['id']; ?>"></td>
                        <td width="40%" id="td<?= $row_cat_formalog['id'] ?>"
                            onClick="MM_openBrWindow('/editor/modules/catalog/detail.php?id=<?= $row_cat_formalog['id'] ?>&catalog_id=<?= $catalog_id ?>&path=<?= $row_content['path'] ?>','detail','scrollbars=yes,resizable=yes','950','600','true')"
                            style="cursor:pointer"
                            onMouseOver="document.getElementById('td<?= $row_cat_formalog['id'] ?>').style.color='blue'"
                            onMouseOut="document.getElementById('td<?= $row_cat_formalog['id'] ?>').style.color=''">

                            <?
                            $titleFi = el_dbselect("SELECT field FROM catalog_prop WHERE catalog_id='" . $catalog_id . "' AND title=1", 0, $titleField);
                            $titleField = el_dbfetch($titleFi);
                            do {
                                //$titleField='field'.$titleFi['field'];
                                if (strlen($row_cat_formalog['field' . $titleField['field'] . '']) > 0) {
                                    $titlename = $row_cat_formalog['field' . $titleField['field'] . ''];
                                }
                                ?>
                                <strong
                                    <?= ($row_cat_formalog['active'] == 0) ? ' style="color:#999"' : '' ?>><?= $titlename ?></strong>
                                <?
                            } while ($titleField = el_dbfetch($titleFi));
                            ?>


                        </td>
                        <td width="50%" align="right">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <? if (count($price_field) > 0) {
                                        for ($i = 0; $i < count($price_field); $i++) {
                                            ?>
                                            <td><?= $price_field['name'][$i] ?> <input name="<?= $price_field['field'][$i] ?>"
                                                                                       type="text"
                                                                                       id="<?= $price_field['field'][$i] ?>"
                                                                                       value="<?= $row_cat_formalog[$price_field['field'][$i]] ?>"
                                                                                       size="5"></td>
                                        <? }
                                    } ?>

                                    <td>Номер:
                                        <input name="sort" type="text"
                                               value="<?php echo $row_cat_formalog['sort']; ?>" size="3"></td>
                                    <td><label>
                                            <input <?php if (!(strcmp($row_cat_formalog['active'], "1"))) {
                                                echo "checked";
                                            } ?> name="active" type="checkbox" value="<?= $row_cat_formalog['id'] ?>" class="chActive">
                                            Активный</label></td>
                                    <td width="30">
                                        <input type="checkbox" name="delGood<?= $row_cat_formalog['id'] ?>" class="checkGood"
                                               value="<?= $row_cat_formalog['id'] ?>"></td>

                                    <td width="30"><i class="material-icons"
                                                      title="Клонировать запись"
                                                      onClick="cloneRow(<?= $row_cat_formalog['id'] ?>, '<?= $catalog_id ?>'); return false;">file_copy</i>
                                    </td>
                                    <td width="30" id="td<?= $row_cat_formalog['id'] ?>"><i class="material-icons"
                                                                                            onClick="MM_openBrWindow('/editor/modules/catalog/detail.php?id=<?= $row_cat_formalog['id'] ?>&catalog_id=<?= $catalog_id ?>&path=<?= $row_content['path'] ?>','detail','scrollbars=yes,resizable=yes','950','600','true')"
                                                                                            style="cursor:pointer"
                                                                                            onMouseOver="document.getElementById('td<?= $row_cat_formalog['id'] ?>').style.color='blue'"
                                                                                            onMouseOut="document.getElementById('td<?= $row_cat_formalog['id'] ?>').style.color=''">
                                            edit</i></td>
                                    <td width="30"><i class="material-icons" title="Сохранить изменения" onclick="$(this).parents('form').trigger('submit')">save</i>
                                    </td>
                                    <td width="30"><i title="Удалить" class="material-icons"
                                                      onClick="if(check('<?= $row_field1n ?>')){var $form = $(this).parents('form'); $form.find('input[name=action]').val('delcat'); $form.trigger('submit');}">delete_forever</i>
                                        <input name="action" type="hidden"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <input type="hidden" name="MM_update_cat" value="edit">
                    <input type="hidden" name="MM_update" value="catedit">
                </table>
            </form>
        <?php } while ($row_cat_formalog = el_dbfetch($catalog)); ?>
        <table width="98%" border="0" align="center" cellpadding="3" cellspacing="0">
            <tr>
                <td align="right" style="text-align:right">
                    <form method="post" name="groupActionFrm">
                        <label for="allCheckGood"><input type="checkbox" name="allCheckGood" id="allCheckGood" value="1">
                            Выделить все</label>
                        <input type="hidden" name="selectedId" id="selectedId">
                        <select name="groupAction" onChange="goGroupAction(this)">
                            <option value="">С отмеченными:</option>
                            <option value="delete">Удалить</option>
                            <option value="activate">Активировать</option>
                            <option value="deactivate">Деактивировать</option>
                        </select>
                    </form>
                </td>
            </tr>
        </table>
        <p><br>
            <br>
        </p><? } else {
        echo "<center><h4>Нет ни одной записи</h4></center>";
    }

        $query_sort = "SELECT MAX(sort), MAX(id) FROM catalog_" . $catalog_id . "_data";// WHERE cat=".$_GET['cat'];
        $sort = el_dbselect($query_sort, 0, $sort);
        $row_sort = el_dbfetch($sort);
        ?>
        <table align="center">
            <tr>
                <td><label for="addMode_0">
                        <input type="radio" name="addMode" value="manual" id="addMode_0"
                               onClick="selectMode('manual<?= $cat ?>')"
                            <?= ($_COOKIE['idshow']['manual' . $cat] == 'Y') ? 'checked' : '' ?>/>
                        Добавление вручную</label>&nbsp;&nbsp;&nbsp;
                </td>
                <td><label for="addMode_1">
                        <input type="radio" name="addMode" value="csv" id="addMode_1"
                               onClick="selectMode('csv<?= $cat ?>')"
                            <?= ($_COOKIE['idshow']['csv' . $cat] == 'Y') ? 'checked' : '' ?>/>
                        Импорт из CSV-файла</label></td>
                <td><label for="addMode_2">

                        <input type="radio" name="addMode" value="csv" id="addMode_2"
                               onClick="selectMode('excel<?= $cat ?>')"
                            <?= ($_COOKIE['idshow']['excel' . $cat] == 'Y') ? 'checked' : '' ?>/>
                        Импорт из Excel-файла</label></td>
            </tr>
        </table>
        <div id="addForm">
            <div id="manual<?= $cat ?>"
                 style="display:<?= ($_COOKIE['idshow']['manual' . $cat] == 'Y') ? 'block' : 'none' ?>">

                <form method="POST" action="<?php echo $editFormAction; ?>" name="newitem" ENCTYPE="multipart/form-data">
                    <table width="98%" align="center" cellpadding="3" cellspacing="0" class="el_tbl">
                        <tr>
                            <td colspan="2" align="center"><b>Новая запись </b></td>
                        </tr>

                        <tr>
                            <td align="right" valign="top">Порядковый номер:</td>
                            <td><input name="sort" type="text" id="sort" value="<?= $row_sort['MAX(sort)'] + 1 ?>" size="3">
                            </td>
                        </tr>
                        <?/*tr>
                        <td align="right" valign="top">Артикул:</td>
                        <td><input name="goodid" type="text" id="goodid" value="<?= $row_sort['MAX(id)'] + 1 ?>" size="3"></td>
                    </tr*/
                        ?>
                        <input name="goodid" type="hidden" id="goodid" value="<?= $row_sort['MAX(id)'] + 1 ?>">
                        <tr>
                            <td align="right" valign="top">Путь (ЧПУ):</td>
                            <td><input name="path" type="text" id="path" value="" size="40"></td>
                        </tr>

                        <?
                        $curr = el_dbselect("SELECT cat, title FROM content 
                    WHERE kod='catalog" . $catalog_id . "' AND site_id=" . intval($_SESSION['site_id']), 0, $currCat);
                        $currCat = el_dbfetch($curr);
                        if (el_dbnumrows($curr) > 1){
                        echo '<tr>
                        <td align="right" valign="top" nowrap>Разместить в разделе:</td>
                        <td>';

                        ?>
                        <div id="cat" style="height:auto; overflow:auto">
                            <? do {
                                $sel = ($currCat['cat'] == $_GET['cat']) ? ' checked' : '';
                                ?>
                                <label for="cat<?= $currCat['cat'] ?>">
                                    <input type="checkbox" name="cats[]" id="cat<?= $currCat['cat'] ?>"
                                           value="<?= $currCat['cat'] ?>" <?= $sel ?>> <?= $currCat['title'] ?>
                                </label><br>
                                <?
                            } while ($currCat = el_dbfetch($curr));
                            echo '</div>
                        </td>
                    </tr>';
                            } else {
                                echo '<input type="hidden" name="cats[]" value="' . $currCat['cat'] . '">';
                            }

                            $query_cat_form = "SELECT * FROM catalog_prop WHERE catalog_id='$catalog_id' ORDER BY sort";
                            $cat_form = el_dbselect($query_cat_form, 0, $cat_form);
                            $row_cat_form = el_dbfetch($cat_form);
                            $totalRows_cat_form = mysqli_num_rows($cat_form);

                            if ($totalRows_cat_form > 0) {


                                $query_cat_form1 = "SELECT * FROM catalogs WHERE catalog_id='" . $catalog_id . "'";
                                $cat_form1 = el_dbselect($query_cat_form1, 0, $cat_form1);
                                $row_cat_form1 = el_dbfetch($cat_form1);

                                $allow_multiUpload = array();

                                do {
                                    $ouput = $input = $script = $prop = '';
                                    switch ($row_cat_form['type']) {
                                        case "textarea":
                                            $input = "textarea";
                                            $prop = "cols=" . $row_cat_form['cols'] . " rows=" . $row_cat_form['rows'] . "";
                                            $output = "</textarea>";//<br>
                                            //<input name='Button' type='button' onClick=\"MM_openBrWindow('/editor/newseditor.php?field=field" . $row_cat_form['field'] . "&form=newitem','editor','resizable=yes','750','640','true','description','new')\" value='Визуальный редактор' class='but'>";
                                            break;
                                        case "select":
                                            $input = "textarea";
                                            $prop = "cols=30 rows=5";
                                            $output = "</textarea><br>Здесь вписываются строки списка через точку с запятой ';'";
                                            break;
                                        case "option":
                                            $itemsArr = explode(';', $row_cat_form['options']);
                                            $items = array();
                                            for ($i = 0; $i < count($itemsArr); $i++) {
                                                $items[] = '<option value="' . $itemsArr[$i] . '">' . $itemsArr[$i] . '</option>';
                                            }
                                            $output = "<select name='field" . $row_cat_form['field'] . "'  id='field" . $row_cat_form['field'] . "'" . ((strlen($row_cat_form['size']) > 0) ? " size=" . $row_cat_form['size'] : '') . ">\n<option></option>\n" . implode("\n", $items) . "</select>";
                                            break;
                                        case "optionlist":
                                            $itemsArr = $items = array();
                                            $itemsArr = explode(';', $row_cat_form['options']);

                                            for ($i = 0; $i < count($itemsArr); $i++) {
                                                //$items[]='<option value="'.$itemsArr[$i].'">'.$itemsArr[$i].'</option>';
                                                $items[] = '<label for="opt' . $i . '">
		  <input type="checkbox" name="field' . $row_cat_form['field'] . '[]" id="opt' . $i . '" value="' . $itemsArr[$i] . '" checked="checked"> ' . $itemsArr[$i] . '</label><br>';
                                            }
                                            $output = "<div style='height:" . ((strlen($row_cat_form['size']) > 0) ? 17 * $row_cat_form['size'] . "px; overflow:auto'" : '100px') . ">\n" . implode("\n", $items) . "</div>";
                                            break;
                                        case "list_fromdb":
                                            $list_field = el_dbselect("SELECT id, field" . $row_cat_form['from_field'] . " FROM catalog_" . $row_cat_form['listdb'] .
                                                "_data WHERE site_id=" . intval($_SESSION['site_id']) . " ORDER BY sort ASC", 0, $list_field);
                                            $row_list_field = @el_dbfetch($list_field);
                                            $itemlist = '';
                                            do {
                                                $itemlist .= "<option value='" . $row_list_field["id"] . "'>" . $row_list_field["field" . $row_cat_form['from_field']] . "</option>\n";
                                            } while ($row_list_field = @el_dbfetch($list_field));
                                            $output = "<select name='field" . $row_cat_form['field'] . "[]' id='field" . $row_cat_form['field'] . "'" . ((strlen($row_cat_form['size']) > 0) ? " size=" . $row_cat_form['size'] . " multiple" : '') . ">\n<option></option>\n" . $itemlist . "</select>";
                                            break;

                                        case "propTable":
                                            $list_field = el_dbselect("SELECT id, field" . $row_cat_form['from_field'] . " FROM catalog_" . $row_cat_form['listdb'] .
                                                "_data WHERE site_id=" . intval($_SESSION['site_id']) . " ORDER BY sort ASC", 0, $list_field);
                                            $row_list_field = @el_dbfetch($list_field);
                                            $itemlist = '';
                                            do {
                                                $itemlist .= "<tr><td>" . $row_list_field["field" . $row_cat_form['from_field']] . "</td><td><input type='text' name='field" . $row_cat_form['field'] . "_" . $row_list_field['id'] . "'></td></tr>\n";
                                            } while ($row_list_field = @el_dbfetch($list_field));
                                            $output = "<table cellpadding='4'>\n" . $itemlist . "</table>";
                                            break;

                                        case "depend_list":
                                            $list_field = el_dbselect("SELECT id, field" . $row_cat_form['from_field'] . " FROM catalog_" . $row_cat_form['listdb'] .
                                                "_data WHERE site_id=" . intval($_SESSION['site_id']) . " ORDER BY field" . $row_cat_form['from_field'] . " ASC", 0, $list_field);
                                            $row_list_field = el_dbfetch($list_field);
                                            $itemlist = '';
                                            do {
                                                $itemlist .= "<option value='" . $row_list_field['id'] . "'>" . $row_list_field["field" . $row_cat_form['from_field']] . "</option>\n";
                                            } while ($row_list_field = el_dbfetch($list_field));
                                            $output = "<select name='field" . $row_cat_form['field'] . "' id='field" . $row_cat_form['field'] . "'" . ((strlen($row_cat_form['size']) > 0) ? " size=" . $row_cat_form['size'] . " multiple" : '') . " onchange='getDependList(this, \"" . $row_cat_form['listdb'] . "\", \"" . $row_cat_form['options'] . "\", \"" . $row_cat_form['default_value'] . "\", \"" . $row_cat_form['from_field'] . "\", \"" . $row_cat_form['to_field'] . "\")'>\n<option></option>\n" . $itemlist . "</select>";
                                            break;

                                        case "marks":
                                            $list_field = el_dbselect("select field1 from catalog_marks_data ORDER BY field1 ASC", 0, $list_field);
                                            $row_list_field = el_dbfetch($list_field);
                                            $itemlist = '';
                                            do {
                                                $itemlist .= "<option value='" . $row_list_field["field1"] . "'>" . $row_list_field["field1"] . "</option>\n";
                                            } while ($row_list_field = el_dbfetch($list_field));
                                            $output = "<select name='field" . $row_cat_form['field'] . "' id='field" . $row_cat_form['field'] . "'" . ((strlen($row_cat_form['size']) > 0) ? " size=" . $row_cat_form['size'] . " multiple" : '') . " onchange='getModels(this)'>\n<option></option>\n" . $itemlist . "</select>";
                                            break;

                                        case "models":
                                            $list_field = el_dbselect("select field1 from catalog_marks_data ORDER BY field1 ASC", 0, $list_field);
                                            $row_list_field = el_dbfetch($list_field);
                                            $itemlist = '';
                                            do {
                                                $itemlist .= "<option value='" . $row_list_field["field1"] . "'>" . $row_list_field["field1"] . "</option>\n";
                                            } while ($row_list_field = el_dbfetch($list_field));
                                            $output = "<select name='field" . $row_cat_form['field'] . "' id='field" . $row_cat_form['field'] . "'" . ((strlen($row_cat_form['size']) > 0) ? " size=" . $row_cat_form['size'] . " multiple" : '') . " onchange='getModif(this)'>\n<option></option>\n" . $itemlist . "</select>";
                                            break;

                                        case "modif":
                                            $output = "<select name='field" . $row_cat_form['field'] . "' id='field" . $row_cat_form['field'] . "'" . ((strlen($row_cat_form['size']) > 0) ? " size=" . $row_cat_form['size'] . " multiple" : '') . ">\n<option></option>\n</select>";
                                            break;

                                        case "photo_album":
                                            $list_field = el_dbselect("select id, name from photo_albums WHERE type<>'video' ORDER BY date_create DESC, id DESC", 0, $list_field);
                                            $row_list_field = el_dbfetch($list_field);
                                            $itemlist = '';
                                            do {
                                                $itemlist .= "<option value='" . $row_list_field['id'] . "'>" . $row_list_field['name'] . "</option>\n";
                                            } while ($row_list_field = el_dbfetch($list_field));
                                            $output = "<select name='field" . $row_cat_form['field'] . "[]' id='field" . $row_cat_form['field'] . "' size=" . $row_cat_form['size'] . " multiple>\n<option></option>\n" . $itemlist . "</select>";
                                            break;
                                        case "video_album":
                                            $list_field = el_dbselect("select id, name from photo_albums WHERE type='video' ORDER BY date_create DESC, id DESC", 0, $list_field);
                                            $row_list_field = el_dbfetch($list_field);
                                            $itemlist = '';
                                            do {
                                                $itemlist .= "<option value='" . $row_list_field['id'] . "'>" . $row_list_field['name'] . "</option>\n";
                                            } while ($row_list_field = el_dbfetch($list_field));
                                            $output = "<select name='field" . $row_cat_form['field'] . "[]' id='field" . $row_cat_form['field'] . "' size=" . $row_cat_form['size'] . " multiple>\n<option></option>\n" . $itemlist . "</select>";
                                            break;

                                        case "comments":
                                            $input = "input";
                                            $prop = " value='1' title='Включить комментирование'";
                                            $output = "";
                                            $row_cat_form['type'] = "checkbox";
                                            break;
                                        case "checkbox":
                                            $input = "input";
                                            $prop = " value='" . $row_cat_form['name'] . "'";
                                            $output = "";
                                            $row_cat_form['type'] = "checkbox";
                                            break;
                                        case "radio":
                                            $input = "input";
                                            $prop = "";
                                            $output = "";
                                            break;
                                        case "small_image":
                                            $input = "input";
                                            $prop = "";
                                            $output = "<br>Укажите местонахождение картинки для предпросмотра на Вашем компьютере для закачки на сервер";
                                            $row_cat_form['type'] = "file";
                                            break;
                                        case "big_image":
                                            $input = "input";
                                            $prop = "";
                                            $output = "<br>Укажите местонахождение картинки на Вашем компьютере для закачки на сервер";
                                            $row_cat_form['type'] = "file";
                                            break;

                                        case "multi_image":
                                            $input = '';
                                            $prop = "";
                                            $output = "<div id='field" . $row_cat_form['field'] . "Upload'></div>
									<input type='hidden' name='field" . $row_cat_form['field'] . "' 
									id='field" . $row_cat_form['field'] . "' value=''>";
                                            $row_cat_form['type'] = "multi_image";
                                            $allow_multiUpload[] = 'field' . $row_cat_form['field'] . 'Upload';
                                            break;

                                        case "file":
                                            $input = "input";
                                            $prop = "";
                                            $output = "<br>Здесь указывается местонахождение файла на Вашем компьютере для закачки на сервер";
                                            break;
                                        case "secure_file":
                                            $input = "input";
                                            $prop = "";
                                            $output = "<br>Здесь указывается местонахождение файла на Вашем компьютере для закачки на сервер.<br>Система даст новое нечитаемое название файлу и поместит в недоступное для посетителей сайта место.";
                                            $row_cat_form['type'] = "file";
                                            break;
                                        case "hidden_file":
                                            $input = "input";
                                            $prop = "";
                                            $output = "<br>Здесь указывается местонахождение файла на Вашем компьютере для закачки на сервер.<br>Система поместит файл в недоступное для посетителей сайта место.";
                                            $row_cat_form['type'] = "file";
                                            break;
                                        case "price":
                                            $input = "input";
                                            $prop = "";
                                            $output = " " . $row_cat_form1['currency'];
                                            $row_cat_form['type'] = 'text';
                                            break;
                                        case "calendar":
                                            $input = "input";
                                            $row_cat_form['type'] = "text";
                                            $prop = ($row_cat_form['name'] == 'Дата окончания публикации') ? " value=''" : " value='" . date('Y-m-d H:s') . "'";
                                            $output = " <script type=\"text/javascript\">$(function() {
									    $(\"#field" . $row_cat_form['field'] . "\").flatpickr({
									        enableTime: false, 
									        allowInput: true, 
									        locale: 'ru', 
									        time_24hr: true,
									        'plugins': [new confirmDatePlugin({})]
									        });
										});
										</script>";
                                            break;
                                        case "datetime":
                                            $input = "input";
                                            $row_cat_form['type'] = "text";
                                            $prop = " value='" . date('Y-m-d H:s') . "'";
                                            $output = " <script type=\"text/javascript\">$(function() {
									    $(\"#field" . $row_cat_form['field'] . "\").flatpickr({
									        enableTime: true, 
									        allowInput: true, 
									        locale: 'ru', 
									        time_24hr: true,
									        'plugins': [new confirmDatePlugin({})]
									        });
										});
										</script>";
                                            break;
                                        case "multi_date":
                                            $input = "input";
                                            $row_cat_form['type'] = "text";
                                            $prop = " value='" . date('Y-m-d H:s') . "'";
                                            $output = " <script type=\"text/javascript\">$(function() {
									    $(\"#field" . $row_cat_form['field'] . "\").flatpickr({
									        enableTime: false, 
									        allowInput: true,
									        mode: 'multiple',
									        locale: 'ru', 
									        time_24hr: true,
									        'plugins': [new confirmDatePlugin({})]
									        });
										});
										</script>";
                                            break;
                                        case "range_date":
                                            $input = "input";
                                            $row_cat_form['type'] = "text";
                                            $prop = " value='" . date('Y-m-d H:s') . "'";
                                            $output = " <script type=\"text/javascript\">$(function() {
									    $(\"#field" . $row_cat_form['field'] . "\").flatpickr({
									        enableTime: false, 
									        allowInput: true,
									        mode: 'range',
									        locale: 'ru', 
									        time_24hr: true,
									        'plugins': [new confirmDatePlugin({})]
									        });
										});
										</script>";
                                            break;
                                        case 'colorpicker':
                                            $input = "input";
                                            $row_cat_form['type'] = "text";
                                            $output = " <script type=\"text/javascript\">$(function() {
										 $(\"#field" . $row_cat_form['field'] . "\").spectrum({
                                            color: \"#ECC\",
                                            showInput: true,
                                            className: \"full-spectrum\",
                                            showInitial: true,
                                            showPalette: true,
                                            showSelectionPalette: true,
                                            maxSelectionSize: 10,
                                            preferredFormat: \"hex\",
                                            localStorageKey: \"spectrum.demo\",
                                            palette: [
                                                [\"rgb(0, 0, 0)\", \"rgb(67, 67, 67)\", \"rgb(102, 102, 102)\",
                                                \"rgb(204, 204, 204)\", \"rgb(217, 217, 217)\",\"rgb(255, 255, 255)\"],
                                                [\"rgb(152, 0, 0)\", \"rgb(255, 0, 0)\", \"rgb(255, 153, 0)\", \"rgb(255, 255, 0)\", \"rgb(0, 255, 0)\",
                                                \"rgb(0, 255, 255)\", \"rgb(74, 134, 232)\", \"rgb(0, 0, 255)\", \"rgb(153, 0, 255)\", \"rgb(255, 0, 255)\"], 
                                                [\"rgb(230, 184, 175)\", \"rgb(244, 204, 204)\", \"rgb(252, 229, 205)\", \"rgb(255, 242, 204)\", \"rgb(217, 234, 211)\", 
                                                \"rgb(208, 224, 227)\", \"rgb(201, 218, 248)\", \"rgb(207, 226, 243)\", \"rgb(217, 210, 233)\", \"rgb(234, 209, 220)\", 
                                                \"rgb(221, 126, 107)\", \"rgb(234, 153, 153)\", \"rgb(249, 203, 156)\", \"rgb(255, 229, 153)\", \"rgb(182, 215, 168)\", 
                                                \"rgb(162, 196, 201)\", \"rgb(164, 194, 244)\", \"rgb(159, 197, 232)\", \"rgb(180, 167, 214)\", \"rgb(213, 166, 189)\", 
                                                \"rgb(204, 65, 37)\", \"rgb(224, 102, 102)\", \"rgb(246, 178, 107)\", \"rgb(255, 217, 102)\", \"rgb(147, 196, 125)\", 
                                                \"rgb(118, 165, 175)\", \"rgb(109, 158, 235)\", \"rgb(111, 168, 220)\", \"rgb(142, 124, 195)\", \"rgb(194, 123, 160)\",
                                                \"rgb(166, 28, 0)\", \"rgb(204, 0, 0)\", \"rgb(230, 145, 56)\", \"rgb(241, 194, 50)\", \"rgb(106, 168, 79)\",
                                                \"rgb(69, 129, 142)\", \"rgb(60, 120, 216)\", \"rgb(61, 133, 198)\", \"rgb(103, 78, 167)\", \"rgb(166, 77, 121)\",
                                                \"rgb(91, 15, 0)\", \"rgb(102, 0, 0)\", \"rgb(120, 63, 4)\", \"rgb(127, 96, 0)\", \"rgb(39, 78, 19)\", 
                                                \"rgb(12, 52, 61)\", \"rgb(28, 69, 135)\", \"rgb(7, 55, 99)\", \"rgb(32, 18, 77)\", \"rgb(76, 17, 48)\"]
                                            ]
                                        })});
										</script>";
                                            break;

                                        case "calendarext":
                                            $output = '<iframe src="/editor/modules/catalog/calendar.php?field=field' . $row_cat_form['field'] . 'Add&frame=ext_calendar" frameborder="0" style="visibility:hidden" width="285" height="210" id="ext_calendar"></iframe>';
                                            $input = "input";
                                            $prop = "id='field" . $row_cat_form['field'] . "Add' value=''";
                                            $row_cat_form['type'] = "hidden";
                                            break;
                                        case 'full_html':
                                            $output = "<textarea cols=" . $row_cat_form['cols'] . " rows=" . $row_cat_form['rows'] . " name='field" . $row_cat_form['field'] . "'></textarea><script src='/editor/visual_editor.php?class=field" . $row_cat_form['field'] . "&height=" . ($row_cat_form['rows'] * 30) . "&type=full'></script>";
                                            break;
                                        case 'basic_html':
                                            $output = "<textarea cols=" . $row_cat_form['cols'] . " rows=" . $row_cat_form['rows'] . " name='field" . $row_cat_form['field'] . "'></textarea><script src='/editor/visual_editor.php?class=field" . $row_cat_form['field'] . "&height=" . ($row_cat_form['rows'] * 30) . "&type=basic'></script>";
                                            break;
                                        case "text":
                                        case "float":
                                        case "integer":
                                        default:
                                            $input = "input";
                                            $prop = "";
                                            $output = "";
                                            $row_cat_form['type'] = 'text';
                                            break;

                                    }


                                    if ($row_cat_form['type'] == 'option' || $row_cat_form['type'] == 'optionlist' || $row_cat_form['type'] == 'list_fromdb' || $row_cat_form['type'] == 'propTable' || $row_cat_form['type'] == 'file_list' || $row_cat_form['type'] == 'full_html' || $row_cat_form['type'] == 'basic_html' || $row_cat_form['type'] == 'photo_album' || $row_cat_form['type'] == 'video_album' || $row_cat_form['type'] == 'multi_image' || $row_cat_form['type'] == 'depend_list' || $row_cat_form['type'] == 'marks' || $row_cat_form['type'] == 'models' || $row_cat_form['type'] == 'modif') {
                                        echo "<tr><td align='right' valign='top'>" . $row_cat_form['name'] . ": </td><td>$output</td>";
                                    } else {
                                        echo "<tr><td align='right' valign='top'>" . $row_cat_form['name'] . ": </td><td>$script<$input type='" . $row_cat_form['type'] . "' name='field" . $row_cat_form['field'] . "' id='field" . $row_cat_form['field'] . "' size='" . $row_cat_form['size'] . "' $prop>$output</td>";
                                    }
                                } while ($row_cat_form = el_dbfetch($cat_form));
                            }
                            ?>

                            <? if ($row_cat_form1['meta'] > 0) { ?>
                                <tr>
                                    <td align="right" valign="top">Title:</td>
                                    <td><input name="title" type="text" id="title" value="" size="80"></td>
                                </tr>
                                <tr>
                                    <td align="right" valign="top">Description:</td>
                                    <td><textarea type="textarea" name="description" cols=60 rows=3></textarea></td>
                                </tr>
                                <tr>
                                    <td align="right" valign="top">Keywords:</td>
                                    <td><textarea type="textarea" name="keywords" cols=60 rows=3></textarea></td>
                                </tr>
                            <? }
                            ?>


                            <tr>
                                <td align="right" valign="top">Активный:</td>
                                <td><input name="active" type="checkbox" id="active" value="checkbox" checked></td>
                            </tr>
                            <tr>
                                <td colspan="2" align="center" valign="top"><input type="submit" name="Submit" value="Добавить"
                                                                                   class="but"></td>
                            </tr>
                            <input type="hidden" name="MM_insert" value="newitem">
                    </table>
                </form>
            </div>
            <div id="csv<?= $cat ?>" style="display:<?= ($_COOKIE['idshow']['csv' . $cat] == 'Y') ? 'block' : 'none' ?>">
                <?
                $st = el_dbselect("SELECT name FROM catalog_prop WHERE catalog_id='$catalog_id' ORDER BY sort ASC", 0, $st);
                $rst = el_dbfetch($st);
                $f = array();
                do {
                    $f[] = '<b>' . $rst['name'] . '</b>';
                } while ($rst = el_dbfetch($st));
                el_showalert('info', 'Струтура CSV-файла должна быть такова (пробелов вокруг разделителей быть не должно):<br>
<b>ID раздела</b> <span class="csvDivider">;</span> <b>Название раздела</b> <span class="csvDivider">;</span> <b>Активность</b> <span class="csvDivider">;</span> <b>Сортировка</b> <span class="csvDivider">;</span> ' . implode(' <span class="csvDivider">;</span> ', $f));
                ?>
                <br/>

                <table border="0" cellpadding="5" cellspacing="0" align="center" width="90%">
                    <tr>
                        <td valign="top" width="30%">
                            <form name="fileform" method="post" enctype="multipart/form-data">
                                <table align="center" cellpadding="4" class="el_tbl">
                                    <caption>Импорт</caption>
                                    <tr>
                                        <td align="center"><INPUT TYPE="file" name="fileUpload" id="fileUpload"/></td>
                                    </tr>
                                    <tr>
                                        <td align="right">Разделитель строк: <input type="text" size="3" name="separetor"
                                                                                    value="<?= (strlen($site_property['import_separator' . $cat]) > 0) ? $site_property['import_separator' . $cat] : '\n' ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right">Разделитель полей: <input type="text" size="3"
                                                                                    name="row_separetor"
                                                                                    value="<?= (strlen($site_property['import_row_separator' . $cat]) > 0) ? $site_property['import_row_separator' . $cat] : ';' ?>"
                                                                                    onblur="$('.csvDivider').html(this.value)">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right">Читать со строки №: <input type="text" size="3" name="start"
                                                                                     value="<?= (strlen($site_property['import_start' . $cat]) > 0) ? $site_property['import_start' . $cat] : '2' ?>">
                                        </td>
                                    </tr><!--
           <td align="right">Курс доллара к рублю: <input type="text" size="3" name="exchange" value="<?= (strlen($site_property['exchange']) > 0) ? $site_property['exchange'] : '25' ?>"></td>
           </tr>-->
                                    <tr>
                                        <td><strong>Что сделать с импортируемыми данными:</strong></td>
                                    </tr>
                                    <tr>
                                        <td align="right"><label for="mrew">Перезаписать каталог
                                                <input name="addmethod" type="radio" id="mrew" value="rewrite">
                                            </label></td>
                                    </tr>
                                    <tr>
                                        <td align="right"><label for="addro">Добавить в каталог
                                                <input type="radio" name="addmethod" id="addro" value="addrow" checked>
                                            </label></td>
                                    </tr>
                                    <tr>
                                        <td align="center">
                                            <input name="submit" type="Submit" class="but" value="Импорт">
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </td>
                        <td valign="top" width="70%">
                            <table align="center" cellpadding="4" class="el_tbl" width="100%">
                                <caption>Экспорт</caption>
                                <tr>
                                    <td align="center">
                                        Выберите раздел для экспорта
                                        <div id="exportcat"
                                             style="height:250px; overflow:auto; padding:10px; width:100% !important">
                                            <? el_catalogSelect('catalog' . $catalog_id, $cat) ?>
                                        </div>
                                        <a href="javascript:void(0)"
                                           onclick="$('#exportcat input[type=checkbox]').prop('checked', true)">Отметить
                                            все</a>
                                        <a style="margin-left:20px;" href="javascript:void(0)"
                                           onclick="$('#exportcat input[type=checkbox]').prop('checked', false)">Снять
                                            выделение</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <div id="export"></div>
                                        <INPUT TYPE="button" name="export" value="Экспорт каталога" onclick="csvExport()"
                                               class="but"/></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="excel<?= $cat ?>"
                 style="display:<?= ($_COOKIE['idshow']['excel' . $cat] == 'Y') ? 'block' : 'none' ?>">
                <table border="0" cellpadding="5" cellspacing="0" align="center" width="90%">
                    <tr>
                        <td valign="top" width="30%">
                            <form name="fileform" method="post" enctype="multipart/form-data">
                                <table align="center" cellpadding="4" class="el_tbl">
                                    <caption>Импорт</caption>
                                    <tr>
                                        <td align="center">
                                            <div class="info"><span class="color:red">Обратите внимание!</span> Корректный
                                                импорт возможен только,
                                                если файл не содержит объедененных ячеек.
                                            </div>
                                            <INPUT TYPE="file" name="excelFile" id="excelFile"/></td>
                                    </tr>

                                    <tr>
                                        <td align="center">
                                            <input name="submit" type="Submit" class="but" value="Импорт">
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <p>&nbsp;</p>
        <p>&nbsp;</p>

        <?
        if (count($allow_multiUpload) > 0) {
            ?>
            <script type="text/template" id="qq-template">
                <div class="qq-uploader-selector qq-uploader qq-gallery" qq-drop-area-text="Перетащите файлы сюда">
                    <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
                        <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                             class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
                    </div>
                    <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
                        <span class="qq-upload-drop-area-text-selector"></span>
                    </div>
                    <div class="qq-upload-button-selector qq-upload-button">
                        <div>Выберите файлы</div>
                    </div>
                    <span class="qq-drop-processing-selector qq-drop-processing">
            <span>Обработка файлов...</span>
            <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
        </span>
                    <ul class="qq-upload-list-selector qq-upload-list" role="region" aria-live="polite"
                        aria-relevant="additions removals">
                        <li>
                            <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
                            <div class="qq-progress-bar-container-selector qq-progress-bar-container">
                                <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                                     class="qq-progress-bar-selector qq-progress-bar"></div>
                            </div>
                            <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
                            <div class="qq-thumbnail-wrapper">
                                <img class="qq-thumbnail-selector" qq-max-size="120" qq-server-scale>
                            </div>
                            <button type="button" class="qq-upload-cancel-selector qq-upload-cancel">X</button>
                            <button type="button" class="qq-upload-retry-selector qq-upload-retry">
                                <span class="qq-btn qq-retry-icon" aria-label="Повторить"></span>
                                Retry
                            </button>

                            <div class="qq-file-info">
                                <div class="qq-file-name">
                                    <span class="qq-upload-file-selector qq-upload-file"></span>
                                    <span class="qq-edit-filename-icon-selector qq-btn qq-edit-filename-icon"
                                          aria-label="Переименовать"></span>
                                </div>
                                <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
                                <span class="qq-upload-size-selector qq-upload-size"></span>
                                <button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete">
                                    <span class="qq-btn qq-delete-icon" aria-label="Удалить"></span>
                                </button>
                                <button type="button" class="qq-btn qq-upload-pause-selector qq-upload-pause">
                                    <span class="qq-btn qq-pause-icon" aria-label="Пауза"></span>
                                </button>
                                <button type="button" class="qq-btn qq-upload-continue-selector qq-upload-continue">
                                    <span class="qq-btn qq-continue-icon" aria-label="Продолжить"></span>
                                </button>
                            </div>
                        </li>
                    </ul>

                    <dialog class="qq-alert-dialog-selector">
                        <div class="qq-dialog-message-selector"></div>
                        <div class="qq-dialog-buttons">
                            <button type="button" class="qq-cancel-button-selector">Закрыть</button>
                        </div>
                    </dialog>

                    <dialog class="qq-confirm-dialog-selector">
                        <div class="qq-dialog-message-selector"></div>
                        <div class="qq-dialog-buttons">
                            <button type="button" class="qq-cancel-button-selector">Нет</button>
                            <button type="button" class="qq-ok-button-selector">Да</button>
                        </div>
                    </dialog>

                    <dialog class="qq-prompt-dialog-selector">
                        <div class="qq-dialog-message-selector"></div>
                        <input type="text">
                        <div class="qq-dialog-buttons">
                            <button type="button" class="qq-cancel-button-selector">Отмена</button>
                            <button type="button" class="qq-ok-button-selector">Ok</button>
                        </div>
                    </dialog>
                </div>
            </script>

        <? } ?>
        <script>
            $(document).ready(function () {
                $(".chActive").change(function () {
                    let state = $(this).prop("checked"),
                        value = $(this).val(),
                        name = $(this).parents("tr").find("strong").text();
                    let changeName = (state) ? "активировать" : "деактивировать";
                    if (confirm("Вы уверены, что хотите " + changeName + " запись №" + value + "\n\" " + name + "\"?")) {
                        $.post("/editor/modules/catalog/index.php",
                            {ajax: 1, catalog_id: "<?=$catalog_id?>", id: value, setActive: (state) ? 1 : 0},
                            function(data){
                                alert(data);
                            })
                    }
                })

                function cleanArr(arr) {
                    var retArr = [];
                    var count = 0;
                    for (key in arr) {
                        if (arr[key] != undefined) {
                            retArr[count] = arr[key];
                            count++;
                        }
                    }
                    return retArr;
                }

                <? for ($a = 0; $a < count($allow_multiUpload); $a++){ ?>
                var fileArr<?=$a?> = [];
                var imgField = $("#<?=$allow_multiUpload[$a]?>".replace('Upload', ''));
                $('#<?=$allow_multiUpload[$a]?>').fineUploader({
                    template: 'qq-template',
                    request: {
                        endpoint: '/editor/modules/catalog/fine-upload/endpoint.php'
                    },
                    thumbnails: {
                        placeholders: {
                            waitingPath: '/js/fine-uploader/placeholders/not_available-generic.png',
                            notAvailablePath: '/js/fine-uploader/placeholders/not_available-generic.png'
                        }
                    },
                    deleteFile: {
                        enabled: true,
                        endpoint: '/editor/modules/catalog/fine-upload/endpoint.php'
                    },
                    validation: {
                        allowedExtensions: ['jpeg', 'jpg', 'gif', 'png']
                    },
                    callbacks: {
                        onComplete: function (id, name, json, xhr) {
                            if (json.success) {
                                fileArr<?=$a?>[id] = json.path;
                                imgField.val(cleanArr(fileArr<?=$a?>).join(' , '));
                                console.log(json);
                            }
                        },
                        onDeleteComplete: function (id) {
                            delete fileArr<?=$a?>[id];
                            imgField.val(cleanArr(fileArr<?=$a?>).join(' , '));
                        }
                    }
                });
                <? }?>
            });
        </script>
        <p>
        <?php
        mysqli_free_result($catalog);
    }
}
if (isset($_REQUEST['ajax'])) {
    if (ob_get_length()) ob_clean();
    header("Content-type: text/html; charset=UTF-8");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    include_once $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';
    $catalog_id = $_POST['catalog_id'];
    $cat = $_POST['cat'];

    if (strlen($_REQUEST['pages']) > 0) {

        $fieldList = el_dbselect("SELECT field, name FROM catalog_prop WHERE catalog_id='$catalog_id' ORDER BY sort", 0, $fieldList);
        $rf = el_dbfetch($fieldList);
        $rfield = array();
        do {
            $rfield[] = 'field' . $rf['field'];
            $rname[] = $rf['name'];
        } while ($rf = el_dbfetch($fieldList));
        $pagesArr = explode('|', $_REQUEST['pages']);
        if(count($pagesArr) == 0){
            echo 'Выберите разделы каталога для экспорта.';
            exit();
        }
        $subQuery = (count($pagesArr) > 1) ?
            ' AND (catalog_' . $catalog_id . '_data.cat=' . implode(' OR catalog_' . $catalog_id . '_data.cat=', $pagesArr) . ')'
            : ' AND catalog_' . $catalog_id . '_data.cat=' . $_REQUEST['pages'];
        $e = el_dbselect("SELECT catalog_" . $catalog_id . "_data.id, 
		catalog_" . $catalog_id . "_data.cat AS cat, 
		catalog_" . $catalog_id . "_data.active AS active, 
		catalog_" . $catalog_id . "_data.sort AS sort, 
		catalog_" . $catalog_id . "_data.goodid AS goodid, 
		cat.name AS catname, 
		" . implode(', ', $rfield) . " 
		FROM catalog_" . $catalog_id . "_data, cat 
		WHERE catalog_" . $catalog_id . "_data.active=1 AND catalog_" . $catalog_id . "_data.cat=cat.id 
		$subQuery ORDER BY cat.id ASC, catalog_" . $catalog_id . "_data.sort DESC, 
		catalog_" . $catalog_id . "_data.id ASC",
            0, $e, 'result', true);
        $re = el_dbfetch($e);
        if (mysqli_num_rows($e) > 0) {
            $ex = 'id раздела;Название раздела;Активность;Сортировка;' . implode(';', $rname) . "\n";
            do {
                $exField = array();
                for ($i = 0; $i < count($rfield); $i++) {
                    $exField[] = str_replace(';', '^', str_replace('^', ' ', str_replace("\n", '', str_replace("\r", '', $re[$rfield[$i]]))));
                }
                $ex .=/*$re['id'].';'.$re['cat'].';'.$re['active'].';'.$re['sort'].';'.$re['goodid'].';'.*/
                    $re['cat'] . ';' . $re['catname'] . ';' . $re['active'] . ';' . $re['sort'] . ';' . implode(';', $exField) . "\n";
            } while ($re = el_dbfetch($e));
            $n = el_dbselect("SELECT name FROM cat WHERE id=" . intval($_POST['cat']), 0, $n, 'row');
            $terget_file = $_SERVER['DOCUMENT_ROOT'] . '/files/export/' . el_translit($n['name'] . '.csv');
            if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/files/export/')) mkdir($_SERVER['DOCUMENT_ROOT'] . '/files/export/', 0755);
            $fp = fopen($terget_file, 'w');
            fwrite($fp, iconv('UTF-8', 'Windows-1251', $ex));
            fclose($fp);
            require_once($_SERVER['DOCUMENT_ROOT'] . "/editor/e_modules/zip.lib.php");
            include_once($_SERVER['DOCUMENT_ROOT'] . "/editor/e_modules/pclzip.lib.php");
            $archive = new PclZip($terget_file . '.zip');
            if ($archive->create($terget_file, '', $_SERVER['DOCUMENT_ROOT'] . '/files/export/') == 0) {
                die("Error : " . $archive->errorInfo(true));
            } else {
                //unlink($terget_file);
            }
            echo '<a href="http://' . $_SERVER['SERVER_NAME'] . '/files/export/' . el_translit($n['name'] . '.csv.zip') . '">Скачать</a><br>
			<iframe src="/files/export/' . el_translit($n['name'] . '.csv.zip') . '" width=1 height=1 style="display:none"></iframe>';
        } else {
            echo 'В этом каталоге пока пусто.';
        }
    }

    if(strlen($_REQUEST['setActive']) > 0){
        $catalog_id = $_POST['catalog_id'];
        $id = intval($_POST['id']);
        $active = intval($_POST['setActive']);
        $res = el_dbselect("UPDATE catalog_".$catalog_id."_data SET active=$active WHERE id=".$id, 0, $res, 'result', true);
        if($res != false){
            echo 'Запись '.(($active == 1) ? 'активирована' :'деактивирована');
        }else{
            echo 'Ошибка изменения записи.';
        }
    }
}
?>