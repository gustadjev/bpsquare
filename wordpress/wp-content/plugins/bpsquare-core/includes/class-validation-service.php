<?php
/**
 * BPSquare Core — Validation Service
 *
 * Centralised input validation logic for REST API requests.
 *
 * @package BPSquare_Core
 */

namespace BPSquare_Core;

defined( 'ABSPATH' ) || exit;

/**
 * Class Validation_Service
 */
class Validation_Service {

	/** @var array<string, string[]> Validation errors collected during validate(). */
	private array $errors = [];

	/**
	 * Validate an inquiry payload.
	 *
	 * @param array $data Raw request data.
	 * @return bool True if valid.
	 */
	public function validate_inquiry( array $data ): bool {
		$this->errors = [];

		$this->required_string( $data, 'firstName', 'First name', 2, 80 );
		$this->required_string( $data, 'lastName', 'Last name', 2, 80 );
		$this->required_email( $data, 'email' );
		$this->optional_string( $data, 'businessName', 'Business name', 0, 120 );
		$this->optional_phone( $data, 'phone' );
		$this->required_string( $data, 'serviceInterest', 'Service interest', 2, 120 );
		$this->optional_string( $data, 'budgetRange', 'Budget range', 0, 80 );
		$this->optional_string( $data, 'preferredTimeline', 'Preferred timeline', 0, 80 );
		$this->required_string( $data, 'projectDescription', 'Project description', 10, 5000 );
		$this->required_consent( $data );

		// Honeypot check: bots often fill hidden fields.
		if ( ! empty( $data['website'] ) ) {
			// Silently fail to avoid revealing honeypot to bots.
			$this->errors['honeypot'] = 'Spam detected.';
		}

		return empty( $this->errors );
	}

	/**
	 * Get collected validation errors.
	 *
	 * @return array<string, string[]>
	 */
	public function get_errors(): array {
		return $this->errors;
	}

	// -------------------------------------------------------------------------
	// Private helpers
	// -------------------------------------------------------------------------

	private function required_string( array $data, string $field, string $label, int $min, int $max ): void {
		$value = isset( $data[ $field ] ) ? trim( (string) $data[ $field ] ) : '';
		if ( '' === $value ) {
			$this->errors[ $field ][] = "{$label} is required.";
			return;
		}
		$len = mb_strlen( $value );
		if ( $len < $min ) {
			$this->errors[ $field ][] = "{$label} must be at least {$min} characters.";
		}
		if ( $len > $max ) {
			$this->errors[ $field ][] = "{$label} must be no more than {$max} characters.";
		}
	}

	private function optional_string( array $data, string $field, string $label, int $min, int $max ): void {
		if ( empty( $data[ $field ] ) ) {
			return;
		}
		$value = trim( (string) $data[ $field ] );
		$len   = mb_strlen( $value );
		if ( $len < $min ) {
			$this->errors[ $field ][] = "{$label} must be at least {$min} characters.";
		}
		if ( $len > $max ) {
			$this->errors[ $field ][] = "{$label} must be no more than {$max} characters.";
		}
	}

	private function required_email( array $data, string $field ): void {
		$value = isset( $data[ $field ] ) ? trim( (string) $data[ $field ] ) : '';
		if ( '' === $value ) {
			$this->errors[ $field ][] = 'Email address is required.';
			return;
		}
		if ( ! is_email( $value ) ) {
			$this->errors[ $field ][] = 'A valid email address is required.';
		}
	}

	private function optional_phone( array $data, string $field ): void {
		if ( empty( $data[ $field ] ) ) {
			return;
		}
		$value = trim( (string) $data[ $field ] );
		// Allow digits, spaces, dashes, dots, parens, and plus sign (E.164 compatible).
		if ( ! preg_match( '/^[0-9\s\+\-\.\(\)]{7,20}$/', $value ) ) {
			$this->errors[ $field ][] = 'Please enter a valid phone number.';
		}
	}

	private function required_consent( array $data ): void {
		if ( empty( $data['consentAccepted'] ) || ! filter_var( $data['consentAccepted'], FILTER_VALIDATE_BOOLEAN ) ) {
			$this->errors['consentAccepted'][] = 'You must accept the consent statement to proceed.';
		}
	}
}
