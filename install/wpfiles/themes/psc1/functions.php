<?php
/**
 * Functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage PSC_1
 * @since PSC1 1.0
 */

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 *
	 * @since PSC1 1.0
	 *
	 * @return void
	 */
	function primary_source_one_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on PSC1, use a find and replace
		 * to change 'primarysourceone' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'primarysourceone', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
//		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * This theme does not use a hard-coded <title> tag in the document head,
		 * WordPress will provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/**
		 * Add post-formats support.
		 */
		// add_theme_support(
		// 	'post-formats',
		// 	array(
		// 		'link',
		// 		'aside',
		// 		'gallery',
		// 		'image',
		// 		'quote',
		// 		'status',
		// 		'video',
		// 		'audio',
		// 		'chat',
		// 	)
		// );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 1568, 9999 );

		register_nav_menus(
			array(
				'primary' => esc_html__( 'Primary menu', 'primarysourceone' ),
		//		'footer'  => __( 'Secondary menu', 'primarysourceone' ),
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
//				'comment-form',
//				'comment-list',
//				'gallery',
				'caption',
//				'style',
//				'script',
//				'navigation-widgets',
			)
		);

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		$logo_width  = 300;
		$logo_height = 100;

		add_theme_support(
			'custom-logo',
			array(
				'height'               => $logo_height,
				'width'                => $logo_width,
				'flex-width'           => true,
				'flex-height'          => true,
				'unlink-homepage-logo' => true,
			)
		);


		// Add support for Block Styles.
		add_theme_support( 'wp-block-styles' );

		// Add support for full and wide align images.
		add_theme_support( 'align-wide' );

		add_action( 'enqueue_block_editor_assets', function() {
			wp_enqueue_style( 'primarysourceone-custom-block-editor-styles',
				get_theme_file_uri( "/assets/css/style-editor.css" ),
				false, wp_get_theme()->get( 'Version' ));
		} );


		// Add support for responsive embedded content.
		add_theme_support( 'responsive-embeds' );
	}

	add_action( 'after_setup_theme', 'primary_source_one_setup' );

	/**
	 * Calculate classes for the main <html> element.
	 *
	 * @since PSC1 1.0
	 *
	 * @return void
	 */
	function primarysourceone_the_html_classes() {
		$classes = apply_filters( 'primarysourceone_html_classes', '' );

		if ( ! $classes ) {
			return;
		}
		echo 'class="' . esc_attr( $classes ) . '"';
	}







