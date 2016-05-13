<?php
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
add_action('woocommerce_before_main_content', 'my_theme_wrapper_start', 10);
add_action('woocommerce_after_main_content', 'my_theme_wrapper_end', 10);

function my_theme_wrapper_start()
{
    echo '<section id="content" role="main">';
}

function my_theme_wrapper_end()
{
    echo '</section>';
}

add_action('after_setup_theme', 'woocommerce_support');
function woocommerce_support()
{
    add_theme_support('woocommerce');
}

/**
 * @param $price
 * @return string
 */
function format_currency_price($price)
{
    $price = number_format($price, 2, ',', ',');
    $currency_pos = get_option('woocommerce_currency_pos');
    $currency = get_woocommerce_currency_symbol();
    switch ($currency_pos) {
        case 'left' :
            $format = $currency . $price;
            break;
        case 'right' :
            $format = $price . $currency;
            break;
        case 'left_space' :
            $format = $currency . '&nbsp;' . $price;
            break;
        case 'right_space' :
            $format = $price . '&nbsp;' . $currency;
            break;
    }

    return $format;
}

add_action('woocommerce_after_shop_loop_item_title', 'cj_show_dimensions', 9);
function cj_show_dimensions()
{
    global $product;
    $dimensions = $product->get_dimensions();
    $dimensions = explode(' x ', $dimensions);
    $dimension_h = explode(' ', $dimensions[1], 2);
    if (is_array($dimensions)) {
        echo '<span class="dimensions">';
        echo $dimensions[0] . ' B x ' . $dimension_h[0] . ' H ' . $dimension_h[1];
        echo '</span>';
    }
}

function woocommerce_product_loop_tags()
{
    global $post, $product;

    $tag_count = sizeof(get_the_terms($post->ID, 'product_tag'));

    echo $product->get_tags(', ', '<span class="tagged_as">' . _n('', '', $tag_count, 'woocommerce') . ' ', '</span>');
}

/**
 * @param $register
 * @param string $name
 * @return bool
 */
function wc_reg_for_menus($register, $name = '')
{
    if ($name == 'pa_stadt') $register = true;
    return $register;
}

add_filter('woocommerce_attribute_show_in_nav_menus', 'wc_reg_for_menus', 1, 2);

/**
 * Removes Product Successfully Added to Cart
 * @param $message
 * @return string
 */
function custom_add_to_cart_message($message)
{
    return '';
}

add_filter('woocommerce_add_to_cart_message', 'custom_add_to_cart_message');
add_filter('_add_to_cart_message', 'custom_add_to_cart_message');

/**
 * get all (visible) variation prices for a product
 * @param $product
 * @param bool|false $count
 * @return int|string
 */
function waa_get_variation_prices($product, $count = false)
{
    global $woocommerce;
    $variation_ids = $product->children['visible'];
    $i = 0;
    if(is_array($variation_ids)) {
        foreach ($variation_ids as $variation) {
            $product_variation = new WC_Product_Variation($variation);
            $variation_id[$i] = $variation;
            $regular_price[$i] = $product_variation->regular_price;
            $variation_name[$i] = get_post_meta($variation_id[$i], 'attribute_pa_print_groesse', true);
            if ($variation_name[$i] != 'original')
                $output = '<input type="hidden" id="variation_price_' . $variation_id[$i] . '" value="' . $regular_price[$i] . '" name="' . $variation_id[$i] . '" />';
            $i++;
        }
    }
    if ($count)
        return count($variation_ids);
    else
        return $output;
}

/**
 * @param $product_id
 * @return array|mixed
 */
function waa_get_max_variation_price($product_id)
{
    global $wpdb;
    $sql = "SELECT
								ID
							FROM 
								{$wpdb->prefix}posts
							WHERE
								post_parent = {$product_id}";

    $data = $wpdb->get_results($sql);

    $output = '';
    foreach ($data as $variation) {
        $variation_price = get_post_meta($variation->ID, '_regular_price', true);
        $variation_name = get_post_meta($variation->ID, 'attribute_pa_print_groesse', true);

        if ($variation_name != 'original') {
            $output[] = $variation_price;
        }
    }

    $output = (is_array($output) ? max($output) : $output);

    return $output;
}

