<?php

if ( !class_exists( 'WAA_Settings_API' ) ) {
    require_once waa_LIB_DIR . '/class.settings-api.php';
}

/**
 * WordPress settings API demo class
 *
 * @author Tareq Hasan
 */
class waa_Admin_Settings {

    private $settings_api;

    function __construct() {
        $this->settings_api = new WAA_Settings_API();

        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_init', array($this, 'tools_page_handler') );

        add_action( 'admin_menu', array($this, 'admin_menu') );
    }

    /**
     * Dashboard scripts and styles
     *
     * @return void
     */
    function dashboard_script() {
        wp_enqueue_style( 'waa-admin-dash', waa_PLUGIN_ASSEST . '/css/admin.css' );

        $this->report_scripts();
    }

    /**
     * Reporting scripts
     *
     * @return void
     */
    function report_scripts() {
        wp_enqueue_style( 'waa-admin-report', waa_PLUGIN_ASSEST . '/css/admin.css' );
        wp_enqueue_style( 'jquery-ui' );
        wp_enqueue_style( 'waa-chosen-style' );

        wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_enqueue_script( 'jquery-flot' );
        wp_enqueue_script( 'jquery-chart' );
        wp_enqueue_script( 'chosen' );
    }

    /**
     * Seller announcement scripts
     *
     * @since 2.1
     *
     * @return void
     */
    function announcement_scripts() {
        global $post_type;

        if ( 'waa_announcement' == $post_type ) {
            wp_enqueue_style( 'waa-chosen-style' );
            wp_enqueue_script( 'chosen' );
        }
    }

    function admin_init() {
        waa_Template_Withdraw::init()->bulk_action_handler();

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }

    function admin_menu() {
        $menu_position = apply_filters( 'doakn_menu_position', 17 );
        $capability    = apply_filters( 'doakn_menu_capability', 'manage_options' );
        $withdraw      = waa_get_withdraw_count();
        $withdraw_text = __( 'Withdraw', 'waa' );

        if ( $withdraw['pending'] ) {
            $withdraw_text = sprintf( __( 'Withdraw %s', 'waa' ), '<span class="awaiting-mod count-1"><span class="pending-count">' . $withdraw['pending'] . '</span></span>');
        }

        $dashboard = add_menu_page( __( 'Künstler Manager', 'waa' ), __( 'Künstler Manager', 'waa' ), $capability, 'waa', array($this, 'dashboard'), 'dashicons-vault', $menu_position );
        add_submenu_page( 'waa', __( 'Dashboard', 'waa' ), __( 'Dashboard', 'waa' ), $capability, 'waa', array($this, 'dashboard') );
        add_submenu_page( 'waa', __( 'Withdraw', 'waa' ), $withdraw_text, $capability, 'waa-withdraw', array($this, 'withdraw_page') );
        add_submenu_page( 'waa', __( 'Sellers Listing', 'waa' ), __( 'All Sellers', 'waa' ), $capability, 'waa-sellers', array($this, 'seller_listing') );
        $report = add_submenu_page( 'waa', __( 'Earning Reports', 'waa' ), __( 'Earning Reports', 'waa' ), $capability, 'waa-reports', array($this, 'report_page') );
        $announcement = add_submenu_page( 'waa', __( 'Announcement', 'waa' ), __( 'Announcement', 'waa' ), $capability, 'edit.php?post_type=waa_announcement' );

        do_action( 'waa_admin_menu' );

        #add_submenu_page( 'waa', __( 'Tools', 'waa' ), __( 'Tools', 'waa' ), $capability, 'waa-tools', array($this, 'tools_page') );
        add_submenu_page( 'waa', __( 'Settings', 'waa' ), __( 'Settings', 'waa' ), $capability, 'waa-settings', array($this, 'settings_page') );
        #add_submenu_page( 'waa', __( 'Add Ons', 'waa' ), __( 'Add-ons', 'waa' ), $capability, 'waa-addons', array($this, 'addon_page') );

        add_action( $dashboard, array($this, 'dashboard_script' ) );
        add_action( $report, array($this, 'report_scripts' ) );
        // add_action( $announcement, array($this, 'announcement_scripts' ) );
        add_action( 'admin_print_scripts-post-new.php', array( $this, 'announcement_scripts' ), 11 );
        add_action( 'admin_print_scripts-post.php', array( $this, 'announcement_scripts' ), 11 );

    }

    function get_settings_sections() {
        $sections = array(
            array(
                'id'    => 'waa_general',
                'title' => __( 'General', 'waa' )
            ),
            array(
                'id'    => 'waa_selling',
                'title' => __( 'Selling Options', 'waa' )
            ),
            array(
                'id'    => 'waa_pages',
                'title' => __( 'Page Settings', 'waa' )
            )
        );
        return apply_filters( 'waa_settings_sections', $sections );
    }

