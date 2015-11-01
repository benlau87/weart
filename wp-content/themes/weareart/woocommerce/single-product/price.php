<?php
/**
 * Single Product Price, including microdata for SEO
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

$waa_only_print = get_post_meta($product->id, 'waa_only_print', true);
$waa_original_price = get_post_meta($product->id, 'waa_original_price', true);
?>
<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">

	<p class="price">
		<?php 
			if($waa_only_print == 'no') {
				echo '<span class="amount">'.number_format($waa_original_price, 2,',','.').'&nbsp;'.get_woocommerce_currency_symbol().'</span> <small class="woocommerce-price-suffix">'.$product->get_price_suffix().'</small></p>';
				echo '<button type="submit" class="single_add_to_cart_button button alt">'.__('Original in den Warenkorb', 'waa').'</button>';
				echo '<div class="order-prints-line"><span>'__('oder Abzüge bestellen', 'waa').'</span></div>';
			}
			else {
				echo $product->get_price_html();
			}
		?>
	</p>

		
	<meta itemprop="price" content="<?php echo $product->get_price(); ?>" />
	<meta itemprop="priceCurrency" content="<?php echo get_woocommerce_currency(); ?>" />
	<link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />

</div>
