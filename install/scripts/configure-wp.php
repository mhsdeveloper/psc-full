<?php



	/* script for setting up all things Wordpress

	*/


	// first copy over required files:



	cp -r /psc/www/html/install/wpfiles/* /psc/www/html/wp-content/



	// setup multisite

	/*

	define('FS_METHOD', 'direct');

//define("WP_ALLOW_MULTISITE", true);
define('MULTISITE', true);
//define('SUBDOMAIN_INSTALL', true);
define('DOMAIN_CURRENT_SITE', 'www.primarysourcecoop.org');
define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);
define( 'WP_AUTO_UPDATE_CORE', false );

*/