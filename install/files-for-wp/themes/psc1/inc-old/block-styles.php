<?php
/**
 * Block Styles
 *
 * @link https://developer.wordpress.org/reference/functions/register_block_style/
 *
 * @package WordPress
 * @subpackage PSC_1
 * @since PSC1 1.0
 */

if ( function_exists( 'register_block_style' ) ) {
	/**
	 * Register block styles.
	 *
	 * @since PSC1 1.0
	 *
	 * @return void
	 */
	function primary_source_one_register_block_styles() {
		// Columns: Overlap.
		register_block_style(
			'core/columns',
			array(
				'name'  => 'primarysourceone-columns-overlap',
				'label' => esc_html__( 'Overlap', 'primarysourceone' ),
			)
		);

		// Cover: Borders.
		register_block_style(
			'core/cover',
			array(
				'name'  => 'primarysourceone-border',
				'label' => esc_html__( 'Borders', 'primarysourceone' ),
			)
		);

		// Group: Borders.
		register_block_style(
			'core/group',
			array(
				'name'  => 'primarysourceone-border',
				'label' => esc_html__( 'Borders', 'primarysourceone' ),
			)
		);

		// Image: Borders.
		register_block_style(
			'core/image',
			array(
				'name'  => 'primarysourceone-border',
				'label' => esc_html__( 'Borders', 'primarysourceone' ),
			)
		);

		// Image: Frame.
		register_block_style(
			'core/image',
			array(
				'name'  => 'primarysourceone-image-frame',
				'label' => esc_html__( 'Frame', 'primarysourceone' ),
			)
		);

		// Latest Posts: Dividers.
		register_block_style(
			'core/latest-posts',
			array(
				'name'  => 'primarysourceone-latest-posts-dividers',
				'label' => esc_html__( 'Dividers', 'primarysourceone' ),
			)
		);

		// Latest Posts: Borders.
		register_block_style(
			'core/latest-posts',
			array(
				'name'  => 'primarysourceone-latest-posts-borders',
				'label' => esc_html__( 'Borders', 'primarysourceone' ),
			)
		);

		// Media & Text: Borders.
		register_block_style(
			'core/media-text',
			array(
				'name'  => 'primarysourceone-border',
				'label' => esc_html__( 'Borders', 'primarysourceone' ),
			)
		);

		// Separator: Thick.
		register_block_style(
			'core/separator',
			array(
				'name'  => 'primarysourceone-separator-thick',
				'label' => esc_html__( 'Thick', 'primarysourceone' ),
			)
		);

		// Social icons: Dark gray color.
		register_block_style(
			'core/social-links',
			array(
				'name'  => 'primarysourceone-social-icons-color',
				'label' => esc_html__( 'Dark gray', 'primarysourceone' ),
			)
		);
	}
	add_action( 'init', 'primary_source_one_register_block_styles' );
}
