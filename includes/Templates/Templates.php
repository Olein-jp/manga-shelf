<?php
/**
 * Block patterns and plugin templates.
 *
 * @package MangaShelf
 */

namespace MangaShelf\Templates;

/**
 * Registers reusable manga layouts.
 */
final class Templates {
	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'init', array( $this, 'register_patterns' ) );
		add_action( 'init', array( $this, 'register_templates' ) );
	}

	/**
	 * Register the detail pattern.
	 *
	 * @return void
	 */
	public function register_patterns() {
		register_block_pattern_category( 'manga-shelf', array( 'label' => __( 'Manga Shelf', 'manga-shelf' ) ) );
		register_block_pattern(
			'manga-shelf/manga-details',
			array(
				'title'      => __( '漫画：作品詳細', 'manga-shelf' ),
				'categories' => array( 'manga-shelf' ),
				'postTypes'  => array( 'manga' ),
				'content'    => '<!-- wp:group {"layout":{"type":"constrained"}} --><div class="wp-block-group"><!-- wp:manga-shelf/cover-image /--><!-- wp:post-title {"level":1} /--><!-- wp:post-content /--><!-- wp:heading {"level":2} --><h2 class="wp-block-heading">' . esc_html__( '刊行情報', 'manga-shelf' ) . '</h2><!-- /wp:heading -->' . $this->volume_list_content() . '</div><!-- /wp:group -->',
			)
		);
	}

	/**
	 * Register optional templates when the current WordPress supports them.
	 *
	 * @return void
	 */
	public function register_templates() {
		if ( ! function_exists( 'register_block_template' ) ) {
			return;
		}

		register_block_template(
			'manga-shelf//single-manga',
			array(
				'title'       => __( 'Manga Shelf：個別作品', 'manga-shelf' ),
				'description' => __( '漫画作品の個別ページ用テンプレートです。', 'manga-shelf' ),
				'post_types'  => array( 'manga' ),
				'content'     => '<!-- wp:template-part {"slug":"header","tagName":"header"} /--><!-- wp:group {"tagName":"main","layout":{"type":"constrained"}} --><main class="wp-block-group"><!-- wp:pattern {"slug":"manga-shelf/manga-details"} /--></main><!-- /wp:group --><!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->',
			)
		);

		register_block_template(
			'manga-shelf//archive-manga',
			array(
				'title'       => __( 'Manga Shelf：作品一覧', 'manga-shelf' ),
				'description' => __( '漫画作品アーカイブ用テンプレートです。', 'manga-shelf' ),
				'post_types'  => array( 'manga' ),
				'content'     => '<!-- wp:template-part {"slug":"header","tagName":"header"} /--><!-- wp:group {"tagName":"main","layout":{"type":"constrained"}} --><main class="wp-block-group"><!-- wp:query-title {"type":"archive","showPrefix":false} /--><!-- wp:query {"query":{"perPage":12,"postType":"manga","order":"desc","orderBy":"date","inherit":true}} --><div class="wp-block-query"><!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} --><!-- wp:manga-shelf/cover-image /--><!-- wp:post-title {"isLink":true} /--><!-- /wp:post-template --><!-- wp:query-pagination /--></div><!-- /wp:query --></main><!-- /wp:group --><!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->',
			)
		);
	}

	/**
	 * Default customizable layout repeated for every volume.
	 *
	 * @return string
	 */
	private function volume_list_content() {
		return '<!-- wp:manga-shelf/volume-list -->'
			. '<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"top"}} --><div class="wp-block-group">'
			. '<!-- wp:manga-shelf/volume-cover {"width":120} /-->'
			. '<!-- wp:group {"layout":{"type":"constrained"}} --><div class="wp-block-group">'
			. '<!-- wp:manga-shelf/volume-title {"level":3} /-->'
			. '<!-- wp:manga-shelf/volume-number /-->'
			. '<!-- wp:manga-shelf/volume-release-date /-->'
			. '<!-- wp:manga-shelf/volume-purchase-link /-->'
			. '<!-- wp:manga-shelf/volume-amazon-link /-->'
			. '</div><!-- /wp:group -->'
			. '</div><!-- /wp:group -->'
			. '<!-- /wp:manga-shelf/volume-list -->';
	}
}
