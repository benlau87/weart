<?php
$waa_template_settings = waa_Template_Settings::init();
$validate                = $waa_template_settings->validate();

if ( $validate !== false && !is_wp_error( $validate ) ) {
   $waa_template_settings->insert_settings_info();
}
$current_user = get_current_user_id();

$scheme = is_ssl() ? 'https' : 'http';
?>

<div class="waa-dashboard-wrap">
    <?php waa_get_template( 'dashboard-nav.php', array( 'active_menu' => 'settings/store' ) ); ?>

    <div class="waa-dashboard-content waa-settings-content">
        <article class="waa-settings-area">
            <header class="waa-dashboard-header">
                <h1 class="entry-title">
                    <?php _e( 'Settings', 'waa' );?>
                    <small>&rarr; <a href="<?php echo waa_get_store_url( get_current_user_id() ); ?>"><?php _e( 'Visit Store', 'waa' ); ?></a></small>
                </h1>
            </header><!-- .waa-dashboard-header -->

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

            if ( isset( $_GET['message'] ) ) {
                ?>
                <div class="waa-alert waa-alert-success">
                    <button type="button" class="waa-close" data-dismiss="alert">&times;</button>
                    <strong><?php _e( 'Your profile has been updated successfully!', 'waa' ); ?></strong>
                </div>
            <?php
            }

            $profile_info = waa_get_store_info( $current_user );

            $gravatar   = isset( $profile_info['gravatar'] ) ? absint( $profile_info['gravatar'] ) : 0;
            $banner     = isset( $profile_info['banner'] ) ? absint( $profile_info['banner'] ) : 0;
            $storename  = isset( $profile_info['store_name'] ) ? esc_attr( $profile_info['store_name'] ) : '';
            $phone      = isset( $profile_info['phone'] ) ? esc_attr( $profile_info['phone'] ) : '';
            $description      = isset( $profile_info['description'] ) ? esc_attr( $profile_info['description'] ) : '';
            $setting_enable_services = isset( $profile_info['enable_services'] ) ? esc_attr( $profile_info['enable_services'] ) : 'no';

            $address         = isset( $profile_info['address'] ) ? $profile_info['address'] : '';
            $address_street1 = isset( $profile_info['address']['street_1'] ) ? $profile_info['address']['street_1'] : '';
            $address_street2 = isset( $profile_info['address']['street_2'] ) ? $profile_info['address']['street_2'] : '';
            $address_city    = isset( $profile_info['address']['city'] ) ? $profile_info['address']['city'] : '';
            $address_zip     = isset( $profile_info['address']['zip'] ) ? $profile_info['address']['zip'] : '';
            $address_country = isset( $profile_info['address']['country'] ) ? $profile_info['address']['country'] : '';
            $address_state   = isset( $profile_info['address']['state'] ) ? $profile_info['address']['state'] : '';

            $waa_category = isset( $profile_info['waa_category'] ) ? $profile_info['waa_category'] : '';
            $enable_tnc     = isset( $profile_info['enable_tnc'] ) ? $profile_info['enable_tnc'] : '';
            $store_tnc      = isset( $profile_info['store_tnc'] ) ? $profile_info['store_tnc'] : '' ;

            if ( is_wp_error( $validate ) ) {
                $storename    = $_POST['waa_store_name'];

                $address_street1 = $_POST['waa_address']['street_1'];
                $address_street2 = $_POST['waa_address']['street_2'];
                $address_city    = $_POST['waa_address']['city'];
                $address_zip     = $_POST['waa_address']['zip'];
                $address_country = $_POST['waa_address']['country'];
                $address_state   = $_POST['waa_address']['state'];
            }
            ?>

            <div class="waa-ajax-response">
                <?php echo waa_get_profile_progressbar(); ?>
            </div>

            <?php do_action( 'waa_settings_before_form', $current_user, $profile_info ); ?>

            <form method="post" id="store-form"  action="" class="waa-form-horizontal">

                <?php wp_nonce_field( 'waa_store_settings_nonce' ); ?>

                <div class="waa-banner">

                    <div class="image-wrap<?php echo $banner ? '' : ' waa-hide'; ?>">
                        <?php $banner_url = $banner ? wp_get_attachment_url( $banner ) : ''; ?>
                        <input type="hidden" class="waa-file-field" value="<?php echo $banner; ?>" name="waa_banner">
                        <img class="waa-banner-img" src="<?php echo esc_url( $banner_url ); ?>">

                        <a class="close waa-remove-banner-image">&times;</a>
                    </div>

                    <div class="button-area<?php echo $banner ? ' waa-hide' : ''; ?>">
                        <i class="fa fa-cloud-upload"></i>

                        <a href="#" class="waa-banner-drag waa-btn waa-btn-info waa-theme"><?php _e( 'Upload banner', 'waa' ); ?></a>
                        <p class="help-block"><?php _e( '(Upload a banner for your store. Banner size is (825x300) pixel. )', 'waa' ); ?></p>
                    </div>
                </div> <!-- .waa-banner -->

                <?php do_action( 'waa_settings_after_banner', $current_user, $profile_info ); ?>

                <div class="waa-form-group">
                    <label class="waa-w3 waa-control-label" for="waa_gravatar"><?php _e( 'Profile Picture', 'waa' ); ?></label>

                    <div class="waa-w5 waa-gravatar">
                        <div class="waa-left gravatar-wrap<?php echo $gravatar ? '' : ' waa-hide'; ?>">
                            <?php $gravatar_url = $gravatar ? wp_get_attachment_url( $gravatar ) : ''; ?>
                            <input type="hidden" class="waa-file-field" value="<?php echo $gravatar; ?>" name="waa_gravatar">
                            <img class="waa-gravatar-img" src="<?php echo esc_url( $gravatar_url ); ?>">
                            <a class="waa-close waa-remove-gravatar-image">&times;</a>
                        </div>
                        <div class="gravatar-button-area<?php echo $gravatar ? ' waa-hide' : ''; ?>">
                            <a href="#" class="waa-gravatar-drag waa-btn waa-btn-default"><i class="fa fa-cloud-upload"></i> <?php _e( 'Upload Photo', 'waa' ); ?></a>
                        </div>
                    </div>
                </div>

                <div class="waa-form-group">
                    <label class="waa-w3 waa-control-label" for="waa_store_name"><?php _e( 'Store Name', 'waa' ); ?></label>

                    <div class="waa-w5 waa-text-left">
                        <input id="waa_store_name" required value="<?php echo $storename; ?>" name="waa_store_name" placeholder="<?php _e( 'store name', 'waa'); ?>" class="waa-form-control" type="text">
                    </div>
                </div>
                 <!--address-->

                <?php
                $verified = false;

                if ( isset( $profile_info['waa_verification']['info']['store_address']['v_status'] ) ) {
                    if ( $profile_info['waa_verification']['info']['store_address']['v_status'] == 'approved' ){
                        $verified = true;
                    }
                }
                waa_seller_address_fields( $verified );

                ?>
                <!--address-->

                <div class="waa-form-group">
                    <label class="waa-w3 waa-control-label" for="setting_phone"><?php _e( 'Phone No', 'waa' ); ?></label>
                    <div class="waa-w5 waa-text-left">
                        <input id="setting_phone" value="<?php echo $phone; ?>" name="setting_phone" placeholder="<?php _e( '+123456..', 'waa' ); ?>" class="waa-form-control input-md" type="text">
                    </div>
                </div>
								
								<div class="waa-form-group">
                    <label class="waa-w3 waa-control-label" for="setting_description"><?php _e( 'Description', 'waa' ); ?></label>
                    <div class="waa-w5 waa-text-left">
                        <textarea id="setting_description" name="setting_description" placeholder="<?php _e( 'Description', 'waa' ); ?>" class="waa-form-control input-md"><?= $description; ?></textarea>
												<input type="hidden" name="setting_show_email" value="no">
                    </div>
                </div>
								
								<div class="waa-form-group">
                    <label class="waa-w3 waa-control-label" for="dps_enable_shipping" style="margin-top:6px">
                        <?php _e( 'Dienstleistungen', 'waa' ); ?>
                        <span class="waa-tooltips-help tips" title="<?php _e( 'Wenn du diese Option wählst, können dich Kunden über dein Profil anschreiben. ', 'waa' ); ?>">
                            <i class="fa fa-question-circle"></i>
                        </span>
                    </label>

                    <div class="waa-w5 waa-text-left">
                        <div class="checkbox">
                            <label>
                                <input type="hidden" name="setting_enable_services" value="no">
                                <input type="checkbox" name="setting_enable_services" value="yes" <?php checked( 'yes', $setting_enable_services, true ); ?>> <?php _e( 'Ich biete auch Dienstleitungen an.', 'waa' ); ?>
                            </label>
                        </div>
                    </div>
                </div>
								
                <!--terms and conditions enable or not -->
                <?php
                $tnc_enable = waa_get_option( 'seller_enable_terms_and_conditions', 'waa_selling', 'off' );
                if ( $tnc_enable == 'on' ) :
                    ?>
                    <div class="waa-form-group">
                        <label class="waa-w3 waa-control-label" for="waa_store_tnc_enable"><?php _e( 'Terms and Conditions', 'waa' ); ?></label>
                        <div class="waa-w5 waa-text-left waa_tock_check">
                            <div class="checkbox">
                                <label>
                                    <input id="waa_store_tnc_enable" value="on" <?php echo $enable_tnc == 'on' ? 'checked':'' ; ?> name="waa_store_tnc_enable" class="waa-form-control" type="checkbox"><?php _e( 'Show terms and conditions in store page', 'waa' ); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="waa-form-group" id="waa_tnc_text">
                        <label class="waa-w3 waa-control-label" for="waa_store_tnc"><?php _e( 'TOC Details', 'waa' ); ?></label>
                        <div class="waa-w8 waa-text-left">
                            <?php
                            $settings = array(
                                'editor_height' => 200,
                                'media_buttons' => false,
                                'teeny' => true,
                                'quicktags' => false
                            );
                            wp_editor( $store_tnc, 'waa_store_tnc', $settings);
                            ?>
                        </div>
                    </div>

                <?php endif;?>

                <?php do_action( 'waa_settings_form_bottom', $current_user, $profile_info ); ?>

                <div class="waa-form-group">

                    <div class="waa-w4 ajax_prev waa-text-left" style="margin-left:24%;">
                        <input type="submit" name="waa_update_store_settings" class="waa-btn waa-btn-danger waa-btn-theme" value="<?php esc_attr_e( 'Update Settings', 'waa' ); ?>">
                    </div>
                </div>
            </form>

            <?php do_action( 'waa_settings_after_form', $current_user, $profile_info ); ?>
            <!--settings updated content ends-->
        </article>
    </div><!-- .waa-dashboard-content -->
</div><!-- .waa-dashboard-wrap -->