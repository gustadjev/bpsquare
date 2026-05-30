<?php
/**
 * BPSquare Core — Email Service
 *
 * Sends notification emails for new inquiry submissions.
 *
 * @package BPSquare_Core
 */

namespace BPSquare_Core;

defined( 'ABSPATH' ) || exit;

/**
 * Class Email_Service
 */
class Email_Service {

	/**
	 * Send an admin notification for a new inquiry.
	 *
	 * @param array $data      Validated inquiry data.
	 * @param int   $post_id   The inquiry CPT post ID.
	 * @return bool True if the email was sent.
	 */
	public function send_admin_notification( array $data, int $post_id ): bool {
		$admin_email = defined( 'BPSQUARE_ADMIN_EMAIL' )
			? BPSQUARE_ADMIN_EMAIL
			: get_option( 'bpsquare_admin_email', get_option( 'admin_email' ) );

		$from_email = defined( 'BPSQUARE_FROM_EMAIL' )
			? BPSQUARE_FROM_EMAIL
			: get_option( 'bpsquare_from_email', 'noreply@' . wp_parse_url( home_url(), PHP_URL_HOST ) );

		$from_name = defined( 'BPSQUARE_FROM_NAME' )
			? BPSQUARE_FROM_NAME
			: get_option( 'bpsquare_from_name', 'BPSquare LLC' );

		$subject = sprintf(
			__( 'New Project Inquiry: %s %s', 'bpsquare-core' ),
			sanitize_text_field( $data['firstName'] ?? '' ),
			sanitize_text_field( $data['lastName'] ?? '' )
		);

		$body    = $this->build_admin_body( $data, $post_id );
		$headers = [
			'Content-Type: text/html; charset=UTF-8',
			"From: {$from_name} <{$from_email}>",
			"Reply-To: {$data['email']}",
		];

		return wp_mail( $admin_email, $subject, $body, $headers );
	}

	/**
	 * Send an auto-reply acknowledgement to the submitter.
	 *
	 * @param array $data Validated inquiry data.
	 * @return bool True if the email was sent.
	 */
	public function send_submitter_acknowledgement( array $data ): bool {
		$to_email = sanitize_email( $data['email'] ?? '' );
		if ( ! $to_email ) {
			return false;
		}

		$from_email = defined( 'BPSQUARE_FROM_EMAIL' )
			? BPSQUARE_FROM_EMAIL
			: get_option( 'bpsquare_from_email', 'noreply@' . wp_parse_url( home_url(), PHP_URL_HOST ) );

		$from_name = defined( 'BPSQUARE_FROM_NAME' )
			? BPSQUARE_FROM_NAME
			: get_option( 'bpsquare_from_name', 'BPSquare LLC' );

		$first_name = sanitize_text_field( $data['firstName'] ?? '' );

		$subject = __( "Thank you for reaching out to BPSquare LLC — We'll be in touch soon!", 'bpsquare-core' );
		$body    = $this->build_acknowledgement_body( $first_name );
		$headers = [
			'Content-Type: text/html; charset=UTF-8',
			"From: {$from_name} <{$from_email}>",
		];

		return wp_mail( $to_email, $subject, $body, $headers );
	}

	// -------------------------------------------------------------------------
	// Private: email body builders
	// -------------------------------------------------------------------------

	private function build_admin_body( array $data, int $post_id ): string {
		$admin_link = admin_url( 'post.php?post=' . $post_id . '&action=edit' );

		$rows = [
			'First Name'         => $data['firstName'] ?? '',
			'Last Name'          => $data['lastName'] ?? '',
			'Business Name'      => $data['businessName'] ?? '—',
			'Business Type'      => $data['businessType'] ?? '—',
			'Current Website/App'=> $data['currentWebsite'] ?? '—',
			'Email'              => $data['email'] ?? '',
			'Phone'              => $data['phone'] ?? '—',
			'Service Interest'   => $data['serviceInterest'] ?? '',
			'Budget Range'       => $data['budgetRange'] ?? '—',
			'Preferred Timeline' => $data['preferredTimeline'] ?? '—',
			'Phased Approach'    => $data['phasedApproach'] ?? '—',
			'Operational Problem'=> $data['operationalProblem'] ?? '—',
			'Project Description'=> $data['projectDescription'] ?? '',
		];

		$table_rows = '';
		foreach ( $rows as $label => $value ) {
			$table_rows .= sprintf(
				'<tr><td style="font-weight:600;padding:6px 12px;background:#f8f9fa;border:1px solid #dee2e6;white-space:nowrap">%s</td><td style="padding:6px 12px;border:1px solid #dee2e6">%s</td></tr>',
				esc_html( $label ),
				nl2br( esc_html( $value ) )
			);
		}

		return sprintf(
			'<!DOCTYPE html><html><body style="font-family:sans-serif;color:#333;max-width:640px;margin:0 auto">
			<h2 style="color:#4f9cf9">New Project Inquiry — BPSquare LLC</h2>
			<p>A new inquiry has been submitted via the website contact form.</p>
			<p><strong>Suggested next action:</strong> Review the operational problem, set an inquiry status, and reply with either a discovery call option or a clarifying question.</p>
			<table style="width:100%%;border-collapse:collapse;margin:20px 0">%s</table>
			<p><a href="%s" style="display:inline-block;padding:10px 20px;background:#4f9cf9;color:#fff;text-decoration:none;border-radius:4px">View in Admin</a></p>
			<hr style="border:none;border-top:1px solid #eee;margin:24px 0">
			<p style="font-size:12px;color:#999">This email was generated automatically by the BPSquare LLC website.</p>
			</body></html>',
			$table_rows,
			esc_url( $admin_link )
		);
	}

	private function build_acknowledgement_body( string $first_name ): string {
		return sprintf(
			'<!DOCTYPE html><html><body style="font-family:sans-serif;color:#333;max-width:600px;margin:0 auto">
			<h2 style="color:#4f9cf9">Thank You, %s!</h2>
			<p>We\'ve received your project inquiry and will review the business problem, timeline, and best next step.</p>
			<p>You can expect a practical response within 1–2 business days. If your request is urgent, feel free to reach out directly at <a href="mailto:bpsquare479@gmail.com">bpsquare479@gmail.com</a>.</p>
			<p>We look forward to learning more about the system you want to build.</p>
			<p style="margin-top:32px">— The BPSquare LLC Team<br>
			<a href="https://bpsquarellc.com">bpsquarellc.com</a></p>
			<hr style="border:none;border-top:1px solid #eee;margin:24px 0">
			<p style="font-size:12px;color:#999">You are receiving this email because you submitted an inquiry on bpsquarellc.com.</p>
			</body></html>',
			esc_html( $first_name )
		);
	}
}
