/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.extraPlugins = 'htmlSource';
	config.filebrowserImageBrowseUrl = base_url+'imgadmin/Imagens/add';
	
	config.toolbar = [
	{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
	{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [  'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-','NumberedList', 'BulletedList', '-', 'Blockquote' ] },
	{ name: 'styles', items: [ 'Format'] },
	{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
	
	'/',
	{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
	{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
	{ name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule' ] },
	{ name: 'tools', items: [ 'htmlSource' ] }
];
	
};
