<?php

/**
 * Pageviews - for counting product post views.
 */
class waa_Pageviews {

    private $meta_key = 'pageview';

    public function __construct() {
        /* Registers the entry views extension scripts if we're on the correct page. */
        add_action( 'template_redirect', array($this, 'load_views'), 25 );

        /* Add the entry views AJAX actions to the appropriate hooks. */
        add_action( 'wp_ajax_waa_pageview', array($this, 'update_ajax') );
        add_action( 'wp_ajax_nopriv_waa_pageview', array($this, 'update_ajax') );
    }

    function load_scripts() {

        $nonce = wp_create_nonce( 'waa_pageview' );

        echo '<script type="text/javascript">/* <![CDATA[ */ jQuery(document).ready( function($) { $.post( "' . admin_url( 'admin-ajax.php' ) . '", { action : "waa_pageview", _ajax_nonce : "' . $nonce . '", post_id : ' . get_the_ID() . ' } ); } ); /* ]]> */</script>' . "\n";
    }

    function load_views() {

        if ( is_singular( 'product' ) ) {
            global $post;

            if ( empty( $_COOKIE['waa_product_viewed'] ) ) {
                $waa_viewed_products = array();
            }
            else {
                $waa_viewed_products = (array) explode( ',', $_COOKIE['waa_product_viewed'] );
            }

            if ( ! in_array( $post->ID, $waa_viewed_products ) ) {
                $waa_viewed_products[] = $post->ID;

                wp_enqueue_script( 'jquery' );

                add_action( 'wp_footer', array($this, 'load_scripts') );
            }

            // Store for single product view
            setcookie( 'waa_product_viewed', implode( ',', $waa_viewed_products ) );
        }
    }

    function update_view( $post_id = '' ) {

        if ( !empty( $post_id ) ) {

            $old_views = get_post_meta( $post_id, $this->meta_key, true );
            $new_views = absint( $old_views ) + 1;

            update_post_meta( $post_id, $this->meta_key, $new_views, $old_views );
        }
    }

    function update_ajax() {

        check_ajax_referer( 'waa_pageview' );

        if ( isset( $_POST['post_id'] ) ) {
            $post_id = absint( $_POST['post_id'] );
        }

        if ( !empty( $post_id ) ) {
            $this->update_view( $post_id );
        }

        exit;
    }
}