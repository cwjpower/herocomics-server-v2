/**
 * @license Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	
	config.language = 'ko';
	config.image_previewText = ' ';
	config.width = '100%';
	config.height = '300px';
	config.allowedContent = true;
	config.enterMode = CKEDITOR.ENTER_DIV;
	
	config.font_names = '돋움;돋움체;굴림;굴림체;바탕;바탕체;궁서;Arial;Tahoma;Times New Roman;Verdana;Courier New;';
	
	config.extraPlugins = 'wpsupload';
	
	config.toolbar_WpsToolbar = [
  		 	{ name : 'styles', items : [ 'Font', 'FontSize' ] },
  		 	{ name : 'basicstyles', items : [ 'Bold', 'Italic', 'Underline', 'Strike' ] },
  		 	{ name : 'colors', items : [ 'TextColor', 'BGColor' ] },
  		 	{ name : 'paragraph', items : [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'NumberedList', 'BulletedList', 'Outdent', 'Indent' ] },
  		 	{ name : 'insert', items : [ 'Link', 'Table', 'SpecialChar', 'Wpsupload' ] },
  			{ name : 'document', items : [ 'Source' ] }
  	];
 	config.toolbar = 'WpsToolbar';
};
