<?php
/**
 * Amazon Associates disclosure.
 *
 * @package MangaShelf
 */

namespace MangaShelf\Integrations\Amazon;

/**
 * Prints the required site identification once when affiliate links are used.
 */
final class Attribution {
	/**
	 * Whether the disclosure was already printed.
	 *
	 * @var bool
	 */
	private static $rendered = false;

	/**
	 * Render the disclosure once when a tracking ID is configured.
	 *
	 * @return void
	 */
	public static function render_once() {
		if ( self::$rendered || ! Settings::tracking_id() ) {
			return;
		}
		self::$rendered = true;
		?>
		<p class="manga-shelf-amazon-disclosure">
			<?php
			echo esc_html(
				sprintf(
					/* translators: %s: site name. */
					__( 'Amazonのアソシエイトとして、%sは適格販売により収入を得ています。', 'manga-shelf' ),
					get_bloginfo( 'name' )
				)
			);
			?>
		</p>
		<?php
	}
}
