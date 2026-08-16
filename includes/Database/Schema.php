<?php
/**
 * Database schema management.
 *
 * @package MangaShelf
 */

namespace MangaShelf\Database;

use MangaShelf\Content\PostType;

/**
 * Installs and upgrades plugin tables.
 */
final class Schema {
	const VERSION = '4';

	/**
	 * Register upgrade checks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'init', array( $this, 'maybe_upgrade' ), 5 );
	}

	/**
	 * Run activation tasks.
	 *
	 * @return void
	 */
	public static function activate() {
		self::install();
		( new PostType() )->register_post_type();
		flush_rewrite_rules();
	}

	/**
	 * Upgrade when the stored schema is behind.
	 *
	 * @return void
	 */
	public function maybe_upgrade() {
		if ( self::VERSION !== get_option( 'manga_shelf_schema_version' ) ) {
			self::install();
		}
	}

	/**
	 * Create or update the volume table.
	 *
	 * @return void
	 */
	public static function install() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table_name      = $wpdb->prefix . 'manga_volumes';
		$charset_collate = $wpdb->get_charset_collate();
		$sql             = "CREATE TABLE {$table_name} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			manga_id bigint(20) unsigned NOT NULL,
			volume_number decimal(10,2) DEFAULT NULL,
			isbn13 varchar(13) DEFAULT NULL,
			title varchar(255) NOT NULL DEFAULT '',
			release_date varchar(100) NOT NULL DEFAULT '',
			release_date_precision varchar(20) NOT NULL DEFAULT 'unknown',
			rakuten_item_code varchar(100) NOT NULL DEFAULT '',
			rakuten_product_url text NOT NULL,
			rakuten_image_url text NOT NULL,
			source varchar(30) NOT NULL DEFAULT 'manual',
			status varchar(30) NOT NULL DEFAULT 'published',
			discovered_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			KEY manga_id (manga_id),
			UNIQUE KEY isbn13 (isbn13),
			UNIQUE KEY manga_volume (manga_id,volume_number)
		) {$charset_collate};";

		dbDelta( $sql );

		$isbn_column = $wpdb->get_row( "SHOW COLUMNS FROM {$table_name} LIKE 'isbn13'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		if ( $isbn_column && 'NO' === $isbn_column->Null ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$wpdb->query( "ALTER TABLE {$table_name} MODIFY isbn13 varchar(13) DEFAULT NULL" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}

		update_option( 'manga_shelf_schema_version', self::VERSION, false );
	}
}
