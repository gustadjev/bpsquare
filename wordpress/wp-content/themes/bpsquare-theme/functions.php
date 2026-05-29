<?php
/**
 * BPSquare Theme — functions.php
 *
 * Minimal headless theme. All business logic lives in the bpsquare-core plugin.
 * This file only handles theme support features and CORS headers for the REST API.
 *
 * @package BPSquare_Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme setup: minimal feature registration.
 */
function bpsquare_theme_setup(): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'comment-form', 'gallery', 'caption' ] );
	add_theme_support( 'custom-logo' );

	// Register nav menus for use in WP admin (even though Angular renders them).
	register_nav_menus( [
		'primary' => __( 'Primary Navigation', 'bpsquare-theme' ),
		'footer'  => __( 'Footer Navigation', 'bpsquare-theme' ),
	] );
}
add_action( 'after_setup_theme', 'bpsquare_theme_setup' );

/**
 * Add CORS headers for WordPress REST API responses.
 * Restricts access to the allowed origin defined in wp-config / environment.
 */
function bpsquare_rest_cors_headers(): void {
	$allowed_origin = defined( 'BPSQUARE_ALLOWED_ORIGIN' )
		? BPSQUARE_ALLOWED_ORIGIN
		: 'http://localhost:4200';

	// If the request's origin matches our allowed origin, reflect it.
	$request_origin = isset( $_SERVER['HTTP_ORIGIN'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_ORIGIN'] ) ) : '';
	if ( $request_origin === $allowed_origin ) {
		header( 'Access-Control-Allow-Origin: ' . esc_url_raw( $allowed_origin ) );
		header( 'Access-Control-Allow-Methods: GET, POST, OPTIONS' );
		header( 'Access-Control-Allow-Credentials: true' );
		header( 'Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Nonce' );
	}

	// Handle preflight OPTIONS request.
	if ( 'OPTIONS' === $_SERVER['REQUEST_METHOD'] ) {
		status_header( 200 );
		exit;
	}
}
add_action( 'rest_api_init', 'bpsquare_rest_cors_headers', 15 );

/**
 * Remove the X-Pingback header for security.
 *
 * @param array $headers Response headers.
 * @return array Modified headers.
 */
function bpsquare_remove_x_pingback( array $headers ): array {
	unset( $headers['X-Pingback'] );
	return $headers;
}
add_filter( 'wp_headers', 'bpsquare_remove_x_pingback' );

/**
 * Disable the WordPress XML-RPC interface.
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * Remove unnecessary <link> tags from WP head for a cleaner headless setup.
 */
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );

/**
 * Expose the allowed CORS origin as a REST API response header so Angular can
 * read it when calling authenticated endpoints.
 *
 * @param \WP_HTTP_Response $result  The response object.
 * @param \WP_REST_Server   $server  The REST server instance.
 * @param \WP_REST_Request  $request The current request.
 * @return \WP_HTTP_Response
 */
function bpsquare_add_cors_to_rest_response( $result, $server, $request ) {
	$allowed_origin = defined( 'BPSQUARE_ALLOWED_ORIGIN' )
		? BPSQUARE_ALLOWED_ORIGIN
		: 'http://localhost:4200';

	$result->header( 'Access-Control-Allow-Origin', $allowed_origin );
	$result->header( 'Access-Control-Allow-Methods', 'GET, POST, OPTIONS' );
	$result->header( 'Access-Control-Allow-Credentials', 'true' );
	$result->header( 'Access-Control-Allow-Headers', 'Authorization, Content-Type, X-WP-Nonce' );
	return $result;
}
add_filter( 'rest_post_dispatch', 'bpsquare_add_cors_to_rest_response', 10, 3 );

/**
 * Expose BPSQUARE_ALLOWED_ORIGIN constant from Docker/server environment variable
 * if it has not already been defined in wp-config.php.
 */
if ( ! defined( 'BPSQUARE_ALLOWED_ORIGIN' ) && isset( $_ENV['BPSQUARE_ALLOWED_ORIGIN'] ) ) {
	define( 'BPSQUARE_ALLOWED_ORIGIN', sanitize_text_field( $_ENV['BPSQUARE_ALLOWED_ORIGIN'] ) );
}
if ( ! defined( 'BPSQUARE_ALLOWED_ORIGIN' ) ) {
	define( 'BPSQUARE_ALLOWED_ORIGIN', 'http://localhost:4200' );
}

/**
 * Enqueue minimal admin styles for the headless theme.
 */
function bpsquare_admin_styles(): void {
	wp_enqueue_style(
		'bpsquare-admin',
		get_template_directory_uri() . '/assets/css/admin.css',
		[],
		'1.0.0'
	);
}
add_action( 'admin_enqueue_scripts', 'bpsquare_admin_styles' );
