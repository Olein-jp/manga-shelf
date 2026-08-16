<?php
/**
 * Main plugin coordinator.
 *
 * @package MangaShelf
 */

namespace MangaShelf;

use MangaShelf\Admin\CoverMigration;
use MangaShelf\Admin\MangaDetails;
use MangaShelf\Admin\MangaImport;
use MangaShelf\Bindings\Bindings;
use MangaShelf\Blocks\Blocks;
use MangaShelf\Content\Meta;
use MangaShelf\Content\PostType;
use MangaShelf\Content\Taxonomies;
use MangaShelf\Database\Schema;
use MangaShelf\Integrations\Rakuten\Attribution;
use MangaShelf\Integrations\Rakuten\Settings;
use MangaShelf\Templates\Templates;

/**
 * Registers the plugin services.
 */
final class Plugin {
	/**
	 * Shared instance.
	 *
	 * @var Plugin|null
	 */
	private static $instance;

	/**
	 * Get the shared instance.
	 *
	 * @return Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		( new PostType() )->register();
		( new Taxonomies() )->register();
		( new Meta() )->register();
		( new Schema() )->register();
		( new Settings() )->register();
		( new \MangaShelf\Integrations\Amazon\Settings() )->register();
		( new Attribution() )->register();
		( new MangaDetails() )->register();
		( new MangaImport() )->register();
		( new CoverMigration() )->register();
		( new Bindings() )->register();
		( new Blocks() )->register();
		( new Templates() )->register();
	}
}