/**
 * @param $product_id
 * @return string
 */
function waa_get_variable_price($product_id)
{
    $woocs = new WOOCS();
    $currencies = $woocs->get_currencies();
    $_create_variation = get_post_meta($product_id, '_create_variation', true);
    $_min_variation_price = get_post_meta($product_id, '_min_variation_price', true);
    $_max_variation_price = waa_get_max_variation_price($product_id);

    if ($_create_variation == 'no' || $_min_variation_price == $_max_variation_price) {
        $_min_variation_price = $_min_variation_price ? '_min_variation_price' : '_regular_price';
        $output = waa_get_woocs_price($product_id, $_min_variation_price);
    } else {
        $output = __('ab', 'waa') . ' ' . number_format((get_post_meta($product_id, '_min_variation_price', true) / $currencies[$woocs->current_currency]['rate']), 2, ',', '.') . ' ' . get_woocommerce_currency_symbol();
    }
    return $output;
}

/**
 * @param $product_id
 * @return string
 */
function waa_get_variable_price_html($product_id)
{
    $woocs = new WOOCS();
    $currencies = $woocs->get_currencies();
    return '<p class="price"><span class="amount">' . waa_get_variable_price($product_id) . '</span> <small class="woocommerce-price-suffix">' . __('inkl. MwSt.', 'waa') . ' <a href="" class="waa-tooltips-help tips" data-html="true" data-original-title="Versandkosten abhängig Größen- und Material-Auswahl">zzgl. Versand</a></small></p>';
}

/**
 * @param $product_id
 * @param $key
 * @return string
 */
function waa_get_woocs_int_price($price, $currency)
{
    $woocs = new WOOCS();

    if ($currency != '€') {
        return $woocs->woocs_exchange_value($price);
    } else {
        return $price;
    }
}

/**
 * @param $product_id
 * @param $key
 * @return string
 */
function waa_get_woocs_int_price_reverse($value, $reverse = false)
{
    $woocs = new WOOCS();
    $currencies = $woocs->get_currencies();

    if($reverse)
        return number_format(($value / $currencies[$woocs->current_currency]['rate']), 2, '.', ',');
    else
        return number_format(($value / $currencies[$woocs->current_currency]['rate']), 2, ',', '.');
}

/**
 * @param $product_id
 * @param $key
 * @return string
 */
function waa_get_woocs_price($product_id, $key)
{
    $woocs = new WOOCS();
    $currencies = $woocs->get_currencies();
    return number_format((get_post_meta($product_id, $key, true) / $currencies[$woocs->current_currency]['rate']), 2, ',', '.') . ' ' . get_woocommerce_currency_symbol();
}

/**
 * @param $product_id
 * @param $key
 * @return string
 */
function waa_get_woocs_price_html($product_id, $key)
{
    $woocs = new WOOCS();
    $currencies = $woocs->get_currencies();
    #print_r(get_post_meta($product_id, '_additional_price'));

    $out = '<p class="price"><span class="amount">' . number_format((get_post_meta($product_id, $key, true) / $currencies[$woocs->current_currency]['rate']), 2, ',', '.') . ' ' . get_woocommerce_currency_symbol() . '</span> <small class="woocommerce-price-suffix">' . __('inkl. MwSt.', 'waa') . ' <a href="" class="waa-tooltips-help tips"     data-html="true" data-original-title="' . waa_get_shipping_costs($product_id) . '">zzgl. Versand</a></small></p>';

    #' . number_format((get_post_meta($product_id, '_additional_price', true) / $currencies[$woocs->current_currency]['rate']), 2, ',', '.') . ' ' . get_woocommerce_currency_symbol() . '
    return $out;
}

/**
 * @param $fields
 * @return mixed
 */
function custom_override_checkout_fields($fields)
{
    # unset($fields['order']['order_comments']);
    unset($fields['billing']['billing_address_2']);
    unset($fields['billing']['billing_phone']);
    unset($fields['shipping']['shipping_address_2']);

    return $fields;
}

