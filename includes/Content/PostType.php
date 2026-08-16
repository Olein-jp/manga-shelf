<?php
/**
 * Manga post type.
 *
 * @package MangaShelf
 */

namespace MangaShelf\Content;

/**
 * Registers the manga post type.
 */
final class PostType {
	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'init', array( $this, 'register_post_type' ) );
	}

	/**
	 * Register the post type.
	 *
	 * @return void
	 */
	public function register_post_type() {
		register_post_type(
			'manga',
			array(
				'labels'       => array(
					'name'          => __( '漫画', 'manga-shelf' ),
					'singular_name' => __( '漫画', 'manga-shelf' ),
					'add_new_item'  => __( '漫画を手動で追加', 'manga-shelf' ),
					'edit_item'     => __( '漫画を編集', 'manga-shelf' ),
				),
				'public'       => true,
				'show_in_rest' => true,
				'menu_icon'    => 'dashicons-book-alt',
				'has_archive'  => true,
				'rewrite'      => array( 'slug' => 'manga' ),
				'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ),
				'template'     => array(
					array( 'manga-shelf/cover-image' ),
					array( 'core/post-title' ),
					array( 'core/post-content' ),
					array( 'manga-shelf/volume-list' ),
				),
			)
		);
	}
}
