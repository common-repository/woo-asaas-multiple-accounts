<?php
/**
 * Plugin main class
 *
 * @package WooAsaas
 */

namespace Woo_Asaas_Multiple_Accounts;

use Woo_Asaas_Multiple_Accounts\Admin\Settings;
use WC_Asaas\Admin\Plugin_Dependency;
use Woo_Asaas_Multiple_Accounts\Admin\Account_Form;
use Woo_Asaas_Multiple_Accounts\Admin\Account_List;
use Woo_Asaas_Multiple_Accounts\Admin\Product_Settings;
use Woo_Asaas_Multiple_Accounts\Shop\Shop;
use Woo_Asaas_Multiple_Accounts\Checkout\Checkout;
use Woo_Asaas_Multiple_Accounts\Admin\Messages;
use WC_Asaas\WC_Asaas;

/**
 * Asaas Gateway for WooCommerce main class
 */
class Woo_Asaas_Multiple_Accounts {

	/**
	 * WooCommerce version.
	 *
	 * @var string
	 */
	public $version = '1.0.1';

	/**
	 * Instance of this class
	 *
	 * @var self
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin public actions
	 *
	 * Block external object instantiation.
	 *
	 * @see Plugin_Dependency::check_dependencies()
	 * @see I18n::load_plugin_textdomain()
	 * @see Woo_Asaas_Multiple_Accounts::settings()
	 */
	private function __construct() {
		add_action( 'admin_init', array( Plugin_Dependency::get_instance(), 'check_dependencies' ) );
		add_action( 'init', array( I18n::get_instance(), 'load_plugin_textdomain' ) );
		add_action( 'init', array( $this, 'shop' ), 20 );
		add_action( 'init', array( $this, 'settings' ), 20 );
		add_action( 'init', array( $this, 'checkout' ), 20 );
		add_action( 'init', array( $this, 'product_settings' ), 20 );

		add_filter( 'woocommerce_asaas_settings_fields', array( Settings::get_instance(), 'settings_fields' ), 10, 2 );
	}

	/**
	 * Settings system
	 *
	 * - Account form
	 * - Remove gateway settigns fields that will managed by accounts
	 * - List accounts on gateway settings screen
	 *
	 * @see Account_List::list_view()
	 * @see Account_Form::save()
	 * @see Settings::settings_fields()
	 * @see Account_Form::form_fields()
	 */
	public function settings() {
		$settings = Settings::get_instance();
		$gateway  = $settings->get_gateway();

		add_action( 'woocommerce_sections_' . $settings->current_tab, array( Messages::get_instance(), 'display' ) );

		if ( false === $gateway ) {
			return;
		}

		if ( Account_Form::get_instance()->is_delete() ) {
			Account_Form::get_instance()->delete( $gateway );
			return;
		}

		if ( Account_Form::get_instance()->is() ) {
			$this->account_form( $settings, $gateway );
			return;
		}

		$this->account_list();
	}

	/**
	 * Account form
	 *
	 * @param  \Woo_Asaas_Multiple_Accounts\Admin\Settings $settings WC gateway settings.
	 * @param  \WC_Asaas\Gateway\Gateway                   $gateway  WC gateway.
	 */
	public function account_form( $settings, $gateway ) {
		add_action( 'woocommerce_update_options_payment_gateways_' . $gateway->id, array( Account_Form::get_instance(), 'save' ), 1 );
		add_action( 'woocommerce_settings_checkout', array( Account_Form::get_instance(), 'load_account_values' ), 1 );

		add_filter( 'woocommerce_settings_api_form_fields_' . $gateway->id, array( Account_Form::get_instance(), 'form_fields' ) );
		add_filter( "woocommerce_save_settings_{$settings->current_tab}_{$settings->current_section}", array( Account_Form::get_instance(), 'validate' ) );
		add_filter( "woocommerce_save_settings_{$settings->current_tab}", array( Account_Form::get_instance(), 'validate' ) );
	}

	/**
	 * Account list
	 */
	public function account_list() {
		add_filter( 'woocommerce_asaas_settings_fields', array( Settings::get_instance(), 'settings_fields' ), 10, 2 );
		add_action( 'woocommerce_settings_checkout', array( Account_List::get_instance(), 'list_view' ), 20 );
	}

	/**
	 * Product settings system
	 */
	public function product_settings() {
		add_action( 'woocommerce_product_options_advanced', array( Product_Settings::get_instance(), 'add_asaas_account_field' ), 10 );
		add_action( 'woocommerce_process_product_meta', array( Product_Settings::get_instance(), 'save' ), 10 );
	}

	/**
	 * Shop system
	 *
	 * - Remove "Add to cart" button and rename to "Buy"
	 * - Redirects to checkout when click on "Buy" button
	 */
	public function shop() {
		add_filter( 'woocommerce_product_single_add_to_cart_text', array( Shop::get_instance(), 'change_add_to_cart_text' ), 10, 2 );
		add_filter( 'woocommerce_add_to_cart_redirect', array( Shop::get_instance(), 'change_buy_button_link' ), 10, 2 );

		// Remove "Add to cart" button.
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		add_action( 'woocommerce_after_shop_loop_item', array( Shop::get_instance(), 'add_view_product_now_button' ), 10, 2 );
	}

	/**
	 * Checkout system
	 *
	 * - Remove alert message when product has been add to cart
	 * - Clear cart before add a new product
	 */
	public function checkout() {
		// Remove alert message when a product has been add to cart.
		add_filter( 'wc_add_to_cart_message_html', '__return_false' );

		add_filter( 'woocommerce_asaas_request_api_key', array( Checkout::get_instance(), 'change_api_key' ) );
		add_filter( 'woocommerce_add_to_cart_validation', array( Checkout::get_instance(), 'remove_cart_item_before_add_to_cart' ), 20, 3 );
	}

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
	 * Get the plugin absolute path
	 *
	 * @return string The plugin absolute path.
	 */
	public function get_plugin_path() {
		return plugin_dir_path( dirname( __FILE__ ) );
	}

	/**
	 * Get the plugin URL
	 *
	 * @return string The plugin URL.
	 */
	public function get_plugin_url() {
		return plugin_dir_url( dirname( __FILE__ ) );
	}

	/**
	 * Get templates path
	 */
	public function get_templates_path() {
		return $this->get_plugin_path() . 'templates/';
	}

	/**
	 * Get Asaas accounts
	 */
	public function get_accounts() {
		return current( WC_Asaas::get_instance()->get_gateways() )->settings['accounts'];
	}
}
