<?php
global $post;

$user_id                 = get_current_user_id();
$processing_time         = waa_get_shipping_processing_times();

$_additional_price       = get_post_meta( $post->ID, '_additional_price', true );
$_additional_qty         = get_post_meta( $post->ID, '_additional_qty', true );
$_processing_time        = get_post_meta( $post->ID, '_dps_processing_time', true );

$dps_shipping_type_price = get_user_meta( $user_id, '_dps_shipping_type_price', true );
$dps_additional_qty      = get_user_meta( $user_id, '_dps_additional_qty', true );
$dps_pt                  = get_user_meta( $user_id, '_dps_pt', true );

$porduct_shipping_pt     = ( $_processing_time ) ? $_processing_time : $dps_pt;
?>

<?php do_action( 'waa_product_options_shipping_before' ); ?>

<div class="waa-form-horizontal waa-product-shipping">
    <input type="hidden" name="product_shipping_class" value="0">
    <?php if ( 'yes' == get_option( 'woocommerce_calc_shipping' ) ): ?>
        <div class="waa-form-group">
            <label class="waa-w4 waa-control-label" for="_disable_shipping"><?php _e( 'Disable Shipping', 'waa' ); ?></label>
            <div class="waa-w8 waa-text-left">
                <?php waa_post_input_box( $post->ID, '_disable_shipping', array( 'label' => __( 'Disable shipping for this product', 'waa' ) ), 'checkbox' ); ?>
            </div>
        </div>
    <?php endif ?>

    <div class="waa-form-group">
        <label class="waa-w4 waa-control-label" for="_backorders"><?php echo __( 'Weight', 'waa' ) . ' (' . get_option( 'woocommerce_weight_unit' ) . ')'; ?></label>
        <div class="waa-w4 waa-text-left">
            <?php waa_post_input_box( $post->ID, '_weight' ); ?>
        </div>
    </div>

    <div class="waa-form-group">
        <label class="waa-w4 waa-control-label" for="_backorders"><?php echo __( 'Dimensions', 'waa' ) . ' (' . get_option( 'woocommerce_dimension_unit' ) . ')'; ?></label>
        <div class="waa-w8 waa-text-left product-dimension">
            <?php waa_post_input_box( $post->ID, '_length', array( 'class' => 'form-control col-sm-1', 'placeholder' => __( 'length', 'waa' ) ), 'number' ); ?>
            <?php waa_post_input_box( $post->ID, '_width', array( 'class' => 'form-control col-sm-1', 'placeholder' => __( 'width', 'waa' ) ), 'number' ); ?>
            <?php waa_post_input_box( $post->ID, '_height', array( 'class' => 'form-control col-sm-1', 'placeholder' => __( 'height', 'waa' ) ), 'number' ); ?>
        </div>
    </div>

    <?php if ( 'yes' == get_option( 'woocommerce_calc_shipping' ) ): ?>
        <div class="waa-form-group hide_if_disable">
            <label class="waa-w4 waa-control-label" for="_overwrite_shipping"><?php _e( 'Override Shipping', 'waa' ); ?></label>
            <div class="waa-w8 waa-text-left">
                <?php waa_post_input_box( $post->ID, '_overwrite_shipping', array( 'label' => __( 'Override default shipping cost for this product', 'waa' ) ), 'checkbox' ); ?>
            </div>
        </div>

        <div class="waa-form-group waa-shipping-price waa-shipping-type-price show_if_override hide_if_disable">
            <label class="waa-w4 waa-control-label" for="shipping_type_price"><?php _e( 'Additional cost', 'waa' ); ?></label>

            <div class="waa-w4 waa-text-left">
                <input id="shipping_type_price" value="<?php echo $_additional_price; ?>" name="_additional_price" placeholder="0.00" class="waa-form-control" type="number" step="any">
            </div>
        </div>

        <div class="waa-form-group waa-shipping-price waa-shipping-add-qty show_if_override hide_if_disable">
            <label class="waa-w4 waa-control-label" for="dps_additional_qty"><?php _e( 'Per Qty Additional Price', 'waa' ); ?></label>

            <div class="waa-w4 waa-text-left">
                <input id="additional_qty" value="<?php echo ( $_additional_qty ) ? $_additional_qty : $dps_additional_qty; ?>" name="_additional_qty" placeholder="1.99" class="waa-form-control" type="number" step="any">
            </div>
        </div>

        <div class="waa-form-group waa-shipping-price waa-shipping-add-qty show_if_override hide_if_disable">
            <label class="waa-w4 waa-control-label" for="dps_additional_qty"><?php _e( 'Processing Time', 'waa' ); ?></label>

            <div class="waa-w4 waa-text-left">
                <select name="_dps_processing_time" id="_dps_processing_time" class="waa-form-control">
                    <?php foreach ( $processing_time as $processing_key => $processing_value ): ?>
                          <option value="<?php echo $processing_key; ?>" <?php selected( $porduct_shipping_pt, $processing_key ); ?>><?php echo $processing_value; ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
    <?php endif ?>

    <?php do_action( 'waa_product_options_shipping' ); ?>
</div>
