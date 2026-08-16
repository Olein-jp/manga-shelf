<?php
/**
 * Volume cover render template.
 *
 * @package MangaShelf
 */

use MangaShelf\Integrations\Rakuten\Attribution;
use MangaShelf\Integrations\Rakuten\UrlPolicy;

$volume      = isset( $block->context['mangaShelf/volume'] ) ? $block->context['mangaShelf/volume'] : array();
$image_url   = isset( $volume['rakuten_image_url'] ) ? $volume['rakuten_image_url'] : '';
$product_url = isset( $volume['rakuten_product_url'] ) ? $volume['rakuten_product_url'] : '';

if ( ! UrlPolicy::is_image( $image_url ) || ! UrlPolicy::is_product( $product_url ) ) {
	return;
}

$width = isset( $attributes['width'] ) ? min( 400, max( 60, (int) $attributes['width'] ) ) : 120;
$alt   = isset( $volume['title'] ) ? sprintf(
	/* translators: %s: volume title. */
	__( '%sの書影', 'manga-shelf' ),
	$volume['title']
) : '';
$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'manga-shelf-volume-cover',
		'style' => 'width:' . $width . 'px;',
	)
);
?>
<figure <?php echo wp_kses_data( $wrapper_attributes ); ?>>
	<a href="<?php echo esc_url( $product_url ); ?>" rel="nofollow sponsored noopener" target="_blank">
		<img alt="<?php echo esc_attr( $alt ); ?>" decoding="async" loading="lazy" src="<?php echo esc_url( $image_url ); ?>">
	</a>
</figure>
<?php Attribution::render_once(); ?>
