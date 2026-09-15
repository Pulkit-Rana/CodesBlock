<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          '5:w.mXp@#-C$R-=UVPnly46%{.jxs5Xo~GxvylyJBq,>4U$</G_2Uz,O^#kE4Tv#' );
define( 'SECURE_AUTH_KEY',   '#C 0KIA*7B_U(.GjBmu:?vL1y;`/5PT2y,80Nzt#~(Z9%dsx8uySSd{EHa&j>3$l' );
define( 'LOGGED_IN_KEY',     '}3=K^$vT:q/d)rp3$!C:v>agD/nf_~$X+>RH)HSR,YPj.b^+f!i:i01h]Y(.UTH.' );
define( 'NONCE_KEY',         '0G@3:,J~rT~p-*I8p|q`^2t<nGo/SFVt@c*ZbT02R*$-zDg#`u3N@y<NWfJ.=a#G' );
define( 'AUTH_SALT',         'x+ ov~:3saq@FU`V{Zf{d@7K/STARmkC+N2SH?+#>`2__h8:$)r)(}I0_.4t3c6$' );
define( 'SECURE_AUTH_SALT',  'j9xYzvsXS 0iH+]$TGoXZeb5J6Xd1MA 4TUm1B$|Fl;#/5p?X@RlU&eTJ-^}(zI1' );
define( 'LOGGED_IN_SALT',    '0k9rZ/Qg6P!oQS<L^bRj,}BwRO/54=-hmoh|H}b!OhU$g>Rt(@&W%m)eqCE2$Q])' );
define( 'NONCE_SALT',        '}`6^Yc=MoN,p)nRJ-XC.$05HyxBujo[SO*,rNl|{?5F9*h#L2n>xm;9^d_/R&Sg1' );
define( 'WP_CACHE_KEY_SALT', 'Bm}1] (&q?CZ.-h$wWA8IMsP,:#|sSBl)g+Dwqz5A%UgpW]E:6}H>~7|zwU5cf1Q' );
/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
