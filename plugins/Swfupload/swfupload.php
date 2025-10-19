
<?
	$get_para_upload = '?ok=1';
	if(isset($_GET['categorias_mais_fotos']))	$get_para_upload .= '&categorias_mais_fotos='.$_GET['categorias_mais_fotos'];
	if(isset($_GET['tabelas']))					$get_para_upload .= '&tabelas='.$_GET['tabelas'];
?>

<link href="<?=DIR?>/plugins/Swfupload/css/default.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?=DIR?>/plugins/Swfupload/swfupload/swfupload.js"></script>
<script type="text/javascript" src="<?=DIR?>/plugins/Swfupload/js/swfupload.swfobject.js"></script>
<script type="text/javascript" src="<?=DIR?>/plugins/Swfupload/js/swfupload.queue.js"></script>
<script type="text/javascript" src="<?=DIR?>/plugins/Swfupload/js/fileprogress.js"></script>
<script type="text/javascript" src="<?=DIR?>/plugins/Swfupload/js/handlers.js"></script>
<script type="text/javascript">
var swfu;

SWFUpload.onload = function () {
	var settings = {
		flash_url : "<?=DIR?>/plugins/Swfupload/swfupload/swfupload.swf",
		upload_url: "<?=DIR?>/plugins/Swfupload/upload.php<?=$get_para_upload?>",
		post_params: {
			"PHPSESSID" : "NONE",
			"HELLO-WORLD" : "Here I Am",
			".what" : "OKAY"
		},
		file_size_limit : "100 MB",
		file_types : "*.*",
		file_types_description : "All Files",
		file_upload_limit : 100,
		file_queue_limit : 0,
		custom_settings : {
			progressTarget : "fsUploadProgress",
			cancelButtonId : "btnCancel"
		},
		debug: false,

		// Button Settings
		button_image_url : "<?=DIR?>/plugins/Swfupload/botao.png",
		button_placeholder_id : "spanButtonPlaceholder",
		button_width: 61,
		button_height: 22,

		// The event handler functions are defined in handlers.js
		swfupload_loaded_handler : swfUploadLoaded,
		file_queued_handler : fileQueued,
		file_queue_error_handler : fileQueueError,
		file_dialog_complete_handler : fileDialogComplete,
		upload_start_handler : uploadStart,
		upload_progress_handler : uploadProgress,
		upload_error_handler : uploadError,
		upload_success_handler : uploadSuccess,
		upload_complete_handler : uploadComplete,
		queue_complete_handler : queueComplete,	// Queue plugin event
		
		// SWFObject settings
		minimum_flash_version : "9.0.28",
		swfupload_pre_load_handler : swfUploadPreLoad,
		swfupload_load_failed_handler : swfUploadLoadFailed
	};

	swfu = new SWFUpload(settings);
	
}

</script>
