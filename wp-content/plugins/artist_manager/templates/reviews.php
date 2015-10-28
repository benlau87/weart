<?php
$waa_template_reviews = waa_Template_reviews::init();
$waa_template_reviews->handle_status();
?>
<div class="waa-dashboard-wrap">
    <?php waa_get_template( 'dashboard-nav.php', array( 'active_menu' => 'reviews' ) ); ?>

    <div class="waa-dashboard-content waa-reviews-content">

        <article class="waa-reviews-area">
            <header class="waa-dashboard-header">
                <h1 class="entry-title"><?php _e( 'Reviews', 'waa' ); ?></h1>
            </header><!-- .waa-dashboard-header -->

            <?php $waa_template_reviews->reviews_view(); ?>

        </article>

    </div><!-- .waa-dashboard-content -->
</div><!-- .waa-dashboard-wrap -->