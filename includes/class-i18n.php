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
class I18n {

	/**
	 * Instance of this class
	 *
	 * @var self
	 */
	protected static $instance = null;

	/**
	 * The list of plugin dependencies
	 *
	 * @var array
	 */
	protected $dependencies;

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
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Load plugin textdomain file
	 */
	public function load_plugin_textdomain() {
		$domain                 = 'woo-asaas-multiple-accounts';
		$languages_rel_dir_path = '/../languages/';
		if ( 'development' === getenv( 'ENV' ) ) {
			load_textdomain( $domain, __DIR__ . $languages_rel_dir_path . '/' . $domain . '-' . get_locale() . '.mo' );
			return;
		}

		load_plugin_textdomain( $domain, false, basename( dirname( dirname( __FILE__ ) ) ) . '/languages' );
	}
}
