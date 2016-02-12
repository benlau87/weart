<?php
/*
Plugin Name: WAA Artist Manager
Plugin URI: 
Description: Manage Artists, their products, sales, commissions, etc.
Version: 1.0
Author: Benedikt Laufer
Author URI: 
*/

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Backwards compatibility for older than PHP 5.3.0
if ( !defined( '__DIR__' ) ) {
    define( '__DIR__', dirname( __FILE__ ) );
}

define( 'waa_PLUGIN_VERSION', '1.0' );
define( 'waa_DIR', __DIR__ );
define( 'waa_INC_DIR', __DIR__ . '/includes' );
define( 'waa_LIB_DIR', __DIR__ . '/lib' );
define( 'waa_PLUGIN_ASSEST', plugins_url( 'assets', __FILE__ ) );
// give a way to turn off loading styles and scripts from parent theme

if ( !defined( 'waa_LOAD_STYLE' ) ) {
    define( 'waa_LOAD_STYLE', true );
}

if ( !defined( 'waa_LOAD_SCRIPTS' ) ) {
    define( 'waa_LOAD_SCRIPTS', true );
}

/**
 * Autoload class files on demand
 *
 * `waa_Installer` becomes => installer.php
 * `waa_Template_Report` becomes => template-report.php
 *
 * @param string  $class requested class name
 */
function waa_autoload( $class ) {
    if ( stripos( $class, 'waa_' ) !== false ) {
        $class_name = str_replace( array( 'waa_', '_' ), array( '', '-' ), $class );
        $file_path = __DIR__ . '/classes/' . strtolower( $class_name ) . '.php';

        if ( file_exists( $file_path ) ) {
            require_once $file_path;
        }
    }
}

spl_autoload_register( 'waa_autoload' );

/**
 * WAA_waa class
 *
 * @class WAA_waa The class that holds the entire WAA_waa plugin
 */
class WAA_waa {

    /**
     * Constructor for the WAA_waa class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     *
     * @uses register_activation_hook()
     * @uses register_deactivation_hook()
     * @uses is_admin()
     * @uses add_action()
     */
    public function __construct() {
        global $wpdb;

        $wpdb->waa_withdraw = $wpdb->prefix . 'waa_withdraw';
        $wpdb->waa_orders   = $wpdb->prefix . 'waa_orders';

        //includes file
        $this->includes();

        // init actions and filter
        $this->init_filters();
        $this->init_actions();

        // initialize classes
        $this->init_classes();

        //for reviews ajax request
        $this->init_ajax();

        do_action( 'waa_loaded' );
    }

    /**
     * Initializes the WAA_waa() class
     *
     * Checks for an existing WAA_WAA_waa() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new WAA_waa();
        }

        return $instance;
    }

    /**
     * Get the plugin path.
     *
     * @return string
     */
    public function plugin_path() {
        return untrailingslashit( plugin_dir_path( __FILE__ ) );
    }

