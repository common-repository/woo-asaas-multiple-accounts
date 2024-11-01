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
class Account_List {

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
	 * Show account list view
	 */
	public function list_view() {
		View::get_instance()->load_template_file( 'account-list.php' );
	}
}
