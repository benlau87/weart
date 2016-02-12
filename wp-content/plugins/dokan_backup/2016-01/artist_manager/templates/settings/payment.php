<?php
$waa_template_settings = waa_Template_Settings::init();
$validate                = $waa_template_settings->validate();

if ( $validate !== false && !is_wp_error( $validate ) ) {
   $waa_template_settings->insert_settings_info();
}
?>
<div class="waa-dashboard-wrap">
    <?php waa_get_template( 'dashboard-nav.php', array( 'active_menu' => 'settings/payment' ) ); ?>

    <div class="waa-dashboard-content waa-settings-content">
        <article class="waa-settings-area">
            <header class="waa-dashboard-header">
                <h1 class="entry-title">
                    <?php _e( 'Payment Settings', 'waa' );?>
                    <small>&rarr; <a href="<?php echo waa_get_store_url( get_current_user_id() ); ?>"><?php _e( 'Visit Store', 'waa' ); ?></a></small>
                </h1>
            </header><!-- .waa-dashboard-header -->

            <div class="waa-page-help">
                <?php _e( 'These are the withdraw methods available for you. Please update your payment informations below to submit withdraw requests and get your store payments seamlessly.', 'waa' ); ?>
            </div>

            <?php if ( is_wp_error( $validate ) ) {
                $messages = $validate->get_error_messages();

                foreach( $messages as $message ) {
                    ?>
                    <div class="waa-alert waa-alert-danger" style="width: 40%; margin-left: 25%;">
                        <button type="button" class="waa-close" data-dismiss="alert">&times;</button>
                        <strong><?php echo $message; ?></strong>
                    </div>

                    <?php
                }
            } ?>

            <?php //$waa_template_settings->setting_field($validate); ?>
            <!--settings updated content-->
            <?php
            global $current_user;

            if ( isset( $_GET['message'] ) ) {
                ?>
                <div class="waa-alert waa-alert-success">
                    <button type="button" class="waa-close" data-dismiss="alert">&times;</button>
                    <strong><?php _e( 'Your profile has been updated successfully!', 'waa' ); ?></strong>
                </div>
            <?php
            }

            $profile_info   = waa_get_store_info( $current_user->ID );


            if ( is_wp_error( $validate ) ) {
            }
            ?>

            <div class="waa-ajax-response">
                <?php echo waa_get_profile_progressbar(); ?>
            </div>

            <?php 
            /**
             * @since 2.2.2 Insert action before payment settings form
             */
            do_action( 'waa_payment_settings_before_form', $current_user, $profile_info ); ?>

            <form method="post" id="payment-form"  action="" class="waa-form-horizontal">

                <?php wp_nonce_field( 'waa_payment_settings_nonce' ); ?>

                <?php $methods = waa_withdraw_get_active_methods(); ?>
                <?php foreach ( $methods as $method_key ) {
                    $method = waa_withdraw_get_method( $method_key );
                    ?>
                    <fieldset classs="payment-field-<?php echo $method_key; ?>">
                        <div class="waa-form-group">
                            <label class="waa-w3 waa-control-label" for="waa_setting"><?php echo $method['title'] ?></label>
                            <div class="waa-w6">
                                <?php if ( is_callable( $method['callback'] ) ) {
                                    call_user_func( $method['callback'], $profile_info );
                                } ?>
                            </div> <!-- .waa-w6 -->
                        </div>
                    </fieldset>
                <?php } ?>

                <?php 
                /**
                 * @since 2.2.2 Insert action on botton of payment settings form
                 */
                do_action( 'waa_payment_settings_form_bottom', $current_user, $profile_info ); ?>

                <div class="waa-form-group">

                    <div class="waa-w4 ajax_prev waa-text-left" style="margin-left:24%;">
                        <input type="submit" name="waa_update_payment_settings" class="waa-btn waa-btn-danger waa-btn-theme" value="<?php esc_attr_e( 'Update Settings', 'waa' ); ?>">
                    </div>
                </div>

            </form>

            <?php 
            /**
             * @since 2.2.2 Insert action after social settings form
             */
            do_action( 'waa_payment_settings_after_form', $current_user, $profile_info ); ?>

            <!--settings updated content ends-->
        </article>
    </div><!-- .waa-dashboard-content -->
</div><!-- .waa-dashboard-wrap -->