<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'frontend' );

/** MySQL database username */
define( 'DB_USER', '[[EDIT-THIS-MYSQL-USER]]' );

/** MySQL database password */
define( 'DB_PASSWORD', '[[EDIT-THIS-MYSQL-PASSWORD]]' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'iHx+Gc]a-;vncnfjvncnmdnvjsksncxmxskjdsnsxvcqo9w93392V5q,br`H*6!aS#cxH=y' );
define( 'SECURE_AUTH_KEY',  'DowY,@L,r3R&;=ej#HrLFwerwerwtge457y5thdfgfserZg1xQL J1qc*nSV!h)p89T_[//R' );
define( 'LOGGED_IN_KEY',    'He:8GtLk9rzITfumesa|t<;17hregbn2q4112YFxtH5}o47I6#2<rC*.6pEr-j8D`4bKsj=||),b-K' );
define( 'NONCE_KEY',        '!k|XFW-3lz]hc(eLG0Kq/?-uE*ZQLasdflk102394bvfmvc(*Y$#(AsH[G<s$+**Y' );
define( 'AUTH_SALT',        'c1CRb$a%b2[RZ*.o0Dsdlkfo3wern9nr2o98neiusanfknfdfPQ]n`_Pbh:`' );
define( 'SECURE_AUTH_SALT', 'GXe>umswd1|6xy+0[BwhTS;;zozozoedlk4e,fm4e0fdhj24e0rf03w,lIEaSIZElx[BFjW5' );
define( 'LOGGED_IN_SALT',   '4.;9@9J*vCwxM4`,2%V4}ip*lcI,|;AJwy`L-hlksk30gn10o9hOIEFW)(Gbkmjn3brtedfj9djgR@!d' );
define( 'NONCE_SALT',       '!Kz9XD1G?HOD5Kyh-*p{[6?!dKab._2iD)=v7b4mpSKLE#I)@#NE(IGN)#(NGFKJDSB$E#R]' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );


define('FS_METHOD', 'direct');

//define("WP_ALLOW_MULTISITE", true);
define('MULTISITE', true);
//define('SUBDOMAIN_INSTALL', true);
define('DOMAIN_CURRENT_SITE', '[[EDIT-THIS-DOMAIN-NAME]]');
define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);

define( 'WP_AUTO_UPDATE_CORE', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
