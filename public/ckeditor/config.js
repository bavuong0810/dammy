/**
 * @license Copyright (c) 2003-2020, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.allowedContent = 'h1 h2 h3 p blockquote strong em;';
	config.extraPlugins = 'lineheight';
	config.line_height = "1px;1.1px;1.2px;1.3px;1.4px;1.5px";
	config.entities_latin = false;
    config.removeButtons = 'Image';
    config.format_tags = 'p;h1;h2;pre';
    config.extraPlugins = 'wordcount,notification';
};
