<?php
/**
 * Block Bindings sources and variations.
 *
 * @package MangaShelf
 */

namespace MangaShelf\Bindings;

use MangaShelf\Database\Volumes;
use MangaShelf\Integrations\Amazon\Link as AmazonLink;
use MangaShelf\Integrations\Amazon\Settings as AmazonSettings;
use MangaShelf\Integrations\Rakuten\UrlPolicy;

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
		add_filter( 'render_block_core/button', array( $this, 'hide_unavailable_store_button' ), 10, 3 );
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
		register_block_bindings_source(
			'manga-shelf/volume-store',
			array(
				'label'              => __( '漫画：各巻の商品リンク', 'manga-shelf' ),
				'get_value_callback' => array( $this, 'volume_store' ),
				'uses_context'       => array( 'mangaShelf/volume' ),
			)
		);
	}

	/**
	 * Resolve a store button attribute for the current volume.
	 *
	 * @param array     $source_args    Source arguments.
	 * @param \WP_Block $block_instance Block instance.
	 * @param string    $attribute_name Bound attribute name.
	 * @return string|false
	 */
	public function volume_store( $source_args, $block_instance, $attribute_name ) {
		$store = isset( $source_args['store'] ) ? sanitize_key( $source_args['store'] ) : '';
		if ( 'url' === $attribute_name ) {
			$url = $this->store_url( $store, $block_instance );
			return $url ? $url : false;
		}

		if ( 'rel' === $attribute_name ) {
			if ( 'rakuten' === $store || ( 'amazon' === $store && AmazonSettings::tracking_id() ) ) {
				return 'nofollow sponsored noopener';
			}

			return 'nofollow noopener';
		}

		return false;
	}

	/**
	 * Remove a store button when its volume has no safe destination URL.
	 *
	 * @param string    $block_content Rendered block HTML.
	 * @param array     $parsed_block  Parsed block data.
	 * @param \WP_Block $block_instance Block instance.
	 * @return string
	 */
	public function hide_unavailable_store_button( $block_content, $parsed_block, $block_instance ) {
		$store = $this->bound_store( $parsed_block );
		if ( $store && ! $this->store_url( $store, $block_instance ) ) {
			return '';
		}

		return $block_content;
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
	 * Load variations in every block editor, including the Site Editor.
	 *
	 * @return void
	 */
	public function enqueue_variations() {
		wp_enqueue_script(
			'manga-shelf-variations',
			MANGA_SHELF_URL . 'assets/variations.js',
			array( 'wp-blocks', 'wp-i18n' ),
			MANGA_SHELF_VERSION,
			true
		);
	}

	/**
	 * Resolve a safe store URL for the volume in block context.
	 *
	 * @param string    $store          Store identifier.
	 * @param \WP_Block $block_instance Block instance.
	 * @return string
	 */
	private function store_url( $store, $block_instance ) {
		$volume = isset( $block_instance->context['mangaShelf/volume'] ) && is_array( $block_instance->context['mangaShelf/volume'] )
			? $block_instance->context['mangaShelf/volume']
			: array();

		if ( 'rakuten' === $store ) {
			$url = isset( $volume['rakuten_product_url'] ) ? (string) $volume['rakuten_product_url'] : '';
			return UrlPolicy::is_product( $url ) ? $url : '';
		}

		if ( 'amazon' === $store ) {
			$isbn = isset( $volume['isbn13'] ) ? (string) $volume['isbn13'] : '';
			$url  = AmazonLink::for_isbn( $isbn );
			return AmazonLink::is_allowed( $url ) ? $url : '';
		}

		return '';
	}

	/**
	 * Read this plugin's store identifier from a Button block binding.
	 *
	 * @param array $parsed_block Parsed block data.
	 * @return string
	 */
	private function bound_store( $parsed_block ) {
		$binding = isset( $parsed_block['attrs']['metadata']['bindings']['url'] )
			? $parsed_block['attrs']['metadata']['bindings']['url']
			: array();
		if ( ! isset( $binding['source'] ) || 'manga-shelf/volume-store' !== $binding['source'] ) {
			return '';
		}

		return isset( $binding['args']['store'] ) ? sanitize_key( $binding['args']['store'] ) : '';
	}
}
