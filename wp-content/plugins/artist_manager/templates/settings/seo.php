<?php
$seo = waa_Store_Seo::init();
?>

<div class="waa-dashboard-wrap">
    <?php waa_get_template( 'dashboard-nav.php', array( 'active_menu' => 'settings/seo' ) ); ?>

    <div class="waa-dashboard-content waa-settings-content waa-store-seo-wrapper">
        <article class="waa-settings-area">
            <header class="waa-dashboard-header">
                <h1 class="entry-title">
                    <?php _e( 'Store SEO', 'waa' ); ?>
                    <small>&rarr; <a href="<?php echo waa_get_store_url( get_current_user_id() ); ?>"><?php _e( 'Visit Store', 'waa' ); ?></a></small>
                </h1>
            </header><!-- .waa-dashboard-header -->
            
            <?php $seo->frontend_meta_form(); ?>

        </article>
    </div><!-- .waa-dashboard-content -->
</div><!-- .waa-dashboard-wrap -->