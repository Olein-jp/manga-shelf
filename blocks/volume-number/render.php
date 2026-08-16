<?php
/**
 * Volume number render template.
 *
 * @package MangaShelf
 */

$volume = isset( $block->context['mangaShelf/volume'] ) ? $block->context['mangaShelf/volume'] : array();
if ( ! isset( $volume['volume_number'] ) || null === $volume['volume_number'] ) {
	return;
}

$number = (float) $volume['volume_number'];
$number = floor( $number ) === $number ? (string) (int) $number : rtrim( rtrim( number_format( $number, 2, '.', '' ), '0' ), '.' );
$prefix = isset( $attributes['prefix'] ) ? $attributes['prefix'] : '第';
$suffix = isset( $attributes['suffix'] ) ? $attributes['suffix'] : '巻';
?>
<span <?php echo wp_kses_data( get_block_wrapper_attributes( array( 'class' => 'manga-shelf-volume-number' ) ) ); ?>><?php echo esc_html( $prefix . $number . $suffix ); ?></span>