    function get_post_type( $post_type ) {
        $pages_array = array( '-1' => __( '- select -', 'waa' ) );
        $pages = get_posts( array('post_type' => $post_type, 'numberposts' => -1) );

        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_array[$page->ID] = $page->post_title;
            }
        }

        return $pages_array;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        $pages_array = $this->get_post_type( 'page' );
        $slider_array = $this->get_post_type( 'waa_slider' );

        $settings_fields = array(
            'waa_general' => array(
                'admin_access' => array(
                    'name'    => 'admin_access',
                    'label'   => __( 'Admin area access', 'waa' ),
                    'desc'    => __( 'Disable sellers and customers from accessing wp-admin area', 'waa' ),
                    'type'    => 'checkbox',
                    'default' => 'on'
                ),
                'store_map' => array(
                    'name'    => 'store_map',
                    'label'   => __( 'Show Map on Store Page', 'waa' ),
                    'desc'    => __( 'Enable showing Store location map on store left sidebar', 'waa' ),
                    'type'    => 'checkbox',
                    'default' => 'on'
                ),
                'contact_seller' => array(
                    'name'    => 'contact_seller',
                    'label'   => __( 'Show Contact Form on Store Page', 'waa' ),
                    'desc'    => __( 'Enable showing contact seller form on store left sidebar', 'waa' ),
                    'type'    => 'checkbox',
                    'default' => 'on'
                ),
                'enable_theme_store_sidebar' => array(
                    'name'    => 'enable_theme_store_sidebar',
                    'label'   => __( 'Enable Store Sidebar From Theme', 'waa' ),
                    'desc'    => __( 'Enable showing Store Sidebar From Your Theme.', 'waa' ),
                    'type'    => 'checkbox',
                    'default' => 'off'
                ),
                'product_add_mail' => array(
                    'name'    => 'product_add_mail',
                    'label'   => __( 'Product Mail Notification', 'waa' ),
                    'desc'    => __( 'Email notification on new product submission', 'waa' ),
                    'type'    => 'checkbox',
                    'default' => 'on'
                ),
                'store_seo' => array(
                    'name'    => 'store_seo',
                    'label'   => __( 'Enable Store SEO', 'waa' ),
                    'desc'    => __( 'Sellers can manage their Store page SEO', 'waa' ),
                    'type'    => 'checkbox',
                    'default' => 'on'
                ),
            ),
            'waa_selling' => array(
                'seller_enable_terms_and_conditions' => array(
                    'name'    => 'seller_enable_terms_and_conditions',
                    'label'   => __( 'Terms and Conditions', 'waa' ),
                    'desc'    => __( 'Enable terms and conditions for seller store', 'waa' ),
                    'type'    => 'checkbox',
                    'default' => 'off'
                 ),
                'new_seller_enable_selling' => array(
                    'name'    => 'new_seller_enable_selling',
                    'label'   => __( 'New Seller Enable Selling', 'waa' ),
                    'desc'    => __( 'Make selling status enable for new registred seller', 'waa' ),
                    'type'    => 'checkbox',
                    'default' => 'on'
                ),
                'product_style' => array(
                    'name'    => 'product_style',
                    'label'   => __( 'Add/Edit Product Style', 'waa' ),
                    'desc'    => __( 'The style you prefer for seller to add or edit products. ', 'waa' ),
                    'type'    => 'select',
                    'default' => 'old',
                    'options' => array(
                        'old' => __( 'Tab View', 'waa' ),
                        'new' => __( 'Flat View', 'waa' )
                    )
                ),
                'product_category_style' => array(
                    'name'    => 'product_category_style',
                    'label'   => __( 'Category Selection', 'waa' ),
                    'desc'    => __( 'What option do you prefer for seller to select product category? ', 'waa' ),
                    'type'    => 'select',
                    'default' => 'single',
                    'options' => array(
                        'single' => __( 'Single', 'waa' ),
                        'multiple' => __( 'Multiple', 'waa' )
                    )
                ),
                'product_status' => array(
                    'name'    => 'product_status',
                    'label'   => __( 'New Product Status', 'waa' ),
                    'desc'    => __( 'Product status when a seller creates a product', 'waa' ),
                    'type'    => 'select',
                    'default' => 'pending',
                    'options' => array(
                        'publish' => __( 'Published', 'waa' ),
                        'pending' => __( 'Pending Review', 'waa' )
                    )
                ),
                'seller_percentage' => array(
                    'name'    => 'seller_percentage',
                    'label'   => __( 'Seller Percentage', 'waa' ),
                    'desc'    => __( 'How much amount (%) a seller will get from each order', 'waa' ),
                    'default' => '90',
                    'type'    => 'text',
                ),
                'order_status_change' => array(
                    'name'    => 'order_status_change',
                    'label'   => __( 'Order Status Change', 'waa' ),
                    'desc'    => __( 'Seller can change order status', 'waa' ),
                    'type'    => 'checkbox',
                    'default' => 'on'
                ),
                'withdraw_methods' => array(
                    'name'    => 'withdraw_methods',
                    'label'   => __( 'Withdraw Methods', 'waa' ),
                    'desc'    => __( 'Withdraw methods for sellers', 'waa' ),
                    'type'    => 'multicheck',
                    'default' => array( 'paypal' => 'paypal' ),
                    'options' => waa_withdraw_get_methods()
                ),
                'withdraw_order_status' => array(
                    'name'    => 'withdraw_order_status',
                    'label'   => __( 'Order Status for Withdraw', 'waa' ),
                    'desc'    => __( 'Order status for which seller can make a withdraw request.', 'waa' ),
                    'type'    => 'multicheck',
                    'default' => array( 'wc-completed' => __( 'Completed', 'waa' ), 'wc-processing' => __( 'Processing', 'waa' ), 'wc-on-hold' => __( 'On-hold', 'waa' ) ),
                    'options' => array( 'wc-completed' => __( 'Completed', 'waa' ), 'wc-processing' => __( 'Processing', 'waa' ), 'wc-on-hold' => __( 'On-hold', 'waa' ) )
                ),
                'withdraw_limit' => array(
                    'name'    => 'withdraw_limit',
                    'label'   => __( 'Minimum Withdraw Limit', 'waa' ),
                    'desc'    => __( 'Minimum balance required to make a withdraw request', 'waa' ),
                    'default' => '50',
                    'type'    => 'text',
                ),
                'withdraw_date_limit' => array(
                    'name'    => 'withdraw_date_limit',
                    'label'   => __( 'Withdraw Threshold', 'waa' ),
                    'desc'    => __( 'Days, ( Make order matured to make a withdraw request) <br> Value "0" will inactive this option', 'waa' ),
                    'default' => '0',
                    'type'    => 'text',
                ),
                'custom_store_url' => array(
                    'name'    => 'custom_store_url',
                    'label'   => __( 'Seller Store URL', 'waa' ),
                    'desc'    => sprintf( __( 'Define seller store URL (%s<strong>[this-text]</strong>/[seller-name])', 'waa' ), site_url( '/' ) ),
                    'default' => 'store',
                    'type'    => 'text',
                ),
                'review_edit' => array(
                    'name'    => 'review_edit',
                    'label'   => __( 'Review Editing', 'waa' ),
                    'desc'    => __( 'Seller can edit product reviews', 'waa' ),
                    'type'    => 'checkbox',
                    'default' => 'off'
                ),
            ),
            'waa_pages' => array(
                'dashboard' => array(
                    'name'    => 'dashboard',
                    'label'   => __( 'Dashboard', 'waa' ),
                    'type'    => 'select',
                    'options' => $pages_array
                ),
                'my_orders' => array(
                    'name'    => 'my_orders',
                    'label'   => __( 'My Orders', 'waa' ),
                    'type'    => 'select',
                    'options' => $pages_array
                )
            )
        );

        return apply_filters( 'waa_settings_fields', $settings_fields );
    }

    function dashboard() {
        include dirname(__FILE__) . '/dashboard.php';
    }

    function settings_page() {
        echo '<div class="wrap">';
        settings_errors();

        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo '</div>';
    }

    function withdraw_page() {
        include dirname(__FILE__) . '/withdraw.php';
    }

    function seller_listing() {
        include dirname(__FILE__) . '/sellers.php';
    }

    function report_page() {
        global $wpdb;

        include dirname(__FILE__) . '/reports.php';
    }

    function addon_page() {
        include dirname(__FILE__) . '/add-on.php';
    }

    function tools_page() {
        include dirname(__FILE__) . '/tools.php';
    }

    function tools_page_handler() {
        if ( isset( $_GET['waa_action'] ) && current_user_can( 'manage_options' ) ) {
            $action = $_GET['waa_action'];

            check_admin_referer( 'waa-tools-action' );

            switch ($action) {
                case 'waa_install_pages':

                    $pages = array(
                        array(
                            'post_title' => __( 'Dashboard', 'waa' ),
                            'slug'       => 'dashboard',
                            'page_id'    => 'dashboard',
                            'content'    => '[waa-dashboard]'
                        ),
                        array(
                            'post_title' => __( 'Store List', 'waa' ),
                            'slug'       => 'store-listing',
                            'page_id'    => 'my_orders',
                            'content'    => '[waa-stores]'
                        ),
                    );

                    foreach ($pages as $page) {
                        $page_id = wp_insert_post( array(
                            'post_title'     => $page['post_title'],
                            'post_name'      => $page['slug'],
                            'post_content'   => $page['content'],
                            'post_status'    => 'publish',
                            'post_type'      => 'page',
                            'comment_status' => 'closed'
                        ) );

                        if ( $page['slug'] == 'dashboard' ) {
                            update_option( 'waa_pages', array( 'dashboard' => $page_id ) );
                        }
                    }

                    flush_rewrite_rules();

                    wp_redirect( admin_url( 'admin.php?page=waa-tools&msg=page_installed' ) );
                    exit;

                    break;

                case 'regen_sync_table':
                    waa_generate_sync_table();

                    wp_redirect( admin_url( 'admin.php?page=waa-tools&msg=regenerated' ) );
                    exit;
                    break;

                default:
                    # code...
                    break;
            }
        }
    }
}

$settings = new waa_Admin_Settings();
