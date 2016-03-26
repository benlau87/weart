<?php

require_once dirname(__FILE__) . '/order-functions.php';
require_once dirname(__FILE__) . '/withdraw-functions.php';


/**
 * Check if a user is seller
 *
 * @param int $user_id
 * @return boolean
 */
function waa_is_user_seller( $user_id ) {
    if ( ! user_can( $user_id, 'dokandar' ) ) {
        return false;
    }

    return true;
}

/**
 * Check if a user is customer
 *
 * @param int $user_id
 * @return boolean
 */
function waa_is_user_customer( $user_id ) {
    if ( ! user_can( $user_id, 'customer' ) ) {
        return false;
    }

    return true;
}

/**
 * Check if current user is the product author
 *
 * @global WP_Post $post
 * @param int $product_id
 * @return boolean
 */
function waa_is_product_author( $product_id = 0 ) {
    global $post;

    if ( !$product_id ) {
        $author = $post->post_author;
    } else {
        $author = get_post_field( 'post_author', $product_id );
    }

    if ( $author == get_current_user_id() ) {
        return true;
    }

    return false;
}

/**
 * Check if it's a store page
 *
 * @return boolean
 */
function waa_is_store_page() {
    $custom_store_url = waa_get_option( 'custom_store_url', 'waa_selling', 'store' );
    if ( get_query_var( $custom_store_url ) ) {
        return true;
    }

    return false;
}

/**
 * Check if current page is store review page
 *
 * @since 2.2
 *
 * @return boolean
 */
function waa_is_store_review_page() {
    if ( get_query_var( 'store_review' ) == 'true' ) {
        return true;
    }

    return false;
}

/**
 * Redirect to login page if not already logged in
 *
 * @return void
 */
function waa_redirect_login() {
    if ( ! is_user_logged_in() ) {
        wp_redirect( waa_get_page_url( 'myaccount', 'woocommerce' ) );
        exit;
    }
}

/**
 * If the current user is not seller, redirect to homepage
 *
 * @param string $redirect
 */
function waa_redirect_if_not_seller( $redirect = '' ) {
    if ( !waa_is_user_seller( get_current_user_id() ) ) {
        $redirect = empty( $redirect ) ? home_url( '/' ) : $redirect;

        wp_redirect( $redirect );
        exit;
    }
}

/**
 * Handles the product delete action
 *
 * @return void
 */
function waa_delete_product_handler() {
    if ( isset( $_GET['action'] ) && $_GET['action'] == 'waa-delete-product' ) {
        $product_id = isset( $_GET['product_id'] ) ? (int) $_GET['product_id'] : 0;

        if ( !$product_id ) {
            wp_redirect( add_query_arg( array( 'message' => 'error' ), waa_get_navigation_url( 'products' ) ) );
            return;
        }

        if ( !wp_verify_nonce( $_GET['_wpnonce'], 'waa-delete-product' ) ) {
            wp_redirect( add_query_arg( array( 'message' => 'error' ), waa_get_navigation_url( 'products' ) ) );
            return;
        }

        if ( !waa_is_product_author( $product_id ) ) {
            wp_redirect( add_query_arg( array( 'message' => 'error' ), waa_get_navigation_url( 'products' ) ) );
            return;
        }

        wp_delete_post( $product_id );
        wp_redirect( add_query_arg( array( 'message' => 'product_deleted' ), waa_get_navigation_url( 'products' ) ) );
        exit;
    }
}

/**
 * Count post type from a user
 *
 * @global WPDB $wpdb
 * @param string $post_type
 * @param int $user_id
 * @return array
 */
function waa_count_posts( $post_type, $user_id ) {
    global $wpdb;

    $cache_key = 'waa-count-' . $post_type . '-' . $user_id;
    $counts = wp_cache_get( $cache_key, 'waa' );

    if ( false === $counts ) {
        $query = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s AND post_author = %d GROUP BY post_status";
        $results = $wpdb->get_results( $wpdb->prepare( $query, $post_type, $user_id ), ARRAY_A );
        $counts = array_fill_keys( get_post_stati(), 0 );

        $total = 0;
        foreach ( $results as $row ) {
            $counts[ $row['post_status'] ] = (int) $row['num_posts'];
            $total += (int) $row['num_posts'];
        }

        $counts['total'] = $total;
        $counts = (object) $counts;
        wp_cache_set( $cache_key, $counts, 'waa' );
    }

    return $counts;
}


/**
 * Count post type from a user
 *
 * @global WPDB $wpdb
 * @param string $post_type
 * @param int $user_id
 * @return array
 */
function waa_count_published_posts( $post_type, $user_id ) {
    global $wpdb;

    $cache_key = 'waa-count-' . $post_type . '-' . $user_id;
    $counts = wp_cache_get( $cache_key, 'waa' );

    if ( false === $counts ) {
        $query = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s AND post_author = %d AND post_status = 'publish' GROUP BY post_status";
        $results = $wpdb->get_results( $wpdb->prepare( $query, $post_type, $user_id ), ARRAY_A );
        $counts = array_fill_keys( get_post_stati(), 0 );

        $total = 0;
        foreach ( $results as $row ) {
            $counts[ $row['post_status'] ] = (int) $row['num_posts'];
            $total += (int) $row['num_posts'];
        }

        $counts['total'] = $total;
        $counts = (object) $counts;
        wp_cache_set( $cache_key, $counts, 'waa' );
    }

    return $counts;
}

/**
 * Get comment count based on post type and user id
 *
 * @global WPDB $wpdb
 * @global WP_User $current_user
 * @param string $post_type
 * @param int $user_id
 * @return array
 */
function waa_count_comments( $post_type, $user_id ) {
    global $wpdb, $current_user;

    $cache_key = 'waa-count-comments-' . $post_type . '-' . $user_id;
    $counts = wp_cache_get( $cache_key, 'waa' );

    if ( $counts === false ) {
        $query = "SELECT c.comment_approved, COUNT( * ) AS num_comments
            FROM $wpdb->comments as c, $wpdb->posts as p
            WHERE p.post_author = %d AND
                p.post_status = 'publish' AND
                c.comment_post_ID = p.ID AND
                p.post_type = %s
            GROUP BY c.comment_approved";

        $count = $wpdb->get_results( $wpdb->prepare( $query, $user_id, $post_type ), ARRAY_A );

        $counts = array('moderated' => 0, 'approved' => 0, 'spam' => 0, 'trash' => 0, 'total' => 0);
        $statuses = array('0' => 'moderated', '1' => 'approved', 'spam' => 'spam', 'trash' => 'trash', 'post-trashed' => 'post-trashed');
        $total = 0;
        foreach ($count as $row) {
            if ( isset( $statuses[$row['comment_approved']] ) ) {
                $counts[$statuses[$row['comment_approved']]] = (int) $row['num_comments'];
                $total += (int) $row['num_comments'];
            }
        }
        $counts['total'] = $total;

        $counts = (object) $counts;
        wp_cache_set( $cache_key, $counts, 'waa' );
    }

    return $counts;
}

/**
 * Get total pageview for a seller
 *
 * @global WPDB $wpdb
 * @param int $seller_id
 * @return int
 */
function waa_author_pageviews( $seller_id ) {
    global $wpdb;

    $cache_key = 'waa-pageview-' . $seller_id;
    $pageview = wp_cache_get( $cache_key, 'waa' );

    if ( $pageview === false ) {
        $sql = "SELECT SUM(meta_value) as pageview
            FROM {$wpdb->postmeta} AS meta
            LEFT JOIN {$wpdb->posts} AS p ON p.ID = meta.post_id
            WHERE meta.meta_key = 'pageview' AND p.post_author = %d AND p.post_status IN ('publish', 'pending', 'draft')";

        $count = $wpdb->get_row( $wpdb->prepare( $sql, $seller_id ) );
        $pageview = $count->pageview;

        wp_cache_set( $cache_key, $pageview, 'waa' );
    }

    return $pageview;
}

/**
 * Get total sales amount of a seller
 *
 * @global WPDB $wpdb
 * @param int $seller_id
 * @return float
 */
