<?php

/**
 * Returns the definitions for the reports and charts
 *
 * @since 1.0
 * @return array
 */
function waa_get_reports_charts() {
    $charts = array(
        'title'  => __( 'Sales', 'waa' ),
        'charts' => array(
            "overview"          => array(
                'title'       => __( 'Overview', 'waa' ),
                'description' => '',
                'hide_title'  => true,
                'function'    => 'waa_sales_overview'
            ),
            "sales_by_day"      => array(
                'title'       => __( 'Sales by day', 'waa' ),
                'description' => '',
                'function'    => 'waa_daily_sales'
            ),
            "top_sellers"       => array(
                'title'       => __( 'Top sellers', 'waa' ),
                'description' => '',
                'function'    => 'waa_top_sellers'
            ),
            "top_earners"       => array(
                'title'       => __( 'Top earners', 'waa' ),
                'description' => '',
                'function'    => 'waa_top_earners'
            )
        )
    );

    return apply_filters( 'waa_reports_charts', $charts );
}


/**
 * Generate SQL query and fetch the report data based on the arguments passed
 *
 * This function was cloned from WC_Admin_Report class.
 *
 * @since 1.0
 *
 * @global WPDB $wpdb
 * @global WP_User $current_user
 * @param array $args
 * @param string $start_date
 * @param string $end_date
 * @return obj
 */
