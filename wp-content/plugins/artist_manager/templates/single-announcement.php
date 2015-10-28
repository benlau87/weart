<?php
$notice_id = get_query_var( 'single-announcement' );
$notice = array();
$template_notice = waa_Template_Notice::init();

if( is_numeric( $notice_id ) ) {
    $notice = $template_notice ->get_single_announcement( $notice_id );
}

?>
<div class="waa-dashboard-wrap">
    <?php waa_get_template( 'dashboard-nav.php', array( 'active_menu' => 'announcement' ) ); ?>

    <div class="waa-dashboard-content waa-notice-listing">

        <?php do_action( 'waa_before_single_notice' ); ?>

            <?php if ( $notice ): ?>
                <?php $notice_data = reset( $notice ); ?>
                <?php
                    if( $notice_data->status == 'unread' ) {
                        $template_notice->update_notice_status( $notice_id, 'read' );
                    }
                 ?>
                <article class="waa-notice-single-notice-area">
                    <header class="waa-dashboard-header waa-clearfix">
                        <span class="left-header-content">
                            <h1 class="entry-title"><?php echo $notice_data->post_title; ?></h1>
                        </span>
                    </header>
                    <span class="waa-single-announcement-date"><i class="fa fa-calendar"></i> <?php echo date('d.m.Y ', strtotime( $notice_data->post_date ) ); ?></span>

                    <div class="entry-content">
                        <?php echo wpautop( $notice_data->post_content ); ?>
                    </div>

                    <div class="waa-announcement-link">
                        <a href="<?php echo waa_get_navigation_url( 'announcement' ) ?>" class="waa-btn waa-btn-theme"><?php _e( 'Back to all Notice', 'waa' ); ?></a>
                    </div>
                    <!-- Table for linsting  -->

                    <!-- Pagination styles -->
                </article>
            <?php else: ?>
                <article class="waa-notice-single-notice-area">
                    <header class="waa-dashboard-header waa-clearfix">
                        <span class="left-header-content">
                            <h1 class="entry-title"><?php _e( 'Notice', 'waa' ); ?></h1>
                        </span>
                    </header>
                    <div class="waa-error">
                        <?php echo sprintf( "<p>%s <a href='%s'>%s</a></p", __( 'No Notice found; ', 'waa' ), waa_get_navigation_url('announcement'), __( 'Back to all Notice', 'waa' ) ) ?>
                    </div>
                </article>
            <?php endif ?>

        <?php do_action( 'waa_after_listing_notice' ); ?>
    </div><!-- #primary .content-area -->
</div><!-- .waa-dashboard-wrap -->