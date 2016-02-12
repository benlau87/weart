<?php
/**
 * The Template for displaying all reviews.
 *
 * @package waa
 * @package waa - 2014 1.0
 */

$store_user = get_userdata( get_query_var( 'author' ) );
$store_info = waa_get_store_info( $store_user->ID );
$map_location = isset( $store_info['location'] ) ? esc_attr( $store_info['location'] ) : '';
$scheme       = is_ssl() ? 'https' : 'http';

wp_enqueue_script( 'google-maps', $scheme . '://maps.google.com/maps/api/js?sensor=true' );

get_header( 'shop' );
?>

<?php do_action( 'woocommerce_before_main_content' ); ?>

<?php if ( waa_get_option( 'enable_theme_store_sidebar', 'waa_general', 'off' ) == 'off' ) { ?>
    <div id="waa-secondary" class="waa-clearfix waa-w3 waa-store-sidebar" role="complementary" style="margin-right:3%;">
        <div class="waa-widget-area widget-collapse">
            <?php
            if ( ! dynamic_sidebar( 'sidebar-store' ) ) {

                $args = array(
                    'before_widget' => '<aside class="widget">',
                    'after_widget'  => '</aside>',
                    'before_title'  => '<h3 class="widget-title">',
                    'after_title'   => '</h3>',
                );

                if ( class_exists( 'waa_Store_Location' ) ) {
                    the_widget( 'waa_Store_Category_Menu', array( 'title' => __( 'Store Category', 'waa' ) ), $args );
                    if( waa_get_option( 'store_map', 'waa_general', 'on' ) == 'on' ) {
                        the_widget( 'waa_Store_Location', array( 'title' => __( 'Store Location', 'waa' ) ), $args );
                    }
                    if( waa_get_option( 'contact_seller', 'waa_general', 'on' ) == 'on' ) {
                        the_widget( 'waa_Store_Contact_Form', array( 'title' => __( 'Contact Seller', 'waa' ) ), $args );
                    }
                }

            }
            ?>

            <?php do_action( 'waa_sidebar_store_after', $store_user, $store_info ); ?>
        </div>
    </div><!-- #secondary .widget-area -->
<?php
} else {
    get_sidebar( 'store' );
}
?>

<div id="waa-primary" class="waa-single-store waa-w8">
    <div id="waa-content" class="store-review-wrap woocommerce" role="main">

        <?php waa_get_template_part( 'store-header' ); ?>

        <?php
        $waa_template_reviews = waa_Template_reviews::init();
        $id                     = $store_user->ID;
        $post_type              = 'product';
        $limit                  = 20;
        $status                 = '1';
        $comments               = $waa_template_reviews->comment_query( $id, $post_type, $limit, $status );
        ?>

        <div id="reviews">
            <div id="comments">

                <h2 class="headline"><?php _e( 'Seller Review', 'waa' ); ?></h2>

                <ol class="commentlist">
                    <?php
                    if ( count( $comments ) == 0 ) {
                        echo '<span colspan="5">' . __( 'No Result Found', 'waa' ) . '</span>';
                    } else {

                        foreach ($comments as $single_comment) {
                            $GLOBALS['comment'] = $single_comment;
                            $comment_date       = get_comment_date( 'l, F jS, Y \a\t g:i a', $single_comment->comment_ID );
                            $comment_author_img = get_avatar( $single_comment->comment_author_email, 180 );
                            $permalink          = get_comment_link( $single_comment );
                            ?>

                            <li <?php comment_class(); ?> itemtype="http://schema.org/Review" itemscope="" itemprop="reviews">
                                <div class="review_comment_container">
                                    <div class="waa-review-author-img"><?php echo $comment_author_img; ?></div>
                                    <div class="comment-text">
                                        <a href="<?php echo $permalink; ?>">
                                            <?php
                                            if ( get_option('woocommerce_enable_review_rating') == 'yes' ) :
                                                $rating =  intval( get_comment_meta( $single_comment->comment_ID, 'rating', true ) ); ?>
                                                <div class="waa-rating">
                                                    <div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title="<?php echo sprintf(__( 'Rated %d out of 5', 'waa' ), $rating) ?>">
                                                        <span style="width:<?php echo ( intval( get_comment_meta( $single_comment->comment_ID, 'rating', true ) ) / 5 ) * 100; ?>%"><strong itemprop="ratingValue"><?php echo $rating; ?></strong> <?php _e( 'out of 5', 'waa' ); ?></span>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </a>
                                        <p>
                                            <strong itemprop="author"><?php echo $single_comment->comment_author; ?></strong>
                                            <em class="verified"><?php echo $single_comment->user_id == 0 ? '(Guest)' : ''; ?></em>
                                            –
                                            <a href="<?php echo $permalink; ?>">
                                                <time datetime="<?php echo date( 'c', strtotime( $comment_date ) ); ?>" itemprop="datePublished"><?php echo $comment_date; ?></time>
                                            </a>
                                        </p>
                                        <div class="description" itemprop="description">
                                            <p><?php echo $single_comment->comment_content; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </li>

                           <?php
                        }
                    }
                    ?>
                </ol>
            </div>
        </div>

        <?php
        echo $waa_template_reviews->review_pagination( $id, $post_type, $limit, $status );
        ?>

    </div><!-- #content .site-content -->
</div><!-- #primary .content-area -->

<?php do_action( 'woocommerce_after_main_content' ); ?>

<?php get_footer(); ?>