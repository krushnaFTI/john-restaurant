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

define( 'DB_NAME', "johnrestaurant" );



/** MySQL database username */

define( 'DB_USER', "root" );



/** MySQL database password */

define( 'DB_PASSWORD', "admin@123" );



/** MySQL hostname */

define( 'DB_HOST', "localhost" );



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

define( 'AUTH_KEY',         ')vLCe|MG^dpeZ}6h&pD{67UcglD(oPjv%?wM^hqgiBT!kA( TN%m%(eKGZ0TCzZ9' );

define( 'SECURE_AUTH_KEY',  'HKfuV`Dj0=:DxXi+YIn_!45z)`HqZUvjqQ{[jSjQlo2[iZ3t0aHK^)fFm@GW=y$p' );

define( 'LOGGED_IN_KEY',    '/>v74:ml<7Lii!N=(W[8wE`6--Jv}GIfM%ypG9;]C@t6Rg00{>CT HyDaJ&,]Kl%' );

define( 'NONCE_KEY',        'xvGZ&CzSYQ,m)H$P/tQ(K7WQX5 -NxgD]4nXsAw0dG-JyG%HDf/JLL:uLkLa/iS#' );

define( 'AUTH_SALT',        '<,PS{4`]1,]SLfEU/^KKTN#?R*N,!+[#mlmev]#dA3akysoFTXt!xDu=d$Sf9iMf' );

define( 'SECURE_AUTH_SALT', 'WX1S6^&,U|>p;9pGZoK_hb:lu99yuye12v4mW8&xYFE)/l<jZiOkOVjE#jrsIsPs' );

define( 'LOGGED_IN_SALT',   '0C$0(N[q(XLHA.&w3y29;Wu#Uf_S&DA*bf8*NG/%;z@e49|L3B+/d$&:g(34$kKq' );

define( 'NONCE_SALT',       '!6i<6JnhKQq7Rih?4@v%]g|$iCzozEVZftxCl&37/)uCagpNV>{X0jM_];z$V=Ve' );



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



/* That's all, stop editing! Happy publishing. */



/** Absolute path to the WordPress directory. */

if ( ! defined( 'ABSPATH' ) ) {

	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

}



/** Sets up WordPress vars and included files. */

require_once( ABSPATH . 'wp-settings.php' );

// define('DISABLE_WP_CRON', true);