add_filter('woocommerce_checkout_fields', 'custom_override_checkout_fields');

function waa_get_shipping_costs($product_id)
{
    $woocs = new WOOCS();
    $currencies = $woocs->get_currencies();
    $shipping_costs = get_post_meta($product_id, '_additional_price', true);
    $out = '<ul>';
    if (is_array($shipping_costs)) {
        foreach ($shipping_costs as $country => $costs) {
            $out .= '<li>';
            if ($country == 'DE') {
                $out .= __('Deutschland', 'waa') . ': ' . number_format(($costs / $currencies[$woocs->current_currency]['rate']), 2, ',', '.') . ' ' . get_woocommerce_currency_symbol();
            } elseif ($country == 'CH') {
                $out .= __('Schweiz', 'waa') . ': ' . number_format(($costs / $currencies[$woocs->current_currency]['rate']), 2, ',', '.') . ' ' . get_woocommerce_currency_symbol();
            } elseif ($country == 'everywhere') {
                $out .= __('EU', 'waa') . ': ' . number_format(($costs / $currencies[$woocs->current_currency]['rate']), 2, ',', '.') . ' ' . get_woocommerce_currency_symbol();
            } elseif ($country == 'AT') {
                $out .= __('Österreich', 'waa') . ': ' . number_format(($costs / $currencies[$woocs->current_currency]['rate']), 2, ',', '.') . ' ' . get_woocommerce_currency_symbol();
            }
            $out .= '</li>';
        }
    } else {
        $out .= '<li>keine Versandkosten angegeben</li>';
    }
    $out .= '</ul>';
    return $out;
}

/**
 * @param $variable_product_id
 * @return string
 */
function waa_get_variable_shipping_costs($variable_product_id)
{
    $shipping_costs = get_post_meta($variable_product_id, '_additional_price', true);
    $out = '<ul>';
    if (is_array($shipping_costs)) {
        foreach ($shipping_costs as $country => $costs) {
            $out .= '<li>';
            if ($country == 'DE') {
                $out .= __('Deutschland', 'waa') . ': ' . number_format($costs, 2, ',', '.') . ' ' . get_woocommerce_currency_symbol();
            } elseif ($country == 'CH') {
                $out .= __('Schweiz', 'waa') . ': ' . number_format($costs, 2, ',', '.') . ' ' . get_woocommerce_currency_symbol();
            } elseif ($country == 'everywhere') {
                $out .= __('EU Ausland', 'waa') . ': ' . number_format($costs, 2, ',', '.') . ' ' . get_woocommerce_currency_symbol();
            }
            $out .= '</li>';
        }
    } else {
        $out .= '<li>keine Versandkosten angegeben</li>';
    }
    $out .= '</ul>';
    return $out;
}

/**
 * @param $type
 * @param $id
 * @return string the "from location" - where the artist ships from
 */
function waa_get_artist_location($id)
{
    $user_id = get_post_field('post_author', $id);
    $location = get_user_meta($user_id, '_dps_form_location', true);
    return $location;
}

/**
 * @param $id
 * @return mixed
 */
function waa_get_artist_by_product($id)
{
    $user_id = get_post_field('post_author', $id);
    return $user_id;
}

/**
 * @param $type
 * @param $id
 * @return array of countries  the artist ships to
 */
function waa_get_artist_delivery_locations($type, $id)
{
    if ($type == 'variation') {
        $user_id = get_post_field('post_author', $id);
        $locations = get_user_meta($user_id, '_dps_country_rates', true);
    }
    if ($type == 'original') {
        $locations = get_post_meta($id, '_additional_price', true);
    }
    return $locations;
}

/**
 * @param $order
 * @return string shipping method id/slug
 */
function waa_get_shipping_method($order)
{
    $shipping_details = $order->get_items('shipping');
    $shipping_method = reset($shipping_details)['method_id'];
    return $shipping_method;
}

/**
 * @param $order
 * @param bool|false $formatted
 * @return string
 */
