<?php
/**
 * Review order table
 *
 * @author        WooThemes
 * @package    WooCommerce/Templates
 * @version     2.3.0
 */

if (!defined('ABSPATH')) {
    exit;
}
$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
$chosen_shipping = $chosen_methods[0];
?>
<table class="shop_table woocommerce-checkout-review-order-table">
    <thead>
    <tr>
        <th class="product-name"><?php _e('Product', 'woocommerce'); ?></th>
        <th class="product-total"><?php _e('Total', 'woocommerce'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    do_action('woocommerce_review_order_before_cart_contents');

    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

        if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key)) {
            ?>
            <tr class="<?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
                <td class="product-name">
                    <?php echo apply_filters('woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key) . '&nbsp;'; ?>
                    <?php echo apply_filters('woocommerce_checkout_cart_item_quantity', ' <strong class="product-quantity">' . sprintf('&times; %s', $cart_item['quantity']) . '</strong>', $cart_item, $cart_item_key); ?>
                    <?php echo WC()->cart->get_item_data($cart_item); ?>
                </td>
                <td class="product-total">
                    <?php echo waa_get_woocs_int_price_reverse($_product->price*$cart_item['quantity']) . ' ' . get_woocommerce_currency_symbol();
                   # echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); ?>
                </td>
            </tr>
            <?php
        }
    }

    do_action('woocommerce_review_order_after_cart_contents');
    ?>
    </tbody>
    <tfoot>

    <tr class="cart-subtotal">
        <th><?php _e('Subtotal', 'woocommerce'); ?></th>
        <td><?php echo waa_get_woocs_int_price_reverse(WC()->cart->subtotal) . ' ' . get_woocommerce_currency_symbol(); ?> <small>(<?php _e('inkl. MwSt.', 'waa') ?>)</small></td>
    </tr>

    <?php

    foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
        <tr class="cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
            <th><?php wc_cart_totals_coupon_label($coupon); ?></th>
            <td><?php wc_cart_totals_coupon_html($coupon); ?></td>
        </tr>
    <?php endforeach; ?>

    <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>

        <?php do_action('woocommerce_review_order_before_shipping'); ?>

        <?php
        #wc_cart_totals_shipping_html();
        $shipping_costs = 0;
        $shipping_price_de = 0;
        $shipping_price_eu = 0;
        $shipping_price_ch = 0;
        $shipping_price_at = 0;
        $artist_location = 0;
        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $variation_id = $cart_item['variation_id'];
            $product_id = $cart_item['product_id'];
            if(isset($cart_item['variation_id']) && !empty($cart_item['variation_id'])) {
                $shipping_price_de += (get_post_meta($variation_id, '_shipping_price_de', true) * $cart_item['quantity']);
                $shipping_price_eu += (get_post_meta($variation_id, '_shipping_price_eu', true) * $cart_item['quantity']);
                $shipping_price_ch += (get_post_meta($variation_id, '_shipping_price_ch', true) * $cart_item['quantity']);
                $shipping_price_at += (get_post_meta($variation_id, '_shipping_price_at', true) * $cart_item['quantity']);
                $artist_location = waa_get_artist_location($variation_id);

            } else {
                $shipping_prices = get_post_meta($product_id, '_additional_price', true);
                $shipping_price_de += ($shipping_prices['DE'] * $cart_item['quantity']);
                $shipping_price_eu += ($shipping_prices['everywhere'] * $cart_item['quantity']);
                $shipping_price_ch += ($shipping_prices['CH'] * $cart_item['quantity']);
                $shipping_price_at += ($shipping_prices['AT'] * $cart_item['quantity']);
                $artist_location = waa_get_artist_location($product_id);
            }
        }

        /**
         * todo: wenn mehrere Produkte verschiedener Künstler im Warenkorb liegen, muss $artist_location berücksichtigt werden
         */

        if (isset($_POST['country'])) {
            $country = isset($_POST['s_country']) ? $_POST['s_country'] : $_POST['country'];
            switch ($country) {
                case 'AT':
                    $shipping_costs = $artist_location == 'AT' ? $shipping_price_at : $shipping_price_eu;
                    $shipping_country_to = 'AT';
                    break;
                case 'DE':
                    $shipping_costs = $artist_location == 'DE' ? $shipping_price_de : $shipping_price_eu;
                    $shipping_country_to = 'DE';
                    break;
                case 'CH':
                    $shipping_costs = $shipping_price_ch;
                    $shipping_country_to = 'CH';
                    break;
            }
        }

        ?>
        <tr class="shipping">
            <th><?= __('Shipping', 'woocommerce'); ?></th>
            <td><?= $chosen_shipping == 'local_pickup' ? number_format(0, 2, ',', '.') . ' ' . get_woocommerce_currency_symbol() : waa_get_woocs_int_price_reverse($shipping_costs) . ' ' . get_woocommerce_currency_symbol() . ' <small>(' . __('inkl. MwSt.', 'waa') . ')</small>'; ?>
            </td>
        </tr>

        <?php do_action('woocommerce_review_order_after_shipping'); ?>

    <?php endif; ?>

    <?php foreach (WC()->cart->get_fees() as $fee) : ?>
        <tr class="fee">
            <th><?php echo esc_html($fee->name); ?></th>
            <td><?php wc_cart_totals_fee_html($fee); ?></td>
        </tr>
    <?php endforeach; ?>

    <?php if (wc_tax_enabled() && WC()->cart->tax_display_cart === 'excl') : ?>
        <?php if (get_option('woocommerce_tax_total_display') === 'itemized') : ?>
            <?php foreach (WC()->cart->get_tax_totals() as $code => $tax) : ?>
                <tr class="tax-rate tax-rate-<?php echo sanitize_title($code); ?>">
                    <th><?php echo esc_html($tax->label); ?></th>
                    <td><?php echo wp_kses_post($tax->formatted_amount); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr class="tax-total">
                <th><?php echo esc_html(WC()->countries->tax_or_vat()); ?></th>
                <td><?php wc_cart_totals_taxes_total_html(); ?></td>
            </tr>
        <?php endif; ?>
    <?php endif; ?>

    <?php do_action('woocommerce_review_order_before_order_total'); ?>

    <tr class="order-total">
        <th><?php _e('Total', 'woocommerce'); ?></th>
        <td><strong><?php
                echo $chosen_shipping == 'local_pickup' ? waa_get_woocs_int_price_reverse(WC()->cart->total) . ' ' . get_woocommerce_currency_symbol() : waa_get_woocs_int_price_reverse(WC()->cart->total+$shipping_costs) . ' ' . get_woocommerce_currency_symbol();
                ?></strong>
            <small>(<?php _e('inkl. MwSt.', 'waa') ?>)</small>
        </td>
    </tr>

    <?php
    function woocommerce_custom_surcharge($shipping_costs) {
        global $woocommerce;

        if ( ! defined( 'DOING_AJAX' ) )
            return;

        $surcharge = $shipping_costs;
        $woocommerce->cart->add_fee( 'Versand', $surcharge, true, '' );

    }
    #woocommerce_custom_surcharge($shipping_costs);
    #print_r( WC()->cart->get_fees());
    $rate = array(
        'id' => 'waa_product_shipping',
        'label' => 'Shipping',
        'cost' => $shipping_costs,
        'calc_tax' => 'per_order'
    );
    $shipping = new waa_WC_Shipping();
    $shipping->add_rate( $rate );
    #print_r($shipping->rates);

    ?>

    <?php do_action('woocommerce_review_order_after_order_total'); ?>

    </tfoot>
</table>