function waa_get_order_report_data( $args = array(), $start_date, $end_date ) {
    global $wpdb, $current_user;

    $defaults = array(
        'data'         => array(),
        'where'        => array(),
        'where_meta'   => array(),
        'query_type'   => 'get_row',
        'group_by'     => '',
        'order_by'     => '',
        'limit'        => '',
        'filter_range' => false,
        'nocache'      => false,
        'debug'        => false
    );

    $args = wp_parse_args( $args, $defaults );

    extract( $args );

    if ( empty( $data ) )
        return false;

    $select = array();

    foreach ( $data as $key => $value ) {
        $distinct = '';

        if ( isset( $value['distinct'] ) )
            $distinct = 'DISTINCT';

        if ( $value['type'] == 'meta' )
            $get_key = "meta_{$key}.meta_value";
        elseif( $value['type'] == 'post_data' )
            $get_key = "posts.{$key}";
        elseif( $value['type'] == 'order_item_meta' )
            $get_key = "order_item_meta_{$key}.meta_value";
        elseif( $value['type'] == 'order_item' )
            $get_key = "order_items.{$key}";

        if ( $value['function'] )
            $get = "{$value['function']}({$distinct} {$get_key})";
        else
            $get = "{$distinct} {$get_key}";

        $select[] = "{$get} as {$value['name']}";
    }

    $query['select'] = "SELECT " . implode( ',', $select );
    $query['from']   = "FROM {$wpdb->posts} AS posts";

    // Joins
    $joins         = array();
    $joins['do']  = "LEFT JOIN {$wpdb->prefix}waa_orders AS do ON posts.ID = do.order_id";

    foreach ( $data as $key => $value ) {
        if ( $value['type'] == 'meta' ) {

            $joins["meta_{$key}"] = "LEFT JOIN {$wpdb->postmeta} AS meta_{$key} ON posts.ID = meta_{$key}.post_id";

        } elseif ( $value['type'] == 'order_item_meta' ) {

            $joins["order_items"] = "LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON posts.ID = order_items.order_id";
            $joins["order_item_meta_{$key}"] = "LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta_{$key} ON order_items.order_item_id = order_item_meta_{$key}.order_item_id";

        } elseif ( $value['type'] == 'order_item' ) {

            $joins["order_items"] = "LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON posts.ID = order_id";

        }
    }

    if ( ! empty( $where_meta ) ) {
        foreach ( $where_meta as $value ) {
            if ( ! is_array( $value ) )
                continue;

            $key = is_array( $value['meta_key'] ) ? $value['meta_key'][0] : $value['meta_key'];

            if ( isset( $value['type'] ) && $value['type'] == 'order_item_meta' ) {

                $joins["order_items"] = "LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON posts.ID = order_id";
                $joins["order_item_meta_{$key}"] = "LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta_{$key} ON order_items.order_item_id = order_item_meta_{$key}.order_item_id";

            } else {
                // If we have a where clause for meta, join the postmeta table
                $joins["meta_{$key}"] = "LEFT JOIN {$wpdb->postmeta} AS meta_{$key} ON posts.ID = meta_{$key}.post_id";
            }
        }
    }

    $query['join'] = implode( ' ', $joins );

    $query['where']  = "
        WHERE   posts.post_type     = 'shop_order'
        AND     posts.post_status   != 'trash'
        AND     do.seller_id = {$current_user->ID}
        AND     do.order_status IN ('" . implode( "','", apply_filters( 'woocommerce_reports_order_statuses', array( 'wc-completed', 'wc-processing', 'wc-on-hold' ) ) ) . "')
        ";

    if ( $filter_range ) {
        $query['where'] .= "
            AND     DATE(post_date) >= '" . $start_date . "'
            AND     DATE(post_date) <= '" . $end_date . "'
        ";
    }

    foreach ( $data as $key => $value ) {
        if ( $value['type'] == 'meta' ) {

            $query['where'] .= " AND meta_{$key}.meta_key = '{$key}'";

        } elseif ( $value['type'] == 'order_item_meta' ) {

            $query['where'] .= " AND order_items.order_item_type = '{$value['order_item_type']}'";
            $query['where'] .= " AND order_item_meta_{$key}.meta_key = '{$key}'";

        }
    }

    if ( ! empty( $where_meta ) ) {
        $relation = isset( $where_meta['relation'] ) ? $where_meta['relation'] : 'AND';

        $query['where'] .= " AND (";

        foreach ( $where_meta as $index => $value ) {
            if ( ! is_array( $value ) )
                continue;

            $key = is_array( $value['meta_key'] ) ? $value['meta_key'][0] : $value['meta_key'];

            if ( strtolower( $value['operator'] ) == 'in' ) {
                if ( is_array( $value['meta_value'] ) )
                    $value['meta_value'] = implode( "','", $value['meta_value'] );
                if ( ! empty( $value['meta_value'] ) )
                    $where_value = "IN ('{$value['meta_value']}')";
            } else {
                $where_value = "{$value['operator']} '{$value['meta_value']}'";
            }

            if ( ! empty( $where_value ) ) {
                if ( $index > 0 )
                    $query['where'] .= ' ' . $relation;

                if ( isset( $value['type'] ) && $value['type'] == 'order_item_meta' ) {
                    if ( is_array( $value['meta_key'] ) )
                        $query['where'] .= " ( order_item_meta_{$key}.meta_key   IN ('" . implode( "','", $value['meta_key'] ) . "')";
                    else
                        $query['where'] .= " ( order_item_meta_{$key}.meta_key   = '{$value['meta_key']}'";

                    $query['where'] .= " AND order_item_meta_{$key}.meta_value {$where_value} )";
                } else {
                    if ( is_array( $value['meta_key'] ) )
                        $query['where'] .= " ( meta_{$key}.meta_key   IN ('" . implode( "','", $value['meta_key'] ) . "')";
                    else
                        $query['where'] .= " ( meta_{$key}.meta_key   = '{$value['meta_key']}'";

                    $query['where'] .= " AND meta_{$key}.meta_value {$where_value} )";
                }
            }
        }

        $query['where'] .= ")";
    }

    if ( ! empty( $where ) ) {
        foreach ( $where as $value ) {
            if ( strtolower( $value['operator'] ) == 'in' ) {
                if ( is_array( $value['value'] ) )
                    $value['value'] = implode( "','", $value['value'] );
                if ( ! empty( $value['value'] ) )
                    $where_value = "IN ('{$value['value']}')";
            } else {
                $where_value = "{$value['operator']} '{$value['value']}'";
            }

            if ( ! empty( $where_value ) )
                $query['where'] .= " AND {$value['key']} {$where_value}";
        }
    }

    if ( $group_by ) {
        $query['group_by'] = "GROUP BY {$group_by}";
    }

    if ( $order_by ) {
        $query['order_by'] = "ORDER BY {$order_by}";
    }

    if ( $limit ) {
        $query['limit'] = "LIMIT {$limit}";
    }

    $query      = apply_filters( 'waa_reports_get_order_report_query', $query );
    $query      = implode( ' ', $query );
    $query_hash = md5( $query_type . $query );

    if ( $debug ) {
        // var_dump( $query );
        printf( '<pre>%s</pre>', print_r( $query, true ) );
    }

    if ( $debug || $nocache || ( false === ( $result = get_transient( 'waa_wc_report_' . $query_hash ) ) ) ) {
        $result = apply_filters( 'waa_reports_get_order_report_data', $wpdb->$query_type( $query ), $data );

        if ( $filter_range ) {
            if ( $end_date == date('Y-m-d', current_time( 'timestamp' ) ) ) {
                $expiration = 60 * 60 * 1; // 1 hour
            } else {
                $expiration = 60 * 60 * 24; // 24 hour
            }
        } else {
            $expiration = 60 * 60 * 24; // 24 hour
        }

        set_transient( 'waa_wc_report_' . $query_hash, $result, $expiration );
    }

    return $result;
}


