<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce_loop;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) ) {
	$woocommerce_loop['loop'] = 0;
}

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) ) {
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
}

// Ensure visibility
if ( ! $product || ! $product->is_visible() ) {
	return;
}

// Increase loop count
$woocommerce_loop['loop']++;

// Extra post classes
$classes = array();
if ( 0 == ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 == $woocommerce_loop['columns'] ) {
	$classes[] = 'first';
}
if ( 0 == $woocommerce_loop['loop'] % $woocommerce_loop['columns'] ) {
	$classes[] = 'last';
}
$author     = get_user_by( 'id', $product->post->post_author );
$store_info = waa_get_store_info( $author->ID );
$city_term = get_term_by('id', $store_info['region'], 'pa_stadt');
$city_id = $city_term->term_id;
$city_link = get_term_link( $city_id, 'pa_stadt' );
$city_name = $city_term->name;
?>

<li <?php post_class( $classes ); ?>>

	<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
	<a href="<?php the_permalink(); ?>">
		<?php the_post_thumbnail( array(600,600) ); ?>
	</a>
	<div class="art-info">
		<header>
			<span class="tagged_as">
				<?php ($city_name ? printf( __('<span class="city"><a href="%1$s" title="Künstler aus %2$s anzeigen">%2$s</a> / </span>', 'waa'), $city_link, $city_name ) : ''); ?>
				<?= $product->get_categories(); ?>
			</span>
			<?php	#woocommerce_product_loop_tags();	?>
			<div class="title"><h3><a href="<?php the_permalink(); ?>">
		<?php
			/**
			 * woocommerce_before_shop_loop_item_title hook
			 *
			 * @hooked woocommerce_show_product_loop_sale_flash - 10
			 * @hooked woocommerce_template_loop_product_thumbnail - 10
			 */
			remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
			#do_action( 'woocommerce_before_shop_loop_item_title' );

			/**
			 * woocommerce_shop_loop_item_title hook
			 *
			 * @hooked woocommerce_template_loop_product_title - 10
			 */
			do_action( 'woocommerce_shop_loop_item_title' );

			?>
				</a></h3></div>
			</header>
			<footer>
			<?php

			/**
			 * woocommerce_after_shop_loop_item_title hook
			 *
			 * @hooked woocommerce_template_loop_rating - 5
			 * @hooked woocommerce_template_loop_price - 10
			 */
			remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
			#do_action( 'woocommerce_after_shop_loop_item_title' );

			echo '<span class="artist">';
			printf( 'von <a href="%s">%s</a>', waa_get_store_url( $author->ID ), $store_info['store_name'] ) .'</span>';

			add_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
			remove_action('woocommerce_after_shop_loop_item_title', 'cj_show_dimensions', 9);
			do_action( 'woocommerce_after_shop_loop_item_title' );


			?>
		</footer>
	</div>

	<?php

		/**
		 * woocommerce_after_shop_loop_item hook
		 *
		 * @hooked woocommerce_template_loop_add_to_cart - 10
		 */
		#do_action( 'woocommerce_after_shop_loop_item' );

	?>

</li>
