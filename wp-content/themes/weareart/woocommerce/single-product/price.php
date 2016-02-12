<?php
/**
 * Single Product Price, including microdata for SEO
 *
 * @author        WooThemes
 * @package    WooCommerce/Templates
 * @version     1.6.4
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $product;

$waa_only_print = get_post_meta($product->id, 'waa_only_print', true);
$waa_original_price = waa_get_woocs_price_html($product->id, 'waa_original_price');

if ($product->product_type == 'variable')
    $available_variations = $product->get_available_variations();
?>
<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
    <?php if ($waa_only_print == 'no' && $product->product_type == 'variable' && get_post_meta($product->id, 'waa_original_price', true) != '') { ?>
    <form class="variations_form cart" method="post" action="<?php bloginfo('url') ?>/checkout/"
          enctype="multipart/form-data">
        <div class="single_variation_wrap" style="display: none;">
            <input type="hidden" name="add-to-cart" value="<?php echo $product->id; ?>">
            <input type="hidden" name="product_id" value="<?php echo $product->id; ?>">
            <input type="hidden" name="attribute_pa_print_groesse" value="original">
            <input type="hidden" name="attribute_pa_print_material" value="original">
            <input type="hidden" name="variation_id" value="<?= $available_variations[0]['variation_id'] ?>">
        </div>
        <?php
        echo $waa_original_price;
        echo '<button type="submit" class="single_add_to_cart_button button alt">' . __('Original in den Warenkorb', 'waa') . '</button>';
        echo '<div class="order-prints-line"><span>' . __('oder Abzüge bestellen', 'waa') . '</span></div>';
        echo '</form>';
        }
        elseif ($product->product_type == 'variable') {
            echo waa_get_variable_price_html($product->id);
        } else {
            echo waa_get_woocs_price_html($product->id, '_regular_price');
        }
        ?>
        </p>
        <meta itemprop="price" content="<?php echo $product->get_price(); ?>"/>
        <meta itemprop="priceCurrency" content="<?php echo get_woocommerce_currency(); ?>"/>
        <link itemprop="availability"
              href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>"/>

</div>
