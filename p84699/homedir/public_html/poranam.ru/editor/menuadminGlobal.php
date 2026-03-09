<?php
session_start();
require_once('../Connections/dbconn.php');

if ((isset($_POST['edit_sites']) && count($_POST['edit_sites']) > 0)) {
    setcookie("edit_sites", serialize($_POST['edit_sites']), time() + 525600);
}

$siteId = $_SESSION['site_id'] = intval($_GET['site_id']);

//error_reporting(E_ALL);

$requiredUserLevel = array(0, 1, 2);
include($_SERVER['DOCUMENT_ROOT'] . "/editor/secure/secure.php");

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
if (isset($_POST['Submit']) && empty($_POST['delcat'])) {
    for ($i = 0; $i < count($_POST['id']); $i++) {
        $updateSQL = sprintf("UPDATE cat SET name=%s, sort=%s WHERE cat_id=%s",
            GetSQLValueString(addslashes(str_replace('``', '"', $_POST['name'][$i])), "text"),
            GetSQLValueString($_POST['sort'][$i], "int"),
            GetSQLValueString($_POST['id'][$i], "int"));


        $Result1 = el_dbselect($updateSQL, 0, $Result1);
    }
    el_log('Управление разделами', 'Изменен порядок следования разделов');
    el_clearcache('menu');
}

//Удаление раздела
if (!empty($_POST['delcat'])) {
    if (intval($_POST['delcat']) > 0) {
        el_deleteGlobalCat(intval($_POST['delcat']));
        el_dbselect("OPTIMIZE TABLE `cat`", 0, $res);
        el_dbselect("OPTIMIZE TABLE `content`", 0, $res);
    } else {
        echo '<script>alert("Нельзя удалять главный раздел!")</script>';
    }
}

/*
$query_dbmenu = "SELECT * FROM cat WHERE parent = 0 AND site_id=" . intval($_SESSION['site_id']) . " ORDER BY sort ASC";
$dbmenu = el_dbselect($query_dbmenu, 0, $dbmenu);
$row_dbmenu = el_dbfetch($dbmenu);
$totalRows_dbmenu = mysqli_num_rows($dbmenu);

$colname_dbmenupod = "1";
if (isset($_GET['id'])) {
    $colname_dbmenupod = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}

$query_dbmenupod = sprintf("SELECT * FROM cat WHERE parent = %s AND site_id=" . intval($_SESSION['site_id']) . " ORDER BY sort ASC", $colname_dbmenupod);
$dbmenupod = el_dbselect($query_dbmenupod, 0, $dbmenupod);
$row_dbmenupod = el_dbfetch($dbmenupod);
$totalRows_dbmenupod = mysqli_num_rows($dbmenupod);*/

$sites = el_dbselect("SELECT * FROM sites", 0, $sites, 'result', true);
$rsites = el_dbfetch($sites);
?>

