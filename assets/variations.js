( function ( blocks, i18n ) {
	'use strict';

	var variations = [
		{
			name: 'manga-shelf-latest-volume',
			title: i18n.__( '漫画：最新巻', 'manga-shelf' ),
			description: i18n.__( 'この作品の最新巻を表示します。', 'manga-shelf' ),
			scope: [ 'inserter' ],
			attributes: {
				metadata: {
					bindings: {
						content: { source: 'manga-shelf/latest-volume' }
					}
				}
			}
		},
		{
			name: 'manga-shelf-latest-release-date',
			title: i18n.__( '漫画：最新巻発売日', 'manga-shelf' ),
			description: i18n.__( 'この作品の最新巻発売日を表示します。', 'manga-shelf' ),
			scope: [ 'inserter' ],
			attributes: {
				metadata: {
					bindings: {
						content: { source: 'manga-shelf/latest-release-date' }
					}
				}
			}
		},
		{
			name: 'manga-shelf-publisher',
			title: i18n.__( '漫画：出版社', 'manga-shelf' ),
			description: i18n.__( '登録した出版社を表示します。', 'manga-shelf' ),
			scope: [ 'inserter' ],
			attributes: {
				metadata: {
					bindings: {
						content: { source: 'core/post-meta', args: { key: 'manga_publisher' } }
					}
				}
			}
		},
		{
			name: 'manga-shelf-rating',
			title: i18n.__( '漫画：評価', 'manga-shelf' ),
			description: i18n.__( '登録した評価を表示します。', 'manga-shelf' ),
			scope: [ 'inserter' ],
			attributes: {
				metadata: {
					bindings: {
						content: { source: 'core/post-meta', args: { key: 'manga_rating' } }
					}
				}
			}
		}
	];

	variations.forEach( function ( variation ) {
		blocks.registerBlockVariation( 'core/paragraph', variation );
	} );

	[
		{
			name: 'manga-shelf-volume-rakuten-button',
			title: i18n.__( '漫画：各巻の楽天ボタン', 'manga-shelf' ),
			description: i18n.__( '各巻の楽天商品ページへ移動するボタンです。', 'manga-shelf' ),
			store: 'rakuten',
			text: i18n.__( '楽天で見る', 'manga-shelf' )
		},
		{
			name: 'manga-shelf-volume-amazon-button',
			title: i18n.__( '漫画：各巻のAmazonボタン', 'manga-shelf' ),
			description: i18n.__( '各巻のAmazon.co.jp商品ページへ移動するボタンです。', 'manga-shelf' ),
			store: 'amazon',
			text: i18n.__( 'Amazonで見る', 'manga-shelf' )
		}
	].forEach( function ( definition ) {
		blocks.registerBlockVariation( 'core/button', {
			name: definition.name,
			title: definition.title,
			description: definition.description,
			icon: 'cart',
			scope: [ 'inserter', 'transform' ],
			attributes: {
				text: definition.text,
				linkTarget: '_blank',
				className: definition.name,
				metadata: {
					bindings: {
						url: {
							source: 'manga-shelf/volume-store',
							args: { store: definition.store }
						},
						rel: {
							source: 'manga-shelf/volume-store',
							args: { store: definition.store }
						}
					}
				}
			},
			isActive: function ( blockAttributes ) {
				var bindings = blockAttributes.metadata && blockAttributes.metadata.bindings;
				var urlBinding = bindings && bindings.url;
				return !! ( urlBinding && urlBinding.source === 'manga-shelf/volume-store' && urlBinding.args && urlBinding.args.store === definition.store );
			}
		} );
	} );
} )( window.wp.blocks, window.wp.i18n );
