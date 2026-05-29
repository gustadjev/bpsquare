<?php
/**
 * BPSquare Core — Admin Settings
 *
 * Adds a settings page under the BPSquare admin menu for configuring
 * email addresses and other plugin options.
 *
 * @package BPSquare_Core
 */

namespace BPSquare_Core;

defined( 'ABSPATH' ) || exit;

/**
 * Class Admin_Settings
 */
class Admin_Settings {

	public function register(): void {
		add_action( 'admin_menu', [ $this, 'add_menu' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_notices', [ $this, 'headless_notice' ] );
	}

	public function add_menu(): void {
		add_menu_page(
			__( 'BPSquare Settings', 'bpsquare-core' ),
			'BPSquare',
			'manage_options',
			'bpsquare-settings',
			[ $this, 'render_settings_page' ],
			'dashicons-star-filled',
			30
		);

		add_submenu_page(
			'bpsquare-settings',
			__( 'Settings', 'bpsquare-core' ),
			__( 'Settings', 'bpsquare-core' ),
			'manage_options',
			'bpsquare-settings',
			[ $this, 'render_settings_page' ]
		);
	}

	public function register_settings(): void {
		register_setting( 'bpsquare_settings_group', 'bpsquare_maintenance_mode', [
			'type'              => 'boolean',
			'sanitize_callback' => 'rest_sanitize_boolean',
			'default'           => false,
		] );
		register_setting( 'bpsquare_settings_group', 'bpsquare_admin_email', [
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_email',
			'default'           => get_option( 'admin_email' ),
		] );
		register_setting( 'bpsquare_settings_group', 'bpsquare_from_email', [
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_email',
			'default'           => 'noreply@bpsquarellc.com',
		] );
		register_setting( 'bpsquare_settings_group', 'bpsquare_from_name', [
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => 'BPSquare LLC',
		] );
		register_setting( 'bpsquare_settings_group', 'bpsquare_allowed_origin', [
			'type'              => 'string',
			'sanitize_callback' => 'esc_url_raw',
			'default'           => 'http://localhost:4200',
		] );
	}

	public function render_settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'BPSquare Settings', 'bpsquare-core' ); ?></h1>

			<?php
			$maintenance_on = (bool) get_option( 'bpsquare_maintenance_mode', false );
			$status_color   = $maintenance_on ? '#f59e0b' : '#22c55e';
			$status_text    = $maintenance_on
				? __( '🚧 Maintenance Mode is currently ON — visitors see the Coming Soon page.', 'bpsquare-core' )
				: __( '✅ Site is currently LIVE — all visitors see the full website.', 'bpsquare-core' );
			?>
			<div style="background:#fff;border-left:4px solid <?php echo esc_attr( $status_color ); ?>;padding:12px 16px;margin:16px 0;border-radius:4px;font-weight:600;font-size:14px;">
				<?php echo esc_html( $status_text ); ?>
			</div>

			<form method="post" action="options.php">
				<?php settings_fields( 'bpsquare_settings_group' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Maintenance Mode', 'bpsquare-core' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="bpsquare_maintenance_mode" value="1"
									<?php checked( 1, get_option( 'bpsquare_maintenance_mode', 0 ) ); ?> />
								<?php esc_html_e( 'Enable Coming Soon / Maintenance Mode', 'bpsquare-core' ); ?>
							</label>
							<p class="description">
								<?php esc_html_e( 'When checked, public visitors see the Coming Soon page. Logged-in administrators always see the live site.', 'bpsquare-core' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="bpsquare_admin_email"><?php esc_html_e( 'Inquiry Notification Email', 'bpsquare-core' ); ?></label></th>
						<td>
							<input type="email" id="bpsquare_admin_email" name="bpsquare_admin_email" value="<?php echo esc_attr( get_option( 'bpsquare_admin_email', get_option( 'admin_email' ) ) ); ?>" class="regular-text">
							<p class="description"><?php esc_html_e( 'Address that receives new inquiry notifications.', 'bpsquare-core' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="bpsquare_from_email"><?php esc_html_e( 'From Email', 'bpsquare-core' ); ?></label></th>
						<td>
							<input type="email" id="bpsquare_from_email" name="bpsquare_from_email" value="<?php echo esc_attr( get_option( 'bpsquare_from_email', 'noreply@bpsquarellc.com' ) ); ?>" class="regular-text">
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="bpsquare_from_name"><?php esc_html_e( 'From Name', 'bpsquare-core' ); ?></label></th>
						<td>
							<input type="text" id="bpsquare_from_name" name="bpsquare_from_name" value="<?php echo esc_attr( get_option( 'bpsquare_from_name', 'BPSquare LLC' ) ); ?>" class="regular-text">
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="bpsquare_allowed_origin"><?php esc_html_e( 'Allowed CORS Origin', 'bpsquare-core' ); ?></label></th>
						<td>
							<input type="url" id="bpsquare_allowed_origin" name="bpsquare_allowed_origin" value="<?php echo esc_attr( get_option( 'bpsquare_allowed_origin', 'http://localhost:4200' ) ); ?>" class="regular-text" placeholder="https://bpsquarellc.com">
							<p class="description"><?php esc_html_e( 'The Angular frontend URL that is allowed to call the WordPress REST API.', 'bpsquare-core' ); ?></p>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Show an admin notice reminding editors this is a headless site,
	 * and highlight maintenance mode status on the dashboard.
	 */
	public function headless_notice(): void {
		$screen = get_current_screen();
		if ( ! $screen || 'dashboard' !== $screen->id ) {
			return;
		}

		echo '<div class="bpsquare-admin-notice notice notice-info"><p>'
			. esc_html__( '🚀 BPSquare LLC — This is a headless WordPress setup. The public website is served by Angular. Manage content here and it will appear on the live site.', 'bpsquare-core' )
			. '</p></div>';

		$maintenance_on = (bool) get_option( 'bpsquare_maintenance_mode', false );
		if ( $maintenance_on ) {
			echo '<div class="notice notice-warning"><p>'
				. wp_kses(
					sprintf(
						/* translators: %s: settings page link */
						__( '🚧 <strong>Maintenance Mode is ON.</strong> Visitors are seeing the Coming Soon page. <a href="%s">Turn it off in BPSquare Settings</a> when you are ready to go live.', 'bpsquare-core' ),
						esc_url( admin_url( 'admin.php?page=bpsquare-settings' ) )
					),
					[ 'strong' => [], 'a' => [ 'href' => [] ] ]
				)
				. '</p></div>';
		}
	}
}
