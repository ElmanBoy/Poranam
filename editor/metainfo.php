<?php
session_start();
require_once('../Connections/dbconn.php');
$requiredUserLevel = array(0, 1, 2);
include($_SERVER['DOCUMENT_ROOT'] . "/editor/secure/secure.php");

$site_id = (intval($_GET['site_id']) > 0) ? intval($_GET['site_id']) : $_SESSION['site_id'];
$id = intval($_GET['id']);
$catid = el_dbselect("SELECT parent, cat_id FROM cat WHERE id = $id", 0, $catid, 'row', true);
$cat_id = intval($catid['cat_id']);
$parentCat = intval($catid['parent']);
$err = 0;

if ($_SESSION['user_level'] > 1 && $site_id != $_SESSION['user_group']) {
    echo '<h4 style="color:red">У Вас недостаточно прав для работы с этим сайтом</h4>';
    exit();
}

function changePath ( $idcat, $level, $new_dirname )
{
    $exPath = el_dbselect("SELECT COUNT(id) AS exist FROM cat WHERE site_id = " . intval($_SESSION['site_id']) . " AND path='$new_dirname'", 0, $exPath, 'row', true);
    if ($exPath['exist'] == 0) {
        $exCat = el_dbselect("SELECT id, path FROM cat WHERE parent='" . $idcat . "' AND site_id = " . intval($_SESSION['site_id']), 0, $exCat);
        if (mysqli_num_rows($exCat) > 0) {
            $rex = el_dbfetch($exCat);
            do {
                $dirArr = array();
                $new_path = '';
                $dirArr = explode('/', $rex['path']);
                $dirArr[$level] = str_replace('/', '', $new_dirname);
                $new_path = '/' . implode('/', $dirArr);
                $ch = el_dbselect("SELECT id FROM cat WHERE parent='" . $rex['id'] . "' AND site_id = " . intval($_SESSION['site_id']), 0, $ch);
                if (mysqli_num_rows($ch) > 0) {
                    changePath($rex['id'], $level, $new_dirname);
                }
                el_dbselect("UPDATE cat SET path='" . $new_path . "' WHERE id='" . $rex['id'] . "' AND site_id = " . intval($_SESSION['site_id']), 0, $pa);
                el_dbselect("UPDATE content SET path='" . $new_path . "' WHERE cat='" . $rex['id'] . "' AND site_id = " . intval($_SESSION['site_id']), 0, $pa);
            } while ($rex = el_dbfetch($exCat));
        }
    } else {
        echo '<script>alert("Раздел с папкой \"' . $new_dirname . '\" уже существует.")</script>';
        return false;
    }
    el_clearcache('menu');
    return true;
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "edit")) {

    if ($_POST['kod'] == '') {
        el_2ini('cache' . $_POST['id'], 'Y');
    } else {
        el_2ini('cache' . $_POST['id'], 'N');
    }

    $colname_db_content = "1";
    if (isset($_GET['id'])) {
        $colname_db_content = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
    }

    $query_db_content = sprintf("SELECT * FROM content WHERE cat = %s", $cat_id);
    $db_content = el_dbselect($query_db_content, 0, $db_content, 'result', true);
    $row_db_content = el_dbfetch($db_content);

    if (isset($_POST['edit'])) {
        if (count($_POST['edit']) > 0) {
            $editf = '0,'.implode(",", $_POST['edit']);
        } else {
            $editf = '0,'.$_POST['edit'];
        }
    } else {
        $editf = null;
    }
    if (isset($_POST['view'])) {
        if (count($_POST['view']) > 0) {
            $viewf = '0,'.implode(",", $_POST['view']);
        } else {
            $viewf = '0,'.$_POST['view'];
        }
    } else {
        $viewf = null;
    }


    $new_dirname = str_replace(strrchr($row_db_content['path'], "/"), "", $row_db_content['path']) . "/" . str_replace("/", "", $_POST['path']);
    $_POST['path'] = ($_POST['path'] == '/') ? '' : $_POST['path'];
    $new_dirname = ($new_dirname == '/') ? '' : $new_dirname;
    if ($row_db_content['path'] != $new_dirname) {
        if (!changePath($_GET['id'], $_GET['lev'], $_POST['path'])) {
            $err++;
        }
    }

    if ($err == 0) {
        $updateSQL = sprintf("UPDATE content SET path=%s, title=%s, description=%s, keywords=%s, caption=%s, kod=%s, template=%s, edit=%s, view=%s 
    WHERE cat=%s AND site_id=%s",
            GetSQLValueString($new_dirname, "text"),
            GetSQLValueString(addslashes($_POST['title']), "text"),
            GetSQLValueString(addslashes($_POST['description']), "text"),
            GetSQLValueString(addslashes($_POST['keywords']), "text"),
            GetSQLValueString(addslashes($_POST['caption']), "text"),
            GetSQLValueString($_POST['kod'], "text"),
            GetSQLValueString($_POST['template'], "text"),
            GetSQLValueString($editf, "text"),
            GetSQLValueString($viewf, "text"),
            GetSQLValueString($cat_id, "int"),
            $site_id);

        $Result1 = el_dbselect($updateSQL, 0, $Result1, 'result', true);
        if ($_POST['menu'] != "Y") {
            $_POST['menu'] = "N";
        } else {
            $_POST['menu'] = "Y";
        }
        $_POST['redirect'] = (strlen($_POST['redirect_out']) > 0) ? $_POST['redirect_out'] : $_POST['redirect'];
        $updateSQL1 = sprintf("UPDATE cat SET path=%s, name=%s, sort=%s, menu=%s, nourl=%s, edit=%s, view=%s, 
        `left`=%s, `bottom`=%s, `redirect`=%s, cat_type=%s 
            WHERE id=%s AND site_id=%s",
            GetSQLValueString($new_dirname, "text"),
            GetSQLValueString($_POST['name'], "text"),
            GetSQLValueString($_POST['sort'], "int"),
            GetSQLValueString($_POST['menu'], "text"),
            GetSQLValueString($_POST['nourl'], "text"),
            GetSQLValueString($editf, "text"),
            GetSQLValueString($viewf, "text"),
            GetSQLValueString($_POST['left'], "text"),
            GetSQLValueString(implode(',', $_POST['bottom']), "text"),
            GetSQLValueString($_POST['redirect'], "text"),
            GetSQLValueString($_POST['cat_type'], "int"),
            GetSQLValueString($id, "int"),
            $site_id);

        $Result2 = el_dbselect($updateSQL1, 0, $Result2, 'result', true);
        el_clearcache();
    }
    echo "<script language=javascript>
  alert('Внесенные изменения сохранены!');
  </script>";
    el_genSiteMap();
    if (substr_count($_POST['kod'], 'catalog') > 0) {
        $catEx = el_dbselect("SELECT id FROM catalogs WHERE catalog_id='" . str_replace('catalog', '', $_POST['kod']) . "'", 0, $catEx, 'row');
        if (strlen($catEx['id']) > 0) {
            el_dbselect("UPDATE catalogs SET cat='" . $_POST['id'] . "' WHERE id=" . $catEx['id'], 0, $res);
        }
    }
}

