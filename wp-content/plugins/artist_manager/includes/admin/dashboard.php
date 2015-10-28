<?php
//wp_widget_rss_output
function waa_admin_dash_metabox( $title = '', $callback = null ) {
    ?>
    <div class="postbox">
        <h3 class="hndle"><span><?php echo esc_html( $title ); ?></span></h3>
        <div class="inside">
            <div class="main">
                <?php if ( is_callable( $callback ) ) {
                    call_user_func( $callback );
                } ?>
            </div> <!-- .main -->
        </div> <!-- .inside -->
    </div> <!-- .postbox -->
    <?php
}

function waa_admin_dash_metabox_report() {
    waa_admin_report();
    ?>
    <div class="chart-container">
        <div class="chart-placeholder main" style="width: 100%; height: 350px;"></div>
    </div>
    <?php
}

function waa_admin_dash_widget_news() {
    #wp_widget_rss_output( 'http://www.weare-art.com/tag/waa/feed/', array( 'items' => 5, 'show_summary' => false, 'show_date' => true ) );
}

function waa_admin_dash_metabox_glance() {
    $user_count = count_users();
    $withdraw_counts = waa_get_withdraw_count();
    $seller_counts = waa_get_seller_count();
    $total_seller = isset( $user_count['avail_roles']['seller'] ) ? $user_count['avail_roles']['seller'] : 0;
    ?>

    <div class="waa-left">
        <h4><?php _e( 'Sellers', 'waa' ); ?></h4>

        <ul>
            <li class="seller-count">
                <div class="dashicons dashicons-businessman"></div>
                <a href="<?php echo admin_url( 'admin.php?page=waa-sellers' ); ?>"><?php printf( _n( __( '%d Total Seller', 'waa' ), __( '%d Total Sellers', 'waa' ), $total_seller, 'waa' ), $total_seller ); ?></a>
            </li>
            <li class="seller-count mark-green">
                <div class="dashicons dashicons-awards"></div>
                <a href="<?php echo admin_url( 'admin.php?page=waa-sellers' ); ?>">
                    <?php
                    if ( $seller_counts['yes'] ) {
                        printf( _n( __( '%d Active Seller', 'waa' ), __( '%d Active Sellers', 'waa' ), $seller_counts['yes'], 'waa' ), $seller_counts['yes'] );
                    } else {
                        _e( 'No Active Seller', 'waa' );
                    }  ?>
                </a>
            </li>
            <li class="seller-count <?php echo ($seller_counts['no'] < 1) ? 'mark-green' : 'mark-red'; ?>">
                <div class="dashicons dashicons-editor-help"></div>
                <a href="<?php echo admin_url( 'admin.php?page=waa-sellers' ); ?>">
                    <?php
                    if ( $seller_counts['no'] ) {
                        printf( _n( __( '%d Pending Seller', 'waa' ), __( '%d Pending Sellers', 'waa' ), $seller_counts['no'], 'waa' ), $seller_counts['no'] );
                    } else {
                        _e( 'No Pending Seller', 'waa' );
                    }  ?>
                </a>
            </li>
        </ul>
    </div>

    <div class="waa-right">
        <h4><?php _e( 'Withdraw', 'waa' ); ?></h4>

        <ul>
            <li class="withdraw-pending <?php echo ($withdraw_counts['pending'] < 1) ? 'mark-green' : 'mark-red'; ?>">
                <div class="dashicons dashicons-visibility"></div>
                <a href="<?php echo admin_url( 'admin.php?page=waa-withdraw' ); ?>"><?php printf( __( '%d Pending Withdraw', 'waa' ), $withdraw_counts['pending'] ); ?></a>
            </li>
            <li class="withdraw-completed mark-green">
                <div class="dashicons dashicons-yes"></div>
                <a href="<?php echo admin_url( 'admin.php?page=waa-withdraw&amp;status=completed' ); ?>"><?php printf( __( '%d Completed Withdraw', 'waa' ), $withdraw_counts['completed'] ); ?></a>
            </li>
            <li class="withdraw-cancelled">
                <div class="dashicons dashicons-dismiss"></div>
                <a href="<?php echo admin_url( 'admin.php?page=waa-withdraw&amp;status=cancelled' ); ?>"><?php printf( __( '%d Cancelled Withdraw', 'waa' ), $withdraw_counts['cancelled'] ); ?></a>
            </li>
        </ul>
    </div>

    <?php
}

?>
<div class="wrap waa-dashboard">

    <h2><?php _e( 'Artist Dashboard', 'waa' ); ?></h2>

    <div class="metabox-holder">
        <div class="post-box-container">
            <div class="meta-box-sortables">
                <?php waa_admin_dash_metabox( __( 'At a Glance', 'waa' ), 'waa_admin_dash_metabox_glance' ); ?>
              
                <?php do_action( 'waa_admin_dashboard_metabox_left' ); ?>
            </div>
        </div> <!-- .post-box-container -->

        <div class="post-box-container">
            <div class="meta-box-sortables">
                <?php waa_admin_dash_metabox( __( 'Overview', 'waa' ), 'waa_admin_dash_metabox_report' ); ?>

                <?php do_action( 'waa_admin_dashboard_metabox_right' ); ?>
            </div>
        </div> <!-- .post-box-container -->

    </div> <!-- .metabox-holder -->

</div> <!-- .wrap -->