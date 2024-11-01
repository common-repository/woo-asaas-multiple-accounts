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
class Plugin_Dependency {

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
	private function __construct() {
		$this->dependencies = apply_filters(
			'woo_asaas_multiple_accounts_plugin_dependencies', array(
				'woo-asaas' => array(
					'name'        => 'Woo Asaas',
					'plugin_file' => 'woo-asaas/woo-asaas.php',
				),
			)
		);
	}

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
	 * Get the dependencies
	 *
	 * @return array The dependencies
	 */
	public function get_dependencies() {
		return $this->dependencies;
	}

	/**
	 * Verify if the WordPress installation satisfies the plugin requirements
	 *
	 * If not, call the function to show the missing plugins in the admin.
	 */
	public function check_dependencies() {
		foreach ( $this->dependencies as $dependency ) {
			if ( ! is_plugin_active( $dependency['plugin_file'] ) ) {
				add_action( 'admin_notices', array( $this, 'dependencies_notice' ) );
			}
		}
	}

	/**
	 * Diplay missing dependencies template
	 */
	public function dependencies_notice() {
		View::get_instance()->load_template_file( 'missing-dependency-plugin.php' );
	}
}
