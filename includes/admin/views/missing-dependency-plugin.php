<?php
/**
 * Missing plugin dependencies notice.
 *
 * @package WooAsaas
 */

use Woo_Asaas_Multiple_Accounts\Admin\Plugin_Dependency;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$dependency = Plugin_Dependency::get_instance();
$plugins    = get_plugins();

foreach ( $dependency->get_dependencies() as $plugin_slug => $plugin ) :
	/* Show nothing if the dependency is active */
	if ( is_plugin_active( $plugin['plugin_file'] ) ) :
		continue;
	endif;

	if ( ! key_exists( $plugin['plugin_file'], $plugins ) ) :
		/* Plugin not installed and hasn't permission to install */
		$button_action = sprintf( 'http://wordpress.org/plugins/%s/', $plugin_slug );

		/* translators: %s: The plugin name  */
		$button_label = sprintf( __( 'Install %s', 'woo-asaas-multiple-accounts' ), $plugin['name'] );

		if ( current_user_can( 'install_plugins' ) ) :
			/* Plugin not installed and has permission to install */
			$button_action = wp_nonce_url(
				self_admin_url( sprintf( 'update.php?action=install-plugin&plugin=%s', $plugin_slug ) ),
				sprintf( 'install-plugin_%s', $plugin_slug )
			);
		endif;
	else :
		/* Plugin not active */
		$button_action = wp_nonce_url(
			self_admin_url( sprintf( 'plugins.php?action=activate&plugin=%s&plugin_status=active', $plugin['plugin_file'] ) ),
			sprintf( 'activate-plugin_%s', $plugin['plugin_file'] )
		);
		/* translators: %s: The plugin name  */
		$button_label = sprintf( __( 'Activate %s', 'woo-asaas-multiple-accounts' ), $plugin['name'] );
	endif;
	?>
<div class="error">
	<p>
		<?php
			/* translators: 1: The plugin name, 2: The dependency plugin name  */
			echo wp_kses_post( sprintf( __( '<strong>%1$s</strong> depends on the %2$s plugin to work.', 'woo-asaas-multiple-accounts' ), 'Woo Asaas Multiple Accounts', $plugin['name'] ) );
		?>
	</p>

	<p><a href="<?php echo esc_url( $button_action ); ?>" class="button button-primary"><?php echo esc_html( $button_label ); ?></a></p>
</div>
<?php endforeach; ?>
