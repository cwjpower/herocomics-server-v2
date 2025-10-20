<?php
require_once '../../wps-config.php';
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>CKEditor</title>

		<link rel="stylesheet" href="<?php echo INC_URL ?>/css/jquery-ui.css">
		<link rel="stylesheet" href="<?php echo INC_URL ?>/js/plupload-2.1.9/jquery.ui.plupload/css/jquery.ui.plupload.css">

		<script src="<?php echo INC_URL ?>/js/jquery.min.js"></script>
		<script src="<?php echo INC_URL ?>/js/jquery-ui.min.js"></script>

		<script src="<?php echo INC_URL ?>/js/plupload-2.1.9/plupload.full.min.js"></script>
		<script src="<?php echo INC_URL ?>/js/plupload-2.1.9/jquery.ui.plupload/jquery.ui.plupload.min.js"></script>

		<script src="<?php echo INC_URL ?>/js/plupload-2.1.9/i18n/ko.js"></script>

	</head>
	<body style="margin: 0; padding: 0;">

		<div id="pluploader">
		    <p>Your browser doesn't have Flash, Silverlight or HTML5 support.</p>
		</div>

		<div id="ckeditor_filelist" style="display: none;"></div>

		<script>
		// Initialize the widget when the DOM is ready
		$(function() {

			var puBaseDir = '../js/plupload-2.1.8';

		    $("#pluploader").plupload({
		        // General settings
		        runtimes : 'html5,flash,silverlight,html4',
		        url : "./ajax/ckeditor-image-upload.php",

		        // Maximum file size
		        max_file_size : '500mb',

		        chunk_size: 0,

		        // Resize images on clientside if we can
		        resize : {
		            width : 1000,
		            quality : 90
		        },

		        // Specify what files to browse for
		        filters : [
		            {title : "Image files", extensions : "jpg,jpeg,gif,png"}
// 		            {title : "Zip files", extensions : "zip,avi"}
		        ],

		        // Rename files by clicking on their titles
		        rename: false,

		        // Sort files
		        sortable: true,

		        // upload file automatically
		        autostart: true,

		        // Enable ability to drag'n'drop files onto the widget (currently only HTML5 supports that)
		        dragdrop: true,

		        // Views to activate
		        views: {
		            list: false,
		            thumbs: true, // Show thumbs
		            active: 'thumbs'
		        },

		        // Flash settings
		        flash_swf_url : puBaseDir + '/Moxie.swf',

		        // Silverlight settings
		        silverlight_xap_url : puBaseDir + '/Moxie.xap',

		        init : {
		        	UploadComplete : function(up, file) {
			        	if ( $("#ckeditor_move").length == 0 ) {
			        		$("#pluploader_buttons").append( '<a class="plupload_button plupload_add ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" id="ckeditor_move" role="button" style="z-index: 1;"><span class="ui-button-icon-primary ui-icon ui-icon-circle-plus"></span><span class="ui-button-text">에디터에 적용합니다</span></a>' );
			        	}
				    },
				    FileUploaded : function(up, file, data) {
					    plUploadAdapter( data, file.id );
					}
			    }
		    });

		    // PlUpload Panel에서 삭제
		    $(document).on("click", ".ui-icon-circle-check", function(e) {
			    var tmpID = "wps-" + $(this).closest("li").attr("id");
			    $( "#" + tmpID ).remove();

			    if ( $("#ckeditor_filelist").html() == "" ) {
				    $("#ckeditor_move").fadeOut().remove();
				}
			});

		    // CKEditor에 첨부 이미지 추가
		    $(document).on("click", "#ckeditor_move span", function(e) {
		    	pasteToCKEditor( $("#ckeditor_filelist").html() );
			});

		    function plUploadAdapter(data, id) {
				var json = JSON.parse(data.response);
			    $("#ckeditor_filelist").append( '<div><img src="' + json.result + '" id="wps-' + id + '"></div>');
			}

		    // CKEditor로 텍스트 복사
		    function pasteToCKEditor(str) {
				var oEditor = window.opener.CKEDITOR.instances.post_content;
				oEditor.insertHtml( str );
			}

		    // 닫기 버튼 추가
			$("#pluploader_buttons").append('<a class="plupload_button plupload_stop ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" id="pluploader_close" role="button" onclick="self.close();"><span class="ui-button-icon-primary ui-icon ui-icon-circle-close"></span><span class="ui-button-text">닫기</span></a>');

		});
		</script>

	</body>
</html>