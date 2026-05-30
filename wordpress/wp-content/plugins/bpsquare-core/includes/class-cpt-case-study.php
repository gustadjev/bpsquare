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
				'name'          => __( 'Project Lab', 'bpsquare-core' ),
				'singular_name' => __( 'Project Lab Entry', 'bpsquare-core' ),
				'add_new_item'  => __( 'Add New Project Lab Entry', 'bpsquare-core' ),
				'edit_item'     => __( 'Edit Project Lab Entry', 'bpsquare-core' ),
				'all_items'     => __( 'All Project Lab Entries', 'bpsquare-core' ),
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
			'_bps_cs_status'         => __( 'Project Status', 'bpsquare-core' ),
			'_bps_cs_client_type'    => __( 'Client Type', 'bpsquare-core' ),
			'_bps_cs_problem'        => __( 'Problem', 'bpsquare-core' ),
			'_bps_cs_constraints'    => __( 'Constraints', 'bpsquare-core' ),
			'_bps_cs_solution'       => __( 'Solution', 'bpsquare-core' ),
			'_bps_cs_technologies'   => __( 'Tools Used (comma-separated)', 'bpsquare-core' ),
			'_bps_cs_outcome'        => __( 'Outcome', 'bpsquare-core' ),
			'_bps_cs_lessons'        => __( 'Lessons Learned', 'bpsquare-core' ),
			'_bps_cs_is_confidential'=> __( 'Confidential?', 'bpsquare-core' ),
		];
		?>
		<table class="form-table">
		<?php foreach ( $fields as $meta_key => $label ) :
			$value = get_post_meta( $post->ID, $meta_key, true );
			$id    = ltrim( $meta_key, '_' );
			?>
			<tr>
				<th><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label></th>
				<td>
					<?php if ( '_bps_cs_status' === $meta_key ) : ?>
						<select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $id ); ?>" style="width:100%">
							<?php foreach ( $this->get_status_options() as $status_key => $status_label ) : ?>
								<option value="<?php echo esc_attr( $status_key ); ?>" <?php selected( $value ?: 'concept', $status_key ); ?>><?php echo esc_html( $status_label ); ?></option>
							<?php endforeach; ?>
						</select>
					<?php elseif ( '_bps_cs_is_confidential' === $meta_key ) : ?>
						<label>
							<input type="checkbox" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $id ); ?>" value="1" <?php checked( '1', $value ); ?>>
							<?php esc_html_e( 'Keep client details general or anonymized.', 'bpsquare-core' ); ?>
						</label>
					<?php else : ?>
						<textarea id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $id ); ?>" rows="4" style="width:100%"><?php echo esc_textarea( $value ); ?></textarea>
					<?php endif; ?>
				</td>
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

		$fields = [
			'bps_cs_status',
			'bps_cs_client_type',
			'bps_cs_problem',
			'bps_cs_constraints',
			'bps_cs_solution',
			'bps_cs_technologies',
			'bps_cs_outcome',
			'bps_cs_lessons',
		];
		foreach ( $fields as $field ) {
			if ( isset( $_POST[ $field ] ) ) {
				update_post_meta( $post_id, '_' . $field, sanitize_textarea_field( wp_unslash( $_POST[ $field ] ) ) );
			}
		}
		update_post_meta( $post_id, '_bps_cs_is_confidential', isset( $_POST['bps_cs_is_confidential'] ) ? '1' : '0' );
	}

	public function register_rest_fields(): void {
		$meta_map = [
			'status'         => '_bps_cs_status',
			'clientType'     => '_bps_cs_client_type',
			'problem'        => '_bps_cs_problem',
			'constraints'    => '_bps_cs_constraints',
			'solution'       => '_bps_cs_solution',
			'technologies'   => '_bps_cs_technologies',
			'outcome'        => '_bps_cs_outcome',
			'lessonsLearned' => '_bps_cs_lessons',
			'isConfidential' => '_bps_cs_is_confidential',
		];

		foreach ( $meta_map as $rest_field => $meta_key ) {
			$key = $meta_key;
			register_rest_field( self::POST_TYPE, $rest_field, [
				'get_callback' => function ( array $post ) use ( $key ) {
					if ( '_bps_cs_is_confidential' === $key ) {
						return '1' === (string) get_post_meta( $post['id'], $key, true );
					}
					return (string) get_post_meta( $post['id'], $key, true );
				},
				'schema' => [ 'type' => '_bps_cs_is_confidential' === $key ? 'boolean' : 'string' ],
			] );
		}
	}

	/**
	 * Project status options used by the admin UI and public API.
	 *
	 * @return array<string, string>
	 */
	private function get_status_options(): array {
		return [
			'concept'     => __( 'Concept', 'bpsquare-core' ),
			'volunteer'   => __( 'Volunteer', 'bpsquare-core' ),
			'in_progress' => __( 'In Progress', 'bpsquare-core' ),
			'completed'   => __( 'Completed', 'bpsquare-core' ),
		];
	}
}
