<?php
/**
 * BPSquare Core — Maintenance Mode
 *
 * This site uses headless WordPress + Angular. The Angular frontend is served
 * directly by Apache (index.html), so WordPress template hooks never fire for
 * public routes. Maintenance mode is therefore implemented via a flag file:
 *
 *   - When the admin enables maintenance mode, a file named `.bpsquare_maintenance`
 *     is created in ABSPATH (the WordPress / webroot directory).
 *   - The .htaccess "BPSquare Angular SPA" block checks for that file and
 *     redirects all public traffic to under-construction.html.
 *   - When the admin disables maintenance mode the flag file is removed.
 *   - Admins can still access /wp-admin and /wp-json regardless (excluded in
 *     the htaccess rule).
 *
 * @package BPSquare_Core
 */

namespace BPSquare_Core;

defined( 'ABSPATH' ) || exit;

/**
 * Class Maintenance_Mode
 */
class Maintenance_Mode {

	/** Absolute path to the flag file. */
	private const FLAG_FILE = ABSPATH . '.bpsquare_maintenance';

	public function register(): void {
		// Write or remove the flag file whenever the option is saved.
		add_action( 'update_option_bpsquare_maintenance_mode', [ $this, 'sync_flag_file' ], 10, 2 );
		add_action( 'add_option_bpsquare_maintenance_mode',    [ $this, 'sync_flag_file_on_add' ], 10, 2 );

		// Admin bar indicator so admins always know the current state.
		add_action( 'admin_bar_menu', [ $this, 'add_admin_bar_indicator' ], 100 );
		add_action( 'admin_head',     [ $this, 'add_admin_bar_styles' ] );
	}

	// ------------------------------------------------------------------
	// Flag file management
	// ------------------------------------------------------------------

	/**
	 * Called when the option is updated (old value → new value).
	 *
	 * @param mixed $old_value Previous option value.
	 * @param mixed $new_value New option value.
	 */
	public function sync_flag_file( $old_value, $new_value ): void {
		$this->apply_flag( (bool) $new_value );
	}

	/**
	 * Called the first time the option is created (add_option).
	 *
	 * @param string $option Option name.
	 * @param mixed  $value  Option value.
	 */
	public function sync_flag_file_on_add( string $option, $value ): void {
		$this->apply_flag( (bool) $value );
	}

	/**
	 * Create or remove the .bpsquare_maintenance flag file.
	 *
	 * @param bool $enable True = maintenance on; false = maintenance off.
	 */
	private function apply_flag( bool $enable ): void {
		$flag = self::FLAG_FILE;

		if ( $enable ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
			file_put_contents( $flag, gmdate( 'Y-m-d H:i:s' ) . "\n" );
		} elseif ( file_exists( $flag ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink
			unlink( $flag );
		}
	}

	// ------------------------------------------------------------------
	// Admin bar indicator
	// ------------------------------------------------------------------

	/**
	 * Add a visual indicator in the WP admin bar showing the current mode.
	 *
	 * @param \WP_Admin_Bar $admin_bar WordPress admin bar object.
	 */
	public function add_admin_bar_indicator( \WP_Admin_Bar $admin_bar ): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$is_on = file_exists( self::FLAG_FILE );

		$admin_bar->add_node( [
			'id'    => 'bpsquare-maintenance',
			'title' => $is_on
				? '🚧 Maintenance: ON'
				: '✅ Site: Live',
			'href'  => admin_url( 'admin.php?page=bpsquare-settings' ),
			'meta'  => [
				'title' => $is_on
					? 'Maintenance mode is ON — visitors see the Coming Soon page. Click to change.'
					: 'Site is live — click to toggle maintenance mode.',
			],
		] );
	}

	/**
	 * Style the admin bar node so the status is easy to spot.
	 */
	public function add_admin_bar_styles(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<style>
			#wp-admin-bar-bpsquare-maintenance > .ab-item { font-weight: 700 !important; }
		</style>
		<?php
	}
}
