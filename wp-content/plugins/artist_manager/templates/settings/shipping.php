<?php
/**
 * Shipping template
 *
 * @since  2.0
 */

$country_obj     = new WC_Countries();
$countries       = $country_obj->countries;
$states          = $country_obj->states;
$user_id         = get_current_user_id();
$processing_time = waa_get_shipping_processing_times();

$dps_enable_shipping     = get_user_meta( $user_id, '_dps_shipping_enable', true );
$dps_shipping_type_price = get_user_meta( $user_id, '_dps_shipping_type_price', true );
$dps_additional_product  = get_user_meta( $user_id, '_dps_additional_product', true );
$dps_additional_qty      = get_user_meta( $user_id, '_dps_additional_qty', true );
$dps_form_location       = get_user_meta( $user_id, '_dps_form_location', true );
$dps_country_rates       = get_user_meta( $user_id, '_dps_country_rates', true );
$dps_state_rates         = get_user_meta( $user_id, '_dps_state_rates', true );
$dps_pt                  = get_user_meta( $user_id, '_dps_pt', true );
$dps_shipping_policy     = get_user_meta( $user_id, '_dps_ship_policy', true );
$dps_refund_policy       = get_user_meta( $user_id, '_dps_refund_policy', true );


?>
<div class="waa-dashboard-wrap">
    <?php waa_get_template( 'dashboard-nav.php', array( 'active_menu' => 'settings/shipping' ) ); ?>

    <div class="waa-dashboard-content waa-settings-content">
        <article class="waa-settings-area">
            <header class="waa-dashboard-header">
                <h1 class="entry-title">
                    <?php _e( 'Shipping Settings', 'waa' ); ?>
                    <small>&rarr; <a href="<?php echo waa_get_store_url( get_current_user_id() ); ?>"><?php _e( 'Visit Store', 'waa' ); ?></a></small>
                </h1>
            </header><!-- .waa-dashboard-header -->

            <div class="waa-page-help">
                <p><?php _e( 'This page contains your store-wide shipping settings, costs, shipping and refund policy.', 'waa' ); ?></p>
                <p><?php _e( 'You can enable/disable shipping for your products. Also you can override these shipping costs from an individual product.', 'waa' ); ?></p>
            </div>

            <?php
            if ( isset( $_GET['message'] ) && $_GET['message'] == 'shipping_saved' ) {
                ?>
                <div class="waa-message">
                    <button type="button" class="waa-close" data-dismiss="alert">&times;</button>
                    <strong><?php _e('Shipping options saved successfully','waa'); ?></strong>
                </div>
                <?php
            }
            ?>

            <form method="post" id="shipping-form"  action="" class="waa-form-horizontal">

                <?php  wp_nonce_field( 'waa_shipping_form_field', 'waa_shipping_form_field_nonce' ); ?>

                <?php
                /**
                 * @since 2.2.2 Insert action before shipping settings form
                 */
                do_action( 'waa_shipping_settings_form_top' ); ?>

                <div class="waa-form-group">
                    <label class="waa-w4 waa-control-label" for="dps_enable_shipping" style="margin-top:6px">
                        <?php _e( 'Enable Shipping', 'waa' ); ?>
                        <span class="waa-tooltips-help tips" title="<?php esc_attr_e( 'Check this if you want to enable shipping for your store', 'waa' ); ?>">
                            <i class="ui ui-question-circle"></i>
                        </span>
                    </label>

                    <div class="waa-w5 waa-text-left">
                        <div class="checkbox">
                            <label>
                                <input type="hidden" name="dps_enable_shipping" value="no">
                                <input type="checkbox" name="dps_enable_shipping" value="yes" <?php checked( 'yes', $dps_enable_shipping, true ); ?>> <?php _e( 'Enable shipping functionality', 'waa' ); ?>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="waa-shipping-wrapper">

                    <div class="waa-form-group waa-shipping-price waa-shipping-type-price">
                        <label class="waa-w4 waa-control-label" for="shipping_type_price">
                            <?php _e( 'Default Shipping Price', 'waa' ); ?>
                            <span class="waa-tooltips-help tips" title="<?php esc_attr_e( 'This is the base price and will be the starting shipping price for each product', 'waa' ); ?>">
                                <i class="ui ui-question-circle"></i>
                            </span>
                        </label>

                        <div class="waa-w5 waa-text-left">
                            <input id="shipping_type_price" value="<?php echo $dps_shipping_type_price; ?>" name="dps_shipping_type_price" placeholder="0.00" class="waa-form-control" type="number" step="any" min="0">
                        </div>
                    </div>

                    <div class="waa-form-group waa-shipping-price waa-shipping-add-product">
                        <label class="waa-w4 waa-control-label" for="dps_additional_product">
                            <?php _e( 'Per Product Additional Price', 'waa' ); ?>
                            <span class="waa-tooltips-help tips" title="<?php esc_attr_e( 'If a customer buys more than one type product from your store, first product of the every second type will be charged with this price', 'waa' ); ?>">
                                <i class="ui ui-question-circle"></i>
                            </span>
                        </label>

                        <div class="waa-w5 waa-text-left">
                            <input id="additional_product" value="<?php echo $dps_additional_product; ?>" name="dps_additional_product" placeholder="0.00" class="waa-form-control" type="number" step="any" min="0">
                        </div>
                    </div>

                    <div class="waa-form-group waa-shipping-price waa-shipping-add-qty">
                        <label class="waa-w4 waa-control-label" for="dps_additional_qty">
                            <?php _e( 'Per Qty Additional Price', 'waa' ); ?>
                            <span class="waa-tooltips-help tips" title="<?php esc_attr_e( 'Every second product of same type will be charged with this price', 'waa' ); ?>">
                                <i class="ui ui-question-circle"></i>
                            </span>
                        </label>

                        <div class="waa-w5 waa-text-left">
                            <input id="additional_qty" value="<?php echo $dps_additional_qty; ?>" name="dps_additional_qty" placeholder="0.00" class="waa-form-control" type="number" step="any" min="0">
                        </div>
                    </div>

                    <div class="waa-form-group waa-shipping-price waa-shipping-add-qty">
                        <label class="waa-w4 waa-control-label" for="dps_pt">
                            <?php _e( 'Processing Time', 'waa' ); ?>
                            <span class="waa-tooltips-help tips" title="<?php esc_attr_e( 'The time required before sending the product for delivery', 'waa' ); ?>">
                                <i class="ui ui-question-circle"></i>
                            </span>
                        </label>

                        <div class="waa-w5 waa-text-left">
                            <select name="dps_pt" id="dps_pt" class="waa-form-control">
                                <?php foreach ( $processing_time as $processing_key => $processing_value ): ?>
                                      <option value="<?php echo $processing_key; ?>" <?php selected( $dps_pt, $processing_key ); ?>><?php echo $processing_value; ?></option>
                                <?php endforeach ?>
                            </select>
                            <!-- <input id="additional_qty" value="<?php echo $dps_pt; ?>" name="dps_pt" placeholder="0.00" class="waa-form-control" type="number" step="any" min="0"> -->
                        </div>
                    </div>

                    <div class="waa-form-group">
                        <label class="waa-w4 waa-control-label" for="_dps_ship_policy">
                            <?php _e( 'Shipping Policy', 'waa' ); ?>
                            <span class="waa-tooltips-help tips" title="<?php _e( 'Write your terms, conditions and instructions about shipping', 'waa' ); ?>">
                                <i class="ui ui-question-circle"></i>
                            </span>
                        </label>

                        <div class="waa-w6 waa-text-left">
                            <textarea name="dps_ship_policy" id="" class="waa-form-control"><?php echo $dps_shipping_policy; ?></textarea>
                        </div>
                    </div>

                    <div class="waa-form-group">
                        <label class="waa-w4 waa-control-label" for="_dps_refund_policy">
                            <?php _e( 'Refund Policy', 'waa' ); ?>
                            <span class="waa-tooltips-help tips" title="<?php _e( 'Write your terms, conditions and instructions about refund', 'waa' ); ?>">
                                <i class="ui ui-question-circle"></i>
                            </span>
                        </label>

                        <div class="waa-w6 waa-text-left">
                            <textarea name="dps_refund_policy" id="" class="waa-form-control"><?php echo $dps_refund_policy; ?></textarea>
                        </div>
                    </div>

                    <div class="waa-form-group">
                        <label class="waa-w4 waa-control-label" for="dps_form_location">
                            <?php _e( 'Ships from:', 'waa' ); ?>
                            <span class="waa-tooltips-help tips" title="<?php _e( 'The place you send the products for delivery. Most of the time it as store location', 'waa' ); ?>">
                                <i class="ui ui-question-circle"></i>
                            </span>
                        </label>

                        <div class="waa-w5">
                            <select name="dps_form_location" class="waa-form-control">
                                <?php waa_country_dropdown( $countries, $dps_form_location ); ?>
                            </select>
                        </div>
                    </div>

                    <div class="waa-form-group">

                        <div class="waa-w12 dps-main-wrapper">
                            <div class="waa-shipping-location-wrapper">

                            <p class="waa-page-help"><?php _e( 'Add the countries you deliver your products to. You can specify states as well. If the shipping price is same except some countries/states, there is an option <strong>Everywhere Else</strong>, you can use that.', 'waa' ) ?></p>

                            <?php if ( $dps_country_rates ) : ?>

                                <?php foreach ( $dps_country_rates as $country => $country_rate ) : ?>

                                    <div class="dps-shipping-location-content">

                                        <table class="dps-shipping-table">
                                            <tbody>

                                                <tr class="dps-shipping-location">
                                                    <td width="40%">
                                                        <label for=""><?php _e( 'Ship to', 'waa' ); ?>
                                                        <span class="waa-tooltips-help tips" title="<?php _e( 'The country you ship to', 'waa' ); ?>">
                                                        <i class="ui ui-question-circle"></i></span></label>
                                                        <select name="dps_country_to[]" class="waa-form-control dps_country_selection" id="dps_country_selection">
                                                            <?php waa_country_dropdown( $countries, $country, true ); ?>
                                                        </select>
                                                    </td>
                                                    <td class="dps_shipping_location_cost">
                                                        <label for=""><?php _e( 'Cost', 'waa' ); ?>
                                                        <span class="waa-tooltips-help tips" title="<?php _e( 'If the shipping price is same for all the states, use this field. Else add the states below', 'waa' ); ?>">
                                                        <i class="ui ui-question-circle"></i></span></label>
                                                        <div class="waa-input-group">
                                                            <span class="waa-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                                            <input type="text" placeholder="0.00" class="waa-form-control" name="dps_country_to_price[]" value="<?php echo esc_attr( $country_rate ); ?>">
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr class="dps-shipping-states-wrapper">
                                                    <table class="dps-shipping-states">
                                                        <tbody>
                                                           <?php if ( $dps_state_rates ): ?>
                                                                <?php if ( isset( $dps_state_rates[$country] ) ): ?>

                                                                    <?php foreach ( $dps_state_rates[$country] as $state => $state_rate ): ?>

                                                                        <?php if ( isset( $states[$country] ) && !empty( $states[$country] ) ): ?>

                                                                            <tr>
                                                                                <td>
                                                                                    <label for=""><?php _e( 'State', 'waa' ) ?>
                                                                                    <span class="waa-tooltips-help tips" title="<?php _e( 'The state you ship to', 'waa' ); ?>">
                                                                                    <i class="ui ui-question-circle"></i></span></label>
                                                                                    <select name="dps_state_to[<?php echo $country ?>][]" class="waa-form-control dps_state_selection">
                                                                                        <?php waa_state_dropdown( $states[$country], $state, true ); ?>
                                                                                    </select>
                                                                                </td>
                                                                                <td>
                                                                                    <label for=""><?php _e( 'Cost', 'waa' ); ?>
                                                                                    <span class="waa-tooltips-help tips" title="<?php _e( 'Shipping price for this state', 'waa' ); ?>">
                                                                                    <i class="ui ui-question-circle"></i></span></label>
                                                                                    <div class="waa-input-group">
                                                                                        <span class="waa-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                                                                        <input type="text" placeholder="0.00" value="<?php echo $state_rate; ?>" class="waa-form-control" name="dps_state_to_price[<?php echo $country; ?>][]">
                                                                                    </div>
                                                                                </td>

                                                                                <td width="15%">
                                                                                    <label for=""></label>
                                                                                    <div>
                                                                                        <a class="dps-add" href="#"><i class="ui ui-plus"></i></a>
                                                                                        <a class="dps-remove" href="#"><i class="ui ui-minus"></i></a>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>

                                                                        <?php else: ?>

                                                                            <tr>
                                                                                <td>
                                                                                    <label for=""><?php _e( 'State', 'waa' ); ?></label>
                                                                                    <input type="text" name="dps_state_to[<?php echo $country ?>][]" class="waa-form-control dps_state_selection" placeholder="<?= __('State', 'waa'); ?>" value="<?php echo $state; ?>">
                                                                                </td>
                                                                                <td>
                                                                                    <label for=""><?php _e( 'Cost', 'waa' ); ?></label>
                                                                                    <div class="waa-input-group">
                                                                                        <span class="waa-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                                                                        <input type="text" placeholder="0.00" class="waa-form-control" name="dps_state_to_price[<?php echo $country; ?>][]" value="<?php echo $state_rate; ?>">
                                                                                    </div>
                                                                                </td>

                                                                                <td width="14%">
                                                                                    <label for=""></label>
                                                                                    <div>
                                                                                        <a class="dps-add" href="#"><i class="ui ui-plus"></i></a>
                                                                                        <a class="dps-remove" href="#"><i class="ui ui-minus"></i></a>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>

                                                                        <?php endif ?>

                                                                    <?php endforeach ?>

                                                                <?php endif ?>

                                                            <?php endif ?>
                                                        </tbody>
                                                    </table>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <a href="#" class="dps-shipping-remove"><i class="ui ui-remove"></i></a>
                                    </div>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <div class="dps-shipping-location-content">
                                    <table class="dps-shipping-table">
                                        <tbody>
                                            <tr class="dps-shipping-location">
                                                <td>
                                                    <label for=""><?php _e( 'Ship to', 'waa' ); ?>
                                                    <span class="waa-tooltips-help tips" title="<?php _e( 'The country you ship to', 'waa' ); ?>">
                                                    <i class="ui ui-question-circle"></i></span></label>
                                                    <select name="dps_country_to[]" class="waa-form-control dps_country_selection" id="dps_country_selection">
                                                        <?php waa_country_dropdown( $countries, '', true ); ?>
                                                    </select>
                                                </td>
                                                <td class="dps_shipping_location_cost">
                                                    <label for=""><?php _e( 'Cost', 'waa' ); ?>
                                                    <span class="waa-tooltips-help tips" title="<?php _e( 'If the shipping price is same for all the states, use this field. Else add the states below', 'waa' ); ?>">
                                                    <i class="ui ui-question-circle"></i></span></label>
                                                    <div class="waa-input-group">
                                                        <span class="waa-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                                        <input type="text" placeholder="0.00" class="waa-form-control" name="dps_country_to_price[]">
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr class="dps-shipping-states-wrapper">
                                                <table class="dps-shipping-states">
                                                    <tbody></tbody>
                                                </table>
                                            </tr>

                                        </tbody>
                                    </table>
                                    <a href="#" class="dps-shipping-remove"><i class="ui ui-remove"></i></a>
                                </div>
                            <?php endif; ?>

                            </div>
                            <a href="#" class="waa-btn waa-btn-default dps-shipping-add waa-right"><?php _e( 'Add Location', 'waa' ); ?></a>
                        </div>
                    </div>

                </div>

                <?php
                /**
                 * @since 2.2.2 Insert action after social settings form
                 */
                do_action( 'waa_shipping_settings_form_bottom' ); ?>

                <div class="waa-form-group">

                    <div class="waa-w4 ajax_prev waa-text-left" style="margin-left:23%;">
                        <input type="submit" name="waa_update_shipping_options" class="waa-btn waa-btn-danger waa-btn-theme" value="<?php esc_attr_e( 'Save Settings', 'waa' ); ?>">
                    </div>
                </div>

            </form>
        </article>
    </div><!-- .waa-dashboard-content -->
