<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * Override this template by copying it to yourtheme/woocommerce/content-single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $product;
?>

<?php
	/**
	 * woocommerce_before_single_product hook
	 *
	 * @hooked wc_print_notices - 10
	 */
	 do_action( 'woocommerce_before_single_product' );

	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
?>

<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>
<div class="row">
	<?php
		/**
		 * woocommerce_before_single_product_summary hook
		 *
		 * @hooked woocommerce_show_product_sale_flash - 10
		 * @hooked woocommerce_show_product_images - 20
		 */
		remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
		do_action( 'woocommerce_before_single_product_summary' );
	?>

	<div class="col-md-4 product-sidebar">

		<?php
			/**
			 * woocommerce_single_product_summary hook
			 *
			 * @hooked woocommerce_template_single_title - 5
			 * @hooked woocommerce_template_single_rating - 10
			 * @hooked woocommerce_template_single_price - 10
			 * @hooked woocommerce_template_single_excerpt - 20
			 * @hooked woocommerce_template_single_add_to_cart - 30
			 * @hooked woocommerce_template_single_meta - 40
			 * @hooked woocommerce_template_single_sharing - 50
			 */
		#	remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title',5);
			#do_action( 'woocommerce_single_product_summary' );
			
			woocommerce_template_single_title();
			
			$author     = get_user_by( 'id', $product->post->post_author );
			$author_info     = waa_get_store_info( $author->ID );
			printf( 'von <a href="%s" class="artist">%s</a>', waa_get_store_url( $author->ID ), $author_info['store_name'] ); 
			woocommerce_template_single_price();
			the_content();
			woocommerce_template_single_add_to_cart();
			woocommerce_template_single_meta();
		?>
		
		
<?php  #$product->list_attributes(); ?>
	</div><!-- .summary -->
</div>


<div class="artist-block">	
	<div class="artist-info">	
		<div class="artist-profilepic"><?php echo get_avatar( $author->ID, 150 ); ?></div>
		<div class="artist-shortdesc">		
			<a href="<?= waa_get_store_url( $author->ID ) ?>" title="<?php printf( __('Profil von %s ansehen	', 'waa'), $author_info['store_name']); ?>"> 
				<?= $author_info['store_name']; 
					$city_term = get_term_by('id', $author_info['region'], 'pa_stadt');
					$city_id = $city_term->term_id;
					$city_link = get_term_link( $city_id, 'pa_stadt' );
				?></a>
				(<?php printf( __('<em>Künstler aus</em> <a href="%1$s" title="weitere Künstler aus %2$s anzeigen">%2$s</a>', 'waa'), $city_link, $city_term->name );?>)
				<?= ( isset($author_info['description'] )) ? '<p>'.truncate_string($author_info['description'], '300').'</p>' : '' ?>
		</div>
	</div>
</div>
	
		
		

		</div>
	</div>

</div>

<?php

	wp_reset_query();
 $args = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => 3,
			'author'         => $author->ID,
			'orderby'        => 'rand',
			'post__not_in' => array( $product->id )
	);
		
 $the_query = new WP_Query($args);
 
	if ( $the_query->have_posts() ) {
		echo '<div class="row more-products-by-artist">';
		echo '<div class="col-md-12">';
		echo '<h3>';
		printf( __( 'weitere Produkte von %s', 'waa' ), $author_info['store_name'] );		
		echo '</h3>';
		echo '<ul class="products" id="more-product-by-artist">';
		echo '<li class="grid-sizer"></li>';
		echo '<li class="gutter-sizer"></li>';
	
		while ( $the_query->have_posts() ) : $the_query->the_post(); 
		
		

 ?>
 

<li <?php post_class( $classes ); ?>>

	<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
	<a href="<?php the_permalink(); ?>">	
		<?php the_post_thumbnail('large'); ?>	
	</a>
	<div class="art-info">
		<header>
			<span class="tagged_as"><?= $product->get_categories(); ?></span>
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
			
			// show category title
			$category = get_the_terms ($post->ID, 'product_cat');
			
			$author     = get_user_by( 'id', $product->post->post_author );
			$store_info = waa_get_store_info( $author->ID );

			echo '<span class="artist">';
			printf( '<a href="%s">%s</a>', waa_get_store_url( $author->ID ), $author->display_name ) .'</span>';
			
			add_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
			remove_action('woocommerce_after_shop_loop_item_title', 'cj_show_dimensions', 9);
			#do_action( 'woocommerce_after_shop_loop_item_title' );
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
	<?php
	endwhile;
	echo '</ul></div>';
}
wp_reset_postdata();
?>
</ul>




</div>

<div class="row">
	<div class="col-md-12">
	<?php
		/**
		 * woocommerce_after_single_product_summary hook
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_upsell_display - 15
		 * @hooked woocommerce_output_related_products - 20
		 */
		remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs',10);
		do_action( 'woocommerce_after_single_product_summary' );
	?>

	<meta itemprop="url" content="<?php the_permalink(); ?>" />
	</div>
</div><!-- #product-<?php the_ID(); ?> -->
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
