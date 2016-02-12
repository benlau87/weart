<?php
$user_id        = get_current_user_id();
$orders_counts  = waa_count_orders( $user_id );
$post_counts    = waa_count_posts( 'product', $user_id );
$comment_counts = waa_count_comments( 'product', $user_id );
$pageviews      = (int) waa_author_pageviews( $user_id );
$earning        = waa_author_total_sales( $user_id );

$products_url   = waa_get_navigation_url( 'products' );
$orders_url     = waa_get_navigation_url( 'orders' );
$reviews_url    = waa_get_navigation_url( 'reviews' );
?>

<div class="waa-dashboard-wrap">
    <?php waa_get_template( 'dashboard-nav.php', array( 'active_menu' => 'dashboard' ) ); ?>

    <div class="waa-dashboard-content">

        <?php
        if ( ! waa_is_seller_enabled( $user_id ) ) {
            waa_seller_not_enabled_notice();
        }
        ?>

        <article class="dashboard-content-area">
            <?php echo waa_get_profile_progressbar(); ?>
            <div class="waa-w6 waa-dash-left">
                <div class="dashboard-widget big-counter">
                    <ul class="list-inline">
                        <li>
                            <div class="title"><?php _e( 'Pageview', 'waa' ); ?></div>
                            <div class="count"><?php echo waa_number_format( $pageviews ); ?></div>
                        </li>
                        <li>
                            <div class="title"><?php _e( 'Orders', 'waa' ); ?></div>
                            <div class="count">
                                <?php
                                $total = $orders_counts->{'wc-completed'} + $orders_counts->{'wc-processing'} + $orders_counts->{'wc-on-hold'};
                                echo number_format_i18n( $total, 0 );
                                ?>
                            </div>
                        </li>
                        <li>
                            <div class="title"><?php _e( 'Sales', 'waa' ); ?></div>
                            <div class="count"><?php echo woocommerce_price( $earning ); ?></div>
                        </li>
                        <li>
                            <div class="title"><?php _e( 'Earning', 'waa' ); ?></div>
                            <div class="count"><?php echo waa_get_seller_balance( $user_id ); ?></div>
                        </li>

                        <?php do_action( 'waa_seller_dashboard_widget_counter' ); ?>

                    </ul>
                </div> <!-- .big-counter -->

                <div class="dashboard-widget orders">
                    <div class="widget-title"><i class="ui ui-shopping-cart"></i> <?php _e( 'Orders', 'waa' ); ?></div>

                    <?php
                    $order_data = array(
                        array( 'value' => $orders_counts->{'wc-completed'}, 'color' => '#73a724'),
                        array( 'value' => $orders_counts->{'wc-pending'}, 'color' => '#999'),
                        array( 'value' => $orders_counts->{'wc-processing'}, 'color' => '#21759b'),
                        array( 'value' => $orders_counts->{'wc-cancelled'}, 'color' => '#d54e21'),
                        array( 'value' => $orders_counts->{'wc-refunded'}, 'color' => '#e6db55'),
                        array( 'value' => $orders_counts->{'wc-on-hold'}, 'color' => '#f0ad4e'),
                    );
										$store_settings = waa_get_store_info( $user_id );
										$user_meta = get_user_meta($user_id);
//										print "<pre>";
//										print_r($store_settings);
//										print_r($user_meta);
//										print "</pre>";
                    ?>

                    <div class="content-half-part">
                        <ul class="list-unstyled list-count">
                            <li>
                                <a href="<?php echo $orders_url; ?>">
                                    <span class="title"><?php _e( 'Total', 'waa' ); ?></span> <span class="count"><?php echo $orders_counts->total; ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo add_query_arg( array( 'order_status' => 'wc-completed' ), $orders_url ); ?>" style="color: <?php echo $order_data[0]['color']; ?>">
                                    <span class="title"><?php _e( 'Completed', 'waa' ); ?></span> <span class="count"><?php echo number_format_i18n( $orders_counts->{'wc-completed'}, 0 ); ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo add_query_arg( array( 'order_status' => 'wc-pending' ), $orders_url ); ?>" style="color: <?php echo $order_data[1]['color']; ?>">
                                    <span class="title"><?php _e( 'Pending', 'waa' ); ?></span> <span class="count"><?php echo number_format_i18n( $orders_counts->{'wc-pending'}, 0 );; ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo add_query_arg( array( 'order_status' => 'wc-processing' ), $orders_url ); ?>" style="color: <?php echo $order_data[2]['color']; ?>">
                                    <span class="title"><?php _e( 'Processing', 'waa' ); ?></span> <span class="count"><?php echo number_format_i18n( $orders_counts->{'wc-processing'}, 0 );; ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo add_query_arg( array( 'order_status' => 'wc-cancelled' ), $orders_url ); ?>" style="color: <?php echo $order_data[3]['color']; ?>">
                                    <span class="title"><?php _e( 'Cancelled', 'waa' ); ?></span> <span class="count"><?php echo number_format_i18n( $orders_counts->{'wc-cancelled'}, 0 ); ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo add_query_arg( array( 'order_status' => 'wc-refunded' ), $orders_url ); ?>" style="color: <?php echo $order_data[4]['color']; ?>">
                                    <span class="title"><?php _e( 'Refunded', 'waa' ); ?></span> <span class="count"><?php echo number_format_i18n( $orders_counts->{'wc-refunded'}, 0 ); ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo add_query_arg( array( 'order_status' => 'wc-on-hold' ), $orders_url ); ?>" style="color: <?php echo $order_data[5]['color']; ?>">
                                    <span class="title"><?php _e( 'On hold', 'waa' ); ?></span> <span class="count"><?php echo number_format_i18n( $orders_counts->{'wc-on-hold'}, 0 ); ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="content-half-part">
                        <canvas id="order-stats"></canvas>
                    </div>
                </div> <!-- .orders -->
