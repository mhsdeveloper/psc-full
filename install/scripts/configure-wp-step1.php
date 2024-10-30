<?php



	/* start multisite setup
	*/
	
	$filename = "/psc/www/html/wp-config.php";

	$text = file_get_contents($filename);

	$find = "/* That's all, stop editing! Happy publishing. */";
	$replace = '
/* Multisite */
define("WP_ALLOW_MULTISITE", true );
define("FS_METHOD", "direct");
/* more code here */

/* That\'s all, stop editing! Happy publishing. */
';

	$text = str_replace($find, $replace, $text);

	file_put_contents($filename, $text);


	echo "\n Initial multisite configuration done.\n";
	echo "Login to Wordpress and continue the install steps.\n";