function waa_author_total_sales( $seller_id ) {
    global $wpdb;

    $cache_key = 'waa-earning-' . $seller_id;
    $earnings = wp_cache_get( $cache_key, 'waa' );

    if ( $earnings === false ) {

        $sql = "SELECT SUM(oim.meta_value) as earnings
                FROM {$wpdb->prefix}woocommerce_order_items AS oi
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim ON oim.order_item_id = oi.order_item_id
                LEFT JOIN {$wpdb->prefix}waa_orders do ON oi.order_id = do.order_id
                WHERE do.seller_id = %d AND oim.meta_key = '_line_total' AND do.order_status IN ('wc-completed', 'wc-processing', 'wc-on-hold')";

        $count = $wpdb->get_row( $wpdb->prepare( $sql, $seller_id ) );
        $earnings = $count->earnings;

        wp_cache_set( $cache_key, $earnings, 'waa' );
    }

    return $earnings;
}

/**
 * Generate waa sync table
 *
 * @global WPDB $wpdb
 */
function waa_generate_sync_table() {
    global $wpdb;

    $sql = "SELECT oi.order_id, p.ID as product_id, p.post_title, p.post_author as seller_id,
                oim2.meta_value as order_total, p.post_status as order_status
            FROM {$wpdb->prefix}woocommerce_order_items oi
            INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim ON oim.order_item_id = oi.order_item_id
            INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim2 ON oim2.order_item_id = oi.order_item_id
            INNER JOIN $wpdb->posts p ON oi.order_id = p.ID
            WHERE
                oim.meta_key = '_product_id' AND
                oim2.meta_key = '_line_total'
            GROUP BY oi.order_id";

    $orders = $wpdb->get_results( $sql );
    $table_name = $wpdb->prefix . 'waa_orders';

    $wpdb->query( 'TRUNCATE TABLE ' . $table_name );

    if ( $orders ) {
        foreach ($orders as $order) {
            $percentage = waa_get_seller_percentage( $order->seller_id );

            $wpdb->insert(
                $table_name,
                array(
                    'order_id'     => $order->order_id,
                    'seller_id'    => $order->seller_id,
                    'order_total'  => $order->order_total,
                    'net_amount'   => ($order->order_total * $percentage)/100,
                    'order_status' => $order->order_status,
                ),
                array(
                    '%d',
                    '%d',
                    '%f',
                    '%f',
                    '%s',
                )
            );
        } // foreach
    } // if
}

if ( !function_exists( 'waa_get_seller_percentage' ) ) :

/**
 * Get store seller percentage settings
 *
 * @param int $seller_id
 * @return int
 */
function waa_get_seller_percentage( $seller_id = 0 ) {
    $global_percentage = (int) waa_get_option( 'seller_percentage', 'waa_selling', '90' );

    if ( ! $seller_id ) {
        return $global_percentage;
    }

    $seller_percentage = (int) get_user_meta( $seller_id, 'waa_seller_percentage', true );
    if ( $seller_percentage ) {
        return $seller_percentage;
    }

    return $global_percentage;
}

endif;

/**
 * Get product status based on user id and settings
 *
 * @return string
 */
function waa_get_new_post_status() {
    $user_id = get_current_user_id();

    // trusted seller
    if ( waa_is_seller_trusted( $user_id ) ) {
        return 'publish';
    }

    // if not trusted, send the option
    $status = waa_get_option( 'product_status', 'waa_selling', 'pending' );

    return $status;
}

/**
 * Function to get the client ip address
 *
 * @return string
 */
function waa_get_client_ip() {
    $ipaddress = '';

    if ( getenv( 'HTTP_CLIENT_IP' ) )
        $ipaddress = getenv( 'HTTP_CLIENT_IP' );
    else if ( getenv( 'HTTP_X_FORWARDED_FOR' ) )
        $ipaddress = getenv( 'HTTP_X_FORWARDED_FOR' & quot );
    else if ( getenv( 'HTTP_X_FORWARDED' ) )
        $ipaddress = getenv( 'HTTP_X_FORWARDED' );
    else if ( getenv( 'HTTP_FORWARDED_FOR' ) )
        $ipaddress = getenv( 'HTTP_FORWARDED_FOR' );
    else if ( getenv( 'HTTP_X_CLUSTER_CLIENT_IP' ) )
        $ipaddress = getenv( 'HTTP_FORWARDED_FOR' );
    else if ( getenv( 'HTTP_FORWARDED' ) )
        $ipaddress = getenv( 'HTTP_FORWARDED' );
    else if ( getenv( 'REMOTE_ADDR' ) )
        $ipaddress = getenv( 'REMOTE_ADDR' );
    else
        $ipaddress = 'UNKNOWN';

    return $ipaddress;
}

/**
 * Datetime format helper function
 *
 * @param string $datetime
 * @return string
 */
function waa_format_time( $datetime ) {
    $timestamp = strtotime( $datetime );

    $date_format = get_option( 'date_format' );
    $time_format = get_option( 'time_format' );

    return date_i18n( $date_format . ', ' . $time_format, $timestamp ) . __(' Uhr', 'waa');
}

/**
 * generate a input box based on arguments
 *
 * @param int $post_id
 * @param string $meta_key
 * @param array $attr
 * @param string $type
 */
function waa_post_input_box( $post_id, $meta_key, $attr = array(), $type = 'text', $currency = false  ) {
    $placeholder = isset( $attr['placeholder'] ) ? esc_attr( $attr['placeholder'] ) : '';
    $class       = isset( $attr['class'] ) ? esc_attr( $attr['class'] ) : 'waa-form-control';
    $name        = isset( $attr['name'] ) ? esc_attr( $attr['name'] ) : $meta_key;
		
		$waa_original_price = get_post_meta( $post_id, 'waa_original_price', true );
		if($waa_original_price && $meta_key == '_regular_price')
			$value = $waa_original_price;
		else
			$value       = isset( $attr['value'] ) ? $attr['value'] : get_post_meta( $post_id, $meta_key, true );
    $size        = isset( $attr['size'] ) ? $attr['size'] : 30;
    $required        = isset( $attr['required'] ) ? 'required' : '';


    switch ($type) {
        case 'text':
            ?>
            <input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo esc_attr( $value ); ?>" class="<?php echo $class; ?>" placeholder="<?php echo $placeholder; ?>" <?php echo $required ?>>
            <?php
            break;

        case 'textarea':
            $rows = isset( $attr['rows'] ) ? absint( $attr['rows'] ) : 4;
            ?>
            <textarea name="<?php echo $name; ?>" id="<?php echo $name; ?>" rows="<?php echo $rows; ?>" class="<?php echo $class; ?>" placeholder="<?php echo $placeholder; ?>"><?php echo esc_textarea( $value ); ?></textarea>
            <?php
            break;

        case 'checkbox':
            $label = isset( $attr['label'] ) ? $attr['label'] : '';
            $class = ( $class == 'waa-form-control' ) ? '' : $class;
            ?>

            <label class="<?php echo $class; ?>" for="<?php echo $name; ?>">
                <input type="hidden" name="<?php echo $name; ?>" value="no">
                <input name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="yes" type="checkbox"<?php checked( $value, 'yes' ); ?>>
                <?php echo $label; ?>
            </label>

            <?php
            break;

        case 'select':
            $options = is_array( $attr['options'] ) ? $attr['options'] : array();
            ?>
            <select name="<?php echo $name; ?>" id="<?php echo $name; ?>" class="<?php echo $class; ?>">
                <?php foreach ($options as $key => $label) { ?>
                    <option value="<?php echo esc_attr( $key ); ?>"<?php selected( $value, $key ); ?>><?php echo $label; ?></option>
                <?php } ?>
            </select>

            <?php
            break;

        case 'number':
            $min = isset( $attr['min'] ) ? $attr['min'] : 0;
            $step = isset( $attr['step'] ) ? $attr['step'] : 'any';
            $woocs = new WOOCS();
            $currencies = $woocs->get_currencies();
            ?>
            <input type="number" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?= $currency ? number_format(($value / $currencies[$woocs->current_currency]['rate']), 2, '.', ',') : esc_attr( $value ) ; ?>" class="<?php echo $class; ?>" placeholder="<?php echo $placeholder; ?>" min="<?php echo esc_attr( $min ); ?>" step="<?php echo esc_attr( $step ); ?>" size="<?php echo esc_attr( $size ); ?>">
            <?php
            break;

        case 'radio':
            $options = is_array( $attr['options'] ) ? $attr['options'] : array();
            foreach ( $options as $key => $label ) {
            ?>
            <label class="<?php echo $class; ?>" for="<?php echo $key; ?>">
                <input name="<?php echo $name; ?>" id="<?php echo $key; ?>" value="<?php echo $key; ?>" type="radio"<?php checked( $value, $key ); ?>>
                <?php echo $label; ?>
            </label>

            <?php
            }
            break;

    }
}

