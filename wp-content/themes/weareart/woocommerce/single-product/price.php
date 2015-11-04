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

if($product->product_type == 'variable')
	$available_variations = $product->get_available_variations();
?>
<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
<?php if($waa_only_print == 'no' && $product->product_type == 'variable') { ?>
			<form class="variations_form cart" method="post" action="<?php bloginfo('url') ?>/checkout/" enctype="multipart/form-data">
			<div class="single_variation_wrap" style="display: none;">
				<input type="hidden" name="add-to-cart" value="<?php echo $product->id; ?>">
				<input type="hidden" name="product_id" value="<?php echo $product->id; ?>">
				<input type="hidden" name="attribute_pa_print_groesse" value="original">
				<input type="hidden" name="attribute_pa_print_material" value="original">
				<input type="hidden" name="variation_id" value="<?=$available_variations[0]['variation_id']?>">
			</div>
			<?php
				echo '<p class="price"><span class="amount">'.number_format($waa_original_price, 2,',','.').'&nbsp;'.get_woocommerce_currency_symbol().'</span> <small class="woocommerce-price-suffix">'.$product->get_price_suffix().'</small></p>';
				echo '<button type="submit" class="single_add_to_cart_button button alt">'.__('Original in den Warenkorb', 'waa').'</button>';
				echo '<div class="order-prints-line"><span>'.__('oder Abzüge bestellen', 'waa').'</span></div>';
				echo '</form>';
			}
			elseif ($product->product_type == 'variable') {
				echo '<p class="price"><span class="amount">'.waa_get_variable_price($product->id).'</span> <small class="woocommerce-price-suffix">'.$product->get_price_suffix().'</small></p>';
			} else {
				echo '<p class="price">'.$product->get_price_html().'</p>';
			}
		?>	    			
	</p>		
	<meta itemprop="price" content="<?php echo $product->get_price(); ?>" />
	<meta itemprop="priceCurrency" content="<?php echo get_woocommerce_currency(); ?>" />
	<link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />

</div>
