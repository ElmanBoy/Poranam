editor.on( 'pluginsLoaded', function( ev )
				{
					// If our custom dialog has not been registered, do that now.
					if ( !CKEDITOR.dialog.exists( 'galleryImage' ) )
					{
						// We need to do the following trick to find out the dialog
						// definition file URL path. In the real world, you would simply
						// point to an absolute path directly, like "/mydir/mydialog.js".
						var href = document.location.href.split( '/' );
						href.pop();
						href.push( 'e_modules', 'ckeditor', 'plugins', 'galleryImage', 'dialogs', 'galleryImage.js' );
						href = href.join( '/' ); 
						// Finally, register the dialog.
						CKEDITOR.dialog.add( 'galleryImage', href );
					}

					// Register the command used to open the dialog.
					editor.addCommand( 'myDialogCmd', new CKEDITOR.dialogCommand( 'galleryImage' ) );

					// Add the a custom toolbar buttons, which fires the above
					// command..
					editor.ui.addButton( 'Gallery',
						{
							label : 'Вставить изображение в галерею',
							command : 'myDialogCmd'
						} );
				});