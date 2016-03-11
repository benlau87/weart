<?php
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
add_action('woocommerce_before_main_content', 'my_theme_wrapper_start', 10);
add_action('woocommerce_after_main_content', 'my_theme_wrapper_end', 10);

function my_theme_wrapper_start() {
  echo '<section id="content" role="main">';
}

function my_theme_wrapper_end() {
  echo '</section>';
}
add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
    add_theme_support( 'woocommerce' );
}

/**
 * @param $price
 * @return string
 */
function format_currency_price($price) {
    $price = number_format($price, 2, ',', ',');
    $currency_pos = get_option( 'woocommerce_currency_pos');
    $currency = get_woocommerce_currency_symbol();
    switch ( $currency_pos ) {
         case 'left' :
             $format = $currency.$price;
         break;
         case 'right' :
             $format = $price.$currency;
         break;
         case 'left_space' :
             $format = $currency.'&nbsp;'.$price;
         break;
         case 'right_space' :
             $format = $price.'&nbsp;'.$currency;
         break;
     }

     return $format;
}

add_action( 'woocommerce_after_shop_loop_item_title', 'cj_show_dimensions', 9 );
function cj_show_dimensions() {
	global $product;
	$dimensions = $product->get_dimensions();
	$dimensions = explode( ' x ', $dimensions );
	$dimension_h = explode(' ', $dimensions[1], 2);
  if ( is_array($dimensions ) ) {
		echo '<span class="dimensions">';
		echo $dimensions[0] . ' B x ' . $dimension_h[0] . ' H ' . $dimension_h[1];
		echo '</span>';
	}
}

function woocommerce_product_loop_tags() {
    global $post, $product;

    $tag_count = sizeof( get_the_terms( $post->ID, 'product_tag' ) );

    echo $product->get_tags( ', ', '<span class="tagged_as">' . _n( '', '', $tag_count, 'woocommerce' ) . ' ', '</span>' );
}

/**
 * @param $register
 * @param string $name
 * @return bool
 */
function wc_reg_for_menus( $register, $name = '' ) {
     if ( $name == 'pa_stadt' ) $register = true;
     return $register;
}
add_filter('woocommerce_attribute_show_in_nav_menus', 'wc_reg_for_menus', 1, 2);

/**
 * Removes Product Successfully Added to Cart
 * @param $message
 * @return string
 */
function custom_add_to_cart_message( $message  ){
	return '';
}
add_filter( 'woocommerce_add_to_cart_message', 'custom_add_to_cart_message' );
add_filter( '_add_to_cart_message', 'custom_add_to_cart_message' );

/**
 * get all (visible) variation prices for a product
 * @param $product
 * @param bool|false $count
 * @return int|string
 */
