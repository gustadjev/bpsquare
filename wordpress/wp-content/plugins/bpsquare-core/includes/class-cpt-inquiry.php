<?php
/**
 * BPSquare Core — Inquiry Custom Post Type
 *
 * Stores contact/project inquiries submitted through the Angular contact form.
 *
 * @package BPSquare_Core
 */

namespace BPSquare_Core;

defined( 'ABSPATH' ) || exit;

/**
 * Class CPT_Inquiry
 */
class CPT_Inquiry {

	public const POST_TYPE = 'bps_inquiry';

	public function register(): void {
		add_action( 'init', [ $this, 'register_post_type' ] );
	}

	public function register_post_type(): void {
		register_post_type( self::POST_TYPE, [
			'labels'          => [
				'name'          => __( 'Inquiries', 'bpsquare-core' ),
				'singular_name' => __( 'Inquiry', 'bpsquare-core' ),
				'all_items'     => __( 'All Inquiries', 'bpsquare-core' ),
				'view_item'     => __( 'View Inquiry', 'bpsquare-core' ),
			],
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => 'edit.php?post_type=bps_service',
			'show_in_rest'    => false, // Inquiries are admin-only; not exposed publicly.
			'menu_icon'       => 'dashicons-email-alt',
			'supports'        => [ 'title' ],
			'has_archive'     => false,
			'rewrite'         => false,
			'capability_type' => 'post',
			// Admin users can read/manage; inquiries themselves are created via REST.
			'capabilities'    => [
				'create_posts' => 'manage_options',
			],
			'map_meta_cap'    => true,
		] );
	}

	/**
	 * Save an inquiry as a CPT post with all meta data.
	 *
	 * @param array $data Validated inquiry data.
	 * @return int|\WP_Error Post ID on success.
	 */
	public static function save( array $data ): int|\WP_Error {
		$first_name = sanitize_text_field( $data['firstName'] ?? '' );
		$last_name  = sanitize_text_field( $data['lastName'] ?? '' );
		$email      = sanitize_email( $data['email'] ?? '' );
		$business   = sanitize_text_field( $data['businessName'] ?? '' );

		$title = trim( "{$first_name} {$last_name}" ) . ( $business ? " — {$business}" : '' );

		$post_id = wp_insert_post( [
			'post_type'   => self::POST_TYPE,
			'post_title'  => $title,
			'post_status' => 'private',
			'post_author' => 1,
		] );

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		$meta = [
			'_bps_inq_first_name'    => $first_name,
			'_bps_inq_last_name'     => $last_name,
			'_bps_inq_business_name' => $business,
			'_bps_inq_email'         => $email,
			'_bps_inq_phone'         => sanitize_text_field( $data['phone'] ?? '' ),
			'_bps_inq_service'       => sanitize_text_field( $data['serviceInterest'] ?? '' ),
			'_bps_inq_budget'        => sanitize_text_field( $data['budgetRange'] ?? '' ),
			'_bps_inq_timeline'      => sanitize_text_field( $data['preferredTimeline'] ?? '' ),
			'_bps_inq_description'   => sanitize_textarea_field( $data['projectDescription'] ?? '' ),
			'_bps_inq_consent'       => ! empty( $data['consentAccepted'] ) ? '1' : '0',
			'_bps_inq_ip'            => sanitize_text_field( self::get_client_ip() ),
			'_bps_inq_submitted_at'  => current_time( 'mysql' ),
		];

		foreach ( $meta as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}

		return $post_id;
	}

	/**
	 * Get a safe client IP address.
	 *
	 * @return string
	 */
	private static function get_client_ip(): string {
		// Prefer REMOTE_ADDR (set by the server, not the client).
		return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	}
}
