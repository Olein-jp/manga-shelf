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
