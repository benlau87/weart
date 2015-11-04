<?php
$waa_template_settings = waa_Template_Settings::init();
$validate                = $waa_template_settings->profile_validate();

if ( $validate !== false && !is_wp_error( $validate ) ) {
   $waa_template_settings->insert_settings_info();
}
?>

<div class="waa-dashboard-wrap">
    <?php waa_get_template( 'dashboard-nav.php', array( 'active_menu' => 'settings/social' ) ); ?>

    <div class="waa-dashboard-content waa-settings-content">
        <article class="waa-settings-area">
            <header class="waa-dashboard-header">
                <h1 class="entry-title">
                    <?php _e( 'Social Profiles', 'waa' );?>
                    <small>&rarr; <a href="<?php echo waa_get_store_url( get_current_user_id() ); ?>"><?php _e( 'Visit Store', 'waa' ); ?></a></small>
                </h1>
            </header><!-- .waa-dashboard-header -->

            <div class="waa-page-help">
                <?php _e( 'Social profiles help you to gain more trust. Consider adding your social profile links for better user interaction.', 'waa' ); ?>
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

            $profile_info  = waa_get_store_info( $current_user->ID );
            $social_fields = waa_get_social_profile_fields();
            ?>

            <div class="waa-ajax-response">
                <?php echo waa_get_profile_progressbar(); ?>
            </div>

            <?php 
            /**
             * @since 2.2.2 Insert action before social settings form
             */
            do_action( 'waa_profile_settings_before_form', $current_user, $profile_info ); ?>

            <form method="post" id="profile-form"  action="" class="waa-form-horizontal"><?php ///settings-form ?>

                <?php wp_nonce_field( 'waa_profile_settings_nonce' ); ?>

                <?php foreach( $social_fields as $key => $field ) { ?>
                    <div class="waa-form-group">
                        <label class="waa-w3 waa-control-label"><?php echo $field['title']; ?></label>

                        <div class="waa-w5">
                            <div class="waa-input-group waa-form-group">
                                <span class="waa-input-group-addon"><i class="ui ui-<?php echo isset( $field['icon'] ) ? $field['icon'] : ''; ?>"></i></span>
                                <input id="settings[social][<?php echo $key; ?>]" value="<?php echo isset( $profile_info['social'][$key] ) ? esc_url( $profile_info['social'][$key] ) : ''; ?>" name="settings[social][<?php echo $key; ?>]" class="waa-form-control" placeholder="http://" type="url">
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <?php 
                /**
                 * @since 2.2.2 Insert action on bottom social settings form
                 */
                do_action( 'waa_profile_settings_form_bottom', $current_user, $profile_info ); ?>

                <div class="waa-form-group">
                    <div class="waa-w4 ajax_prev waa-text-left" style="margin-left:24%;">
                        <input type="submit" name="waa_update_profile_settings" class="waa-btn waa-btn-danger waa-btn-theme" value="<?php esc_attr_e( 'Update Settings', 'waa' ); ?>">
                    </div>
                </div>

            </form>

            <?php 
            /**
             * @since 2.2.2 Insert action after social settings form
             */
            do_action( 'waa_profile_settings_after_form', $current_user, $profile_info ); ?>
            <!--settings updated content end-->

        </article>
    </div><!-- .waa-dashboard-content -->
</div><!-- .waa-dashboard-wrap -->