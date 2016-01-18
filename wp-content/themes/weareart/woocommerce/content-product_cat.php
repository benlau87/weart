<?php
/**
 * The template for displaying product category thumbnails within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product_cat.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $woocommerce_loop;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) ) {
	$woocommerce_loop['loop'] = 0;
}

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) ) {
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
}

// Increase loop count
$woocommerce_loop['loop'] ++;
?>
<li <?php wc_product_cat_class(); ?>>
	<?php do_action( 'woocommerce_before_subcategory', $category ); ?>

	<a href="<?php echo get_term_link( $category->slug, 'product_cat' ); ?>">

		<?php
			/**
			 * woocommerce_before_subcategory_title hook
			 *
			 * @hooked woocommerce_subcategory_thumbnail - 10
			 */
			#do_action( 'woocommerce_before_subcategory_title', $category );


			// category (artist) thumb
			#woocommerce_subcategory_thumbnail($category);
			#print_r($category);
			$user = get_user_by('login',$category->slug);
		  the_author_image_size(150, 150, $user->ID);
			// random product (art) thumb
			$post_array = get_posts(array('post_type' => 'product', 'term' => $category->term_id, 'numberposts' => 1, 'orderby' => rand));
			$rand_post_id = $post_array[0]->ID;
			?>

		<div class="the_post_image">
		<?php
			$image_title 	= esc_attr( get_the_title( get_post_thumbnail_id() ) );
			$image       	= get_the_post_thumbnail( $post->ID, array(600,600), array(
				'title'	=> $image_title,
				'alt'	=> $image_title
				) );
			echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="%s" />', wc_placeholder_img_src(), __( 'Placeholder', 'woocommerce' ) ), $post->ID );
		?>
		</div>
		<h3>
			<?php
				echo $category->name;

				if ( $category->count > 0 )
					echo apply_filters( 'woocommerce_subcategory_count_html', ' <mark class="count">(' . $category->count . ')</mark>', $category );
			?>
		</h3>

		<?php
			/**
			 * woocommerce_after_subcategory_title hook
			 */
			do_action( 'woocommerce_after_subcategory_title', $category );
		?>

	</a>

	<?php do_action( 'woocommerce_after_subcategory', $category ); ?>
</li>
