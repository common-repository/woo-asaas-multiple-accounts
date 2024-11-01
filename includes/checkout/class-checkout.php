<?php
/**
 * Plugin dependency class
 *
 * @package WooAsaas
 */

namespace Woo_Asaas_Multiple_Accounts\Checkout;

use Woo_Asaas_Multiple_Accounts\Admin\Settings;

/**
 * WooCommerce Asaas
 */
class Checkout {
	/**
	 * Instance of this class
	 *
	 * @var self
	 */
	protected static $instance = null;

	/**
	 * Define the dependencies
	 *
	 * Block external object instantiation.
	 */
	private function __construct() { }

	/**
	 * Return an instance of this class
	 *
	 * @return self A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Clear cart items before add new product
	 *
	 * @param  boolean $passed The true.
	 * @return boolean
	 */
	public function remove_cart_item_before_add_to_cart( $passed ) {
		if ( ! WC()->cart->is_empty() ) {
			WC()->cart->empty_cart();
		}

		return $passed;
	}

	/**
	 * Change API key according to product setting.
	 */
	public function change_api_key() {
		$items = WC()->cart->get_cart();
		$item  = wc_get_product( reset( $items )['product_id'] );

		$accounts   = Settings::get_instance()->get_accounts();
		$account_id = $item->get_meta( 'woo_asaas_multiple_accounts_account_name' );

		if ( ! $account_id ) {
			$account_id = Settings::get_instance()->get_default_account_id();
		}

		return $accounts[ $account_id ]['api_key'];
	}
}
