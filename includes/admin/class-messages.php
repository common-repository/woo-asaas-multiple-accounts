<?php
/**
 * Plugin dependency class
 *
 * @package WooAsaas
 */

namespace Woo_Asaas_Multiple_Accounts\Admin;

/**
 * WooCommerce Asaas
 */
class Messages {
	/**
	 * Instance of this class
	 *
	 * @var self
	 */
	protected static $instance = null;

	/**
	 * Meta data key
	 *
	 * @var string
	 */
	protected $key = 'woo_asaas_multiple_accounts_messages';

	/**
	 * Message data
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * Define the dependencies
	 */
	private function __construct() {
		$this->init_data();
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
	 * Initialize the data
	 *
	 * @return null|array
	 */
	public function init_data() {
		if ( ! is_null( $this->data ) ) {
			return $this->data;
		}

		$this->data = get_user_meta( get_current_user_id(), $this->key, true );

		if ( '' === $this->data ) {
			$this->data = $this->default_data();
		}
	}

	/**
	 * Set the default data
	 *
	 * @return array
	 */
	public function default_data() {
		return array(
			'messages' => array(),
			'errors'   => array(),
		);
	}

	/**
	 * Add a message
	 *
	 * @param  string $message The message content.
	 */
	public function add_message( $message ) {
		$this->data['messages'][] = $message;
		$this->save();
	}

	/**
	 * Add a error
	 *
	 * @param  string $message The message content.
	 */
	public function add_error( $message ) {
		$this->data['errors'][] = $message;
		$this->save();
	}

	/**
	 * Save the messages
	 */
	public function save() {
		update_user_meta( get_current_user_id(), $this->key, $this->data );
	}

	/**
	 * Displat the messages and the errors
	 */
	public function display() {
		foreach ( $this->data['messages'] as $message ) {
			\WC_Admin_Settings::add_message( $message );
		}

		foreach ( $this->data['errors'] as $error ) {
			\WC_Admin_Settings::add_error( $error );
		}

		delete_user_meta( get_current_user_id(), $this->key );
	}
}
