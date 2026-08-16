<?php
/**
 * Manga import admin screen.
 *
 * @package MangaShelf
 */

namespace MangaShelf\Admin;

use MangaShelf\Integrations\Rakuten\Attribution;
use MangaShelf\Integrations\Rakuten\Client;
use MangaShelf\Integrations\Rakuten\VolumeSync;

/**
 * Searches Rakuten Books and imports a selected manga.
 */
final class MangaImport {
	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'add_page' ) );
		add_action( 'admin_post_manga_shelf_import', array( $this, 'import' ) );
	}

	/**
	 * Add the import page.
	 *
	 * @return void
	 */
	public function add_page() {
		add_submenu_page(
			'edit.php?post_type=manga',
			__( '楽天から漫画を追加', 'manga-shelf' ),
			__( '楽天から追加', 'manga-shelf' ),
			'edit_posts',
			'manga-shelf-import',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Render search form and results.
	 *
	 * @return void
	 */
	public function render_page() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		$query   = '';
		$results = array();
		$error   = null;
		if ( isset( $_POST['manga_shelf_search'] ) ) {
			check_admin_referer( 'manga_shelf_search' );
			$query = isset( $_POST['manga_shelf_query'] ) ? sanitize_text_field( wp_unslash( $_POST['manga_shelf_query'] ) ) : '';
			if ( $query ) {
				$results = ( new Client() )->search_books( $query );
				if ( is_wp_error( $results ) ) {
					$error   = $results;
					$results = array();
				}
			}
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( '楽天ブックスから漫画を追加', 'manga-shelf' ); ?></h1>
			<p><?php esc_html_e( '紙の通常版コミックスを選んでください。特装版・限定版・新装版は自動判定の対象外です。', 'manga-shelf' ); ?></p>
			<?php if ( $error ) : ?>
				<div class="notice notice-error"><p><?php echo esc_html( $error->get_error_message() ); ?></p></div>
			<?php endif; ?>
			<form method="post">
				<?php wp_nonce_field( 'manga_shelf_search' ); ?>
				<input type="hidden" name="manga_shelf_search" value="1">
				<label class="screen-reader-text" for="manga-shelf-query"><?php esc_html_e( '作品名', 'manga-shelf' ); ?></label>
				<input class="regular-text" id="manga-shelf-query" name="manga_shelf_query" placeholder="<?php esc_attr_e( '例：葬送のフリーレン', 'manga-shelf' ); ?>" required type="search" value="<?php echo esc_attr( $query ); ?>">
				<?php submit_button( __( '検索', 'manga-shelf' ), 'primary', 'submit', false ); ?>
			</form>

			<?php if ( $results ) : ?>
				<table class="widefat striped" style="margin-top: 1.5rem">
					<thead><tr><th><?php esc_html_e( '書影', 'manga-shelf' ); ?></th><th><?php esc_html_e( '書籍', 'manga-shelf' ); ?></th><th><?php esc_html_e( '発売日', 'manga-shelf' ); ?></th><th><?php esc_html_e( '操作', 'manga-shelf' ); ?></th></tr></thead>
					<tbody>
					<?php foreach ( $results as $item ) : ?>
						<tr>
							<td>
							<?php
							if ( $item['image_url'] && $item['product_url'] ) :
								?>
							<a href="<?php echo esc_url( $item['product_url'] ); ?>" rel="nofollow sponsored noopener" target="_blank"><img alt="" height="100" src="<?php echo esc_url( $item['image_url'] ); ?>"></a><?php endif; ?></td>
							<td><strong><?php echo esc_html( $item['title'] ); ?></strong><br><?php echo esc_html( $item['author'] ); ?> / <?php echo esc_html( $item['publisher'] ); ?><br>ISBN: <?php echo esc_html( $item['isbn13'] ); ?></td>
							<td><?php echo esc_html( $item['release_date'] ); ?></td>
							<td><?php $this->render_import_form( $item ); ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<p><?php Attribution::render_once(); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render the import action for one result.
	 *
	 * @param array $item Normalized API item.
	 * @return void
	 */
	private function render_import_form( array $item ) {
		$keys = array( 'title', 'series_title', 'volume_number', 'author', 'publisher', 'isbn13', 'release_date', 'item_code', 'product_url', 'image_url', 'date_precision' );
		?>
		<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
			<input type="hidden" name="action" value="manga_shelf_import">
			<?php wp_nonce_field( 'manga_shelf_import' ); ?>
			<?php foreach ( $keys as $key ) : ?>
				<input type="hidden" name="item[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( null === $item[ $key ] ? '' : $item[ $key ] ); ?>">
			<?php endforeach; ?>
			<?php submit_button( __( 'この作品を追加', 'manga-shelf' ), 'secondary', 'submit', false ); ?>
		</form>
		<?php
	}

	/**
	 * Import a manga and all matching volumes returned by the API.
	 *
	 * @return void
	 */
	public function import() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'この操作を実行する権限がありません。', 'manga-shelf' ) );
		}
		check_admin_referer( 'manga_shelf_import' );

		$raw  = isset( $_POST['item'] ) && is_array( $_POST['item'] ) ? wp_unslash( $_POST['item'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Every value is sanitized by sanitize_item().
		$item = $this->sanitize_item( $raw );
		if ( ! $item['series_title'] || ! $item['isbn13'] ) {
			wp_die( esc_html__( '作品データが不足しています。', 'manga-shelf' ) );
		}

		$post_id = wp_insert_post(
			array(
				'post_type'   => 'manga',
				'post_status' => 'draft',
				'post_title'  => $item['series_title'],
				'meta_input'  => array(
					'manga_publisher'        => $item['publisher'],
					'manga_reading_status'   => 'want-to-read',
					'manga_tracking_enabled' => true,
				),
			),
			true
		);
		if ( is_wp_error( $post_id ) ) {
			wp_die( esc_html( $post_id->get_error_message() ) );
		}

		if ( $item['author'] ) {
			wp_set_object_terms( $post_id, array_filter( array_map( 'trim', preg_split( '/[\/,、]/u', $item['author'] ) ) ), 'manga_author' );
		}

		$sync     = new VolumeSync();
		$imported = $sync->sync( $post_id, $item['series_title'] );
		if ( is_wp_error( $imported ) || 0 === $imported ) {
			$sync->save( $post_id, $item );
		}

		if ( $item['image_url'] && $item['product_url'] ) {
			update_post_meta( $post_id, 'manga_cover_image_url', $item['image_url'] );
			update_post_meta( $post_id, 'manga_cover_product_url', $item['product_url'] );
		}

		wp_safe_redirect( get_edit_post_link( $post_id, 'url' ) );
		exit;
	}

	/**
	 * Sanitize posted API data.
	 *
	 * @param array $raw Raw data.
	 * @return array
	 */
	private function sanitize_item( array $raw ) {
		$text_keys = array( 'title', 'series_title', 'author', 'publisher', 'isbn13', 'release_date', 'item_code', 'date_precision' );
		$item      = array();
		foreach ( $text_keys as $key ) {
			$item[ $key ] = isset( $raw[ $key ] ) ? sanitize_text_field( $raw[ $key ] ) : '';
		}
		$item['volume_number'] = isset( $raw['volume_number'] ) && '' !== $raw['volume_number'] ? (float) $raw['volume_number'] : null;
		$item['product_url']   = isset( $raw['product_url'] ) ? esc_url_raw( $raw['product_url'] ) : '';
		$item['image_url']     = isset( $raw['image_url'] ) ? esc_url_raw( $raw['image_url'] ) : '';
		return $item;
	}
}
