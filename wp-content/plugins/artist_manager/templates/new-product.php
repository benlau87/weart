<div class="waa-dashboard-wrap">
    <?php waa_get_template( 'dashboard-nav.php', array( 'active_menu' => 'product' ) ); ?>

    <div class="waa-dashboard-content">

        <div class="waa-new-product-area">
            <?php if ( waa_Template_Shortcodes::$errors ) { ?>
                <div class="waa-alert waa-alert-danger">
                    <a class="waa-close" data-dismiss="alert">&times;</a>

                    <?php foreach ( waa_Template_Shortcodes::$errors as $error) { ?>

                        <strong><?php _e( 'Error!', 'waa' ); ?></strong> <?php echo $error ?>.<br>

                    <?php } ?>
                </div>
            <?php } ?>

            <?php

            $can_sell = apply_filters( 'waa_can_post', true );

            if ( $can_sell ) {

                if ( waa_is_seller_enabled( get_current_user_id() ) ) { ?>

                <form class="waa-form-container" method="post">

                    <div class="row product-edit-container waa-clearfix">
                        <div class="waa-w4">
                            <div class="waa-feat-image-upload">
                                <div class="instruction-inside">
                                    <input type="hidden" name="feat_image_id" class="waa-feat-image-id" value="0">
                                    <i class="ui ui-cloud-upload"></i>
                                    <a href="#" class="waa-feat-image-btn waa-btn"><?php _e( 'Upload Product Image', 'waa' ); ?></a>
                                </div>

                                <div class="image-wrap waa-hide">
                                    <a class="close waa-remove-feat-image">&times;</a>
                                        <img src="" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="waa-w6">
                            <div class="waa-form-group">
                                <input class="waa-form-control" name="post_title" id="post-title" type="text" placeholder="<?php esc_attr_e( 'Product name..', 'waa' ); ?>" value="<?php echo waa_posted_input( 'post_title' ); ?>">
                            </div>

                            <div class="waa-form-group">
                                <div class="waa-input-group">
                                    <span class="waa-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                    <input class="waa-form-control" name="price" id="product-price" type="text" placeholder="0.00" value="<?php echo waa_posted_input( 'price' ); ?>">
                                </div>
                            </div>

                            <div class="waa-form-group">
                                <textarea name="post_excerpt" id="post-excerpt" rows="5" class="waa-form-control" placeholder="<?php esc_attr_e( 'Short description about the product...', 'waa' ); ?>"><?php echo waa_posted_textarea( 'post_excerpt' ); ?></textarea>
                            </div>

                            <?php if ( waa_get_option( 'product_category_style', 'waa_selling', 'single' ) == 'single' ): ?>
                                <div class="waa-form-group">

                                    <?php
                                    wp_dropdown_categories( array(
                                        'show_option_none' => __( '- Select a category -', 'waa' ),
                                        'hierarchical'     => 1,
                                        'hide_empty'       => 0,
                                        'name'             => 'product_cat',
                                        'id'               => 'product_cat',
                                        'taxonomy'         => 'product_cat',
                                        'title_li'         => '',
                                        'class'            => 'product_cat waa-form-control chosen',
                                        'exclude'          => '',
                                        'selected'         => waa_Template_Shortcodes::$product_cat,
                                    ) );
                                    ?>
                                </div>
                            <?php elseif ( waa_get_option( 'product_category_style', 'waa_selling', 'single' ) == 'multiple' ): ?>
                                <div class="waa-form-group waa-list-category-box">
                                    <h5><?php _e( 'Choose a category', 'waa' );  ?></h5>
                                    <ul class="waa-checkbox-cat">
                                        <?php
                                        include_once waa_LIB_DIR.'/class.category-walker.php';
                                        wp_list_categories(array(

                                          'walker'       => new waaCategoryWalker(),
                                          'title_li'     => '',
                                          'id'           => 'product_cat',
                                          'hide_empty'   => 0,
                                          'taxonomy'     => 'product_cat',
                                          'hierarchical' => 1,
                                          'selected'     => array()
                                        ));
                                        ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <div class="waa-form-group">

                                <?php
                                $drop_down_tags = wp_dropdown_categories( array(
                                    'show_option_none' => __( '', 'waa' ),
                                    'hierarchical'     => 1,
                                    'hide_empty'       => 0,
                                    'name'             => 'product_tag[]',
                                    'id'               => 'product_tag',
                                    'taxonomy'         => 'product_tag',
                                    'title_li'         => '',
                                    'class'            => 'product_tags waa-form-control chosen',
                                    'exclude'          => '',
                                    'selected'         => '',
                                    'echo'             => 0
                                ) );

                                echo str_replace( '<select', '<select data-placeholder="Select product tags" multiple="multiple" ', $drop_down_tags );
                                ?>
                            </div>


                        </div>
                    </div>

                    <!-- <textarea name="post_content" id="" cols="30" rows="10" class="span7" placeholder="Describe your product..."><?php echo waa_posted_textarea( 'post_content' ); ?></textarea> -->
                    <div class="waa-form-group">
                        <?php wp_editor( waa_Template_Shortcodes::$post_content, 'post_content', array('editor_height' => 50, 'quicktags' => false, 'media_buttons' => false, 'teeny' => true, 'editor_class' => 'post_content') ); ?>
                    </div>

                    <?php do_action( 'waa_new_product_form' ); ?>

                    <div class="waa-form-group">
                        <?php wp_nonce_field( 'waa_add_new_product', 'waa_add_new_product_nonce' ); ?>
                        <input type="submit" name="add_product" class="waa-btn waa-btn-danger waa-btn-theme" value="<?php esc_attr_e( 'Add Product', 'waa' ); ?>"/>
                    </div>

                </form>

                <?php } else { ?>

                    <?php waa_seller_not_enabled_notice(); ?>

                <?php } ?>

            <?php } else { ?>

                <?php do_action( 'waa_can_post_notice' ); ?>

            <?php } ?>
    </div> <!-- #primary .content-area -->
</div><!-- .waa-dashboard-wrap -->