/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	var width = $('#ckEdit_width').val();
	if (!width) {
		width = 675;
	}
	
	var height = $("#ckEdit_height").val();
	if(!height){
		height = 300;
	}
	config.language = 'zh-cn';
	config.width = width; //宽度
    config.height = height; //高度    
    
    config.font_names='宋体/宋体;黑体/黑体;仿宋/仿宋_GB2312;楷体/楷体_GB2312;隶书/隶书;幼圆/幼圆;微软雅黑/微软雅黑;'+ config.font_names;
 // The toolbar groups arrangement, optimized for two toolbar rows.
//	config.toolbarGroups = [
//		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
//		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
//		{ name: 'links' },
//		{ name: 'insert' },
//		{ name: 'forms' },
//		{ name: 'tools' },
//		{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
//		{ name: 'others' },
//		'/',
//		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
//		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
//		{ name: 'styles' },
//		{ name: 'colors' },
//		{ name: 'about' }
//	];
    
 // Toolbar configuration generated automatically by the editor based on config.toolbarGroups.
    config.toolbar = [
    	{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
    	{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
//    	{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
//    	{ name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },

    	{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
    	{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
    	{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
    	'/',
    	{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
    	{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
    	{ name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
    	{ name: 'others', items: [ '-' ] },
    	{ name: 'about', items: [ 'About' ] }
//    	'/',
    
    	
//    	{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
    	
    ];

    // Toolbar groups configuration.
//    config.toolbarGroups = [
//    	{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
//    	{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
//    	{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ] },
//    	{ name: 'forms' },
//    	'/',
//    	{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
//    	{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
//    	{ name: 'links' },
//    	{ name: 'insert' },
//    	'/',
//    	{ name: 'styles' },
//    	{ name: 'colors' },
//    	{ name: 'tools' },
//    	{ name: 'others' },
//    	{ name: 'about' }
//    ];

	// Remove some buttons provided by the standard plugins, which are
	// not needed in the Standard(s) toolbar.
//	config.removeButtons = 'Underline,Subscript,Superscript';
    config.removeButtons = 'Save,NewPage,Preview,Print,Templates,Language,Flash,Smiley,Iframe,PageBreak,Styles,Format,Font,BidiLtr,BidiRtl';
	// Set the most common block elements.
	config.format_tags = 'p;h1;h2;h3;pre';

	// Simplify the dialog windows.
	config.removeDialogTabs = 'image:advanced;link:advanced';
	
	config.filebrowserUploadUrl="/index.php/file/ckUpload/";
};
