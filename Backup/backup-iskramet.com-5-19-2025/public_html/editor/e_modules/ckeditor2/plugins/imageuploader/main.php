<?
session_start();
setcookie('sy_lang', 'ru', time() + 14400);
require(__DIR__ . "/pluginconfig.php");

function exploreDir($dirName)
{
	$dirName = str_replace('//', '/', $dirName);
	if (file_exists($dirName)) {
		$dir = dir($dirName);
		echo '<ul>';
		while ($file = $dir->read()) {
			if ($file != '.' && $file != '..') {
				if (is_dir($dirName . '/' . $file)) {
					//$liContent = '';
					echo '<li><i></i><a data-src="' . $dirName . '/' . $file . '" href="filebrowser.php?CKEditor=editor1&CKEditorFuncNum=0&langCode=ru&path=' . $dirName . '/' . $file . '" target="files">' . $file . '</a>';
					exploreDir($dirName . '/' . $file);
					echo '</li>'."\n";
				}
			}
		}
		echo '</ul>';
	}
}
//TO_DO
//Добавить создание/переименование/удаление папок
?>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<title><?php echo $imagebrowser1; ?></title>
	<meta name="author" content="Moritz Maleck">
	<link rel="icon" href="img/cd-ico-browser.ico">

	<link rel="stylesheet" href="styles.css">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="dist/js.cookie-2.0.3.min.js"></script>
	<script src="dist/jquery.lazyload.min.js"></script>
	<script src="dist/sweetalert.min.js"></script>
	<link rel="stylesheet" type="text/css" href="dist/sweetalert.css">

	<script src="function.js"></script>
	<style>
		body{
			font-family: Arial;
			font-size: 85%;
			min-width: 1025px;
		}
		#folders {
			width: 17%;
			float: left;
			padding: 15px;

		}
		#folderList{
			margin-top:50px;
			overflow: auto;
			height:90%;
			min-width: 240px;
		}
		#folders ul,
		#folders ul li {
			margin: 0;
			padding: 0;
			list-style: none;
		}
		#folders ul li ul{
			display:none;
		}
		#folders ul li.parentFolder > i{
			display:inline-block;
			width:19px;
			height:17px;
			position: relative;
			left: -23px;
			top: 3px;
			background: url('img/leftmenu_plus.gif') no-repeat left top;
			cursor:pointer;
		}
		#folders ul li.parentFolder.open > i{
			display:inline-block;
			width:19px;
			height:17px;
			background: url('img/leftmenu_minus.gif') no-repeat left top;
			cursor:pointer;
		}
		#folders ul li.parentFolder.open > ul{
			display: block !important;
		}

		#folders ul li {
			padding-left: 20px;
			background: url('img/folder.gif') no-repeat 18px 4px;
			line-height: 23px;
			position:relative;
		}
		#folders ul li a,
		#folders ul li input{
			text-decoration: none;
			color: #000;
			display: inline-block;
			width: 80%;
		}
		#folders ul li a.selected{
			background-color: #0A8ED0;
			color: #fff;
			display: inline-block;
			width: 80%;
		}

		#files {
			width: 80%;
			float: right;
		}

		#files iframe {
			border: none;
			width: 100%;
			height: 98%;
		}
		#header{
			width:20%;
			min-width: 220px;
		}
		#header img{
			margin: 9px 10px 0;
			float: left;
		}
	</style>
	<script type="text/javascript">
		var currentFolder = '';

		function createFolder(){
			$(".selected").parent()
				.addClass("open")
				.children("ul")
				.closest("ul")
				.append("<li class='parentFolder' id='newLi'><input type='text' placeholder='<?=$placeholder1?>' id='createdFolder'></li>");
			$("#createdFolder").on("blur", function(){
				var name = $(this).val();
				if(name != null && name.length > 0) {
					$.post('folders.php', {currentFolder: currentFolder, name: name, new: 1}, function (data) {
						if(data.length > 0)
							alert(data);
						var newPath = currentFolder + '/' + name;
						$('<i></i><a data-src="' + newPath + '" href="filebrowser.php?CKEditor=editor1&amp;CKEditorFuncNum=0&amp;langCode=ru&amp;path=' + newPath + '" target="files">' + name + '</a>').replaceAll("#createdFolder");
						bindSelection();
					})
				}else{
					$("#newLi").remove();
				}
			})

		}
		function editFolder(){
			var lastPathArr = currentFolder.split("/");
			var lastPath = lastPathArr[lastPathArr.length - 1];
			lastPathArr.pop();
			var oldContent = $(".selected");
			$("<input type='text' value='" + lastPath + "' id='editedFolder'>").replaceAll(".selected");
			$("#editedFolder").on("blur", function(){
				var newName = $(this).val()
				if(newName != null && newName.length > 0) {
					$.post('folders.php', {currentFolder: currentFolder, name: newName, rename: 1}, function (data) {
						if(data.length > 0)
							alert(data);
						var newPath = lastPathArr.join("/") + "/" + newName;
						$(oldContent).replaceAll("#editedFolder");
						$(".selected").data("src", newPath)
							.attr("href", "filebrowser.php?CKEditor=editor1&CKEditorFuncNum=0&langCode=ru&path=" + newPath)
							.text(newName);
						$("#filesFrame").attr("src", "filebrowser.php?CKEditor=editor1&CKEditorFuncNum=0&langCode=ru&path=" + newPath);
					})
				}
			})
		}
		function deleteFolder(){
			if(confirm("<?=$confirm1?>")) {
				$.post('folders.php', {currentFolder: currentFolder, delete: 1}, function (data) {
					if(data.length > 0)
						alert(data);
					$(".selected").parent().remove();
				})
			}
		}
		function bindSelection(){
			$("#folderList ul li a:not(.main)").on("click", function(){
				var cObj = $(this);
				$("#folderList ul li a").removeClass("selected");
				cObj.addClass("selected");
				currentFolder = cObj.data("src");
			})
		}
		$(document).ready(function(){
			bindSelection();
		})
	</script>
</head>
<body style="padding-top:0px; max-width:100%">

<div id="folders">
	<div id="header">
		<img onclick="createFolder()" src="img/cd-icon-newfolder.png" class="iconHover" title="<?=$imagebrowser6?>">
		<img onclick="editFolder()" src="img/cd-icon-renfolder.png" class="iconHover" title="<?=$imagebrowser7?>">
		<img onclick="deleteFolder()" src="img/cd-icon-delfolder.png" class="iconHover" title="<?=$imagebrowser8?>">
	</div>
	<div id="folderList">
	<?
	echo '<ul><li><i></i><a class="main" href="filebrowser.php?CKEditor=editor1&CKEditorFuncNum=0&langCode=ru&path=' . $useruploadpath . '" target="files">' . $useruploadfolder . '</a>';
	exploreDir($useruploadpath);
	echo '</li></ul>';
	?>
	</div>
</div>
<script>
	$("#folderList ul li:has('ul')").addClass("parentFolder");
	$(".parentFolder i").click(function(){
		$(this).parent().toggleClass("open");
	})
</script>
<div id="files">
	<iframe name="files" id="filesFrame" src="filebrowser.php?CKEditor=editor1&CKEditorFuncNum=0&langCode=ru"></iframe>
</div>
</body>
</html>