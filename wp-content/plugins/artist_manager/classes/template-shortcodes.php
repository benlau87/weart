<?php

/**
 * Tempalte shortcode class file
 *
 * @load all shortcode for template  rendering
 */
class waa_Template_Shortcodes
{

    public static $errors;
    public static $product_cat;
    public static $post_content;
    public static $validated;
    public static $validate;

    /**
     *  waa template shortcodes __constract
     *  Initial loaded when class create an instanace
     */
    function __construct()
    {

        add_action('template_redirect', array($this, 'handle_all_submit'), 11);
        add_action('template_redirect', array($this, 'handle_delete_product'));
        add_action('template_redirect', array($this, 'handle_withdraws'));
        add_action('template_redirect', array($this, 'handle_coupons'));
        add_action('template_redirect', array($this, 'handle_order_export'));
        add_action('template_redirect', array($this, 'handle_shipping'));

        add_shortcode('waa-dashboard', array($this, 'load_template_files'));
        add_shortcode('waa-best-selling-product', array($this, 'best_selling_product_shortcode'));
        add_shortcode('waa-top-rated-product', array($this, 'top_rated_product_shortcode'));
        add_shortcode('waa-stores', array($this, 'store_listing'));
        add_shortcode('waa-my-orders', array($this, 'my_orders_page'));
    }

    /**
     * Singleton method
     *
     * @return self
     */
    public static function init()
    {
        static $instance = false;

        if (!$instance) {
            $instance = new waa_Template_Shortcodes();
        }

        return $instance;
    }

    /**
     * Load template files
     *
     * Based on the query vars, load the appropriate template files
     * in the frontend user dashboard.
     *
     * @return void
     */
    public function load_template_files()
    {
        global $wp;

        if (!function_exists('WC')) {
            return sprintf(__('Please install <a href="%s"><strong>WooCommerce</strong></a> plugin first', 'waa'), 'http://wordpress.org/plugins/woocommerce/');
        }

        if (isset($wp->query_vars['reports'])) {
            waa_get_template_part('reports');
            return;
        }

        if (isset($wp->query_vars['products'])) {
            waa_get_template_part('products');
            return;
        }

        if (isset($wp->query_vars['new-product'])) {
            if (waa_get_option('product_style', 'waa_selling', 'old') == 'old') {
                waa_get_template_part('new-product');
            } elseif (waa_get_option('product_style', 'waa_selling', 'old') == 'new') {
                waa_get_template_part('new-product-single');
            }
            return;
        }

        if (isset($wp->query_vars['orders'])) {
            waa_get_template_part('orders');
            return;
        }

        if (isset($wp->query_vars['coupons'])) {
            waa_get_template_part('coupons');
            return;
        }

        if (isset($wp->query_vars['reviews'])) {
            waa_get_template_part('reviews');
            return;
        }

        if (isset($wp->query_vars['withdraw'])) {
            waa_get_template_part('withdraw');
            return;
        }

        if (isset($wp->query_vars['announcement'])) {
            waa_get_template_part('announcement');
            return;
        }

        if (isset($wp->query_vars['single-announcement'])) {
            waa_get_template_part('single-announcement');
            return;
        }

        if (isset($wp->query_vars['settings'])) {
            switch ($wp->query_vars['settings']) {

                case 'store':
                    waa_get_template_part('settings/store');
                    break;

                case 'social':
                    waa_get_template_part('settings/social');
                    break;

                case 'shipping':
                    $waa_shipping_option = get_option('woocommerce_waa_product_shipping_settings', array('enabled' => 'yes'));
                    $enable_shipping = (isset($waa_shipping_option['enabled'])) ? $waa_shipping_option['enabled'] : 'yes';

                    if ($enable_shipping == 'yes') {
                        waa_get_template_part('settings/shipping');
                    }
                    break;

                case 'payment':
                    waa_get_template_part('settings/payment');
                    break;

                case 'seo':
                    waa_get_template_part('settings/seo');
                    break;

                default:
                    /**
                     * Allow plugins too hook into here and add their
                     * own settings pages
                     *
                     * @since 2.2
                     */
                    $template_path = apply_filters('waa_settings_template', false, $wp->query_vars['settings']);

                    if ($template_path !== false && file_exists($template_path)) {
                        require_once $template_path;
                    }
                    break;
            }
        }

        if (isset($wp->query_vars['page'])) {
            waa_get_template_part('dashboard');
            return;
        }

        do_action('waa_load_custom_template', $wp->query_vars);
    }

