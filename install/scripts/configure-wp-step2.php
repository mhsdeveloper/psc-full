<?php



	/* start multisite setup
	*/
	
	$filename = "/psc/www/html/wp-config.php";

	$text = file_get_contents($filename);

	$find = "/* That's all, stop editing! Happy publishing. */";



	$replace = '

define( "MULTISITE", true );
define( "SUBDOMAIN_INSTALL", false );
define( "DOMAIN_CURRENT_SITE", "[[EDIT-THIS-DOMAIN-NAME]]" );
define( "PATH_CURRENT_SITE", "/" );
define( "SITE_ID_CURRENT_SITE", 1 );
define( "BLOG_ID_CURRENT_SITE", 1 );

/* That\'s all, stop editing! Happy publishing. */
';

	$text = str_replace($find, $replace, $text);

	file_put_contents($filename, $text);


	echo "\n Multisite configuration done.\n";
	echo "Login to Wordpress to enable multisite settings.\n";