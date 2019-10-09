<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

/**
 * Fraktguiden_Service_Table class
 */
class Fraktguiden_Service_Table {

	/**
	 * Shipping method
	 *
	 * @var string
	 */
	protected $shipping_method;

	/**
	 * Construct
	 *
	 * @param string $shipping_method Shipping method.
	 */
	public function __construct( $shipping_method ) {
		$this->shipping_method = $shipping_method;
	}

	/**
	 * Validate the service table field
	 *
	 * @param  string $key   Key.
	 * @param  mixed  $value Value.
	 *
	 * @return array
	 */
	public function validate_services_table_field( $key, $value = null ) {
		if ( isset( $value ) ) {
			return $value;
		}

		$sanitized_services = [];
		$field_key          = $this->shipping_method->get_field_key( 'services' );

		$services = filter_input( INPUT_POST, $field_key, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		if ( empty( $services ) ) {
			return $sanitized_services;
		}

		foreach ( $services as $service ) {
			if ( preg_match( '/^[A-Za-z_\-]+$/', $service ) ) {
				$sanitized_services[] = $service;
			}
		}

		return $sanitized_services;
	}

	/**
	 * Process services field
	 *
	 * @param string|null $instance_key Instance key.
	 */
	public function process_services_field( $instance_key ) {

		$service_key = $this->shipping_method->get_field_key( 'services' );

		// Process services table.
		$services  = Fraktguiden_Service::all( $service_key );

		$options   = [];

		$service_options = [];
		// Only process options for enabled services.
		foreach ( $services as $bring_product => $service ) {
			$service_options[ $bring_product ] = $service->process_post_data();
		}
		$service_options = array_filter( $service_options );

		update_option( $service_key . '_options', $service_options );
	}

	/**
	 * Generate services field
	 *
	 * @return string html
	 */
	public function generate_services_table_html() {
		$field_key       = $this->shipping_method->get_field_key( 'services' );
		$services        = Fraktguiden_Helper::get_services_data();
		$service_options = [
			'field_key'                => $field_key,
			'selected'                 => $this->shipping_method->services,
			'custom_names'             => get_option( $field_key . '_custom_names' ),
			'customer_numbers'         => get_option( $field_key . '_customer_numbers' ),
			'custom_prices'            => get_option( $field_key . '_custom_prices' ),
			'free_shipping_checks'     => get_option( $field_key . '_free_shipping_checks' ),
			'free_shipping_thresholds' => get_option( $field_key . '_free_shipping_thresholds' ),
		];

		ob_start();
		require dirname( dirname( __DIR__ ) ) . '/templates/service-field.php';
		return ob_get_clean();
	}
}
