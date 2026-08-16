<?php
/**
 * Volume release date render template.
 *
 * @package MangaShelf
 */

$volume       = isset( $block->context['mangaShelf/volume'] ) ? $block->context['mangaShelf/volume'] : array();
$release_date = isset( $volume['release_date'] ) ? $volume['release_date'] : '';
if ( ! $release_date ) {
	return;
}

$prefix = isset( $attributes['prefix'] ) ? $attributes['prefix'] : __( '発売日：', 'manga-shelf' );
?>
<time <?php echo wp_kses_data( get_block_wrapper_attributes( array( 'class' => 'manga-shelf-volume-release-date' ) ) ); ?>><?php echo esc_html( $prefix . $release_date ); ?></time>
