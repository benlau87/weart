<?php

/**
 * Get default withdraw methods
 *
 * @return array
 */
function waa_withdraw_register_methods() {
    $methods = array(
        'paypal' => array(
            'title'    =>  __( 'PayPal', 'waa' ),
            'callback' => 'waa_withdraw_method_paypal'
        ),
        'bank' => array(
            'title'    => __( 'Bank Transfer', 'waa' ),
            'callback' => 'waa_withdraw_method_bank'
        ),
    );

    return apply_filters( 'waa_withdraw_methods', $methods );
}

/**
 * Get registered withdraw methods suitable for Settings Api
 *
 * @return array
 */
function waa_withdraw_get_methods() {
    $methods = array();
    $registered = waa_withdraw_register_methods();

    foreach ($registered as $key => $value) {
        $methods[$key] = $value['title'];
    }

    return $methods;
}

/**
 * Get active withdraw methods.
 *
 * Default is paypal
 *
 * @return array
 */
function waa_withdraw_get_active_methods() {
    $methods = waa_get_option( 'withdraw_methods', 'waa_selling', array( 'paypal' ) );

    return $methods;
}

/**
 * Get a single withdraw method based on key
 *
 * @param string $method_key
 * @return boolean|array
 */
function waa_withdraw_get_method( $method_key ) {
    $methods = waa_withdraw_register_methods();

    if ( isset( $methods[$method_key] ) ) {
        return $methods[$method_key];
    }

    return false;
}

/**
 * Get title from a withdraw method
 *
 * @param string $method_key
 * @return string
 */
function waa_withdraw_get_method_title( $method_key ) {
    $registered = waa_withdraw_register_methods();

    if ( isset( $registered[$method_key]) ) {
        return $registered[$method_key]['title'];
    }

    return '';
}

/**
 * Callback for PayPal in store settings
 *
 * @global WP_User $current_user
 * @param array $store_settings
 */
function waa_withdraw_method_paypal( $store_settings ) {
    global $current_user;

    $email = isset( $store_settings['payment']['paypal']['email'] ) ? esc_attr( $store_settings['payment']['paypal']['email'] ) : $current_user->user_email ;
    ?>
    <div class="waa-form-group">
        <div class="waa-w8">
            <div class="waa-input-group">
                <span class="waa-input-group-addon"><?php _e( 'E-mail', 'waa' ); ?></span>
                <input value="<?php echo $email; ?>" name="settings[paypal][email]" class="waa-form-control email" placeholder="you@domain.com" type="text">
            </div>
        </div>
    </div>
    <?php
}

/**
 * Callback for Bank in store settings
 *
 * @global WP_User $current_user
 * @param array $store_settings
 */
function waa_withdraw_method_bank( $store_settings ) {
    $account_name   = isset( $store_settings['payment']['bank']['ac_name'] ) ? esc_attr( $store_settings['payment']['bank']['ac_name'] ) : '';
    $account_iban = isset( $store_settings['payment']['bank']['ac_iban'] ) ? esc_attr( $store_settings['payment']['bank']['ac_iban'] ) : '';
    $account_bic     = isset( $store_settings['payment']['bank']['ac_bic'] ) ? esc_attr( $store_settings['payment']['bank']['ac_bic'] ) : '';
    $bank_name      = isset( $store_settings['payment']['bank']['bank_name'] ) ? esc_attr( $store_settings['payment']['bank']['bank_name'] ) : '';
    ?>
    <div class="waa-form-group">
        <div class="doakn-w8">
            <input name="settings[bank][ac_name]" value="<?php echo $account_name; ?>" class="waa-form-control" placeholder="<?php esc_attr_e( 'Your bank account name', 'waa' ); ?>" type="text">
        </div>
    </div>

    <div class="waa-form-group">
        <div class="doakn-w8">
            <input name="settings[bank][ac_iban]" value="<?php echo $account_iban; ?>" class="waa-form-control" placeholder="<?php esc_attr_e( 'Your bank account number', 'waa' ); ?>" type="text">
        </div>
    </div>
		
		    <div class="waa-form-group">
        <div class="doakn-w8">
            <input value="<?php echo $account_bic; ?>" name="settings[bank][ac_bic]" class="waa-form-control" placeholder="<?php esc_attr_e( 'Swift code', 'waa' ); ?>" type="text">
        </div>
    </div> <!-- .waa-form-group -->

    <div class="waa-form-group">
        <div class="doakn-w8">
            <input name="settings[bank][bank_name]" value="<?php echo $bank_name; ?>" class="waa-form-control" placeholder="<?php _e( 'Name of bank', 'waa' ) ?>" type="text">
        </div>
    </div>
    <?php
}

/**
 * Get withdraw counts, used in admin area
 *
 * @global WPDB $wpdb
 * @return array
 */
function waa_get_withdraw_count() {
    global $wpdb;

    $cache_key = 'waa_withdraw_count';
    $counts = wp_cache_get( $cache_key );

    if ( false === $counts ) {

        $counts = array( 'pending' => 0, 'completed' => 0, 'cancelled' => 0 );
        $sql = "SELECT COUNT(id) as count, status FROM {$wpdb->waa_withdraw} GROUP BY status";
        $result = $wpdb->get_results( $sql );

        if ( $result ) {
            foreach ($result as $row) {
                if ( $row->status == '0' ) {
                    $counts['pending'] = (int) $row->count;
                } elseif ( $row->status == '1' ) {
                    $counts['completed'] = (int) $row->count;
                } elseif ( $row->status == '2' ) {
                    $counts['cancelled'] = (int) $row->count;
                }
            }
        }
    }

    return $counts;
}

/**
 * Get active withdraw order status.
 *
 * Default is 'completed', 'processing', 'on-hold'
 *
 */
function waa_withdraw_get_active_order_status() {
    $order_status = waa_get_option( 'withdraw_order_status', 'waa_selling', array( 'wc-completed' ) );

    return apply_filters( 'waa_withdraw_active_status', $order_status );
}

/**
 * get comma seperated value from "waa_withdraw_get_active_order_status()" return array
 * @param array array
 */
function waa_withdraw_get_active_order_status_in_comma() {
    $order_status = waa_withdraw_get_active_order_status();
    $status = "'" . implode("', '", $order_status ) . "'";
    return $status;
}
