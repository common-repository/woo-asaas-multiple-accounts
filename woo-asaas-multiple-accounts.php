<?php
/**
 * Plugin Name:     Woo Asaas Multiple Accounts
 * Description:     Allow multiple Asaas API accounts and associate each product with one account.
 * Author:          Asaas
 * Author URI:      https://www.asaas.com
 * Text Domain:     woo-asaas-multiple-accounts
 * Domain Path:     /languages
 * Version:         1.0.1
 *
 * @package         WooAsaas
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once 'autoload.php';

add_action( 'plugins_loaded', array( \Woo_Asaas_Multiple_Accounts\Woo_Asaas_Multiple_Accounts::class, 'get_instance' ) );
