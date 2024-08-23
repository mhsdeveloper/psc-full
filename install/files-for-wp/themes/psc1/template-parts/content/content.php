<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage PSC_1
 * @since PSC1 1.0
 */

?>
<div class="entry-header">
	<?php if ( is_singular() ) : ?>
		<?php the_title( '<h1 class="entry-title default-max-width">', '</h1>' ); ?>
	<?php else : ?>
		<?php the_title( sprintf( '<h2 class="entry-title default-max-width"><a href="%s">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
	<?php endif; ?>

	<?php primary_source_one_post_thumbnail(); ?>
</div><!-- .entry-header -->


<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="entry-content">
		<?php
		the_content(
			primary_source_one_continue_reading_text()
		);

		wp_link_pages(
			array(
				'before'   => '<nav class="page-links" aria-label="' . esc_attr__( 'Page', 'primarysourceone' ) . '">',
				'after'    => '</nav>',
				/* translators: %: Page number. */
				'pagelink' => esc_html__( 'Page %', 'primarysourceone' ),
			)
		);

		?>
	</div><!-- .entry-content -->

	<div class="entry-footer default-max-width">
		<?php primary_source_one_entry_meta_footer(); ?>
	</div><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