    /**
     * Get the template path.
     *
     * @return string
     */
    public function template_path() {
        return apply_filters( 'waa_template_path', 'waa/' );
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     */
    public static function activate() {
        global $wpdb;

        $wpdb->waa_withdraw     = $wpdb->prefix . 'waa_withdraw';
        $wpdb->waa_orders       = $wpdb->prefix . 'waa_orders';
        $wpdb->waa_announcement = $wpdb->prefix . 'waa_announcement';

        require_once __DIR__ . '/includes/functions.php';

        $installer = new waa_Installer();
        $installer->do_install();
    }

    /**
     * Placeholder for deactivation function
     *
     * Nothing being called here yet.
     */
    public static function deactivate() {

    }

    /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup() {
        load_plugin_textdomain( 'waa', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    function init_actions() {

        // Localize our plugin
        add_action( 'admin_init', array( $this, 'load_table_prifix' ) );

        add_action( 'init', array( $this, 'localization_setup' ) );
        add_action( 'init', array( $this, 'register_scripts' ) );

        add_action( 'template_redirect', array( $this, 'redirect_if_not_logged_seller' ), 11 );

        add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        add_action( 'login_enqueue_scripts', array( $this, 'login_scripts' ) );

        // add_action( 'admin_init', array( $this, 'install_theme' ) );
        add_action( 'admin_init', array( $this, 'block_admin_access' ) );
    }

    public function register_scripts() {
        $suffix   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

        // register styles
        wp_register_style( 'jquery-ui', plugins_url( 'assets/css/jquery-ui-1.10.0.custom.css', __FILE__ ), false, null );
        wp_register_style( 'fontawesome', plugins_url( 'assets/css/font-awesome.min.css', __FILE__ ), false, null );
        wp_register_style( 'waa-extra', plugins_url( 'assets/css/waa-extra.css', __FILE__ ), false, null );
        wp_register_style( 'waa-style', plugins_url( 'assets/css/style.css', __FILE__ ), false, null );
        wp_register_style( 'waa-chosen-style', plugins_url( 'assets/css/chosen.min.css', __FILE__ ), false, null );
        wp_register_style( 'waa-magnific-popup', plugins_url( 'assets/css/magnific-popup.css', __FILE__ ), false, null );

        // register scripts
        wp_register_script( 'jquery-flot', plugins_url( 'assets/js/flot-all.min.js', __FILE__ ), false, null, true );
        wp_register_script( 'jquery-chart', plugins_url( 'assets/js/Chart.min.js', __FILE__ ), false, null, true );
        wp_register_script( 'waa-tabs-scripts', plugins_url( 'assets/js/jquery.easytabs.min.js', __FILE__ ), false, null, true );
        wp_register_script( 'waa-hashchange-scripts', plugins_url( 'assets/js/jquery.hashchange.min.js', __FILE__ ), false, null, true );
        wp_register_script( 'waa-tag-it', plugins_url( 'assets/js/tag-it.min.js', __FILE__ ), array( 'jquery' ), null, true );
        wp_register_script( 'chosen', plugins_url( 'assets/js/chosen.jquery.min.js', __FILE__ ), array( 'jquery' ), null, true );
        wp_register_script( 'waa-popup', plugins_url( 'assets/js/jquery.magnific-popup.min.js', __FILE__ ), array( 'jquery' ), null, true );
        wp_register_script( 'bootstrap-tooltip', plugins_url( 'assets/js/bootstrap-tooltips.js', __FILE__ ), false, null, true );
        wp_register_script( 'form-validate', plugins_url( 'assets/js/form-validate.js', __FILE__ ), array( 'jquery' ), null, true  );

        wp_register_script( 'waa-script', plugins_url( 'assets/js/all.js', __FILE__ ), false, null, true );
        wp_register_script( 'waa-product-shipping', plugins_url( 'assets/js/single-product-shipping.js', __FILE__ ), false, null, true );
    }

    /**
     * Enqueue admin scripts
     *
     * Allows plugin assets to be loaded.
     *
     * @uses wp_enqueue_script()
     * @uses wp_localize_script()
     * @uses wp_enqueue_style
     */
    public function scripts() {

        if ( is_singular( 'product' ) && !get_query_var( 'edit' ) ) {
            wp_enqueue_script( 'waa-product-shipping' );
            $localize_script = array(
                'ajaxurl'     => admin_url( 'admin-ajax.php' ),
                'nonce'       => wp_create_nonce( 'waa_reviews' ),
                'ajax_loader' => plugins_url( 'assets/images/ajax-loader.gif', __FILE__ ),
                'seller'      => array(
                    'available'    => __( 'Available', 'waa' ),
                    'notAvailable' => __( 'Not Available', 'waa' )
                ),
                'delete_confirm' => __('Are you want to sure ?', 'waa' ),
                'wrong_message' => __('Something wrong, Please try again', 'waa' ),
            );
            wp_localize_script( 'jquery', 'waa', $localize_script );
        }

        $page_id = waa_get_option( 'dashboard', 'waa_pages' );

        // bailout if not dashboard
        if ( ! $page_id ) {
            return;
        }

        if ( ! function_exists( 'WC' ) ) {
            return;
        }

        $localize_script = array(
            'ajaxurl'     => admin_url( 'admin-ajax.php' ),
            'nonce'       => wp_create_nonce( 'waa_reviews' ),
            'ajax_loader' => plugins_url( 'assets/images/ajax-loader.gif', __FILE__ ),
            'seller'      => array(
                'available'    => __( 'Available', 'waa' ),
                'notAvailable' => __( 'Not Available', 'waa' )
            ),
            'delete_confirm' => __('Are you want to sure ?', 'waa' ),
            'wrong_message' => __('Something wrong, Please try again', 'waa' ),
            'duplicates_attribute_messg' => __( 'Sorry this attribute option already exist, Try another one', 'waa' ),
            'variation_unset_warning' => __( 'Warning! This product will not have any variation by unchecked this option', 'waa' ),
        );

        $form_validate_messages = array(
            'required'        => __( "This field is required from localization.", 'waa' ),
            'remote'          => __( "Please fix this field.", 'waa' ),
            'email'           => __( "Please enter a valid email address." , 'waa' ),
            'url'             => __( "Please enter a valid URL." , 'waa' ),
            'date'            => __( "Please enter a valid date." , 'waa' ),
            'dateISO'         => __( "Please enter a valid date (ISO)." , 'waa' ),
            'number'          => __( "Please enter a valid number." , 'waa' ),
            'digits'          => __( "Please enter only digits." , 'waa' ),
            'creditcard'      => __( "Please enter a valid credit card number." , 'waa' ),
            'equalTo'         => __( "Please enter the same value again." , 'waa' ),
            'maxlength_msg'   => __( "Please enter no more than {0} characters." , 'waa' ),
            'minlength_msg'   => __( "Please enter at least {0} characters." , 'waa' ),
            'rangelength_msg' => __( "Please enter a value between {0} and {1} characters long." , 'waa' ),
            'range_msg'       => __( "Please enter a value between {0} and {1}." , 'waa' ),
            'max_msg'         => __( "Please enter a value less than or equal to {0}." , 'waa' ),
            'min_msg'         => __( "Please enter a value greater than or equal to {0}." , 'waa' ),
        );

        wp_localize_script( 'form-validate', 'waaValidateMsg', $form_validate_messages );

        // var_dump('lol');

        // load only in waa dashboard and edit page
        if ( is_page( $page_id ) || ( get_query_var( 'edit' ) && is_singular( 'product' ) ) ) {


            if ( waa_LOAD_STYLE ) {
                wp_enqueue_style( 'jquery-ui' );
                wp_enqueue_style( 'fontawesome' );
                wp_enqueue_style( 'waa-extra' );
                wp_enqueue_style( 'waa-style' );
                wp_enqueue_style( 'waa-magnific-popup' );
            }

            if ( waa_LOAD_SCRIPTS ) {

                wp_enqueue_script( 'jquery' );
                wp_enqueue_script( 'jquery-ui' );
                wp_enqueue_script( 'jquery-ui-autocomplete' );
                wp_enqueue_script( 'jquery-ui-datepicker' );
                wp_enqueue_script( 'underscore' );
                wp_enqueue_script( 'post' );
                wp_enqueue_script( 'waa-tag-it' );
                wp_enqueue_script( 'bootstrap-tooltip' );
                wp_enqueue_script( 'form-validate' );
                wp_enqueue_script( 'waa-tabs-scripts' );
                wp_enqueue_script( 'jquery-chart' );
                wp_enqueue_script( 'jquery-flot' );
                wp_enqueue_script( 'chosen' );
                wp_enqueue_media();
                wp_enqueue_script( 'waa-popup' );

                wp_enqueue_script( 'waa-script' );
                wp_localize_script( 'jquery', 'waa', $localize_script );
            }
        }

        // store and my account page
        $custom_store_url = waa_get_option( 'custom_store_url', 'waa_selling', 'store' );
        if ( get_query_var( $custom_store_url ) || get_query_var( 'store_review' ) || is_account_page() ) {

            if ( waa_LOAD_STYLE ) {
                wp_enqueue_style( 'fontawesome' );
                wp_enqueue_style( 'waa-style' );
            }


            if ( waa_LOAD_SCRIPTS ) {
                wp_enqueue_script( 'jquery-ui-sortable' );
                wp_enqueue_script( 'jquery-ui-datepicker' );
                wp_enqueue_script( 'bootstrap-tooltip' );
                wp_enqueue_script( 'chosen' );
                wp_enqueue_script( 'form-validate' );
                wp_enqueue_script( 'waa-script' );
                wp_localize_script( 'jquery', 'waa', $localize_script );
            }
        }

        // load waa style on every pages. requires for shortcodes in other pages
        if ( waa_LOAD_STYLE ) {
            wp_enqueue_style( 'waa-style' );
            wp_enqueue_style( 'fontawesome' );
        }

        //load country select js in seller settings store template
        global $wp;
        if ( isset( $wp->query_vars['settings'] ) == 'store' ) {
            wp_enqueue_script( 'wc-country-select' );
        }

        do_action( 'waa_after_load_script' );
    }


    /**
     * Include all the required files
     *
     * @return void
     */
    function includes() {
        $lib_dir     = __DIR__ . '/lib/';
        $inc_dir     = __DIR__ . '/includes/';
        $classes_dir = __DIR__ . '/classes/';

        require_once $inc_dir . 'functions.php';
        require_once $inc_dir . 'widgets/menu-category.php';
        require_once $inc_dir . 'widgets/store-menu-category.php';
        require_once $inc_dir . 'widgets/best-seller.php';
        require_once $inc_dir . 'widgets/feature-seller.php';
        require_once $inc_dir . 'widgets/bestselling-product.php';
        require_once $inc_dir . 'widgets/top-rated-product.php';
        require_once $inc_dir . 'widgets/store-location.php';
        require_once $inc_dir . 'widgets/store-contact.php';
        require_once $inc_dir . 'widgets/store-menu.php';

        require_once $inc_dir . 'wc-functions.php';

        if ( is_admin() ) {
            require_once $inc_dir . 'admin/admin.php';
            require_once $inc_dir . 'admin/announcement.php';
            require_once $inc_dir . 'admin/ajax.php';
            require_once $inc_dir . 'admin-functions.php';
        } else {
            require_once $inc_dir . 'wc-template.php';
            require_once $inc_dir . 'template-tags.php';
        }

        require_once $classes_dir. 'store-seo.php';

    }

    /**
     * Initialize filters
     *
     * @return void
     */
    function init_filters() {
        add_filter( 'posts_where', array( $this, 'hide_others_uploads' ) );
        add_filter( 'body_class', array( $this, 'add_dashboard_template_class' ), 99 );
        add_filter( 'wp_title', array( $this, 'wp_title' ), 20, 2 );
    }

    /**
     * Hide other users uploads for `seller` users
     *
     * Hide media uploads in page "upload.php" and "media-upload.php" for
     * sellers. They can see only thier uploads.
     *
     * FIXME: fix the upload counts
     *
     * @global string $pagenow
     * @global object $wpdb
     * @param string  $where
     * @return string
     */
    function hide_others_uploads( $where ) {
        global $pagenow, $wpdb;

        if ( ( $pagenow == 'upload.php' || $pagenow == 'media-upload.php' ) && current_user_can( 'dokandar' ) ) {
            $user_id = get_current_user_id();

            $where .= " AND $wpdb->posts.post_author = $user_id";
        }

        return $where;
    }

    /**
     * Init ajax classes
     *
     * @return void
     */
    function init_ajax() {
        $doing_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

        if ( $doing_ajax ) {
            waa_Ajax::init()->init_ajax();
            new waa_Pageviews();
        }
    }

    /**
     * Init all the classes
     *
     * @return void
     */
    function init_classes() {
        if ( is_admin() ) {
            new waa_Admin_User_Profile();
            waa_Admin_Ajax::init();
            new waa_Announcement();
        } else {
            new waa_Pageviews();
        }

        new waa_Rewrites();
        waa_Email::init();
        waa_Template_Shortcodes::init();
        waa_Template_Shipping::init();
    }

    function redirect_if_not_logged_seller() {
        global $post;

        $page_id = waa_get_option( 'dashboard', 'waa_pages' );

        if ( ! $page_id ) {
            return;
        }

        if ( is_page( $page_id ) ) {
            waa_redirect_login();
            waa_redirect_if_not_seller();
        }
    }

    /**
     * Block user access to admin panel for specific roles
     *
     * @global string $pagenow
     */
    function block_admin_access() {
        global $pagenow, $current_user;

        // bail out if we are from WP Cli
        if ( defined( 'WP_CLI' ) ) {
            return;
        }

        $no_access   = waa_get_option( 'admin_access', 'waa_general', 'on' );
        $valid_pages = array( 'admin-ajax.php', 'admin-post.php', 'async-upload.php', 'media-upload.php' );
        $user_role   = reset( $current_user->roles );

        if ( ( $no_access == 'on' ) && ( !in_array( $pagenow, $valid_pages ) ) && in_array( $user_role, array( 'seller', 'customer' ) ) ) {
            wp_redirect( home_url() );
            exit;
        }
    }

    function login_scripts() {
        wp_enqueue_script( 'jquery' );
    }

    /**
     * Scripts and styles for admin panel
     */
    function admin_enqueue_scripts() {
        wp_enqueue_script( 'waa_slider_admin', waa_PLUGIN_ASSEST.'/js/admin.js', array( 'jquery' ) );
    }

    function load_table_prifix() {
        global $wpdb;

        $wpdb->waa_withdraw = $wpdb->prefix . 'waa_withdraw';
        $wpdb->waa_orders   = $wpdb->prefix . 'waa_orders';
    }

    /**
     * Add body class for waa-dashboard
     *
     * @param array $classes
     */
    function add_dashboard_template_class( $classes ) {
        $page_id = waa_get_option( 'dashboard', 'waa_pages' );

        if ( ! $page_id ) {
            return $classes;
        }

        if ( is_page( $page_id ) || ( get_query_var( 'edit' ) && is_singular( 'product' ) ) ) {
            $classes[] = 'waa-dashboard';
        }

        if ( waa_is_store_page () ) {
            $classes[] = 'waa-store';
        }

        return $classes;
    }


    /**
     * Create a nicely formatted and more specific title element text for output
     * in head of document, based on current view.
     *
     * @since waa 1.0.4
     *
     * @param string  $title Default title text for current view.
     * @param string  $sep   Optional separator.
     * @return string The filtered title.
     */
    function wp_title( $title, $sep ) {
        global $paged, $page;

        if ( is_feed() ) {
            return $title;
        }

        if ( waa_is_store_page() ) {
            $site_title = get_bloginfo( 'name' );
            $store_user = get_userdata( get_query_var( 'author' ) );
            $store_info = waa_get_store_info( $store_user->ID );
            $store_name = esc_html( $store_info['store_name'] );
            $title      = "$store_name $sep $site_title";

            // Add a page number if necessary.
            if ( $paged >= 2 || $page >= 2 ) {
                $title = "$title $sep " . sprintf( __( 'Page %s', 'waa' ), max( $paged, $page ) );
            }

            return $title;
        }

        return $title;
    }

} // WAA_waa

function waa_load_plugin() {
    $waa = WAA_waa::init();

}

add_action( 'plugins_loaded', 'waa_load_plugin', 5 );

register_activation_hook( __FILE__, array( 'WAA_waa', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WAA_waa', 'deactivate' ) );
