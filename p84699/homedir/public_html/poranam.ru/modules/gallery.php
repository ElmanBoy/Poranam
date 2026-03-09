<link href="/js/fancybox/jquery.fancybox.css" rel="stylesheet" type="text/css"/>
<script src="/js/fancybox/jquery.fancybox.pack.js" type="text/javascript"></script>
<?
//error_reporting(E_ALL);
$database_dbconn = el_getvar('database_dbconn');
$dbconn = el_getvar('dbconn');
$path = el_getvar('path');
include $_SERVER['DOCUMENT_ROOT'] . '/Connections/site_props.php';
$colNums = ($site_property['gallcols' . $row_dbcontent['cat']]) ? $site_property['gallcols' . $row_dbcontent['cat']] : 2;

if (isset($_GET['id']) && intval($_GET['id']) != 0) {
    if (isset($_POST['rating']) && $_POST['rating'] > 0) {
        $r = el_dbselect("SELECT raiting FROM photo WHERE id='" . intval($_GET['id']) . "'", 0, $r);
        $ra = el_dbfetch($r);
        $nrait = $ra['raiting'] + $_POST['rating'];
        $nres = el_dbselect("UPDATE photo SET raiting='" . $nrait . "' WHERE id='" . $_GET['id'] . "' ", 0, $nres);
    }
    $bigim = el_dbselect("SELECT * FROM photo WHERE id='" . intval($_GET['id']) . "'", 0, $bigim);
    $row_bigim = el_dbfetch($bigim);
    $src = $_SERVER['DOCUMENT_ROOT'] . $row_bigim['path'];

    if (file_exists($src) && is_file($src)) {
        $pn = '';
        $n = el_dbselect("SELECT id FROM photo WHERE id>'" . intval($_GET['id']) . "' 
						AND caption='" . $row_dbcontent['cat'] . "' ORDER BY sort ASC, id DESC", 0, $n, 'row');

        if (intval($n['id']) == 0) {
            $n = el_dbselect("SELECT id FROM photo WHERE caption='" . $row_dbcontent['cat'] . "' ORDER BY sort ASC, id DESC", 0, $n, 'row');
            $pn = '';
        } else {
            if (intval($_GET['pn']) > 0) $pn = '&pn=' . intval($_GET['pn']);
        }//<a href="'.$row_dbcontent['path'].'/?id='.$n['id'].$pn.'#ptop"></a>
        echo '<a name="ptop"></a><br>
		<center><a id="fancy" href="' . $row_bigim['path'] . '"><img src="' . $row_bigim['path'] . '" border="0" class="gallery_img_current nofloat"' . (($row_bigim['bigw'] > 650) ? ' width=650' : '') . '></a>
		<div class="clear"></div>
		' . stripslashes($row_bigim['text']) . '<br>' . stripslashes($row_bigim['author']) . '</center>';
    } else {
        echo '<h4 align=center>К сожалению, на сервере нет такой фотографии</h4>';
    }

    if ($row_bigim['in_comments'] == '1') {
        if ($row_bigim['in_rait'] == '1') {
            $allow_raiting = 1;
        } else {
            $allow_raiting = 0;
        }
        //echo '<center>';
        //include $_SERVER['DOCUMENT_ROOT'].'/modules/comments.php';
        //echo '</center>';
    }
}

$currentPage = "";

$maxRows_Recordset1 = (strlen($site_property['gallphotos' . $row_dbcontent['cat']]) > 0) ? $site_property['gallphotos' . $row_dbcontent['cat']] : 8;
$pn = 0;
if (isset($_GET['pn'])) {
    $pn = $_GET['pn'];
}
$startRow_Recordset1 = $pn * $maxRows_Recordset1;

$colname_Recordset1 = $row_dbcontent['cat'];
;
$query_Recordset1 = sprintf("SELECT * FROM photo WHERE caption = '%s' ORDER BY sort ASC, id ASC", $colname_Recordset1);
$query_limit_Recordset1 = sprintf("%s LIMIT %d, %d", $query_Recordset1, $startRow_Recordset1, $maxRows_Recordset1);
$Recordset1 = el_dbselect($query_limit_Recordset1, 0, $Recordset1, 'result', true);
$row_Recordset1 = el_dbfetch($Recordset1);

if (isset($_GET['tr'])) {
    $tr = $_GET['tr'];
} else {
    $all_Recordset1 = mysql_query($query_Recordset1);
    $tr = mysqli_num_rows($all_Recordset1);
}
$totalPages_Recordset1 = ceil($tr / $maxRows_Recordset1) - 1;

$queryString_Recordset1 = "";
if (!empty($_SERVER['QUERY_STRING'])) {
    $params = explode("&", $_SERVER['QUERY_STRING']);
    $newParams = array();
    foreach ($params as $param) {
        if (stristr($param, "pn") == false &&
            stristr($param, "tr") == false) {
            array_push($newParams, $param);
        }
    }
    if (count($newParams) != 0) {
        $queryString_Recordset1 = "&" . implode("&", $newParams);
    }
}
$queryString_Recordset1 = sprintf("&tr=%d%s", $tr, $queryString_Recordset1);


$maxRows_albums = 12;
$apn = 0;
if (isset($_GET['apn'])) {
    $apn = $_GET['apn'];
}
$startRow_albums = $apn * $maxRows_albums;

$all_albums = el_dbselect("SELECT * FROM photo_albums WHERE parent_cat=" . intval($row_dbcontent['cat']), 0, $all_albums);
$albums = el_dbselect("SELECT photo_albums.name, photo_albums.date_create, photo_albums.cover, photo_albums.cat as cat_id, cat.path, cat.ptext 
FROM cat LEFT JOIN photo_albums ON cat.id=photo_albums.cat
WHERE photo_albums.parent_cat=" . intval($row_dbcontent['cat']) . "
ORDER BY photo_albums.sort ASC, photo_albums.id DESC LIMIT $startRow_albums, $maxRows_albums", 0, $albums, true);
$row_albums = el_dbfetch($albums);

if (isset($_GET['atr'])) {
    $atr = $_GET['atr'];
} else {
    $atr = mysqli_num_rows($all_albums);
}
$totalPages_albums = ceil($atr / $maxRows_albums) - 1;

$queryString_Recordset1 = "";
if (!empty($_SERVER['QUERY_STRING'])) {
    $paramsA = explode("&", $_SERVER['QUERY_STRING']);
    $newParamsA = array();
    foreach ($paramsA as $paramA) {
        if (stristr($paramA, "apn") == false &&
            stristr($paramA, "atr") == false &&
            stristr($paramA, "pn") == false &&
            stristr($paramA, "tr") == false) {
            array_push($newParamsA, $paramA);
        }
    }
    if (count($newParamsA) != 0) {
        $queryString_Recordset1 .= "&" . implode("&", $newParamsA);
    }
}
$queryString_Recordset1 = sprintf("&atr=%d%s", $atr, $queryString_Recordset1);
?>
<p align="center">
    <? if (mysqli_num_rows($albums) > 0){
    if ($atr > $maxRows_albums) {
        el_paging($apn, $currentPage, $queryString_albums, $totalPages_albums, $maxRows_albums, $atr, 'apn', 'atr');
    } ?>

    <table class="gallery_tbl" border="0" align="center" cellpadding="3" cellspacing="3">
        <tr>
            <?php if (mysqli_num_rows($albums) > 0) {
                $string = 0;
                do {
                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $row_albums['cover'])) {
                        $bigSize = getimagesize($_SERVER['DOCUMENT_ROOT'] . $row_albums['cover']);
                        $total_img = el_dbselect("SELECT id FROM photo WHERE caption=" . $row_albums['cat_id'], 0, $total_img);
                        $timg = mysqli_num_rows($total_img);
                        include $_SERVER['DOCUMENT_ROOT'] . '/tmpl/gallery/albums.php';
                        $string++;
                        if ($string == $colNums) {
                            echo "</tr><tr>";
                            $string = 0;
                        }
                    }
                } while ($row_albums = el_dbfetch($albums)); ?>
                <? if (mysqli_num_rows($albums) == 0) {
                    echo "</tr>";
                }
            } else {
                echo "&nbsp;";
            } ?>
    </table>


<? if ($atr > $maxRows_albums) {
    el_paging($apn, $currentPage, $queryString_albums, $totalPages_albums, $maxRows_albums, $atr, 'apn', 'atr');
}
//mysqli_free_result($albums);

}
?>




