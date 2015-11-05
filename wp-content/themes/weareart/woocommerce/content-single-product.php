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
				woocommerce_template_single_add_to_cart();
				woocommerce_template_single_meta();
			?>
			
			
			<?php  #$product->list_attributes(); ?>
		</div><!-- .summary -->
	</div><!-- .row productinfo -->
		
	<div class="artist-block">	
		<div class="artist-info">	
			<div class="artist-profilepic"><?php echo get_avatar( $author->ID, 150 ); ?></div>
			<div class="artist-shortdesc">		
				<span style="font-weight:bold; font-style:italic">&raquo; <?= the_title().' &laquo;</span> '.__('von', 'waa'); ?><a href="<?= waa_get_store_url( $author->ID ) ?>" title="<?php printf( __('Profil von %s ansehen	', 'waa'), $author_info['store_name']); ?>"> 
					<?= $author_info['store_name']; 
						$city_term = get_term_by('id', $author_info['region'], 'pa_stadt');
						$city_id = $city_term->term_id;
						$city_link = get_term_link( $city_id, 'pa_stadt' );
					?></a>
					(<?php printf( __('<em>Künstler aus</em> <a href="%1$s" title="weitere Künstler aus %2$s anzeigen">%2$s</a>', 'waa'), $city_link, $city_term->name );?>)
					<?php
									the_content();
					#echo ( isset($author_info['description'] )) ? '<p>'.truncate_string($author_info['description'], '300').'</p>' : '' 
					?>
			</div>
		</div>
	</div><!-- END artist-block -->
</div><!-- .itemscope -->

</div>
</div><!-- .col-md-12 -->
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
 
 <?php wc_get_template_part( 'content', 'product' ); ?>

 <?php
	endwhile;
	echo '</ul></div></div><!-- more-products-by-artist END -->';
}
wp_reset_postdata();
?>

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