/**
 * Generate sales overview report chart in report area
 *
 * @since 1.0
 *
 * @return void
 */
function waa_sales_overview() {
    $start_date = date( 'Y-m-01', current_time('timestamp') );
    $end_date = date( 'Y-m-d', strtotime( 'midnight', current_time( 'timestamp' ) ) );

    waa_report_sales_overview( $start_date, $end_date, __( 'This month\'s sales', 'waa' ) );
}


/**
 * Generate seller dashboard overview chart
 *
 * @since 1.0
 * @return void
 */
function waa_dashboard_sales_overview() {
    $start_date = date( 'Y-m-01', current_time('timestamp') );
    $end_date = date( 'Y-m-d', strtotime( 'midnight', current_time( 'timestamp' ) ) );

    waa_sales_overview_chart_data( $start_date, $end_date, 'day' );
}


/**
 * Generates daily sales report
 *
 * @since 1.0
 * @global WPDB $wpdb
 */
function waa_daily_sales() {
    global $wpdb;

    $start_date = date( 'Y-m-01', current_time('timestamp') );
    $end_date = date( 'Y-m-d', strtotime( 'midnight', current_time( 'timestamp' ) ) );

    if ( isset( $_POST['waa_report_filter'] ) ) {
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
    }
    ?>

    <form method="post" class="waa-form-inline report-filter waa-clearfix" action="">
        <div class="waa-form-group">
            <label for="from"><?php _e( 'From:', 'waa' ); ?></label> <input type="text" class="datepicker" name="start_date" id="from" readonly="readonly" value="<?php echo esc_attr( $start_date ); ?>" />
        </div>

        <div class="waa-form-group">
            <label for="to"><?php _e( 'To:', 'waa' ); ?></label>
            <input type="text" name="end_date" id="to" class="datepicker" readonly="readonly" value="<?php echo esc_attr( $end_date ); ?>" />

            <input type="submit" name="waa_report_filter" class="waa-btn waa-btn-success waa-btn-sm waa-theme" value="<?php _e( 'Show', 'waa' ); ?>" />
        </div>
    </form>
    <?php

    waa_report_sales_overview( $start_date, $end_date, __( 'Sales in this period', 'waa' ) );
}



