<?php
$country_obj = new WC_Countries();
$countries = $country_obj->countries;
$states = $country_obj->states;
$user_id = get_current_user_id();
$processing_time = waa_get_shipping_processing_times();

$dps_enable_shipping = get_user_meta($user_id, '_dps_shipping_enable', true);
$dps_enable_pickup = get_user_meta($user_id, '_dps_enable_pickup', true);
$profile_info = waa_get_store_info($user_id);
$address_country = isset($profile_info['address']['country']) ? $profile_info['address']['country'] : '';
$dps_form_location = get_user_meta($user_id, '_dps_form_location', true);
$dps_form_location = $dps_form_location ? $dps_form_location : $address_country;
$dps_country_rates = get_user_meta($user_id, '_dps_country_rates', true);
$dps_state_rates = get_user_meta($user_id, '_dps_state_rates', true);
$dps_pt = get_user_meta($user_id, '_dps_pt', true);
$dps_shipping_policy = get_user_meta($user_id, '_dps_ship_policy', true);
$dps_refund_policy = get_user_meta($user_id, '_dps_refund_policy', true);

$dps_country_to = get_user_meta($user_id, '_dps_country_rates', true);
$dps_home_country = array_key_exists('home_country', $dps_country_to) ? 'yes' : '';
$dps_eu_countries = array_key_exists('eu_countries', $dps_country_to) ? 'yes' : '';
$dps_switzerland = array_key_exists('switzerland', $dps_country_to) ? 'yes' : '';

