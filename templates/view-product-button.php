<?php
/**
 * View product button
 *
 * @package WooAsaas
 */

global $product;
?>

<a href="<?php echo esc_html( $product->the_permalink() ); ?>" class="button">
	<?php echo esc_html( __( 'View product', 'woo-asaas-multiple-accounts' ) ); ?>
</a>
