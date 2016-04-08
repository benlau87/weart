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
        $shipping_costs = 0;
        $shipping_price_de = 0;
        $shipping_price_eu = 0;
        $shipping_price_ch = 0;
        $shipping_price_at = 0;
        $artist_location = array();
        $artist_ids = array();
        $i=0;
        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $variation_id = $cart_item['variation_id'];
            $product_id = $cart_item['product_id'];

            if (isset($cart_item['variation_id']) && !empty($cart_item['variation_id'])) {
                $artist_location = waa_get_artist_location($variation_id);
                $shipping_price_de += ($artist_location == 'DE' ? get_post_meta($variation_id, '_shipping_price_de', true) : get_post_meta($variation_id, '_shipping_price_eu', true)) * $cart_item['quantity'];
                #$shipping_price_eu += (get_post_meta($variation_id, '_shipping_price_eu', true) * $cart_item['quantity']);
                $shipping_price_ch += (get_post_meta($variation_id, '_shipping_price_ch', true) * $cart_item['quantity']);
                $shipping_price_at += ($artist_location == 'AT' ? get_post_meta($variation_id, '_shipping_price_at', true) : get_post_meta($variation_id, '_shipping_price_eu', true)) * $cart_item['quantity'];
                $artist_ids[$i] = waa_get_artist_by_product($variation_id);
            } else {
                $artist_location = waa_get_artist_location($product_id);

                $shipping_prices = get_post_meta($product_id, '_additional_price', true);

                $shipping_price_de += ($artist_location == 'DE' ? $shipping_prices['DE'] : $shipping_prices['everywhere']) * $cart_item['quantity'];
                #$shipping_price_eu += ($shipping_prices['everywhere'] * $cart_item['quantity']);
                $shipping_price_ch += ($shipping_prices['CH'] * $cart_item['quantity']);
                $shipping_price_at += ($artist_location == 'AT' ? $shipping_prices['AT'] : $shipping_prices['everywhere']) * $cart_item['quantity'];
                $artist_location = waa_get_artist_location($product_id);
                $artist_ids[$i] = waa_get_artist_by_product($product_id);
                $i++;
            }
        }

        if (isset($_POST['country'])) {
            switch ($_POST['country']) {
                case 'AT':
                    $shipping_costs += $shipping_price_at;
                    break;
                case 'DE':
                    $shipping_costs += $shipping_price_de;
                    break;
                case 'CH':
                    $shipping_costs += $shipping_price_ch;
                    break;
            }
        }

        WC()->session->set( 'shipping_total' , $shipping_costs );
        WC()->session->set( 'total' , WC()->cart->total+$shipping_costs );
        WC()->session->set( 'subtotal' , WC()->cart->total+$shipping_costs );
        WC()->session->set( 'cart_contents_total' , WC()->cart->total+$shipping_costs );
        #echo $shipping_costs;

        echo WC()->cart->shipping_total;
        #echo WC()->cart->shipping_total;
        WC()->cart->cart_session_data['shipping_total'] = $shipping_costs;
        #echo WC()->cart->shipping_total;
        #echo WC()->cart->get_cart_shipping_total();
        #print_r(WC()->cart->get_shipping_packages())
        ?>

        <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

            <?php do_action( 'woocommerce_review_order_before_shipping' ); ?>

            <?php wc_cart_totals_shipping_html(); ?>

            <?php do_action( 'woocommerce_review_order_after_shipping' ); ?>

        <?php endif; ?>

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
                echo $chosen_shipping == 'local_pickup' ? waa_get_woocs_int_price_reverse(WC()->cart->total) . ' ' . get_woocommerce_currency_symbol() : waa_get_woocs_int_price_reverse(WC()->cart->total) . ' ' . get_woocommerce_currency_symbol();
                ?></strong>
            <small>(<?php _e('inkl. MwSt.', 'waa') ?>)</small>
        </td>
    </tr>

    <?php
   # print_r($_REQUEST);

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