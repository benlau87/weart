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


?>