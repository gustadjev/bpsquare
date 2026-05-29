<?php
/**
 * BPSquare Core — Main Plugin Class
 *
 * @package BPSquare_Core
 */

namespace BPSquare_Core;

defined( 'ABSPATH' ) || exit;

/**
 * Class Plugin
 *
 * Singleton that wires up all plugin components.
 */
final class Plugin {

	/** @var Plugin|null */
	private static ?Plugin $instance = null;

	/**
	 * Returns/creates the plugin singleton.
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Plugin constructor — hooks into WordPress lifecycle.
	 */
	private function __construct() {
		add_action( 'plugins_loaded', [ $this, 'init' ] );
		register_activation_hook( BPSQUARE_CORE_BASENAME, [ $this, 'activate' ] );
		register_deactivation_hook( BPSQUARE_CORE_BASENAME, [ $this, 'deactivate' ] );
	}

	/**
	 * Initialise all plugin components after WordPress is loaded.
	 */
	public function init(): void {
		$this->load_textdomain();

		( new CPT_Service() )->register();
		( new CPT_Case_Study() )->register();
		( new CPT_Testimonial() )->register();
		( new CPT_FAQ() )->register();
		( new CPT_Inquiry() )->register();

		( new Maintenance_Mode() )->register();

		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
		( new Admin_Settings() )->register();
	}

	/**
	 * Register all custom REST API routes.
	 */
	public function register_rest_routes(): void {
		( new REST_Inquiries_Controller() )->register_routes();
		( new REST_Content_Controller() )->register_routes();
	}

	/**
	 * Plugin activation callback.
	 */
	public function activate(): void {
		( new CPT_Service() )->register();
		( new CPT_Case_Study() )->register();
		( new CPT_Testimonial() )->register();
		( new CPT_FAQ() )->register();
		( new CPT_Inquiry() )->register();
		flush_rewrite_rules();
		$this->maybe_create_tables();
		$this->seed_default_content();
	}

	/**
	 * Plugin deactivation callback.
	 */
	public function deactivate(): void {
		flush_rewrite_rules();
	}

	/**
	 * Load plugin text domain for translations.
	 */
	private function load_textdomain(): void {
		load_plugin_textdomain(
			'bpsquare-core',
			false,
			dirname( BPSQUARE_CORE_BASENAME ) . '/languages'
		);
	}

