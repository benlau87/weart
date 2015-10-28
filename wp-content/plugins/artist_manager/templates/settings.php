<?php
$waa_template_settings = waa_Template_Settings::init();
$validate                = $waa_template_settings->validate();

if ( $validate !== false && !is_wp_error( $validate ) ) {
   $waa_template_settings->insert_settings_info();
}

$scheme = is_ssl() ? 'https' : 'http';
wp_enqueue_script( 'google-maps', $scheme . '://maps.google.com/maps/api/js?sensor=true' );
?>

<div class="waa-dashboard-wrap">
    <?php waa_get_template( 'dashboard-nav.php', array( 'active_menu' => 'settings' ) ); ?>

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

            <?php $waa_template_settings->setting_field($validate); ?>
        </article>
    </div><!-- .waa-dashboard-content -->
</div><!-- .waa-dashboard-wrap -->