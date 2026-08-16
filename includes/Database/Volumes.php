<?php
/**
 * Volume repository.
 *
 * @package MangaShelf
 */

namespace MangaShelf\Database;

/**
 * Reads and writes manga volume rows.
 */
final class Volumes {
	/**
	 * Get table name.
	 *
	 * @return string
	 */
	private function table_name() {
		global $wpdb;
		return $wpdb->prefix . 'manga_volumes';
	}

	/**
	 * Upsert a volume by ISBN, falling back to manga and volume number.
	 *
	 * @param array $volume Volume fields.
	 * @return int|false
	 */
	public function upsert( array $volume ) {
		global $wpdb;

		$defaults = array(
			'manga_id'               => 0,
			'volume_number'          => null,
			'isbn13'                 => null,
			'title'                  => '',
			'release_date'           => '',
			'release_date_precision' => 'unknown',
			'rakuten_item_code'      => '',
			'rakuten_product_url'    => '',
			'source'                 => 'manual',
			'status'                 => 'published',
		);
		$data     = wp_parse_args( $volume, $defaults );
		$existing = 0;

		if ( $data['isbn13'] ) {
			$existing = (int) $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->prepare( 'SELECT id FROM ' . $this->table_name() . ' WHERE isbn13 = %s', $data['isbn13'] ) // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			);
		} elseif ( null !== $data['volume_number'] ) {
			$existing = (int) $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->prepare( 'SELECT id FROM ' . $this->table_name() . ' WHERE manga_id = %d AND volume_number = %f', $data['manga_id'], $data['volume_number'] ) // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			);
		}

		$data['updated_at'] = current_time( 'mysql' );
		if ( $existing ) {
			$wpdb->update( $this->table_name(), $data, array( 'id' => $existing ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			return $existing;
		}

		$data['discovered_at'] = current_time( 'mysql' );
		$result                = $wpdb->insert( $this->table_name(), $data ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		return $result ? (int) $wpdb->insert_id : false;
	}

	/**
	 * Get all volumes for a manga.
	 *
	 * @param int  $manga_id Manga post ID.
	 * @param bool $ascending Sort ascending when true.
	 * @return array
	 */
	public function for_manga( $manga_id, $ascending = true ) {
		global $wpdb;
		if ( $ascending ) {
			$sql = $wpdb->prepare( 'SELECT * FROM ' . $this->table_name() . ' WHERE manga_id = %d ORDER BY volume_number ASC, id ASC', $manga_id ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		} else {
			$sql = $wpdb->prepare( 'SELECT * FROM ' . $this->table_name() . ' WHERE manga_id = %d ORDER BY volume_number DESC, id DESC', $manga_id ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		}

		return $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * Get the latest volume.
	 *
	 * @param int $manga_id Manga post ID.
	 * @return object|null
	 */
	public function latest( $manga_id ) {
		$volumes = $this->for_manga( $manga_id, false );
		return $volumes ? $volumes[0] : null;
	}
}
