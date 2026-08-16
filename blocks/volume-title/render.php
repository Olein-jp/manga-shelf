<?php
/**
 * Volume title render template.
 *
 * @package MangaShelf
 */

use MangaShelf\Integrations\Rakuten\Attribution;
use MangaShelf\Integrations\Rakuten\UrlPolicy;

$volume       = isset( $block->context['mangaShelf/volume'] ) ? $block->context['mangaShelf/volume'] : array();
$volume_title = isset( $volume['title'] ) ? $volume['title'] : '';
if ( ! $volume_title ) {
	return;
}

$tags        = array(
	0 => 'div',
	2 => 'h2',
	3 => 'h3',
	4 => 'h4',
	7 => 'p',
);
$level       = isset( $attributes['level'] ) ? (int) $attributes['level'] : 3;
$html_tag    = isset( $tags[ $level ] ) ? $tags[ $level ] : 'h3';
$product_url = isset( $volume['rakuten_product_url'] ) ? $volume['rakuten_product_url'] : '';
$has_link    = ! empty( $attributes['linkToProduct'] ) && UrlPolicy::is_product( $product_url );
?>
<<?php echo tag_escape( $html_tag ); ?> <?php echo wp_kses_data( get_block_wrapper_attributes( array( 'class' => 'manga-shelf-volume-title' ) ) ); ?>>
	<?php if ( $has_link ) : ?>
		<a href="<?php echo esc_url( $product_url ); ?>" rel="nofollow sponsored noopener" target="_blank">
	<?php endif; ?>
	<?php echo esc_html( $volume_title ); ?>
	<?php if ( $has_link ) : ?>
		</a>
	<?php endif; ?>
</<?php echo tag_escape( $html_tag ); ?>>
<?php
if ( $has_link ) {
	Attribution::render_once();
}
?>
