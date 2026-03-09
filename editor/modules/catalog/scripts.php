var swfu;
		window.onload = function () {
			swfu = new SWFUpload({
				// Backend Settings
				upload_url: "/editor/modules/gallery/upload.php",
				post_params: {"PHPSESSID": "<?php echo session_id(); ?>", "cat":<?=$cat?>, "in_comments":1, "in_rait":1},

				// File Upload Settings
				file_size_limit : "20 MB",	// 2MB
				file_types : "*.jpg;*.gif;*.png",
				file_types_description : "Файлы изображений",
				file_upload_limit : 0,

				// Event Handler Settings - these functions as defined in Handlers.js
				//  The handlers are not part of SWFUpload but are part of my website and control how
				//  my website reacts to the SWFUpload events.
				swfupload_preload_handler : preLoad,
				swfupload_load_failed_handler : loadFailed,
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				upload_start_handler : uploadStart,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				upload_complete_handler : uploadComplete,
				
				thumb_width: <?=(intval($site_property['gallerySWidth'.$cat])==0)?'200':$site_property['gallerySWidth'.$cat]?>,
				thumb_height: <?=(intval($site_property['gallerySHeight'.$cat])==0)?'200':$site_property['gallerySHeight'.$cat]?>,

				// Button Settings
				button_image_url : "/js/swfu/images/SmallSpyGlassWithTransperancy_17x18.png",
				button_placeholder_id : "spanButtonPlaceholder",
				button_width: 190,
				button_height: 18,
				button_text : '<span class="button">Выберите файлы <span class="buttonSmall">(макс. 20 MB)</span></span>',
				button_text_style : '.button { font-family: Helvetica, Arial, sans-serif; font-size: 12pt; } .buttonSmall { font-size: 10pt; }',
				button_text_top_padding: 0,
				button_text_left_padding: 18,
				button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
				button_cursor: SWFUpload.CURSOR.HAND,
				
				// Flash Settings
				flash_url : "/js/swfu/swfupload.swf",
				flash9_url : "/js/swfu/swfupload_fp9.swf",

				custom_settings : {
					upload_target : "divFileProgressContainer",
					cat : "<?=$cat?>"
				},
				
				// Debug Settings
				debug: false
			});
		};