<?php
/**
 * Amazon Associates settings.
 *
 * @package MangaShelf
 */

namespace MangaShelf\Integrations\Amazon;

/**
 * Registers the optional Amazon Associates tracking ID.
 */
final class Settings {
	const TRACKING_ID_OPTION = 'manga_shelf_amazon_tracking_id';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_page' ) );
	}

	/**
	 * Register the tracking ID option.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			'manga_shelf_amazon',
			self::TRACKING_ID_OPTION,
			array( 'sanitize_callback' => array( $this, 'sanitize_tracking_id' ) )
		);
	}

	/**
	 * Add the settings submenu.
	 *
	 * @return void
	 */
	public function add_page() {
		add_submenu_page(
			'edit.php?post_type=manga',
			__( 'Manga Shelf Amazon設定', 'manga-shelf' ),
			__( 'Amazon設定', 'manga-shelf' ),
			'manage_options',
			'manga-shelf-amazon-settings',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Render the settings form.
	 *
	 * @return void
	 */
	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Manga Shelf Amazon設定', 'manga-shelf' ); ?></h1>
			<p><?php esc_html_e( '各巻のISBNを使ってAmazon.co.jpの書籍検索リンクを作成します。トラッキングIDを設定しない場合は通常のAmazonリンクになります。', 'manga-shelf' ); ?></p>
			<form action="options.php" method="post">
				<?php settings_fields( 'manga_shelf_amazon' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="manga-shelf-amazon-tracking-id"><?php esc_html_e( 'アソシエイト・トラッキングID', 'manga-shelf' ); ?></label></th>
						<td>
							<input class="regular-text" id="manga-shelf-amazon-tracking-id" name="<?php echo esc_attr( self::TRACKING_ID_OPTION ); ?>" type="text" value="<?php echo esc_attr( get_option( self::TRACKING_ID_OPTION, '' ) ); ?>" placeholder="example-22">
							<p class="description"><?php esc_html_e( 'wp-config.phpの MANGA_SHELF_AMAZON_TRACKING_ID 定数を設定すると、保存値より優先されます。', 'manga-shelf' ); ?></p>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
			<p><?php esc_html_e( 'アフィリエイトリンクを使う場合は、Amazonアソシエイト・プログラムへの参加と、サイト情報・プライバシーポリシー等への適切な開示も確認してください。', 'manga-shelf' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Resolve the tracking ID.
	 *
	 * @return string
	 */
	public static function tracking_id() {
		$value = defined( 'MANGA_SHELF_AMAZON_TRACKING_ID' ) ? (string) MANGA_SHELF_AMAZON_TRACKING_ID : (string) get_option( self::TRACKING_ID_OPTION, '' );
		return preg_replace( '/[^A-Za-z0-9_-]/', '', trim( $value ) );
	}

	/**
	 * Keep only characters accepted in tracking IDs.
	 *
	 * @param mixed $value Submitted value.
	 * @return string
	 */
	public function sanitize_tracking_id( $value ) {
		return preg_replace( '/[^A-Za-z0-9_-]/', '', trim( sanitize_text_field( $value ) ) );
	}
}
