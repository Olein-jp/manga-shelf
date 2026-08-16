( function ( blocks, blockEditor, components, element, i18n ) {
	'use strict';

	var el = element.createElement;

	blocks.registerBlockType( 'manga-shelf/cover-image', {
		edit: function () {
			var blockProps = blockEditor.useBlockProps();
			return el(
				'div',
				blockProps,
				el( components.Placeholder, {
					icon: 'format-image',
					label: i18n.__( '漫画：楽天書影', 'manga-shelf' ),
					instructions: i18n.__( '公開画面では、楽天から提供された書影を外部URLのまま表示します。', 'manga-shelf' )
				} )
			);
		},
		save: function () {
			return null;
		}
	} );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.components, window.wp.element, window.wp.i18n );
