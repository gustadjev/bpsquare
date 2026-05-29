<?php
/**
 * BPSquare Core — FAQ Custom Post Type
 *
 * @package BPSquare_Core
 */

namespace BPSquare_Core;

defined( 'ABSPATH' ) || exit;

/**
 * Class CPT_FAQ
 */
class CPT_FAQ {

	public const POST_TYPE = 'bps_faq';

	public function register(): void {
		add_action( 'init', [ $this, 'register_post_type' ] );
		add_action( 'rest_api_init', [ $this, 'register_rest_fields' ] );
	}

	public function register_post_type(): void {
		register_post_type( self::POST_TYPE, [
			'labels'          => [
				'name'          => __( 'FAQs', 'bpsquare-core' ),
				'singular_name' => __( 'FAQ', 'bpsquare-core' ),
				'add_new_item'  => __( 'Add New FAQ', 'bpsquare-core' ),
				'edit_item'     => __( 'Edit FAQ', 'bpsquare-core' ),
				'all_items'     => __( 'All FAQs', 'bpsquare-core' ),
			],
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'show_in_rest'    => true,
			'rest_base'       => 'faqs',
			'menu_icon'       => 'dashicons-editor-help',
			'supports'        => [ 'title', 'editor', 'revisions' ],
			'has_archive'     => false,
			'rewrite'         => false,
			'capability_type' => 'post',
		] );
	}

	public function register_rest_fields(): void {
		// Expose clean answer text from post content.
		register_rest_field( self::POST_TYPE, 'answer', [
			'get_callback' => function ( array $post ): string {
				$p = get_post( $post['id'] );
				return $p ? wp_strip_all_tags( $p->post_content ) : '';
			},
			'schema' => [ 'type' => 'string' ],
		] );
	}
}
