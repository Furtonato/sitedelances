/** v4.1
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */


//CKEDITOR.config.enterMode = CKEDITOR.ENTER_DIV;
CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;

CKEDITOR.config.skin = 'moono';


CKEDITOR.editorConfig = function( config ) {

	config.allowedContent = true;
	config.toolbar = [
		{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
		{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
		{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
		{ name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
		{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl' ] },
		{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
		{ name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
		'/',
		{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
		{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
		{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
	];

};




CKEDITOR.editorConfig = function( config ){
    config.filebrowserBrowseUrl = '../plugins/Ckeditor/ckfinder/ckfinder.html',
    config.filebrowserImageBrowseUrl = '../plugins/Ckeditor/ckfinder/ckfinder.html?type=Images',
    config.filebrowserFlashBrowseUrl = '../plugins/Ckeditor/ckfinder/ckfinder.html?type=Flash',
    config.filebrowserUploadUrl = '../plugins/Ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Files',
    config.filebrowserImageUploadUrl = '../plugins/Ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Images',
    config.filebrowserFlashUploadUrl = '../plugins/Ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Flash'
};