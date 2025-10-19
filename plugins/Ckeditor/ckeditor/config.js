/** v4.1
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */


//CKEDITOR.config.enterMode = CKEDITOR.ENTER_DIV;
CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;

CKEDITOR.config.skin = 'moono';

CKEDITOR.editorConfig = function( config ){
    config.filebrowserBrowseUrl = '../plugins/Ckeditor/ckfinder/ckfinder.html',
    config.filebrowserImageBrowseUrl = '../plugins/Ckeditor/ckfinder/ckfinder.html?type=Images',
    config.filebrowserFlashBrowseUrl = '../plugins/Ckeditor/ckfinder/ckfinder.html?type=Flash',
    config.filebrowserUploadUrl = '../plugins/Ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Files',
    config.filebrowserImageUploadUrl = '../plugins/Ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Images',
    config.filebrowserFlashUploadUrl = '../plugins/Ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Flash'
};