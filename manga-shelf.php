<?php
/**
 * Plugin Name:       Manga Shelf
 * Plugin URI:        https://github.com/Olein-jp/manga-shelf
 * Description:       漫画を管理するための WordPress プラグインです。
 * Version:           0.1.3-dev
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            Koji Kuno
 * Author URI:        https://olein-design.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       manga-shelf
 * Update URI:        https://github.com/Olein-jp/manga-shelf
 *
 * @package MangaShelf
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/vendor/autoload.php';

define( 'MANGA_SHELF_VERSION', '0.1.3-dev' );
define( 'MANGA_SHELF_FILE', __FILE__ );
define( 'MANGA_SHELF_PATH', plugin_dir_path( __FILE__ ) );
define( 'MANGA_SHELF_URL', plugin_dir_url( __FILE__ ) );

register_activation_hook( __FILE__, array( 'MangaShelf\\Database\\Schema', 'activate' ) );

add_action(
	'plugins_loaded',
	static function () {
		MangaShelf\Plugin::instance()->register();
	}
);

new Inc2734\WP_GitHub_Plugin_Updater\Bootstrap(
	plugin_basename( __FILE__ ),
	'Olein-jp',
	'manga-shelf',
	array(
		'homepage'     => 'https://github.com/Olein-jp/manga-shelf',
		'requires'     => '6.7',
		'requires_php' => '7.4',
	)
);
