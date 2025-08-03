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
define( 'DB_NAME', 'projetoame_wordpress' );

/** Database username */
define( 'DB_USER', 'projetoame' );

/** Database password */
define( 'DB_PASSWORD', 'vp3imJizMOgWxbM' );

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
define( 'AUTH_KEY',          'eaMQ&3qsm..WAK(c*C@|zHtibO{wsV<O-sZXK#eeHdz!a A|%EawA1W7X*s1`+Im' );
define( 'SECURE_AUTH_KEY',   'cD:n}^3.+)X1PexDG&iz(oHasM1s*Cw>$KB?1a5fsS(L/$`j;hbqbwwN.?CTLdv:' );
define( 'LOGGED_IN_KEY',     ']bpPZ$@n6]c^EvBH{7#/;f&*I,>y:nPl:/n);w+ZveT<(x2:_l|*DPu(71@JA>HN' );
define( 'NONCE_KEY',         'j^49Tz>-1.iKGI2-[daJ=.DWwT?pse][3cHb`2DDc_Fz_HWIu>xN`B%v9IO_8cjg' );
define( 'AUTH_SALT',         'C]3$g1qB|44WKsRL/WkjXf]Zt/epZo(;2p+=y9Bt<w@eT`/IA2DnLY7K%}$ksuiB' );
define( 'SECURE_AUTH_SALT',  ',40<oM`7B=MB)SO{LeomERFwo)1u#W4RxYXolL1pWikn-vFbGKdUKk.D#ydj.3W.' );
define( 'LOGGED_IN_SALT',    'm(J=s&ggKYZh<s)o3TwNArgm~H2M;dFH>eSoZ[cZra}kgW@<ab7}Q0/blWB3?8F~' );
define( 'NONCE_SALT',        'Q)a:GHz1POu|SN1th/ Z#} wvAJc29X?3.D}zK{bYwigYbj6V=@a%q.2B:+7;%kO' );
define( 'WP_CACHE_KEY_SALT', 'm41=g TS6-f:lw+c7K5,L2i@-n(8~qvBiOgFEoY00ca6kQ<XAfQMZa(Jl.uZa#qP' );


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

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
