<?php ?>
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'primarysourceone' ); ?></a>
		
	<header id="masthead" class="owrapper <?php echo esc_attr( $wrapper_classes ); ?>" role="banner">

		<div class="iwrapper">
			<!-- <a class="mhsLink" href="https://www.masshist.org"><img src="/publications/template/images/mhs_final_white.svg" alt="Logo of the Massachusetts Historical Society"/></a> -->
			<a class="coopHome" href="/"><img src="/publications/template/images/logo.png"/></a>

			<?php if ( has_custom_logo()) : ?>
				<div class="site-logo"><?php the_custom_logo(); ?></div>
			<?php endif; ?>

			<?php if ( $site_name ) : ?>
				<h1><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo str_replace(["&lt;", "&gt;"], ["<em>", "</em>"], $site_name); ?></a></h1>
			<?php endif; ?>

			<?php if ( $description && true === get_theme_mod( 'display_title_and_tagline', true ) ) : ?>
				<p class="site-description">
					<?php echo $description; // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</p>
			<?php endif; ?>

			<?php get_template_part( 'template-parts/header/site-nav' ); ?>
		</div>


	</header><!-- #masthead -->

