<?php
/**
 * Plugin dependency class
 *
 * @package WooAsaas
 */

namespace Woo_Asaas_Multiple_Accounts\Shop;

use Woo_Asaas_Multiple_Accounts\Template;

/**
 * WooCommerce Asaas
 */
class Shop {
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
	 * Rename "Add to cart" button to "Buy"
	 */
	public function change_add_to_cart_text() {
		return __( 'Buy', 'woo-asaas-multiple-accounts' );
	}

	/**
	 * Change "Buy/Add to cart" button link to go to checkout
	 */
	public function change_buy_button_link() {
		return get_permalink( get_option( 'woocommerce_checkout_page_id' ) );
	}

	/**
	 * Add "View product" button instead of "Add to cart" button
	 */
	public function add_view_product_now_button() {
		Template::get_instance()->load_template_file( 'view-product-button.php' );
	}
}