<html>
<head>
    <title>Управление общими разделами</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="style.css" rel="stylesheet" type="text/css">
    <script src="/js/jquery-1.11.0.min.js"></script>
    <script src="/js/jquery-ui.js"></script>
    <script src="/js/jquery.mjs.nestedSortable.js"></script>
    <script src="/js/tooltip.js"></script>
    <script language="javascript">


        function MM_openBrWindow(theURL, winName, features, myWidth, myHeight, isCenter) { //v3.0
            top.MM_openBrWindow(theURL, winName, features, myWidth, myHeight, isCenter);
        }

        function MM_goToURL() { //v3.0
            var i, args = MM_goToURL.arguments;
            document.MM_returnValue = false;
            for (i = 0; i < (args.length - 1); i += 2) eval(args[i] + ".location='" + args[i + 1] + "'");
        }

        function check(item_name) {
            var OK = confirm('Вы действительно хотите удалить раздел "' + item_name + '" ?');
            if (OK) {
                return true
            } else {
                return false
            }
        }

        function writeCookie(name, value, hours) {
            var expire = "";
            if (hours != null) {
                expire = new Date((new Date()).getTime() + hours * 3600000);
                expire = "; expires=" + expire.toGMTString();
            }
            document.cookie = name + "=" + value + expire;
        }

        // Example:

        // alert( readCookie("myCookie") );

        function readCookie(name) {
            var cookieValue = "";
            var search = name + "=";
            if (document.cookie.length > 0) {
                offset = document.cookie.indexOf(search);
                if (offset != -1) {
                    offset += search.length;
                    end = document.cookie.indexOf(";", offset);
                    if (end == -1) end = document.cookie.length;
                    cookieValue = document.cookie.substring(offset, end)
                }
            }
            return cookieValue;
        }


        function opentree(id, lev, key) {
            var par = $("#tr" + id),
                im = $("#im" + id),
                obj = $("#ch" + id);
            if (obj.css("display") == 'none') {
                im.text("remove");
                obj.show();
                document.cookie = "amenu" + id + "=Y; expires=Thu, 31 Dec 2100 23:59:59 GMT;";
                document.cookie = "amenuid" + id + "=" + id + "; expires=Thu, 31 Dec 2100 23:59:59 GMT;";
                document.cookie = "amenulev" + id + "=" + lev + "; expires=Thu, 31 Dec 2100 23:59:59 GMT;";
            } else {
                document.cookie = "amenu" + id + "=N; expires=Thu, 31 Dec 2100 23:59:59 GMT;";
                im.text("add");
                obj.hide();
            }
        }

        $(document).ready(function () {
            $('*').tooltip({showURL: false});

            $("#menuList").nestedSortable({
                revert: 250,
                revertDuration: 200,
                snap: true,
                snapMode: "outer",
                containment: "parent",
                axis: "y",
                cursor: "grabbing",
                handle: ".parent1",
                items: "li",
                // isTree: true,
                disableParentChange: true,
                protectRoot: true,
                //placeholder: 'placeholder',
                //forcePlaceholderSize: true,
                toleranceElement: "> div",
                stop: function (event, ui) {
                    var $lis = $("#menuList li");
                    for (var i = 0; i < $lis.length; i++) {
                        $($lis[i]).find(".parent3 #sort").val(i);
                    }
                    $("#saveSort").show();
                }
            });

            $(".parent, .child").on("mouseover", function (e) {
                e.stopPropagation();
                var id = $(this).attr("id");
                $("#" + id + " > div").css("backgroundColor", "#ececec");
                $("#" + id + " > .parent4 > table").show();
            }).on("mouseout", function (e) {
                e.stopPropagation();
                var id = $(this).attr("id");
                $("#" + id + " > div").css("backgroundColor", "");
                $("#" + id + " > .parent4 > table").hide();
            });

            $("#select_all").on("change", function(){
                $("#siteList input:checkbox").prop("checked", $(this).prop("checked"));
            });
        });

    </script>

    <style type="text/css">
        .lbr {
            min-width: 35px;
        }

        ol {
            margin: 0;
            padding-inline-start: 0;
        }

        #menuList, #menuList li {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .parent {
            left: 10px;
            background-color: #fff;
            width: 710px;
            z-index: 1;
            float: left;
            user-select: none;
            /*border-bottom: 1px solid #d3d3d3;*/
        }

        .parent .parent4 table, .child .parent4 table {
            display: none;
        }

        .parent1 {
            width: 85px;
            position: relative;
            float: left;
            height: 30px;
            padding: 2px 0;
        }

        .parent1 i {
            cursor: pointer;
            vertical-align: top;
            vertical-align: -webkit-baseline-middle;
            font-size: 20px;
            padding-top: 3px;
        }

        .parent1 img {
            padding-top: 3px;
        }

        .parid {
            width: 48px;
            float: left;
            position: relative;
            height: 30px;
            line-height: 30px;
            padding: 2px 0;
        }

        .parent2 {
            position: relative;
            float: left;
            padding: 2px 0;
            z-index: 10;
            line-height: 30px;
            height: 30px;
        }

        .parent2 span {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
        }

        .parent3 {
            position: relative;
            float: left;
            z-index: 10;
            height: 30px;
            padding-top: 4px;
            cursor: move;
        }

        .parent4 {
            width: 210px;
            position: relative;
            float: left;
            z-index: 10;
            height: 30px;
            padding-left: 10px;
            padding-top: 4px;
        }

        .parent5 {
            width: 101px;
            float: left;
            padding: 6px 7px;
            z-index: 10;
            height: 30px;
        }

        .child {
            padding-left: 20px !important;
            background-color: #fff;
            width: 710px;
            z-index: 1;
            float: left;
            user-select: none;
        }

        .drag {
            cursor: grab !important;
            color: lightgrey;
        }

        .drag:hover {
            color: #3197D3;
        }

        .placeholder, .ui-sortable-placeholder {
            outline: 1px dashed #4183C4;
            visibility: visible !important;
            margin-bottom: 3px;
        }

        .mjs-nestedSortable-error {
            background: #fbe3e4;
            border-color: transparent;
        }

        #siteList {
            max-height: 200px;
            max-width: 500px;
            border: 1px solid #3bb4ea;
            overflow: auto;
        }

        #siteList form {
            margin-bottom: 20px;
        }

        #siteList label {
            display: block;
            margin: 3px 5px;
        }
    </style>
