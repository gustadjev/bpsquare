<?php
/**
 * BPSquare Core — Service Custom Post Type
 *
 * @package BPSquare_Core
 */

namespace BPSquare_Core;

defined( 'ABSPATH' ) || exit;

/**
 * Class CPT_Service
 *
 * Registers the bps_service custom post type and its custom meta fields.
 */
class CPT_Service {

	public const POST_TYPE = 'bps_service';

	/**
	 * Register the CPT and related hooks.
	 */
	public function register(): void {
		add_action( 'init', [ $this, 'register_post_type' ] );
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
		add_action( 'save_post_' . self::POST_TYPE, [ $this, 'save_meta' ] );
		add_action( 'rest_api_init', [ $this, 'register_rest_fields' ] );
	}

	/**
	 * Register the post type.
	 */
	public function register_post_type(): void {
		register_post_type( self::POST_TYPE, [
			'labels'       => [
				'name'               => __( 'Services', 'bpsquare-core' ),
				'singular_name'      => __( 'Service', 'bpsquare-core' ),
				'add_new'            => __( 'Add Service', 'bpsquare-core' ),
				'add_new_item'       => __( 'Add New Service', 'bpsquare-core' ),
				'edit_item'          => __( 'Edit Service', 'bpsquare-core' ),
				'all_items'          => __( 'All Services', 'bpsquare-core' ),
				'search_items'       => __( 'Search Services', 'bpsquare-core' ),
				'not_found'          => __( 'No services found.', 'bpsquare-core' ),
				'not_found_in_trash' => __( 'No services found in trash.', 'bpsquare-core' ),
			],
			'public'             => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_rest'       => true,
			'rest_base'          => 'services',
			'menu_icon'          => 'dashicons-hammer',
			'supports'           => [ 'title', 'thumbnail', 'revisions' ],
			'has_archive'        => false,
			'rewrite'            => false,
			'capability_type'    => 'post',
		] );
	}

	/**
	 * Add meta boxes to the service edit screen.
	 */
	public function add_meta_boxes(): void {
		add_meta_box(
			'bps_service_details',
			__( 'Service Details', 'bpsquare-core' ),
			[ $this, 'render_meta_box' ],
			self::POST_TYPE,
			'normal',
			'high'
		);
	}

	/**
	 * Render the meta box HTML.
	 *
	 * @param \WP_Post $post The current post object.
	 */
	public function render_meta_box( \WP_Post $post ): void {
		wp_nonce_field( 'bps_service_save', 'bps_service_nonce' );

		$summary   = get_post_meta( $post->ID, '_bps_service_summary', true );
		$details   = get_post_meta( $post->ID, '_bps_service_details', true );
		$icon      = get_post_meta( $post->ID, '_bps_service_icon', true );
		$cta_label = get_post_meta( $post->ID, '_bps_service_cta_label', true );
		$cta_url   = get_post_meta( $post->ID, '_bps_service_cta_url', true );
		?>
		<table class="form-table">
			<tr>
				<th><label for="bps_service_summary"><?php esc_html_e( 'Short Summary', 'bpsquare-core' ); ?></label></th>
				<td><textarea id="bps_service_summary" name="bps_service_summary" rows="3" style="width:100%"><?php echo esc_textarea( $summary ); ?></textarea></td>
			</tr>
			<tr>
				<th><label for="bps_service_details"><?php esc_html_e( 'Feature List (one per line)', 'bpsquare-core' ); ?></label></th>
				<td><textarea id="bps_service_details" name="bps_service_details" rows="6" style="width:100%"><?php echo esc_textarea( $details ); ?></textarea></td>
			</tr>
			<tr>
				<th><label for="bps_service_icon"><?php esc_html_e( 'Material Icon Name', 'bpsquare-core' ); ?></label></th>
				<td><input type="text" id="bps_service_icon" name="bps_service_icon" value="<?php echo esc_attr( $icon ); ?>" style="width:100%" placeholder="e.g. web, code, design_services"></td>
			</tr>
			<tr>
				<th><label for="bps_service_cta_label"><?php esc_html_e( 'CTA Label', 'bpsquare-core' ); ?></label></th>
				<td><input type="text" id="bps_service_cta_label" name="bps_service_cta_label" value="<?php echo esc_attr( $cta_label ); ?>" style="width:100%" placeholder="Learn More"></td>
			</tr>
			<tr>
				<th><label for="bps_service_cta_url"><?php esc_html_e( 'CTA URL (optional)', 'bpsquare-core' ); ?></label></th>
				<td><input type="url" id="bps_service_cta_url" name="bps_service_cta_url" value="<?php echo esc_attr( $cta_url ); ?>" style="width:100%" placeholder="https://"></td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Save meta box data.
	 *
	 * @param int $post_id The post ID.
	 */
	public function save_meta( int $post_id ): void {
		if ( ! isset( $_POST['bps_service_nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bps_service_nonce'] ) ), 'bps_service_save' )
			|| ! current_user_can( 'edit_post', $post_id )
			|| defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE
		) {
			return;
		}

		$fields = [
			'_bps_service_summary'   => 'bps_service_summary',
			'_bps_service_details'   => 'bps_service_details',
			'_bps_service_icon'      => 'bps_service_icon',
			'_bps_service_cta_label' => 'bps_service_cta_label',
		];
		foreach ( $fields as $meta_key => $field_name ) {
			if ( isset( $_POST[ $field_name ] ) ) {
				update_post_meta( $post_id, $meta_key, sanitize_textarea_field( wp_unslash( $_POST[ $field_name ] ) ) );
			}
		}
		if ( isset( $_POST['bps_service_cta_url'] ) ) {
			update_post_meta( $post_id, '_bps_service_cta_url', esc_url_raw( wp_unslash( $_POST['bps_service_cta_url'] ) ) );
		}
	}

	/**
	 * Register custom fields for the REST API.
	 */
	public function register_rest_fields(): void {
		$meta_map = [
			'summary'   => '_bps_service_summary',
			'details'   => '_bps_service_details',
			'icon'      => '_bps_service_icon',
			'cta_label' => '_bps_service_cta_label',
			'cta_url'   => '_bps_service_cta_url',
		];

		foreach ( $meta_map as $rest_field => $meta_key ) {
			$key = $meta_key; // capture for closure.
			register_rest_field( self::POST_TYPE, $rest_field, [
				'get_callback' => function ( array $post ) use ( $key ): string {
					return (string) get_post_meta( $post['id'], $key, true );
				},
				'schema'       => [ 'type' => 'string' ],
			] );
		}
	}
}
