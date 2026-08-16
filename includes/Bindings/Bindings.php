<?php
/**
 * Block Bindings sources and variations.
 *
 * @package MangaShelf
 */

namespace MangaShelf\Bindings;

use MangaShelf\Database\Volumes;

/**
 * Exposes derived manga values to core blocks.
 */
final class Bindings {
	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'init', array( $this, 'register_sources' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_variations' ) );
	}

	/**
	 * Register computed binding sources.
	 *
	 * @return void
	 */
	public function register_sources() {
		if ( ! function_exists( 'register_block_bindings_source' ) ) {
			return;
		}

		register_block_bindings_source(
			'manga-shelf/latest-volume',
			array(
				'label'              => __( '漫画：最新巻', 'manga-shelf' ),
				'get_value_callback' => array( $this, 'latest_volume' ),
				'uses_context'       => array( 'postId' ),
			)
		);
		register_block_bindings_source(
			'manga-shelf/latest-release-date',
			array(
				'label'              => __( '漫画：最新巻発売日', 'manga-shelf' ),
				'get_value_callback' => array( $this, 'latest_release_date' ),
				'uses_context'       => array( 'postId' ),
			)
		);
	}

	/**
	 * Resolve the latest volume label.
	 *
	 * @param array     $source_args    Source arguments.
	 * @param \WP_Block $block_instance Block instance.
	 * @return string
	 */
	public function latest_volume( $source_args, $block_instance ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$latest = ( new Volumes() )->latest( $this->post_id( $block_instance ) );
		if ( ! $latest ) {
			return '';
		}
		/* translators: %s: manga volume number. */
		return null !== $latest->volume_number ? sprintf( __( '%s巻', 'manga-shelf' ), (float) $latest->volume_number ) : $latest->title;
	}

	/**
	 * Resolve the latest release date.
	 *
	 * @param array     $source_args    Source arguments.
	 * @param \WP_Block $block_instance Block instance.
	 * @return string
	 */
	public function latest_release_date( $source_args, $block_instance ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$latest = ( new Volumes() )->latest( $this->post_id( $block_instance ) );
		return $latest ? $latest->release_date : '';
	}

	/**
	 * Resolve current post ID from block context.
	 *
	 * @param \WP_Block $block_instance Block instance.
	 * @return int
	 */
	private function post_id( $block_instance ) {
		return isset( $block_instance->context['postId'] ) ? (int) $block_instance->context['postId'] : get_the_ID();
	}

	/**
	 * Load variations only in the manga editor.
	 *
	 * @return void
	 */
	public function enqueue_variations() {
		$screen = get_current_screen();
		if ( ! $screen || 'manga' !== $screen->post_type ) {
			return;
		}

		wp_enqueue_script(
			'manga-shelf-variations',
			MANGA_SHELF_URL . 'assets/variations.js',
			array( 'wp-blocks', 'wp-i18n' ),
			MANGA_SHELF_VERSION,
			true
		);
	}
}