/**
 * Get user friendly post status based on post
 *
 * @param string $status
 * @return string
 */
function waa_get_post_status( $status ) {
    switch ($status) {
        case 'publish':
            return __( 'Online', 'waa' );
            break;

        case 'draft':
            return __( 'Draft', 'waa' );
            break;

        case 'pending':
            return __( 'Pending Review', 'waa' );
            break;

        case 'future':
            return __( 'Scheduled', 'waa' );
            break;

        default:
            return '';
            break;
    }
}

/**
 * Get user friendly post status label based class
 *
 * @param string $status
 * @return string
 */
function waa_get_post_status_label_class( $status ) {
    switch ( $status ) {
        case 'publish':
            return 'waa-label-success';
            break;

        case 'draft':
            return 'waa-label-default';
            break;

        case 'pending':
            return 'waa-label-warning';;
            break;

        case 'future':
            return 'waa-label-info';;
            break;

        default:
            return '';
            break;
    }
}

/**
 * Get readable product type based on product
 *
 * @param string $status
 * @return string
 */
function waa_wc_get_product_status( $status ) {
    switch ($status) {
        case 'simple':
            $name = __( 'Simple Product', 'waa' );
            break;

        case 'variable':
            $name = __( 'Variable Product', 'waa' );
            break;

        case 'grouped':
            $name = __( 'Grouped Product', 'waa' );
            break;

        case 'external':
            $name = __( 'Scheduled', 'waa' );
            break;

        default:
            $name = '';
            break;
    }

    return apply_filters( 'waa_product_status_case', $name, $status );
}

/**
 * Helper function for input text field
 *
 * @param string $key
 * @return string
 */
function waa_posted_input( $key ) {
    $value = isset( $_POST[$key] ) ? trim( $_POST[$key] ) : '';

    return esc_attr( $value );
}

/**
 * Helper function for input textarea
 *
 * @param string $key
 * @return string
 */
function waa_posted_textarea( $key ) {
    $value = isset( $_POST[$key] ) ? trim( $_POST[$key] ) : '';

    return esc_textarea( $value );
}

/**
 * Get template part implementation for wedocs
 *
 * Looks at the theme directory first
 */
function waa_get_template_part( $slug, $name = '' ) {
    $waa = WAA_waa::init();

    $template = '';

    // Look in yourtheme/waa/slug-name.php and yourtheme/waa/slug.php
    $template = locate_template( array( $waa->template_path() . "{$slug}-{$name}.php", $waa->template_path() . "{$slug}.php" ) );

    // Get default slug-name.php
    if ( ! $template && $name && file_exists( $waa->plugin_path() . "/templates/{$slug}-{$name}.php" ) ) {
        $template = $waa->plugin_path() . "/templates/{$slug}-{$name}.php";
    }

    if ( ! $template && !$name && file_exists( $waa->plugin_path() . "/templates/{$slug}.php" ) ) {
        $template = $waa->plugin_path() . "/templates/{$slug}.php";
    }

    // Allow 3rd party plugin filter template file from their plugin
    $template = apply_filters( 'waa_get_template_part', $template, $slug, $name );

    if ( $template ) {
        load_template( $template, false );
    }
}

/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 *
 * @access public
 * @param mixed $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return void
 */
