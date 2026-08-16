<?php
/**
 * Rakuten Web Service attribution.
 *
 * @package MangaShelf
 */

namespace MangaShelf\Integrations\Rakuten;

/**
 * Displays the required unmodified attribution on manga views.
 */
final class Attribution {
	/**
	 * Whether the attribution was already printed in this request.
	 *
	 * @var bool
	 */
	private static $rendered = false;

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'wp_footer', array( $this, 'render' ) );
	}

	/**
	 * Render the official text attribution snippet.
	 *
	 * @return void
	 */
	public function render() {
		if ( ! is_singular( 'manga' ) && ! is_post_type_archive( 'manga' ) && ! is_tax( array( 'manga_author', 'manga_genre' ) ) ) {
			return;
		}

		self::render_once();
	}

	/**
	 * Render the official snippet once per request.
	 *
	 * @return void
	 */
	public static function render_once() {
		if ( self::$rendered ) {
			return;
		}
		self::$rendered = true;

		$attribution = '<!-- Rakuten Web Services Attribution Snippet FROM HERE -->'
			. '<a href="https://developers.rakuten.com/" target="_blank">Supported by Rakuten Developers</a>'
			. '<!-- Rakuten Web Services Attribution Snippet TO HERE -->';

		echo $attribution; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Rakuten requires the official snippet to remain unmodified.
	}
}