<?php /*
                <div class="dashboard-widget reviews">
                    <div class="widget-title"><i class="ui ui-comments"></i> <?php _e( 'Reviews', 'waa' ); ?></div>

                    <ul class="list-unstyled list-count">
                        <li>
                            <a href="<?php echo $reviews_url; ?>">
                                <span class="title"><?php _e( 'All', 'waa' ); ?></span> <span class="count"><?php echo $comment_counts->total; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo add_query_arg( array( 'comment_status' => 'hold' ), $reviews_url ); ?>">
                                <span class="title"><?php _e( 'Pending', 'waa' ); ?></span> <span class="count"><?php echo $comment_counts->moderated; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo add_query_arg( array( 'comment_status' => 'spam' ), $reviews_url ); ?>">
                                <span class="title"><?php _e( 'Spam', 'waa' ); ?></span> <span class="count"><?php echo $comment_counts->spam; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo add_query_arg( array( 'comment_status' => 'trash' ), $reviews_url ); ?>">
                                <span class="title"><?php _e( 'Trash', 'waa' ); ?></span> <span class="count"><?php echo $comment_counts->trash; ?></span>
                            </a>
                        </li>
                    </ul>
                </div> <!-- .reviews -->
								*/ ?>

                <div class="dashboard-widget products">
                    <div class="widget-title">
                        <i class="icon-briefcase"></i> <?php _e( 'Products', 'waa' ); ?>

                        <span class="pull-right">
                            <a href="<?php echo waa_get_navigation_url( 'new-product' ); ?>"><?php _e( '+ Add new product', 'waa' ); ?></a>
                        </span>
                    </div>

                    <ul class="list-unstyled list-count">
                        <li>
                            <a href="<?php echo $products_url; ?>">
                                <span class="title"><?php _e( 'Total', 'waa' ); ?></span> <span class="count"><?php echo $post_counts->total; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo add_query_arg( array( 'post_status' => 'publish' ), $products_url ); ?>">
                                <span class="title"><?php _e( 'Live', 'waa' ); ?></span> <span class="count"><?php echo $post_counts->publish; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo add_query_arg( array( 'post_status' => 'draft' ), $products_url ); ?>">
                                <span class="title"><?php _e( 'Offline', 'waa' ); ?></span> <span class="count"><?php echo $post_counts->draft; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo add_query_arg( array( 'post_status' => 'pending' ), $products_url ); ?>">
                                <span class="title"><?php _e( 'Pending Review', 'waa' ); ?></span> <span class="count"><?php echo $post_counts->pending; ?></span>
                            </a>
                        </li>
                    </ul>
                </div> <!-- .products -->

            </div> <!-- .col-md-6 -->

            <div class="waa-w6 waa-dash-right">
                <div class="dashboard-widget sells-graph">
                    <div class="widget-title"><i class="ui ui-credit-card"></i> <?php _e( 'Sales', 'waa' ); ?></div>

                    <?php
                    require_once waa_DIR . '/includes/reports.php';

                    waa_dashboard_sales_overview();
                    ?>
                </div> <!-- .sells-graph -->

                <div class="dashboard-widget waa-announcement-widget">
                    <div class="widget-title">
                        <i class="icon-briefcase"></i> <?php _e( 'Latest Announcement', 'waa' ); ?>

                        <span class="pull-right">
                            <a href="<?php echo waa_get_navigation_url( 'announcement' ); ?>"><?php _e( 'See All', 'waa' ); ?></a>
                        </span>
                    </div>
                    <?php
                        $template_notice = waa_Template_Notice::init();
                        $query = $template_notice->get_announcement_by_users(3);
                    ?>
                    <?php if ( $query->posts ): ?>
                        <ul class="list-unstyled">
                            <?php foreach ( $query->posts as $notice ): ?>
                                <?php
                                    $notice_url =  trailingslashit( waa_get_navigation_url( 'single-announcement' ).''.$notice->ID );
                                 ?>
                                <li>
                                    <div class="waa-dashboard-announce-content waa-left">
                                        <a href="<?php echo $notice_url; ?>"><h3><?php echo $notice->post_title; ?></h3></a>
                                        <?php echo wp_trim_words( $notice->post_content, 6, '...' ); ?>
                                    </div>
                                    <div class="waa-dashboard-announce-date waa-right <?php echo ( $notice->status == 'unread' ) ? 'waa-dashboard-announce-unread' : 'waa-dashboard-announce-read'; ?>">
                                        <div class="announce-day"><?php echo date( 'd', strtotime( $notice->post_date ) ); ?></div>
                                        <div class="announce-month"><?php echo date( 'l', strtotime( $notice->post_date ) ); ?></div>
                                        <div class="announce-year"><?php echo date( 'Y', strtotime( $notice->post_date ) ); ?></div>
                                    </div>
                                    <div class="waa-clearfix"></div>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    <?php else: ?>
                        <div class="waa-no-announcement">
                            <div class="annoument-no-wrapper">
                                <i class="ui ui-bell waa-announcement-icon"></i>
                                <p><?php _e( 'No announcement found', 'waa' ) ?></p>
                            </div>
                        </div>
                    <?php endif ?>
                </div> <!-- .products -->

            </div>
        </article><!-- .dashboard-content-area -->
    </div><!-- .waa-dashboard-content -->
</div><!-- .waa-dashboard-wrap -->

<script type="text/javascript">
    jQuery(function($) {
        var order_stats = <?php echo json_encode( $order_data ); ?>;

        var ctx = $("#order-stats").get(0).getContext("2d");
        new Chart(ctx).Doughnut(order_stats);
    });
</script>