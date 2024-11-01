<?php
/**
 * Plugin dependency class
 *
 * @package WooAsaas
 */

namespace Woo_Asaas_Multiple_Accounts;

/**
 * WooCommerce Asaas
 */
class Template {
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
	 * Get the template views directory path
	 */
	public function get_template_path() {
		return __DIR__ . '/../templates/';
	}

	/**
	 * Load a template file
	 *
	 * @param  string $template_name Template name.
	 * @param  array  $args          Arguments.
	 */
	public function load_template_file( $template_name, $args = array() ) {
		wc_get_template(
			$template_name,
			$args,
			$this->get_template_path(),
			$this->get_template_path()
		);
	}
}
