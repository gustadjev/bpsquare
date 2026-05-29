<?php
/**
 * Plugin Name:       BPSquare Core
 * Plugin URI:        https://bpsquarellc.com
 * Description:       Core functionality for the BPSquare LLC website. Registers custom
 *                    post types, REST API endpoints, inquiry handling, email notifications,
 *                    and admin settings. Do not add this logic to the theme.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            BPSquare LLC
 * Author URI:        https://bpsquarellc.com
 * License:           Proprietary
 * Text Domain:       bpsquare-core
 * Domain Path:       /languages
 *
 * @package BPSquare_Core
 */

defined( 'ABSPATH' ) || exit;

// Plugin version and path constants.
define( 'BPSQUARE_CORE_VERSION', '1.0.0' );
define( 'BPSQUARE_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'BPSQUARE_CORE_URL', plugin_dir_url( __FILE__ ) );
define( 'BPSQUARE_CORE_BASENAME', plugin_basename( __FILE__ ) );

// Autoload plugin classes.
spl_autoload_register( function ( string $class ): void {
	$prefix = 'BPSquare_Core\\';
	if ( strncmp( $prefix, $class, strlen( $prefix ) ) !== 0 ) {
		return;
	}
	$relative = substr( $class, strlen( $prefix ) );
	$file     = BPSQUARE_CORE_DIR . 'includes/class-' . strtolower( str_replace( [ '_', '\\' ], [ '-', '/' ], $relative ) ) . '.php';
	if ( file_exists( $file ) ) {
		require_once $file;
	}
} );

// Bootstrap.
require_once BPSQUARE_CORE_DIR . 'includes/class-plugin.php';

/**
 * Returns the main plugin instance.
 *
 * @return \BPSquare_Core\Plugin
 */
function bpsquare_core(): \BPSquare_Core\Plugin {
	return \BPSquare_Core\Plugin::instance();
}

bpsquare_core();
