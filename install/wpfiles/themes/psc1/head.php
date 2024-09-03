<?php
/**
 * The html opening and head tag.

 */

	//website cache buster
 	$VERSION = "wp1";

	$body_classes = get_body_class();
	$body_classes = implode(" ", $body_classes);
			
	if(strlen($_SERVER['REQUEST_URI']) < 3) $body_classes .= " coop-homepage ";
	
	$site_name    = get_bloginfo( 'name' );
	$description  = get_bloginfo( 'description', 'display' );

	$wrapper_classes  = 'site-header';
	$wrapper_classes .= has_custom_logo() ? ' has-logo' : '';
	$wrapper_classes .= ( true === get_theme_mod( 'display_title_and_tagline', true ) ) ? ' has-title-and-tagline' : '';
	$wrapper_classes .= has_nav_menu( 'primary' ) ? ' has-menu' : '';

	$WORDPRESS = true; //this lets the other header etc know that it's running in Wordpress

?><!doctype html>
<html <?php language_attributes(); ?> <?php primarysourceone_the_html_classes(); ?> >
<head>
	<?php include(SERVER_WWW_ROOT . "/html/publications/template/views/head.php");?>


</head>