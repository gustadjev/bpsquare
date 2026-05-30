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
				'title'   => 'Custom Web Apps for Service Businesses',
				'summary' => 'Client portals, intake systems, dashboards, and business-specific applications for service-based small businesses.',
				'details' => "Role-based workflows\nAdmin dashboards\nForms, requests, and status tracking\nREST API and database integration\nMaintainable first-release scope",
				'icon'    => 'code',
				'order'   => 1,
			],
			[
				'title'   => 'Internal Tools & Workflow Automation',
				'summary' => 'Practical systems that reduce spreadsheet work, repeated emails, manual handoffs, and disconnected processes.',
				'details' => "Workflow mapping\nForm and notification automation\nInternal dashboards\nReporting views\nData movement between tools",
				'icon'    => 'schema',
				'order'   => 2,
			],
			[
				'title'   => 'Small Business Operations Modernization',
				'summary' => 'A phased path from informal manual processes to clearer, trackable digital operations.',
				'details' => "Current-state review\nRequirements gathering\nProcess redesign\nPhased implementation plans\nTraining-friendly handoff",
				'icon'    => 'trending_up',
				'order'   => 3,
			],
			[
				'title'   => 'WordPress + Angular Integration',
				'summary' => 'Headless WordPress, Angular frontends, and custom REST endpoints for content-managed business experiences.',
				'details' => "Custom post types\nAngular frontends\nWordPress REST API\nAdmin-friendly content management\nBusiness system integrations",
				'icon'    => 'integration_instructions',
				'order'   => 4,
			],
			[
				'title'   => 'Custom WordPress Theme Development',
				'summary' => 'Lightweight, business-specific WordPress themes and admin experiences built for clarity, speed, and maintainability.',
				'details' => "Custom themes\nAdmin settings\nReusable templates\nSEO and performance foundations\nPlugin-aware implementation",
				'icon'    => 'dashboard_customize',
				'order'   => 5,
			],
			[
				'title'   => 'Project Scoping & Technical Consulting',
				'summary' => 'Guidance for early-stage technology decisions, requirements, and realistic first-release planning.',
				'details' => "Discovery sessions\nRequirements analysis\nArchitecture recommendations\nVendor/platform evaluation\nTechnical documentation",
				'icon'    => 'support_agent',
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
				'title'        => 'Service Business Intake Workflow',
				'status'       => 'concept',
				'client_type'  => 'Service-based small business',
				'problem'      => 'A service business receives requests through calls, emails, and forms, making follow-up inconsistent and hard to track.',
				'constraints'  => 'The first release needs to be affordable, easy to manage, and useful without forcing the team to change every process at once.',
				'solution'     => 'BPSquare LLC scopes a centralized intake flow with structured forms, status tracking, admin review, and email notifications.',
				'technologies' => 'Angular, WordPress REST API, PHP, MySQL',
				'outcome'      => 'Early project pattern: reduce manual triage and make every request easier to see, assign, and follow up.',
				'lessons'      => 'Start with the smallest workflow that creates visibility, then expand after the team proves the process.',
				'confidential' => '1',
			],
			[
				'title'        => 'Internal Operations Dashboard',
				'status'       => 'in_progress',
				'client_type'  => 'Small operations team',
				'problem'      => 'A small team manages work from spreadsheets and messages, leaving no single view of active tasks, clients, or next steps.',
				'constraints'  => 'The dashboard must support existing habits while gradually replacing fragile manual tracking.',
				'solution'     => 'BPSquare LLC designs a lightweight internal dashboard that starts with the most important workflow and can expand in phases.',
				'technologies' => 'Angular, REST API, MySQL, Role-Based Access',
				'outcome'      => 'Early project pattern: create visibility before adding complexity.',
				'lessons'      => 'A useful internal tool is often less about adding features and more about making the next action obvious.',
				'confidential' => '1',
			],
			[
				'title'        => 'Custom WordPress Business System',
				'status'       => 'volunteer',
				'client_type'  => 'Community-focused business',
				'problem'      => 'A business needs a content-managed website, but also needs structured services, inquiries, FAQs, and business-specific admin screens.',
				'constraints'  => 'The owner needs familiar content editing without giving up frontend control or custom business logic.',
				'solution'     => 'BPSquare LLC builds a custom WordPress theme and plugin foundation with custom post types, REST endpoints, and a modern frontend.',
				'technologies' => 'WordPress, PHP, Custom Theme, Angular, SCSS',
				'outcome'      => 'Early project pattern: use WordPress as a manageable business backend, not just a page builder.',
				'lessons'      => 'WordPress works best for custom business sites when the admin model is designed around the real content and workflow.',
				'confidential' => '0',
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
				update_post_meta( $post_id, '_bps_cs_status', sanitize_key( $cs['status'] ) );
				update_post_meta( $post_id, '_bps_cs_client_type', sanitize_text_field( $cs['client_type'] ) );
				update_post_meta( $post_id, '_bps_cs_constraints', sanitize_textarea_field( $cs['constraints'] ) );
				update_post_meta( $post_id, '_bps_cs_solution', sanitize_textarea_field( $cs['solution'] ) );
				update_post_meta( $post_id, '_bps_cs_technologies', sanitize_text_field( $cs['technologies'] ) );
				update_post_meta( $post_id, '_bps_cs_outcome', sanitize_textarea_field( $cs['outcome'] ) );
				update_post_meta( $post_id, '_bps_cs_lessons', sanitize_textarea_field( $cs['lessons'] ) );
				update_post_meta( $post_id, '_bps_cs_is_confidential', sanitize_text_field( $cs['confidential'] ) );
			}
		}

		update_option( 'bpsquare_seeded', true );
	}
}
