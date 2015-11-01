<?php
/* includes */
include('functions/functions-woocommerce.php');
include('functions/functions-artists.php');
include('functions/functions-shortcodes.php');
include('functions/functions-metabox.php');

/* waa general setup */
add_action( 'after_setup_theme', 'waa_setup' );
function waa_setup() {
	load_theme_textdomain( 'waa', get_template_directory() . '/languages' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	register_nav_menus(
	array( 'main-menu' => __( 'Main Menu', 'waa' ) )
	);
}

/* waa widget setup */
add_action( 'widgets_init', 'waa_widgets_init' );
function waa_widgets_init() {
	register_sidebar( array (
		'name' => __( 'Sidebar', 'waa' ),
		'id' => 'primary-widget-area',
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => "</li>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	
	add_action('after_setup_theme', 'remove_admin_bar');

	function remove_admin_bar() {
	if (!current_user_can('administrator') && !is_admin()) {
		show_admin_bar(false);
	}
	}
		
	
	/* Footer Sidebars */
	register_sidebar( array (
		'name' => __( 'Footer Col 1', 'waa' ),
		'id' => 'footer-col-1',
		'before_widget' => '<div class="col-md-3" id="%1$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
	) );
	register_sidebar( array (
		'name' => __( 'Footer Col 2', 'waa' ),
		'id' => 'footer-col-2',
		'before_widget' => '<div class="col-md-3" id="%1$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
	) );
	register_sidebar( array (
		'name' => __( 'Footer Col 3', 'waa' ),
		'id' => 'footer-col-3',
		'before_widget' => '<div class="col-md-3" id="%1$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
	) );
	register_sidebar( array (
		'name' => __( 'Footer Col 4', 'waa' ),
		'id' => 'footer-col-4',
		'before_widget' => '<div class="col-md-3" id="%1$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
	) );

}

/* add scripts */
add_action( 'wp_enqueue_scripts', 'waa_load_scripts' );
function waa_load_scripts() {
	wp_enqueue_script( 'jquery' );
	wp_register_script( 'bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js' );
	wp_enqueue_script( 'bootstrap' );

	wp_register_script( 'custom', get_template_directory_uri() . '/js/custom.js' );
	wp_enqueue_script( 'custom' );	
	
	wp_register_script( 'masonry', get_template_directory_uri() . '/js/masonry.min.js' );
	wp_enqueue_script( 'masonry' );
}

/* add stylesheets */
add_action( 'wp_enqueue_scripts', 'waa_load_styles' );
add_editor_style();
function waa_load_styles() {
	wp_register_style( 'bootstrap.min', get_template_directory_uri() . '/css/bootstrap.min.css' );
	wp_enqueue_style( 'bootstrap.min' );
	
	wp_register_style( 'bootstrap-theme', get_template_directory_uri() . '/css/bootstrap-theme.min.css' );
	wp_enqueue_style( 'bootstrap-theme' );

	wp_register_style( 'global-style', get_template_directory_uri() . '/css/global.css' );
	wp_enqueue_style( 'global-style' );

	wp_register_style( 'Crimson', 'https://fonts.googleapis.com/css?family=Crimson+Text:400,400italic,600,700' );
	wp_enqueue_style( 'Crimson' );
	
	wp_register_style( 'Font-Awesome', get_template_directory_uri() . '/css/font-awesome.min.css' );
	wp_enqueue_style( 'Font-Awesome' );
}

// Hide Wordpress update notifications
function hide_update_notice_to_all_but_admin_users()
{
    if (!current_user_can('update_core')) {
        remove_action( 'admin_notices', 'update_nag', 3 );
    }
}
add_action( 'admin_head', 'hide_update_notice_to_all_but_admin_users', 1 );

// check if category has children
function category_has_children( $term_id = 0, $taxonomy = 'category' ) {
    $children = get_categories( array( 'child_of' => $term_id, 'taxonomy' => $taxonomy ) );
    return ( $children );
}


// custom waa wordpress logo
add_action('admin_head', 'waa_custom_logo');

function waa_custom_logo() {
echo '
<style type="text/css">
#header-logo { background-image: url('.get_bloginfo('template_directory').'/images/custom-logo.gif) !important; }
</style>
';
}


function remove_footer_admin () {
echo 'Fueled by <a href="http://www.wordpress.org" target="_blank">WordPress</a> | &copy; WeArt</p>';
}
add_filter('admin_footer_text', 'remove_footer_admin');


function truncate_string($string,$length=250,$appendStr="..."){
	$truncated_str = "";
	$useAppendStr = (strlen($string) > intval($length))? true:false;
	mb_internal_encoding("UTF-8");
	$truncated_str = mb_substr($string,0,$length);
	$truncated_str .= ($useAppendStr)? $appendStr:"";
	return $truncated_str;
}

/**
 * Redirect users to custom URL based on their role after login
 *
 * @param string $redirect
 * @param object $user
 * @return string
 */
function wc_custom_user_redirect( $redirect, $user ) {
	// Get the first of all the roles assigned to the user
	$role = $user->roles[0];
	$dashboard = admin_url();
	$seller = waa_get_navigation_url();
	$myaccount = get_permalink( wc_get_page_id( 'myaccount' ) );
	if( $role == 'administrator' ) {
		//Redirect administrators to the dashboard
		$redirect = $dashboard;
	} elseif ( $role == 'shop-manager' ) {
		//Redirect shop managers to the dashboard
		$redirect = $dashboard;
	} elseif ( $role == 'editor' ) {
		//Redirect editors to the dashboard
		$redirect = $dashboard;
	} elseif ( $role == 'author' ) {
		//Redirect authors to the dashboard
		$redirect = $dashboard;
	} elseif ( $role == 'customer' ) {
		//Redirect customers and subscribers to the "My Account" page
		$redirect = $myaccount;
	} elseif ( $role == 'seller' ) {
		//Redirect customers and subscribers to the "My Account" page
		$redirect = $seller;
	} else {
		//Redirect any other role to the previous visited page or, if not available, to the home
		$redirect = wp_get_referer() ? wp_get_referer() : home_url();
	}
	return $redirect;
}
add_filter( 'woocommerce_login_redirect', 'wc_custom_user_redirect', 10, 2 );




/**
 * Add customer/artist menu items
 *
 * @since 	1.0
 */
add_filter( 'wp_nav_menu_items', 'add_customer_menu', 10, 2);
function add_customer_menu($items, $args)
{
		if(is_user_logged_in() && $args->theme_location == 'main-menu')
		{
				$user=wp_get_current_user();
				if ( waa_is_user_customer( $user->ID ) && !current_user_can( 'manage_options' ) ) {
					$items .= "<li class='customer-menu'><a href='".waa_get_page_url( 'myaccount', 'woocommerce' )."'>".__( 'My Account', 'waa' )."</a></li>";
				} elseif ( waa_is_user_seller ( $user->ID ) && !current_user_can( 'manage_options' ) ) {
					$items .= '<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">'.__( 'My Account', 'waa' ).'  <b class="caret"></b></a>
						<ul class="dropdown-menu">
								<li><a href="'.waa_get_store_url( $user_id ).'" target="_blank">'.__( 'Visit your store', 'waa' ).' <i class="fa fa-external-link"></i></a></li>
								<li class="divider"></li>';
								 $nav_urls = waa_get_dashboard_nav();
								foreach ($nav_urls as $key => $item) {
										$items .= '<li><a href="'.$item['url'].'">'.$item['icon'].' &nbsp;'. $item['title'].'</a></li>';
								}
							$items .= '<li><a href="'.get_permalink( get_option("woocommerce_myaccount_page_id") ).'" title="'.__("My Account","waa").'"><i class="fa fa-cog"></i> '.__('My Account', 'waa').'</a></li>';
								$items .= '<li><a href="'.wp_logout_url( home_url() ).'"><i class="fa fa-power-off"></i> '.__('Logout', 'waa').'</a></li>';
							$items .= '
                    </ul>
                </li>';
				} elseif ( current_user_can( 'manage_options' ) ) {
					$items .= "<li class='admin-menu'><a href='".get_admin_url()."'>".__( 'Admin', 'waa' )."</a></li>";
				}
		} elseif ($args->theme_location == 'main-menu') {
			$items .= '<li class="shopping-cart"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-shopping-cart"></i></a>'; 
			$items .= '<ul class="dropdown-menu mini-shopping-cart">
                <li>
                    <div class="widget_shopping_cart_content"></div>
                </li>
            </ul>
        </li>';
			
		}
		return $items;
}

add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

// @param int $post_id - The id of the post that you are setting the attributes for
// @param array[] $attributes - This needs to be an array containing ALL your attributes so it can insert them in one go
function wcproduct_set_attributes($post_id, $attributes) {
		$i = 0;
		// Loop through the attributes array
		foreach ($attributes as $name => $value) {
				$product_attributes[$i] = array (
						'name' => sanitize_title( $name  ), // set attribute name
						'value' => $value, // set attribute value
						'position' => 1,
						'is_visible' => 1,
						'is_variation' => 0,
						'is_taxonomy' => 1
				);

				$i++;
		}

		// Now update the post with its new attributes
		update_post_meta($post_id, '_product_attributes', $product_attributes);
}