/**
 * Sales overview factory function
 *
 * @since 1.0
 *
 * @global type $woocommerce
 * @global WPDB $wpdb
 * @global type $wp_locale
 * @global WP_User $current_user
 * @param type $start_date
 * @param type $end_date
 * @param type $heading
 */
function waa_report_sales_overview( $start_date, $end_date, $heading = '' ) {
    global $woocommerce, $wpdb, $wp_locale, $current_user;

    $total_sales = $total_orders = $order_items = $discount_total = $shipping_total = 0;

    $order_totals = waa_get_order_report_data( array(
        'data' => array(
            '_order_total' => array(
                'type'     => 'meta',
                'function' => 'SUM',
                'name'     => 'total_sales'
            ),
            '_order_shipping' => array(
                'type'     => 'meta',
                'function' => 'SUM',
                'name'     => 'total_shipping'
            ),
            'ID' => array(
                'type'     => 'post_data',
                'function' => 'COUNT',
                'name'     => 'total_orders'
            )
        ),
        'filter_range' => true,
        // 'debug' => true
    ), $start_date, $end_date );

    $total_sales    = $order_totals->total_sales;
    $total_shipping = $order_totals->total_shipping;
    $total_orders   = absint( $order_totals->total_orders );
    $total_items    = absint( waa_get_order_report_data( array(
        'data' => array(
            '_qty' => array(
                'type'            => 'order_item_meta',
                'order_item_type' => 'line_item',
                'function'        => 'SUM',
                'name'            => 'order_item_qty'
            )
        ),
        'query_type' => 'get_var',
        'filter_range' => true
    ), $start_date, $end_date ) );

    // Get discount amounts in range
    $total_coupons = waa_get_order_report_data( array(
        'data' => array(
            'discount_amount' => array(
                'type'            => 'order_item_meta',
                'order_item_type' => 'coupon',
                'function'        => 'SUM',
                'name'            => 'discount_amount'
            )
        ),
        'where' => array(
            array(
                'key'      => 'order_item_type',
                'value'    => 'coupon',
                'operator' => '='
            )
        ),
        'query_type' => 'get_var',
        'filter_range' => true
    ), $start_date, $end_date );

    $average_sales = $total_sales / ( 30 + 1 );

    $legend = apply_filters( 'waa-seller-dashboard-reports-left-sidebar', array(
        'sales_in_this_period' => array(
            'title' => sprintf( __( '%s sales in this period', 'waa' ), '<strong>' . wc_price( $total_sales ) . '</strong>' ),
        ),

        'average_daily_sales' => array(
            'title' => sprintf( __( '%s average daily sales', 'waa' ), '<strong>' . wc_price( $average_sales ) . '</strong>' ),
        ),

        'orders_placed' => array(
            'title' => sprintf( __( '%s orders placed', 'waa' ), '<strong>' . $total_orders . '</strong>' ),
        ),

        'items_purchased' => array(
            'title' => sprintf( __( '%s items purchased', 'waa' ), '<strong>' . $total_items . '</strong>' ),
        ),

        'charged_for_shipping' => array(
            'title' => sprintf( __( '%s charged for shipping', 'waa' ), '<strong>' . wc_price( $total_shipping ) . '</strong>' ),
        )
    ) ); 
    ?>
    <div id="poststuff" class="waa-reports-wrap">
        <div class="waa-reports-sidebar report-left waa-left">
            <ul class="chart-legend">
                <?php foreach ($legend as $item) {
                    printf( '<li>%s</li>', $item['title'] );
                } ?>
            </ul>
        </div>

        <div class="doakn-reports-main report-right waa-right">
            <div class="postbox">
                <h3><span><?php echo $heading; ?></span></h3>

                <?php waa_sales_overview_chart_data( $start_date, $end_date, 'day' ); ?>
            </div>
        </div>
    </div>
    <?php
}



/**
 * Prepares chart data for sales overview
 *
 * @since 1.0
 *
 * @global type $wp_locale
 * @param type $start_date
 * @param type $end_date
 * @param type $group_by
 */
