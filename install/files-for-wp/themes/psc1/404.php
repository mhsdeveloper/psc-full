<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage PSC_1
 * @since PSC1 1.0
 */
?>

<?	include("head.php");?>


<body class="<?=$body_classes; ?>">


<?php //wp_body_open(); ?>


	<div id="page" class="site">


		<? include("header.php"); ?>


		<div id="content" class="site-content">
			<h1>Sorry, that page doesn't exist.</h1>
		</div><!-- #content -->

	<? 
		get_template_part( 'template-parts/footer/footer-widgets' ); 
		get_footer();
	?>


	</div><!-- #page -->

	<?php wp_footer(); ?>

</body>
</html>
