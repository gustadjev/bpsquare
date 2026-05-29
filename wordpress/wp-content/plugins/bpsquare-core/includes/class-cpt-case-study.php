<?php
/**
 * BPSquare Core — Case Study Custom Post Type
 *
 * @package BPSquare_Core
 */

namespace BPSquare_Core;

defined( 'ABSPATH' ) || exit;

/**
 * Class CPT_Case_Study
 */
class CPT_Case_Study {

	public const POST_TYPE = 'bps_case_study';

	public function register(): void {
		add_action( 'init', [ $this, 'register_post_type' ] );
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
		add_action( 'save_post_' . self::POST_TYPE, [ $this, 'save_meta' ] );
		add_action( 'rest_api_init', [ $this, 'register_rest_fields' ] );
	}

	public function register_post_type(): void {
		register_post_type( self::POST_TYPE, [
			'labels'          => [
				'name'          => __( 'Case Studies', 'bpsquare-core' ),
				'singular_name' => __( 'Case Study', 'bpsquare-core' ),
				'add_new_item'  => __( 'Add New Case Study', 'bpsquare-core' ),
				'edit_item'     => __( 'Edit Case Study', 'bpsquare-core' ),
				'all_items'     => __( 'All Case Studies', 'bpsquare-core' ),
			],
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'show_in_rest'    => true,
			'rest_base'       => 'case-studies',
			'menu_icon'       => 'dashicons-portfolio',
			'supports'        => [ 'title', 'thumbnail', 'revisions' ],
			'has_archive'     => false,
			'rewrite'         => false,
			'capability_type' => 'post',
		] );
	}

	public function add_meta_boxes(): void {
		add_meta_box(
			'bps_case_study_details',
			__( 'Case Study Details', 'bpsquare-core' ),
			[ $this, 'render_meta_box' ],
			self::POST_TYPE,
			'normal',
			'high'
		);
	}

	public function render_meta_box( \WP_Post $post ): void {
		wp_nonce_field( 'bps_cs_save', 'bps_cs_nonce' );

		$fields = [
			'_bps_cs_problem'      => __( 'Problem', 'bpsquare-core' ),
			'_bps_cs_solution'     => __( 'Solution', 'bpsquare-core' ),
			'_bps_cs_technologies' => __( 'Technologies (comma-separated)', 'bpsquare-core' ),
			'_bps_cs_outcome'      => __( 'Outcome', 'bpsquare-core' ),
		];
		?>
		<table class="form-table">
		<?php foreach ( $fields as $meta_key => $label ) :
			$value = get_post_meta( $post->ID, $meta_key, true );
			$id    = ltrim( $meta_key, '_' );
			?>
			<tr>
				<th><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label></th>
				<td><textarea id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $id ); ?>" rows="4" style="width:100%"><?php echo esc_textarea( $value ); ?></textarea></td>
			</tr>
		<?php endforeach; ?>
		</table>
		<?php
	}

	public function save_meta( int $post_id ): void {
		if ( ! isset( $_POST['bps_cs_nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bps_cs_nonce'] ) ), 'bps_cs_save' )
			|| ! current_user_can( 'edit_post', $post_id )
			|| defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE
		) {
			return;
		}

		$fields = [ 'bps_cs_problem', 'bps_cs_solution', 'bps_cs_technologies', 'bps_cs_outcome' ];
		foreach ( $fields as $field ) {
			if ( isset( $_POST[ $field ] ) ) {
				update_post_meta( $post_id, '_' . $field, sanitize_textarea_field( wp_unslash( $_POST[ $field ] ) ) );
			}
		}
	}

	public function register_rest_fields(): void {
		$meta_map = [
			'problem'      => '_bps_cs_problem',
			'solution'     => '_bps_cs_solution',
			'technologies' => '_bps_cs_technologies',
			'outcome'      => '_bps_cs_outcome',
		];

		foreach ( $meta_map as $rest_field => $meta_key ) {
			$key = $meta_key;
			register_rest_field( self::POST_TYPE, $rest_field, [
				'get_callback' => function ( array $post ) use ( $key ): string {
					return (string) get_post_meta( $post['id'], $key, true );
				},
				'schema' => [ 'type' => 'string' ],
			] );
		}
	}
}
