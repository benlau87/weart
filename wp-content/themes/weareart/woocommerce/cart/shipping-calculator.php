<?php
/**
 * Shipping Calculator
 *
 * @author        WooThemes
 * @package    WooCommerce/Templates
 * @version     2.0.8
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (get_option('woocommerce_enable_shipping_calc') === 'no' || !WC()->cart->needs_shipping()) {
    return;
}

?>

<?php do_action('woocommerce_before_shipping_calculator'); ?>

    <p><a href="#" class="shipping-calculator-button"><?php _e('Calculate Shipping', 'woocommerce'); ?></a></p>

    <section class="shipping-calculator-form" style="display:none;">

        <p class="form-row form-row-wide" id="calc_shipping_country_field">
            <select name="calc_shipping_country" id="calc_shipping_country" class="country_to_state" rel="calc_shipping_state">
                <option value=""><?php _e( 'Select a country&hellip;', 'woocommerce' ); ?></option>
                <?php
                foreach( WC()->countries->get_shipping_countries() as $key => $value )
                    echo '<option value="' . esc_attr( $key ) . '"' . selected( WC()->customer->get_shipping_country(), esc_attr( $key ), false ) . '>' . esc_html( $value ) . '</option>';
                ?>
            </select>
        </p>

        <p>
            <button type="submit" name="calc_shipping" value="1"
                    class="button"><?php _e('Update Totals', 'woocommerce'); ?></button>
        </p>

        <?php wp_nonce_field('woocommerce-cart'); ?>
    </section>
</form>

<?php do_action('woocommerce_after_shipping_calculator'); ?>
