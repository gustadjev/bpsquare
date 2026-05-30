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
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
		add_action( 'save_post_' . self::POST_TYPE, [ $this, 'save_admin_meta' ] );
		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', [ $this, 'columns' ] );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', [ $this, 'render_column' ], 10, 2 );
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

	public function add_meta_boxes(): void {
		add_meta_box(
			'bps_inquiry_details',
			__( 'Inquiry Details', 'bpsquare-core' ),
			[ $this, 'render_details_meta_box' ],
			self::POST_TYPE,
			'normal',
			'high'
		);

		add_meta_box(
			'bps_inquiry_status',
			__( 'Inquiry Status', 'bpsquare-core' ),
			[ $this, 'render_status_meta_box' ],
			self::POST_TYPE,
			'side',
			'high'
		);
	}

	public function render_status_meta_box( \WP_Post $post ): void {
		wp_nonce_field( 'bps_inquiry_save', 'bps_inquiry_nonce' );
		$status = get_post_meta( $post->ID, '_bps_inq_status', true ) ?: 'new';
		?>
		<p>
			<label for="bps_inq_status"><?php esc_html_e( 'Status', 'bpsquare-core' ); ?></label>
			<select id="bps_inq_status" name="bps_inq_status" style="width:100%">
				<?php foreach ( self::get_status_options() as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $status, $key ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
	}

	public function render_details_meta_box( \WP_Post $post ): void {
		$fields = [
			'_bps_inq_first_name'    => __( 'First Name', 'bpsquare-core' ),
			'_bps_inq_last_name'     => __( 'Last Name', 'bpsquare-core' ),
			'_bps_inq_business_name' => __( 'Business Name', 'bpsquare-core' ),
			'_bps_inq_business_type' => __( 'Business Type', 'bpsquare-core' ),
			'_bps_inq_current_url'   => __( 'Current Website/App URL', 'bpsquare-core' ),
			'_bps_inq_email'         => __( 'Email', 'bpsquare-core' ),
			'_bps_inq_phone'         => __( 'Phone', 'bpsquare-core' ),
			'_bps_inq_service'       => __( 'Service Interest', 'bpsquare-core' ),
			'_bps_inq_budget'        => __( 'Budget Range', 'bpsquare-core' ),
			'_bps_inq_timeline'      => __( 'Preferred Timeline', 'bpsquare-core' ),
			'_bps_inq_phased'        => __( 'Phased Approach', 'bpsquare-core' ),
			'_bps_inq_problem'       => __( 'Operational Problem', 'bpsquare-core' ),
			'_bps_inq_description'   => __( 'Project Description', 'bpsquare-core' ),
			'_bps_inq_submitted_at'  => __( 'Submitted At', 'bpsquare-core' ),
			'_bps_inq_ip'            => __( 'IP Address', 'bpsquare-core' ),
		];
		?>
		<table class="form-table">
			<?php foreach ( $fields as $key => $label ) :
				$value = get_post_meta( $post->ID, $key, true );
				?>
				<tr>
					<th><?php echo esc_html( $label ); ?></th>
					<td>
						<?php if ( str_contains( $key, 'description' ) || str_contains( $key, 'problem' ) ) : ?>
							<textarea readonly rows="5" style="width:100%"><?php echo esc_textarea( $value ); ?></textarea>
						<?php elseif ( '_bps_inq_email' === $key && $value ) : ?>
							<a href="mailto:<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $value ); ?></a>
						<?php elseif ( '_bps_inq_current_url' === $key && $value ) : ?>
							<a href="<?php echo esc_url( $value ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $value ); ?></a>
						<?php else : ?>
							<code><?php echo esc_html( $value ?: '—' ); ?></code>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
		<?php
	}

	public function save_admin_meta( int $post_id ): void {
		if ( ! isset( $_POST['bps_inquiry_nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bps_inquiry_nonce'] ) ), 'bps_inquiry_save' )
			|| ! current_user_can( 'edit_post', $post_id )
			|| ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		) {
			return;
		}

		if ( isset( $_POST['bps_inq_status'] ) ) {
			$status = sanitize_key( wp_unslash( $_POST['bps_inq_status'] ) );
			if ( array_key_exists( $status, self::get_status_options() ) ) {
				update_post_meta( $post_id, '_bps_inq_status', $status );
			}
		}
	}

	public function columns( array $columns ): array {
		$columns['bps_inq_status'] = __( 'Status', 'bpsquare-core' );
		$columns['bps_inq_service'] = __( 'Service', 'bpsquare-core' );
		$columns['bps_inq_email'] = __( 'Email', 'bpsquare-core' );
		return $columns;
	}

	public function render_column( string $column, int $post_id ): void {
		if ( 'bps_inq_status' === $column ) {
			$status = get_post_meta( $post_id, '_bps_inq_status', true ) ?: 'new';
			echo esc_html( self::get_status_options()[ $status ] ?? $status );
		}
		if ( 'bps_inq_service' === $column ) {
			echo esc_html( get_post_meta( $post_id, '_bps_inq_service', true ) ?: '—' );
		}
		if ( 'bps_inq_email' === $column ) {
			$email = get_post_meta( $post_id, '_bps_inq_email', true );
			echo $email ? '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>' : '—';
		}
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
			'_bps_inq_business_type' => sanitize_text_field( $data['businessType'] ?? '' ),
			'_bps_inq_current_url'   => esc_url_raw( $data['currentWebsite'] ?? '' ),
			'_bps_inq_email'         => $email,
			'_bps_inq_phone'         => sanitize_text_field( $data['phone'] ?? '' ),
			'_bps_inq_service'       => sanitize_text_field( $data['serviceInterest'] ?? '' ),
			'_bps_inq_budget'        => sanitize_text_field( $data['budgetRange'] ?? '' ),
			'_bps_inq_timeline'      => sanitize_text_field( $data['preferredTimeline'] ?? '' ),
			'_bps_inq_phased'        => sanitize_text_field( $data['phasedApproach'] ?? '' ),
			'_bps_inq_problem'       => sanitize_textarea_field( $data['operationalProblem'] ?? '' ),
			'_bps_inq_description'   => sanitize_textarea_field( $data['projectDescription'] ?? '' ),
			'_bps_inq_status'        => 'new',
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

	/**
	 * Inquiry workflow statuses.
	 *
	 * @return array<string, string>
	 */
	private static function get_status_options(): array {
		return [
			'new'                 => __( 'New', 'bpsquare-core' ),
			'reviewed'            => __( 'Reviewed', 'bpsquare-core' ),
			'discovery_scheduled' => __( 'Discovery Scheduled', 'bpsquare-core' ),
			'proposal_sent'       => __( 'Proposal Sent', 'bpsquare-core' ),
			'accepted'            => __( 'Accepted', 'bpsquare-core' ),
			'closed'              => __( 'Closed', 'bpsquare-core' ),
			'not_a_fit'           => __( 'Not a Fit', 'bpsquare-core' ),
		];
	}
}