$colname_db_content = "1";
if (isset($_GET['id'])) {
    $colname_db_content = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
};
$query_db_content = sprintf("SELECT * FROM content WHERE cat = %s AND site_id = %s", $cat_id, $site_id);
$db_content = el_dbselect($query_db_content, 0, $db_content, 'result', true);
$row_db_content = el_dbfetch($db_content);

$query_db_contentfirst = "SELECT keywords FROM content WHERE cat = 1 AND site_id = " . $site_id;
$db_contentfirst = el_dbselect($query_db_contentfirst, 0, $db_contentfirst, 'result', true);
$row_db_contentfirst = el_dbfetch($db_contentfirst);

$page_url = "http://" . $_SERVER['SERVER_NAME'] . $row_db_content['path'];
$page_name = $row_db_content['caption'];

$modulesSubquery = '';
if ($_SESSION['user_level'] > 1) {
    $modulesSubquery = " WHERE is_register = 0";
}
$query_modules = "SELECT * FROM modules $modulesSubquery ORDER BY sort ASC";
$modules = el_dbselect($query_modules, 0, $modules, 'result', true);
$row_modules = el_dbfetch($modules);

$query_tmpl = "SELECT * FROM template WHERE `master`<>1";
$tmpl = el_dbselect($query_tmpl, 0, $tmpl, 'result', true);
$row_tmpl = el_dbfetch($tmpl);

