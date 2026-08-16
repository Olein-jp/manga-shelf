<?php
/**
 * Plugin Name:       Manga Shelf
 * Plugin URI:        https://github.com/Olein-jp/manga-shelf
 * Description:       漫画を管理するための WordPress プラグインです。
 * Version:           0.0.0-dev
 * Requires at least: 5.9
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

new Inc2734\WP_GitHub_Plugin_Updater\Bootstrap(
	plugin_basename( __FILE__ ),
	'Olein-jp',
	'manga-shelf',
	array(
		'homepage'     => 'https://github.com/Olein-jp/manga-shelf',
		'requires'     => '5.9',
		'requires_php' => '7.4',
	)
);
