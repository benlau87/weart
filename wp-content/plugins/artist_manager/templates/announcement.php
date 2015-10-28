<?php
    $announcement = waa_Template_Notice::init();

?>

<div class="waa-dashboard-wrap">
    <?php waa_get_template( 'dashboard-nav.php', array( 'active_menu' => 'announcement' ) ); ?>
    <?php //var_dump( $urls = waa_get_dashboard_nav() ); ?>

    <div class="waa-dashboard-content waa-notice-listing">

        <?php do_action( 'waa_before_listing_notice' ); ?>

            <article class="waa-notice-listing-area">
                <header class="waa-dashboard-header waa-clearfix">
                    <span class="left-header-content">
                        <h1 class="entry-title"><?php _e( 'Announcement', 'waa' ); ?></h1>
                    </span>
                </header>

                <div class="notice-listing-top waa-clearfix">
                    <!-- Listing filters -->
                </div>

                <?php // show errors ?>

                <?php $announcement->show_announcement_template(); ?>
                <!-- Table for linsting  -->

                <!-- Pagination styles -->
            </article>

        <?php do_action( 'waa_after_listing_notice' ); ?>
    </div><!-- #primary .content-area -->
</div><!-- .waa-dashboard-wrap -->