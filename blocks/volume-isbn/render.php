<?php
/**
 * Volume ISBN render template.
 *
 * @package MangaShelf
 */

$volume = isset( $block->context['mangaShelf/volume'] ) ? $block->context['mangaShelf/volume'] : array();
$isbn   = isset( $volume['isbn13'] ) ? $volume['isbn13'] : '';
if ( ! $isbn ) {
	return;
}

$prefix = isset( $attributes['prefix'] ) ? $attributes['prefix'] : 'ISBN：';
?>
<span <?php echo wp_kses_data( get_block_wrapper_attributes( array( 'class' => 'manga-shelf-volume-isbn' ) ) ); ?>><?php echo esc_html( $prefix . $isbn ); ?></span>
