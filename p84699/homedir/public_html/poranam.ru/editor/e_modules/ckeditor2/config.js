/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */
UPLOADCARE_PUBLIC_KEY = 'eb5a505ebf732b034d04';
UPLOADCARE_LOCALE = 'ru';
var CKEDITOR_BASEPATH = '/editor/e_modules/ckeditor2/';

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
    config.embed_provider = "//ckeditor.iframe.ly/api/oembed?url={url}&callback={callback}";
    //config.contentsCss = '/css/style.css';
    config.enableContextMenu = true;
	config.removePlugins = 'iframe';
	config.allowedContent = true;
	config.extraAllowedContent = 'style';
	config.youtube_related = false;
	config.filebrowserImageBrowseUrl = '/editor/e_modules/ckeditor2/plugins/imageuploader/main.php';
	config.filebrowserImageUploadUrl = '/editor/e_modules/ckeditor2/plugins/imageuploader/imgupload.php';
	config.filebrowserUploadUrl = '/editor/e_modules/ckeditor2/plugins/imageuploader/imgupload.php';
	config.filebrowserBrowseUrl = '/editor/e_modules/ckeditor2/plugins/imageuploader/main.php';
	config.removeButtons = 'Font-family,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,About,Embed,EmbedSemantic,Uploadcare';
	config.extraPlugins = 'imageuploader,html5video,video,widget,widgetselection,clipboard,lineutils';
};