</div><!-- .waa-dashboard-wrap -->

<!-- Added for Render content via Jquery -->

<div class="dps-shipping-location-content" id="dps-shipping-hidden-lcoation-content">
    <table class="dps-shipping-table">
        <tbody>

            <tr class="dps-shipping-location">
                <td>
                    <label for=""><?php _e( 'Ship to', 'waa' ); ?>
                    <span class="waa-tooltips-help tips" title="<?php _e( 'The country you ship to', 'waa' ); ?>">
                    <i class="ui ui-question-circle"></i></span></label>
                    <select name="dps_country_to[]" class="waa-form-control dps_country_selection" id="dps_country_selection">
                        <?php waa_country_dropdown( $countries, '', true ); ?>
                    </select>
                </td>
                <td class="dps_shipping_location_cost">
                    <label for=""><?php _e( 'Cost', 'waa' ); ?>
                    <span class="waa-tooltips-help tips" title="<?php _e( 'If the shipping price is same for all the states, use this field. Else add the states below', 'waa' ); ?>">
                    <i class="ui ui-question-circle"></i></span></label>
                    <div class="waa-input-group">
                        <span class="waa-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                        <input type="text" placeholder="0.00" class="waa-form-control" name="dps_country_to_price[]">
                    </div>
                </td>
            </tr>
            <tr class="dps-shipping-states-wrapper">
                <table class="dps-shipping-states">
                    <tbody></tbody>
                </table>
            </tr>
        </tbody>
    </table>
    <a href="#" class="dps-shipping-remove"><i class="ui ui-remove"></i></a>
</div>

<!-- End of render content via jquery -->
