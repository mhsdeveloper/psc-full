<?php
 /* Template Name: About Page Template */ 
 /**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage PSC_1
 * @since PSC1 1.0
 */
?><? include("head.php");?>


<body class="aboutPage <?=$body_classes; ?>">


<?php //wp_body_open(); ?>


	<div id="page" class="site">


		<? include("header.php"); ?>


		<div id="content" class="site-content">
			<?

			/* Start the Loop */
			while ( have_posts() ) {
				the_post();
			?>
				<div class="iwrapper"><?	the_title( '<h1 class="title">', '</h1>' ); ?></div>
				
				<article id="post-<? the_ID(); ?>" <? post_class(); ?>>
				
					<div class="entry-content">
						<?
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
				</article>
			<?

				// If comments are open or there is at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}

				
			} // End of the loop.
			?>

			<? if ( is_active_sidebar( 'sidebar-1' ) ) { ?>
				<aside class="widget-area">
					<? dynamic_sidebar( 'sidebar-1' ); ?>
				</aside><!-- .widget-area -->
			<? } ?>


		</div><!-- #content -->

	<? 
		get_template_part( 'template-parts/footer/footer-widgets' ); 
		get_footer();
	?>


	</div><!-- #page -->
</body>
</html>
