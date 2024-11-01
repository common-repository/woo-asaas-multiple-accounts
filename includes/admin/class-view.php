<?php
/**
 * Admin view class
 *
 * @package WooAsaas
 */

namespace Woo_Asaas_Multiple_Accounts\Admin;

/**
 * Admin view class
 */
class View {

	/**
	 * Instance of this class
	 *
	 * @var self
	 */
	protected static $instance = null;

	/**
	 * Block external object instantiation
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
	 * Get the admin views directory path
	 */
	public function get_template_path() {
		return __DIR__ . '/views/';
	}

	/**
	 * Load a template file
	 *
	 * @param string $file The template file name.
	 * @param array  $args The template arguments.
	 */
	public function load_template_file( $file, $args = array() ) {
		if ( ! empty( $args ) && is_array( $args ) ) {
			extract( $args ); // @codingStandardsIgnoreLine
		}

		require $this->get_template_path() . $file;
	}
}
