<?php
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
define('DB_NAME', 'wp_data');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '159357');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'CUfEo748`E?{>I6|*]IdhRJ 9QXh)1(gA$!8joc~G^Nj_4]&Ioh|wLcH4k`qNfYE');
define('SECURE_AUTH_KEY',  '.#+|bTutJ]rcdN~eDcM W~Qwr,XTxSf|`Zc+?U[=$GA0b|<wxq/2Za<!P;v+tyi2');
define('LOGGED_IN_KEY',    'W~{eW|XtWE,?H+xQ!@FpYG*!Lc^HxavJ)q12 7NE3P,<KWEwRdHl)XzwlMTEZjPy');
define('NONCE_KEY',        '7[cXqsSa=,V:}}p3jQ9XdmbT)VRe,Gi-+ E|Dg2Ji{9([CrYkLWsb@!Y>_ L&y}*');
define('AUTH_SALT',        'T0A1SH.]}Wec-Vt ;4CfB>,N[t5PP9HttFcUP&5+1qJ-bRYD;ULO?Ui8H4Ft(t$Q');
define('SECURE_AUTH_SALT', '#bx%mqsm^!;Bscf}?)zw,8PAzC|j.;U:&l9Z[I, #!A<Jr(fip?TB#aKU3_a+c+v');
define('LOGGED_IN_SALT',   'JnWfV&Sfd=s/S/hw#(n$n+o-XU2.O!6$;3t|#W+{^+}xS.` >7/|xVEM|0s-J/j1');
define('NONCE_SALT',       'fz+jpY-,{;+4p3V#T}))_O/p1EmLr/#b^G z*.+CFQ=4Rsl5-)WJ& ;G;}qF%@@I');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