    /**
     * Handle all the form POST submit
     *
     * @return void
     */
    function handle_all_submit()
    {

        if (!is_user_logged_in()) {
            return;
        }

        if (!waa_is_user_seller(get_current_user_id())) {
            return;
        }

        $errors = array();
        self::$product_cat = -1;
        self::$post_content = __('Details about your product...', 'waa');

        if (!$_POST) {
            return;
        }

        if (isset($_POST['waa_add_product']) && wp_verify_nonce($_POST['waa_add_new_product_nonce'], 'waa_add_new_product')) {

            $post_title = trim($_POST['post_title']);
            $post_content = trim($_POST['post_content']);
            $post_excerpt = isset($_POST['post_excerpt']) ? trim($_POST['post_excerpt']) : '';
            $price = floatval($_POST['_regular_price']);
            $featured_image = absint($_POST['feat_image_id']);


            /**
             * validate, if artist has entered shipping information
             */


            if (empty($post_title)) {

                $errors[] = __('Please enter product title', 'waa');
            }

            if (empty($featured_image)) {

                $errors[] = __('Choose an image', 'waa');
            }

            if (waa_get_option('product_category_style', 'waa_selling', 'single') == 'single') {
                $product_cat = intval($_POST['product_cat']);
                if ($product_cat < 0) {
                    $errors[] = __('Please select a category', 'waa');
                }
            } else {
                if (!isset($_POST['product_cat']) && empty($_POST['product_cat'])) {
                    $errors[] = __('Please select atleast one category', 'waa');
                }
            }

            self::$errors = apply_filters('waa_can_add_product', $errors);

            if (!self::$errors) {

                if (isset($_POST['waa_product_id']) && empty($_POST['waa_product_id'])) {

                    $post_data = apply_filters('waa_insert_product_post_data', array(
                        'post_type' => 'product',
                        # 'post_status'  => 'draft',
                        'post_status' => 'publish',
                        'post_title' => $post_title,
                        'post_content' => $post_content,
                        'post_excerpt' => $post_excerpt,
                    ));

                    $product_id = wp_insert_post($post_data);

                } else {
                    $post_id = (int)$_POST['waa_product_id'];
                    $product_status = waa_get_new_post_status();
                    $product_info = apply_filters('waa_update_product_post_data', array(
                        'ID' => $post_id,
                        'post_title' => sanitize_text_field($_POST['post_title']),
                        'post_content' => $_POST['post_content'],
                        'post_excerpt' => $_POST['post_excerpt'],
                        #  'post_status'    => isset( $_POST['post_status'] ) ? ( $_POST['post_status'] == 'draft' ) ? $product_status : $_POST['post_status'] : 'pending',
                        'post_status' => 'publish',
                        'comment_status' => isset($_POST['_enable_reviews']) ? 'open' : 'closed'
                    ));

                    $product_id = wp_update_post($product_info);
                }

                if ($product_id) {

                    /** set images **/
                    if ($featured_image) {
                        set_post_thumbnail($product_id, $featured_image);
                    }

                    if (isset($_POST['product_tag']) && !empty($_POST['product_tag'])) {
                        $tags_ids = array_map('intval', (array)$_POST['product_tag']);
                        wp_set_object_terms($product_id, $tags_ids, 'product_tag');
                    }

                    /** set product category * */
                    if (waa_get_option('product_category_style', 'waa_selling', 'single') == 'single') {
                        wp_set_object_terms($product_id, (int)$_POST['product_cat'], 'product_cat');
                    } else {
                        if (isset($_POST['product_cat']) && !empty($_POST['product_cat'])) {
                            $cat_ids = array_map('intval', (array)$_POST['product_cat']);
                            wp_set_object_terms($product_id, $cat_ids, 'product_cat');
                        }
                    }

                    /** Set Product type by default simple */
                    if (isset($_POST['_create_variation']) && $_POST['_create_variation'] == 'yes') {
                        wp_set_object_terms($product_id, 'variable', 'product_type');
                    } else {
                        wp_set_object_terms($product_id, 'simple', 'product_type');
                    }


                    update_post_meta($product_id, '_regular_price', $price);
                    update_post_meta($product_id, '_sale_price', '');
                    update_post_meta($product_id, '_price', $price);
                    update_post_meta($product_id, '_visibility', 'visible');

                    waa_new_process_product_meta($product_id);

                    if (isset($_POST['waa_product_id']) && empty($_POST['waa_product_id'])) {
                        do_action('waa_new_product_added', $product_id, $post_data);
                    }

                    if (isset($_POST['waa_product_id']) && empty($_POST['waa_product_id'])) {
                        if (waa_get_option('product_add_mail', 'waa_general', 'on') == 'on') {
                            waa_Email::init()->new_product_added($product_id, $product_status);
                        }
                    }

                    wp_redirect(add_query_arg(array('message' => 'success'), waa_edit_product_url($product_id)));
                    exit;
                }
            }
        }

        if (isset($_POST['add_product']) && wp_verify_nonce($_POST['waa_add_new_product_nonce'], 'waa_add_new_product')) {
            $post_title = trim($_POST['post_title']);
            $post_content = trim($_POST['post_content']);
            $post_excerpt = trim($_POST['post_excerpt']);
            $price = floatval($_POST['price']);
            $featured_image = absint($_POST['feat_image_id']);

            if (empty($post_title)) {

                $errors[] = __('Please enter product title', 'waa');
            }

            if (empty($featured_image)) {

                $errors[] = __('Choose an image', 'waa');
            }

            if (waa_get_option('product_category_style', 'waa_selling', 'single') == 'single') {
                $product_cat = intval($_POST['product_cat']);
                if ($product_cat < 0) {
                    $errors[] = __('Please select a category', 'waa');
                }
            } else {
                if (!isset($_POST['product_cat']) && empty($_POST['product_cat'])) {
                    $errors[] = __('Please select atleast one category', 'waa');
                }
            }

            self::$errors = apply_filters('waa_can_add_product', $errors);

            if (!self::$errors) {


                $post_data = apply_filters('waa_insert_product_post_data', array(
                    'post_type' => 'product',
                    'post_status' => 'draft',
                    'post_title' => $post_title,
                    'post_content' => $post_content,
                    'post_excerpt' => $post_excerpt,
                ));

                $product_id = wp_insert_post($post_data);

                if ($product_id) {

                    /** set images **/
                    if ($featured_image) {
                        set_post_thumbnail($product_id, $featured_image);
                    }

                    if (isset($_POST['product_tag']) && !empty($_POST['product_tag'])) {
                        $tags_ids = array_map('intval', (array)$_POST['product_tag']);
                        wp_set_object_terms($product_id, $tags_ids, 'product_tag');
                    }

                    /** set product category * */
                    if (waa_get_option('product_category_style', 'waa_selling', 'single') == 'single') {
                        wp_set_object_terms($product_id, (int)$_POST['product_cat'], 'product_cat');
                    } else {
                        if (isset($_POST['product_cat']) && !empty($_POST['product_cat'])) {
                            $cat_ids = array_map('intval', (array)$_POST['product_cat']);
                            wp_set_object_terms($product_id, $cat_ids, 'product_cat');
                        }
                    }

                    /** Set Product type by default simple */
                    wp_set_object_terms($product_id, 'simple', 'product_type');

                    update_post_meta($product_id, '_regular_price', $price);
                    update_post_meta($product_id, '_sale_price', '');
                    update_post_meta($product_id, '_price', $price);
                    update_post_meta($product_id, '_visibility', 'visible');

                    do_action('waa_new_product_added', $product_id, $post_data);

                    if (waa_get_option('product_add_mail', 'waa_general', 'on') == 'on') {
                        waa_Email::init()->new_product_added($product_id, 'draft');
                    }

                    wp_redirect(waa_edit_product_url($product_id));
                    exit;
                }
            }
        }


        if (isset($_GET['product_id'])) {
            $post_id = intval($_GET['product_id']);
        } else {
            global $post, $product;

            if (!empty($post)) {
                $post_id = $post->ID;
            }
        }


        if (isset($_POST['update_product']) && wp_verify_nonce($_POST['waa_edit_product_nonce'], 'waa_edit_product')) {
            $post_title = trim($_POST['post_title']);
            if (empty($post_title)) {

                $errors[] = __('Please enter product title', 'waa');
            }

            if (waa_get_option('product_category_style', 'waa_selling', 'single') == 'single') {
                $product_cat = intval($_POST['product_cat']);
                if ($product_cat < 0) {
                    $errors[] = __('Please select a category', 'waa');
                }
            } else {
                if (!isset($_POST['product_cat']) && empty($_POST['product_cat'])) {
                    $errors[] = __('Please select atleast one category', 'waa');
                }
            }

            self::$errors = apply_filters('waa_can_edit_product', $errors);

            if (!self::$errors) {

                $product_status = waa_get_new_post_status();
                $product_info = array(
                    'ID' => $post_id,
                    'post_title' => sanitize_text_field($_POST['post_title']),
                    'post_content' => $_POST['post_content'],
                    'post_excerpt' => $_POST['post_excerpt'],
                    'post_status' => isset($_POST['post_status']) ? ($_POST['post_status'] == 'draft') ? $product_status : $_POST['post_status'] : 'pending',
                    'comment_status' => isset($_POST['_enable_reviews']) ? 'open' : 'closed'
                );

                wp_update_post($product_info);

                /** Set Product tags */
                if (isset($_POST['product_tag'])) {
                    $tags_ids = array_map('intval', (array)$_POST['product_tag']);
                } else {
                    $tags_ids = array();
                }
                wp_set_object_terms($post_id, $tags_ids, 'product_tag');


                /** set product category * */

                if (waa_get_option('product_category_style', 'waa_selling', 'single') == 'single') {
                    wp_set_object_terms($post_id, (int)$_POST['product_cat'], 'product_cat');
                } else {
                    if (isset($_POST['product_cat']) && !empty($_POST['product_cat'])) {
                        $cat_ids = array_map('intval', (array)$_POST['product_cat']);
                        wp_set_object_terms($post_id, $cat_ids, 'product_cat');
                    }
                }

                wp_set_object_terms($post_id, 'simple', 'product_type');

                /**  Process all variation products meta */
                waa_process_product_meta($post_id);

                /** set images **/
                $featured_image = absint($_POST['feat_image_id']);
                if ($featured_image) {
                    set_post_thumbnail($post_id, $featured_image);
                }

                $edit_url = waa_edit_product_url($post_id);
                wp_redirect(add_query_arg(array('message' => 'success'), $edit_url));
                exit;
            }
        }


    }

