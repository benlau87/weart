<?php
/* includes */
include('functions/functions-woocommerce.php');
include('functions/functions-artists.php');
include('form-register.php');

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


add_action( 'comment_form_before', 'waa_enqueue_comment_reply_script' );
function waa_enqueue_comment_reply_script() {
	if ( get_option( 'thread_comments' ) ) { wp_enqueue_script( 'comment-reply' ); }
}
add_filter( 'the_title', 'waa_title' );
function waa_title( $title ) {
if ( $title == '' ) {
return '&rarr;';
} else {
return $title;
}
}
add_filter( 'wp_title', 'waa_filter_wp_title' );
function waa_filter_wp_title( $title )
{
return $title . esc_attr( get_bloginfo( 'name' ) );
}

// Hide Wordpress update notifications
function hide_update_notice_to_all_but_admin_users()
{
    if (!current_user_can('update_core')) {
        remove_action( 'admin_notices', 'update_nag', 3 );
    }
}
add_action( 'admin_head', 'hide_update_notice_to_all_but_admin_users', 1 );

function category_has_children( $term_id = 0, $taxonomy = 'category' ) {
    $children = get_categories( array( 'child_of' => $term_id, 'taxonomy' => $taxonomy ) );
    return ( $children );
}