function waa_get_artist_pickup_details($order, $formatted = true)
{
    $items = $order->get_items();
    $i=0;
    $artist_infos = array();
    foreach ($items as $item) {
        $product_id = !empty($item['product_id']) ? $item['product_id'] : $item['variation_id'];
        $artist_id = get_post_field('post_author', $product_id);
        $artist_info[$i] = waa_get_store_info($artist_id);
        $user_info[$i] = get_userdata($artist_id);
        $artist_infos = array_merge($artist_info, $user_info);
    }
    if ($formatted) {
        foreach ($artist_infos as $artist_info) {
            $first_name = $artist_info[1]->first_name;
            $last_name = $artist_info[1]->last_name;
            $mail = $artist_info[1]->user_email;
            $out = '<strong>' . $first_name . ' ' . $last_name . '</strong> (' . __('Künstlername', 'waa') . ': ' . $artist_info[0]['store_name'] . ')<br>';
            $out .= $artist_info[0]['address']['street_1'] . '<br>';
            $out .= $artist_info[0]['address']['zip'] . ' ' . $artist_info[0]['address']['city'] . '<br>';
            $out .= WC()->countries->countries[$artist_info[0]['address']['country']] . '<br>';
            $out .= 'E-Mail-Adresse: ' . $mail . '<br>';
            $artist_info[0]['phone'] != '' ? $out .= 'Telefon: ' . $artist_info[0]['phone'] . '<br>' : $out .= '';
            return $out;
        }
    } else {
        return $artist_infos;
    }
}

/**
 * @param $artist_ids
 * @return bool
 */
function cart_allows_pickup($artist_ids)
{
    foreach ($artist_ids as $artist_id) {
        $cart_allows_pickup = artist_has_pickup($artist_id);
        if(!$cart_allows_pickup)
            return false;
    }
    return true;
}

/**
 * Notify Admin when new user registers
 * @param $user_login
 */
function new_customer_registered_send_email_admin($user_login) {
    ob_start();
    do_action('woocommerce_email_header', 'Neue Kunde registriert');
    $email_header = ob_get_clean();
    ob_start();
    do_action('woocommerce_email_footer');
    $email_footer = ob_get_clean();

    woocommerce_mail(
        get_bloginfo('admin_email'),
        get_bloginfo('name').' - Neue Kunde registriert',
        $email_header.'<p>Der Nutzer '.esc_html( $user_login ).' hat sich auf weare-art.com registriert.</p>'.$email_footer
    );
}
add_action('new_customer_registered', 'new_customer_registered_send_email_admin');

/**
* Return the permalink of the shop page for the continue shopping redirect filter
*
 * @param  string $return_to
* @return string
*/
function my_woocommerce_continue_shopping_redirect( $return_to ) {
    return get_permalink( wc_get_page_id( 'shop' ) );
}
add_filter( 'woocommerce_continue_shopping_redirect', 'my_woocommerce_continue_shopping_redirect', 20 );

function waa_add_terms_to_registration(){
if ( wc_get_page_id( 'terms' ) > 0 && apply_filters( 'woocommerce_checkout_show_terms', true ) ) : ?>
    <p class="form-row terms">
        <label for="terms">
            <input type="checkbox" class="input-checkbox" name="terms" <?php checked( apply_filters( 'woocommerce_terms_is_checked_default', isset( $_POST['terms'] ) ), true ); ?> id="terms" />
            <?php printf( __( 'I&rsquo;ve read and accept the <a href="%s" target="_blank">terms &amp; conditions</a>', 'woocommerce' ), esc_url( wc_get_page_permalink( 'terms' ) ) ); ?>
        </label>
    </p>
<?php endif;
}
add_action( 'register_form', 'waa_add_terms_to_registration', 80 );

function waa_terms_validation_registration( $errors, $username, $password, $email ){
    if ( empty( $_POST['terms'] ) ) {
        throw new Exception( __( 'You must accept our Terms &amp; Conditions.', 'woocommerce' ) );
    }
    return $errors;
}
add_action( 'woocommerce_process_registration_errors', 'waa_terms_validation_registration', 10, 4 );


?>