<?php
/**
 * Volume list render template.
 *
 * @package MangaShelf
 */

use MangaShelf\Database\Volumes;

$manga_id = isset( $block->context['postId'] ) ? (int) $block->context['postId'] : get_the_ID();
$volumes  = ( new Volumes() )->for_manga( $manga_id );

if ( ! $volumes ) {
	return;
}
?>
<div <?php echo wp_kses_data( get_block_wrapper_attributes( array( 'class' => 'manga-shelf-volume-list' ) ) ); ?>>
	<ul class="manga-shelf-volume-list__items">
		<?php foreach ( $volumes as $volume ) : ?>
			<li class="manga-shelf-volume-list__item">
				<span class="manga-shelf-volume-list__title"><?php echo esc_html( $volume->title ); ?></span>
				<?php if ( ! empty( $attributes['showReleaseDate'] ) && $volume->release_date ) : ?>
					<time class="manga-shelf-volume-list__date"><?php echo esc_html( $volume->release_date ); ?></time>
				<?php endif; ?>
				<?php if ( ! empty( $attributes['showPurchaseLink'] ) && $volume->rakuten_product_url ) : ?>
					<a class="manga-shelf-volume-list__link" href="<?php echo esc_url( $volume->rakuten_product_url ); ?>" rel="nofollow sponsored noopener" target="_blank"><?php esc_html_e( '楽天で見る', 'manga-shelf' ); ?></a>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
