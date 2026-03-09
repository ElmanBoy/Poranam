// Copyright (c) 2015, Fujana Solutions - Moritz Maleck. All rights reserved.
// For licensing, see LICENSE.md

CKEDITOR.plugins.add( 'imageuploader', {
    init: function( editor ) {
        editor.config.filebrowserBrowseUrl = '/editor/e_modules/ckeditor2/plugins/imageuploader/main.php';
        editor.config.filebrowserImageBrowseUrl = '/editor/e_modules/ckeditor2/plugins/imageuploader/main.php';
        editor.config.filebrowserUploadUrl = '/editor/e_modules/ckeditor2/plugins/imageuploader/imgupload.php';
        editor.config.filebrowserImageUploadUrl = '/editor/e_modules/ckeditor2/plugins/imageuploader/imgupload.php';
    }
});