    /**
     * Handle the coupons submission
     *
     * @return void
     */
    function handle_coupons()
    {

        if (!is_user_logged_in()) {
            return;
        }

        if (!waa_is_user_seller(get_current_user_id())) {
            return;
        }

        // Coupon functionality
        $waa_template_coupons = waa_Template_Coupons::init();

        self::$validated = $waa_template_coupons->validate();

        if (!is_wp_error(self::$validated)) {
            $waa_template_coupons->coupons_create();
        }

        $waa_template_coupons->coupun_delete();
    }

    /**
     * Handle delete product link
     *
     * @return void
     */
    function handle_delete_product()
    {

        if (!is_user_logged_in()) {
            return;
        }

        if (!waa_is_user_seller(get_current_user_id())) {
            return;
        }

        waa_delete_product_handler();
    }

    /**
     * Handle Withdraw form submission
     *
     * @return void
     */
    function handle_withdraws()
    {
        // Withdraw functionality
        $waa_withdraw = waa_Template_Withdraw::init();
        self::$validate = $waa_withdraw->validate();

        if (self::$validate !== false && !is_wp_error(self::$validate)) {
            $waa_withdraw->insert_withdraw_info();
        }

        $waa_withdraw->cancel_pending();
    }

    /**
     * Export user orders to CSV format
     *
     * @since 1.4
     * @return void
     */
    function handle_order_export()
    {
        if (!is_user_logged_in()) {
            return;
        }

        if (!waa_is_user_seller(get_current_user_id())) {
            return;
        }

        if (isset($_POST['waa_order_export_all'])) {

            $filename = "Orders-" . time();
            header("Content-Type: application/csv; charset=" . get_option('blog_charset'));
            header("Content-Disposition: attachment; filename=$filename.csv");

            $headers = array(
                'order_id' => __('Order No', 'waa'),
                'order_items' => __('Order Items', 'waa'),
                'order_shipping' => __('Shipping method', 'waa'),
                'order_shipping_cost' => __('Shipping Cost', 'waa'),
                'order_payment_method' => __('Payment method', 'waa'),
                'order_total' => __('Order Total', 'waa'),
                'order_status' => __('Order Status', 'waa'),
                'order_date' => __('Order Date', 'waa'),
                'customer_name' => __('Customer Name', 'waa'),
                'customer_email' => __('Customer Email', 'waa'),
                'customer_phone' => __('Customer Phone', 'waa'),
                'customer_ip' => __('Customer IP', 'waa'),
            );

            foreach ((array)$headers as $label) {
                echo $label . ', ';
            }

            echo "\r\n";
            $user_orders = waa_get_seller_orders(get_current_user_id(), 'all', NULL, 10000000, 0);
            $statuses = wc_get_order_statuses();
            $results = array();
            foreach ($user_orders as $order) {
                $the_order = new WC_Order($order->order_id);

                $customer = get_post_meta($order->order_id, '_customer_user', true);
                if ($customer) {
                    $customer_details = get_user_by('id', $customer);
                    $customer_name = $customer_details->user_login;
                    $customer_email = esc_html(get_post_meta($order->order_id, '_billing_email', true));
                    $customer_phone = esc_html(get_post_meta($order->order_id, '_billing_phone', true));
                    $customer_ip = esc_html(get_post_meta($order->order_id, '_customer_ip_address', true));
                } else {
                    $customer_name = get_post_meta($order->id, '_billing_first_name', true) . ' ' . get_post_meta($order->id, '_billing_last_name', true) . '(Guest)';
                    $customer_email = esc_html(get_post_meta($order->order_id, '_billing_email', true));
                    $customer_phone = esc_html(get_post_meta($order->order_id, '_billing_phone', true));
                    $customer_ip = esc_html(get_post_meta($order->order_id, '_customer_ip_address', true));
                }

                $results = array(
                    'order_id' => $order->order_id,
                    'order_items' => waa_get_product_list_by_order($the_order, ';'),
                    'order_shipping' => $the_order->get_shipping_method(),
                    'order_shipping_cost' => $the_order->get_total_shipping(),
                    'order_payment_method' => get_post_meta($order->order_id, '_payment_method_title', true),
                    'order_total' => $the_order->get_total(),
                    'order_status' => $statuses[$the_order->post_status],
                    'order_date' => $the_order->order_date,
                    'customer_name' => $customer_name,
                    'customer_email' => $customer_email,
                    'customer_phone' => $customer_phone,
                    'customer_ip' => $customer_ip,
                );

                foreach ($results as $csv_key => $csv_val) {
                    echo $csv_val . ', ';
                }
                echo "\r\n";
            }
            exit();
        }

        if (isset($_POST['waa_order_export_filtered'])) {

            $filename = "Orders-" . time();
            header("Content-Type: application/csv; charset=" . get_option('blog_charset'));
            header("Content-Disposition: attachment; filename=$filename.csv");

            $headers = array(
                'order_id' => __('Order No', 'waa'),
                'order_items' => __('Order Items', 'waa'),
                'order_shipping' => __('Shipping method', 'waa'),
                'order_shipping_cost' => __('Shipping Cost', 'waa'),
                'order_payment_method' => __('Payment method', 'waa'),
                'order_total' => __('Order Total', 'waa'),
                'order_status' => __('Order Status', 'waa'),
                'order_date' => __('Order Date', 'waa'),
                'customer_name' => __('Customer Name', 'waa'),
                'customer_email' => __('Customer Email', 'waa'),
                'customer_phone' => __('Customer Phone', 'waa'),
                'customer_ip' => __('Customer IP', 'waa'),
            );

            foreach ((array)$headers as $label) {
                echo $label . ', ';
            }
            echo "\r\n";

            $order_date = (isset($_POST['order_date'])) ? $_POST['order_date'] : NULL;
            $order_status = (isset($_POST['order_status'])) ? $_POST['order_status'] : 'all';
            $user_orders = waa_get_seller_orders(get_current_user_id(), $order_status, $order_date, 10000000, 0);
            $statuses = wc_get_order_statuses();
            $results = array();

            foreach ($user_orders as $order) {
                $the_order = new WC_Order($order->order_id);

                $customer = get_post_meta($order->order_id, '_customer_user', true);
                if ($customer) {
                    $customer_details = get_user_by('id', $customer);
                    $customer_name = $customer_details->user_login;
                    $customer_email = esc_html(get_post_meta($order->order_id, '_billing_email', true));
                    $customer_phone = esc_html(get_post_meta($order->order_id, '_billing_phone', true));
                    $customer_ip = esc_html(get_post_meta($order->order_id, '_customer_ip_address', true));
                } else {
                    $customer_name = get_post_meta($order->id, '_billing_first_name', true) . ' ' . get_post_meta($order->id, '_billing_last_name', true) . '(Guest)';
                    $customer_email = esc_html(get_post_meta($order->order_id, '_billing_email', true));
                    $customer_phone = esc_html(get_post_meta($order->order_id, '_billing_phone', true));
                    $customer_ip = esc_html(get_post_meta($order->order_id, '_customer_ip_address', true));
                }

                $results = array(
                    'order_id' => $order->order_id,
                    'order_items' => waa_get_product_list_by_order($the_order),
                    'order_shipping' => $the_order->get_shipping_method(),
                    'order_shipping_cost' => $the_order->get_total_shipping(),
                    'order_payment_method' => get_post_meta($order->order_id, '_payment_method_title', true),
                    'order_total' => $the_order->get_total(),
                    'order_status' => $statuses[$the_order->post_status],
                    'order_date' => $the_order->order_date,
                    'customer_name' => $customer_name,
                    'customer_email' => $customer_email,
                    'customer_phone' => $customer_phone,
                    'customer_ip' => $customer_ip,
                );

                foreach ($results as $csv_key => $csv_val) {
                    echo $csv_val . ', ';
                }
                echo "\r\n";
            }
            exit();
        }
    }

