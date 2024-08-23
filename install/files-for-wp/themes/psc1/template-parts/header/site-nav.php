<?php
/**
 * Displays the site navigation.
 *
 * @package WordPress
 * @subpackage PSC_1
 * @since PSC1 1.0
 */

?>

<?php if ( has_nav_menu( 'primary' ) ) : ?>
	<div class="hamburger" tabindex="0" id="hamburger">
	<svg width="9.0608826mm" height="6.5919871mm" viewBox="0 0 9.0608826 6.5919871" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:svg="http://www.w3.org/2000/svg">
		<g><rect style="fill:#ffffff;fill-opacity:1;stroke-width:0" width="9.0608826" height="1.336555" x="0" y="0" rx="0.63938898" ry="0.66827047" />
		<rect style="fill:#ffffff;fill-opacity:1;stroke-width:0" width="9.0608826" height="1.336555" x="0" y="2.609634" rx="0.63938898" ry="0.66827047" />
		<rect style="fill:#ffffff;fill-opacity:1;stroke-width:0" width="9.0608826" height="1.336555" x="0" y="5.25543" rx="0.63938898" ry="0.66827047" />
		</g></svg>
	</div>

	<nav id="site-navigation" class="primary-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary menu', 'primarysourceone' ); ?>">
		<a href="#" class="closer" id="navCloser">×</a>
		<ul id="primary-menu-list" class="primary-menu">
			<?php
			wp_nav_menu(
				array(
					'theme_location'  => 'primary',
					'fallback_cb'     => false,
					'container'		  => false,
					'items_wrap'	  => '%3$s'
				)
			);
			?>
		</ul>
	</nav><!-- #site-navigation -->
<?php endif; ?>