function waa_sales_overview_chart_data( $start_date, $end_date, $group_by ) {
    global $wp_locale;

    $start_date_to_time = strtotime( $start_date );
    $end_date_to_time = strtotime( $end_date );

    if ( $group_by == 'day' ) {
        $group_by_query       = 'YEAR(post_date), MONTH(post_date), DAY(post_date)';
        $chart_interval       = ceil( max( 0, ( $end_date_to_time - $start_date_to_time ) / ( 60 * 60 * 24 ) ) );
        $barwidth             = 60 * 60 * 24 * 1000;
    } else {
        $group_by_query = 'YEAR(post_date), MONTH(post_date)';
        $chart_interval = 0;
        $min_date             = $start_date_to_time;
        while ( ( $min_date   = strtotime( "+1 MONTH", $min_date ) ) <= $end_date_to_time ) {
            $chart_interval ++;
        }
        $barwidth             = 60 * 60 * 24 * 7 * 4 * 1000;
    }

    // Get orders and dates in range - we want the SUM of order totals, COUNT of order items, COUNT of orders, and the date
    $orders = waa_get_order_report_data( array(
        'data' => array(
            '_order_total' => array(
                'type'     => 'meta',
                'function' => 'SUM',
                'name'     => 'total_sales'
            ),
            'ID' => array(
                'type'     => 'post_data',
                'function' => 'COUNT',
                'name'     => 'total_orders',
                'distinct' => true,
            ),
            'post_date' => array(
                'type'     => 'post_data',
                'function' => '',
                'name'     => 'post_date'
            ),
        ),
        'group_by'     => $group_by_query,
        'order_by'     => 'post_date ASC',
        'query_type'   => 'get_results',
        'filter_range' => true,
        'debug' => false
    ), $start_date, $end_date );

    // Prepare data for report
    $order_counts      = waa_prepare_chart_data( $orders, 'post_date', 'total_orders', $chart_interval, $start_date_to_time, $group_by );
    $order_amounts     = waa_prepare_chart_data( $orders, 'post_date', 'total_sales', $chart_interval, $start_date_to_time, $group_by );

    // Encode in json format
    $chart_data = json_encode( array(
        'order_counts'      => array_values( $order_counts ),
        'order_amounts'     => array_values( $order_amounts )
    ) );

    $chart_colours = array(
        'order_counts'  => '#3498db',
        'order_amounts'   => '#1abc9c'
    );

    ?>
    <div class="chart-container">
        <div class="chart-placeholder main" style="width: 100%; height: 350px;"></div>
    </div>

    <script type="text/javascript">
        jQuery(function($) {

            var order_data = jQuery.parseJSON( '<?php echo $chart_data; ?>' );
            var series = [
                {
                    label: "<?php echo esc_js( __( 'Number of items sold', 'waa' ) ) ?>",
                    data: order_data.order_amounts,
                    shadowSize: 0,
                    hoverable: true,
                    points: { show: true, radius: 5, lineWidth: 1, fillColor: '#fff', fill: true },
                    lines: { show: true, lineWidth: 2, fill: false },
                    shadowSize: 0,
                    prepend_tooltip: "<?php echo get_woocommerce_currency_symbol(); ?>"
                },
                {
                    label: "<?php echo esc_js( __( 'Number of orders', 'waa' ) ) ?>",
                    data: order_data.order_counts,
                    shadowSize: 0,
                    hoverable: true,
                    points: { show: true, radius: 5, lineWidth: 2, fillColor: '#fff', fill: true },
                    lines: { show: true, lineWidth: 3, fill: false },
                    shadowSize: 0,
                    append_tooltip: " <?php echo __( 'sales', 'waa' ); ?>"
                },
            ];

            var main_chart = jQuery.plot(
                jQuery('.chart-placeholder.main'),
                series,
                {
                    legend: {
                        show: true,
                        position: 'nw'
                    },
                    series: {
                        lines: { show: true, lineWidth: 4, fill: false },
                        points: { show: true }
                    },
                    grid: {
                        borderColor: '#eee',
                        color: '#aaa',
                        borderWidth: 1,
                        hoverable: true,
                        show: true,
                        aboveData: false,
                    },
                    xaxis: {
                        color: '#aaa',
                        position: "bottom",
                        tickColor: 'transparent',
                        mode: "time",
                        timeformat: "<?php if ( $group_by == 'day' ) echo '%d %b'; else echo '%b'; ?>",
                        monthNames: <?php echo json_encode( array_values( $wp_locale->month_abbrev ) ) ?>,
                        tickLength: 1,
                        minTickSize: [1, "<?php echo $group_by; ?>"],
                        font: {
                            color: "#aaa"
                        }
                    },
                    yaxes: [
                        {
                            min: 0,
                            minTickSize: 1,
                            tickDecimals: 0,
                            color: '#d4d9dc',
                            font: { color: "#aaa" }
                        },
                        {
                            position: "right",
                            min: 0,
                            tickDecimals: 2,
                            alignTicksWithAxis: 1,
                            color: 'transparent',
                            font: { color: "#aaa" }
                        }
                    ],
                    colors: ["<?php echo $chart_colours['order_counts']; ?>", "<?php echo $chart_colours['order_amounts']; ?>"]
                }
            );

            jQuery('.chart-placeholder').resize();
        });

    </script>
    <?php
}

