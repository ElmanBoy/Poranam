<?
$form = $_GET['form'];
$field = $_GET['field'];
?>
<html>
<head>
	<title>HTML-Редактор</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<style>
		input, select {
			FONT-FAMILY: MS Sans Serif;
			FONT-SIZE: 12px;
		}

		body, td {
			FONT-FAMILY: Tahoma;
			FONT-SIZE: 12px
		}

		a:hover {
			color: #86869B
		}

		a:visited {
			color: navy
		}

		a {
			color: navy
		}

		a:active {
			color: #ff0000
		}

		.st {
			FONT-FAMILY: MS Sans Serif;
			FONT-SIZE: 12px;
		}

		.MenuFile {
			position: absolute;
			top: 27;
		}

		body {
			margin-left: 0px;
			margin-top: 0px;
			margin-right: 0px;
			margin-bottom: 0px;
			border: 0px;
		}

		body, td, th {
			font-family: Arial, Helvetica, sans-serif;
		}

		/*.cke_skin_office2003 .cke_button_Gallery .cke_icon,*/
		/*#cke_62 {*/
			/*background: url(e_modules/ckeditor/plugins/galleryImage/images.png) no-repeat center;*/
		/*}*/

		/*#cke_64 {*/
			/*background: url(e_modules/ckeditor/plugins/flashVideo/images/film.png) no-repeat center;*/
		/*}*/

	</style>
	<script type="text/javascript" src="/js/jquery.js"></script>
	<script type="text/javascript" src="/editor/e_modules/ckeditor2/ckeditor.js"></script>
	<script type="text/javascript">
		var tex = opener.document.<?=$form?>.<?=$field?>.value;
		var editor;
	</script>
	<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body>
<form method="post" name="Add"
      onSubmit="opener.document.<?= $form ?>.<?= $field ?>.value=CKEDITOR.instances.editor2.getData() ;window.close();return false">
	<textarea name="editor2" id="editor2"></textarea>
    <p style="margin-top:4px;text-align: center;">
	<input type="submit" name="Button" value="Вставить" class="but">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="button" class="but" onClick="window.close()" name="Button" value="Закрыть"></p>
</form>
</center>
<script type="text/javascript">
	$('#editor2').val(tex);
	editor = CKEDITOR.replace('editor2', {
//		extraPlugins: 'imageuploader',
//		filebrowserImageBrowseUrl: '/editor/e_modules/ckeditor2/plugins/imageuploader/imgbrowser.php',
//		filebrowserUploadUrl: '/editor/e_modules/ckeditor2/plugins/imageuploader/imgupload.php',
//		filebrowserImageUploadUrl: '/editor/e_modules/ckeditor2/plugins/imageuploader/imgupload.php',
		height: '440'
	});


</script>

</body>
</html>
