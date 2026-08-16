<?php
/**
 * Rakuten Books API client.
 *
 * @package MangaShelf
 */

namespace MangaShelf\Integrations\Rakuten;

use WP_Error;

/**
 * Retrieves book records from Rakuten Web Service.
 */
final class Client {
	const ENDPOINT = 'https://app.rakuten.co.jp/services/api/BooksBook/Search/20170404';

	/**
	 * Search paper books by title.
	 *
	 * @param string $title    Search term.
	 * @param int    $page     Result page.
	 * @param int    $hits     Results per page.
	 * @return array|WP_Error
	 */
	public function search_books( $title, $page = 1, $hits = 30 ) {
		$application_id = Settings::application_id();
		if ( ! $application_id ) {
			return new WP_Error( 'missing_application_id', __( '楽天アプリケーションIDを設定してください。', 'manga-shelf' ) );
		}

		$args = array(
			'applicationId' => $application_id,
			'format'        => 'json',
			'formatVersion' => 2,
			'title'         => sanitize_text_field( $title ),
			'booksGenreId'  => '001001',
			'hits'          => min( 30, max( 1, (int) $hits ) ),
			'page'          => max( 1, (int) $page ),
			'sort'          => 'standard',
		);
		if ( Settings::affiliate_id() ) {
			$args['affiliateId'] = Settings::affiliate_id();
		}

		$response = wp_safe_remote_get(
			add_query_arg( $args, self::ENDPOINT ),
			array(
				'timeout'    => 15,
				'user-agent' => 'Manga Shelf/' . MANGA_SHELF_VERSION . '; ' . home_url( '/' ),
			)
		);
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			/* translators: %d: HTTP status code. */
			return new WP_Error( 'rakuten_http_error', sprintf( __( '楽天APIがHTTP %dを返しました。', 'manga-shelf' ), wp_remote_retrieve_response_code( $response ) ) );
		}

		$payload = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! is_array( $payload ) || ! isset( $payload['Items'] ) || ! is_array( $payload['Items'] ) ) {
			return new WP_Error( 'rakuten_invalid_response', __( '楽天APIの応答を解析できませんでした。', 'manga-shelf' ) );
		}

		return array_map( array( $this, 'normalize_item' ), $payload['Items'] );
	}

	/**
	 * Normalize one API item.
	 *
	 * @param array $item Raw item.
	 * @return array
	 */
	private function normalize_item( array $item ) {
		$title = isset( $item['title'] ) ? sanitize_text_field( $item['title'] ) : '';
		return array(
			'title'          => $title,
			'series_title'   => $this->series_title( $title ),
			'volume_number'  => $this->volume_number( $title ),
			'author'         => isset( $item['author'] ) ? sanitize_text_field( $item['author'] ) : '',
			'publisher'      => isset( $item['publisherName'] ) ? sanitize_text_field( $item['publisherName'] ) : '',
			'isbn13'         => isset( $item['isbn'] ) ? preg_replace( '/[^0-9]/', '', $item['isbn'] ) : '',
			'release_date'   => isset( $item['salesDate'] ) ? sanitize_text_field( $item['salesDate'] ) : '',
			'item_code'      => isset( $item['itemCode'] ) ? sanitize_text_field( $item['itemCode'] ) : '',
			'product_url'    => isset( $item['affiliateUrl'] ) && $item['affiliateUrl'] ? esc_url_raw( $item['affiliateUrl'] ) : esc_url_raw( isset( $item['itemUrl'] ) ? $item['itemUrl'] : '' ),
			'image_url'      => esc_url_raw( isset( $item['largeImageUrl'] ) ? $item['largeImageUrl'] : '' ),
			'date_precision' => $this->date_precision( isset( $item['salesDate'] ) ? $item['salesDate'] : '' ),
		);
	}

	/**
	 * Remove a trailing volume expression.
	 *
	 * @param string $title Book title.
	 * @return string
	 */
	private function series_title( $title ) {
		$series = preg_replace( '/[\s　]*(?:\(|（)?(?:第)?[0-9０-９]+(?:\.[0-9]+)?(?:巻)?(?:\)|）)?[\s　]*$/u', '', $title );
		return trim( $series ? $series : $title );
	}

	/**
	 * Parse the trailing volume number.
	 *
	 * @param string $title Book title.
	 * @return float|null
	 */
	private function volume_number( $title ) {
		$normalized = strtr(
			$title,
			array(
				'０' => '0',
				'１' => '1',
				'２' => '2',
				'３' => '3',
				'４' => '4',
				'５' => '5',
				'６' => '6',
				'７' => '7',
				'８' => '8',
				'９' => '9',
			)
		);
		if ( preg_match( '/(?:第)?([0-9]+(?:\.[0-9]+)?)(?:巻)?(?:\)|）)?[\s　]*$/u', $normalized, $matches ) ) {
			return (float) $matches[1];
		}
		return null;
	}

	/**
	 * Determine the precision of a Japanese release string.
	 *
	 * @param string $date Release string.
	 * @return string
	 */
	private function date_precision( $date ) {
		if ( preg_match( '/[0-9０-９]{1,2}日/u', $date ) ) {
			return 'day';
		}
		if ( preg_match( '/(上旬|中旬|下旬)/u', $date ) ) {
			return 'period';
		}
		if ( preg_match( '/[0-9０-９]{1,2}月/u', $date ) ) {
			return 'month';
		}
		return 'unknown';
	}
}
