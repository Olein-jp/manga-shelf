<?php
/**
 * External Rakuten cover render template.
 *
 * @package MangaShelf
 */

$manga_id    = isset( $block->context['postId'] ) ? (int) $block->context['postId'] : get_the_ID();
$image_url   = get_post_meta( $manga_id, 'manga_cover_image_url', true );
$product_url = get_post_meta( $manga_id, 'manga_cover_product_url', true );

if ( ! \MangaShelf\Integrations\Rakuten\UrlPolicy::is_image( $image_url ) || ! \MangaShelf\Integrations\Rakuten\UrlPolicy::is_product( $product_url ) ) {
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
