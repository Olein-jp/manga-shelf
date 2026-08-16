<?php
/**
 * Rakuten API settings.
 *
 * @package MangaShelf
 */

namespace MangaShelf\Integrations\Rakuten;

/**
 * Registers API settings and their screen.
 */
final class Settings {
	const APPLICATION_ID_OPTION = 'manga_shelf_rakuten_application_id';
	const ACCESS_KEY_OPTION     = 'manga_shelf_rakuten_access_key';
	const AFFILIATE_ID_OPTION   = 'manga_shelf_rakuten_affiliate_id';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_page' ) );
		add_action( 'admin_post_manga_shelf_test_rakuten', array( $this, 'test_connection' ) );
	}

	/**
	 * Register options.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting( 'manga_shelf_rakuten', self::APPLICATION_ID_OPTION, array( 'sanitize_callback' => array( $this, 'sanitize_credential' ) ) );
		register_setting( 'manga_shelf_rakuten', self::ACCESS_KEY_OPTION, array( 'sanitize_callback' => array( $this, 'sanitize_credential' ) ) );
		register_setting( 'manga_shelf_rakuten', self::AFFILIATE_ID_OPTION, array( 'sanitize_callback' => array( $this, 'sanitize_credential' ) ) );
	}

	/**
	 * Add the submenu.
	 *
	 * @return void
	 */
	public function add_page() {
		add_submenu_page(
			'edit.php?post_type=manga',
			__( 'Manga Shelf 設定', 'manga-shelf' ),
			__( '楽天API設定', 'manga-shelf' ),
			'manage_options',
			'manga-shelf-settings',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Render settings form.
	 *
	 * @return void
	 */
	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Manga Shelf 楽天API設定', 'manga-shelf' ); ?></h1>
			<?php settings_errors( 'manga_shelf_rakuten' ); ?>
			<p><?php esc_html_e( '楽天Web ServiceのアプリケーションIDとAccess Keyが必要です。wp-config.phpの定数を設定すると、保存値より優先されます。', 'manga-shelf' ); ?></p>
			<form action="options.php" method="post">
				<?php settings_fields( 'manga_shelf_rakuten' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="manga-shelf-access-key"><?php esc_html_e( 'Access Key', 'manga-shelf' ); ?></label></th>
						<td><input class="regular-text" id="manga-shelf-access-key" name="<?php echo esc_attr( self::ACCESS_KEY_OPTION ); ?>" type="password" value="<?php echo esc_attr( get_option( self::ACCESS_KEY_OPTION, '' ) ); ?>" autocomplete="off"></td>
					</tr>
					<tr>
						<th scope="row"><label for="manga-shelf-application-id"><?php esc_html_e( 'アプリケーションID', 'manga-shelf' ); ?></label></th>
						<td><input class="regular-text" id="manga-shelf-application-id" name="<?php echo esc_attr( self::APPLICATION_ID_OPTION ); ?>" type="password" value="<?php echo esc_attr( get_option( self::APPLICATION_ID_OPTION, '' ) ); ?>" autocomplete="off"></td>
					</tr>
					<tr>
						<th scope="row"><label for="manga-shelf-affiliate-id"><?php esc_html_e( 'アフィリエイトID（任意）', 'manga-shelf' ); ?></label></th>
						<td><input class="regular-text" id="manga-shelf-affiliate-id" name="<?php echo esc_attr( self::AFFILIATE_ID_OPTION ); ?>" type="text" value="<?php echo esc_attr( get_option( self::AFFILIATE_ID_OPTION, '' ) ); ?>"></td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
			<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
				<input type="hidden" name="action" value="manga_shelf_test_rakuten">
				<?php wp_nonce_field( 'manga_shelf_test_rakuten' ); ?>
				<?php submit_button( __( '接続を確認', 'manga-shelf' ), 'secondary' ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Run a small authenticated test request.
	 *
	 * @return void
	 */
	public function test_connection() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'この操作を実行する権限がありません。', 'manga-shelf' ) );
		}
		check_admin_referer( 'manga_shelf_test_rakuten' );

		$result = ( new Client() )->search_books( 'ONE PIECE', 1 );
		$type   = is_wp_error( $result ) ? 'error' : 'success';
		$text   = is_wp_error( $result ) ? $result->get_error_message() : __( '楽天ブックスAPIに接続できました。', 'manga-shelf' );
		add_settings_error( 'manga_shelf_rakuten', 'connection', $text, $type );
		set_transient( 'settings_errors', get_settings_errors( 'manga_shelf_rakuten' ), 30 );
		wp_safe_redirect( admin_url( 'edit.php?post_type=manga&page=manga-shelf-settings&settings-updated=true' ) );
		exit;
	}

	/**
	 * Resolve the application ID.
	 *
	 * @return string
	 */
	public static function application_id() {
		$value = defined( 'MANGA_SHELF_RAKUTEN_APPLICATION_ID' ) ? (string) MANGA_SHELF_RAKUTEN_APPLICATION_ID : (string) get_option( self::APPLICATION_ID_OPTION, '' );
		return trim( $value );
	}

	/**
	 * Resolve the access key.
	 *
	 * @return string
	 */
	public static function access_key() {
		$value = defined( 'MANGA_SHELF_RAKUTEN_ACCESS_KEY' ) ? (string) MANGA_SHELF_RAKUTEN_ACCESS_KEY : (string) get_option( self::ACCESS_KEY_OPTION, '' );
		return trim( $value );
	}

	/**
	 * Resolve the affiliate ID.
	 *
	 * @return string
	 */
	public static function affiliate_id() {
		$value = defined( 'MANGA_SHELF_RAKUTEN_AFFILIATE_ID' ) ? (string) MANGA_SHELF_RAKUTEN_AFFILIATE_ID : (string) get_option( self::AFFILIATE_ID_OPTION, '' );
		return trim( $value );
	}

	/**
	 * Sanitize a credential without changing valid punctuation.
	 *
	 * @param mixed $value Submitted value.
	 * @return string
	 */
	public function sanitize_credential( $value ) {
		return trim( sanitize_text_field( $value ) );
	}
}
