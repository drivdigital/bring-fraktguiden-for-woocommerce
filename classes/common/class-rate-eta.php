<?php

namespace Bring_Fraktguiden\Common;

use DateTime;

/**
 * Checkout Modifications
 */
class Rate_Eta {

	/**
	 * Setup.
	 */
	static function setup() {
	}

	/**
	 * Add opening hours to a full label
	 *
	 * @param \WC_Shipping_Rate $rate Shipping rate.
	 */
	public static function add_estimated_delivery_date( $rate ) {
		$meta_data = $rate->get_meta_data();
		if ( empty( $meta_data['expected_delivery_date'] ) ) {
			return;
		}
		$expected_delivery_date = new DateTime( $meta_data['expected_delivery_date'] );
		$today                  = new DateTime( 'now', $expected_delivery_date->getTimezone() );
		$diff                   = $today->diff( $expected_delivery_date );
		$diffDays               = $diff->format( "%r%a%H%I" );

		if ( 10000 > $diffDays && 0 < $diffDays ) {
			$eta = __( 'Tomorrow', 'bring-fraktguiden-for-woocommerce' );
		} else if ( 60000 > $diffDays ) {
			$eta = wp_date(
				'l',
				$expected_delivery_date->getTimestamp(),
				$expected_delivery_date->getTimezone()
			);
		} else {
			$eta = wp_date(
				'j. M',
				$expected_delivery_date->getTimestamp(),
				$expected_delivery_date->getTimezone()
			);
		}

		printf(
			'<div class="bring-fraktguiden-eta">%s</div>',
			esc_html(
				apply_filters(
					'bring_fraktguiden_shipping_rate_eta',
					ucfirst( $eta ),
					$expected_delivery_date,
					$rate
				)
			)
		);
	}
}
