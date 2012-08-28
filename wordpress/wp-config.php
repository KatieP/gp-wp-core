<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache

/** 
 * ENABLE SESSION MANAGEMENT
 * By default wordpress is stateless i.e., it doesn't record any session data and it uses cookies only.
 * We are enabling sessions to pass form data for multi-page forms.
 * 
 * Future Reference: Do not use with loadbalancers etc, see http://tuxradar.com/practicalphp/10/3/7 for storing session data in a database. Also Redis?
 */

if (!session_id()) {
	session_start();
}


/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 's1-wordpress');

/** MySQL database username */
define('DB_USER', 's1-wordpress');
#define('DB_USER', 'root'); # Toggle for dev environment only 

/** MySQL database password */
define('DB_PASSWORD', '7BXmxPmwy4LJZNhR');
#define('DB_PASSWORD', ''); # Toggle for dev environment only

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', 'utf8_general_ci');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '2/@X-g|Ww7tPuVHEFqZ&#~1vJO~lc--z5$mJe#lm.1:x1H4?oKyl9`Vo^l-J[{8B');
define('SECURE_AUTH_KEY',  'Y/;qCjAzcw8&U&Q[WfxRaix}Im;Q^hFb9p;5?H&hGm,:NmE{~n/P9Mn3J+uDF<Ef');
define('LOGGED_IN_KEY',    'c qV;~TODtc,1x2t2aV3dME~yEAf3.!iopG*b rfkRk~-9?X[!gR;`q?/h ej]}0');
define('NONCE_KEY',        '}[~ao2/ZW};N.GLz,KZE 5JWZ+HAZcieiJbNBKoj+-a;{U#y8O@-;WXXyLdwNHx%');
define('AUTH_SALT',        'E^a9pKxk*/)l}|Y.Z${g(TT4Mj|E-oW)mH~?^D&<1VGO)Kq11/5I8cM5,eBV>.2{');
define('SECURE_AUTH_SALT', '|3QZm6Vqp=//P].w*TA): rmtU7aYW5Tt-Bt}B&`Qet2M5CVp1-j%PQk+8t(}lY6');
define('LOGGED_IN_SALT',   'k*Achy|Xh|UPiGID<i6#b.f2r%JZbuDhMKW438iNC|6Xu8-CLVxW&(bZ39Vl{~|i');
define('NONCE_SALT',       'L2(wv,-0V<^{mC`dHdUJ7[(_KXNVf6qh9hlJxM?7-S{>^d9^i_fl?wms;M<QC;K>');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);
define('DISABLE_WP_CRON', true);

define( 'SUNRISE', 'on');
define('WP_ALLOW_MULTISITE', true);
define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', true );
$base = '/';
#define( 'DOMAIN_CURRENT_SITE', 'www.thegreenpages.com.au' );
define( 'PATH_CURRENT_SITE', '/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

