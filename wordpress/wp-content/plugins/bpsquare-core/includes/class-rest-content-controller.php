<?php
/**
 * BPSquare Core — REST Content Controller
 *
 * Provides read-only GET endpoints for Angular to consume:
 *   GET /wp-json/bpsquare/v1/services
 *   GET /wp-json/bpsquare/v1/case-studies
 *   GET /wp-json/bpsquare/v1/testimonials
 *   GET /wp-json/bpsquare/v1/faqs
 *
 * @package BPSquare_Core
 */

namespace BPSquare_Core;

defined( 'ABSPATH' ) || exit;

/**
 * Class REST_Content_Controller
 */
class REST_Content_Controller extends \WP_REST_Controller {

	protected $namespace = 'bpsquare/v1';

	public function register_routes(): void {
		$endpoints = [
			'services'     => [ CPT_Service::POST_TYPE,    [ $this, 'get_services' ] ],
			'case-studies' => [ CPT_Case_Study::POST_TYPE, [ $this, 'get_case_studies' ] ],
			'testimonials' => [ CPT_Testimonial::POST_TYPE,[ $this, 'get_testimonials' ] ],
			'faqs'         => [ CPT_FAQ::POST_TYPE,        [ $this, 'get_faqs' ] ],
		];

		foreach ( $endpoints as $route => [ $post_type, $callback ] ) {
			register_rest_route( $this->namespace, '/' . $route, [
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => $callback,
					'permission_callback' => '__return_true',
				],
			] );
		}
	}

	/**
	 * GET /wp-json/bpsquare/v1/services
	 *
	 * @return \WP_REST_Response
	 */
	public function get_services( \WP_REST_Request $request ): \WP_REST_Response {
		$posts = get_posts( [
			'post_type'      => CPT_Service::POST_TYPE,
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		] );

		$data = array_map( function ( \WP_Post $post ): array {
			return [
				'id'        => $post->ID,
				'title'     => wp_strip_all_tags( $post->post_title ),
				'summary'   => (string) get_post_meta( $post->ID, '_bps_service_summary', true ),
				'details'   => array_filter( array_map( 'trim', explode( "\n", (string) get_post_meta( $post->ID, '_bps_service_details', true ) ) ) ),
				'icon'      => (string) get_post_meta( $post->ID, '_bps_service_icon', true ),
				'ctaLabel'  => (string) get_post_meta( $post->ID, '_bps_service_cta_label', true ) ?: 'Learn More',
				'ctaUrl'    => (string) get_post_meta( $post->ID, '_bps_service_cta_url', true ),
				'thumbnail' => get_the_post_thumbnail_url( $post->ID, 'medium' ) ?: null,
			];
		}, $posts );

		return rest_ensure_response( array_values( $data ) );
	}

	/**
	 * GET /wp-json/bpsquare/v1/case-studies
	 *
	 * @return \WP_REST_Response
	 */
	public function get_case_studies( \WP_REST_Request $request ): \WP_REST_Response {
		$posts = get_posts( [
			'post_type'      => CPT_Case_Study::POST_TYPE,
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		] );

		$data = array_map( function ( \WP_Post $post ): array {
			$techs_raw = (string) get_post_meta( $post->ID, '_bps_cs_technologies', true );
			$techs     = array_filter( array_map( 'trim', explode( ',', $techs_raw ) ) );

			return [
				'id'             => $post->ID,
				'title'          => wp_strip_all_tags( $post->post_title ),
				'status'         => (string) get_post_meta( $post->ID, '_bps_cs_status', true ) ?: 'concept',
				'clientType'     => (string) get_post_meta( $post->ID, '_bps_cs_client_type', true ),
				'problem'        => (string) get_post_meta( $post->ID, '_bps_cs_problem', true ),
				'constraints'    => (string) get_post_meta( $post->ID, '_bps_cs_constraints', true ),
				'solution'       => (string) get_post_meta( $post->ID, '_bps_cs_solution', true ),
				'technologies'   => array_values( $techs ),
				'outcome'        => (string) get_post_meta( $post->ID, '_bps_cs_outcome', true ),
				'lessonsLearned' => (string) get_post_meta( $post->ID, '_bps_cs_lessons', true ),
				'isConfidential' => '1' === (string) get_post_meta( $post->ID, '_bps_cs_is_confidential', true ),
				'thumbnail'      => get_the_post_thumbnail_url( $post->ID, 'medium' ) ?: null,
			];
		}, $posts );

		return rest_ensure_response( array_values( $data ) );
	}

	/**
	 * GET /wp-json/bpsquare/v1/testimonials
	 *
	 * @return \WP_REST_Response
	 */
	public function get_testimonials( \WP_REST_Request $request ): \WP_REST_Response {
		$posts = get_posts( [
			'post_type'      => CPT_Testimonial::POST_TYPE,
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		] );

		$data = array_map( function ( \WP_Post $post ): array {
			return [
				'id'           => $post->ID,
				'author'       => (string) get_post_meta( $post->ID, '_bps_testimonial_author', true ),
				'businessName' => (string) get_post_meta( $post->ID, '_bps_testimonial_business', true ),
				'role'         => (string) get_post_meta( $post->ID, '_bps_testimonial_role', true ),
				'content'      => wp_strip_all_tags( $post->post_content ),
			];
		}, $posts );

		return rest_ensure_response( array_values( $data ) );
	}

	/**
	 * GET /wp-json/bpsquare/v1/faqs
	 *
	 * @return \WP_REST_Response
	 */
	public function get_faqs( \WP_REST_Request $request ): \WP_REST_Response {
		$posts = get_posts( [
			'post_type'      => CPT_FAQ::POST_TYPE,
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		] );

		$data = array_map( function ( \WP_Post $post ): array {
			return [
				'id'       => $post->ID,
				'question' => wp_strip_all_tags( $post->post_title ),
				'answer'   => wp_strip_all_tags( $post->post_content ),
			];
		}, $posts );

		return rest_ensure_response( array_values( $data ) );
	}
}
