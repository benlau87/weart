<?php
global $post;

$tax_classes = array_filter( array_map( 'trim', explode( "\n", get_option( 'woocommerce_tax_classes' ) ) ) );
$classes_options = array();
$classes_options[''] = __( 'Standard', 'waa' );

if ( $tax_classes ) {

    foreach ( $tax_classes as $class ) {
        $classes_options[ sanitize_title( $class ) ] = esc_html( $class );
    }
}

?>
<div class="waa-form-horizontal">
    <div class="waa-form-group">
        <label class="waa-w4 waa-control-label" for="_sku"><?php _e( 'SKU', 'waa' ); ?></label>
        <div class="waa-w4 waa-text-left">
            <?php waa_post_input_box( $post->ID, '_sku', array( 'placeholder' => __( 'SKU', 'waa' ) ) ); ?>
        </div>
    </div>

    <div class="waa-form-group">
        <label class="waa-w4 waa-control-label" for=""><?php _e( 'Manage Stock?', 'waa' ); ?></label>
        <div class="waa-w6 waa-text-left">
            <?php waa_post_input_box( $post->ID, '_manage_stock', array('label' => __( 'Enable stock management at product level', 'waa' ) ), 'checkbox' ); ?>
        </div>
    </div>

    <div class="waa-form-group">
        <label class="waa-w4 waa-control-label" for="_stock_qty"><?php _e( 'Stock Qty', 'waa' ); ?></label>
        <div class="waa-w4 waa-text-left">
            <input type="number" name="_stock" id="_stock" step="any" placeholder="10" value="<?php echo wc_stock_amount( get_post_meta( $post->ID, '_stock', true ) ); ?>">
        </div>
    </div>

    <div class="waa-form-group hide_if_variable">
        <label class="waa-w4 waa-control-label" for="_stock_status"><?php _e( 'Stock Status', 'waa' ); ?></label>
        <div class="waa-w4 waa-text-left">
            <?php waa_post_input_box( $post->ID, '_stock_status', array( 'options' => array(
                'instock' => __( 'In Stock', 'waa' ),
                'outofstock' => __( 'Out of Stock', 'waa' )
                ) ), 'select'
            ); ?>
        </div>
    </div>

    <div class="waa-form-group">
        <label class="waa-w4 waa-control-label" for="_backorders"><?php _e( 'Allow Backorders', 'waa' ); ?></label>
        <div class="waa-w4 waa-text-left">
            <?php waa_post_input_box( $post->ID, '_backorders', array( 'options' => array(
                'no' => __( 'Do not allow', 'waa' ),
                'notify' => __( 'Allow but notify customer', 'waa' ),
                'yes' => __( 'Allow', 'waa' )
                ) ), 'select'
            ); ?>
        </div>
    </div>

    <?php if ( 'yes' == get_option( 'woocommerce_calc_taxes' ) ) { ?>

        <div class="waa-form-group">
            <label class="waa-w4 waa-control-label" for="_tax_status"><?php _e( 'Tax Status', 'waa' ); ?></label>
            <div class="waa-w4 waa-text-left">
                <?php waa_post_input_box( $post->ID, '_tax_status', array( 'options' => array(
                    'taxable'   => __( 'Taxable', 'waa' ),
                    'shipping'  => __( 'Shipping only', 'waa' ),
                    'none'      => _x( 'None', 'Tax status', 'waa' )
                    ) ), 'select'
                ); ?>
            </div>
        </div>

        <div class="waa-form-group">
            <label class="waa-w4 waa-control-label" for="_tax_class"><?php _e( 'Tax Class', 'waa' ); ?></label>
            <div class="waa-w4 waa-text-left">
                <?php waa_post_input_box( $post->ID, '_tax_class', array( 'options' => $classes_options ), 'select' ); ?>
            </div>
        </div>

    <?php } ?>
</div> <!-- .form-horizontal -->