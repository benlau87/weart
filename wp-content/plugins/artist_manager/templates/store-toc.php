<?php
/**
 * The Template for displaying all reviews.
 *
 * @package waa
 * @package waa - 2014 1.0
 */

$store_user = get_userdata( get_query_var( 'author' ) );
$store_info = waa_get_store_info( $store_user->ID );
$scheme       = is_ssl() ? 'https' : 'http';

wp_enqueue_script( 'google-maps', $scheme . '://maps.google.com/maps/api/js?sensor=true' );

get_header( 'shop' );
?>

<?php do_action( 'woocommerce_before_main_content' ); ?>

<?php if ( waa_get_option( 'enable_theme_store_sidebar', 'waa_general', 'off' ) == 'off' ) { ?>
    <div id="waa-secondary" class="waa-clearfix waa-w3 waa-store-sidebar" role="complementary" style="margin-right:3%;">
        <div class="waa-widget-area widget-collapse">
            <?php
            if ( ! dynamic_sidebar( 'sidebar-store' ) ) {

                $args = array(
                    'before_widget' => '<aside class="widget">',
                    'after_widget'  => '</aside>',
                    'before_title'  => '<h3 class="widget-title">',
                    'after_title'   => '</h3>',
                );

                if ( class_exists( 'waa_Store_Location' ) ) {
                    the_widget( 'waa_Store_Category_Menu', array( 'title' => __( 'Store Category', 'waa' ) ), $args );
                    if( waa_get_option( 'store_map', 'waa_general', 'on' ) == 'on' ) {
                        the_widget( 'waa_Store_Location', array( 'title' => __( 'Store Location', 'waa' ) ), $args );
                    }
                    if( waa_get_option( 'contact_seller', 'waa_general', 'on' ) == 'on' ) {
                        the_widget( 'waa_Store_Contact_Form', array( 'title' => __( 'Contact Seller', 'waa' ) ), $args );
                    }
                }

            }
            ?>

            <?php do_action( 'waa_sidebar_store_after', $store_user, $store_info ); ?>
        </div>
    </div><!-- #secondary .widget-area -->
<?php
} else {
    get_sidebar( 'store' );
}
?>

<div id="primary" class="content-area waa-single-store waa-w8">
    <div id="content" class="site-content store-review-wrap woocommerce" role="main">

        <?php waa_get_template_part( 'store-header' ); ?>

        <div id="store-toc-wrapper">
            <div id="store-toc">
                <?php
                if( isset( $store_info['store_tnc'] ) ):
                ?>
                    <h2 class="headline"><?php _e( 'Terms And Conditions', 'waa' ); ?></h2>
                    <div>
                        <?php
                        echo nl2br($store_info['store_tnc']);
                        ?>
                    </div>
                <?php
                endif;
                ?>
            </div><!-- #store-toc -->
        </div><!-- #store-toc-wrap -->

    </div><!-- #content .site-content -->
</div><!-- #primary .content-area -->

<?php do_action( 'woocommerce_after_main_content' ); ?>

<?php get_footer(); ?>