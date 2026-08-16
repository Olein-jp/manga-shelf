<?php
/**
 * Migration tools for legacy locally stored Rakuten covers.
 *
 * @package MangaShelf
 */

namespace MangaShelf\Admin;

use MangaShelf\Database\Volumes;

/**
 * Provides an explicit two-step migration for imported cover attachments.
 */
final class CoverMigration {
	const LEGACY_ATTACHMENT_META = '_manga_shelf_legacy_cover';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'add_page' ) );
		add_action( 'admin_post_manga_shelf_migrate_covers', array( $this, 'migrate' ) );
		add_action( 'admin_post_manga_shelf_delete_legacy_covers', array( $this, 'delete_legacy_covers' ) );
	}

	/**
	 * Add the migration page.
	 *
	 * @return void
	 */
	public function add_page() {
		add_submenu_page(
			'edit.php?post_type=manga',
			__( '楽天書影の移行', 'manga-shelf' ),
			__( '楽天書影の移行', 'manga-shelf' ),
			'manage_options',
			'manga-shelf-cover-migration',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Render the migration and cleanup controls.
	 *
	 * @return void
	 */
	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$candidates = $this->migration_candidates();
		$legacy     = $this->legacy_attachment_ids();
		$migrated   = isset( $_GET['manga_shelf_migrated'] ) ? absint( wp_unslash( $_GET['manga_shelf_migrated'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only status after a nonce-protected action.
		$deleted    = isset( $_GET['manga_shelf_deleted'] ) ? absint( wp_unslash( $_GET['manga_shelf_deleted'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only status after a nonce-protected action.
		$error      = isset( $_GET['manga_shelf_error'] ) ? sanitize_key( wp_unslash( $_GET['manga_shelf_error'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only status after a nonce-protected action.
		?>
		<div class="wrap">
			<h1><?php esc_html_e( '楽天書影の安全な表示への移行', 'manga-shelf' ); ?></h1>
			<?php if ( $migrated ) : ?>
				<div class="notice notice-success"><p><?php /* translators: %d: number of migrated covers. */ echo esc_html( sprintf( __( '%d件の書影を外部URL表示へ切り替えました。', 'manga-shelf' ), $migrated ) ); ?></p></div>
			<?php endif; ?>
			<?php if ( $deleted ) : ?>
				<div class="notice notice-success"><p><?php /* translators: %d: number of deleted covers. */ echo esc_html( sprintf( __( '%d件の旧ローカル書影を削除しました。', 'manga-shelf' ), $deleted ) ); ?></p></div>
			<?php endif; ?>
			<?php if ( 'confirmation' === $error ) : ?>
				<div class="notice notice-error"><p><?php esc_html_e( '完全削除を実行するには確認チェックが必要です。', 'manga-shelf' ); ?></p></div>
			<?php endif; ?>

			<p><?php esc_html_e( '以前のバージョンがメディアライブラリへ保存した楽天書影を、楽天の画像URLと商品リンクを使う表示へ切り替えます。最初の操作ではローカルファイルを削除しません。', 'manga-shelf' ); ?></p>

			<h2><?php esc_html_e( '手順1：外部URL表示へ切り替える', 'manga-shelf' ); ?></h2>
			<?php if ( $candidates ) : ?>
				<table class="widefat striped">
					<thead><tr><th><?php esc_html_e( '作品', 'manga-shelf' ); ?></th><th><?php esc_html_e( 'メディアID', 'manga-shelf' ); ?></th><th><?php esc_html_e( '移行後の商品リンク', 'manga-shelf' ); ?></th></tr></thead>
					<tbody>
					<?php foreach ( $candidates as $candidate ) : ?>
						<tr>
							<td><a href="<?php echo esc_url( get_edit_post_link( $candidate['manga_id'] ) ); ?>"><?php echo esc_html( get_the_title( $candidate['manga_id'] ) ); ?></a></td>
							<td><?php echo esc_html( $candidate['attachment_id'] ); ?></td>
							<td><?php echo $candidate['product_url'] ? esc_html( $candidate['product_url'] ) : esc_html__( '商品リンクがないため書影は表示されません', 'manga-shelf' ); ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
					<input type="hidden" name="action" value="manga_shelf_migrate_covers">
					<?php wp_nonce_field( 'manga_shelf_migrate_covers' ); ?>
					<?php submit_button( __( '外部URL表示へ切り替える', 'manga-shelf' ) ); ?>
				</form>
			<?php else : ?>
				<p><?php esc_html_e( '移行対象の楽天書影はありません。', 'manga-shelf' ); ?></p>
			<?php endif; ?>

			<h2><?php esc_html_e( '手順2：旧ローカル書影を削除する', 'manga-shelf' ); ?></h2>
			<?php if ( $legacy ) : ?>
				<p><?php /* translators: %d: number of legacy covers. */ echo esc_html( sprintf( __( '外部URLへ切り替え済みのローカル書影が%d件あります。次の操作はファイルを完全に削除し、元に戻せません。先にバックアップと公開画面を確認してください。', 'manga-shelf' ), count( $legacy ) ) ); ?></p>
				<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
					<input type="hidden" name="action" value="manga_shelf_delete_legacy_covers">
					<?php wp_nonce_field( 'manga_shelf_delete_legacy_covers' ); ?>
					<p><label><input name="confirm_delete" required type="checkbox" value="1"> <?php esc_html_e( '対象が楽天から取り込まれた旧書影であることを確認し、完全削除に同意します。', 'manga-shelf' ); ?></label></p>
					<?php submit_button( __( '旧ローカル書影を完全削除', 'manga-shelf' ), 'delete' ); ?>
				</form>
			<?php else : ?>
				<p><?php esc_html_e( '削除待ちの旧ローカル書影はありません。', 'manga-shelf' ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Switch legacy featured images to external Rakuten URLs.
	 *
	 * @return void
	 */
	public function migrate() {
		$this->authorize( 'manga_shelf_migrate_covers' );
		$count = 0;
		foreach ( $this->migration_candidates() as $candidate ) {
			update_post_meta( $candidate['manga_id'], 'manga_cover_image_url', $candidate['source_url'] );
			if ( $candidate['product_url'] ) {
				update_post_meta( $candidate['manga_id'], 'manga_cover_product_url', $candidate['product_url'] );
			}
			delete_post_thumbnail( $candidate['manga_id'] );
			update_post_meta( $candidate['attachment_id'], self::LEGACY_ATTACHMENT_META, $candidate['manga_id'] );
			++$count;
		}
		$this->redirect( array( 'manga_shelf_migrated' => $count ) );
	}

	/**
	 * Permanently delete attachments explicitly marked by the migration.
	 *
	 * @return void
	 */
	public function delete_legacy_covers() {
		$this->authorize( 'manga_shelf_delete_legacy_covers' );
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- authorize() verifies this action's nonce immediately above.
		$confirmed = isset( $_POST['confirm_delete'] ) && '1' === sanitize_text_field( wp_unslash( $_POST['confirm_delete'] ) );
		if ( ! $confirmed ) {
			$this->redirect( array( 'manga_shelf_error' => 'confirmation' ) );
		}

		$count = 0;
		foreach ( $this->legacy_attachment_ids() as $attachment_id ) {
			if ( $this->is_rakuten_image_url( get_post_meta( $attachment_id, '_source_url', true ) ) && wp_delete_attachment( $attachment_id, true ) ) {
				++$count;
			}
		}
		$this->redirect( array( 'manga_shelf_deleted' => $count ) );
	}

	/**
	 * Resolve safely identifiable legacy imports.
	 *
	 * @return array
	 */
	private function migration_candidates() {
		$manga_ids  = get_posts(
			array(
				'post_type'      => 'manga',
				'post_status'    => 'any',
				'fields'         => 'ids',
				'posts_per_page' => -1,
				'meta_key'       => '_thumbnail_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			)
		);
		$candidates = array();
		foreach ( $manga_ids as $manga_id ) {
			$attachment_id = get_post_thumbnail_id( $manga_id );
			$source_url    = get_post_meta( $attachment_id, '_source_url', true );
			if ( ! $attachment_id || ! $this->is_rakuten_image_url( $source_url ) ) {
				continue;
			}
			$candidates[] = array(
				'manga_id'      => (int) $manga_id,
				'attachment_id' => (int) $attachment_id,
				'source_url'    => esc_url_raw( $source_url ),
				'product_url'   => $this->product_url( $manga_id ),
			);
		}
		return $candidates;
	}

	/**
	 * Find attachments marked during the first migration step.
	 *
	 * @return array
	 */
	private function legacy_attachment_ids() {
		return get_posts(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'any',
				'fields'         => 'ids',
				'posts_per_page' => -1,
				'meta_key'       => self::LEGACY_ATTACHMENT_META, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			)
		);
	}

	/**
	 * Find the first available Rakuten product URL for a manga.
	 *
	 * @param int $manga_id Manga post ID.
	 * @return string
	 */
	private function product_url( $manga_id ) {
		foreach ( ( new Volumes() )->for_manga( $manga_id ) as $volume ) {
			if ( $volume->rakuten_product_url ) {
				return esc_url_raw( $volume->rakuten_product_url );
			}
		}
		return '';
	}

	/**
	 * Limit migration to the known Rakuten thumbnail host.
	 *
	 * @param string $url Source URL.
	 * @return bool
	 */
	private function is_rakuten_image_url( $url ) {
		return 'thumbnail.image.rakuten.co.jp' === wp_parse_url( $url, PHP_URL_HOST );
	}

	/**
	 * Check capability and nonce for a migration action.
	 *
	 * @param string $action Nonce action.
	 * @return void
	 */
	private function authorize( $action ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'この操作を実行する権限がありません。', 'manga-shelf' ) );
		}
		check_admin_referer( $action );
	}

	/**
	 * Redirect back to the migration screen.
	 *
	 * @param array $args Query arguments.
	 * @return void
	 */
	private function redirect( array $args ) {
		$url = add_query_arg( $args, admin_url( 'edit.php?post_type=manga&page=manga-shelf-cover-migration' ) );
		wp_safe_redirect( $url );
		exit;
	}
}
