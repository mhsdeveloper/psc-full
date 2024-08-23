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

<div class="entry-header alignwide">
	<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	<?php primary_source_one_post_thumbnail(); ?>
</div><!-- .entry-header -->


<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-content">
		<?php
		the_content();

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

	<?php if ( ! is_singular( 'attachment' ) ) : ?>
		<?php get_template_part( 'template-parts/post/author-bio' ); ?>
	<?php endif; ?>

</article><!-- #post-<?php the_ID(); ?> -->
