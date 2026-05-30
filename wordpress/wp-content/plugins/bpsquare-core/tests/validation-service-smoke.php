<?php
/**
 * Lightweight smoke tests for Validation_Service.
 *
 * Run with:
 * php wordpress/wp-content/plugins/bpsquare-core/tests/validation-service-smoke.php
 */

namespace {
	define( 'ABSPATH', __DIR__ );
}

namespace BPSquare_Core {
	function is_email( string $email ): bool {
		return false !== filter_var( $email, FILTER_VALIDATE_EMAIL );
	}
}

namespace {
	require_once __DIR__ . '/../includes/class-validation-service.php';

	use BPSquare_Core\Validation_Service;

	function assert_true( bool $condition, string $message ): void {
		if ( ! $condition ) {
			fwrite( STDERR, "FAIL: {$message}\n" );
			exit( 1 );
		}
	}

	$valid_payload = [
		'firstName'          => 'Jane',
		'lastName'           => 'Smith',
		'businessName'       => 'Smith Services',
		'businessType'       => 'Service-Based Small Business',
		'currentWebsite'     => 'https://example.com',
		'email'              => 'jane@example.com',
		'phone'              => '+1 555 123 4567',
		'serviceInterest'    => 'Internal Tool or Workflow Automation',
		'budgetRange'        => '$3,000 - $7,500',
		'preferredTimeline'  => '1-3 Months',
		'phasedApproach'     => 'Maybe, I would like guidance',
		'operationalProblem' => 'Requests are tracked across spreadsheets and email.',
		'projectDescription' => 'We need a better internal intake and follow-up workflow.',
		'consentAccepted'    => true,
		'website'            => '',
	];

	$validator = new Validation_Service();
	assert_true( $validator->validate_inquiry( $valid_payload ), 'Valid inquiry should pass.' );

	$invalid_email = $valid_payload;
	$invalid_email['email'] = 'not-an-email';
	assert_true( ! $validator->validate_inquiry( $invalid_email ), 'Invalid email should fail.' );
	assert_true( isset( $validator->get_errors()['email'] ), 'Invalid email should report email error.' );

	$invalid_url = $valid_payload;
	$invalid_url['currentWebsite'] = 'example.com';
	assert_true( ! $validator->validate_inquiry( $invalid_url ), 'Invalid URL should fail.' );
	assert_true( isset( $validator->get_errors()['currentWebsite'] ), 'Invalid URL should report currentWebsite error.' );

	$honeypot = $valid_payload;
	$honeypot['website'] = 'spam';
	assert_true( ! $validator->validate_inquiry( $honeypot ), 'Filled honeypot should fail.' );
	assert_true( isset( $validator->get_errors()['honeypot'] ), 'Filled honeypot should report honeypot error.' );

	$too_long = $valid_payload;
	$too_long['operationalProblem'] = str_repeat( 'x', 2001 );
	assert_true( ! $validator->validate_inquiry( $too_long ), 'Overlong operational problem should fail.' );

	echo "Validation_Service smoke tests passed.\n";
}
