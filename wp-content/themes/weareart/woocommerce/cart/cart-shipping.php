<?php
/**
 * Shipping Methods Display
 *
 * In 2.1 we show methods per package. This allows for multiple methods per order if so desired.
 *
 * @author        WooThemes
 * @package    WooCommerce/Templates
 * @version     2.3.0
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<tr class="shipping">
    <th><?php
        if ($show_package_details) {
            printf(__('Shipping #%d', 'woocommerce'), $index + 1);
        } else {
            _e('Shipping', 'woocommerce');
        }
        ?></th>
    <td>
        <?php
        $shipping_costs = 0;
        $shipping_price_de = 0;
        $shipping_price_eu = 0;
        $shipping_price_ch = 0;
        $shipping_price_at = 0;
        $artist_location = array();
        $artist_ids = array();
        $i = 0;
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

        if (isset($_POST['calc_shipping_country'])) {
            switch ($_POST['calc_shipping_country']) {
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

        if (!empty($available_methods)) {
        $shipping_costs = is_checkout() ? WC()->cart->shipping_total : $shipping_costs;
        #if (isset($_POST['calc_shipping_country'])) {
        if ($chosen_method == 'local_pickup')
            echo waa_get_woocs_int_price_reverse(0) . ' ' . get_woocommerce_currency_symbol() . ' <small>(' . __('inkl. MwSt.', 'waa') . ')</small>';
        else
            echo waa_get_woocs_int_price_reverse($shipping_costs) . ' ' . get_woocommerce_currency_symbol() . ' <small>(' . __('inkl. MwSt.', 'waa') . ')</small>';
        # }

        ?>

        <?php
        if (1 === count($available_methods)) {
            $method = current($available_methods);

            #echo wp_kses_post( wc_cart_totals_shipping_method_label( $method ) );


            ?>
            <input type="hidden" name="shipping_method[<?php echo $index; ?>]" data-index="<?php echo $index; ?>"
                   id="shipping_method_<?php echo $index; ?>" value="<?php echo esc_attr($method->id); ?>"
                   class="shipping_method"/>

        <?php } elseif (get_option('woocommerce_shipping_method_format') === 'select') { ?>
            <select name="shipping_method[<?php echo $index; ?>]" data-index="<?php echo $index; ?>"
                    id="shipping_method_<?php echo $index; ?>" class="shipping_method">
                <?php foreach ($available_methods as $method) : ?>
                    <option
                        value="<?php echo esc_attr($method->id); ?>" <?php selected($method->id, $chosen_method); ?>><?php echo wp_kses_post(wc_cart_totals_shipping_method_label($method)); ?></option>
                    <?php
                endforeach; ?>
            </select>

        <?php }
        else { ?>
        <form class="woocommerce-shipping-calculator" action="<?php echo esc_url(WC()->cart->get_cart_url()); ?>"
              method="post">
            <ul id="shipping_method">
                <?php foreach ($available_methods as $method) :
                    if ($method->id == 'local_pickup') {
                        if (cart_allows_pickup($artist_ids)) { ?>
                            <li>
                                <input type="radio" name="shipping_method[<?php echo $index; ?>]"
                                       data-index="<?php echo $index; ?>"
                                       id="shipping_method_<?php echo $index; ?>_<?php echo sanitize_title($method->id); ?>"
                                       value="<?php echo esc_attr($method->id); ?>" <?php #checked($method->id, $chosen_method);
                                if (isset($_POST['shipping_method']) && reset($_POST['shipping_method']) == esc_attr($method->id)) {
                                    echo 'checked';
                                } ?>
                                       class="shipping_method"/>
                                <label
                                    for="shipping_method_<?php echo $index; ?>_<?php echo sanitize_title($method->id); ?>"><?= __('Abholung vor Ort (kostenlos)', 'waa'); ?></label>
                            </li>
                        <?php }
                    } else { ?>
                        <li>
                            <input type="radio" name="shipping_method[<?php echo $index; ?>]"
                                   data-index="<?php echo $index; ?>"
                                   id="shipping_method_<?php echo $index; ?>_<?php echo sanitize_title($method->id); ?>"
                                   value="<?php echo esc_attr($method->id); ?>" <?php #checked($method->id, $chosen_method);
                            if ((isset($_POST['shipping_method']) && reset($_POST['shipping_method']) == esc_attr($method->id)) || !cart_allows_pickup($artist_ids)) {
                                echo 'checked ';
                            }
                            if (is_checkout())
                                echo 'checked ';
                            ?>
                                   class="shipping_method"/>
                            <label
                                for="shipping_method_<?php echo $index; ?>_<?php echo sanitize_title($method->id); ?>"><?= __('Versand durch Künstler', 'waa'); ?>
                                <span
                                    class="waa-tooltips-help tips" title=""
                                    data-original-title="<?= __('Auf dem Künstlerprofil kannst du seinen Standort für die Selbstabholung sehen, die genaue Adresse erhältst du nach Abschluss des Kaufes.', 'waa') ?>">
																			<i class="ui ui-question-circle"></i>
																		</span>
                            </label>
                        </li>
                    <?php } ?>

                <?php endforeach; ?>
            </ul>

            <?php } ?>
            <?php } elseif
            ((WC()->countries->get_states(WC()->customer->get_shipping_country()) && !WC()->customer->get_shipping_state()) || !WC()->customer->get_shipping_postcode()
            ) { ?>
                <?php if (is_cart() && get_option('woocommerce_enable_shipping_calc') === 'yes') { ?>

                    <p><?php _e('Please use the shipping calculator to see available shipping methods.', 'woocommerce'); ?></p>

                <?php } elseif (is_cart()) { ?>

                    <p><?php _e('Please continue to the checkout and enter your full address to see if there are any available shipping methods.', 'woocommerce'); ?></p>

                <?php } else { ?>

                    <p><?php _e('Please fill in your details to see available shipping methods.', 'woocommerce'); ?></p>

                <?php } ?>

            <?php } else { ?>
                444
                <?php if (is_cart()) { ?>

                    <?php echo apply_filters('woocommerce_cart_no_shipping_available_html',
                        '<p>' . __('There are no shipping methods available. Please double check your address, or contact us if you need any help.', 'woocommerce') . '</p>'
                    ); ?>

                <?php } else { ?>

                    <?php echo apply_filters('woocommerce_no_shipping_available_html',
                        '<p>' . __('There are no shipping methods available. Please double check your address, or contact us if you need any help.', 'woocommerce') . '</p>'
                    ); ?>

                <?php } ?>

            <?php } ?>

            <?php if ($show_package_details) { ?>
                <?php
                foreach ($package['contents'] as $item_id => $values) {
                    if ($values['data']->needs_shipping()) {
                        $product_names[] = $values['data']->get_title() . ' &times;' . $values['quantity'];
                    }
                }

                echo '<p class="woocommerce-shipping-contents"><small>' . __('Shipping', 'woocommerce') . ': ' . implode(', ', $product_names) . '</small></p>';
                ?>
            <?php } ?>

            <?php if (is_cart()) { ?>
                <?php woocommerce_shipping_calculator(); ?>
            <?php } ?>
    </td>
</tr>
