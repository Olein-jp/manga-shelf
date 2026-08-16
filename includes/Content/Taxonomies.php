<?php
/**
 * Manga taxonomies.
 *
 * @package MangaShelf
 */

namespace MangaShelf\Content;

/**
 * Registers manga taxonomies.
 */
final class Taxonomies {
	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'init', array( $this, 'register_taxonomies' ) );
	}

	/**
	 * Register author and genre taxonomies.
	 *
	 * @return void
	 */
	public function register_taxonomies() {
		register_taxonomy(
			'manga_author',
			array( 'manga' ),
			array(
				'labels'            => array(
					'name'          => __( '作者', 'manga-shelf' ),
					'singular_name' => __( '作者', 'manga-shelf' ),
				),
				'public'            => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'hierarchical'      => false,
			)
		);

		register_taxonomy(
			'manga_genre',
			array( 'manga' ),
			array(
				'labels'            => array(
					'name'          => __( 'ジャンル', 'manga-shelf' ),
					'singular_name' => __( 'ジャンル', 'manga-shelf' ),
				),
				'public'            => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'hierarchical'      => true,
			)
		);
	}
}
