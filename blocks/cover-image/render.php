<?php
/**
 * External Rakuten cover render template.
 *
 * @package MangaShelf
 */

$manga_id           = isset( $block->context['postId'] ) ? (int) $block->context['postId'] : get_the_ID();
$image_url          = get_post_meta( $manga_id, 'manga_cover_image_url', true );
$product_url        = get_post_meta( $manga_id, 'manga_cover_product_url', true );
$image_host         = strtolower( (string) wp_parse_url( $image_url, PHP_URL_HOST ) );
$product_host       = strtolower( (string) wp_parse_url( $product_url, PHP_URL_HOST ) );
$is_rakuten_product = 'rakuten.co.jp' === $product_host || '.rakuten.co.jp' === substr( $product_host, -14 );

if ( 'thumbnail.image.rakuten.co.jp' !== $image_host || ! $is_rakuten_product ) {
	return;
}

$alt = sprintf(
	/* translators: %s: manga title. */
	__( '%sの書影', 'manga-shelf' ),
	get_the_title( $manga_id )
);
?>
<figure <?php echo wp_kses_data( get_block_wrapper_attributes( array( 'class' => 'manga-shelf-cover-image' ) ) ); ?>>
	<a href="<?php echo esc_url( $product_url ); ?>" rel="nofollow sponsored noopener" target="_blank">
		<img alt="<?php echo esc_attr( $alt ); ?>" decoding="async" loading="lazy" src="<?php echo esc_url( $image_url ); ?>">
	</a>
</figure>
<?php \MangaShelf\Integrations\Rakuten\Attribution::render_once(); ?>