    /**
     *  Handle Shipping post submit
     *
     * @since  2.0
     * @return void
     */
    function handle_shipping()
    {
        if (!is_user_logged_in()) {
            return;
        }

        if (!waa_is_user_seller(get_current_user_id())) {
            return;
        }


        if (isset($_POST['waa_update_shipping_options']) && wp_verify_nonce($_POST['waa_shipping_form_field_nonce'], 'waa_shipping_form_field') && $_POST['dps_pt'] && $_POST['dps_form_location'] &&
            isset($_POST['dps_country_to'])) {

            $user_id = get_current_user_id();
            $s_rates = array();
            $rates = array();

            if (isset($_POST['dps_enable_shipping'])) {
                update_user_meta($user_id, '_dps_shipping_enable', $_POST['dps_enable_shipping']);
            }

            if (isset($_POST['dps_enable_shipping'])) {
                update_user_meta($user_id, '_dps_enable_pickup', $_POST['dps_enable_pickup']);
            }

            if (isset($_POST['waa_shipping_type'])) {
                update_user_meta($user_id, '_waa_shipping_type', $_POST['waa_shipping_type']);
            }

            if (isset($_POST['dps_shipping_type_price'])) {
                update_user_meta($user_id, '_dps_shipping_type_price', $_POST['dps_shipping_type_price']);
            }

            if (isset($_POST['dps_additional_product'])) {
                update_user_meta($user_id, '_dps_additional_product', $_POST['dps_additional_product']);
            }

            if (isset($_POST['dps_additional_qty'])) {
                update_user_meta($user_id, '_dps_additional_qty', $_POST['dps_additional_qty']);
            }

            if (isset($_POST['dps_pt'])) {
                update_user_meta($user_id, '_dps_pt', $_POST['dps_pt']);
            }

            if (isset($_POST['dps_ship_policy'])) {
                update_user_meta($user_id, '_dps_ship_policy', $_POST['dps_ship_policy']);
            }

            if (isset($_POST['dps_refund_policy'])) {
                update_user_meta($user_id, '_dps_refund_policy', $_POST['dps_refund_policy']);
            }

            if (isset($_POST['dps_form_location'])) {
                update_user_meta($user_id, '_dps_form_location', $_POST['dps_form_location']);
            }

            if (isset($_POST['dps_country_to'])) {

                foreach ($_POST['dps_country_to'] as $key => $value) {
                    $country = $value;
                    $c_price = floatval($_POST['dps_country_to_price'][$key]);

                    if (!$c_price && empty($c_price)) {
                        $c_price = 0;
                    }

                    if (!empty($value)) {
                        $rates[$country] = $c_price;
                    }
                }
            }

            update_user_meta($user_id, '_dps_country_rates', $rates);

            if (isset($_POST['dps_state_to'])) {
                foreach ($_POST['dps_state_to'] as $country_code => $states) {

                    foreach ($states as $key_val => $name) {
                        $country_c = $country_code;
                        $state_code = $name;
                        $s_price = floatval($_POST['dps_state_to_price'][$country_c][$key_val]);

                        if (!$s_price || empty($s_price)) {
                            $s_price = 0;
                        }

                        if (!empty($name)) {
                            $s_rates[$country_c][$state_code] = $s_price;
                        }
                    }
                }
            }

            update_user_meta($user_id, '_dps_state_rates', $s_rates);

            $shipping_url = waa_get_navigation_url('settings/shipping');
            wp_redirect(add_query_arg(array('message' => 'shipping_saved'), $shipping_url));
            exit;

        } elseif (isset($_POST['waa_update_shipping_options']) && wp_verify_nonce($_POST['waa_shipping_form_field_nonce'], 'waa_shipping_form_field')) {
            $shipping_url = waa_get_navigation_url('settings/shipping');
            wp_redirect(add_query_arg(array('message' => 'shipping_not_saved'), $shipping_url));
            exit;
        }
    }

