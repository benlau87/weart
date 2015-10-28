<div class="waa-form-horizontal">
    <div class="waa-form-group">
        <label class="waa-w4 waa-control-label" for="_purchase_note"><?php _e( 'Purchase Note', 'waa' ); ?></label>
        <div class="waa-w6 waa-text-left">
            <?php waa_post_input_box( $post->ID, '_purchase_note', array(), 'textarea' ); ?>
        </div>
    </div>

    <div class="waa-form-group">
        <label class="waa-w4 waa-control-label" for="_enable_reviews"><?php _e( 'Reviews', 'waa' ); ?></label>
        <div class="waa-w4 waa-text-left">
            <?php $_enable_reviews = ( $post->comment_status == 'open' ) ? 'yes' : 'no'; ?>
            <?php waa_post_input_box( $post->ID, '_enable_reviews', array('value' => $_enable_reviews, 'label' => __( 'Enable Reviews', 'waa' ) ), 'checkbox' ); ?>
        </div>
    </div>

    <div class="waa-form-group">
        <label class="waa-w4 waa-control-label" for="_purchase_note"><?php _e( 'Visibility', 'waa' ); ?></label>
        <div class="waa-w6 waa-text-left">
            <?php waa_post_input_box( $post->ID, '_visibility', array( 'options' => array(
                'visible' => __( 'Catalog or Search', 'waa' ),
                'catalog' => __( 'Catalog', 'waa' ),
                'search' => __( 'Search', 'waa' ),
                'hidden' => __( 'Hidden', 'waa ')
            ) ), 'select' ); ?>
        </div>
    </div>
    
    <div class="waa-form-group">
        <label class="waa-w4 waa-control-label" for="_enable_reviews"><?php _e( 'Sold Individually', 'waa' ); ?></label>
        <div class="waa-w7 waa-text-left">
            <?php waa_post_input_box( $post->ID, '_sold_individually', array('label' => __( 'Enable this to only allow one of this item to be bought in a single order', 'waa' ) ), 'checkbox' ); ?>
        </div>
    </div>

</div> <!-- .form-horizontal -->