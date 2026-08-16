<?php
/**
 * Optional cleanup for Manga Shelf.
 *
 * @package MangaShelf
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Data is preserved by default. Define this constant explicitly to remove it.
if ( ! defined( 'MANGA_SHELF_DELETE_DATA' ) || ! MANGA_SHELF_DELETE_DATA ) {
	return;
}

global $wpdb;
$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'manga_volumes' ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.NotPrepared
delete_option( 'manga_shelf_schema_version' );
delete_option( 'manga_shelf_rakuten_application_id' );
delete_option( 'manga_shelf_rakuten_access_key' );
delete_option( 'manga_shelf_rakuten_affiliate_id' );
delete_option( 'manga_shelf_amazon_tracking_id' );

$legacy_cover_ids = get_posts(
	array(
		'post_type'      => 'attachment',
		'post_status'    => 'any',
		'fields'         => 'ids',
		'posts_per_page' => -1,
		'meta_key'       => '_manga_shelf_legacy_cover', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
	)
);
foreach ( $legacy_cover_ids as $legacy_cover_id ) {
	wp_delete_attachment( $legacy_cover_id, true );
}

$manga_ids = get_posts(
	array(
		'post_type'      => 'manga',
		'post_status'    => 'any',
		'fields'         => 'ids',
		'posts_per_page' => -1,
	)
);
foreach ( $manga_ids as $manga_id ) {
	wp_delete_post( $manga_id, true );
}