    /**
     * Render best selling products
     *
     * @param  array $atts
     *
     * @return string
     */
    function best_selling_product_shortcode($atts)
    {
        /**
         * Filter return the number of best selling product per page.
         *
         * @since 2.2
         *
         * @param array
         */
        $per_page = shortcode_atts(apply_filters('waa_best_selling_product_per_page', array(
            'no_of_product' => 8
        ), $atts));

        ob_start();
        ?>
        <ul>
            <?php
            $best_selling_query = waa_get_best_selling_products();
            ?>
            <?php while ($best_selling_query->have_posts()) : $best_selling_query->the_post(); ?>

                <?php wc_get_template_part('content', 'product'); ?>

            <?php endwhile; ?>
        </ul>
        <?php

        return ob_get_clean();
    }

    /**
     * Render top rated products via shortcode
     *
     * @param  array $atts
     *
     * @return string
     */
    function top_rated_product_shortcode($atts)
    {
        /**
         * Filter return the number of top rated product per page.
         *
         * @since 2.2
         *
         * @param array
         */
        $per_page = shortcode_atts(apply_filters('waa_top_rated_product_per_page', array(
            'no_of_product' => 8
        ), $atts));

        ob_start();
        ?>
        <ul>
            <?php
            $best_selling_query = waa_get_top_rated_products();
            ?>
            <?php while ($best_selling_query->have_posts()) : $best_selling_query->the_post(); ?>

                <?php wc_get_template_part('content', 'product'); ?>

            <?php endwhile; ?>
        </ul>
        <?php

        return ob_get_clean();
    }

