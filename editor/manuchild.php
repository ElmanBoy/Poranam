<?
require_once('../Connections/dbconn.php');
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
                    <div class="child" id="tr<?php echo $row_menuchild['id']; ?>"
                         onMouseOver="gc1('<?= $row_menuchild['id']; ?>')"
                         onMouseOut="gc1('<?= $row_menuchild['id']; ?>')" style="display:none">
                        <form method="POST" action="<?php echo $editFormAction; ?>"
                              name="edit<?= $row_menuchild['id']; ?>">
                            <div class="parent1"><img src="img/level_<?= $lev ?>.gif" border=0 align=middle>
                                <? if (el_child($parent1) != FALSE) { ?>
                                    <img src="img/plus.gif" title="Подразделы" id="im<?= $row_menuchild['id'] ?>"
                                         border=0 align=middle onClick="opentree('<?= $row_menuchild['id'] ?>')">
                                <? } ?>
                            </div>

                            <div class="parent2"><input name="id" type="hidden" id="id"
                                                        value="<?php echo $row_menuchild['id']; ?>">
                                <span <?= ($row_menuchild['menu'] != "Y") ? "style=\"color:#999999\"" : "" ?>
                                       title="Двойной клик - редактирование описания раздела"
                                       onDblClick="MM_openBrWindow('e_modules/catdescedit.php?id=<?php echo $row_menuchild['id']; ?>','newcat','scrollbars=yes,resizable=yes','500','200','true')"
                                       ><?php echo stripslashes($row_menuchild['name'])?></span>
                            </div>
                            <div class="parent3" align="center"
                                 onMouseDown="dragrow('<?php echo $row_menuchild['id']; ?>')"><input name="path"
                                                                                                     type="hidden"
                                                                                                     value="<?php echo $row_menuchild['path']; ?>">
                                <input name="sort" type="text" id="sort" value="<?php echo $row_menuchild['sort'] ?>"
                                       size="2">
                            </div>

                            <div class="parent4">
                                <table border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td width="35" align="center"><input name="imageField" type="image"
                                                                             onClick="MM_goToURL('self','editor.php?cat=<?php echo $row_menuchild['id']; ?>');return document.MM_returnValue"
                                                                             src="img/menu_edit.gif"
                                                                             alt="Редактировать содержимое раздела"
                                                                             border="0">
                                        </td>
                                        <td width="13%" align="center" class="lbr"><a
                                                    href="http://<? echo $SERVER_NAME . $row_menuchild['path']; ?>"
                                                    title="<? echo $_SERVER['SERVER_NAME'] . $row_menuchild['path']; ?>"
                                                    target="_blank"><img src="img/menu_view.gif" width="35" height="24"
                                                                         border="0" style="cursor:pointer;"></a>
                                        </td>
                                        <td width="13%" align="center" class="lbr"><input name="Submit" type="image"
                                                                                          id="submit"
                                                                                          src="img/menu_save.gif"
                                                                                          alt="Сохранить изменения"
                                                                                          border="0">
                                        </td>
                                    </tr>
                                </table>
                                <input name="action" type="hidden" id="action" value=""><input type="hidden"
                                                                                               name="MM_update"
                                                                                               value="edit">
                            </div>

                            <div class="parent5">
                                <div onClick="show_panel('<?= $row_menuchild['id'] ?>', '<?= $row_menuchild['name'] ?>', '<?= $row_menuchild['path'] ?>', '<?= $row_menuchild['menu'] ?>')"
                                     class="more">
                                    Дополнительно
                                </div>

                            </div>
                        </form>
                        <br><br>
                    </div>
                    <div style="display:none" id="ch<?= $row_menuchild['id'] ?>"></div>
                    <?
//menuadminchild($row_menuchild['id'],$table, $lev, $imenu);
                }
            } while ($row_menuchild = el_dbfetch($menuchild));
        }
    }

    menuadminchild($_GET['parent'], 'cat', $lev = 2, $imenu = 0);
}
?>