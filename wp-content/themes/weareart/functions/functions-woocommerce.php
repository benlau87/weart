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

add_filter('woocommerce_attribute_show_in_nav_menus', 'wc_reg_for_menus', 1, 2);

function wc_reg_for_menus( $register, $name = '' ) {
     if ( $name == 'pa_stadt' ) $register = true;
     return $register;
}



?>