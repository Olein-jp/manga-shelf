<?php
/**
 * Volume purchase link render template.
 *
 * @package MangaShelf
 */

use MangaShelf\Integrations\Rakuten\Attribution;
use MangaShelf\Integrations\Rakuten\UrlPolicy;

$volume      = isset( $block->context['mangaShelf/volume'] ) ? $block->context['mangaShelf/volume'] : array();
$product_url = isset( $volume['rakuten_product_url'] ) ? $volume['rakuten_product_url'] : '';
if ( ! UrlPolicy::is_product( $product_url ) ) {
	return;
}

$label = isset( $attributes['label'] ) && $attributes['label'] ? $attributes['label'] : __( '楽天で見る', 'manga-shelf' );
?>
<a <?php echo wp_kses_data( get_block_wrapper_attributes( array( 'class' => 'manga-shelf-volume-purchase-link' ) ) ); ?> href="<?php echo esc_url( $product_url ); ?>" rel="nofollow sponsored noopener" target="_blank"><?php echo esc_html( $label ); ?></a>
<?php Attribution::render_once(); ?>
