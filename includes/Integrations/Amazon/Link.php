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
	 * Build a direct product URL when ISBN-10 can identify the print book.
	 *
	 * Valid ISBN-13 values that cannot be converted to ISBN-10 fall back to an
	 * Amazon book search because they cannot safely be used as an ASIN.
	 *
	 * @param string $isbn ISBN-10 or ISBN-13.
	 * @return string
	 */
	public static function for_isbn( $isbn ) {
		$isbn = preg_replace( '/[^0-9Xx]/', '', (string) $isbn );
		$asin = self::isbn10( $isbn );
		if ( $asin ) {
			$url = 'https://www.amazon.co.jp/dp/' . rawurlencode( $asin ) . '/ref=nosim';
			if ( Settings::tracking_id() ) {
				$url = add_query_arg( 'tag', Settings::tracking_id(), $url );
			}
			return $url;
		}

		if ( 13 !== strlen( $isbn ) || ! self::is_valid_isbn13( $isbn ) ) {
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
	 * Resolve an Amazon book ASIN from ISBN-10 or a convertible ISBN-13.
	 *
	 * Amazon uses ISBN-10 as the ASIN for supported print books. ISBN-13 values
	 * with the 978 prefix can be converted by recalculating the check digit.
	 *
	 * @param string $isbn Normalized ISBN.
	 * @return string
	 */
	private static function isbn10( $isbn ) {
		if ( 10 === strlen( $isbn ) ) {
			return self::is_valid_isbn10( $isbn ) ? strtoupper( $isbn ) : '';
		}

		if ( 13 !== strlen( $isbn ) || '978' !== substr( $isbn, 0, 3 ) || ! self::is_valid_isbn13( $isbn ) ) {
			return '';
		}

		$body = substr( $isbn, 3, 9 );
		$sum  = 0;
		for ( $index = 0; $index < 9; ++$index ) {
			$sum += (int) $body[ $index ] * ( 10 - $index );
		}
		$check = ( 11 - ( $sum % 11 ) ) % 11;
		return $body . ( 10 === $check ? 'X' : (string) $check );
	}

	/**
	 * Validate an ISBN-13 check digit.
	 *
	 * @param string $isbn ISBN-13.
	 * @return bool
	 */
	private static function is_valid_isbn13( $isbn ) {
		if ( 13 !== strlen( $isbn ) || ! ctype_digit( $isbn ) ) {
			return false;
		}

		$sum = 0;
		for ( $index = 0; $index < 12; ++$index ) {
			$sum += (int) $isbn[ $index ] * ( 0 === $index % 2 ? 1 : 3 );
		}
		return ( 10 - ( $sum % 10 ) ) % 10 === (int) $isbn[12];
	}

	/**
	 * Validate an ISBN-10 check digit.
	 *
	 * @param string $isbn ISBN-10.
	 * @return bool
	 */
	private static function is_valid_isbn10( $isbn ) {
		if ( ! preg_match( '/^[0-9]{9}[0-9Xx]$/', $isbn ) ) {
			return false;
		}

		$sum = 0;
		for ( $index = 0; $index < 10; ++$index ) {
			$digit = 9 === $index && 'X' === strtoupper( $isbn[ $index ] ) ? 10 : (int) $isbn[ $index ];
			$sum  += $digit * ( 10 - $index );
		}
		return 0 === $sum % 11;
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
