( function ( blocks, blockEditor, components, element, i18n ) {
	'use strict';

	var el = element.createElement;
	var defaultTemplate = [
		[ 'core/group', { layout: { type: 'flex', flexWrap: 'nowrap', verticalAlignment: 'top' } }, [
			[ 'manga-shelf/volume-cover', { width: 120 } ],
			[ 'core/group', { layout: { type: 'constrained' } }, [
				[ 'manga-shelf/volume-title', { level: 3 } ],
				[ 'manga-shelf/volume-number' ],
				[ 'manga-shelf/volume-release-date' ],
				[ 'manga-shelf/volume-purchase-link' ]
			] ]
		] ]
	];

	function fieldEdit( label, controls ) {
		return function ( props ) {
			var blockProps = blockEditor.useBlockProps( { className: 'manga-shelf-volume-field-placeholder' } );
			return el(
				element.Fragment,
				null,
				controls ? controls( props ) : null,
				el( 'div', blockProps, label )
			);
		};
	}

	function panel( children ) {
		return el(
			blockEditor.InspectorControls,
			null,
			el( components.PanelBody, { title: i18n.__( '表示設定', 'manga-shelf' ) }, children )
		);
	}

	blocks.registerBlockType( 'manga-shelf/volume-list', {
		edit: function () {
			var blockProps = blockEditor.useBlockProps();
			var innerBlocksProps = blockEditor.useInnerBlocksProps( blockProps, {
				template: defaultTemplate,
				templateLock: false,
				renderAppender: blockEditor.InnerBlocks.ButtonBlockAppender
			} );
			return el( 'div', innerBlocksProps );
		},
		save: function () {
			return el( blockEditor.InnerBlocks.Content );
		}
	} );

	blocks.registerBlockType( 'manga-shelf/volume-cover', {
		edit: fieldEdit( i18n.__( '各巻の書影', 'manga-shelf' ), function ( props ) {
			return panel( el( components.RangeControl, {
				label: i18n.__( '幅（px）', 'manga-shelf' ),
				min: 60,
				max: 400,
				value: props.attributes.width,
				onChange: function ( value ) { props.setAttributes( { width: value } ); }
			} ) );
		} ),
		save: function () { return null; }
	} );

	blocks.registerBlockType( 'manga-shelf/volume-title', {
		edit: fieldEdit( i18n.__( '各巻のタイトル', 'manga-shelf' ), function ( props ) {
			return panel( [
				el( components.SelectControl, {
					key: 'level',
					label: i18n.__( 'HTML要素', 'manga-shelf' ),
					value: String( props.attributes.level ),
					options: [
						{ label: 'div', value: '0' },
						{ label: 'h2', value: '2' },
						{ label: 'h3', value: '3' },
						{ label: 'h4', value: '4' },
						{ label: 'p', value: '7' }
					],
					onChange: function ( value ) { props.setAttributes( { level: Number( value ) } ); }
				} ),
				el( components.ToggleControl, {
					key: 'link',
					label: i18n.__( '楽天の商品ページへリンク', 'manga-shelf' ),
					checked: props.attributes.linkToProduct,
					onChange: function ( value ) { props.setAttributes( { linkToProduct: value } ); }
				} )
			] );
		} ),
		save: function () { return null; }
	} );

	blocks.registerBlockType( 'manga-shelf/volume-number', {
		edit: fieldEdit( i18n.__( '各巻の巻数', 'manga-shelf' ), function ( props ) {
			return panel( [
				el( components.TextControl, { key: 'prefix', label: i18n.__( '前に付ける文字', 'manga-shelf' ), value: props.attributes.prefix, onChange: function ( value ) { props.setAttributes( { prefix: value } ); } } ),
				el( components.TextControl, { key: 'suffix', label: i18n.__( '後ろに付ける文字', 'manga-shelf' ), value: props.attributes.suffix, onChange: function ( value ) { props.setAttributes( { suffix: value } ); } } )
			] );
		} ),
		save: function () { return null; }
	} );

	blocks.registerBlockType( 'manga-shelf/volume-release-date', {
		edit: fieldEdit( i18n.__( '各巻の発売日', 'manga-shelf' ), function ( props ) {
			return panel( el( components.TextControl, { label: i18n.__( '前に付ける文字', 'manga-shelf' ), value: props.attributes.prefix, onChange: function ( value ) { props.setAttributes( { prefix: value } ); } } ) );
		} ),
		save: function () { return null; }
	} );

	blocks.registerBlockType( 'manga-shelf/volume-isbn', {
		edit: fieldEdit( i18n.__( '各巻のISBN', 'manga-shelf' ), function ( props ) {
			return panel( el( components.TextControl, { label: i18n.__( '前に付ける文字', 'manga-shelf' ), value: props.attributes.prefix, onChange: function ( value ) { props.setAttributes( { prefix: value } ); } } ) );
		} ),
		save: function () { return null; }
	} );

	blocks.registerBlockType( 'manga-shelf/volume-purchase-link', {
		edit: fieldEdit( i18n.__( '各巻の楽天リンク', 'manga-shelf' ), function ( props ) {
			return panel( el( components.TextControl, { label: i18n.__( 'リンク文言', 'manga-shelf' ), value: props.attributes.label, onChange: function ( value ) { props.setAttributes( { label: value } ); } } ) );
		} ),
		save: function () { return null; }
	} );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.components, window.wp.element, window.wp.i18n );
