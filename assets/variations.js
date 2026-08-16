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
} )( window.wp.blocks, window.wp.i18n );
