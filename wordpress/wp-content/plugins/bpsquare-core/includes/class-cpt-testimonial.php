<?php
/**
 * BPSquare Core — Testimonial Custom Post Type
 *
 * @package BPSquare_Core
 */

namespace BPSquare_Core;

defined( 'ABSPATH' ) || exit;

/**
 * Class CPT_Testimonial
 */
class CPT_Testimonial {

	public const POST_TYPE = 'bps_testimonial';

	public function register(): void {
		add_action( 'init', [ $this, 'register_post_type' ] );
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
		add_action( 'save_post_' . self::POST_TYPE, [ $this, 'save_meta' ] );
		add_action( 'rest_api_init', [ $this, 'register_rest_fields' ] );
	}

	public function register_post_type(): void {
		register_post_type( self::POST_TYPE, [
			'labels'          => [
				'name'          => __( 'Testimonials', 'bpsquare-core' ),
				'singular_name' => __( 'Testimonial', 'bpsquare-core' ),
				'add_new_item'  => __( 'Add New Testimonial', 'bpsquare-core' ),
				'edit_item'     => __( 'Edit Testimonial', 'bpsquare-core' ),
				'all_items'     => __( 'All Testimonials', 'bpsquare-core' ),
			],
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'show_in_rest'    => true,
			'rest_base'       => 'testimonials',
			'menu_icon'       => 'dashicons-format-quote',
			'supports'        => [ 'title', 'editor', 'thumbnail' ],
			'has_archive'     => false,
			'rewrite'         => false,
			'capability_type' => 'post',
		] );
	}

	public function add_meta_boxes(): void {
		add_meta_box(
			'bps_testimonial_details',
			__( 'Testimonial Details', 'bpsquare-core' ),
			[ $this, 'render_meta_box' ],
			self::POST_TYPE,
			'side',
			'high'
		);
	}

	public function render_meta_box( \WP_Post $post ): void {
		wp_nonce_field( 'bps_testimonial_save', 'bps_testimonial_nonce' );

		$author   = get_post_meta( $post->ID, '_bps_testimonial_author', true );
		$business = get_post_meta( $post->ID, '_bps_testimonial_business', true );
		$role     = get_post_meta( $post->ID, '_bps_testimonial_role', true );
		?>
		<p>
			<label for="bps_testimonial_author"><?php esc_html_e( 'Author Name', 'bpsquare-core' ); ?></label><br>
			<input type="text" id="bps_testimonial_author" name="bps_testimonial_author" value="<?php echo esc_attr( $author ); ?>" style="width:100%">
		</p>
		<p>
			<label for="bps_testimonial_business"><?php esc_html_e( 'Business Name', 'bpsquare-core' ); ?></label><br>
			<input type="text" id="bps_testimonial_business" name="bps_testimonial_business" value="<?php echo esc_attr( $business ); ?>" style="width:100%">
		</p>
		<p>
			<label for="bps_testimonial_role"><?php esc_html_e( 'Role / Title', 'bpsquare-core' ); ?></label><br>
			<input type="text" id="bps_testimonial_role" name="bps_testimonial_role" value="<?php echo esc_attr( $role ); ?>" style="width:100%">
		</p>
		<?php
	}

	public function save_meta( int $post_id ): void {
		if ( ! isset( $_POST['bps_testimonial_nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bps_testimonial_nonce'] ) ), 'bps_testimonial_save' )
			|| ! current_user_can( 'edit_post', $post_id )
			|| defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE
		) {
			return;
		}

		$fields = [ 'bps_testimonial_author', 'bps_testimonial_business', 'bps_testimonial_role' ];
		foreach ( $fields as $field ) {
			if ( isset( $_POST[ $field ] ) ) {
				update_post_meta( $post_id, '_' . $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
			}
		}
	}

	public function register_rest_fields(): void {
		$meta_map = [
			'author'        => '_bps_testimonial_author',
			'business_name' => '_bps_testimonial_business',
			'role'          => '_bps_testimonial_role',
		];

		foreach ( $meta_map as $rest_field => $meta_key ) {
			$key = $meta_key;
			register_rest_field( self::POST_TYPE, $rest_field, [
				'get_callback' => fn( array $post ) => (string) get_post_meta( $post['id'], $key, true ),
				'schema'       => [ 'type' => 'string' ],
			] );
		}

		// Expose post content as 'content_text' for easier Angular consumption.
		register_rest_field( self::POST_TYPE, 'content_text', [
			'get_callback' => function ( array $post ): string {
				$p = get_post( $post['id'] );
				return $p ? wp_strip_all_tags( $p->post_content ) : '';
			},
			'schema' => [ 'type' => 'string' ],
		] );
	}
}
