<?php
/**
 * BPSquare Core — REST Inquiries Controller
 *
 * Handles POST /wp-json/bpsquare/v1/inquiries
 *
 * @package BPSquare_Core
 */

namespace BPSquare_Core;

defined( 'ABSPATH' ) || exit;

/**
 * Class REST_Inquiries_Controller
 */
class REST_Inquiries_Controller extends \WP_REST_Controller {

	/** @var string REST namespace. */
	protected $namespace = 'bpsquare/v1';

	/** @var string REST base. */
	protected $rest_base = 'inquiries';

	/** Maximum submissions per IP per hour. */
	private const RATE_LIMIT = 5;
	/** Rate limit window in seconds. */
	private const RATE_WINDOW = 3600;

	/**
	 * Register REST routes.
	 */
	public function register_routes(): void {
		register_rest_route( $this->namespace, '/' . $this->rest_base, [
			[
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'create_item' ],
				'permission_callback' => '__return_true', // Public endpoint; protection via validation + rate limiting.
				'args'                => $this->get_endpoint_args_for_item_schema( \WP_REST_Server::CREATABLE ),
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		] );
	}

	/**
	 * Handle inquiry submission.
	 *
	 * @param \WP_REST_Request $request Full request data.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function create_item( $request ) {
		$data = $request->get_json_params() ?: $request->get_params();

		// Rate limiting.
		if ( $this->is_rate_limited() ) {
			return new \WP_Error(
				'too_many_requests',
				__( 'Too many submissions. Please try again later.', 'bpsquare-core' ),
				[ 'status' => 429 ]
			);
		}

		// Validate input.
		$validator = new Validation_Service();
		if ( ! $validator->validate_inquiry( $data ) ) {
			return new \WP_Error(
				'invalid_inquiry',
				__( 'Please correct the errors below.', 'bpsquare-core' ),
				[ 'status' => 400, 'errors' => $validator->get_errors() ]
			);
		}

		// Save inquiry to WordPress.
		$post_id = CPT_Inquiry::save( $data );
		if ( is_wp_error( $post_id ) ) {
			return new \WP_Error(
				'save_failed',
				__( 'There was a problem saving your inquiry. Please try again.', 'bpsquare-core' ),
				[ 'status' => 500 ]
			);
		}

		// Send email notifications.
		$email_service = new Email_Service();
		$email_service->send_admin_notification( $data, $post_id );
		$email_service->send_submitter_acknowledgement( $data );

		// Record this submission for rate limiting.
		$this->record_submission();

		return new \WP_REST_Response( [
			'success'   => true,
			'message'   => __( "Thank you! We've received your inquiry and will be in touch within 1–2 business days.", 'bpsquare-core' ),
			'inquiryId' => $post_id,
		], 201 );
	}

	/**
	 * Get the item schema.
	 *
	 * @return array
	 */
	public function get_item_schema(): array {
		return [
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'inquiry',
			'type'       => 'object',
			'properties' => [
				'firstName'          => [ 'type' => 'string', 'required' => true ],
				'lastName'           => [ 'type' => 'string', 'required' => true ],
				'businessName'       => [ 'type' => 'string' ],
				'businessType'       => [ 'type' => 'string' ],
				'currentWebsite'     => [ 'type' => 'string' ],
				'email'              => [ 'type' => 'string', 'format' => 'email', 'required' => true ],
				'phone'              => [ 'type' => 'string' ],
				'serviceInterest'    => [ 'type' => 'string', 'required' => true ],
				'budgetRange'        => [ 'type' => 'string' ],
				'preferredTimeline'  => [ 'type' => 'string' ],
				'phasedApproach'     => [ 'type' => 'string' ],
				'operationalProblem' => [ 'type' => 'string' ],
				'projectDescription' => [ 'type' => 'string', 'required' => true ],
				'consentAccepted'    => [ 'type' => 'boolean', 'required' => true ],
				'website'            => [ 'type' => 'string', 'description' => 'Honeypot — must be empty.' ],
			],
		];
	}

	// -------------------------------------------------------------------------
	// Private: rate limiting
	// -------------------------------------------------------------------------

	/**
	 * Check whether the current IP has exceeded the submission rate limit.
	 *
	 * @return bool True if rate-limited.
	 */
	private function is_rate_limited(): bool {
		global $wpdb;

		$ip_hash = $this->get_ip_hash();
		$table   = $wpdb->prefix . 'bpsquare_rate_limit';
		$window  = gmdate( 'Y-m-d H:i:s', time() - self::RATE_WINDOW );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$row = $wpdb->get_row( $wpdb->prepare(
			"SELECT hit_count, window_start FROM {$table} WHERE ip_hash = %s AND action_key = 'inquiry'",
			$ip_hash
		) );

		if ( ! $row ) {
			return false; // No record — first submission.
		}

		// If the window has expired, the record will be reset on next insert.
		if ( strtotime( $row->window_start ) < strtotime( $window ) ) {
			return false;
		}

		return (int) $row->hit_count >= self::RATE_LIMIT;
	}

	/**
	 * Record a submission in the rate-limit table.
	 */
	private function record_submission(): void {
		global $wpdb;

		$ip_hash = $this->get_ip_hash();
		$table   = $wpdb->prefix . 'bpsquare_rate_limit';
		$now     = current_time( 'mysql', true );
		$window  = gmdate( 'Y-m-d H:i:s', time() - self::RATE_WINDOW );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$existing = $wpdb->get_row( $wpdb->prepare(
			"SELECT id, hit_count, window_start FROM {$table} WHERE ip_hash = %s AND action_key = 'inquiry'",
			$ip_hash
		) );

		if ( ! $existing ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->insert( $table, [
				'ip_hash'      => $ip_hash,
				'action_key'   => 'inquiry',
				'hit_count'    => 1,
				'window_start' => $now,
			] );
		} elseif ( strtotime( $existing->window_start ) < strtotime( $window ) ) {
			// Window expired; reset.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->update(
				$table,
				[ 'hit_count' => 1, 'window_start' => $now ],
				[ 'id' => $existing->id ]
			);
		} else {
			// Increment.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->update(
				$table,
				[ 'hit_count' => (int) $existing->hit_count + 1 ],
				[ 'id' => $existing->id ]
			);
		}
	}

	/**
	 * Get a SHA-256 hash of the client IP address.
	 *
	 * @return string
	 */
	private function get_ip_hash(): string {
		$ip = isset( $_SERVER['REMOTE_ADDR'] )
			? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) )
			: 'unknown';
		return hash( 'sha256', $ip );
	}
}
