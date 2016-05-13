<?php

/**
 *  waa regular Shipping Class
 *
 *  Register WooCommerce gateway as
 *  waa Shipping
 *
 *  @author WAA <info@WAA.com>
 */
class waa_WC_Shipping extends WC_Shipping_Method {
    /**
     * Constructor for your shipping class
     *
     * @access public
     * @return void
     */
    public function __construct() {
        $this->id                 = 'waa_product_shipping';
        $this->method_title       = __( 'waa Shipping' );
        $this->method_description = __( 'Enable sellers to set shipping cost per product and per country' );

        $this->enabled      = $this->get_option( 'enabled' );
        $this->title        = $this->get_option( 'title' );
        $this->tax_status   = $this->get_option( 'tax_status' );

        $this->init();
    }

    /**
     * Init your settings
     *
     * @access public
     * @return void
     */
    function init() {
        // Load the settings API
        $this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
        $this->init_settings(); // This is part of the settings API. Loads settings you previously init.


        // Save settings in admin if you have any defined
        add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
    }


    /**
     * Initialise Gateway Settings Form Fields
     *
     * @access public
     * @return void
     */
    function init_form_fields() {

        $this->form_fields = array(
            'enabled' => array(
                'title'         => __( 'Enable/Disable', 'waa' ),
                'type'          => 'checkbox',
                'label'         => __( 'Enable Shipping', 'waa' ),
                'default'       => 'yes'
            ),
            'title' => array(
                'title'         => __( 'Method Title', 'waa' ),
                'type'          => 'text',
                'description'   => __( 'This controls the title which the user sees during checkout.', 'waa' ),
                'default'       => __( 'Regular Shipping', 'waa' ),
                'desc_tip'      => true,
            ),
            'tax_status' => array(
                'title'         => __( 'Tax Status', 'woocommerce' ),
                'type'          => 'select',
                'default'       => 'taxable',
                'options'       => array(
                    'taxable'   => __( 'Taxable', 'woocommerce' ),
                    'none'      => _x( 'None', 'Tax status', 'woocommerce' )
                ),
            ),

        );

    }

    /**
     * calculate_shipping function.
     *
     * @access public
     * @param mixed $package
     * @return void
     */
    public function calculate_shipping( $package ) {
        $products = $package['contents'];
        $destination_country = isset( $package['destination']['country'] ) ? $package['destination']['country'] : '';
        $destination_state = isset( $package['destination']['state'] ) ? $package['destination']['state'] : '';

        $amount = 0.0;

        if ( $products ) {
            $amount = $this->calculate_per_seller( $products, $destination_country, $destination_state );
        }

        $tax_rate = ( $this->tax_status == 'none' ) ? false : '';

        $rate = array(
            'id'    => $this->id,
            'label' => $this->title,
            'cost'  => $amount,
            'taxes' => $tax_rate
        );

        // Register the rate
        $this->add_rate( $rate );
    }

    /**
     * Check if shipping for this product is enabled
     *
     * @param  int  $product_id
     * @return boolean
     */
    public static function is_product_disable_shipping( $product_id ) {
        $enabled = get_post_meta( $product_id, '_disable_shipping', true );

        if ( $enabled == 'yes' ) {
            return true;
        }

        return false;
    }

    /**
     * Check if shipping for this product is enabled
     *
     * @param  int  $product_id
     * @return boolean
     */
    public static function is_shipping_enabled_for_seller( $seller_id ) {
        $enabled = get_user_meta( $seller_id, '_dps_shipping_enable', true );

        if ( $enabled == 'yes' ) {
            return true;
        }

        return false;
    }


    /**
     * Get product shipping costs
     *
     * @param  int $product_id
     * @return array
     */
    public static function get_seller_country_shipping_costs( $seller_id ) {
        $country_cost = get_user_meta( $seller_id, '_dps_country_rates', true );
        $country_cost = is_array( $country_cost ) ? $country_cost : array();

        return $country_cost;
    }


    /**
     * Calculate shipping per seller
     *
     * @param  array $products
     * @param  array $destination
     * @return float
     */
    public function calculate_per_seller( $products, $destination_country, $destination_state  ) {
        $amount = 0.0;
        $price = array();

        $seller_products = array();

        foreach ($products as $product) {
            $seller_id                     = get_post_field( 'post_author', $product['product_id'] );
            $seller_products[$seller_id][] = $product;
        }

        if ( $seller_products ) {

            foreach ( $seller_products as $seller_id => $products ) {

                if( !self::is_shipping_enabled_for_seller( $seller_id ) ) {
                    continue;
                }

                foreach ( $products as $product ) {

                    if ( self::is_product_disable_shipping( $product['product_id'] ) ) {
                        continue 2;
                    }

                    $is_virtual      = get_post_meta( $product['product_id'], '_virtual', true );
                    $is_downloadable = get_post_meta( $product['product_id'], '_downloadable', true );

                    if ( ( $is_virtual == 'yes' ) || ( $is_downloadable == 'yes' ) ) {
                        continue 2;
                    }

                    $default_shipping_price     = get_user_meta( $seller_id, '_dps_shipping_type_price', true );

                    if ( get_post_meta( $product['product_id'], '_overwrite_shipping', true ) == 'yes' ) {
                        $dps_country_rates = get_post_meta( $product['product_id'], '_additional_price', true );

                        if ( $product['variation_id'] ) {
                            $dps_country_rates = get_post_meta( $product['variation_id'], '_additional_price', true ) ? get_post_meta( $product['variation_id'], '_additional_price', true ) : array();
                        }

                        if ( !array_key_exists( $destination_country, $dps_country_rates ) ) {
                            $price[$seller_id]['addition_price'] = isset( $dps_country_rates['everywhere'] ) ? $dps_country_rates['everywhere'] : 0;
                        } else {
                            $price[$seller_id]['addition_price'] = ( isset( $dps_country_rates[$destination_country] ) ) ? $dps_country_rates[$destination_country] : 0;
                        }
                    } else {
                        $price[ $seller_id ]['addition_price'] = 0;
                    }

                    $price[ $seller_id ]['default'] = $default_shipping_price;

                    /**
                     * todo: qty wird noch nicht berechnet..
                     */
                    if ( $product['quantity'] > 1 ) {
                        $price[ $seller_id ]['qty'][] = ( ( $product['quantity'] - 1 ) * $price[$seller_id]['addition_price'] );
                    } else {
                        $price[ $seller_id ]['qty'][] = 0;
                    }
                }
            }
        }
        if ( !empty( $price ) ) {
            foreach ( $price as $s_id => $value ) {
                $amount = $amount + floatval( reset($value['qty']) ) + ( isset($value['addition_price']) ? $value['addition_price'] : 0 + $value['default']);
            }
        }


        return apply_filters( 'waa_shipping_calculate_amount', $amount, $price, $products, $destination_country, $destination_state );
    }
}




