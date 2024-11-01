<?php
/**
 * Plugin dependency class
 *
 * @package WooAsaas
 */

namespace Woo_Asaas_Multiple_Accounts\Admin;

use WC_Asaas\Admin\Settings\Credit_Card;
use WC_Asaas\WC_Asaas;

/**
 * WooCommerce Asaas
 */
class Account_Form {

	/**
	 * Instance of this class
	 *
	 * @var self
	 */
	protected static $instance = null;

	/**
	 * Use this replacement because the form fields is loaded before the save action
	 *
	 * @var string
	 */
	protected $config_url_replacement = '%config_url%';

	/**
	 * Define the dependencies
	 *
	 * Block external object instantiation.
	 */
	private function __construct() {
		$this->load_script();
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
	 * Verify if is account form page
	 *
	 * @return boolean
	 */
	public function is() {
		return Settings::get_instance()->get_gateway() && isset( $_GET['account'] );
	}

	/**
	 * Check if is delete action
	 *
	 * @return boolean
	 */
	public function is_delete() {
		return $this->is() && isset( $_GET['delete'] );
	}

	/**
	 * Get the account id
	 *
	 * @return boolean|number The account id. False, if isn't account form page
	 */
	public function get_account_value() {
		$account = wp_unslash( $_GET['account'] );

		if ( ! $this->is() || '' === $account ) {
			return false;
		}

		return $account;
	}

	/**
	 * Get account by the index
	 *
	 * @return array
	 */
	public function get_account() {
		$index = $this->get_account_value();

		if ( false === $index ) {
			return false;
		}

		$accounts = Settings::get_instance()->get_gateway()->settings['accounts'];

		if ( ! isset( $accounts[ $index ] ) ) {
			Messages::get_instance()->add_error( __( 'Inexistent account id.', 'woo-asaas-multiple-accounts' ) );
			wp_redirect( remove_query_arg( 'account' ) );
			exit;
		}

		return $accounts[ $index ];
	}

	/**
	 * Get fields for account form
	 *
	 * @link https://docs.woocommerce.com/document/settings-api
	 * @see Credit_Card::get_fields()
	 *
	 * @return array The account fields.
	 */
	public function form_fields() {
		return $this->get_fields();
	}

	/**
	 * Get account form fields
	 *
	 * @return array
	 */
	public function get_fields() {
		$account = $this->get_account();

		return array(
			'form_title'   => array(
				'title' => __( 'New Account', 'woo-asaas-multiple-accounts' ),
				'type'  => 'title',
			),
			'default'      => array(
				'title'       => __( 'Default', 'woo-asaas-multiple-accounts' ),
				'label'       => __( 'Set as default', 'woo-asaas-multiple-accounts' ),
				'type'        => 'checkbox',
				'description' => __( 'Set this account as default when no account is set on product.', 'woo-asaas-multiple-accounts' ),
				'disabled'    => isset( $account['default'] ) && 'yes' === $account['default'],
				'default'     => 'no',
			),
			'name'         => array(
				'title'       => __( 'Name', 'woo-asaas-multiple-accounts' ),
				'type'        => 'text',
				'description' => __( 'The account name.', 'woo-asaas-multiple-accounts' ),
				'required'    => true,
			),
			'api_key'      => array(
				'title'       => __( 'API Key', 'woo-asaas-multiple-accounts' ),
				'type'        => 'text',
				/* translators: %s: href */
				'description' => sprintf( __( 'The API Key used to connect with Asaas. <a href="%s">Click here</a> to get it.', 'woo-asaas-multiple-accounts' ), $this->config_url_replacement ),
				'required'    => true,
			),
			'notification' => array(
				'title'       => __( 'Notification between Asaas and customer', 'woo-asaas-multiple-accounts' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable Notification', 'woo-asaas-multiple-accounts' ),
				'description' => __( 'Allow Asaas to send email and SMS about the purchase and notify him periodically while the purchase is not paid.', 'woo-asaas-multiple-accounts' ),
				'default'     => 'no',
			),
		);
	}

	/**
	 * Load the account data into gateway settings
	 *
	 * This function is necessary to load the right values into form fields. It must be called just in the form edition
	 * requests.
	 */
	public function load_account_values() {
		$gateway = Settings::get_instance()->get_gateway();
		$account = $this->get_account();

		if ( $gateway->get_post_data() ) {
			return;
		}

		if ( false === $account ) {
			return;
		}

		foreach ( $gateway->get_form_fields() as $key => $field ) {
			if ( ! isset( $account[ $key ] ) ) {
				continue;
			}

			$gateway->settings[ $key ] = $account[ $key ];
		}
	}

	/**
	 * Validate form submition
	 *
	 * @param  boolean $has_post Check if has post.
	 */
	public function validate( $has_post ) {
		if ( ! $this->is() ) {
			return $has_post;
		}

		$gateway   = Settings::get_instance()->get_gateway();
		$post_data = $gateway->get_post_data();

		if ( empty( $post_data ) ) {
			return $has_post;
		}

		$has_error = false;

		foreach ( $gateway->get_form_fields() as $key => $field ) {
			$value = $gateway->get_field_value( $key, $field, $post_data );

			if ( isset( $field['required'] ) && true === $field['required'] && '' === $value ) {
				/* translators: %s: field name */
				Messages::get_instance()->add_error( sprintf( __( 'The field %s is required.', 'woo-asaas-multiple-accounts' ), $field['title'] ) );
				$has_error = true;
			}
		}

		return ! $has_error;
	}

	/**
	 * Save account form
	 *
	 * @see WC_Settings_API::process_admin_options()
	 * @return null
	 */
	public function save() {
		if ( ! $this->is() ) {
			return;
		}

		$current_gateway = Settings::get_instance()->get_gateway();
		remove_action( 'woocommerce_update_options_payment_gateways_' . $current_gateway->id, array( $current_gateway, 'process_admin_options' ) );

		$current_gateway->init_settings();
		$id        = $this->get_account_id( $current_gateway );
		$accounts  = $current_gateway->settings['accounts'];
		$post_data = $current_gateway->get_post_data();

		foreach ( $current_gateway->get_form_fields() as $key => $field ) {
			if ( 'title' === $current_gateway->get_field_type( $field ) ) {
				continue;
			}

			$value = $current_gateway->get_field_value( $key, $field, $post_data );

			// When checkbox is disabled the value is not include on HTTP body and so the `get_field_value` returns 'no'.
			if ( $this->get_account_value() && 'default' === $key && 'no' === $value ) {
				$value = 'yes';
			}

			// Set 'default' as true on first account.
			if ( ! $this->get_account_value() && 'default' === $key && 0 === count( $accounts ) ) {
				$value = 'yes';
			}

			if ( 'default' === $key && 'yes' === $value ) {
				$accounts = $this->set_default_account( $accounts, $id );
			}

			$accounts[ $id ][ $key ] = $value;
		}

		$this->sync_accounts( $accounts );

		Messages::get_instance()->add_message( sprintf( __( 'Account saved successfully.', 'woo-asaas-multiple-accounts' ) ) );

		wp_redirect( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=' . $current_gateway->id . '&account=' . $id ) );
		exit;
	}

	/**
	 * Set account id as default
	 *
	 * @param  array  $accounts Accounts array.
	 * @param  string $id       Account hash id.
	 * @return array
	 */
	public function set_default_account( $accounts, $id ) {
		foreach ( $accounts as $key => $account ) {
			$accounts[ $key ]['default'] = $id === $key ? 'yes' : 'no';
		}

		return $accounts;
	}

	/**
	 * Sync accounts with all the gateways
	 *
	 * @param  array $accounts All the accounts.
	 */
	private function sync_accounts( $accounts ) {
		foreach ( WC_Asaas::get_instance()->get_gateways() as $gateway ) {
			$gateway->init_settings();
			$gateway->settings['accounts'] = $accounts;

			update_option( $gateway->get_option_key(), apply_filters( 'woocommerce_settings_api_sanitized_fields_' . $gateway->id, $gateway->settings ), 'yes' );
		}
	}

	/**
	 * Delete a account
	 *
	 * @param  \WC_Asaas\Gateway $current_gateway The default geteway.
	 */
	public function delete( $current_gateway ) {
		if ( ! $this->is() ) {
			return;
		}

		$index    = $this->get_account_id( $current_gateway );
		$accounts = $current_gateway->settings['accounts'];
		$account  = $accounts[ $index ];

		unset( $accounts[ $index ] );
		
		if ( 'yes' == $account['default'] && count( $accounts ) >= 1 ) {
			reset( $accounts );
			$accounts = $this->set_default_account( $accounts, key( $accounts ) );
		}

		$this->sync_accounts( $accounts );

		\WC_Admin_Settings::add_message( __( 'Account successfully deleted.', 'woo-asaas-multiple-accounts' ) );

		wp_redirect( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=' . $current_gateway->id . '#accounts' ) );
		exit;
	}

	/**
	 * Initialize settings account array
	 *
	 * @param  WC_Asaas\Gateway\Gateway $gateway Default gateway.
	 */
	public function init_accounts( $gateway ) {
		if ( ! isset( $gateway->settings['accounts'] ) ) {
			$gateway->settings['accounts'] = array();
		}
	}

	/**
	 * Get account hash id or generate new
	 *
	 * @param  WC_Asaas\Gateway\Gateway $gateway Default gateway.
	 * @return string
	 */
	public function get_account_id( $gateway ) {
		$this->init_accounts( $gateway );

		$index = $this->get_account_value();
		if ( false !== $index ) {
			return $index;
		}

		return wp_generate_password( 8, false, false );
	}

	/**
	 * Load script for accout form
	 */
	public function load_script() {
		wp_enqueue_script(
			'woo-asaas-multiple-accounts-scripts',
			plugin_dir_url( realpath( __DIR__ . '/../' ) ) . 'assets/js/account-form.js',
			array( 'jquery' )
		);
	}
}
