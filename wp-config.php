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
define( 'DB_NAME', 'u970484384_soulful_dbt' );

/** Database username */
define( 'DB_USER', 'u970484384_soulful_usr' );

/** Database password */
define( 'DB_PASSWORD', 'G0R&t7x*|j' );

/** Database hostname */
define( 'DB_HOST', '194.59.164.105' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',          'F$-m.VcXC %]8<H jkJS5|aL/GTk#I~3E2sG=>c0 xXlf<:`R<-mP:2eQ:bv4gz!' );
define( 'SECURE_AUTH_KEY',   'M~f#W?D3G#q{MZYcFlWR0JGS]X37OBp$,}BA;Q2OSWs~=3VXFrO{<vwL8ucMD-a{' );
define( 'LOGGED_IN_KEY',     'w%B#U8g-b~ne1z<Nz!PoLN0,pPR8lu3q249S0:1i>?ec}nvx$^}p$$1F_zY`MDTq' );
define( 'NONCE_KEY',         '3:@udWyGC{<Bh5;F[[zWA}k3Umk4GHKuvi7h9z{*q@xW$wFVv^=w]C;U.DJ~<6C/' );
define( 'AUTH_SALT',         'k2=a@Pn>kTO3iW(1qLD{7=PmM3=a]gNOP~Esc%pm&u<%LFRs96-`$z}w+Tp>Ecwp' );
define( 'SECURE_AUTH_SALT',  '0`j#H?[}JMbR%<zY7-<4h?8V]`W>&.y#`[5i-.AzaG56PED%DeoZB+_K~<0;mx45' );
define( 'LOGGED_IN_SALT',    '| <iw-m@Ww>zp*gL%hH#/44XQDY2m8Za,uefC~k03$[8Z {7B^T&6QT^D0f8<xut' );
define( 'NONCE_SALT',        ',+_Wfm|{Dbjd*w^++QsX,j3/qEQE[dz-Z3Hb?d,j*5%rDBfpk,Ex^,+gh0Y=s8=A' );
define( 'WP_CACHE_KEY_SALT', '.A>-4N@{lqs&2U5xjgun{Yw}ortw.PNICcWng3WEXrqDnO8zxu3VA(pdgtoecJ<U' );


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