</head>

<body>
<h5>Управление общими разделами</h5>

<h5>Выбор редактируемых сайтов</h5>
<form method="post">
    <div id="siteList">
        <?
        do {
            $sel = ' checked';
            if(isset($_POST['edit_sites'])){
                if(in_array($rsites['id'], $_POST['edit_sites'])){
                    $sel = ' checked';
                }else{
                    $sel = '';
                }
            }else{
                if(isset($_COOKIE['edit_sites']) && in_array($rsites['id'], unserialize($_COOKIE['edit_sites']))){
                    $sel = ' checked';
                }else{
                    $sel = '';
                }
            }
            ?>
            <label><input type="checkbox" name="edit_sites[]" value="<?= $rsites['id'] ?>"<?=$sel?>> <?= $rsites['short_name'] ?></label>
            <?
        } while ($rsites = el_dbfetch($sites));
        ?>
    </div>
    <label><input type="checkbox" id="select_all"> Выделить все</label><br>
    <input type="submit" name="site_selected" value="Выбрать" class="but">
</form>
<?
if ((isset($_POST['edit_sites']) && count($_POST['edit_sites']) > 0) || (isset($_COOKIE['edit_sites']) && count(unserialize($_COOKIE['edit_sites'])) > 0)) {
    $_POST['edit_sites'] = (isset($_POST['edit_sites'])) ? $_POST['edit_sites'] : unserialize($_COOKIE['edit_sites']);
    ?>
    <button title="Добавить новый раздел в меню"
            onClick="MM_openBrWindow('newcategory.php?parentid=0&site_id=<?= $_SESSION['site_id'] ?>&cat_type=2','newcat','scrollbars=no,resizable=yes','720',
                    '500','true')">
        <i class="material-icons">add_circle</i> Создать новый раздел
    </button>

    <input id="saveSort" value="Сохранить изменения" onclick="document.edit.submit()" class="but"
           style="display:none; margin-left: 20px;">
    <br>

    <?
    function el_child($parent)
    {
        global $database_dbconn, $dbconn, $hid1;
        $child = mysqli_query($dbconn, "SELECT * FROM cat WHERE parent='$parent' AND site_id=" . intval($_SESSION['site_id']));
        $allchil = mysqli_num_rows($child);
        if ($allchil > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function menuadminchild($parent, $table, $lev, $imenu)
    {//Child Items
        static $level;
        global $userLevel;
        $querymenuchild = "SELECT * FROM cat WHERE site_id IN (".implode(', ', $_POST['edit_sites']).") AND parent='$parent' AND cat_type = 2 GROUP BY cat_id ORDER BY sort ASC";
        $menuchild = el_dbselect($querymenuchild, 0, $menuchild, 'result', true);
        $row_menuchild = el_dbfetch($menuchild);
        $idchild = $row_menuchild['id'];
        if ($idchild) {//if item is exist...
            $imenu++;
            ($imenu > 1) ? $lev++ : $lev = $lev;
            echo '<ol id="ch' . $parent . '" style="display: ' . (($_COOKIE['amenu' . $parent] == 'Y') ? 'block' : 'none') . '">';
            do {
                $parent = $row_menuchild['id'];
                $hasChild = el_child($parent);
                if (strlen($row_menuchild['edit']) > 0) {
                    $araccess = explode(",", $row_menuchild['edit']);
                } else {
                    $araccess = array(0);
                }
                if (in_array($userLevel, $araccess) || $userLevel == "1") {
                    ?>
                    <li class="child" id="tr<?php echo $row_menuchild['id']; ?>">

                        <div class="parent1">
                            <i class="material-icons drag" title="Перетащите для изменения сортировки в меню">drag_indicator</i>
                            <img src="img/level_<?= $lev ?>.gif" border="0" align="middle">
                            <? if ($hasChild) { ?>
                                <i class="material-icons" title="Подразделы" id="im<?= $row_menuchild['id'] ?>"
                                   onClick="opentree(<?= $row_menuchild['id'] ?>, <?= $_GET['lev'] + 1 ?>, 0)">
                                    <?= ($_COOKIE['amenu' . $row_menuchild['id']] == 'Y') ? 'remove' : 'add' ?></i>
                            <? } ?>
                        </div>
                        <div class="parid">&nbsp;<?php echo $row_menuchild['id']; ?></div>
                        <div class="parent2"><input name="id[]" type="hidden" id="id"
                                                    value="<?php echo $row_menuchild['id']; ?>">
                            <span <?= ($row_menuchild['menu'] != "Y") ? "style=\"color:#999999\"" : "" ?>
                                  title="<?= stripslashes(htmlspecialchars($row_menuchild['name'])) ?>"
                                  onDblClick="MM_openBrWindow('e_modules/catdescedit.php?id=<?php echo $row_menuchild['id']; ?>','newcat','scrollbars=yes,resizable=yes','500','200','true')"
                            ><?= stripslashes($row_menuchild['name']) ?></span>
                            <input type="hidden" name="name[]" value="<?= stripslashes($row_menuchild['name']) ?>">
                        </div>
                        <div class="parent3" align="center"><input name="path" type="hidden"
                                                                   value="<?php echo $row_menuchild['path']; ?>"> <input
                                    name="sort[]" type="hidden" id="sort" value="<?php echo $row_menuchild['sort'] ?>"
                                    size="2">
                        </div>

                        <div class="parent4">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" class="lbr"><a
                                                href="editor.php?cat=<?php echo $row_menuchild['cat_id']; ?>" target="Main"
                                                title="Редактировать содержимое раздела">
                                            <i class="material-icons">edit</i></a>
                                    </td>
                                    <td align="center" class="lbr">
                                        <a href="http://<? echo $_SERVER['SERVER_NAME'] . $row_menuchild['path']; ?>"
                                           title="<? echo 'Открыть раздел в новом окне: ' . $_SERVER['SERVER_NAME'] . $row_menuchild['path']; ?>"
                                           target="_blank"><i class="material-icons">screen_share</i></a>
                                    </td>
                                    <td align="center" class="lbr">
                                        <a href="javascript:void(0)" title="Добавить подраздел">
                                            <i onClick="MM_openBrWindow('newcategory.php?parentid=<?= $row_menuchild['id'] ?>&site_id=<?= $_SESSION['site_id'] ?>','newcat',
                                                    'scrollbars=no,resizable=yes',
                                                    '720','450','true') " class="material-icons">playlist_add</i>
                                        </a>
                                    </td>
                                    <td align="center" class="lbr">
                                        <a href="javascript:void(0)" title="Перенести в другой раздел">
                                            <i class="material-icons"
                                               onClick="MM_openBrWindow('menumigrate.php?id=<?= $row_menuchild['id'] ?>','newcat',
                                                       'scrollbars=yes,resizable=yes','400','600','true')">low_priority</i>
                                        </a>
                                    </td>

                                    <td align="center" class="lbr">
                                        <a href="javascript:void(0)" title="Свойства раздела">
                                            <i onClick="MM_openBrWindow('metainfo.php?id=<?= $row_menuchild['id'] ?>&lev=1','metainfo','scrollbars=yes,resizable=yes',
                                                    '650','700','true')"
                                               class="material-icons">settings_applications</i>
                                        </a>
                                    </td>
                                    <td align="center" class="lbr">
                                        <a href="javascript:void(0)" title="Удалить раздел">
                                            <i onClick="document.edit.delcat.value=<?= $row_menuchild['id'] ?>; if(check('<?= $row_menuchild['name'] ?>')){document.edit
                                                    .submit()};" class="material-icons">delete_forever</i>
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <input name="action" type="hidden" id="action" value=""><input type="hidden"
                                                                                           name="MM_update"
                                                                                           value="edit">
                            <input name="parentNode<?= $parent ?>" type="hidden" id="parentNode<?= $parent ?>"
                                   value="<?= $parent ?>">
                        </div>
                        <? if ($hasChild) {
                            menuadminchild($parent, 'cat', $lev, $imenu);
                        } ?>
                    </li>

                    <?

                }
            } while ($row_menuchild = el_dbfetch($menuchild));
            echo '</ol>';
        }
    }


    $imenu = 0;
    function el_menuadmin()
    {//Parent items, first level only
        global $database_dbconn;
        global $dbconn, $userLevel;
        global $SERVER_NAME;

        $querymenutree = "SELECT * FROM cat WHERE site_id IN (".implode(', ', $_POST['edit_sites']).") 
        AND cat_type = 2 AND (parent IN (SELECT cat_id FROM cat WHERE cat_type != 2) OR parent = 0) GROUP BY cat_id ORDER BY sort";
        $menutree = el_dbselect($querymenutree, 0, $menutree, 'result', true);
        $row_menutree = el_dbfetch($menutree);
        $m = 0;
        if (el_dbnumrows($menutree) > 0) {
            do {
                $m++;
                if (strlen($row_menutree['edit']) > 0) {
                    $araccess = explode(",", $row_menutree['edit']);
                } else {
                    $araccess = array(0);
                }

                if (in_array($userLevel, $araccess) || $userLevel == "1") {
                    $parent = $row_menutree['id'];
                    $hasChild = el_child($parent);
                    ?>
                    <li class="parent" id="tr<?= $row_menutree['id']; ?>">

                        <div class="parent1">
                            <?php
                            if ($row_menutree['path'] != '') {
                                ?>
                                <i class="material-icons drag" style="cursor: grab"
                                   title="Перетащите для изменения сортировки в меню">drag_indicator</i>
                                <?php
                            }
                            ?>
                            <img src="img/level_1.gif" border=0 align=middle>
                            <? if ($hasChild) { ?>
                                <i class="material-icons" title="Подразделы" id="im<?= $row_menutree['id'] ?>"
                                   onClick="opentree(<?= $row_menutree['id'] ?>, 2, 0);"><?= ($_COOKIE['amenu' . $parent] == 'Y') ? 'remove' : 'add' ?></i>
                            <? } ?>
                        </div>
                        <div class="parid">&nbsp;<?php echo $row_menutree['id']; ?></div>
                        <div class="parent2">
                            <input name="id[]" type="hidden" id="id" value="<?php echo $row_menutree['id']; ?>">
                            <span <?= ($row_menutree['menu'] != "Y") ? "style=\"color:#999999\"" : "" ?>
                                  title="<?= stripslashes(htmlspecialchars($row_menutree['name'])) ?>"
                                  onDblClick="MM_openBrWindow('e_modules/catdescedit.php?id=<?php echo $row_menutree['id']; ?>','newcat','scrollbars=yes,resizable=yes','500','300','true')"
                            ><?= stripslashes($row_menutree['name']) ?></span>
                            <input type="hidden" name="name[]" value="<?= stripslashes($row_menutree['name']) ?>">
                        </div>

                        <div class="parent3" align="center">
                            <input name="path" type="hidden" value="<?php echo $row_menutree['path']; ?>">
                            <? if ($m != 1) { ?>
                                <input name="sort[]" type="hidden" id="sort"
                                       value="<?php echo $row_menutree['sort'] ?>" size="4">
                            <? } else {

                                echo '<input name="sort[]" type="hidden" id="sort" value="' . $row_menutree['sort'] . '">';
                            } ?>

                        </div>
                        <div class="parent4">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" class="lbr"><a
                                                href="editor.php?cat=<?php echo $row_menutree['cat_id']; ?>"
                                                target="Main"
                                                title="Редактировать содержимое раздела">
                                            <i class="material-icons">edit</i></a>
                                    </td>
                                    <td align="center" class="lbr">
                                        <a href="http://<? echo $_SERVER['SERVER_NAME'] . $row_menutree['path']; ?>"
                                           title="<? echo 'Открыть раздел в новом окне: ' . $_SERVER['SERVER_NAME'] . $row_menutree['path']; ?>"
                                           target="_blank"><i class="material-icons">screen_share</i></a>
                                    </td>
                                    <td align="center" class="lbr">
                                        <a href="javascript:void(0)" title="Добавить подраздел">
                                            <i onClick="MM_openBrWindow('newcategory.php?parentid=<?= $row_menutree['id'] ?>&site_id=<?= $_SESSION['site_id'] ?>','newcat',
                                                    'scrollbars=no,resizable=yes',
                                                    '720','450','true') " class="material-icons">playlist_add</i>
                                        </a>
                                    </td>
                                    <td align="center" class="lbr">
                                        <a href="javascript:void(0)" title="Перенести в другой раздел">
                                            <i class="material-icons"
                                               onClick="MM_openBrWindow('menumigrate.php?id=<?= $row_menutree['id'] ?>','newcat',
                                                       'scrollbars=yes,resizable=yes','400','600','true')">low_priority</i>
                                        </a>
                                    </td>

                                    <td align="center" class="lbr">
                                        <a href="javascript:void(0)" title="Свойства раздела">
                                            <i onClick="MM_openBrWindow('metainfo.php?id=<?= $row_menutree['id'] ?>&lev=1','metainfo','scrollbars=yes,resizable=yes',
                                                    '650','700','true')"
                                               class="material-icons">settings_applications</i>
                                        </a>
                                    </td>
                                    <? if ($row_menutree['path'] != '') { ?>
                                        <td align="center" class="lbr">
                                            <a href="javascript:void(0)" title="Удалить раздел">
                                                <i onClick="document.edit.delcat.value=<?= $row_menutree['id'] ?>; if(check('<?= $row_menutree['name'] ?>')){document.edit
                                                        .submit()};" class="material-icons">delete_forever</i>
                                            </a>
                                        </td>
                                    <? } else { ?>
                                        <td align="center" class="lbr"></td>
                                        <?
                                    } ?>
                                </tr>
                            </table>
                            <input name="action" type="hidden" id="action">
                        </div>
                        <input type="hidden" name="MM_update" value="edit">
                        <? if ($hasChild) {
                            menuadminchild($parent, 'cat', $lev = 2, $imenu = 0);
                        } ?>
                    </li>

                    <?
                    /*if($_COOKIE['amenu'.['id']]=='Y'){
                        echo '<script>opentree('.$row_menutree['id'].', '.$_COOKIE['amenulev'.$row_menutree['id']].', 1)</script>';
                    }*/
                    $imenu = 0;