$query_users = "SELECT * FROM userstatus WHERE level > 0";
$users = el_dbselect($query_users, 0, $users, 'result', true);
$row_users = el_dbfetch($users);

$totalRows_users = mysqli_num_rows($users);;
$views = el_dbselect($query_users, 0, $views, 'result', true);
$row_views = el_dbfetch($views);

$query_menu = "SELECT * FROM cat WHERE id='" . $id . "'";
$menu = el_dbselect($query_menu, 0, $menu, 'result', true);
$row_menu = el_dbfetch($menu);

?>
<html>
<head>
    <title>Свойства раздела "<?= $row_db_content['caption']; ?>"</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script type="text/javascript" src="/js/jquery.js"></script>
    <style type="text/css">
        <!--
        body {
            background-color: #FFFFFF;
        }

        -->
    </style>
    <link href="style.css" rel="stylesheet" type="text/css">
    <script language="javascript">
        function openAcc(d) {
            var layer = document.getElementById("accs");
            if (d == 0) {
                layer.style.display = "none";
                for (i = 0; i < layer.children.length; i++) {
                    if (layer.children[i].tagName == "INPUT") {
                        layer.children[i].checked = false;
                    }
                }
            } else if (d == 1) {
                layer.style.display = "block";
            }
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
        $(document).ready(function (e) {
            if($("input[name='path']").is("input")) {
                $("input[name='name']").keyup(function (e) {
                    $("input[name='path']").val($(this).val().translit());
                    $("input[name='title'], input[name='caption']").val($(this).val());
                });
            }
        });
    </script>

</head>

<body>
<form method="POST" action="<?php echo $editFormAction; ?>" name="edit">
    <table width="100%" border="0" cellspacing="0" cellpadding="5" class="el_tbl">
        <?
        if($row_db_content['path'] != '') {
            ?>
            <tr valign="top">
                <td align="right">Адрес раздела:</td>
                <td colspan="2"><a href="<?= $page_url ?>"
                                   target="_blank"><?= $_SERVER['SERVER_NAME'] . str_replace(strrchr($row_db_content['path'], "/"), "", $row_db_content['path']) ?></a><input
                            name="path" type="text" id="path" value="<?php echo strrchr($row_db_content['path'], "/"); ?>">
                    <input name="id" type="hidden" id="id" value="<?php echo $row_db_content['cat']; ?>">
                </td>
            </tr>
            <?
        }else{

        }
        ?>
        <tr>
            <td align="right">Название раздела :</td>
            <td><input name="name" type="text" id="name"
                       value="<?php echo str_replace('\"', '``', $row_menu['name']); ?>" size="50"></td>
        </tr>
        <tr valign="top">
            <td align="right">Заголовок окна страницы(&lt;title&gt;):</td>
            <td colspan="2"><input name="title" type="text" id="title"
                                   value="<?php echo str_replace('\"', '``', $row_db_content['title']); ?>" size="50">
            </td>
        </tr>
        <tr valign="top">
            <td align="right">Заголовок над текстом:</td>
            <td colspan="2"><input name="caption" type="text" id="caption"
                                   value="<?php echo str_replace('\"', '``', $row_db_content['caption']); ?>" size="50">
            </td>
        </tr>

        <tr valign="top">
            <td align="right">Описание страницы(&lt;description&gt;):</td>
            <td colspan="2"><textarea name="description" cols="40" rows="2"
                                      id="description"><?php echo str_replace('\"', '``', $row_db_content['description']); ?></textarea>
            </td>
        </tr>
        <tr valign="top">
            <td align="right">Ключевые слова через запятую(&lt;keywords&gt;): <br></td>
            <td colspan="2"><textarea name="keywords" cols="40" rows="5"
                                      id="keywords"><?php if (strlen($row_db_content['keywords']) < 1) {
                        echo htmlspecialchars($row_db_contentfirst['keywords']);
                    } else {
                        echo htmlspecialchars($row_db_content['keywords']);
                    } ?></textarea></td>
        </tr>
        <?
        if ($_SESSION['user_level'] < 2) {
        ?>
        <tr valign="top">
            <td align="right">Используемый модуль:</td>
            <td colspan="2"><select name="kod" id="kod">
                    <?php
                    do {
                        if ($row_modules['status'] == "Y") {
                            ?>
                            <option
                                    value="<?php echo $row_modules['type'] ?>" <? if ($row_db_content['kod'] == $row_modules['type']) {
                                echo "selected";
                                $mname = $row_modules['name'];
                            } ?>><?php echo $row_modules['name'] ?></option>
                        <?php }
                    } while ($row_modules = el_dbfetch($modules));
                    $rows = mysqli_num_rows($modules);
                    if ($rows > 0) {
                        mysqli_data_seek($modules, 0);
                        $row_modules = el_dbfetch($modules);
                    }
                    ?>
                </select></td>
        </tr>
        <tr valign="top">
            <td align="right">Шаблон дизайна:</td>
            <td colspan="2"><select name="template" id="template">
                    <?php
                    do {
                        ?>
                        <option
                                value="<?php echo $row_tmpl['path'] ?>" <? if ($row_db_content['template'] == $row_tmpl['path']) {
                            echo "selected";
                        } ?>><?php echo $row_tmpl['name'] ?></option>
                        <?php
                    } while ($row_tmpl = el_dbfetch($tmpl));
                    $rows = mysqli_num_rows($tmpl);
                    if ($rows > 0) {
                        mysqli_data_seek($tmpl, 0);
                        $row_tmpl = el_dbfetch($tmpl);
                    }
                    ?>
                </select></td>
        </tr>
        <tr>
            <td align="right">Порядковый номер в меню:</td>
            <td><input name="sort" type="text" id="sort"
                       value="<?php echo str_replace('\"', '``', $row_menu['sort']); ?>" size="5"></td>
        </tr>
        <tr>
            <td align="right">Показывать в меню:</td>
            <td><input <?= ($row_menu['menu'] == 'Y') ? "checked" : "" ?> name="menu" type="checkbox" id="menu"
                                                                          value="Y"></td>
        </tr>
        <? /*tr>
            <td align="right">Показывать в "подвале" сайта:</td>
            <td><input <?= ($row_menu['bottom'] == '1') ? "checked" : "" ?> name="menu" type="checkbox" id="menu"
                                                                          value="1"></td>
        </tr*/ ?>
        <tr>
            <td align="right">Открывать раздел в новом окне:</td>
            <td><input <?= ($row_menu['left'] == 'Y') ? "checked" : "" ?> name="left" type="checkbox" id="left"
                                                                          value="Y"></td>
        </tr>
        <tr>
            <td align="right">Раздел не участвует в построение URL внутренних страниц</td>
            <td><input <?= ($row_menu['nourl'] == '1') ? "checked" : "" ?> name="nourl" type="checkbox" id="menu"
                                                                           value="1"></td>
        </tr>
        <tr>
        <tr>
            <td align="right" valign="top">Редирект на:</td>
            <td><input name="redirect_out" type="text" id="redirect_out" value="<?= $row_menu['redirect'] ?>" size="50">
                <br>

                или на свой раздел:
                <select name="redirect" style="max-width: 200px;">
                    <option></option>
                    <? el_pageSelect('', $row_menu['redirect']) ?>
                </select></td>
        </tr>
            <tr>
                <td align="right" valign="top">Тип раздела</td>
                <td>
                    <label>
                        <input <?= ($row_menu['cat_type'] == '1' || $row_menu['cat_type'] == '0') ? "checked" : "" ?> name="cat_type" type="radio" value="1">
                        Раздел сайта
                    </label>
                    <br>
                    <label>
                        <input <?= ($row_menu['cat_type'] == '2') ? "checked" : "" ?> name="cat_type" type="radio" value="2">
                        Общая информация
                    </label>
                    <br>
                    <label>
                        <input <?= ($row_menu['cat_type'] == '3') ? "checked" : "" ?> name="cat_type" type="radio" value="3">
                        Справочник
                    </label>

                </td>
            </tr>
            <?
            if($parentCat == 47){
                ?>
                  <tr>
                      <td align="right" valign="top">Категория галереи</td>
                      <td>
                          <select name="bottom[]" multiple>
                              <?
                              $pt = el_dbselect("SELECT id, field1 FROM catalog_ptype_data WHERE active = 1", 0, $pt, 'result', true);
                              $rpt = el_dbfetch($pt);
                              $cArr = explode(",", $row_menu['bottom']);
                              do{
                                  $sel = (in_array($rpt['id'], $cArr)) ? ' selected="selected"' : '';
                                  echo '<option value="'.$rpt['id'].'"'.$sel.'>'.$rpt['field1'].'</option>'."\n";
                              }while($rpt = el_dbfetch($pt));
                              ?>
                          </select>
                      </td>
                  </tr>
                <?
            }else{
                echo '<input type="hidden" name="bottom[]" value="">';
            }
            ?>
            <?
        }
        if ($_SESSION['user_level'] < 2) { ?>
            <tr valign="top">
                <td align="right">Группы, которым разрешен доступ на редактирование этого раздела:</td>
                <td colspan="2">
                    <?
                    $aredit = explode(",", $row_db_content['edit']);
                    do { ?>
                        <input name="edit[<?= $row_users['id'] ?>]" type="checkbox" id="edit[<?= $row_users['id'] ?>]"
                               value="<?= $row_users['id'] ?>" <?= (in_array($row_users['id'], $aredit)) ? "checked" : "" ?>> <?= $row_users['name'] ?>
                        <br>
                    <? } while ($row_users = el_dbfetch($users)); ?>    </td>
            </tr>
            <tr valign="top">
                <td align="right">Группы, которым разрешен доступ на просмотр этого раздела:<br>
                    (если не отмечена ни одна группа, то доступ будет только у администратора)
                </td>
                <td colspan="2">
                    <label>
                        <input type="radio" name="access" value="0"
                               onClick="openAcc(0)" <?= (strlen($row_db_content['view']) < 1) ? 'checked="checked"' : '' ?>>
                        Общий доступ</label>
                    <br>
                    <label>
                        <input type="radio" name="access" value="1"
                               onClick="openAcc(1)" <?= (strlen($row_db_content['view']) > 0) ? 'checked="checked"' : '' ?>>
                        Ограниченный доступ</label>
                    <div style="display:<?= (strlen($row_db_content['view']) > 0) ? 'block' : 'none' ?>" id="accs">
                        <?
                        $arview = explode(",", $row_db_content['view']);
                        do { ?>
                            <input name="view[<?= $row_views['id'] ?>]" type="checkbox"
                                   id="view[<?= $row_views['id'] ?>]"
                                   value="<?= $row_views['id'] ?>" <?= (in_array($row_views['id'], $arview)) ? "checked" : "" ?>> <?= $row_views['name'] ?>
                            <br>
                        <? } while ($row_views = el_dbfetch($views)); ?>
                    </div>
                </td>
            </tr>  <? } ?>
        <tr valign="top">
            <td colspan="3" align="center"><input name="Submit" type="submit" class="but agree" value="Сохранить">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input name="Submit2" type="button" class="but close" onClick="top.closeDialog()" value="Закрыть"></td>
        </tr>
    </table>
    <input type="hidden" name="MM_update" value="edit">
</form>
</body>
</html>

