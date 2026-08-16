<?php
/**
 * Volume list render template.
 *
 * @package MangaShelf
 */

use MangaShelf\Database\Volumes;
use MangaShelf\Integrations\Rakuten\Attribution;
use MangaShelf\Integrations\Rakuten\UrlPolicy;

$manga_id = isset( $block->context['postId'] ) ? (int) $block->context['postId'] : get_the_ID();
$volumes  = ( new Volumes() )->for_manga( $manga_id );

if ( ! $volumes ) {
	return;
}
$has_rakuten_data = false;
foreach ( $volumes as $volume ) {
	if ( 'rakuten' === $volume->source ) {
		$has_rakuten_data = true;
		break;
	}
}
?>
<div <?php echo wp_kses_data( get_block_wrapper_attributes( array( 'class' => 'manga-shelf-volume-list' ) ) ); ?>>
	<ul class="manga-shelf-volume-list__items">
		<?php foreach ( $volumes as $volume ) : ?>
			<li class="manga-shelf-volume-list__item">
				<?php if ( ! empty( $block->parsed_block['innerBlocks'] ) ) : ?>
					<?php
					$context                      = $block->context;
					$context['mangaShelf/volume'] = (array) $volume;
					foreach ( $block->parsed_block['innerBlocks'] as $inner_block ) {
						echo ( new \WP_Block( $inner_block, $context, \WP_Block_Type_Registry::get_instance() ) )->render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Blocks escape their own output.
					}
					?>
				<?php else : ?>
					<span class="manga-shelf-volume-title"><?php echo esc_html( $volume->title ); ?></span>
					<?php if ( ! empty( $attributes['showReleaseDate'] ) && $volume->release_date ) : ?>
						<time class="manga-shelf-volume-release-date"><?php echo esc_html( $volume->release_date ); ?></time>
					<?php endif; ?>
					<?php if ( ! empty( $attributes['showPurchaseLink'] ) && UrlPolicy::is_product( $volume->rakuten_product_url ) ) : ?>
						<a class="manga-shelf-volume-purchase-link" href="<?php echo esc_url( $volume->rakuten_product_url ); ?>" rel="nofollow sponsored noopener" target="_blank"><?php esc_html_e( '楽天で見る', 'manga-shelf' ); ?></a>
					<?php endif; ?>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
<?php if ( $has_rakuten_data ) : ?>
	<?php Attribution::render_once(); ?>
<?php endif; ?>
