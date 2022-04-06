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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wpdb' );

/** MySQL database username */
define( 'DB_USER', 'user' );

/** MySQL database password */
define( 'DB_PASSWORD', 'password' );

/** MySQL hostname */
define( 'DB_HOST', 'mysql' );

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
define( 'AUTH_KEY',         '3Q5MG^j#igri`8]qi75Ptt6Jk-2Wu?22 OvbqgpSCLeW_|M@#OQHQcm>Xa9JD,g|' );
define( 'SECURE_AUTH_KEY',  '3jmVdYg@pX0io@}8gMxGEaf/qP@D >>,&BRuQd5& X{BofYlsx|J5ve3`SI,uCs`' );
define( 'LOGGED_IN_KEY',    'UxhSlmrV?J</?u7*Sk,8<9[_Tz{x**}j]MPSD`vHtz6p8?9mz;{gXPowx|I&YH_i' );
define( 'NONCE_KEY',        '3,`51A_MoFn9RO%xb#OwkPX46K7=dTNCC4w#j6oVPmUA{WTYJ,=WGTl4,f8c.LPg' );
define( 'AUTH_SALT',        'Zgn_|S{MSRFdOv_RJgsi.UO.]bdO3?hO5]oU!5&s+JUw4(sv2 yy1Wg*]>1(!KDe' );
define( 'SECURE_AUTH_SALT', '%E~W[y5>*Pjhag)=gA<XG|^F*Tu[{L8[B6 kgBF.st:55pf2qE2||SkvOlg%o?!k' );
define( 'LOGGED_IN_SALT',   'Elsfh8~RT>wk~`FY&aohL nEWS^L5=l8? 8+mg?}/jR!z-l;ZBG9g<`u*pTH~yh?' );
define( 'NONCE_SALT',       'FIn7kT^U~7WW3P9c18!h);wbkF{9h)h gsuKV}*&uh;Xa2}78,a@?s6TP$TCz}?g' );

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
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', true );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
