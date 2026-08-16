<?php
/**
 * Manga metadata.
 *
 * @package MangaShelf
 */

namespace MangaShelf\Content;

/**
 * Registers post metadata.
 */
final class Meta {
	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'init', array( $this, 'register_meta' ) );
	}

	/**
	 * Register public manga fields.
	 *
	 * @return void
	 */
	public function register_meta() {
		$fields = array(
			'manga_reading_status'    => array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_key',
			),
			'manga_rating'            => array(
				'type'              => 'number',
				'sanitize_callback' => array( $this, 'sanitize_rating' ),
			),
			'manga_official_url'      => array(
				'type'              => 'string',
				'sanitize_callback' => 'esc_url_raw',
			),
			'manga_sample_url'        => array(
				'type'              => 'string',
				'sanitize_callback' => 'esc_url_raw',
			),
			'manga_publisher'         => array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'manga_label'             => array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'manga_tracking_enabled'  => array(
				'type'              => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
			),
			'manga_cover_image_url'   => array(
				'type'              => 'string',
				'sanitize_callback' => 'esc_url_raw',
			),
			'manga_cover_product_url' => array(
				'type'              => 'string',
				'sanitize_callback' => 'esc_url_raw',
			),
		);

		foreach ( $fields as $key => $args ) {
			register_post_meta(
				'manga',
				$key,
				array_merge(
					$args,
					array(
						'single'        => true,
						'show_in_rest'  => true,
						'auth_callback' => static function () {
							return current_user_can( 'edit_posts' );
						},
					)
				)
			);
		}
	}

	/**
	 * Keep ratings between zero and five.
	 *
	 * @param mixed $value Rating value.
	 * @return float
	 */
	public function sanitize_rating( $value ) {
		return min( 5, max( 0, (float) $value ) );
	}
}