//menuadminchild($parent, 'cat', $lev=2, $imenu=0);
                }
            } while ($row_menutree = el_dbfetch($menutree));
        } else {
            echo '<h4>Пока нет ни одного раздела.</h4>';
        }
    }

    ?>
    <table id="mainlist" class="menutd">

        <thead
        >
        <tr>
            <th style="width:55px">Уровень</th>
            <th style="width:30px">ID</th>
            <th style="width:230px">Название</th>
            <th style="width:50px"></th>
            <th style="width:198px"></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="5">
                <form method="POST" action="<?php echo $editFormAction; ?>" name="edit">
                    <ol id="menuList" class="sortable">
                        <? el_menuadmin();
                        /*function el_haveChild($id)
                        {
                            $res = el_dbselect("SELECT id FROM cat WHERE parent=$id", 0, $res);
                            return (mysqli_num_rows($res) > 0) ? true : false;
                        }

                        $allcat = el_dbselect("(SELECT id FROM cat ORDER BY parent ASC) UNION (SELECT id FROM cat WHERE parent>0 ORDER BY parent ASC)", 0, $allcat);
                        $allc = el_dbfetch($allcat);
                        if (mysqli_num_rows($allcat) > 0) {
                            $i = 0;
                            echo '<script>
            var openCats=new Array();
            var openCookie=new Array();';
                            do {
                                if ($_COOKIE['amenu' . $allc['id']] == 'Y' && el_haveChild($allc['id'])) {
                                    echo '
                    openCats[' . $i . ']=' . $allc['id'] . ';
                    openCookie[' . $i . ']=' . $_COOKIE['amenulev' . $allc['id']] . ';';
                                    $i++;
                                }
                            } while ($allc = el_dbfetch($allcat));
                            echo '
            showtreememo(openCats, openCookie);
            </script>' . "\n";
                        }*/
                        ?>
                    </ol>
                    <input name="delcat" type="hidden" id="delcat"><br><br>
                    <input name="Submit" type="hidden">
                </form>
            </td>
        </tr>
        </tbody>
    </table>
    <?
}
?>
</body>
</html>