        var editor;
        editor = CKEDITOR.replace( '<?=$_GET['class']?>' , {
        <? if($_GET['type']=='basic'){ ?>
        toolbar :
            [
                ['Source','-',''],
                ['Cut','Copy','Paste','PasteText','PasteFromWord','-'],
                ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat','-'],
                ['Format','-','Bold','Subscript','Superscript'],
                ['NumberedList','BulletedList','Outdent','Indent','Blockquote','-'],
                ['JustifyLeft','JustifyCenter','JustifyRight','-'],
                ['Image','Table','HorizontalRule','SpecialChar','PageBreak','InsertPre','-'],
                ['Link','Unlink','Anchor','HorizontalRule','ShowBlocks','Maximize']
            ],
        <? }?>
	        extraPlugins: 'imageuploader,youtube,html5video,widget,widgetselection,clipboard,lineutils',
	        filebrowserImageBrowseUrl: '/editor/e_modules/ckeditor2/plugins/imageuploader/main.php',
			filebrowserBrowseUrl: '/editor/e_modules/ckeditor2/plugins/imageuploader/main.php',
	        filebrowserUploadUrl: '/editor/e_modules/ckeditor2/plugins/imageuploader/imgupload.php',
	        filebrowserImageUploadUrl: '/editor/e_modules/ckeditor2/plugins/imageuploader/imgupload.php',
			height: '<?=$_GET['height']?>px'
		});
		

