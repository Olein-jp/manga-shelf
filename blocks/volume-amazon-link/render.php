<?php
/**
 * Volume Amazon link render template.
 *
 * @package MangaShelf
 */

use MangaShelf\Integrations\Amazon\Attribution;
use MangaShelf\Integrations\Amazon\Link;
use MangaShelf\Integrations\Amazon\Settings;

$volume = isset( $block->context['mangaShelf/volume'] ) ? $block->context['mangaShelf/volume'] : array();
$isbn   = isset( $volume['isbn13'] ) ? $volume['isbn13'] : '';
$url    = Link::for_isbn( $isbn );
if ( ! Link::is_allowed( $url ) ) {
	return;
}

$label = isset( $attributes['label'] ) && $attributes['label'] ? $attributes['label'] : __( 'Amazonで見る', 'manga-shelf' );
$rel   = Settings::tracking_id() ? 'nofollow sponsored noopener' : 'nofollow noopener';
?>
<a <?php echo wp_kses_data( get_block_wrapper_attributes( array( 'class' => 'manga-shelf-volume-amazon-link' ) ) ); ?> href="<?php echo esc_url( $url ); ?>" rel="<?php echo esc_attr( $rel ); ?>" target="_blank"><?php echo esc_html( $label ); ?></a>
<?php if ( Settings::tracking_id() ) : ?>
	<span class="manga-shelf-affiliate-disclosure"><?php esc_html_e( '（広告）', 'manga-shelf' ); ?></span>
<?php endif; ?>
<?php Attribution::render_once(); ?>
