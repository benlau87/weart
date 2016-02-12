<?php
global $post;

$_downloadable   = get_post_meta( $post->ID, '_downloadable', true );

?>
<div class="update-button-wrap">
    <input type="submit" name="update_product" class="waa-btn waa-btn-theme waa-btn-lg" value="<?php esc_attr_e( 'Update Product', 'waa' ); ?>"/>
</div>

<div class="toggle-sidebar-container">

    <div class="waa-post-status waa-toggle-sidebar">
        <label for="post_status"><?php _e( 'Product Status:', 'waa' ); ?></label>

        <?php $pending_class = $post->post_status == 'pending' ? '  waa-label waa-label-warning': ''; ?>
        <span class="waa-toggle-selected-display<?php echo $pending_class; ?>"><?php echo waa_get_post_status( $post->post_status ); ?></span>

        <?php if ( $post->post_status != 'pending' ) { ?>
            <a class="waa-toggle-edit waa-label waa-label-success" href="#"><?php _e( 'Edit', 'waa' ); ?></a>

            <div class="waa-toggle-select-container waa-hide">

                <?php $post_statuses = apply_filters( 'waa_post_status', array(
                    'publish' => __( 'Online', 'waa' ),
                    'draft'   => __( 'Draft', 'waa' )
                ), $post ); ?>

                <select id="post_status" class="waa-toggle-select" name="post_status">
                    <?php foreach ($post_statuses as $status => $label) { ?>
                        <option value="<?php echo $status; ?>"<?php selected( $post->post_status, $status ); ?>><?php echo $label; ?></option>
                    <?php } ?>
                </select>

                <a class="waa-toggle-save waa-btn waa-btn-default waa-btn-sm" href="#"><?php _e( 'OK', 'waa' ); ?></a>
                <a class="waa-toggle-cacnel" href="#"><?php _e( 'Cancel', 'waa' ); ?></a>
            </div> <!-- #waa-toggle-select -->
        <?php } ?>
    </div>

    <div class="product-type waa-toggle-sidebar">
        <label for="product_type"><?php _e( 'Product Type:', 'waa' ); ?></label>

        <?php
        $supported_types = apply_filters( 'waa_product_type_selector', array(
            'simple'    => __( 'Simple product', 'waa' ),
            'variable'  => __( 'Variable product', 'waa' )
        ) );

        if ( $terms = wp_get_object_terms( $post->ID, 'product_type' ) ) {
            $product_type = sanitize_title( current( $terms )->name );
        } else {
            $product_type = 'simple';
        }


        if ( !array_key_exists( $product_type, $supported_types) ) {
            $product_type = 'simple';
        }
        ?>

        <span class="waa-toggle-selected-display"><?php echo waa_wc_get_product_status( $product_type ); ?></span>
        <a class="waa-toggle-edit waa-label waa-label-success" href="#"><?php _e( 'Edit', 'waa' ); ?></a>

            <div class="waa-toggle-select-container waa-hide">
                <select name="_product_type" id="_product_type" class="waa-toggle-select">
                    <?php
                    foreach ( $supported_types as $value => $label ) {
                        echo '<option value="' . esc_attr( $value ) . '" ' . selected( $product_type, $value, false ) .'>' . esc_html( $label ) . '</option>';
                    }
                    ?>

                </select>

                <a class="waa-toggle-save waa-btn waa-btn-default waa-btn-sm" href="#"><?php _e( 'OK', 'waa' ); ?></a>
                <a class="waa-toggle-cacnel" href="#"><?php _e( 'Cancel', 'waa' ); ?></a>
            </div> <!-- #waa-toggle-select -->

    </div> <!-- .product-type -->
</div>

<?php do_action( 'waa_product_edit_before_sidebar' ); ?>

<aside class="downloadable downloadable_files">
    <div class="waa-side-head">
        <label class="checkbox-inline">
            <input type="checkbox" id="_downloadable" name="_downloadable" value="yes"<?php checked( $_downloadable, 'yes' ); ?>>
            <?php _e( 'Downloadable Product', 'waa' ); ?>
        </label>
    </div> <!-- .waa-side-head -->

    <div class="waa-side-body<?php echo ($_downloadable == 'yes' ) ? '' : ' waa-hide'; ?>">
        <ul class="list-unstyled ">
            <li class="waa-form-group">

                <table class="waa-table waa-table-condensed">
                    <tfoot>
                        <tr>
                            <th>
                                <a href="#" class="insert-file-row waa-btn waa-btn-sm waa-btn-success" data-row="<?php
                                    $file = array(
                                        'file' => '',
                                        'name' => ''
                                    );
                                    ob_start();
                                    include waa_INC_DIR . '/woo-views/html-product-download.php';
                                    echo esc_attr( ob_get_clean() );
                                ?>"><?php _e( 'Add File', 'waa' ); ?></a>
                            </th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        $downloadable_files = get_post_meta( $post->ID, '_downloadable_files', true );

                        if ( $downloadable_files ) {
                            foreach ( $downloadable_files as $key => $file ) {
                                include waa_INC_DIR . '/woo-views/html-product-download.php';
                            }
                        }
                        ?>
                    </tbody>
                </table>

            </li>
            <li class="waa-form-group">
                <div class="waa-input-group">
                    <span class="waa-input-group-addon"><?php _e( 'Limit', 'waa' ); ?></span>
                    <?php waa_post_input_box( $post->ID, '_download_limit', array( 'placeholder' => __( 'Number of times', 'waa' ), 'min' => 1, 'step' => 1 ), 'number' ); ?>
                </div>
            </li>
            <li class="waa-form-group">
                <div class="waa-input-group">
                    <span class="waa-input-group-addon">Expiry</span>
                    <?php waa_post_input_box( $post->ID, '_download_expiry', array( 'placeholder' => __( 'Number of days', 'waa' ), 'min' => 1, 'step' => 1 ), 'number' ); ?>
                </div>
            </li>
        </ul>
    </div> <!-- .waa-side-body -->
</aside> <!-- .downloadable -->

<?php do_action( 'waa_product_edit_after_downloadable' ); ?>

<aside class="product-gallery">
    <div class="waa-side-head">
        <?php _e( 'Image Gallery', 'waa' ); ?>
    </div>

    <div class="waa-side-body" id="waa-product-images">
        <div id="product_images_container">
            <ul class="product_images waa-clearfix">
                <?php
                $product_images = get_post_meta( $post->ID, '_product_image_gallery', true );
                $gallery = explode( ',', $product_images );

                if ( $gallery ) {
                    foreach ($gallery as $image_id) {
                        if ( empty( $image_id ) ) {
                            continue;
                        }

                        $attachment_image = wp_get_attachment_image_src( $image_id, 'thumbnail' );
                        ?>
                        <li class="image" data-attachment_id="<?php echo $image_id; ?>">
                            <img src="<?php echo $attachment_image[0]; ?>" alt="">

                            <ul class="actions">
                                <li><a href="#" class="delete" title="<?php esc_attr_e( 'Delete image', 'waa' ); ?>"><?php _e( 'Delete', 'waa' ); ?></a></li>
                            </ul>
                        </li>
                        <?php
                    }
                }
                ?>
            </ul>

            <input type="hidden" id="product_image_gallery" name="product_image_gallery" value="<?php echo esc_attr( $product_images ); ?>">
        </div>

        <a href="#" class="add-product-images waa-btn waa-btn-success"><?php _e( '+ Add product images', 'waa' ); ?></a>
    </div>
</aside> <!-- .product-gallery -->

<?php do_action( 'waa_product_edit_after_sidebar' ); ?>