	/**
	 * Create custom database tables if needed.
	 */
	private function maybe_create_tables(): void {
		global $wpdb;
		$table   = $wpdb->prefix . 'bpsquare_rate_limit';
		$charset = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS {$table} (
			id          BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			ip_hash     VARCHAR(64) NOT NULL,
			action_key  VARCHAR(64) NOT NULL DEFAULT 'inquiry',
			hit_count   INT UNSIGNED NOT NULL DEFAULT 1,
			window_start DATETIME NOT NULL,
			PRIMARY KEY (id),
			UNIQUE KEY ip_action (ip_hash, action_key)
		) {$charset};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Seed default Services and Case Studies if none exist yet.
	 */
	private function seed_default_content(): void {
		if ( get_option( 'bpsquare_seeded', false ) ) {
			return;
		}

		$services = [
			[
				'title'   => 'Website Development',
				'summary' => 'Professional websites for small businesses, service providers, community organizations, and entrepreneurs.',
				'details' => "Responsive design\nSEO-ready structure\nContact forms\nService pages\nContent organization\nPerformance optimization",
				'icon'    => 'web',
				'order'   => 1,
			],
			[
				'title'   => 'Custom Web Application Development',
				'summary' => 'Custom business applications built to improve workflows, reduce manual work, and support business operations.',
				'details' => "Admin dashboards\nInternal tools\nData-driven applications\nRole-based access\nAPI integrations",
				'icon'    => 'code',
				'order'   => 2,
			],
			[
				'title'   => 'UI/UX Design & Prototyping',
				'summary' => 'Design support before development to reduce confusion and improve user experience.',
				'details' => "Discovery sessions\nWireframes\nLow-fidelity prototypes\nHigh-fidelity prototypes\nFigma-ready design concepts",
				'icon'    => 'design_services',
				'order'   => 3,
			],
			[
				'title'   => 'Business Process Automation',
				'summary' => 'Help businesses simplify repetitive processes and improve efficiency.',
				'details' => "Workflow analysis\nForm automation\nData movement\nReporting support\nIntegration planning",
				'icon'    => 'auto_awesome',
				'order'   => 4,
			],
			[
				'title'   => 'Technical Consulting',
				'summary' => 'Guidance for businesses that need help making technology decisions.',
				'details' => "Project scoping\nRequirements gathering\nSystem recommendations\nVendor/platform evaluation\nTechnical documentation",
				'icon'    => 'support_agent',
				'order'   => 5,
			],
			[
				'title'   => 'Website Maintenance & Support',
				'summary' => 'Ongoing website and application support after launch.',
				'details' => "Updates\nBug fixes\nContent changes\nPerformance reviews\nSecurity patch coordination",
				'icon'    => 'build',
				'order'   => 6,
			],
		];

		foreach ( $services as $service ) {
			$post_id = wp_insert_post( [
				'post_type'   => 'bps_service',
				'post_title'  => sanitize_text_field( $service['title'] ),
				'post_status' => 'publish',
				'menu_order'  => $service['order'],
			] );
			if ( ! is_wp_error( $post_id ) ) {
				update_post_meta( $post_id, '_bps_service_summary', sanitize_textarea_field( $service['summary'] ) );
				update_post_meta( $post_id, '_bps_service_details', sanitize_textarea_field( $service['details'] ) );
				update_post_meta( $post_id, '_bps_service_icon', sanitize_text_field( $service['icon'] ) );
				update_post_meta( $post_id, '_bps_service_cta_label', 'Learn More' );
			}
		}

		$case_studies = [
			[
				'title'        => 'Small Business Website Redesign',
				'problem'      => 'A local service-based business had an outdated website that was not mobile-friendly and generated no leads.',
				'solution'     => 'BPSquare LLC designed and built a modern, responsive WordPress website with a clear service overview, contact form, and local SEO structure.',
				'technologies' => 'WordPress, PHP, SCSS, Custom Theme',
				'outcome'      => 'The client saw a 40% increase in contact form submissions within the first 60 days of launch.',
			],
			[
				'title'        => 'Internal Business Workflow Tool',
				'problem'      => 'A small business was tracking customer orders in spreadsheets, leading to errors and delays.',
				'solution'     => 'BPSquare LLC built a custom web application with an admin dashboard, order tracking, and status notifications.',
				'technologies' => 'Angular, Node.js, MySQL, REST API',
				'outcome'      => 'Manual data entry time was reduced by over 60%, and the team reported fewer errors and faster order processing.',
			],
			[
				'title'        => 'Service-Based Business Landing Page',
				'problem'      => 'A professional consultant had no online presence and was relying entirely on word-of-mouth referrals.',
				'solution'     => 'BPSquare LLC created a focused landing page highlighting services, credentials, and a consultation booking call-to-action.',
				'technologies' => 'WordPress, Custom Theme, Contact Form 7',
				'outcome'      => 'The consultant began receiving consistent online inquiries within weeks of launch.',
			],
		];

		foreach ( $case_studies as $cs ) {
			$post_id = wp_insert_post( [
				'post_type'   => 'bps_case_study',
				'post_title'  => sanitize_text_field( $cs['title'] ),
				'post_status' => 'publish',
			] );
			if ( ! is_wp_error( $post_id ) ) {
				update_post_meta( $post_id, '_bps_cs_problem', sanitize_textarea_field( $cs['problem'] ) );
				update_post_meta( $post_id, '_bps_cs_solution', sanitize_textarea_field( $cs['solution'] ) );
				update_post_meta( $post_id, '_bps_cs_technologies', sanitize_text_field( $cs['technologies'] ) );
				update_post_meta( $post_id, '_bps_cs_outcome', sanitize_textarea_field( $cs['outcome'] ) );
			}
		}

		update_option( 'bpsquare_seeded', true );
	}
}
