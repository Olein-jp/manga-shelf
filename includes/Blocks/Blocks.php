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
		register_block_type_from_metadata( MANGA_SHELF_PATH . 'blocks/volume-list' );
	}
}