    /**
     * Displays the store lists
     *
     * @param  array $atts
     * @return string
     */
    function store_listing($atts)
    {
        global $post;

        /**
         * Filter return the number of store listing number per page.
         *
         * @since 2.2
         *
         * @param array
         */
        $attr = shortcode_atts(apply_filters('waa_store_listing_per_page', array(
            'per_page' => 10,
        )), $atts);

        $paged = max(1, get_query_var('paged'));
        $limit = $attr['per_page'];
        $offset = ($paged - 1) * $limit;

        $sellers = waa_get_sellers($limit, $offset);

        ob_start();

        if ($sellers['users']) {
            ?>
            <ul class="waa-seller-wrap">
                <?php
                foreach ($sellers['users'] as $seller) {
                    $store_info = waa_get_store_info($seller->ID);
                    $banner_id = isset($store_info['banner']) ? $store_info['banner'] : 0;
                    $store_name = isset($store_info['store_name']) ? esc_html($store_info['store_name']) : __('N/A', 'waa');
                    $store_url = waa_get_store_url($seller->ID);
                    ?>

                    <li class="waa-single-seller">
                        <div class="waa-store-thumbnail">

                            <a href="<?php echo $store_url; ?>">
                                <?php if ($banner_id) {
                                    $banner_url = wp_get_attachment_image_src($banner_id, 'medium');
                                    ?>
                                    <img class="waa-store-img" src="<?php echo esc_url($banner_url[0]); ?>"
                                         alt="<?php echo esc_attr($store_name); ?>">
                                <?php } else { ?>
                                    <img class="waa-store-img" src="<?php echo waa_get_no_seller_image(); ?>"
                                         alt="<?php _e('No Image', 'waa'); ?>">
                                <?php } ?>
                            </a>

                            <div class="waa-store-caption">
                                <h3><a href="<?php echo $store_url; ?>"><?php echo $store_name; ?></a></h3>

                                <address>
                                    <?php if (isset($store_info['address']) && !empty($store_info['address'])) {

                                        echo waa_get_seller_address($seller->ID);

                                    } ?>

                                    <?php if (isset($store_info['phone']) && !empty($store_info['phone'])) { ?>
                                        <br>
                                        <abbr
                                            title="<?php _e('Phone', 'waa'); ?>"><?php _e('P:', 'waa'); ?></abbr> <?php echo esc_html($store_info['phone']); ?>
                                    <?php } ?>

                                </address>

                                <p><a class="waa-btn waa-btn-theme"
                                      href="<?php echo $store_url; ?>"><?php _e('Visit Store', 'waa'); ?></a></p>

                            </div> <!-- .caption -->
                        </div> <!-- .thumbnail -->
                    </li> <!-- .single-seller -->
                <?php } ?>

            </ul> <!-- .waa-seller-wrap -->

            <?php
            $user_count = $sellers['count'];
            $num_of_pages = ceil($user_count / $limit);

            if ($num_of_pages > 1) {
                echo '<div class="pagination-container clearfix">';
                $page_links = paginate_links(array(
                    'current' => $paged,
                    'total' => $num_of_pages,
                    'base' => str_replace($post->ID, '%#%', esc_url(get_pagenum_link($post->ID))),
                    'type' => 'array',
                    'prev_text' => __('&larr; Previous', 'waa'),
                    'next_text' => __('Next &rarr;', 'waa'),
                ));

                if ($page_links) {
                    $pagination_links = '<div class="pagination-wrap">';
                    $pagination_links .= '<ul class="pagination"><li>';
                    $pagination_links .= join("</li>\n\t<li>", $page_links);
                    $pagination_links .= "</li>\n</ul>\n";
                    $pagination_links .= '</div>';

                    echo $pagination_links;
                }

                echo '</div>';
            }
            ?>

            <?php
        } else {
            ?>

            <p class="waa-error"><?php _e('No seller found!', 'waa'); ?></p>

            <?php
        }

        $content = ob_get_clean();

        return apply_filters('waa_seller_listing', $content, $attr);
    }

    /**
     * Render my orders page
     *
     * @return string
     */
    function my_orders_page()
    {
        return waa_get_template_part('my-orders');
    }

}
