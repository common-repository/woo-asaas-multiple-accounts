<?php
/**
 * Admin view class
 *
 * @package WooAsaas
 */

namespace Woo_Asaas_Multiple_Accounts\Admin;

use WC_Asaas\Admin\Settings\Settings as Woo_Asaas_Settings;
use WC_Asaas\WC_Asaas;

/**
 * Admin view class
 */
class Settings {

	/**
	 * Instance of this class
	 *
	 * @var self
	 */
	protected static $instance = null;

	/**
	 * Current tab
	 *
	 * @var string
	 */
	public $current_tab;

	/**
	 * Current section
	 *
	 * @var string
	 */
	public $current_section;

	/**
	 * Get params from URL
	 */
	private function __construct() {
		// Extracted from WC_Admin_Menus::settings_page_init(). This values need to be used before the function call.
		$this->current_tab     = empty( $_GET['tab'] ) ? 'general' : sanitize_title( wp_unslash( $_GET['tab'] ) ); // WPCS: input var okay, CSRF ok.
		$this->current_section = empty( $_REQUEST['section'] ) ? '' : sanitize_title( wp_unslash( $_REQUEST['section'] ) ); // WPCS: input var okay, CSRF ok.
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
	 * Unset `api_key` and `notification` fields
	 *
	 * @param  array $fields   Array of fields.
	 */
	public function settings_fields( $fields ) {
		unset( $fields['api_key'] );
		unset( $fields['notification'] );

		return $fields;
	}

	/**
	 * Get all accounts
	 *
	 * @return array
	 */
	public function get_accounts() {
		return get_option( 'woocommerce_asaas-credit-card_settings' )['accounts'];
	}

	/**
	 * Get default account
	 *
	 * @return int
	 */
	public function get_default_account_id() {
		foreach ( $this->get_accounts() as $id => $account ) {
			if ( 'yes' === $account['default'] ) {
				return $id;
			}
		}
	}

	/**
	 * Get the current geteway
	 *
	 * @see \WC_Admin_Menus::settings_page_init()
	 *
	 * @return boolean|\WC_Asaas\Gateway\Gateway
	 */
	public function get_gateway() {
		if ( 'checkout' !== $this->current_tab || 0 !== strpos( $this->current_section, 'asaas-' ) ) {
			return false;
		}

		$gateway_id = sanitize_text_field( $this->current_section );
		$gateway    = WC_Asaas::get_instance()->get_gateway_by_id( $gateway_id );

		if ( is_null( $gateway ) ) {
			// phpcs:disable WordPress.PHP.DevelopmentFunctions
			trigger_error( sprintf( 'The function %s must be executed after the gateways be loaded', __FUNCTION__ ), E_USER_NOTICE );
			// phpcs:enable WordPress.PHP.DevelopmentFunctions			

			return false;
		}

		return $gateway;
	}
}
