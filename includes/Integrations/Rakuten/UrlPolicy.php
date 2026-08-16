<?php
/**
 * Rakuten URL allowlist.
 *
 * @package MangaShelf
 */

namespace MangaShelf\Integrations\Rakuten;

/**
 * Limits API-provided images and purchase links to known Rakuten hosts.
 */
final class UrlPolicy {
	/**
	 * Whether a URL is hosted by the Rakuten thumbnail service.
	 *
	 * @param string $url Image URL.
	 * @return bool
	 */
	public static function is_image( $url ) {
		return self::is_https( $url ) && 'thumbnail.image.rakuten.co.jp' === self::host( $url );
	}

	/**
	 * Whether a URL points to a Rakuten-owned product or affiliate host.
	 *
	 * @param string $url Product URL.
	 * @return bool
	 */
	public static function is_product( $url ) {
		if ( ! self::is_https( $url ) ) {
			return false;
		}
		$host = self::host( $url );
		return 'rakuten.co.jp' === $host || '.rakuten.co.jp' === substr( $host, -14 );
	}

	/**
	 * Normalize a URL host for comparison.
	 *
	 * @param string $url URL.
	 * @return string
	 */
	private static function host( $url ) {
		return strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );
	}

	/**
	 * Require encrypted external URLs.
	 *
	 * @param string $url URL.
	 * @return bool
	 */
	private static function is_https( $url ) {
		return 'https' === strtolower( (string) wp_parse_url( $url, PHP_URL_SCHEME ) );
	}
}
