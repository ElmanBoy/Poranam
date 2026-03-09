<?php
require_once('../../../Connections/dbconn.php');

//error_reporting(E_ALL);
$requiredUserLevel = array(0, 1, 2);
include($_SERVER['DOCUMENT_ROOT'] . "/editor/secure/secure.php");

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
	$editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

function getChildrenCats($catalog_id, $parentId, $level, $cat){
    $curr = el_dbselect("SELECT content.cat AS cat, cat.name AS title
	   FROM content, cat WHERE content.cat=cat.id AND content.kod='catalog" . $catalog_id . "' 
	   AND cat.parent=" . intval($parentId), 0, $curr, 'result', true);
    $currCat = el_dbfetch($curr);

    if (substr_count($_POST['checked'], ' , ') > 0) {
        $good_cats = explode(' , ', $cat);
        while (list($key, $val) = each($good_cats)) {
            $good_cats[$key] = trim($val);
        }
    }
    $cats = array();
    ?>
    <div id="child<?= intval($parentId) ?>" style="margin-left:<?= (15 * $level) ?>px; display:none">
        <? do {
            if (is_array($good_cats)) {
                $sel = (in_array($cat, $good_cats, false)) ? ' checked' : '';
            } else {
                $sel = (trim($cat) == $currCat['cat']) ? ' checked' : '';
            }
            $child = el_dbselect("SELECT COUNT(id) AS count FROM cat WHERE parent=" . $currCat['cat'], 0, $child, 'row', true);
            if ($child['count'] > 0) {
                ?>
                <img src="/editor/img/plus.gif" class="icon_plus" id="toggle<?= $currCat['cat'] ?>"
                     onClick="showChildCat(this, <?= ($_POST['level'] + 1) ?>, <?= $currCat['cat'] ?>)">
            <? } ?>
            <label for="catCh<?= $currCat['cat'] ?>" id="cat<?= $currCat['cat'] ?>">
                <input type="checkbox" name="cats[]" id="catCh<?= $currCat['cat'] ?>" value="<?= $currCat['cat'] ?>"<?= $sel ?>> <?= $currCat['title'] ?>
            </label><br>
            <?
            $cats[] = $currCat['cat'];
        } while ($currCat = el_dbfetch($curr));
        ?>
    </div>
    <?
    if (isset($_POST['expand'])) {
        $expArr = explode(',', $_POST['expand']);
        echo '<script type="text/javascript">' . "\n";
        for ($i = 0; $i < count($expArr); $i++) {
            if (in_array($expArr[$i], $cats)) {
                echo '$("#toggle' . $expArr[$i] . '").click();' . "\n";
            }
        }
        echo '</script>';
    }
}

//el_dbselect("SET NAMES 'utf8'", 0, $res, 'result');
//el_dbselect("SET character_set_server='utf8'", 0, $res, 'result');

$title_catalog = el_dbselect("SELECT field FROM catalog_prop WHERE title='1' AND catalog_id='" . $_GET['catalog_id'] . "'", 0, $title_catalog, 'row');

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "edititem")) {

// Смотрим структуру каталога

	$query_cat_form = "SELECT * FROM catalog_prop WHERE catalog_id='" . $_GET['catalog_id'] . "' ORDER BY sort";
	$cat_form = el_dbselect($query_cat_form, 0, $cat_form, 'result', true);
	$row_cat_form = el_dbfetch($cat_form);
	$totalRows_cat_form = el_dbnumrows($cat_form);

    $cat_form1 = '';
	$query_cat_form1 = "SELECT * FROM catalogs WHERE catalog_id='" . $_GET['catalog_id'] . "'";
	$cat_form1 = el_dbselect($query_cat_form1, 0, $cat_form1, 'result', true);
	$row_cat_form1 = el_dbfetch($cat_form1);

	$download_path = str_replace('//', '/', $row_cat_form1['down_files']);
	if (strlen($download_path) > 0) {
		if (!is_dir($download_path)) {
			mkdir($_SERVER['DOCUMENT_ROOT'] . $download_path, 0777);
		}
	} else {
		$download_path = '/files';
	}

	$d = el_dbselect("SELECT * FROM catalog_" . $_GET['catalog_id'] . "_data 
	WHERE id = ".intval($_GET['id']), 0, $detail, 'row', true);

	$query_field = "";
	$fflag = 1;
    $temp_name = $_POST['goodid'] . '_' . el_genpass(5) . '_';
	do {
		$field_number = "field" . $row_cat_form['field'];

		switch ($row_cat_form['type']) {
			case "textarea":
				$type = "LONGTEXT";
				break;
			case "checkbox":
			case "radio":
				$type = "TEXT";
				break;
			case "optionlist":
			case "option":
			case "select":
			case "depend_list":
			case "list_fromdb":
				$type = "LONGTEXT";
				break;
			case "propTable":
				$type = "LONGTEXT";
				$row_cat_form = el_dbselect("SELECT listdb FROM catalog_prop WHERE catalog_id='" . $_GET['catalog_id'] . "' AND field='" . $row_cat_form['field'] . "' 
			ORDER BY sort", 0, $row_cat_form, 'row', true);
                $list_field = '';
				$list_field = el_dbselect("select id from catalog_" . $row_cat_form['listdb'] . "_data ORDER BY sort ASC", 0, $list_field, 'result', true);
				$row_list_field = @el_dbfetch($list_field);
				$itemlist = array();
				do {
					$itemlist[$row_list_field['id']] = $_POST[$field_number . '_' . $row_list_field['id']];
				} while ($row_list_field = @el_dbfetch($list_field));
				echo $_POST[$field_number] = json_encode($itemlist);

				break;
			case "calendar":
				$type = "DATE";
				break;
			case "price":
				$type = "DOUBLE";
				$_POST[$field_number] = trim(str_replace(" ", "", $_POST[$field_number]));
				$_POST[$field_number] = str_replace(",", ".", $_POST[$field_number]);
				$_POST[$field_number] = sprintf("%01.2f", $_POST[$field_number]);
				break;
			case "small_image":
				$type = "TEXT";
				if (!empty($_FILES[$field_number]['name'])) {
					if (el_resize_images($_FILES[$field_number]['tmp_name'],
						el_translit($_FILES[$field_number]['name']), $row_cat_form1['small_size'], $row_cat_form1['small_size'], $temp_name . 'small_')) {
						$_POST[$field_number] = "/images/" . $_SESSION['site_id'].'/'.el_translit($temp_name . 'small_' . $_FILES[$field_number]['name']);
					} else {
						echo "<script>alert('Файл для предпросмотра с названием \"" . $_FILES[$field_number]['name'] . "\" не удалось закачать!')</script>";
					}
				} else {
					$_POST[$field_number] = $_POST[$field_number . "hidden"];
				}
				break;
			case "big_image":
				$type = "TEXT";
                if (!empty($_FILES[$field_number]['name'])) {

                    $tempDir = $_SERVER['DOCUMENT_ROOT'] . '/images/temporary/';
                    $targetFileName = el_translit($temp_name . '_' . $_FILES[$field_number]['name']);
                    if (!is_dir($tempDir)) mkdir($tempDir, 0777);
                    if(move_uploaded_file($_FILES[$field_number]['tmp_name'], $tempDir . $targetFileName)) {
echo $row_cat_form1['big_size'].'/'.$row_cat_form1['big_size_h'];
                        if (el_resize_images($tempDir . $targetFileName, $targetFileName,
                            $row_cat_form1['big_size'], $row_cat_form1['big_size_h'], ''))
                        {
                            $_POST[$field_number] = "/images/" . $_SESSION['site_id'] . '/' . $targetFileName;
                            el_imageLogo($_POST[$field_number], '/images/copyright.png', 'center');
                            unlink($tempDir.$targetFileName);
                        } else {
                            echo "<script>alert('Файл с названием \"" . $_FILES[$field_number]['name'] . "\" не удалось закачать!')</script>";
                        }
                    }else{
                        echo "<script>alert('Файл с названием \"" . $_FILES[$field_number]['name'] . "\" не удалось закачать')</script>";
                    }
                }else{
                    $_POST[$field_number] = $_POST[$field_number . "hidden"];
                }

				break;
			case "file":
				$type = "TEXT";
				if (strlen($_FILES[$field_number]['name']) > 0) {
                    if(!is_dir($_SERVER['DOCUMENT_ROOT'] . "/files/".$_SESSION['site_id'])){
                        mkdir($_SERVER['DOCUMENT_ROOT'] . "/files/".$_SESSION['site_id'], 0777);
                    }
					if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/files/" .$_SESSION['site_id'].'/'. $temp_name.'_'.$_FILES[$field_number]['name'])) {
						echo "<script>alert('Файл с названием \"" . $_FILES[$field_number]['name'] . "\" уже есть!')</script>";
						$fflag = 0;
					} else {
						if (!move_uploaded_file($_FILES[$field_number]['tmp_name'],
                            $_SERVER['DOCUMENT_ROOT'] . "/files/" .$_SESSION['site_id'].'/'. $temp_name.'_'. $_FILES[$field_number]['name'])) {
							echo "<script>alert('Не удалось закачать файл \"" . $_FILES[$field_number]['name'] . "\"!\\nВозможно, не настроен доступ к папке \"files\".')</script>";
							$fflag = 0;
						} else {
							$_POST[$field_number] = "/files/" .$_SESSION['site_id'].'/'. $temp_name.'_'. $_FILES[$field_number]['name'];
						}
					}
				} else {
					$_POST[$field_number] = $_POST[$field_number . "f"];
				}
				break;
			case "password":
				$_POST[$field_number] = ($d['field3'] != $_POST[$field_number])?str_replace("$1$", "", crypt(md5($_POST[$field_number]), '$1$')) : $d['field3'];
				break;
			case "hidden_file":
				chmod($_SERVER['DOCUMENT_ROOT'] . "/files", 0777);
				chmod($_SERVER['DOCUMENT_ROOT'] . "/files/secure", 0777);
				if (!is_dir($_SERVER['DOCUMENT_ROOT'] . "/files/secure")) {
					mkdir($_SERVER['DOCUMENT_ROOT'] . "/files/secure", 0777);
					copy($_SERVER['DOCUMENT_ROOT'] . "/modules/.htaccess", $_SERVER['DOCUMENT_ROOT'] . "/files/secure/.htaccess");
				}
				if (strlen($_FILES[$field_number]['name']) > 0) {
					if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/files/secure/" . $_FILES[$field_number]['name'])) {
						echo "<script>alert('Файл с названием \"" . $_FILES[$field_number]['name'] . "\" уже есть!')</script>";
					} else {
						if (!move_uploaded_file($_FILES[$field_number]['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . "/files/secure/" . $_FILES[$field_number]['name'])) {
							echo "<script>alert('Не удалось закачать файл " . $_FILES[$field_number]['name'] . "!')</script>";
							$fflag = 0;
						} else {
							$_POST[$field_number] = $_FILES[$field_number]['name'];
						}
					}
				} else {
					$_POST[$field_number] = $_POST[$field_number . "f"];
				}
				chmod($_SERVER['DOCUMENT_ROOT'] . "/files/", 0755);
				chmod($_SERVER['DOCUMENT_ROOT'] . "/files/secure", 0755);
				$type = "TEXT";
				break;

			case "secure_file":
				function secure_file_upload()
				{
					global $_POST, $_FILES, $type, $field_number, $fflag;
					chmod($_SERVER['DOCUMENT_ROOT'] . "/files", 0777);
					chmod($_SERVER['DOCUMENT_ROOT'] . "/files/secure", 0777);
					$file_ext = strrchr($_FILES[$field_number]['name'], '.');
					$newfilename = el_genpass(20) . $file_ext;
					if (!is_dir($_SERVER['DOCUMENT_ROOT'] . "/files/secure")) {
						mkdir($_SERVER['DOCUMENT_ROOT'] . "/files/secure", 0777);
						copy($_SERVER['DOCUMENT_ROOT'] . "/modules/.htaccess", $_SERVER['DOCUMENT_ROOT'] . "/files/secure/.htaccess");
					}
					if (strlen($_FILES[$field_number]['name']) > 0) {
						if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/files/secure/" . $newfilename)) {
							secure_file_upload();
						} else {
							if (!move_uploaded_file($_FILES[$field_number]['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . "/files/secure/" . $newfilename)) {
								echo "<script>alert('Не удалось закачать файл " . $_FILES[$field_number]['name'] . "!')</script>";
								$fflag = 0;
							} else {
								$_POST[$field_number] = $newfilename;
							}
						}
					} else {
						$_POST[$field_number] = $_POST[$field_number . "f"];
					}
					return $_POST[$field_number];
					chmod($_SERVER['DOCUMENT_ROOT'] . "/files/", 0755);
					chmod($_SERVER['DOCUMENT_ROOT'] . "/files/secure", 0755);
				}

				$type = "TEXT";
				$_POST[$field_number] = secure_file_upload();
				break;

			default:
				$type = "TEXT";
				break;
		}
		//echo $row_cat_form['type'].'|'.$field_number.'|'.$_POST[$field_number].'<br>';
		if ($row_cat_form['type'] == 'optionlist' || $row_cat_form['type'] == 'list_fromdb' || $row_cat_form['type'] == 'option'
			|| $row_cat_form['type'] == 'depend_list'
		) {
			$arop = (is_array($_POST[$field_number]) && count($_POST[$field_number]) > 0) ? @implode(',',
				$_POST[$field_number]) : $_POST[$field_number];
			$post_field = "'" . GetSQLValueString($arop, $type) . "', ";
			$query_field .= $field_number . "=" . $post_field;
		} else {
			$post_field = "'" . GetSQLValueString($_POST[$field_number], $type) . "', ";
			$query_field .= $field_number . "=" . $post_field;
		}

	} while ($row_cat_form = el_dbfetch($cat_form));

	$cat_post = (count($_POST['cats']) > 0) ? ' ' . implode(' , ', $_POST['cats']) . ' ' : intval($_POST['cats']);
	$active_post = GetSQLValueString(isset($_POST['active']) ? "true" : "", "defined", "'1'", "'0'");
	$sort_post = GetSQLValueString($_POST['sort'], "int");
	$goodid_post = GetSQLValueString($_POST['goodid'], "int");

	//Мета теги///
	if ($row_cat_form1['meta'] > 0) {
		$post_meta = "title='" . $_POST['title'] . "', description='" . $_POST['description'] . "',  keywords='" . $_POST['keywords'] . "', ";
	}
	//////////////

	$path = (strlen(trim($_POST['path'])) == 0) ?
		el_translit($_POST['field' . $title_catalog['field']], 'path') . ((strlen(trim($_POST['goodid'])) > 0) ? '-' . $_POST['goodid'] : '')
		: el_translit($_POST['path'], 'path');

	if ($fflag != 0) {
		$updateSQL = "UPDATE catalog_" . $_GET['catalog_id'] . "_data SET cat='" . $cat_post . "', " . $query_field . " " . $post_meta . " active=" .
            $active_post . ", sort='" . $sort_post . "', goodid='" . $goodid_post . "', path='$path'  WHERE id=" . $_GET['id']." AND site_id=".intval($_SESSION['site_id']);

		$Result1 = el_dbselect($updateSQL, 0, $Result1, 'result', true);


		$page_name = el_dbselect("SELECT name FROM cat WHERE path='" . $_GET['path'] . "' AND site_id=".intval($_SESSION['site_id']), 0, $page_name, 'row');
		el_log('Изменение записи &laquo;' . $_POST['field1'] . '&raquo; в каталоге в разделе &laquo;' . $page_name['name'] . '&raquo;', 2);
		el_clearcache('catalogs');
		el_clearcache('tags');
		el_genSiteMap();
		echo "<script>alert('Изменения сохранены!')</script>";
	}
}

$colname_detail = "-1";
if (isset($_GET['id'])) {
	$colname_detail = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}

$query_detail = sprintf("SELECT * FROM catalog_" . $_GET['catalog_id'] . "_data WHERE id = %s AND site_id=".intval($_SESSION['site_id']), $colname_detail);
$detail = el_dbselect($query_detail, 0, $detail, 'result', true);
$row_detail = el_dbfetch($detail);
$totalRows_detail = el_dbnumrows($detail);

$uPars = explode('&', $_SERVER['QUERY_STRING']);
$currId = array_shift($uPars);
$urlParams = htmlentities(implode('&', $uPars));
$prevId = el_dbselect("SELECT id FROM catalog_" . $_GET['catalog_id'] . "_data 
WHERE id < $colname_detail AND site_id=".intval($_SESSION['site_id'])." ORDER BY id DESC LIMIT 0,1", 0, $prevId, 'row');
$nextId = el_dbselect("SELECT id FROM catalog_" . $_GET['catalog_id'] . "_data 
WHERE id > $colname_detail AND site_id=".intval($_SESSION['site_id'])." ORDER BY id ASC LIMIT 0,1", 0, $nextId, 'row');
?>
<html>
<head>
	<title>Редактирование записи каталога</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link href="/editor/style.css" rel="stylesheet" type="text/css">
	<style type="text/css">
		.style1 {
			color: #009900
		}

		.style2 {
			color: #FF0000
		}

		.paging, .paging td {
			border-width: 0px;
		}
        .icon_plus {
            cursor: pointer;
            position: relative;
            display: inline;
        }
        .childCats {
            position: relative;
            left: 10px;
        }
	</style>
	<script language="JavaScript" type="text/JavaScript">
		<!--

		function opclose(id) {
			if (document.getElementById(id).style.display == "none") {
				document.cookie = "idshow[" + id + "]=Y; expires=Thu, 31 Dec 2020 23:59:59 GMT; path=/editor/;";
				document.getElementById(id).style.display = "block";
				document.getElementById(id + "_button").value = "Скрыть дополнительные цены"
			} else {
				document.cookie = "idshow[" + id + "]=N; expires=Thu, 31 Dec 2020 23:59:59 GMT; path=/editor/;";
				document.getElementById(id).style.display = "none";
				document.getElementById(id + "_button").value = "Показать дополнительные цены"
			}
			;
		}

		function MM_openBrWindow(theURL, winName, features, myWidth, myHeight, isCenter) { //v3.0
			if (window.screen)if (isCenter)if (isCenter == "true") {
				var myLeft = (screen.width - myWidth) / 2;
				var myTop = (screen.height - myHeight) / 2;
				features += (features != '') ? ',' : '';
				features += ',left=' + myLeft + ',top=' + myTop;
			}
			window.open(theURL, winName, features + ((features != '') ? ',' : '') + 'width=' + myWidth + ',height=' + myHeight);
		}

		function delimg(im, name) {
			var OK = confirm("Вы действительно хотите удалить на сервере файл \"" + name.replace("/images/", "") + "\" ?");
			if (OK) {
				document.deform.imname.value = name;
				document.deform.field.value = im;
				document.deform.submit();
			}
		}

		function swf_delImg(file_name, id, field_name) {
			$("#" + id).css('cursor', 'wait');
			var target_field = $('#' + field_name);
			$.post('/js/delThumb.php', {'file_name': file_name}, function (data) {
				if (data.length > 0) {
					alert(data);
				}
				var d = target_field.val();
				var dArr = d.split(' , ');
				var nArr = [];
				var c = 0;
				for (var i = 0; i < dArr.length; i++) {
					if ('/images/small/' + file_name.replace('/images/small/', '') != dArr[i]/* && el_trim(dArr[i]).length == 56*/) {
						nArr[c] = dArr[i];
						c++;
					}
				}
				target_field.val(nArr.join(' , '));
				$("#" + id).css('cursor', 'default').remove();
			})
		}

		//-->
	</SCRIPT>
	<script type="text/javascript" src="/js/jquery-1.11.0.min.js"></script>
	<link href="/js/css/start/jquery.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" href="/js/flatpickr/flatpickr.min.css">
	<link rel="stylesheet" href="/js/flatpickr/plugins/confirmDate/confirmDate.css">


	<script src="/js/flatpickr/flatpickr.js"></script>
	<script src="/js/flatpickr/plugins/confirmDate/confirmDate.js"></script>
	<script src="/js/flatpickr/l10n/ru.js"></script>
	<script type="text/javascript" src="/editor/e_modules/ckeditor2/ckeditor.js"></script>
	<script type="text/javascript" src="/js/fine-uploader/jquery.fine-uploader.js"></script>
	<link rel="stylesheet" href="/js/fine-uploader/fine-uploader-gallery.css">
    <script src="/js/spectrum.js"></script>
    <link rel="stylesheet" href="/js/spectrum.css">
	<script language="javascript">
		function getDependList(obj, parent_catalog, child_catalog, target_field, parent_field, child_field, curr_value) {
			$('#field' + target_field).after('<span id="preload"><img src="/images/loading.gif" align=absmiddle>&nbsp; Пожалуйста, подождите...</span>');
			$.post('/editor/modules/catalog/getDependList.php',
				{
					'parent_catalog': parent_catalog,
					'child_catalog': child_catalog,
					'val': $(obj).val(),
					'target_field': target_field,
					'parent_field': parent_field,
					'child_field': child_field,
					'curr_value': curr_value
				},
				function (data) {/*alert(data);*/
					$('#field' + target_field).html(data);
					$('#preload').remove()
				}
			);
		}

		function getModels(obj, curr_val) {
			$('#field3').after('<span id="preload"><img src="/images/loading.gif" align=absmiddle></span>');
			$.post('/editor/modules/catalog/getModels.php',
				{'val': $(obj).val(), 'curr_value': curr_val},
				function (data) {/*alert(data);*/
					$('#field3').html('');
					$('#field3').html(data);
					$('#preload').remove()
				}
			);
		}

		function getModif(obj, curr_val) {
			$('#field4').after('<span id="preload"><img src="/images/loading.gif" align=absmiddle></span>');
			$.post('/editor/modules/catalog/getModif.php',
				{'val': $(obj).val(), 'curr_value': curr_val},
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
					'Я': 'ya', 'я': 'ya', ' ': '-', '?': '', ',': '-', '.': '-', '"': '', '@': '', '&': '',
					'«': '', '»': '', '+': ''
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

        function showChildCat(obj) {
            var parentId = $(obj).attr("id").replace("toggle", "");
            if ($(obj).attr('src') == '/editor/img/plus.gif') {
                $(obj).attr('src', '/editor/img/minus.gif');;
                $('#child' + parentId).show();
            } else {
                $('#child' + parentId).hide();
                $(obj).attr('src', '/editor/img/plus.gif')
            }
        }

		$(document).ready(function (e) {
			$("input[name='field<?=$title_catalog['field']?>']").keyup(function (e) {
				$("input[name='path']").val($(this).val().translit() + '-' + $("#goodid").val())
			});
		});
		var swfu;
	</script>
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
</head>

<body>
<form name="deform" method="post"><input type="hidden" name="imname"><input type="hidden" name="field"></form>
<form method="POST" action="<?php echo $editFormAction; ?>" name="edititem" ENCTYPE="multipart/form-data">
	<table width="98%" align="center" cellpadding="3" cellspacing="0" class="el_tbl">
		<tr>
			<td colspan="2">
				<table width="100%" border="0" cellspacing="0" cellpadding="4" class="paging">
					<tr>
						<td width="20%">
							<? if (strlen($prevId['id']) > 0) { ?>
								<input type="button" name="button" id="button" value="  &laquo;  " class="but"
									   title="Предыдущая запись"
									   onClick="location.href='<?= $_SERVER['SCRIPT_NAME'] . '?id=' . $prevId['id'] . '&' . $urlParams ?>'">
							<? } ?>          </td>
						<td width="60%" align="center"><b>Редактирование записи #<?= $colname_detail ?><? print_r($row_cat_form); ?></b></td>
						<td width="20%" align="right">
							<? if (strlen($nextId['id']) > 0) { ?>
								<input type="button" name="button" id="button" value="  &raquo;  " class="but"
									   title="Следующая запись"
									   onClick="location.href='<?= $_SERVER['SCRIPT_NAME'] . '?id=' . $nextId['id'] . '&' . $urlParams ?>'">
							<? } ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td align="right" valign="top">Номер:</td>
			<td><input name="sort" type="text" id="sort" value="<?php echo $row_detail['sort']; ?>" size="3">
				<? /*input name="cat" type="hidden" id="cat" value="<?php echo $row_detail['cat']; ?>"*/ ?>
				<input name="id" type="hidden" id="id" value="<?php echo $row_detail['id']; ?>"></td>
		</tr>
		<?/*tr>
			<td align="right" valign="top">Артикул:</td>
			<td><input name="goodid" type="text" id="goodid" value="<?php echo $row_detail['goodid']; ?>" size="3"></td>
		</tr*/?>
        <input name="goodid" type="hidden" id="goodid" value="<?php echo $row_detail['goodid']; ?>">
		<tr>
			<td align="right" valign="top">Путь (ЧПУ):</td>
			<td><input name="path" type="text" id="path" value="<?= $row_detail['path'] ?>" size="60"></td>
		</tr>

        <?
        $curr = el_dbselect("SELECT content.cat AS cat, cat.name AS title
	   FROM content, cat WHERE content.cat=cat.id AND content.kod='catalog" . $_GET['catalog_id'] . "' AND cat.parent=
	   (SELECT id FROM cat WHERE `path`='/catalog')
	   AND cat.site_id=" . intval($_SESSION['site_id'])." ORDER BY cat.sort", 0, $currCat, 'result', true);
        $currCat = el_dbfetch($curr);
        if(el_dbnumrows($curr) > 1){
        echo '<tr>
			<td align="right" valign="top" nowrap>Разместить в разделе:</td>
			<td>';

        if (substr_count($row_detail['cat'], ' , ') > 0) {
            $good_cats = explode(',', $row_detail['cat']);
            foreach ($good_cats as $key => $val) {
                $good_cats[$key] = trim($val);
            }
        } else {
            $good_cats = $row_detail['cat'];
        }
        ?>
        <div id="cat" style="height:auto; overflow:auto">
            <? do {
                if (is_array($good_cats)) {
                    $sel = (in_array($currCat['cat'], $good_cats, false)) ? ' checked' : '';
                } else {
                    $sel = (intval(trim($row_detail['cat'])) == intval($currCat['cat'])) ? ' checked' : '';
                }
                $child = el_dbselect("SELECT COUNT(id) AS count FROM cat WHERE parent=" . intval($currCat['cat']), 0, $child, 'row', true);
                if ($child['count'] > 0) {
                    ?>
                    <img src="/editor/img/plus.gif" class="icon_plus" id="toggle<?= $currCat['cat'] ?>" onClick="showChildCat(this)">
                <? } ?>
                <label for="cat<?= $currCat['cat'] ?>">
                    <input type="checkbox" name="cats[]" id="cat<?= $currCat['cat'] ?>"
                           value="<?= $currCat['cat'] ?>"<?=$sel?>> <?= $currCat['title'] ?>
                    <?
                    if ($child['count'] > 0) {
                        getChildrenCats($_GET['catalog_id'], $currCat['cat'], 1, $row_detail['cat']);
                    }
                    ?>
                </label><br>
                <?
            } while ($currCat = el_dbfetch($curr));
            echo '</div>
                </td>
            </tr>';
            }else{
                echo '<input type="hidden" name="cats[]" value="'.((intval($currCat['cat']) > 0) ? $currCat['cat'] : $row_detail['cat']).'">';
            }

		if (isset($_POST['imname']) && strlen($_POST['imname']) > 0) {
			if (!@unlink($_SERVER['DOCUMENT_ROOT'] . $_POST['imname'])) {
				echo '<script>alert("Не удалось удалить файл \"' . str_replace('/images/', '', $_POST['imname']) . '\"!\\nВидимо, файла уже нет на сервере.");</script>';
			} else {
				echo '<script>alert("Файл \"' . str_replace('/images/', '', $_POST['imname']) . '\" удален!");
		document.edititem.' . $_POST['field'] . 'hidden.value="";
		</script>';
			}
		}


		;
		$query_cat_form = "SELECT * FROM catalog_prop WHERE catalog_id='" . $_GET['catalog_id'] . "' ORDER BY sort";
		$cat_form = el_dbselect($query_cat_form, 0, $cat_form, 'result', true);
		$row_cat_form = el_dbfetch($cat_form);
		$totalRows_cat_form = el_dbnumrows($cat_form);

		if ($totalRows_cat_form > 0) {

			;
			$query_cat_form1 = "SELECT * FROM catalogs WHERE catalog_id='" . $_GET['catalog_id'] . "'";
			$cat_form1 = el_dbselect($query_cat_form1, 0, $cat_form1, 'result', true);
			$row_cat_form1 = el_dbfetch($cat_form1);
			$depend_fields = array();
			$allow_multiUpload = array();

			do {
				$field_num = "field" . $row_cat_form['field'];
				$prop = $output = $script_line = '';

				switch ($row_cat_form['type']) {
					case "text":
						$input = "input";
						$prop = " value='" . $row_detail[$field_num] . "'>";
						$output = "";
						$script_line = "";
						break;
					case "textarea":
						$input = "textarea";
						$prop = "cols=" . $row_cat_form['cols'] . " rows=" . $row_cat_form['rows'] . ">" . trim($row_detail[$field_num]);
						$output = "</textarea>";//<br>
      //<input name='Button' type='button' onClick=\"MM_openBrWindow('/editor/newseditor.php?field=" . $field_num . "&form=edititem','editor','resizable=yes','750','640','true','description','new')\" value='Визуальный редактор' class='but'>";
						$script_line = "";
						break;
					case "select":
						$input = "textarea";
						$prop = "cols=30 rows=5>" . $row_detail[$field_num];
						$output = "</textarea><br>Здесь вписываются строки списка через точку с запятой ';'"; 
						$script_line = "";
						break;

					case "option":
						$item = explode(";", $row_cat_form['options']);
						$sitem = explode(";", $row_detail[$field_num]);
						$opt = "";
						for ($i = 0; $i < count($item); $i++) {
							if (in_array($item[$i], $sitem)) {
								$opt .= "<option value='" . $item[$i] . "' selected>" . $item[$i] . "</option>\n";
							} else {
								$opt .= "<option value='" . $item[$i] . "'>" . $item[$i] . "</option>\n";
							}
						}
						$output = "<select name='" . $field_num . "' id='" . $field_num . "'" . ((strlen($row_cat_form['size']) > 0) ? " size=" . $row_cat_form['size'] . " multiple" : '') . ">\n<option></option>\n" . $opt . "</select>";
						break;

					case "list_fromdb":
						$list_field = el_dbselect("SELECT id, field" . $row_cat_form['from_field'] . " FROM catalog_" . $row_cat_form['listdb'] . "_data 
						WHERE site_id=".intval($_SESSION['site_id'])." ORDER BY sort ASC", 0, $list_field/*, 'result', true, true*/);
						$row_list_field = @el_dbfetch($list_field);
						$itemlist = '';
						$sitemlist = explode(",", $row_detail[$field_num]);
						do {
							if (in_array($row_list_field['id'], $sitemlist)) {
								$ch = "selected";
							} else {
								$ch = "";
							}
							$itemlist .= "<option $ch value='" . $row_list_field['id'] . "'>" . $row_list_field["field" . $row_cat_form['from_field']] . "</option>\n";
						} while ($row_list_field = @el_dbfetch($list_field));
						$output = "<select name='" . $field_num . "[]' id='" . $field_num . "'" . ((strlen($row_cat_form['size']) > 0) ? " size=" . $row_cat_form['size'] . " multiple" : '') . ">\n<option></option>\n" . $itemlist . "</select>";
						break;

					case "propTable":
						$rowsValue = json_decode($row_detail[$field_num]);
						$list_field = el_dbselect("SELECT id, field" . $row_cat_form['from_field'] . " FROM catalog_" . $row_cat_form['listdb'] . "_data 
						WHERE site_id=".intval($_SESSION['site_id'])." ORDER BY sort ASC", 0, $list_field);
						$row_list_field = @el_dbfetch($list_field);
						$itemlist = '';
						$count = 0;
						do {
							$itemlist .= "<tr><td>" . $row_list_field["field" . $row_cat_form['from_field']] . "</td>
		<td><input type='text' name='field" . $row_cat_form['field'] . "_" . $row_list_field['id'] . "' value='" . $rowsValue->$row_list_field['id'] . "'></td></tr>\n";
							$count++;
						} while ($row_list_field = @el_dbfetch($list_field));
						$output = "<table cellpadding='4'>\n" . $itemlist . "</table>";
						break;

					case "optionlist":
						$item = explode(";", $row_cat_form['options']);
						$sitem = explode(";", $row_detail[$field_num]);
						$opt = "";
						for ($i = 0; $i < count($item); $i++) {
							/*if(in_array($item[$i], $sitem)){
								$opt.="<option value='".$item[$i]."' selected>".$item[$i]."</option>\n";
							}else{
								$opt.="<option value='".$item[$i]."'>".$item[$i]."</option>\n";
							}*/
							$ch = (in_array($item[$i], $sitem)) ? ' checked="checked"' : '';
							$items[] = '<label for="opt' . $i . '">
		  <input type="checkbox" name="field' . $row_cat_form['field'] . '[]" id="opt' . $i . '" value="' . $item[$i] . '"' . $ch . '> ' . $item[$i] . '</label><br>';
						}
						$output = "<div style='height:" . ((strlen($row_cat_form['size']) > 0) ? 17 * $row_cat_form['size'] . "px; overflow:auto'" : '100px') . ">\n" . implode("\n", $items) . "</div>";
						break;
					case "checkbox":
						$input = "input";
						if ($row_detail[$field_num] == $row_cat_form['name']) {
							$prop = "checked value='" . $row_cat_form['name'] . "'>";
						} else {
							$prop = " value='" . $row_cat_form['name'] . "'>";
						};
						$output = "";
						$script_line = "";
						break;

					case "depend_list":
						$itemlist = '';
						$sitemlist = explode(";", $row_detail[$field_num]);
						$list_field = el_dbselect("SELECT id, field" . $row_cat_form['from_field'] . " FROM catalog_" . $row_cat_form['listdb'] . "_data 
						WHERE site_id=".intval($_SESSION['site_id'])." ORDER BY field" . $row_cat_form['from_field'] . " ASC", 0, $list_field);
						$row_list_field = el_dbfetch($list_field);
						$sitemlist = explode(";", $row_detail[$field_num]);
						do {
							$ch = (in_array($row_list_field['id'], $sitemlist)) ? " selected" : '';
							$itemlist .= "<option value='" . $row_list_field['id'] . "'" . $ch . ">" . $row_list_field["field" . $row_cat_form['from_field']] . "</option>\n";
						} while ($row_list_field = el_dbfetch($list_field));
						$output = "<select name='field" . $row_cat_form['field'] . "' id='field" . $row_cat_form['field'] . "'" . ((strlen($row_cat_form['size']) > 0) ? " size='" . $row_cat_form['size'] . "' multiple" : '') . " onchange='getDependList(this, \"" . $row_cat_form['listdb'] . "\", \"" . $row_cat_form['options'] . "\", \"" . $row_cat_form['default_value'] . "\", \"" . $row_cat_form['from_field'] . "\", \"" . $row_cat_form['to_field'] . "\", \"" . $row_detail["field" . $row_cat_form['default_value']] . "\")'>\n<option></option>\n" . $itemlist . "</select>";
						$depend_fields[] = $row_cat_form['field'];
						break;

					case "marks":
						$list_field = el_dbselect("select field1 from catalog_marks_data ORDER BY field1 ASC", 0, $list_field);
						$row_list_field = el_dbfetch($list_field);
						$itemlist = '';
						$mark = $row_detail[$field_num];
						do {
							$sel = ($row_detail[$field_num] == $row_list_field["field1"]) ? ' selected' : '';
							$itemlist .= "<option value='" . $row_list_field["field1"] . "'$sel>" . $row_list_field["field1"] . "</option>\n";
						} while ($row_list_field = el_dbfetch($list_field));
						$output = "<select name='field" . $row_cat_form['field'] . "' id='field" . $row_cat_form['field'] . "'" . ((strlen($row_cat_form['size']) > 0) ? " size=" . $row_cat_form['size'] . " multiple" : '') . " onchange='getModels(this)'>\n<option></option>\n" . $itemlist . "</select>";
						break;

					case "models":
						$list_field = el_dbselect("select field1 from catalog_marks_data ORDER BY field1 ASC", 0, $list_field);
						$row_list_field = el_dbfetch($list_field);
						$itemlist = '';
						$model = $row_detail[$field_num];
						do {
							$sel = ($row_detail[$field_num] == $row_list_field["field1"]) ? ' selected' : '';
							$itemlist .= "<option value='" . $row_list_field["field1"] . "'$sel>" . $row_list_field["field1"] . "</option>\n";
						} while ($row_list_field = el_dbfetch($list_field));
						$output = "<select name='field" . $row_cat_form['field'] . "' id='field" . $row_cat_form['field'] . "'" . ((strlen($row_cat_form['size']) > 0) ? " size=" . $row_cat_form['size'] . " multiple" : '') . " onchange='getModif(this, \"" . $row_detail[$field_num] . "\")'>\n<option></option>\n" . $itemlist . "</select><script>$('#field3').change();getModels($('#field2'), \"" . $row_detail[$field_num] . "\");</script>";
						break;


					/*	case "modif":
						$output="<select name='field".$row_cat_form['field']."' id='field".$row_cat_form['field']."'".((strlen($row_cat_form['size'])>0)?" size=".$row_cat_form['size']." multiple":'').">\n<option></option>\n</select>";
						break;*/

					case "photo_album":
						$list_field = el_dbselect("select id, name from photo_albums WHERE type<>'video' ORDER BY date_create DESC, id DESC", 0, $list_field);
						$row_list_field = el_dbfetch($list_field);
						$itemlist = '';
						do {
							$sel = ($row_list_field['id'] == $row_detail[$field_num]) ? ' selected' : '';
							$itemlist .= "<option value='" . $row_list_field['id'] . "'" . $sel . ">" . $row_list_field['name'] . "</option>\n";
						} while ($row_list_field = el_dbfetch($list_field));
						$output = "<select name='field" . $row_cat_form['field'] . "' id='field" . $row_cat_form['field'] . "' size=" . $row_cat_form['size'] . ">\n<option></option>\n" . $itemlist . "</select>";
						break;
					case "video_album":
						$list_field = el_dbselect("select id, name from photo_albums WHERE type='video' ORDER BY date_create DESC, id DESC", 0, $list_field);
						$row_list_field = el_dbfetch($list_field);
						$itemlist = '';
						do {
							$sel = ($row_list_field['id'] == $row_detail[$field_num]) ? ' selected' : '';
							$itemlist .= "<option value='" . $row_list_field['id'] . "'" . $sel . ">" . $row_list_field['name'] . "</option>\n";
						} while ($row_list_field = el_dbfetch($list_field));
						$output = "<select name='field" . $row_cat_form['field'] . "' id='field" . $row_cat_form['field'] . "' size=" . $row_cat_form['size'] . ">\n<option></option>\n" . $itemlist . "</select>";
						break;

					case "radio":
						$input = "input";
						if ($row_detail[$field_num] == $row_cat_form['name']) {
							$prop = "checked value='" . $row_cat_form['name'] . "'>";
						} else {
							$prop = " value='" . $row_cat_form['name'] . "'>";
						};
						$output = "";
						$script_line = "";
						break;
					case "small_image":
						$input = "input";
						$prop = ">";
						$output = "<input type=hidden name='" . $field_num . "hidden' value='" . $row_detail[$field_num] . "'><br>Укажите местонахождение картинки для предпросмотра на Вашем компьютере для закачки на сервер если нужно сменить существующую";
						$row_cat_form['type'] = "file";
						$script_line = '';
						if (is_file($_SERVER['DOCUMENT_ROOT'] . $row_detail[$field_num])) {
							$script_line = "<img align=top id='" . $field_num . "img' src=" . $row_detail[$field_num] . " border=0><br><input type=button value='Удалить' onclick=\"document.edititem." . $field_num . "hidden.value=''; document.getElementById('" . $field_num . "img').style.display='none';\" class=but>&nbsp;&nbsp;&nbsp;<input type=button class=but value='Удалить на сервере' onclick=\"document.edititem." . $field_num . "hidden.value=''; delimg('" . $field_num . "', '" . $row_detail[$field_num] . "');\"><br><br>";
						}
						break;
					case "big_image":
						$input = "input";
						$prop = ">";
						$output = "<input type=hidden name='" . $field_num . "hidden' value='" . $row_detail[$field_num] . "'><br>Укажите местонахождение картинки на Вашем компьютере для закачки на сервер если нужно сменить существующую";
						$row_cat_form['type'] = "file";
						$script_line = '';
						if (is_file($_SERVER['DOCUMENT_ROOT'] . $row_detail[$field_num])) {
							$script_line = "<img align=top id='" . $field_num . "img' src=" . $row_detail[$field_num] . " border=0><br><input type=button value='Удалить' onclick=\"document.edititem." . $field_num . "hidden.value=''; document.getElementById('" . $field_num . "img').style.display='none';\" class=but>&nbsp;&nbsp;&nbsp;<input type=button class=but value='Удалить на сервере' onclick=\"document.edititem." . $field_num . "hidden.value=''; delimg('" . $field_num . "', '" . $row_detail[$field_num] . "');\"><br><br>";
						}
						break;

					case "multi_image":
						$input = "input";
						$prop = ">";
						$output = "<div id='" . $field_num . "Upload'></div>
						<input type='hidden' name='" . $field_num . "' id='" . $field_num . "' value='" . $row_detail[$field_num] . "'>";
						$row_cat_form['type'] = "multi_image";
						//Показываем ранее закачанные фотографии
						$iArr = explode(' , ', $row_detail[$field_num]);
						for ($i = 0; $i < count($iArr); $i++) {
							if (strlen($iArr[$i]) > 0) {
								$output .= '<div style="display: inline-block; position: relative; margin-right: 5px;" id="thumbE' . $i . '">
								<img src="' . $iArr[$i] . '" title="' . $iArr[$i] . '" border="0">
								<img title="Удалить" onclick="swf_delImg(\'' . $iArr[$i] . '\', \'thumbE' . $i . '\', \'' . $field_num . '\')"
								 src="/images/components/ico_del.png" 
								 style="position:absolute; top:0; right:0; cursor:pointer"></div>';
							}
						}
						//Запоминаем имя поля для обработки в js внизу
						$allow_multiUpload[] = $field_num . 'Upload';
						break;

					case "file":
						$input = "input";
						$prop = ">";
						$output = "<br>Здесь указывается местонахождение файла на Вашем компьютере для закачки на сервер";
						$script_line = "<input type='hidden' value='" . $row_detail[$field_num] . "' name='field" . $row_cat_form['field'] . "f'>Файл - <b>'" . $row_detail[$field_num] . "'</b><br>";
						break;
					case "file_list":
						$input = "input";
						$output = el_createFileSelect($_SERVER['DOCUMENT_ROOT'] . $site_property['down_path' . $_GET['cat']], $field_num, '', $row_detail[$field_num], 'file') . "<br>Выберите файл из указанной в настройках раздела папки";
						break;
					case "secure_file":
						$input = "input";
						$prop = ">";
						$output = "<br>Здесь указывается местонахождение файла на Вашем компьютере для закачки на сервер.<br>Система даст новое нечитаемое название файлу и поместит в недоступное для посетителей сайта место.";
						$script_line = "<input type='hidden' value='" . $row_detail[$field_num] . "' name='field" . $row_cat_form['field'] . "f'>Файл - <b>'" . $row_detail[$field_num] . "'</b><br>";
						$row_cat_form['type'] = "file";
						break;

					case "hidden_file":
						$input = "input";
						$prop = ">";
						$output = "<br>Здесь указывается местонахождение файла на Вашем компьютере для закачки на сервер.<br>Система поместит файл в недоступное для посетителей сайта место.";
						$script_line = "<input type='hidden' value='" . $row_detail[$field_num] . "' name='field" . $row_cat_form['field'] . "f'>Файл - <b>'" . $row_detail[$field_num] . "'</b><br>";
						$row_cat_form['type'] = "file";
						break;

//					case "calendar":
//						$input = "input";
//						$prop = " value='" . $row_detail[$field_num] . "'>";
//						$row_cat_form['type'] = "text";
//						$output = " <script type=\"text/javascript\">$(function() {
//							$(\"#field" . $row_cat_form['field'] . "\").flatpickr({
//							enableTime: true,
//							allowInput: true,
//							locale: 'ru',
//							time_24hr: true,
//							'plugins': [new confirmDatePlugin({})]
//							});
//						});
//						</script>";
//						break;
					case "calendar":
						$input = "input";
						$row_cat_form['type'] = "text";
						$prop = " value='" . (($row_detail[$field_num] == '0000-00-00') ? '' : $row_detail[$field_num]) . "'>";
						$output = ' <script type="text/javascript">$(function() {
									    $("#field' . $row_cat_form['field'] . '").flatpickr({
									        enableTime: false, 
									        allowInput: true, 
									        locale: "ru", 
									        time_24hr: true,
									        "plugins": [new confirmDatePlugin({})]
									        });
										});
										</script>';
						break;
					case "datetime":
						$input = "input";
						$row_cat_form['type'] = "text";
						$prop = " value='" . $row_detail[$field_num] . "'>";
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
						$prop = " value='" . $row_detail[$field_num] . "'>";
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
						$prop = " value='" . $row_detail[$field_num] . "'>";
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
                        $prop = " value='" . $row_detail[$field_num] . "'>";
                        $output = " <script type=\"text/javascript\">$(function() {
										 $(\"#field" . $row_cat_form['field'] . "\").spectrum({
                                            color: \"".$row_detail[$field_num]."\",
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
					case "price":
						$input = "input";
						$prop = " value='" . $row_detail[$field_num] . "'>";
						$script_line = "";
						$output = " " . $row_cat_form1['currency'];
						break;
					case "comments":
						$input = "input";
						if ($row_detail[$field_num] == 1) {
							$prop = "checked value='1' title='Включить/Выключить комментирование'>";
						} else {
							$prop = " value='1' title='Включить/Выключить комментирование'>";
						};
						$output = "";
						$script_line = "";
						$row_cat_form['type'] = "checkbox";
						$output = "<input name='Button' type='button' onClick=\"MM_openBrWindow('/editor/modules/catalog/commentsedit.php?pagepath=" . $_GET['path'] . "/?id=" . $_GET['id'] . "','editor','resizable=yes','700','640','true','description','new')\" value='Комментарии' class='but'>";
						break;
					case "price":
						$input = "input";
						$prop = "";
						$output = " " . $row_cat_form1['currency'];
						break;
					case 'full_html':
						$output = "<textarea cols=" . $row_cat_form['cols'] . " rows=" . $row_cat_form['rows'] . " name='field" . $row_cat_form['field'] . "'>" . trim($row_detail[$field_num]) . "</textarea><script src='/editor/visual_editor.php?class=field" . $row_cat_form['field'] . "&type=full&height=" . ($row_cat_form['rows'] * 30) . "'></script>";
						break;
					case 'basic_html':
						$output = "<textarea cols=" . $row_cat_form['cols'] . " rows=" . $row_cat_form['rows'] . " name='field" . $row_cat_form['field'] . "'>" . trim($row_detail[$field_num])  . "</textarea><script src='/editor/visual_editor.php?class=field" . $row_cat_form['field'] . "&type=basic&height=" . ($row_cat_form['rows'] * 30) . "'></script>";
						break;
					case "text":
					default:
						$input = "input";
						$prop = " value='" . $row_detail[$field_num] . "'>";
						$output = "";
						$script_line = "";
						$row_cat_form['type'] = 'text';
						break;
				}
				if ($row_cat_form['type'] == 'option' || $row_cat_form['type'] == 'optionlist' || $row_cat_form['type'] == 'list_fromdb' || $row_cat_form['type'] == 'file_list' || $row_cat_form['type'] == 'full_html' || $row_cat_form['type'] == 'basic_html' || $row_cat_form['type'] == 'photo_album' || $row_cat_form['type'] == 'video_album' || $row_cat_form['type'] == 'multi_image' || $row_cat_form['type'] == 'depend_list' || $row_cat_form['type'] == 'propTable' || $row_cat_form['type'] == 'marks' || $row_cat_form['type'] == 'models' || $row_cat_form['type'] == 'modif') {
					echo "<tr><td align='right' valign='top'>" . $row_cat_form['name'] . ": </td><td>$output</td>";
				} else {
					echo "<tr><td align='right' valign='top'>" . $row_cat_form['name'] . ": </td><td>$script_line<$input type='" . $row_cat_form['type'] . "' name='field" . $row_cat_form['field'] . "' id='field" . $row_cat_form['field'] . "' size='" . $row_cat_form['size'] . "'$prop$output</td>";
				}

			} while ($row_cat_form = el_dbfetch($cat_form));
		}
		?>
		<? if ($row_cat_form1['meta'] > 0) { ?>
			<tr>
				<td align="right" valign="top">Title:</td>
				<td><input name="title" type="text" id="title" value="<?= $row_detail['title'] ?>" size="80"></td>
			</tr>
			<tr>
				<td align="right" valign="top">Description:</td>
				<td><textarea type="textarea" name="description" cols=60
							  rows=3><?= $row_detail['description'] ?></textarea></td>
			</tr>
			<tr>
				<td align="right" valign="top">Keywords:</td>
				<td><textarea type="textarea" name="keywords" cols=60 rows=3><?= $row_detail['keywords'] ?></textarea>
				</td>
			</tr>
		<? }
		?>
		<tr>
			<td align="right" valign="top">Активный:</td>
			<td><input <?php if (!(strcmp($row_detail['active'], "1"))) {
					echo "checked";
				} ?> name="active" type="checkbox" id="active" value="checkbox"></td>
		</tr>
		<tr>
			<td colspan="2" align="center" valign="top">

				<input type="button" name="Submit2" value="Закрыть" onClick="top.reloadFrame();top.closeDialog()" class="but close">
                <input type="submit" name="Submit" value="Сохранить" class="but agree"></td>
		</tr>
		<input type="hidden" name="MM_update" value="edititem">
	</table>
</form>

<script language="javascript">
	<?
	if (count($depend_fields) > 0) {
		for ($i = 0; $i < count($depend_fields); $i++) {
			echo '$("#field' . $depend_fields[$i] . '").change();' . "\n";
		}
	}
	if(count($allow_multiUpload) > 0){
	?>
	$(document).ready(function () {
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

		var imgField<?=$a?> = $("#<?=$allow_multiUpload[$a]?>".replace('Upload', ''));
		var fileArr<?=$a?> = [];
		var fileResultArr<?=$a?> = [];
		var oldFileArr<?=$a?> = imgField<?=$a?>.val();
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
						console.log('onComplete ' + imgField<?=$a?>.val(), fileArr<?=$a?>, id);
					}
				},
				onDeleteComplete: function (id, XHR, IsError) {
					if(!IsError) {
						delete fileArr<?=$a?>[id];
						var oldFileArray = oldFileArr<?=$a?>.split(' , ');
						for (var i = 0; i < oldFileArray.length; i++){
							if(fileArr<?=$a?>.indexOf(oldFileArray[i]) == -1)
								fileArr<?=$a?>.push(oldFileArray[i]);
						}
						if(fileArr<?=$a?>.length > 0) {
							imgField<?=$a?>.val(cleanArr(fileArr<?=$a?>).join(' , '));
						}else{
							imgField<?=$a?>.val(oldFileArr<?=$a?>);
						}
						console.log('onDeleteComplete ' + imgField<?=$a?>.val()+'|'+fileResultArr<?=$a?>);
					}else{
						alert("Не удаётся удалить этот файл");
					}
				},
				onAllComplete: function (success, fail) {
					for(var i in success) {
						if (!success.hasOwnProperty(i)) continue;
							fileResultArr<?=$a?>[i] = fileArr<?=$a?>[success[i]];
					}
					fileResultArr<?=$a?>.push(oldFileArr<?=$a?>);
					imgField<?=$a?>.val(fileResultArr<?=$a?>.join(' , '));
					console.log('onAllComplete ' + success, fileResultArr<?=$a?>, imgField<?=$a?>.val());
				}
			}
		});
		<? }?>
	});
	<?
	} ?>
</script>

</body>
</html>