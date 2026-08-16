<?php
/**
 * Rakuten volume synchronization.
 *
 * @package MangaShelf
 */

namespace MangaShelf\Integrations\Rakuten;

use MangaShelf\Database\Volumes;
use WP_Error;

/**
 * Finds regular-edition volumes and stores their current API fields.
 */
final class VolumeSync {
	/**
	 * Refresh all matching volumes for a manga.
	 *
	 * @param int    $manga_id    Manga post ID.
	 * @param string $series_title Series title.
	 * @return int|WP_Error
	 */
	public function sync( $manga_id, $series_title ) {
		$items = ( new Client() )->search_books( $series_title );
		if ( is_wp_error( $items ) ) {
			return $items;
		}

		$count = 0;
		foreach ( $items as $item ) {
			if ( $this->is_same_series( $series_title, $item['series_title'] ) && $this->is_regular_edition( $item['title'] ) ) {
				$count += (int) (bool) $this->save( $manga_id, $item );
			}
		}
		return $count;
	}

	/**
	 * Store one normalized API item.
	 *
	 * @param int   $manga_id Manga post ID.
	 * @param array $item     Normalized API item.
	 * @return int|false
	 */
	public function save( $manga_id, array $item ) {
		return ( new Volumes() )->upsert(
			array(
				'manga_id'               => $manga_id,
				'volume_number'          => $item['volume_number'],
				'isbn13'                 => $item['isbn13'],
				'title'                  => $item['title'],
				'release_date'           => $item['release_date'],
				'release_date_precision' => $item['date_precision'],
				'rakuten_item_code'      => $item['item_code'],
				'rakuten_product_url'    => $item['product_url'],
				'rakuten_image_url'      => $item['image_url'],
				'source'                 => 'rakuten',
			)
		);
	}

	/**
	 * Compare normalized series titles.
	 *
	 * @param string $expected Expected title.
	 * @param string $actual   Actual title.
	 * @return bool
	 */
	private function is_same_series( $expected, $actual ) {
		$normalize = static function ( $value ) {
			return strtolower( preg_replace( '/[\s　・:：\-]/u', '', $value ) );
		};
		return $normalize( $expected ) === $normalize( $actual );
	}

	/**
	 * Exclude non-standard editions.
	 *
	 * @param string $title Book title.
	 * @return bool
	 */
	private function is_regular_edition( $title ) {
		return ! preg_match( '/(特装版|限定版|文庫版|完全版|新装版|電子版|Kindle)/iu', $title );
	}
}