function waa_get_variation_prices($product, $count = false) {
	global $woocommerce;
	$variation_ids = $product->children['visible'];
	$i = 0;
	foreach ($variation_ids as $variation) {
		$product_variation = new WC_Product_Variation($variation);
		$variation_id[$i] = $variation;
		$regular_price[$i] = $product_variation->regular_price;
		$variation_name[$i] = get_post_meta($variation_id[$i], 'attribute_pa_print_groesse', true);
		if ( $variation_name[$i] != 'original')
			$output .= '<input type="hidden" id="variation_price_'.$variation_id[$i].'" value="'.$regular_price[$i].'" name="'.$variation_id[$i].'" />';
		$i++;
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
function waa_get_max_variation_price($product_id) {
	global $wpdb;
	$sql = "SELECT
								ID
							FROM 
								{$wpdb->prefix}posts
							WHERE
								post_parent = {$product_id}";

	$data = $wpdb->get_results( $sql );

    $output = '';
	foreach($data as $variation) {
		$variation_price = get_post_meta($variation->ID, '_regular_price', true);
		$variation_name = get_post_meta($variation->ID, 'attribute_pa_print_groesse', true);

		if($variation_name != 'original') {
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
function waa_get_variable_price($product_id) {
    $woocs = new WOOCS();
    $currencies = $woocs->get_currencies();
    $_create_variation = get_post_meta($product_id, '_create_variation', true);
    $_min_variation_price = get_post_meta($product_id, '_min_variation_price', true);
    $_max_variation_price = waa_get_max_variation_price($product_id);

    if($_create_variation == 'no' || $_min_variation_price == $_max_variation_price) {
        $_min_variation_price = $_min_variation_price 	? '_min_variation_price' : '_regular_price';
        $output = waa_get_woocs_price($product_id, $_min_variation_price);
    } else {
        $output = __('ab', 'waa').' '.number_format((get_post_meta($product_id, '_min_variation_price', true) / $currencies[$woocs->current_currency]['rate']), 2, ',', '.') . ' ' . get_woocommerce_currency_symbol();
    }
    return $output;
}

/**
 * @param $product_id
 * @return string
 */
function waa_get_variable_price_html($product_id) {
    $woocs = new WOOCS();
    $currencies = $woocs->get_currencies();
    return  '<p class="price"><span class="amount">'.waa_get_variable_price($product_id) . '</span> <small class="woocommerce-price-suffix">' . __('inkl. MwSt.', 'waa') . ' <a href="" class="waa-tooltips-help tips" data-html="true" data-original-title="'.waa_get_shipping_costs($product_id).'">zzgl. Versand</a></small></p>';
}

/**
 * @param $product_id
 * @param $key
 * @return string
 */
function waa_get_woocs_int_price($price, $currency) {
    $woocs = new WOOCS();

    if($currency != '€') {
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
function waa_get_woocs_int_price_reverse($value) {
    $woocs = new WOOCS();
    $currencies = $woocs->get_currencies();

    return number_format(($value / $currencies[$woocs->current_currency]['rate']), 2, '.', ',');
}

/**
 * @param $product_id
 * @param $key
 * @return string
 */
function waa_get_woocs_price($product_id, $key) {
    $woocs = new WOOCS();
    $currencies = $woocs->get_currencies();
    return number_format((get_post_meta($product_id, $key, true) / $currencies[$woocs->current_currency]['rate']), 2, ',', '.') . ' ' . get_woocommerce_currency_symbol();
}

/**
 * @param $product_id
 * @param $key
 * @return string
 */
function waa_get_woocs_price_html($product_id, $key) {
    $woocs = new WOOCS();
    $currencies = $woocs->get_currencies();
    #print_r(get_post_meta($product_id, '_additional_price'));

    $out = '<p class="price"><span class="amount">'.number_format((get_post_meta($product_id, $key, true) / $currencies[$woocs->current_currency]['rate']), 2, ',', '.') . ' ' . get_woocommerce_currency_symbol() . '</span> <small class="woocommerce-price-suffix">' . __('inkl. MwSt.', 'waa') . ' <a href="" class="waa-tooltips-help tips"     data-html="true" data-original-title="'.waa_get_shipping_costs($product_id).'">zzgl. Versand</a></small></p>';

    #' . number_format((get_post_meta($product_id, '_additional_price', true) / $currencies[$woocs->current_currency]['rate']), 2, ',', '.') . ' ' . get_woocommerce_currency_symbol() . '
    return $out;
}

/**
 * @param $fields
 * @return mixed
 */
function custom_override_checkout_fields( $fields ) {
    # unset($fields['order']['order_comments']);
     unset($fields['billing']['billing_address_2']);
     unset($fields['billing']['billing_phone']);
     unset($fields['shipping']['shipping_address_2']);

     return $fields;
}
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );

function waa_get_shipping_costs($product_id) {
    $shipping_costs = get_post_meta($product_id, '_additional_price', true);
    $out = '<ul>';
    if (is_array($shipping_costs)) {
        foreach ($shipping_costs as $country => $costs) {
            $out .= '<li>';
            if ($country == 'DE') {
                $out .= __('Deutschland', 'waa') . ': ' . number_format($costs, 2, ',', '.') . ' ' .get_woocommerce_currency_symbol();
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
?>