<?php
/**
 * Account list HTML table
 *
 * @package WooAsaas
 */

use Woo_Asaas_Multiple_Accounts\Admin\Settings;

global $current_section;
?>
<tr valign="top">
	<td class="wc_payment_gateways_wrapper" colspan="2">
		<h3 id="accounts" class="wc-settings-sub-title"><?php esc_html_e( 'Accounts', 'woo-asaas-multiple-accounts' ); ?></h3>

		<div class="tablenav">
			<a class="alignleft button" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=' . $current_section . '&account' ), 'create_account' ) ); ?>"><?php echo esc_html_x( 'Add new', 'new account', 'woo-asaas-multiple-accounts' ); ?></a>
		</div>

		<table id="accounts-table" class="wc_gateways widefat" cellspacing="0">
			<thead>
				<tr>
					<?php
						$columns = array(
							'name'         => __( 'Name', 'woo-asaas-multiple-accounts' ),
							'api_key'      => __( 'API Key', 'woo-asaas-multiple-accounts' ),
							'default'      => __( 'Default', 'woo-asaas-multiple-accounts' ),
							'notification' => __( 'Notification', 'woo-asaas-multiple-accounts' ),
							'action'       => '',
						);

						foreach ( $columns as $key => $column ) {
							echo '<th class="' . esc_attr( $key ) . '">' . esc_html( $column ) . '</th>';
						}
						?>
				</tr>
			</thead>
			<tbody>
				<?php
					$gateway  = Settings::get_instance()->get_gateway();
					$accounts = $gateway->settings['accounts'];

				foreach ( $accounts as $id => $account ) {
					echo '<tr>';

					foreach ( $columns as $key => $column ) {
						$width = '';

						echo '<td class="' . esc_attr( $key ) . '" width="' . esc_attr( $width ) . '">';

						switch ( $key ) {
							case 'name':
								echo '<a href="' . esc_url( wp_nonce_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=' . $current_section . '&account=' . $id ), 'edit_acount' ) ) . '" class="wc-payment-gateway-method-title">' . esc_html( $account['name'] ) . '</a>';
								break;
							case 'api_key':
								echo wp_kses_post( $account['api_key'] );
								break;
							case 'default':
								if ( 'yes' === $account['default'] ) {
									echo '<span class="woocommerce-input-toggle woocommerce-input-toggle--enabled">' . esc_attr__( 'Yes', 'woocommerce' ) . '</span>';
								} else {
									echo '<span class="woocommerce-input-toggle woocommerce-input-toggle--disabled">' . esc_attr__( 'No', 'woocommerce' ) . '</span>';
								}
								break;
							case 'notification':
								if ( 'yes' === $account['notification'] ) {
									echo '<span class="woocommerce-input-toggle woocommerce-input-toggle--enabled">' . esc_attr__( 'Yes', 'woocommerce' ) . '</span>';
								} else {
									echo '<span class="woocommerce-input-toggle woocommerce-input-toggle--disabled">' . esc_attr__( 'No', 'woocommerce' ) . '</span>';
								}
								echo '</a>';
								break;
							case 'action':
								echo '<a class="button button--account-edit" href="' . esc_url( wp_nonce_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=' . $current_section . '&account=' . $id ), 'edit_account' ) ) . '">' . esc_html__( 'Manage', 'woo-asaas-multiple-accounts' ) . '</a>';
								if ( count( $accounts ) > 1 ) {
									echo '<a class="button button--account-delete" style="margin-left: 10px" href="' . esc_url( wp_nonce_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=' . $current_section . '&account=' . $id . '&delete' ) ) ) . '" data-confirm="' . esc_html__( 'Do you really want to remove a default account?', 'woo-asaas-multiple-accounts' ) . '">' . esc_html__( 'Delete', 'woo-asaas-multiple-accounts' ) . '</a>';
								} else {
									echo '<button class="button" style="margin-left: 10px" disabled>' . esc_html__( 'Delete', 'woo-asaas-multiple-accounts' ) . '</button>';
								}
								break;
						}

						echo '</td>';
					}

					echo '</tr>';
				}
				?>
			</tbody>
		</table>
	</td>
</tr>
