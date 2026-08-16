<?php
/**
 * Manga details meta box.
 *
 * @package MangaShelf
 */

namespace MangaShelf\Admin;

/**
 * Provides a compact editor for the public manga metadata.
 */
final class MangaDetails {
	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'add_meta_boxes_manga', array( $this, 'add_meta_box' ) );
		add_action( 'save_post_manga', array( $this, 'save' ) );
	}

	/**
	 * Add the meta box.
	 *
	 * @return void
	 */
	public function add_meta_box() {
		add_meta_box( 'manga-shelf-details', __( '作品情報', 'manga-shelf' ), array( $this, 'render' ), 'manga', 'side' );
	}

	/**
	 * Render fields.
	 *
	 * @param \WP_Post $post Current post.
	 * @return void
	 */
	public function render( $post ) {
		wp_nonce_field( 'manga_shelf_save_details', 'manga_shelf_details_nonce' );
		$status = get_post_meta( $post->ID, 'manga_reading_status', true );
		$rating = get_post_meta( $post->ID, 'manga_rating', true );
		?>
		<p>
			<label for="manga-reading-status"><strong><?php esc_html_e( '読書状態', 'manga-shelf' ); ?></strong></label><br>
			<select class="widefat" id="manga-reading-status" name="manga_reading_status">
				<?php
				$options = array(
					'want-to-read' => __( '読みたい', 'manga-shelf' ),
					'reading'      => __( '読書中', 'manga-shelf' ),
					'completed'    => __( '読了', 'manga-shelf' ),
					'on-hold'      => __( '保留', 'manga-shelf' ),
				);
				foreach ( $options as $value => $label ) {
					printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $value ), selected( $status, $value, false ), esc_html( $label ) );
				}
				?>
			</select>
		</p>
		<p>
			<label for="manga-rating"><strong><?php esc_html_e( '評価（0〜5）', 'manga-shelf' ); ?></strong></label><br>
			<input class="small-text" id="manga-rating" max="5" min="0" name="manga_rating" step="0.5" type="number" value="<?php echo esc_attr( $rating ); ?>">
		</p>
		<?php
		$this->url_field( $post->ID, 'manga_official_url', __( '公式サイト', 'manga-shelf' ) );
		$this->url_field( $post->ID, 'manga_sample_url', __( '試し読みURL', 'manga-shelf' ) );
		?>
		<p><label><input name="manga_tracking_enabled" type="checkbox" value="1" <?php checked( get_post_meta( $post->ID, 'manga_tracking_enabled', true ) ); ?>> <?php esc_html_e( '新刊追跡を有効にする', 'manga-shelf' ); ?></label></p>
		<?php
	}

	/**
	 * Render a URL field.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key     Meta key.
	 * @param string $label   Field label.
	 * @return void
	 */
	private function url_field( $post_id, $key, $label ) {
		?>
		<p>
			<label for="<?php echo esc_attr( $key ); ?>"><strong><?php echo esc_html( $label ); ?></strong></label><br>
			<input class="widefat" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" type="url" value="<?php echo esc_attr( get_post_meta( $post_id, $key, true ) ); ?>">
		</p>
		<?php
	}

	/**
	 * Save submitted metadata.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function save( $post_id ) {
		$nonce = isset( $_POST['manga_shelf_details_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['manga_shelf_details_nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'manga_shelf_save_details' ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$text_fields = array( 'manga_reading_status' );
		$url_fields  = array( 'manga_official_url', 'manga_sample_url' );
		foreach ( $text_fields as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				update_post_meta( $post_id, $key, sanitize_key( wp_unslash( $_POST[ $key ] ) ) );
			}
		}
		foreach ( $url_fields as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				update_post_meta( $post_id, $key, esc_url_raw( wp_unslash( $_POST[ $key ] ) ) );
			}
		}
		if ( isset( $_POST['manga_rating'] ) ) {
			$rating = sanitize_text_field( wp_unslash( $_POST['manga_rating'] ) );
			update_post_meta( $post_id, 'manga_rating', min( 5, max( 0, (float) $rating ) ) );
		}
		update_post_meta( $post_id, 'manga_tracking_enabled', isset( $_POST['manga_tracking_enabled'] ) );
	}
}
