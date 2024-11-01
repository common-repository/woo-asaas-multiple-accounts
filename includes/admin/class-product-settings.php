<?php
/**
 * Plugin dependency class
 *
 * @package WooAsaas
 */

namespace Woo_Asaas_Multiple_Accounts\Admin;

use Woo_Asaas_Multiple_Accounts\Woo_Asaas_Multiple_Accounts;

/**
 * WooCommerce Asaas
 */
class Product_Settings {
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
	private function __construct() {}

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
	 * Get Asaas account field name
	 *
	 * @return string
	 */
	public function get_asaas_account_field_name() {
		return 'woo_asaas_multiple_accounts_account_name';
	}

	/**
	 * Add Asaas account field on advanced option tab
	 */
	public function add_asaas_account_field() {
		$accounts = Woo_Asaas_Multiple_Accounts::get_instance()->get_accounts();

		$options            = array();
		$options['default'] = __( 'Select a Asaas account', 'woo-asaas-multiple-accounts' );

		foreach ( $accounts as $id => $account ) {
			$options[ $id ] = $account['name'];
		}

		$args = array(
			'label'       => __( 'Asaas gateway account', 'woo-asaas-multiple-accounts' ),
			'description' => __( 'Select the account that will process the payment', 'woo-asaas-multiple-accounts' ),
			'desv_tip'    => true,
			'id'          => $this->get_asaas_account_field_name(),
			'options'     => $options,
		);

		woocommerce_wp_select( $args );
	}

	/**
	 * Save Assas account custom field
	 *
	 * @param  int $post_id The post id.
	 */
	public function save( $post_id ) {
		$field_value = '';
		$field_name  = $this->get_asaas_account_field_name();

		check_admin_referer( 'update-post_' . $post_id );

		if ( isset( $_POST[ $field_name ] ) ) {
			$field_value = sanitize_text_field( wp_unslash( $_POST[ $field_name ] ) );
		}

		$product = wc_get_product( $post_id );
		$product->update_meta_data( $field_name, $field_value );
		$product->save();
	}
}
