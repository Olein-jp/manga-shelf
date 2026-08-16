<?php
/**
 * Amazon link generation.
 *
 * @package MangaShelf
 */

namespace MangaShelf\Integrations\Amazon;

/**
 * Builds constrained Amazon.co.jp links without Product Advertising API data.
 */
final class Link {
	/**
	 * Build a book search URL for an ISBN.
	 *
	 * ISBN and ASIN are not assumed to be identical. A search URL avoids sending
	 * readers to an unrelated product when Amazon uses a different ASIN.
	 *
	 * @param string $isbn ISBN-10 or ISBN-13.
	 * @return string
	 */
	public static function for_isbn( $isbn ) {
		$isbn = preg_replace( '/[^0-9Xx]/', '', (string) $isbn );
		if ( ! in_array( strlen( $isbn ), array( 10, 13 ), true ) ) {
			return '';
		}

		$args = array(
			'i' => 'stripbooks',
			'k' => strtoupper( $isbn ),
		);
		if ( Settings::tracking_id() ) {
			$args['tag'] = Settings::tracking_id();
		}

		return add_query_arg( $args, 'https://www.amazon.co.jp/s' );
	}

	/**
	 * Verify that a generated or stored URL belongs to Amazon Japan.
	 *
	 * @param string $url URL to check.
	 * @return bool
	 */
	public static function is_allowed( $url ) {
		return 'https' === strtolower( (string) wp_parse_url( $url, PHP_URL_SCHEME ) )
			&& 'www.amazon.co.jp' === strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );
	}
}
