( function ( blocks, blockEditor, components, element, i18n ) {
	'use strict';

	var el = element.createElement;

	blocks.registerBlockType( 'manga-shelf/volume-list', {
		edit: function ( props ) {
			var blockProps = blockEditor.useBlockProps();
			return el(
				'div',
				blockProps,
				el(
					blockEditor.InspectorControls,
					null,
					el(
						components.PanelBody,
						{ title: i18n.__( '表示設定', 'manga-shelf' ) },
						el( components.ToggleControl, {
							label: i18n.__( '発売日を表示', 'manga-shelf' ),
							checked: props.attributes.showReleaseDate,
							onChange: function ( value ) { props.setAttributes( { showReleaseDate: value } ); }
						} ),
						el( components.ToggleControl, {
							label: i18n.__( '購入リンクを表示', 'manga-shelf' ),
							checked: props.attributes.showPurchaseLink,
							onChange: function ( value ) { props.setAttributes( { showPurchaseLink: value } ); }
						} )
					)
				),
				el( components.Placeholder, {
					icon: 'book-alt',
					label: i18n.__( '漫画：巻一覧', 'manga-shelf' ),
					instructions: i18n.__( '公開画面では、この作品に登録された巻が表示されます。', 'manga-shelf' )
				} )
			);
		},
		save: function () {
			return null;
		}
	} );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.components, window.wp.element, window.wp.i18n );
