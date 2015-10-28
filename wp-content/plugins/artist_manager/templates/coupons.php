<?php
$waa_template_coupons = waa_Template_Coupons::init();
$is_edit_page           = isset( $_GET['view'] ) && $_GET['view'] == 'add_coupons';
?>

<div class="waa-dashboard-wrap">
    <?php waa_get_template( 'dashboard-nav.php', array( 'active_menu' => 'coupon' ) ); ?>

    <div class="waa-dashboard-content waa-coupon-content">

        <article class="dashboard-coupons-area">
            <header class="waa-dashboard-header waa-clearfix">
                <span class="left-header-content waa-left">
                    <h1 class="entry-title">
                        <?php _e( 'Coupon', 'waa' ); ?>
                    <?php if ( $is_edit_page ) {
                        printf( '<small> - %s</small>', __( 'Edit Coupon', 'waa' ) );
                    } ?>
                    </h1>
                </span>

                <?php if ( !$is_edit_page ) { ?>
                    <span class="left-header-content waa-right">
                        <a href="<?php echo add_query_arg( array( 'view' => 'add_coupons'), waa_get_navigation_url( 'coupons' ) ); ?>" class="waa-btn waa-btn-theme waa-right"><i class="fa fa-gift">&nbsp;</i> <?php _e( 'Add new Coupon', 'waa' ); ?></a>
                    </span>
                <?php } ?>
            </header><!-- .entry-header -->

            <?php
            if ( !waa_is_seller_enabled( get_current_user_id() ) ) {
                waa_seller_not_enabled_notice();
            } else {
                ?>

                <?php $waa_template_coupons->list_user_coupons(); ?>

                <?php
                if ( is_wp_error( waa_Template_Shortcodes::$validated )) {
                    $messages = waa_Template_Shortcodes::$validated->get_error_messages();

                    foreach ($messages as $message) {
                        ?>
                        <div class="waa-alert waa-alert-danger" style="width: 40%; margin-left: 25%;">
                            <button type="button" class="waa-close" data-dismiss="alert">&times;</button>
                            <strong><?php _e( $message,'waa'); ?></strong>
                        </div>
                        <?php
                    }
                }
                ?>
                <?php $waa_template_coupons->add_coupons_form( waa_Template_Shortcodes::$validated ); ?>

            <?php } ?>

        </article>
    </div><!-- .waa-dashboard-content -->
</div><!-- .waa-dashboard-wrap -->