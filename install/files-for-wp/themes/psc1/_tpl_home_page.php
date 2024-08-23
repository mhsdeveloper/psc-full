<?php /* Template Name: Home Page Template */ 

?><?	include("head.php");?>


<body class="homepage <?=$body_classes; ?>">


<?php // wp_body_open(); ?>


	<div id="page" class="site">


		<? include("header.php"); ?>


		<div id="content" class="site-content">
			<?

			/* Start the Loop */
			while ( have_posts() ) {
				the_post();
			?>
			
				
				<article id="post-<? the_ID(); ?>" <? post_class(); ?>>
			
					<div class="entry-content">
						<!-- <div class="innerContainer heroTitle">
							<h1><?php echo str_replace(["&lt;", "&gt;"], ["<em>", "</em>"], $site_name); ?></h1>
						</div> -->

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