/**
 * Output the top sellers chart.
 *
 * @access public
 * @return void
 */
function waa_top_sellers() {

    global $start_date, $end_date, $woocommerce, $wpdb, $current_user;

    $start_date = isset( $_POST['start_date'] ) ? $_POST['start_date'] : '';
    $end_date   = isset( $_POST['end_date'] ) ? $_POST['end_date'] : '';

    if ( ! $start_date )
        $start_date = date( 'Ymd', strtotime( date( 'Ym', current_time( 'timestamp' ) ) . '01' ) );
    if ( ! $end_date )
         $end_date = date( 'Ymd', current_time( 'timestamp' ) );

    $start_date = strtotime( $start_date );
    $end_date = strtotime( $end_date );

    // Get order ids and dates in range
    $order_items = apply_filters( 'woocommerce_reports_top_sellers_order_items', $wpdb->get_results( "
        SELECT order_item_meta_2.meta_value as product_id, SUM( order_item_meta.meta_value ) as item_quantity FROM {$wpdb->prefix}woocommerce_order_items as order_items

        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta_2 ON order_items.order_item_id = order_item_meta_2.order_item_id
        LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
        LEFT JOIN {$wpdb->prefix}waa_orders AS do ON posts.ID = do.order_id

        WHERE   posts.post_type     = 'shop_order'
        AND     posts.post_status   != 'trash'
        AND     do.seller_id = {$current_user->ID}
        AND     do.order_status IN ('" . implode( "','", apply_filters( 'woocommerce_reports_order_statuses', array( 'wc-completed', 'wc-processing', 'wc-on-hold' ) ) ) . "')
        AND     post_date > '" . date('Y-m-d', $start_date ) . "'
        AND     post_date < '" . date('Y-m-d', strtotime('+1 day', $end_date ) ) . "'
        AND     order_items.order_item_type = 'line_item'
        AND     order_item_meta.meta_key = '_qty'
        AND     order_item_meta_2.meta_key = '_product_id'
        GROUP BY order_item_meta_2.meta_value
    " ), $start_date, $end_date );

    $found_products = array();

    if ( $order_items ) {
        foreach ( $order_items as $order_item ) {
            $found_products[ $order_item->product_id ] = $order_item->item_quantity;
        }
    }

    asort( $found_products );
    $found_products = array_reverse( $found_products, true );
    $found_products = array_slice( $found_products, 0, 25, true );
    reset( $found_products );
    ?>
    <form method="post" action="" class="report-filter waa-form-inline waa-clearfix">
        <div class="waa-form-group">
            <label for="from"><?php _e( 'From:', 'waa' ); ?></label>
            <input type="text" class="datepicker" name="start_date" id="from" readonly="readonly" value="<?php echo esc_attr( date('Y-m-d', $start_date) ); ?>" />
        </div>

        <div class="waa-form-group">
            <label for="to"><?php _e( 'To:', 'waa' ); ?></label>
            <input type="text" class="datepicker" name="end_date" id="to" readonly="readonly" value="<?php echo esc_attr( date('Y-m-d', $end_date) ); ?>" />
        </div>

        <input type="submit" class="waa-btn waa-btn-success waa-btn-sm waa-theme" value="<?php _e( 'Show', 'waa' ); ?>" />
    </form>


    <table class="table table-striped">
        <thead>
            <tr>
                <th><?php _e( 'Product', 'waa' ); ?></th>
                <th><?php _e( 'Sales', 'waa' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
                $max_sales = current( $found_products );
                foreach ( $found_products as $product_id => $sales ) {
                    $width = $sales > 0 ? ( $sales / $max_sales ) * 100 : 0;
                    $product_title = get_the_title( $product_id );

                    if ( $product_title ) {
                        $product_name = '<a href="' . get_permalink( $product_id ) . '">'. __( $product_title ) .'</a>';
                        $orders_link = admin_url( 'edit.php?s&post_status=all&post_type=shop_order&action=-1&s=' . urlencode( $product_title ) . '&shop_order_status=' . implode( ",", apply_filters( 'woocommerce_reports_order_statuses', array( 'wc-completed', 'wc-processing', 'wc-on-hold' ) ) ) );
                    } else {
                        $product_name = __( 'Product does not exist', 'waa' );
                        $orders_link = admin_url( 'edit.php?s&post_status=all&post_type=shop_order&action=-1&s=&shop_order_status=' . implode( ",", apply_filters( 'woocommerce_reports_order_statuses', array( 'wc-completed', 'wc-processing', 'wc-on-hold' ) ) ) );
                    }

                    $orders_link = apply_filters( 'waa_reports_order_link', $orders_link, $product_id, $product_title );
                    $orders_link = ''; //FIXME : order link

                    echo '<tr><th class="60%">' . $product_name . '</th><td width="1%"><span>' . esc_html( $sales ) . '</span></td><td width="30%"><div class="progress"><a class="progress-bar" href="' . esc_url( $orders_link ) . '" style="width:' . esc_attr( $width ) . '%">&nbsp;</a></div></td></tr>';
                }
            ?>
        </tbody>
    </table>
    <?php
}


/**
 * Output the top earners chart.
 *
 * @access public
 * @return void
 */
function waa_top_earners() {

    global $start_date, $end_date, $woocommerce, $wpdb, $current_user;

    $start_date = isset( $_POST['start_date'] ) ? $_POST['start_date'] : '';
    $end_date   = isset( $_POST['end_date'] ) ? $_POST['end_date'] : '';

    if ( ! $start_date )
        $start_date = date( 'Ymd', strtotime( date('Ym', current_time( 'timestamp' ) ) . '01' ) );
    if ( ! $end_date )
        $end_date = date( 'Ymd', current_time( 'timestamp' ) );

    $start_date = strtotime( $start_date );
    $end_date = strtotime( $end_date );

    // Get order ids and dates in range
    $order_items = apply_filters( 'woocommerce_reports_top_earners_order_items', $wpdb->get_results( "
        SELECT order_item_meta_2.meta_value as product_id, SUM( order_item_meta.meta_value ) as line_total FROM {$wpdb->prefix}woocommerce_order_items as order_items

        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta_2 ON order_items.order_item_id = order_item_meta_2.order_item_id
        LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
        LEFT JOIN {$wpdb->prefix}waa_orders AS do ON posts.ID = do.order_id

        WHERE   posts.post_type     = 'shop_order'
        AND     posts.post_status   != 'trash'
        AND     do.seller_id = {$current_user->ID}
        AND     do.order_status           IN ('" . implode( "','", apply_filters( 'woocommerce_reports_order_statuses', array( 'wc-completed', 'wc-processing', 'wc-on-hold' ) ) ) . "')
        AND     post_date > '" . date('Y-m-d', $start_date ) . "'
        AND     post_date < '" . date('Y-m-d', strtotime('+1 day', $end_date ) ) . "'
        AND     order_items.order_item_type = 'line_item'
        AND     order_item_meta.meta_key = '_line_total'
        AND     order_item_meta_2.meta_key = '_product_id'
        GROUP BY order_item_meta_2.meta_value
    " ), $start_date, $end_date );

    $found_products = array();

    if ( $order_items ) {
        foreach ( $order_items as $order_item ) {
            $found_products[ $order_item->product_id ] = $order_item->line_total;
        }
    }

    asort( $found_products );
    $found_products = array_reverse( $found_products, true );
    $found_products = array_slice( $found_products, 0, 25, true );
    reset( $found_products );
    ?>
    <form method="post" action="" class="report-filter waa-form-inline waa-clearfix">
        <div class="waa-form-group">
            <label for="from"><?php _e( 'From:', 'waa' ); ?></label>
            <input type="text" class="datepicker" name="start_date" id="from" readonly="readonly" value="<?php echo esc_attr( date('Y-m-d', $start_date) ); ?>" />
        </div>

        <div class="waa-form-group">
            <label for="to"><?php _e( 'To:', 'waa' ); ?></label>
            <input type="text" class="datepicker" name="end_date" id="to" readonly="readonly" value="<?php echo esc_attr( date('Y-m-d', $end_date) ); ?>" />
        </div>

        <input type="submit" class="waa-btn waa-btn-success waa-btn-sm waa-theme" value="<?php _e( 'Show', 'waa' ); ?>" />
    </form>

    <table class="table table-striped">
        <thead>
            <tr>
                <th><?php _e( 'Product', 'waa' ); ?></th>
                <th colspan="2"><?php _e( 'Sales', 'waa' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
                $max_sales = current( $found_products );
                foreach ( $found_products as $product_id => $sales ) {
                    $width = $sales > 0 ? ( round( $sales ) / round( $max_sales ) ) * 100 : 0;

                    $product_title = get_the_title( $product_id );

                    if ( $product_title ) {
                        $product_name = '<a href="'.get_permalink( $product_id ).'">'. __( $product_title ) .'</a>';
                        $orders_link = admin_url( 'edit.php?s&post_status=all&post_type=shop_order&action=-1&s=' . urlencode( $product_title ) . '&shop_order_status=' . implode( ",", apply_filters( 'woocommerce_reports_order_statuses', array( 'wc-completed', 'wc-processing', 'wc-on-hold' ) ) ) );
                    } else {
                        $product_name = __( 'Product no longer exists', 'waa' );
                        $orders_link = admin_url( 'edit.php?s&post_status=all&post_type=shop_order&action=-1&s=&shop_order_status=' . implode( ",", apply_filters( 'woocommerce_reports_order_statuses', array( 'wc-completed', 'wc-processing', 'wc-on-hold' ) ) ) );
                    }

                    $orders_link = apply_filters( 'woocommerce_reports_order_link', $orders_link, $product_id, $product_title );

                    echo '<tr><th>' . $product_name . '</th><td width="1%"><span>' . woocommerce_price( $sales ) . '</span></td><td class="bars"><a href="' . esc_url( $orders_link ) . '" style="width:' . esc_attr( $width ) . '%">&nbsp;</a></td></tr>';
                }
            ?>
        </tbody>
    </table>
    <?php
}