function waa_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
    if ( $args && is_array( $args ) ) {
        extract( $args );
    }

    $located = waa_locate_template( $template_name, $template_path, $default_path );

    if ( ! file_exists( $located ) ) {
        _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );
        return;
    }

    do_action( 'waa_before_template_part', $template_name, $template_path, $located, $args );

    include( $located );

    do_action( 'waa_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *      yourtheme       /   $template_path  /   $template_name
 *      yourtheme       /   $template_name
 *      $default_path   /   $template_name
 *
 * @access public
 * @param mixed $template_name
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return string
 */
function waa_locate_template( $template_name, $template_path = '', $default_path = '' ) {
    $waa = WAA_waa::init();

    if ( ! $template_path ) {
        $template_path = $waa->template_path();
    }

    if ( ! $default_path ) {
        $default_path = $waa->plugin_path() . '/templates/';
    }

    // Look within passed path within the theme - this is priority
    $template = locate_template(
        array(
            trailingslashit( $template_path ) . $template_name,
        )
    );

    // Get default template
    if ( ! $template ) {
        $template = $default_path . $template_name;
    }

    // Return what we found
    return apply_filters('waa_locate_template', $template, $template_name, $template_path);
}

/**
 * Get page permalink based on context
 *
 * @param string $page
 * @param string $context
 * @return string url of the page
 */
function waa_get_page_url( $page, $context = 'waa' ) {

    if ( $context == 'woocommerce' ) {
        $page_id = wc_get_page_id( $page );
    } else {
        $page_id = waa_get_option( $page, 'waa_pages' );
    }

    return get_permalink( $page_id );
}

/**
 * Get edit product url
 *
 * @param type $product_id
 * @return type
 */
function waa_edit_product_url( $product_id ) {
    if ( get_post_field( 'post_status', $product_id ) == 'publish' ) {
        return trailingslashit( get_permalink( $product_id ) ). 'edit/';
    }

    if ( waa_get_option( 'product_style', 'waa_selling', 'old' ) == 'old' ) {
        $new_product_url = waa_get_navigation_url('products');
    } elseif ( waa_get_option( 'product_style', 'waa_selling', 'old' ) == 'new' ) {
        $new_product_url = waa_get_navigation_url('new-product');
    }

    return add_query_arg( array( 'product_id' => $product_id, 'action' => 'edit' ), $new_product_url );
}

/**
 * Ads additional columns to admin user table
 *
 * @param array $columns
 * @return array
 */
function my_custom_admin_product_columns( $columns ) {
    $columns['author'] = __( 'Author', 'waa' );

    return $columns;
}

add_filter( 'manage_edit-product_columns', 'my_custom_admin_product_columns' );


/**
 * Get the value of a settings field
 *
 * @param string $option settings field name
 * @param string $section the section name this field belongs to
 * @param string $default default text if it's not found
 * @return mixed
 */
function waa_get_option( $option, $section, $default = '' ) {

    $options = get_option( $section );

    if ( isset( $options[$option] ) ) {
        return $options[$option];
    }

    return $default;
}

/**
 * Redirect users from standard WordPress register page to woocommerce
 * my account page
 *
 * @global string $action
 */
function waa_redirect_to_register(){
    global $action;

    if ( $action == 'register' ) {
        wp_redirect( waa_get_page_url( 'myaccount', 'woocommerce' ) );
        exit;
    }
}

add_action( 'login_init', 'waa_redirect_to_register' );

/**
 * Pretty print a variable
 *
 * @param var $value
 */
function waa_pre( $value ) {
    printf( '<pre>%s</pre>', print_r( $value, true ) );
}

/**
 * Check if the seller is enabled
 *
 * @param int $user_id
 * @return boolean
 */
function waa_is_seller_enabled( $user_id ) {
    $selling = get_user_meta( $user_id, 'waa_enable_selling', true );

    if ( $selling == 'yes' ) {
        return true;
    }

    return false;
}

/**
 * Check if the seller is trusted
 *
 * @param int $user_id
 * @return boolean
 */
function waa_is_seller_trusted( $user_id ) {
    $publishing = get_user_meta( $user_id, 'waa_publishing', true );

    if ( $publishing == 'yes' ) {
        return true;
    }

    return false;
}

/**
 * Get store page url of a seller
 *
 * @param int $user_id
 * @return string
 */
function waa_get_store_url( $user_id ) {
    $userdata = get_userdata( $user_id );
    $custom_store_url = waa_get_option( 'custom_store_url', 'waa_selling', 'store' );
    return sprintf( '%s/%s/', home_url( '/' . $custom_store_url ), $userdata->user_nicename );
}

/**
 * Get review page url of a seller
 *
 * @param int $user_id
 * @return string
 */
function waa_get_review_url( $user_id ) {
    $userstore = waa_get_store_url( $user_id );

    return apply_filters( 'waa_get_seller_review_url', $userstore ."reviews" );
}

/**
 * Helper function for loggin
 *
 * @param string $message
 */
function waa_log( $message ) {
    $message = sprintf( "[%s] %s\n", date( 'd.m.Y h:i:s' ), $message );
    error_log( $message, 3, waa_DIR . '/debug.log' );
}

/**
 * Filter WP Media Manager files if the current user is seller.
 *
 * Do not show other sellers images to a seller. He can see images only by him
 *
 * @param array $args
 * @return array
 */
function waa_media_uploader_restrict( $args ) {
    // bail out for admin and editor
    if ( current_user_can( 'delete_pages' ) ) {
        return $args;
    }

    if ( current_user_can( 'dokandar' ) ) {
        $args['author'] = get_current_user_id();

        return $args;
    }

    return $args;
}

add_filter( 'ajax_query_attachments_args', 'waa_media_uploader_restrict' );

/**
 * Get store info based on seller ID
 *
 * @param int $seller_id
 * @return array
 */
function waa_get_store_info( $seller_id ) {
    $info = get_user_meta( $seller_id, 'waa_profile_settings', true );
    $info = is_array( $info ) ? $info : array();

    $defaults = array(
        'store_name' => '',
        'social'     => array(),
        'payment'    => array( 'paypal' => array( 'email' ), 'bank' => array() ),
        'phone'      => '',
				'description' => '',
        'show_email' => 'off',
        'address'    => '',
        'location'   => '',
        'banner'     => 0
    );

    $info               = wp_parse_args( $info, $defaults );
    $info['store_name'] = empty( $info['store_name'] ) ? get_user_by( 'id', $seller_id )->display_name : $info['store_name'];

    return $info;
}

/**
 * Get tabs for showing in a single store page
 *
 * @since 2.2
 *
 * @param  int  $store_id
 *
 * @return array
 */
function waa_get_store_tabs( $store_id ) {

    $tabs = array(
        'products' => array(
            'title' => __( 'Products', 'waa' ),
            'url'   => waa_get_store_url( $store_id )
        ),
        'reviews' => array(
            'title' => __( 'Reviews', 'waa' ),
            'url'   => waa_get_review_url( $store_id )
        ),
    );

    $store_info = waa_get_store_info( $store_id );
    $tnc_enable = waa_get_option( 'seller_enable_terms_and_conditions', 'waa_selling', 'off' );

    if ( isset($store_info['enable_tnc']) && $store_info['enable_tnc'] == 'on' && $tnc_enable == 'on' ) {
        $tabs['terms_and_conditions'] = array(
            'title' => __( 'Terms and Conditions', 'waa' ),
            'url'   => waa_get_toc_url( $store_id )
        );
    }

    return apply_filters( 'waa_store_tabs', $tabs, $store_id );
}

/**
 * Get withdraw email method based on seller ID and type
 *
 * @param int $seller_id
 * @param string $type
 * @return string
 */
function waa_get_seller_withdraw_mail( $seller_id, $type = 'paypal' ) {
    $info = waa_get_store_info( $seller_id );

    if ( isset( $info['payment'][$type]['email'] ) ) {
        return $info['payment'][$type]['email'];
    }

    return false;
}

/**
 * Get seller bank details
 *
 * @param int $seller_id
 * @return string
 */
function waa_get_seller_bank_details( $seller_id ) {
    $info = waa_get_store_info( $seller_id );
    $payment = $info['payment']['bank'];
    $details = array();

    if ( isset( $payment['ac_name'] ) ) {
        $details[] = sprintf( __( 'Account Name: %s', 'waa' ), $payment['ac_name'] );
    }
    if ( isset( $payment['ac_iban'] ) ) {
        $details[] = sprintf( __( 'Account Number: %s', 'waa' ), $payment['ac_iban'] );
    }
    if ( isset( $payment['ac_bic'] ) ) {
        $details[] = sprintf( __( 'SWIFT: %s', 'waa' ), $payment['ac_bic'] );
    }
    if ( isset( $payment['bank_name'] ) ) {
        $details[] = sprintf( __( 'Bank Name: %s', 'waa' ), $payment['bank_name'] );
    }

    return nl2br( implode( "\n", $details ) );
}


/**
 * Get seller shipping details
 *
 * @param int $seller_id
 * @return string
 */
function waa_get_seller_shipping_details( $seller_id ) {
    $info = waa_get_store_info( $seller_id );
    $payment = $info['payment']['bank'];
    $details = array();

    if ( isset( $payment['ac_name'] ) ) {
        $details[] = sprintf( __( 'Account Name: %s', 'waa' ), $payment['ac_name'] );
    }
    if ( isset( $payment['ac_iban'] ) ) {
        $details[] = sprintf( __( 'Account Number: %s', 'waa' ), $payment['ac_iban'] );
    }
    if ( isset( $payment['ac_bic'] ) ) {
        $details[] = sprintf( __( 'SWIFT: %s', 'waa' ), $payment['ac_bic'] );
    }
    if ( isset( $payment['bank_name'] ) ) {
        $details[] = sprintf( __( 'Bank Name: %s', 'waa' ), $payment['bank_name'] );
    }

    return nl2br( implode( "\n", $details ) );
}


/**
 * Check if Artist allows pickup
 *
 * @param $user_id
 * @return bool
 */
function artist_has_pickup($user_id)
{
    $_dps_shipping_enable = get_user_meta($user_id, '_dps_shipping_enable', true);
    $_dps_enable_pickup = get_user_meta($user_id, '_dps_enable_pickup', true);
    if ($_dps_shipping_enable == 'yes' && $_dps_enable_pickup == 'yes') {
        return true;
    } else {
        return false;
    }
}

/**
 * Check if Artist has entered shipping information
 *
 * @param $user_id
 * @return bool
 */
function artist_has_shipping($user_id)
{
    $_dps_shipping_type_price = get_user_meta($user_id, '_dps_shipping_type_price', true);
    $_dps_pt = get_user_meta($user_id, '_dps_pt', true); // time to send package
    $_dps_form_location = get_user_meta($user_id, '_dps_form_location', true);
    // wenn gebuehr = 0 muss _dps_state_rates angegeben sein und gebuehr darf dort nicht != 0 sein.
    $_dps_country_rates = unserialize(is_array(get_user_meta($user_id, '_dps_country_rates', true)) ? reset(get_user_meta($user_id, '_dps_country_rates', true)) : get_user_meta($user_id, '_dps_country_rates', true));
    #$_dps_state_rates = unserialize(is_array(get_user_meta($user_id, '_dps_state_rates', true)) ? reset(get_user_meta($user_id, '_dps_state_rates', true)) : get_user_meta($user_id, '_dps_state_rates', true));

    if (artist_has_pickup($user_id))
        return true;
    else if (($_dps_pt != '' && $_dps_form_location != '') && ($_dps_shipping_type_price != '0' || !empty($_dps_country_rates) || !empty($_dps_state_rates) ))
        return true;
    return false;
}

/**
 *  Check if Artist is allowed to add a new product
 *
 * @param $user_id
 * @return bool
 */
function artist_can_add_product($user_id)
{
    if (artist_has_shipping($user_id))
        return true;
    return false;
}

/**
 * Get seller listing
 *
 * @param int $number
 * @param int $offset
 * @return array
 */
function waa_get_sellers( $number = 10, $offset = 0 ) {
    $args = apply_filters( 'waa_seller_list_query', array(
        'role' => 'seller',
        'number'     => $number,
        'offset'     => $offset,
        'orderby'    => 'registered',
        'order'      => 'ASC',
        'meta_query' => array(
            array(
                'key'     => 'waa_enable_selling',
                'value'   => 'yes',
                'compare' => '='
            )
        )
    ) );

    $user_query = new WP_User_Query( $args );


    $sellers    = $user_query->get_results();

    return array( 'users' => $sellers, 'count' => $user_query->total_users );
}

/**
 * Add cart total amount on add_to_cart_fragments
 *
 * @param array $fragment
 * @return array
 */
function waa_add_to_cart_fragments( $fragment ) {
    $fragment['amount'] = WC()->cart->get_cart_total();

    return $fragment;
}

add_filter( 'add_to_cart_fragments', 'waa_add_to_cart_fragments' );


/**
 * Put data with post_date's into an array of times
 *
 * @param  array $data array of your data
 * @param  string $date_key key for the 'date' field. e.g. 'post_date'
 * @param  string $data_key key for the data you are charting
 * @param  int $interval
 * @param  string $start_date
 * @param  string $group_by
 * @return string
 */
function waa_prepare_chart_data( $data, $date_key, $data_key, $interval, $start_date, $group_by ) {
    $prepared_data = array();

    // Ensure all days (or months) have values first in this range
    for ( $i = 0; $i <= $interval; $i ++ ) {
        switch ( $group_by ) {
            case 'day' :
                $time = strtotime( date( 'Ymd', strtotime( "+{$i} DAY", $start_date ) ) ) * 1000;
            break;
            case 'month' :
                $time = strtotime( date( 'Ym', strtotime( "+{$i} MONTH", $start_date ) ) . '01' ) * 1000;
            break;
        }

        if ( ! isset( $prepared_data[ $time ] ) )
            $prepared_data[ $time ] = array( esc_js( $time ), 0 );
    }

    foreach ( $data as $d ) {
        switch ( $group_by ) {
            case 'day' :
                $time = strtotime( date( 'Ymd', strtotime( $d->$date_key ) ) ) * 1000;
            break;
            case 'month' :
                $time = strtotime( date( 'Ym', strtotime( $d->$date_key ) ) . '01' ) * 1000;
            break;
        }

        if ( ! isset( $prepared_data[ $time ] ) ) {
            continue;
        }

        if ( $data_key )
            $prepared_data[ $time ][1] += $d->$data_key;
        else
            $prepared_data[ $time ][1] ++;
    }

    return $prepared_data;
}

/**
 * Disable selling capability by default once a seller is registered
 *
 * @param int $user_id
 */
function waa_admin_user_register( $user_id ) {
    $user = new WP_User( $user_id );
    $role = reset( $user->roles );

    if ( $role == 'seller' ) {

        if ( waa_get_option( 'new_seller_enable_selling', 'waa_selling' ) == 'off' ) {
            update_user_meta( $user_id, 'waa_enable_selling', 'no' );
        } else {
            update_user_meta( $user_id, 'waa_enable_selling', 'yes' );
        }
    }
}

add_action( 'user_register', 'waa_admin_user_register' );

/**
 * Get seller count based on enable and disabled sellers
 *
 * @global WPDB $wpdb
 * @return array
 */
function waa_get_seller_count() {
    global $wpdb;


    $counts = array( 'yes' => 0, 'no' => 0 );

    $result = $wpdb->get_results( "SELECT COUNT(um.user_id) as count, um1.meta_value as type
                FROM $wpdb->usermeta um
                LEFT JOIN $wpdb->usermeta um1 ON um1.user_id = um.user_id
                WHERE um.meta_key = 'wp_capabilities' AND um1.meta_key = 'waa_enable_selling'
                AND um.meta_value LIKE '%seller%'
                GROUP BY um1.meta_value" );

    if ( $result ) {
        foreach ($result as $row) {
            $counts[$row->type] = (int) $row->count;
        }
    }

    return $counts;
}

/**
 * Prevent sellers and customers from seeing the admin bar
 *
 * @param bool $show_admin_bar
 * @return bool
 */
function waa_disable_admin_bar( $show_admin_bar ) {
    global $current_user;

    if ( $current_user->ID !== 0 ) {
        $role = reset( $current_user->roles );

        if ( in_array( $role, array( 'seller', 'customer' ) ) ) {
            return false;
        }
    }

    return $show_admin_bar;
}

add_filter( 'show_admin_bar', 'waa_disable_admin_bar' );

/**
 * Human readable number format.
 *
 * Shortens the number by dividing 1000
 *
 * @param type $number
 * @return type
 */
function waa_number_format( $number ) {
    $threshold = 10000;

    if ( $number > $threshold ) {
        return number_format( $number/1000, 0, '.', '' ) . ' K';
    }

    return $number;
}

/**
 * Get coupon edit url
 *
 * @param int $coupon_id
 * @param string $coupon_page
 * @return string
 */
function waa_get_coupon_edit_url( $coupon_id, $coupon_page = '' ) {

    if ( !$coupon_page ) {
        $coupon_page = waa_get_page_url( 'coupons' );
    }

    $edit_url = wp_nonce_url( add_query_arg( array('post' => $coupon_id, 'action' => 'edit', 'view' => 'add_coupons'), $coupon_page ), '_coupon_nonce', 'coupon_nonce_url' );

    return $edit_url;
}

/**
 * User avatar wrapper for custom uploaded avatar
 *
 * @since 2.0
 *
 * @param string $avatar
 * @param mixed $id_or_email
 * @param int $size
 * @param string $default
 * @param string $alt
 * @return string image tag of the user avatar
 */
function waa_get_avatar( $avatar, $id_or_email, $size, $default, $alt ) {

    if ( is_numeric( $id_or_email ) ) {
        $user = get_user_by( 'id', $id_or_email );
    } elseif ( is_object( $id_or_email ) ) {
        if ( $id_or_email->user_id != '0' ) {
            $user = get_user_by( 'id', $id_or_email->user_id );
        } else {
            return $avatar;
        }
    } else {
        $user = get_user_by( 'email', $id_or_email );
    }

    if ( !$user ) {
        return $avatar;
    }

    // see if there is a user_avatar meta field
    $user_avatar = get_user_meta( $user->ID, 'waa_profile_settings', true );
    $gravatar_id = isset( $user_avatar['gravatar'] ) ? $user_avatar['gravatar'] : 0;
    if ( empty( $gravatar_id ) ) {
        return $avatar;
    }

    $avater_url = wp_get_attachment_thumb_url( $gravatar_id );

    return sprintf( '<img src="%1$s" alt="%2$s" width="%3$s" height="%3$s" class="avatar photo">', esc_url( $avater_url ), $alt, $size );
}

add_filter( 'get_avatar', 'waa_get_avatar', 99, 5 );

/**
 * Get best sellers list
 *
 * @param  integer $limit
 * @return array
 */
function waa_get_best_sellers( $limit = 5 ) {
    global  $wpdb;

    $cache_key = 'waa-best-seller-' . $limit;
    $seller = wp_cache_get( $cache_key, 'widget' );

    if ( false === $seller ) {

        $qry = "SELECT seller_id, display_name, SUM( net_amount ) AS total_sell
            FROM {$wpdb->prefix}waa_orders AS o,{$wpdb->prefix}users AS u
            WHERE o.seller_id = u.ID
            GROUP BY o.seller_id
            ORDER BY total_sell DESC LIMIT ".$limit;

        $seller = $wpdb->get_results( $qry );
        wp_cache_set( $cache_key, $seller, 'widget' );
    }

    return $seller;
}

/**
 * Get feature sellers list
 *
 * @param  integer $limit
 * @return array
 */
function waa_get_feature_sellers( $count = 5 ) {
    $args = array(
        'role'         => 'seller',
        'meta_key'     => 'waa_feature_seller',
        'meta_value'   => 'yes',
        'offset'       => $count
    );
    $users = get_users( $args );

    $args = array(
        'role'         => 'administrator',
        'meta_key'     => 'waa_feature_seller',
        'meta_value'   => 'yes',
        'offset'       => $count
    );
    $admins = get_users( $args );

    $sellers = array_merge( $admins, $users );
    return $sellers;
}

/**
 * Get navigation url for the waa dashboard
 *
 * @param  string $name endpoint name
 * @return string url
 */
function waa_get_navigation_url( $name = '' ) {
    $page_id = waa_get_option( 'dashboard', 'waa_pages' );

    if ( ! $page_id ) {
        return;
    }

    if ( ! empty( $name ) ) {
        $url = get_permalink( $page_id ) . $name.'/';
    } else {
        $url = get_permalink( $page_id );
    }

    return apply_filters( 'waa_get_navigation_url', $url, $name );
}


/**
 * Generate country dropdwon
 *
 * @param array $options
 * @param string $selected
 * @param bool $everywhere
 */
function waa_country_dropdown( $options, $selected = '', $everywhere = false ) {
    printf( '<option value="">%s</option>', __( '- Select a location -', 'waa' ) );

    if ( $everywhere ) {
        echo '<optgroup label="--------------------------">';
        printf( '<option value="everywhere"%s>%s</option>', selected( $selected, 'everywhere', true ), __( 'Everywhere Else', 'waa' ) );
        echo '</optgroup>';
    }

    echo '<optgroup label="------------------------------">';
    foreach ($options as $key => $value) {
        printf( '<option value="%s"%s>%s</option>', $key, selected( $selected, $key, true ), $value );
    }
    echo '</optgroup>';
}

/**
 * Generate country dropdwon
 *
 * @param array $options
 * @param string $selected
 * @param bool $everywhere
 */
function waa_state_dropdown( $options, $selected = '', $everywhere = false ) {
    printf( '<option value="">%s</option>', __( '- Select a State -', 'waa' ) );

    if ( $everywhere ) {
        echo '<optgroup label="--------------------------">';
        printf( '<option value="everywhere" %s>%s</option>', selected( $selected, 'everywhere', true ), __( 'Everywhere Else', 'waa' ) );
        echo '</optgroup>';
    }

    echo '<optgroup label="------------------------------">';
    foreach ($options as $key => $value) {
        printf( '<option value="%s" %s>%s</option>', $key, selected( $selected, $key, true ), $value );
    }
    echo '</optgroup>';
}

/**
 * Shupping Processing time dropdown options
 *
 * @return array
 */
function waa_get_shipping_processing_times() {
    $times = array(
        '' => __( 'Ready to ship in...', 'waa' ),
        '1' => __( '1 business day', 'waa' ),
        '2' => __( '1-2 business day', 'waa' ),
        '3' => __( '1-3 business day', 'waa' ),
        '4' => __( '3-5 business day', 'waa' ),
        '5' => __( '1-2 weeks', 'waa' ),
        '6' => __( '2-3 weeks', 'waa' ),
        '7' => __( '3-4 weeks', 'waa' ),
        '8' => __( '4-6 weeks', 'waa' ),
        '9' => __( '6-8 weeks', 'waa' ),
    );

    return apply_filters( 'waa_shipping_processing_times', $times );
}

/**
 * Get a single processing time string
 *
 * @param string $index
 * @return string
 */
function waa_get_processing_time_value( $index ) {
    $times = waa_get_shipping_processing_times();

    if ( isset( $times[$index] ) ) {
        return $times[$index];
    }
}

/**
 * Adds seller email to the new order notification email
 *
 * @param string  $admin_email
 * @param WC_Order $order
 * @return array
 */
function waa_wc_email_recipient_add_seller( $admin_email, $order ) {
    $emails = array( $admin_email );

    $seller_id = waa_get_seller_id_by_order( $order->id );

    if ( $seller_id ) {
        $seller_email = get_user_by( 'id', $seller_id )->user_email;

        if ( $admin_email != $seller_email ) {
            array_push( $emails, $seller_email );
        }
    }

    return $emails;
}

add_filter( 'woocommerce_email_recipient_new_order', 'waa_wc_email_recipient_add_seller', 10, 2 );

// Add Toolbar Menus
function waa_admin_toolbar() {
    global $wp_admin_bar;

    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $args = array(
        'id'     => 'waa',
        'title'  => __( 'Künstler Manager', 'admin' ),
        'href'   => admin_url( 'admin.php?page=waa' )
    );

    $wp_admin_bar->add_menu( $args );

    $wp_admin_bar->add_menu( array(
        'id'     => 'waa-dashboard',
        'parent' => 'waa',
        'title'  => __( 'Dashboard', '' ),
        'href'   => admin_url( 'admin.php?page=waa' )
    ) );

    $wp_admin_bar->add_menu( array(
        'id'     => 'waa-withdraw',
        'parent' => 'waa',
        'title'  => __( 'Withdraw', 'waa' ),
        'href'   => admin_url( 'admin.php?page=waa-withdraw' )
    ) );

    $wp_admin_bar->add_menu( array(
        'id'     => 'waa-sellers',
        'parent' => 'waa',
        'title'  => __( 'All Sellers', 'waa' ),
        'href'   => admin_url( 'admin.php?page=waa-sellers' )
    ) );

    $wp_admin_bar->add_menu( array(
        'id'     => 'waa-reports',
        'parent' => 'waa',
        'title'  => __( 'Earning Reports', 'waa' ),
        'href'   => admin_url( 'admin.php?page=waa-reports' )
    ) );

    $wp_admin_bar->add_menu( array(
        'id'     => 'waa-settings',
        'parent' => 'waa',
        'title'  => __( 'Settings', 'waa' ),
        'href'   => admin_url( 'admin.php?page=waa-settings' )
    ) );
}

// Hook into the 'wp_before_admin_bar_render' action
add_action( 'wp_before_admin_bar_render', 'waa_admin_toolbar' );

/**
 * Returns Current User Profile progress bar HTML
 *
 * @since 2.1
 *
 * @return output
 */
function waa_get_profile_progressbar() {
    global $current_user;

    $profile_info = waa_get_store_info( $current_user->ID );
    $progress     = isset( $profile_info['profile_completion']['progress'] ) ? $profile_info['profile_completion']['progress'] : 0;
    $next_todo    = isset( $profile_info['profile_completion']['next_todo'] ) ? $profile_info['profile_completion']['next_todo'] : __('Start with adding a Banner to gain profile progress','waa');

    ob_start();

    if (  strlen( trim( $next_todo ) ) != 0 ) { ?>
        <div class="waa-panel waa-panel-default waa-profile-completeness">
            <div class="waa-panel-body">
            <div class="waa-progress">
                <div class="waa-progress-bar waa-progress-bar-info waa-progress-bar-striped" role="progressbar"
                     aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $progress ?>%">
                    <?php echo $progress . __( '% Profile complete', 'waa' ) ?>
                </div>
            </div>

            <div class="waa-alert waa-alert-info waa-panel-alert"><?php echo $next_todo; ?></div>
           </div>
        </div>
    <?php
    }

    $output = ob_get_clean();

    #return $output;
    return '';
}

/**
 * Display a monthly dropdown for filtering product listing on seller dashboard
 *
 * @since 2.1
 * @access public
 *
 * @param int $user_id
 */
function waa_product_listing_filter_months_dropdown( $user_id ) {
    global $wpdb, $wp_locale;

    $months = $wpdb->get_results( $wpdb->prepare( "
        SELECT DISTINCT YEAR( post_date ) AS year, MONTH( post_date ) AS month
        FROM $wpdb->posts
        WHERE post_type = 'product'
        AND post_author = %d
        ORDER BY post_date DESC
    ", $user_id )  );

    /**
     * Filter the 'Months' drop-down results.
     *
     * @since 2.1
     *
     * @param object $months    The months drop-down query results.
     */
    $months = apply_filters( 'months_dropdown_results', $months );

    $month_count = count( $months );

    if ( !$month_count || ( 1 == $month_count && 0 == $months[0]->month ) )
        return;

    $date = isset( $_GET['date'] ) ? (int) $_GET['date'] : 0;
    ?>
    <select name="date" id="filter-by-date" class="waa-form-control">
        <option<?php selected( $date, 0 ); ?> value="0"><?php _e( 'All dates' ); ?></option>
    <?php
    foreach ( $months as $arc_row ) {
        if ( 0 == $arc_row->year )
            continue;

        $month = zeroise( $arc_row->month, 2 );
        $year = $arc_row->year;

        printf( "<option %s value='%s' >%s</option>\n",
            selected( $date, $year . $month, false ),
            esc_attr( $year . $month ),
            /* translators: 1: month name, 2: 4-digit year */
            sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $month ), $year )
        );
    }
    ?>
    </select>
    <?php
}

/**
 * Display form for filtering product listing on seller dashboard
 *
 * @since 2.1
 * @access public
 *
 */
function waa_product_listing_filter() {
    do_action( 'waa_product_listing_filter_before_form' );
    ?>

    <form class="waa-form-inline waa-w6" method="get" >

        <div class="waa-form-group">
            <?php waa_product_listing_filter_months_dropdown( get_current_user_id() ); ?>
        </div>

        <div class="waa-form-group">
            <?php
            wp_dropdown_categories( array(
                'show_option_none' => __( '- Select a category -', 'waa' ),
                'hierarchical'     => 1,
                'hide_empty'       => 0,
                'name'             => 'product_cat',
                'id'               => 'product_cat',
                'taxonomy'         => 'product_cat',
                'title_li'         => '',
                'class'            => 'product_cat waa-form-control chosen',
                'exclude'          => '',
                'selected'         => isset( $_GET['product_cat'] ) ? $_GET['product_cat'] : '-1',
            ) );
            ?>
        </div>

        <?php
        if ( isset( $_GET['product_search_name'] ) ) { ?>
            <input type="hidden" name="product_search_name" value="<?php echo $_GET['product_search_name']; ?>">
        <?php }
        ?>

        <button type="submit" name="product_listing_filter" value="ok" class="waa-btn waa-btn-theme"><?php _e( 'Filter', 'waa'); ?></button>

    </form>
    <?php do_action( 'waa_product_listing_filter_before_search_form' ); ?>
    <form method="get" class="waa-form-inline waa-w6">

        <button type="submit" name="product_listing_search" value="ok" class="waa-btn waa-btn-theme waa-right"><?php _e( 'Search', 'waa'); ?></button>

        <?php wp_nonce_field( 'waa_product_search', 'waa_product_search_nonce' ); ?>

        <div class="waa-form-group waa-right">
            <input type="text" class="waa-form-control" name="product_search_name" placeholder="Search Products" value="<?php echo isset( $_GET['product_search_name'] ) ? $_GET['product_search_name'] : '' ?>">
        </div>

        <?php
        if ( isset( $_GET['product_cat'] ) ) { ?>
            <input type="hidden" name="product_cat" value="<?php echo $_GET['product_cat']; ?>">
        <?php }

        if ( isset( $_GET['date'] ) ) { ?>
            <input type="hidden" name="date" value="<?php echo $_GET['date']; ?>">
        <?php }
        ?>
    </form>
    <?php
    do_action( 'waa_product_listing_filter_after_form' );
}

/**
 * Search by SKU or ID for seller dashboard product listings.
 *
 * @param string $where
 * @return string
 */
function waa_product_search_by_sku( $where ) {
    global $pagenow, $wpdb, $wp;

    if ( !isset( $_GET['product_search_name'] ) || empty( $_GET['product_search_name'] ) || ! isset( $_POST['waa_product_search_nonce'] ) || ! wp_verify_nonce( $_POST['waa_product_search_nonce'], 'waa_product_search' ) ) {
        return $where;
    }

    $search_ids = array();
    $terms      = explode( ',', $_GET['product_search_name'] );

    foreach ( $terms as $term ) {
        if ( is_numeric( $term ) ) {
            $search_ids[] = $term;
        }
        // Attempt to get a SKU
        $sku_to_id = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_sku' AND meta_value LIKE '%%%s%%';", wc_clean( $term ) ) );

        if ( $sku_to_id && sizeof( $sku_to_id ) > 0 ) {
            $search_ids = array_merge( $search_ids, $sku_to_id );
        }
    }

    $search_ids = array_filter( array_map( 'absint', $search_ids ) );

    if ( sizeof( $search_ids ) > 0 ) {
        $where = str_replace( ')))', ") OR ({$wpdb->posts}.ID IN (" . implode( ',', $search_ids ) . "))))", $where );
    }

    return $where;
}

add_filter( 'posts_search', 'waa_product_search_by_sku' );

/**
 * waa Social Profile fields
 *
 * @since 2.2
 *
 * @return array
 */
function waa_get_social_profile_fields() {
    $fields = array(
        'fb' => array(
            'icon'  => 'facebook-square',
            'title' => __( 'Facebook', 'waa' ),
        ),
        'gplus' => array(
            'icon'  => 'google-plus-square',
            'title' => __( 'Google Plus', 'waa' ),
        ),
        'twitter' => array(
            'icon'  => 'twitter-square',
            'title' => __( 'Twitter', 'waa' ),
        ),
        'linkedin' => array(
            'icon'  => 'linkedin-square',
            'title' => __( 'LinkedIn', 'waa' ),
        ),
        'youtube' => array(
            'icon'  => 'youtube-square',
            'title' => __( 'Youtube', 'waa' ),
        ),
        'instagram' => array(
            'icon'  => 'instagram',
            'title' => __( 'Instagram', 'waa' ),
        ),
        'flickr' => array(
            'icon'  => 'flickr',
            'title' => __( 'Flickr', 'waa' ),
        ),
    );

    return apply_filters( 'waa_profile_social_fields', $fields );
}

/**
 * Generate Address fields form for seller
 * @since 2.3
 *
 * @param boolean verified
 *
 * @return void
 */

function waa_seller_address_fields( $verified = false, $required = false, $data = false ) {

    $disabled = $verified ? 'disabled' : '';

    /**
     * Filter the seller Address fields
     *
     * @since 2.2
     *
     * @param array $waa_seller_address
     */
    $seller_address_fields = apply_filters( 'waa_seller_address_fields', array(

            'street_1' => array(
                'required' => $required ? 1 : 0,
            ),
            'street_2' => false,
            //make 'street_2' => false, if needed to be removed
            'city'     => array(
                'required' => $required ? 1 : 0,
            ),
            'zip'      => array(
                'required' => $required ? 1 : 0,
            ),
            'country'  => array(
                'required' => $required ? 1 : 0,
            ),
            'state'    => false
        )
    );

    $profile_info = waa_get_store_info( get_current_user_id() );
		
		if ($data) :
			
			$address_street1 = isset( $data['street_1'] ) ? $data['street_1'] : '';
			$address_city = isset( $data['city'] ) ? $data['city'] : '';
			$address_zip = isset( $data['zip'] ) ? $data['zip'] : '';
			$address_country = isset( $data['country'] ) ? $data['country'] : '';
		
		else :

			$address         = isset( $profile_info['address'] ) ? $profile_info['address'] : '';
			$address_street1 = isset( $profile_info['address']['street_1'] ) ? $profile_info['address']['street_1'] : '';
			$address_street2 = isset( $profile_info['address']['street_2'] ) ? $profile_info['address']['street_2'] : '';
			$address_city    = isset( $profile_info['address']['city'] ) ? $profile_info['address']['city'] : '';
			$address_zip     = isset( $profile_info['address']['zip'] ) ? $profile_info['address']['zip'] : '';
			$address_country = isset( $profile_info['address']['country'] ) ? $profile_info['address']['country'] : '';
			$address_state   = isset( $profile_info['address']['state'] ) ? $profile_info['address']['state'] : '';
		
		endif;
    ?>
    <input type="hidden" id="waa_selected_country" value="<?php echo $address_country?>" />
    <input type="hidden" id="waa_selected_state" value="<?php echo $address_state?>" />
    <div class="waa-form-group">
        <label class="waa-w3 waa-control-label hide-if-get-started" for="setting_address"><?php _e( 'Address', 'waa' ); ?></label>

        <div class="waa-w5 waa-text-left waa-address-fields">
            <?php if ( $seller_address_fields['street_1'] ) { ?>
                <div class="waa-form-group">
                    <label class="waa-w3 control-label" for="waa_address[street_1]"><?php _e( 'Street ', 'waa' ); ?>
                        <?php
                        $required_attr = '';
                        if ( $seller_address_fields['street_1']['required'] ) {
                            $required_attr = 'required'; ?>
                            <span class="required"> *</span>
                        <?php } ?>
                    </label>
                    <input <?php echo $required_attr; ?> <?php echo $disabled ?> id="waa_address[street_1]" value="<?php echo esc_attr( $address_street1 ); ?>" name="waa_address[street_1]" placeholder="<?= __('Street ', 'waa') ?>" class="waa-form-control input-md<?= ($address_street1 == '' && $data ? ' waa-error' : ''); ?>" type="text">
                </div>
            <?php }
            if ( $seller_address_fields['street_2'] ) { ?>
                <div class="waa-form-group">
                    <label class="waa-w3 control-label" for="waa_address[street_2]"><?php _e( 'Street 2', 'waa' ); ?>
                        <?php
                        $required_attr = '';
                        if ( $seller_address_fields['street_2']['required'] ) {
                            $required_attr = 'required'; ?>
                            <span class="required"> *</span>
                        <?php } ?>
                    </label>
                    <input <?php echo $required_attr; ?> <?php echo $disabled ?> id="waa_address[street_2]" value="<?php echo esc_attr( $address_street2 ); ?>" name="waa_address[street_2]" placeholder="<?= __('Adress-Zusatz', 'waa') ?>" class="waa-form-control input-md<?= ($address_street1 == '' && $data ? ' waa-error' : ''); ?>" type="text">
                </div>
            <?php }
            if ( $seller_address_fields['city'] || $seller_address_fields['zip'] ) {
            ?>
                <div class="waa-from-group">
                    <?php if ( $seller_address_fields['city'] ) { ?>
                        <div class="waa-form-group waa-w6 waa-left waa-right-margin-30">
                            <label class="control-label" for="waa_address[city]"><?php _e( 'City', 'waa' ); ?>
                                <?php
                                $required_attr = '';
                                if ( $seller_address_fields['city']['required'] ) {
                                    $required_attr = 'required'; ?>
                                    <span class="required"> *</span>
                                <?php } ?>
                            </label>
                            <input <?php echo $required_attr; ?> <?php echo $disabled ?> id="waa_address[city]" value="<?php echo esc_attr( $address_city ); ?>" name="waa_address[city]" placeholder="<?= __('City', 'waa') ?>" class="waa-form-control input-md<?= ($address_city == '' && $data ? ' waa-error' : ''); ?>" type="text">
                        </div>
                    <?php }
                    if ( $seller_address_fields['zip'] ) { ?>
                        <div class="waa-form-group waa-w5 waa-left">
                            <label class="control-label" for="waa_address[zip]"><?php _e( 'Post/ZIP Code', 'waa' ); ?>
                                <?php
                                $required_attr = '';
                                if ( $seller_address_fields['zip']['required'] ) {
                                    $required_attr = 'required'; ?>
                                    <span class="required"> *</span>
                                <?php } ?>
                            </label>
                            <input <?php echo $required_attr; ?> <?php echo $disabled ?> id="waa_address[zip]" value="<?php echo esc_attr( $address_zip ); ?>" name="waa_address[zip]" placeholder="<?php _e( 'Post/ZIP Code', 'waa' ); ?>" class="waa-form-control input-md<?= ($address_zip == '' && $data ? ' waa-error' : ''); ?>" type="text">
                        </div>
                    <?php } ?>
                    <div class="waa-clearfix"></div>
                </div>
            <?php }

            if ( $seller_address_fields['country'] ) {
                $country_obj   = new WC_Countries();
                $countries     = $country_obj->countries;
                $states        = $country_obj->states;
            ?>
                <div class="waa-form-group">
                    <label class="control-label" for="waa_address[country]"><?php _e( 'Country ', 'waa' ); ?>
                        <?php
                        $required_attr = '';
                        if ( $seller_address_fields['country']['required'] ) {
                            $required_attr = 'required'; ?>
                            <span class="required"> *</span>
                        <?php } ?>
                    </label>
                    <select <?php echo $required_attr; ?> <?php echo $disabled ?> name="waa_address[country]" class="country_to_state waa-form-control<?= ($address_country == '' && $data ? ' waa-error' : ''); ?>" id="waa_address_country">
                        <?php waa_country_dropdown( $countries, $address_country, false ); ?>
                    </select>
                </div>
            <?php }
            if ( $seller_address_fields['state'] ) {
                $address_state_class = '';
                $is_input            = false;
                $no_states           = false;
                if ( isset( $states[$address_country] ) ) {
                    if ( empty( $states[$address_country] ) ) {
                        $address_state_class = 'waa-hide';
                        $no_states           = true;
                    } else {

                    }
                } else {
                    $is_input = true;
                }
            ?>
                <div  id="waa-states-box" class="waa-form-group">
                    <label class="waa-w3 control-label" for="waa_address[state]"><?php _e( 'State ', 'waa' ); ?>
                    </label>
                <?php if ( $is_input ) { ?>
                    <input <?php echo $disabled ?> name="waa_address[state]" class="waa-form-control <?php echo $address_state_class ?>" id="waa_address_state" value="<?php echo $address_state ?>"/>
                <?php } else { ?>
                    <select <?php echo $disabled ?> name="waa_address[state]" class="waa-form-control" id="waa_address_state">
                        <?php waa_state_dropdown( $states[$address_country], $address_state ) ?>
                    </select>
                <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php
}

/**
 * Generate Address string | array for given seller id or current user
 *
 * @since 2.3
 *
 * @param int seller_id, defaults to current_user_id
 * @param boolean get_array, if true returns array instead of string
 *
 * @return String|array Address | array Address
 */
function waa_get_seller_address( $seller_id = '', $get_array = false ) {

    if ( $seller_id == '' ) {
        $seller_id = get_current_user_id();
    }

    $profile_info = waa_get_store_info( $seller_id );

    if ( isset( $profile_info['address'] ) ) {

        $address = $profile_info['address'];

        $country_obj = new WC_Countries();
        $countries   = $country_obj->countries;
        $states      = $country_obj->states;

        $street_1     = isset( $address['street_1'] ) ? $address['street_1'] : '';
        $street_2     = isset( $address['street_2'] ) ? $address['street_2'] : '';
        $city         = isset( $address['city'] ) ? $address['city'] : '';

        $zip          = isset( $address['zip'] ) ? $address['zip'] : '';
        $country_code = isset( $address['country'] ) ? $address['country'] : '';
        $state_code   = isset( $address['state'] ) ? $address['state'] : '';
        $state_code   = ( $address['state'] == 'N/A' ) ? '' : $address['state'];

        $country_name = isset( $countries[$country_code] ) ? $countries[$country_code] : '';
        $state_name   = isset( $states[$country_code][$state_code] ) ? $states[$country_code][$state_code] : $state_code;

    } else {
        return 'N/A';
    }

    if ( $get_array == TRUE ) {
        $address = array(
            'street_1' => $street_1,
            'street_2' => $street_2,
            'city'     => $city,
            'zip'      => $zip,
            'country'  => $country_name,
            'state'    => isset( $states[$country_code][$state_code] ) ? $states[$country_code][$state_code] : $state_code,
        );

        return $address;
    }

    $country           = new WC_Countries();
    $formatted_address = $country->get_formatted_address( array(
        'address_1' => $street_1,
        'address_2' => $street_2,
        'city'      => $city,
        'postcode'  => $zip,
        'state'     => $state_code,
        'country'   => $country_code
    ) );

    return apply_filters( 'waa_get_seller_address', $formatted_address, $profile_info );
}

/**
 * Get terms and conditions page
 *
 * @since 2.3
 *
 * @param $store_id
 * @param $store_info
 *
 * @return string
 */
function waa_get_toc_url( $store_id ) {
    $userstore = waa_get_store_url( $store_id );
    return apply_filters( 'waa_get_toc_url', $userstore ."toc" );
}