<? if (mysqli_num_rows($Recordset1) > 0) {

    if ($tr > $maxRows_Recordset1) {
        $queryString_Recordset1 .= '#img_list';
        el_paging($pn, $currentPage, $queryString_Recordset1, $totalPages_Recordset1, $maxRows_Recordset1, $tr);
    } ?>
    <? /*a href="/media/">&larr; К списку альбомов</a */ ?>
    <table class="gallery_tbl" border="0" align="center" cellpadding="0" cellspacing="4">
        <tr>
            <?php if (mysqli_num_rows($Recordset1) > 0) {
                $string = 0;
                do {
                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $row_Recordset1['smallpath'])) {
                        $bigSize = getimagesize($_SERVER['DOCUMENT_ROOT'] . $row_Recordset1['path']);
                        include $_SERVER['DOCUMENT_ROOT'] . '/tmpl/gallery/image.php';
                        $string++;
                        if ($string == $colNums) {
                            echo "</tr><tr>";
                            $string = 0;
                        }
                    }
                } while ($row_Recordset1 = el_dbfetch($Recordset1)); ?>
                <? if (mysqli_num_rows($Recordset1) == 0) {
                    echo "</tr>";
                }
            } else {
                echo "&nbsp;";
            } ?>
    </table>


    <? if ($tr > $maxRows_Recordset1) {
        el_paging($pn, $currentPage, $queryString_Recordset1, $totalPages_Recordset1, $maxRows_Recordset1, $tr); ?>
        <?php
    }
//mysqli_free_result($Recordset1);
}
?>
<p>&nbsp;</p>
<script type="text/javascript">
    $(document).ready(function (e) {
        $("a.fancy").fancybox();
    });
</script>