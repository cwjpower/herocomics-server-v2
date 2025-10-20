CKEDITOR.plugins.add('wpsupload',
{
    init: function (editor) {
    	
    	var setupDir = "";
    	pathArray = location.pathname.split( "/" );
    	for (var i = 0; i < pathArray.length; i++ ) {
    		if ( pathArray[i] == "admin" || pathArray[i] == "content" || pathArray[i] == "includes" ) {
    			break;
    		} else {
    			setupDir += pathArray[i] + "/";
    		}
    	}
    	var uploaderUrl = window.location.protocol + "//" + window.location.hostname + setupDir + "includes/lib/ckeditor_image_upload.php";
    	
        var pluginName = 'wpsupload';
        editor.ui.addButton('Wpsupload',
            {
                label: '다중 이미지 업로드',
                command: 'OpenWindow',
                icon: this.path + 'upload.png',
                click: function(editor) {
                	var nw= window.open( uploaderUrl, 'PLUploadWin', 'width=655,height=315,scrollbars=no,scrolling=no,location=no,toolbar=no' );
                	nw.focus();
                }
            });
    }
});