?>
<div class="waa-dashboard-wrap">
    <?php waa_get_template('dashboard-nav.php', array('active_menu' => 'settings/shipping')); ?>

    <div class="waa-dashboard-content waa-settings-content">
        <article class="waa-settings-area">
            <header class="waa-dashboard-header">
                <h1 class="entry-title">
                    <?php _e('Shipping Settings', 'waa'); ?>
                    <small>&rarr; <a
                            href="<?php echo waa_get_store_url(get_current_user_id()); ?>"><?php _e('Visit Store', 'waa'); ?></a>
                    </small>
                </h1>
            </header><!-- .waa-dashboard-header -->

            <div class="waa-page-help">
                <p><?php _e('This page contains your store-wide shipping settings, costs, shipping and refund policy.', 'waa'); ?></p>

                <p><?php _e('You can enable/disable shipping for your products. Also you can override these shipping costs from an individual product.', 'waa'); ?></p>
            </div>

            <?php
            if (isset($_GET['message']) && $_GET['message'] == 'shipping_saved') {
                ?>
                <div class="waa-message">
                    <button type="button" class="waa-close" data-dismiss="alert">&times;</button>
                    <strong><?php _e('Shipping options saved successfully', 'waa'); ?></strong>
                </div>
                <?php
            } elseif (isset($_GET['message']) && $_GET['message'] == 'shipping_not_saved') { ?>
                <div class="waa-alert waa-alert-danger">
                    <button type="button" class="waa-close" data-dismiss="alert">&times;</button>
                    <strong><?php _e('Bitte fülle alle mit einem Sternchen * markierten Felder aus.', 'waa'); ?></strong>
                </div>
            <?php } ?>

            <form method="post" id="shipping-form" action="" class="waa-form-horizontal">

                <?php wp_nonce_field('waa_shipping_form_field', 'waa_shipping_form_field_nonce'); ?>

                <?php
                do_action('waa_shipping_settings_form_top'); ?>
                <input type="hidden" name="dps_enable_shipping" value="yes">

                <div class="waa-form-group">
                    <label class="waa-w4 waa-control-label" for="dps_enable_shipping" style="margin-top:6px">
                        <?php _e('Selbstabholung', 'waa'); ?>
                        <span class="waa-tooltips-help tips"
                              title="<?php esc_attr_e('Biete deinen Kunden an, das Kunstwerk bei dir persönlich abzuholen.', 'waa'); ?>">
                            <i class="ui ui-question-circle"></i>
                        </span>
                    </label>

                    <div class="waa-w5 waa-text-left">
                        <div class="checkbox">
                            <label>
                                <input type="hidden" name="dps_enable_pickup" value="no">
                                <?php
                                function is_pickup_allowed($user_id)
                                {
                                    $profile_info = waa_get_store_info($user_id);

                                    if (isset($profile_info['address'])) {

                                        $address = $profile_info['address'];

                                        $street_1 = isset($address['street_1']) ? $address['street_1'] : '';
                                        $city = isset($address['city']) ? $address['city'] : '';
                                        $zip = isset($address['zip']) ? $address['zip'] : '';
                                        $country_code = isset($address['country']) ? $address['country'] : '';

                                        if (!empty($street_1) && !empty($city) && !empty($zip) && !empty($country_code)) {
                                            return true;
                                        }
                                    } else {
                                        return false;
                                    }
                                }

                                if (is_pickup_allowed($user_id)) : ?>
                                    <input type="checkbox" name="dps_enable_pickup"
                                           value="yes" <?php checked('yes', $dps_enable_pickup, true); ?>> <?php _e('Selbstabholung erlauben', 'waa'); ?>
                                <?php else: ?>
                                    <input type="checkbox" name=""
                                           value="" disabled>
                                    <s><?php _e('Selbstabholung erlauben', 'waa'); ?></s>
                                    <br>
                                    <small><?php printf(__('Du kannst diese Option nur wählen, wenn du in deinem <a href="%s">Profil</a> eine vollständige Adresse hinterlegt hast', 'waa'), waa_get_navigation_url('settings/store')); ?></small>
                                <?php endif; ?>
                            </label>
                        </div>
                    </div>
                </div>

                <hr/>
                <br>

                <div class="waa-shipping-wrapper">
                    <div class="waa-form-group waa-shipping-price waa-shipping-add-qty">
                        <label class="waa-w4 waa-control-label" for="dps_pt">
                            <?php _e('Processing Time', 'waa'); ?> *
                            <span class="waa-tooltips-help tips"
                                  title="<?php esc_attr_e('The time required before sending the product for delivery', 'waa'); ?>">
                                <i class="ui ui-question-circle"></i>
                            </span>
                        </label>

                        <div class="waa-w5 waa-text-left">
                            <select name="dps_pt" id="dps_pt" class="waa-form-control" required>
                                <?php foreach ($processing_time as $processing_key => $processing_value): ?>
                                    <option
                                        value="<?php echo $processing_key; ?>" <?php selected($dps_pt, $processing_key); ?>><?php echo $processing_value; ?></option>
                                <?php endforeach ?>
                            </select>
                            <!-- <input id="additional_qty" value="<?php echo $dps_pt; ?>" name="dps_pt" placeholder="0.00" class="waa-form-control" type="number" step="any" min="0"> -->
                        </div>
                    </div>

                    <div class="waa-form-group">
                        <label class="waa-w4 waa-control-label" for="_dps_ship_policy">
                            <?php _e('Shipping Policy', 'waa'); ?>
                            <span class="waa-tooltips-help tips"
                                  title="<?php _e('Write your terms, conditions and instructions about shipping', 'waa'); ?>">
                                <i class="ui ui-question-circle"></i>
                            </span>
                        </label>

                        <div class="waa-w6 waa-text-left">
                            <textarea name="dps_ship_policy" id=""
                                      class="waa-form-control"><?php echo $dps_shipping_policy; ?></textarea>
                        </div>
                    </div>

                    <div class="waa-form-group">
                        <label class="waa-w4 waa-control-label" for="_dps_refund_policy">
                            <?php _e('Refund Policy', 'waa'); ?>
                            <span class="waa-tooltips-help tips"
                                  title="<?php _e('Write your terms, conditions and instructions about refund', 'waa'); ?>">
                                <i class="ui ui-question-circle"></i>
                            </span>
                        </label>

                        <div class="waa-w6 waa-text-left">
                            <textarea name="dps_refund_policy" id=""
                                      class="waa-form-control"><?php echo $dps_refund_policy; ?></textarea>
                        </div>
                    </div>

                    <div class="waa-form-group">
                        <label class="waa-w4 waa-control-label" for="dps_form_location">
                            <?php _e('Ships from:', 'waa'); ?> *
                            <span class="waa-tooltips-help tips"
                                  title="<?php _e('The place you send the products for delivery. Most of the time it as store location', 'waa'); ?>">
                                <i class="ui ui-question-circle"></i>
                            </span>
                        </label>

                        <div class="waa-w5">
                            <select name="dps_form_location" class="waa-form-control" required>
                                <?php waa_country_dropdown($countries, $dps_form_location); ?>
                            </select>
                        </div>
                    </div>

                    <div class="waa-form-group">

                        <div class="waa-w12 dps-main-wrapper">
                            <div class="waa-shipping-location-wrapper">

                                <p class="waa-page-help"><?php _e('Add the countries you deliver your products to. You can specify states as well. If the shipping price is same except some countries/states, there is an option <strong>Everywhere Else</strong>, you can use that.', 'waa') ?></p>


                                <div class="dps-shipping-location-content">
                                    <table class="dps-shipping-table">
                                        <tbody>
                                        <tr class="dps-shipping-location">
                                            <td colspan="3" width="100%" class="text-center">
                                                <?php _e('Ship to', 'waa'); ?> *
                                                        <span class="waa-tooltips-help tips"
                                                              title="<?php _e('The country you ship to', 'waa'); ?>">
                                                    <i class="ui ui-question-circle"></i></span></label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-4 text-center">
                                                <div class="checkbox">
                                                    <label for="home_country">
                                                        <input type="checkbox" name="dps_country_to[]" value="home_country" id="home_country"
                                                               class="waa-form-control dps_country_selection"
                                                               id="home_country" <?php checked('yes', $dps_home_country, true); ?>>
                                                        <?= __('Heimatland', 'waa'); ?>
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="col-md-4">
                                                <div class="checkbox">
                                                    <label for="eu_countries">
                                                        <input type="checkbox" name="dps_country_to[]" value="eu_countries" id="eu_countries"
                                                               class="waa-form-control dps_country_selection" <?php checked('yes', $dps_eu_countries, true); ?>>
                                                        <?= __('EU', 'waa'); ?>
                                                    </label>
                                                </div>

                                            </td>
                                            <td class="col-md-4">
                                                <div class="checkbox">
                                                    <label for="switzerland">
                                                        <input type="checkbox" name="dps_country_to[]" value="switzerland" id="switzerland"
                                                               class="waa-form-control dps_country_selection" <?php checked('yes', $dps_switzerland, true); ?>>
                                                        <?= __('Schweiz', 'waa'); ?>
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                /**
                 * @since 2.2.2 Insert action after social settings form
                 */
                do_action('waa_shipping_settings_form_bottom'); ?>

                <div class="waa-form-group">

                    <div class="waa-w4 ajax_prev waa-text-left" style="margin-left:23%;">
                        <input type="submit" name="waa_update_shipping_options"
                               class="waa-btn waa-btn-danger waa-btn-theme"
                               value="<?php esc_attr_e('Save Settings', 'waa'); ?>">
                    </div>
                </div>

            </form>
        </article>
    </div><!-- .waa-dashboard-content -->
</div><!-- .waa-dashboard-wrap -->