<?
require_once('../Connections/dbconn.php');;
$query_access1 = "SELECT * FROM userstatus";
$access1 = el_dbselect($query_access1, 0, $access1, 'result', true);
$row_access1 = el_dbfetch($access1);
$arreqlevel = array();
do {
    array_push($arreqlevel, $row_access1['id']);
} while ($row_access1 = el_dbfetch($access1));

$requiredUserLevel = $arreqlevel;
include($_SERVER['DOCUMENT_ROOT'] . "/editor/secure/secure.php");

if ($_GET['mode'] == 'child' && isset($_GET['parent'])) {
    ;

    function el_child($parent)
    {
        global $database_dbconn, $dbconn, $hid1;
        $child = mysqli_query($dbconn, "SELECT * FROM cat WHERE parent='$parent'");
        $allchil = mysqli_num_rows($child);
        if ($allchil > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }


    function menuadminchild($parent, $table, $lev, $imenu)
    {//Child Items
        global $database_dbconn;
        global $dbconn, $userLevel;
        global $SERVER_NAME;
        $querymenuchild = "SELECT * FROM cat WHERE parent='$parent' ORDER BY sort ASC";
        $menuchild = el_dbselect($querymenuchild, 0, $menuchild, 'result', true);
        $row_menuchild = el_dbfetch($menuchild);
        $idchild = $row_menuchild['id'];
        if ($idchild) {//if item is exist...
            $imenu++;
            ($imenu > 1) ? $lev++ : $lev = $lev;
            do {
                $parent1 = $row_menuchild['id'];
                if (strlen($row_menuchild['edit']) > 0) {
                    $araccess = explode(",", $row_menuchild['edit']);
                } else {
                    $araccess = array(0);
                }
                if (in_array($userLevel, $araccess) || $userLevel == "1") {
                    ?>
                    <li class="child" id="tr<?php echo $row_menuchild['id']; ?>">

                        <div class="parent1"><img src="img/level_<?= $lev ?>.gif" border=0 align=middle>
                            <? if (el_child($parent1) != FALSE) { ?>
                                <img src="img/plus.gif" title="Подразделы" id="im<?= $row_menuchild['id'] ?>" border=0
                                     align=middle
                                     onClick="opentree(<?= $row_menuchild['id'] ?>, <?= $_GET['lev'] + 1 ?>, 0)"
                                     style="cursor:pointer">
                            <? } ?>
                        </div>
                        <div class="parid">&nbsp;<?php echo $row_menuchild['id']; ?></div>
                        <div class="parent2"><input name="id[]" type="hidden" id="id"
                                                    value="<?php echo $row_menuchild['id']; ?>">
                            <span <?= ($row_menuchild['menu'] != "Y") ? "style=\"color:#999999\"" : "" ?>
                                   title="Двойной клик - редактирование описания раздела"
                                   onDblClick="MM_openBrWindow('e_modules/catdescedit.php?id=<?php echo $row_menuchild['id']; ?>','newcat','scrollbars=yes,resizable=yes','500','200','true')"
                            ><?=stripslashes($row_menuchild['name'])?></span>
                        </div>
                        <div class="parent3" align="center"><input name="path" type="hidden"
                                                                   value="<?php echo $row_menuchild['path']; ?>"> <input
                                    name="sort[]" type="text" id="sort" value="<?php echo $row_menuchild['sort'] ?>"
                                    size="2">
                        </div>

                        <div class="parent4">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" class="lbr"><a href="editor.php?cat=<?php echo $row_menuchild['id']; ?>" target="Main"
                                                                      title="Редактировать содержимое раздела">
                                            <i class="material-icons">edit</i></a>
                                    </td>
                                    <td align="center" class="lbr">
                                        <a href="http://<? echo $_SERVER['SERVER_NAME'] . $row_menuchild['path']; ?>"
                                           title="<? echo $_SERVER['SERVER_NAME'] . $row_menuchild['path']; ?>"
                                           target="_blank"><i class="material-icons">screen_share</i></a>
                                    </td>
                                    <td align="center" class="lbr">
                                        <a href="javascript:void(0)" title="Добавить подраздел">
                                            <i onClick="MM_openBrWindow('newcategory.php?parentid=<?= $row_menuchild['id'] ?>','newcat',
                                                    'scrollbars=no,resizable=yes',
                                                    '720','450','true') " class="material-icons">playlist_add</i>
                                        </a>
                                    </td>
                                    <td align="center" class="lbr">
                                        <a href="javascript:void(0)" title="Перенести в другой раздел">
                                            <i class="material-icons" onClick="MM_openBrWindow('menumigrate.php?id=<?= $row_menuchild['id'] ?>','newcat',
                                                    'scrollbars=yes,resizable=yes','400','600','true')">low_priority</i>
                                        </a>
                                    </td>

                                    <td align="center" class="lbr">
                                        <a href="javascript:void(0)" title="Свойства раздела">
                                            <i onClick="MM_openBrWindow('metainfo.php?id=<?= $row_menuchild['id'] ?>&lev=1','metainfo','scrollbars=yes,resizable=yes',
                                                    '650','700','true')" class="material-icons">settings_applications</i>
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

                    </li>

                    <?

                }
            } while ($row_menuchild = el_dbfetch($menuchild));
        }
    }

    if (ob_get_length()) ob_clean();
    header("Content-type: text/html; charset=utf-8");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    echo '<br><ol class="menuChildList">';
    menuadminchild($_GET['parent'], 'cat', $_GET['lev'], $imenu = 0);
    echo '</ol>';
}
?>