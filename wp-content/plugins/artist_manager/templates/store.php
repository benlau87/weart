<?php
/**
 *
 * @package waa
 * @package waa - 2014 1.0
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

$store_user = get_userdata(get_query_var('author'));
$store_info = waa_get_store_info($store_user->ID);
$scheme = is_ssl() ? 'https' : 'http';

$store_desc = strlen($store_info['description']) > 155 ? substr($store_info['description'],0,155)."..." : $store_info['description'];
global $wpseo_front;
if(defined($wpseo_front)){
    remove_action('wp_head',array($wpseo_front,'head'),1);
}
else {
    $wp_thing = WPSEO_Frontend::get_instance();
    remove_action('wp_head',array($wp_thing,'head'),1);
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <title>Shop von <?= $store_info['store_name'] ?> auf WeAre-Art.com</title>
        <meta charset="<?php bloginfo( 'charset' ); ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
        <link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_uri(); ?>" />

        <meta name="description" content="<?= $store_desc; ?>" />
        <meta name="twitter:card" value="<?= $store_desc; ?>">
        <meta property="og:title" content="Shop von <?= $store_info['store_name'] ?> auf WeAre-Art.com" />
        <meta property="og:type" content="profile" />
        <meta property="profile:first_name" content="<?= $store_user->user_firstname; ?>">
        <meta property="profile:last_name" content="<?= $store_user->user_lastname; ?>">
        <meta property="profile:username" content="<?= $store_info['store_name']; ?>">
        <meta property="og:url" content="http://<?= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" />
        <meta property="og:image" content="<?= the_post_thumbnail_url(); ?>" />
        <meta property="og:description" content="<?= $store_desc; ?>" />

        <?php wp_head(); ?>
    </head>
<body <?php body_class(); ?>>
<a id="skippy" class="sr-only sr-only-focusable" href="#content"><div class="container"><span class="skiplink-text">Skip to main content</span></div></a>

<header class="navbar navbar-static-top" id="top" role="banner">
    <div class="container">
        <div class="navbar-header">
            <button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target="#bs-navbar" aria-controls="bs-navbar" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar top-bar"></span>
                <span class="icon-bar middle-bar"></span>
                <span class="icon-bar bottom-bar"></span>
            </button>
            <a href="<?= home_url(); ?>" class="navbar-brand"><img src="<?= get_template_directory_uri(); ?>/img/logo.png" alt="We are Art" /></a>
        </div>
        <nav id="bs-navbar" class="collapse navbar-collapse">
            <?php wp_nav_menu( array( 'theme_location' => 'main-menu', 'menu_class' => 'nav navbar-nav' ) );			?>
        </nav>
    </div>
</header>


    <div id="content">
        <div class="showcase woocommerce">
            <div class="container">
                <div class="row">
                    <div id="waa-primary" class="waa-single-store col-md-12">
                        <div id="waa-content" class="store-page-wrap woocommerce" role="main">

                            <?php waa_get_template_part('store-header'); ?>

                            <?php do_action('waa_store_profile_frame_after', $store_user, $store_info); ?>

                            <div class="row">
                                <div class="sidebar-right col-md-4 col-sm-6 col-xs-12">
                                    <div class="showcase-content">
                                        <?php if (isset($store_info['store_name'])) { ?>
                                            <h1 class="page-title"><?php echo esc_html($store_info['store_name']); ?></h1>
                                        <?php } ?>

                                        <div
                                            class="entry_author_image"><?php echo get_avatar($store_user->ID, 150); ?></div>
                                        <div class="showcase2-content">
                                            <?php if (isset($store_info['address']['city']) && !empty($store_info['address']['city'])) { ?>
                                                <span class="artist-location"><?= $store_info['address']['city'];
                                                    if (isset($store_info['address']['country']) && !empty($store_info['address']['country'])) {
                                                        echo ', ' . $store_info['address']['country'];
                                                    } ?></span><br>
                                            <?php }
                                            $store_categories = waa_store_categories($store_user->ID);
                                            ?>
                                            <span class="artist-art-count"><?= __('Kunstwerke', 'waa'); ?>
                                                : <strong><?= waa_count_published_posts('product', $store_user->ID)->total ?></strong></span>
                                <span class="artist-art-count"><?= __('Kategorien', 'waa'); ?>
                                    : <strong><?= $store_categories ? $store_categories : 'noch keine'; ?></strong></span>
                                            <?php if (isset($store_info['description']) && !empty($store_info['description'])) { ?>
                                                <p class="artist-description"><?= nl2br($store_info['description']); ?></p>
                                            <?php } ?>


                                            <?php
                                            if ($store_info['enable_services'] == "yes") {
                                                ?>
                                                <a href="#" data-toggle="modal" data-target="#request-service"
                                                   class="btn btn-inverted">
                                                    <span><?= __('Mich kann man buchen', 'waa'); ?></span></a>

                                                <div class="modal fade in" id="request-service" tabindex="-1"
                                                     role="dialog"
                                                     aria-labelledby="request-service" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                        aria-hidden="true">&times;</button>
                                                                <h4 class="modal-title"><?= __('Künstler beauftragen', 'waa') ?></h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <?php the_widget('waa_Store_Contact_Form', array('title' => __('Contact Seller', 'waa')), $args); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>

                                            <?php
                                            if (is_array($store_info['social']) && !empty($store_info['social'])) : ?>
                                                <div class="content-social-share">
                                                    <span> Share </span>
                                                    <ul>
                                                        <?php if (isset ($store_info['social']['fb']) && !empty ($store_info['social']['fb'])) { ?>
                                                            <li><a href="<?= $store_info['social']['fb'] ?>"
                                                                   target="_blank"><i
                                                                    class="ui ui-facebook"></i></a></li><?php } ?>
                                                        <?php if (isset ($store_info['social']['twitter']) && !empty ($store_info['social']['twitter'])) { ?>
                                                            <li><a href="<?= $store_info['social']['twitter'] ?>"
                                                                   target="_blank"><i
                                                                    class="ui ui-twitter"></i></a></li><?php } ?>
                                                        <?php if (isset ($store_info['social']['gplus']) && !empty ($store_info['social']['gplus'])) { ?>
                                                            <li><a href="<?= $store_info['social']['gplus'] ?>"
                                                                   target="_blank"><i
                                                                    class="ui ui-google-plus"></i></a></li><?php } ?>
                                                        <?php if (isset ($store_info['social']['instagram']) && !empty ($store_info['social']['instagram'])) { ?>
                                                            <li><a href="<?= $store_info['social']['instagram'] ?>"
                                                                   target="_blank"><i
                                                                    class="ui ui-instagram"></i></a></li><?php } ?>
                                                        <?php if (isset ($store_info['social']['youtube']) && !empty ($store_info['social']['youtube'])) { ?>
                                                            <li><a href="<?= $store_info['social']['youtube'] ?>"
                                                                   target="_blank"><i
                                                                    class="ui ui-youtube"></i></a></li><?php } ?>
                                                        <?php if (isset ($store_info['social']['flickr']) && !empty ($store_info['social']['flickr'])) { ?>
                                                            <li><a href="<?= $store_info['social']['flickr'] ?>"
                                                                   target="_blank"><i
                                                                    class="ui ui-flickr"></i></a></li><?php } ?>
                                                        <?php if (isset ($store_info['social']['linkedin']) && !empty ($store_info['social']['linkedin'])) { ?>
                                                            <li><a href="<?= $store_info['social']['linkedin'] ?>"
                                                                   target="_blank"><i class="ui ui-linkedin"></i></a>
                                                            </li><?php } ?>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>


                                <?php if (have_posts()) { ?>

                                    <div class="col-md-8 col-sm-6 col-xs-12">

                                        <?php woocommerce_product_loop_start(); ?>

                                        <?php while (have_posts()) : the_post(); ?>

                                            <?php wc_get_template_part('content', 'product_all'); ?>

                                        <?php endwhile; // end of the loop. ?>

                                        <?php woocommerce_product_loop_end(); ?>

                                    </div>

                                    <?php waa_content_nav('nav-below'); ?>

                                <?php } else { ?>

                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <p class="waa-info"><?php _e('No products were found of this seller!', 'waa'); ?></p>
                                    </div>

                                <?php } ?>


                            </div><!-- .waa-single-store -->

                            <?php do_action('woocommerce_after_main_content'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php get_footer('shop'); ?>