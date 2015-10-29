<?php
/**
 * waa customizer
 *
 * @author WAA
 */
class waa_Customizer {

    function __construct() {
        add_action( 'customize_register', array($this, 'register_control') );
        add_action( 'wp_head', array($this, 'generate_styles'), 99 );
        add_action( 'customize_preview_init', array($this, 'customizer_scripts' ) );
    }

    function register_control( $wp_customize ) {

        // logo
        $wp_customize->add_section( 'waa_logo_section', array(
            'title' => __( 'Theme Logo', 'waa' ),
            'priority' => 9,
            'description' => __( 'Upload your logo to replace the default Logo (dimension : 180 X 50)', 'waa' ),
        ) );

        $wp_customize->add_setting( 'waa_logo' );

        $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'waa_logo', array(
            'label' => __( 'Upload Logo', 'waa' ),
            'section' => 'waa_logo_section',
            'settings' => 'waa_logo',
        ) ) );

        // link color
        $wp_customize->add_setting( 'waa_link_color', array(
            'default' => '#f05025',
            'transport' => 'postMessage'
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'waa_link_color', array(
            'label' => __( 'Link', 'waa' ),
            'section' => 'colors',
            'settings' => 'waa_link_color',
            'priority' => 20
        ) ) );


        // link hover color
        $wp_customize->add_setting( 'waa_link_hover_color', array(
            'default' => '#aa0000',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'waa_link_hover_color', array(
            'label' => __( 'Link hover', 'waa' ),
            'section' => 'colors',
            'settings' => 'waa_link_hover_color',
            'priority' => 25
        ) ) );

        // Header Background color
        $wp_customize->add_setting( 'waa_header_bg', array(
            'default' => '#fff',
            'transport' => 'postMessage'
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'waa_header_bg', array(
            'label' => __( 'Header Background', 'waa' ),
            'section' => 'colors',
            'settings' => 'waa_header_bg',
            'priority' => 30
        ) ) );

        // nav backgroung color
        $wp_customize->add_setting( 'waa_nav_bg', array(
            'default' => '#fff',
            'transport' => 'postMessage'
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'waa_nav_bg', array(
            'label' => __( 'Navigation Background', 'waa' ),
            'section' => 'colors',
            'settings' => 'waa_nav_bg',
            'priority' => 33
        ) ) );

        // nav color
        $wp_customize->add_setting( 'waa_nav_color', array(
            'default' => '#777777',
            'transport' => 'postMessage'
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'waa_nav_color', array(
            'label' => __( 'Navigation Link', 'waa' ),
            'section' => 'colors',
            'settings' => 'waa_nav_color',
            'priority' => 35
        ) ) );

        // nav hover color
        $wp_customize->add_setting( 'waa_nav_hover', array(
            'default' => '#333',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'waa_nav_hover', array(
            'label' => __( 'Navigation Link Hover', 'waa' ),
            'section' => 'colors',
            'settings' => 'waa_nav_hover',
            'priority' => 40
        ) ) );

        // Footer Background color
        $wp_customize->add_setting( 'waa_footer_bg', array(
            'default' => '#393939',
            'transport' => 'postMessage',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'waa_footer_bg', array(
            'label' => __( 'Footer Background', 'waa' ),
            'section' => 'colors',
            'settings' => 'waa_footer_bg',
            'priority' => 50
        ) ) );

        // Footer bottom bar Background color
        $wp_customize->add_setting( 'waa_footer_bottom_bar_bg_color', array(
            'default' => '#242424',
            'transport' => 'postMessage',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'waa_footer_bottom_bar_bg_color', array(
            'label' => __( 'Copy Container Background', 'waa' ),
            'section' => 'colors',
            'settings' => 'waa_footer_bottom_bar_bg_color',
            'priority' => 50
        ) ) );

        // footer text color
        $wp_customize->add_setting( 'waa_footer_text', array(
            'default' => '#E8E8E8',
            'transport' => 'postMessage',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'waa_footer_text', array(
            'label' => __( 'Footer text', 'waa' ),
            'section' => 'colors',
            'settings' => 'waa_footer_text',
            'priority' => 55
        ) ) );

        // Siebar widget header color
        $wp_customize->add_setting( 'sidebar_widget_header', array(
            'default' => '#222222',
            'transport' => 'postMessage',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sidebar_widget_header', array(
            'label' => __( 'Sidebar widget header', 'waa' ),
            'section' => 'colors',
            'settings' => 'sidebar_widget_header',
            'priority' => 56
        ) ) );

        // widget header color
        $wp_customize->add_setting( 'footer_widget_header', array(
            'default' => '#E8E8E8',
            'transport' => 'postMessage',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'footer_widget_header', array(
            'label' => __( 'Footer widget header', 'waa' ),
            'section' => 'colors',
            'settings' => 'footer_widget_header',
            'priority' => 60
        ) ) );


        // widget text color
        $wp_customize->add_setting( 'waa_footer_widget_link', array(
            'default' => '#AFAFAF',
            'transport' => 'postMessage',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'waa_footer_widget_link', array(
            'label' => __( 'Footer Widget link', 'waa' ),
            'section' => 'colors',
            'settings' => 'waa_footer_widget_link',
            'priority' => 65
        ) ) );

        // widget text hover color
        $wp_customize->add_setting( 'waa_footer_link_hover', array(
            'default' => '#E8E8E8',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'waa_footer_link_hover', array(
            'label' => __( 'Footer Widget link hover', 'waa' ),
            'section' => 'colors',
            'settings' => 'waa_footer_link_hover',
            'priority' => 70
        ) ) );
    }

    function generate_styles() {
        ?>
        <style type="text/css">
            <?php if ( $logo = get_theme_mod( 'waa_logo' ) ) : ?>
                .site-header h1.site-title a { background: url("<?php echo esc_url( $logo ); ?>") no-repeat scroll 0 0 rgba(0, 0, 0, 0);}
            <?php endif; ?>
            <?php if ( $waa_link_color = get_theme_mod( 'waa_link_color' ) ) { ?>
                .site-header .cart-area-top a, .site-footer .footer-copy a,
                a { color: <?php echo $waa_link_color; ?>; }
            <?php } ?>
            <?php if ( $waa_link_hover_color = get_theme_mod( 'waa_link_hover_color' ) ) { ?>
                .site-header .cart-area-top a:hover, .site-footer .footer-copy a:hover,
                a:hover { color: <?php echo $waa_link_hover_color; ?>; }
            <?php } ?>
            <?php if ( $waa_header_bg = get_theme_mod( 'waa_header_bg' ) ) { ?>
                .site-header { background-color: <?php echo $waa_header_bg; ?> ; }
            <?php } ?>
            <?php if ( $waa_nav_bg = get_theme_mod( 'waa_nav_bg' ) ) { ?>
                .navbar-default{ background-color: <?php echo $waa_nav_bg; ?>; }
            <?php } ?>
            <?php if ( $waa_nav_color = get_theme_mod( 'waa_nav_color' ) ) { ?>
            .navbar-default .navbar-nav > li > a{ color: <?php echo $waa_nav_color; ?>; }
            <?php } ?>
            <?php if ( $waa_nav_hover = get_theme_mod( 'waa_nav_hover' ) ) { ?>
                .navbar-default .navbar-nav > li > a:hover { color: <?php echo $waa_nav_hover; ?>; }
            <?php } ?>
            <?php if ( $sidebar_widget_header = get_theme_mod( 'sidebar_widget_header' ) ) { ?>
                .widget-title{ color: <?php echo $sidebar_widget_header; ?>; }
            <?php } ?>
            <?php if ( $footer_widget_header = get_theme_mod( 'footer_widget_header' ) ) { ?>
                .site-footer .widget h3{ color: <?php echo $footer_widget_header; ?>; }
            <?php } ?>
            <?php if ( $waa_footer_widget_link = get_theme_mod( 'waa_footer_widget_link' ) ) { ?>
                .site-footer .widget ul li a{ color: <?php echo $waa_footer_widget_link; ?>; }
            <?php } ?>
            <?php if ( $waa_footer_link_hover = get_theme_mod( 'waa_footer_link_hover' ) ) { ?>
                .site-footer .widget ul li a:hover{ color: <?php echo $waa_footer_link_hover; ?>; }
            <?php } ?>
            <?php if ( $waa_footer_text = get_theme_mod( 'waa_footer_text' ) ) { ?>
                .site-footer { color: <?php echo $waa_footer_text; ?>; }
            <?php } ?>
            <?php if ( $waa_footer_bg = get_theme_mod( 'waa_footer_bg' ) ) { ?>
                .site-footer .footer-widget-area { background: <?php echo $waa_footer_bg; ?> ; }
            <?php } ?>
            <?php if ( $waa_footer_bottom_bar_bg_color = get_theme_mod( 'waa_footer_bottom_bar_bg_color' ) ) { ?>
                .site-footer .copy-container { background: <?php echo $waa_footer_bottom_bar_bg_color; ?> ; }
            <?php } ?>
        </style>
        <?php
    }

    function customizer_scripts() {
        $url = plugins_url( 'assets/js/theme-customizer.js', __FILE__ );
        wp_enqueue_script( 'waa-theme-customizer', $url, array('jquery', 'customize-preview') );
    }

}

new waa_Customizer();