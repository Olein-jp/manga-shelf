<?php
/**
 * Plugin blocks.
 *
 * @package MangaShelf
 */

namespace MangaShelf\Blocks;

/**
 * Registers blocks from metadata.
 */
final class Blocks {
	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'init', array( $this, 'register_blocks' ) );
	}

	/**
	 * Register the volume list block.
	 *
	 * @return void
	 */
	public function register_blocks() {
		wp_register_script(
			'manga-shelf-cover-image-editor',
			MANGA_SHELF_URL . 'blocks/cover-image/index.js',
			array( 'wp-blocks', 'wp-components', 'wp-element', 'wp-i18n', 'wp-block-editor' ),
			MANGA_SHELF_VERSION,
			true
		);
		wp_register_style(
			'manga-shelf-cover-image',
			MANGA_SHELF_URL . 'blocks/cover-image/style.css',
			array(),
			MANGA_SHELF_VERSION
		);
		wp_register_script(
			'manga-shelf-volume-list-editor',
			MANGA_SHELF_URL . 'blocks/volume-list/index.js',
			array( 'wp-blocks', 'wp-components', 'wp-element', 'wp-i18n', 'wp-block-editor' ),
			MANGA_SHELF_VERSION,
			true
		);
		wp_register_style(
			'manga-shelf-volume-list',
			MANGA_SHELF_URL . 'blocks/volume-list/style.css',
			array(),
			MANGA_SHELF_VERSION
		);
		register_block_type_from_metadata( MANGA_SHELF_PATH . 'blocks/cover-image' );
		register_block_type_from_metadata( MANGA_SHELF_PATH . 'blocks/volume-amazon-link' );
		register_block_type_from_metadata( MANGA_SHELF_PATH . 'blocks/volume-cover' );
		register_block_type_from_metadata( MANGA_SHELF_PATH . 'blocks/volume-isbn' );
		register_block_type_from_metadata( MANGA_SHELF_PATH . 'blocks/volume-list' );
		register_block_type_from_metadata( MANGA_SHELF_PATH . 'blocks/volume-number' );
		register_block_type_from_metadata( MANGA_SHELF_PATH . 'blocks/volume-purchase-link' );
		register_block_type_from_metadata( MANGA_SHELF_PATH . 'blocks/volume-release-date' );
		register_block_type_from_metadata( MANGA_SHELF_PATH . 'blocks/volume-title' );
	}
}
